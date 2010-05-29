<?php
$START_AMOUNT = 1000;
$DAILY_AMOUNT = 20;

include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

//$body = list_entities('object','predictions',0,10,false);
$e = elgg_get_entities (array('type' => 'object', 'subtype' => 'predictions', limit => 0,
    'offset' => 0, 'full_view' => FALSE));
foreach ($e as $k => $p) {
    if ($p->status != 'open'  && $p->status != 'suspended') {
        unset ($e[$k]);
    }
}
$body = elgg_view_entity_list($e);

// Get categories, if they're installed
global $CONFIG;



// Get the current page's viewer
$page_viewer = get_loggedin_user();

//unset($page_viewer->opendollars);
//unset($page_viewer->lastdaily);
if (!isset($page_viewer->opendollars) || $page_viewer->opendollars==null) {
    $page_viewer->opendollars = $START_AMOUNT;
    system_message ('Thank you for playing the Prediction Markets, $'
            . $START_AMOUNT . ' have been credited to your account!');
} else {
    if (!isset($page_viewer->lastdaily) || $page_viewer->lastdaily==null  || time() - $page_viewer->lastdaily > 3600*23 ) {
        $page_viewer->opendollars += $DAILY_AMOUNT;
        $page_viewer->lastdaily = time();
        system_message ('A Daily reward of $'
                . $DAILY_AMOUNT . ' has been credited to your account!');
    }
}

add_submenu_item( 'Prediction Markets', $CONFIG->wwwroot . "pg/mod/predictions/");
add_submenu_item( 'Add a Market', $CONFIG->wwwroot . "pg/mod/predictions/add.php");
add_submenu_item( 'Your Account', $CONFIG->wwwroot . "pg/mod/predictions/transactions.php");
add_submenu_item( 'Leaderboard', $CONFIG->wwwroot . "pg/mod/predictions/leaderboard.php");

$left  = '<br/>This is a <i><b>preview</b></i> of the upcomping prediction markets module.  We will be in <b>beta testng mode</b> this month, betting is currently turned on for only one market.  <br/><br/>*NOTE* There will possible <b>balance reset</b> on 1 June.';
$left .= '<br/><br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left .= '<br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';

		// Get categories, if they're installed
		global $CONFIG;
		$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?search_type=tags&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));


// layout the page
$body = elgg_view_layout('two_column_left_sidebar', $left, $body, $area3);

page_draw("Predictions",$body);

?>
