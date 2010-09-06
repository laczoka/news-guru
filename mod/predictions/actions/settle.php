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
    if ($m->guid == $t->market) {
        $owner = get_entity($t->owner_guid);
        
        $option_name = ($t->option == 'option1') ? $m->option1 : $m->option2;
            
        // handle tradeout bets need to be included into the report but the do not change user balance
        if ($t->status == 'closed') {
            $return = $t->settlementPrice;
            $report_transactions[] = array( option_name => $option_name, 
                                        tr_url => $t->getURL(), 
                                    owner_name => $owner->name, 
                                     owner_url => $owner->getURL(), 
                                    tr_created => $t->getTimeCreated(),
                                     tr_closed => $t->settlementDate,
                                         price => $t->price,
                                         stake => $t->size,
                                           win => $t->settlementPrice - $size );
        }
        // handle open bets
        if ($t->status == 'open') {
            $ev = $size * (1/$t->price) ;
            $return = ($option == $t->option) ? round($ev) : 0.0;
            $owner->opendollars += round($return);
            $t->settlementPrice = round($return);
            $t->settlementDate = time();
            $t->status = 'settled';
            
            $report_transactions[] = array( option_name => $option_name, 
                                        tr_url => $t->getURL(), 
                                    owner_name => $owner->name, 
                                     owner_url => $owner->getURL(), 
                                    tr_created => $t->getTimeCreated(),
                                    tr_closed => NULL,
                                         price => $t->price,
                                         stake => $size,
                                           win => $return - $size );
        }
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

$report = PredictionsSettlementReport::create(
    $m->guid,
    $report_content
);

$report->save();

// send update to the river
add_to_river('river/object/predictions/settle','settle',$page_viewer->guid,$report->guid);

UNLOCK_RESOURCE($mutex);
forward($report->getURL());
?>
