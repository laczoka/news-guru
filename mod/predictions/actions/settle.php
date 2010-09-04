<?php
include dirname(dirname(__FILE__)).'/lib/lock.php';
// init
$page_viewer = get_loggedin_user();

$size = 100.0;

elgg_set_ignore_access(TRUE);


$m = get_entity(get_input('market'));
if (empty($m)) {
    register_error('No market found');
    forward('mod/predictions/index.php');
}

$mutex = LOCK_RESOURCE($m->guid);
if (NULL === $mutex) {
    register_error("Couldn't acquire exclusive access to market");
    forward('mod/predictions/index.php');
}

$time_of_settlement = time();

$option = get_input('option');

$e = elgg_get_entities_from_metadata(array(
        'type'                  => "object",
        'subtype'               => "transaction",
        'limit'                 =>  0,
        'offset'                =>  0, 
        "metadata_name_value_pairs" => array( "name" => "market", "value" => $m->guid) )
     );

$report_transactions = array();

foreach ($e as $t) {
    if ($m->guid == $t-> market) {
        $owner = get_entity($t->owner_guid);
        $ev = $size * (1/$t->price) ;
        if ($option == $t->option) {
            $return = $t->status == 'open' ? round($ev) : 0.0;
        } else {
            $return = $t->status == 'open' ? 0.0 : 0.0;
        }

        if ($t->status == 'open') {
            $owner->opendollars += round($return);
            $t->settlementPrice = round($return);
            $t->settlementDate = time();
            $t->status = 'settled';
            
            $option_name = ($t->option == 'option1') ? $m->option1 : $m->option2;
            $report_transactions[] = array( option_name => $option_name, 
                                        tr_url => $t->getURL(), 
                                    owner_name => $owner->name, 
                                     owner_url => $owner->getURL(), 
                                    tr_created => $t->getTimeCreated(),
                                         price => $t->price,
                                         stake => $size,
                                           win => $return - $size );
            // accumulate total return for market creator
            if ($t->owner_guid == $m->owner_guid) {
            	$report_market_creator_total_return += $return - $size;
            }
            // accumulate total return for settlement officer
            if ($t->owner_guid == $page_viewer->guid) {
            	$report_settlement_officer_total_return += $return - $size;
            }
        }
    }
}
$report_content = array(
	market_creator_total_return => $report_market_creator_total_return,
	settlement_officer_total_return => $report_settlement_officer_total_return,
	transactions => $report_transactions					
);
// do not set suspension date for already suspended markets
// this is to deal with legacy markets, will be removed
if (empty($m->suspended_utc) && ($m->status == 'open')) {
	
	$m->suspended_utc = $time_of_settlement;
}

$m->settled_utc = $time_of_settlement;
$m->outcome = $option == "option1" ? $m->option1 : $m->option2;
$m->settled_by = $page_viewer->guid;
$m->status = 'settled';

$report = new ElggObject();
$report->title = "Settlement report";
$report->subtype = "settlement_report";
$report->access_id = ACCESS_LOGGED_IN;
$report->market = $m->guid;
$report->report = serialize($report_content);
$report->tags = array("settlement");

/*$report = new ElggSettlementReport();
$report->report = serialize($report_content); */

$report->save();

// send update to the river
add_to_river('river/object/predictions/settle','settle',$page_viewer->guid,$report->guid);

UNLOCK_RESOURCE($mutex);
forward($report->getURL());
?>
