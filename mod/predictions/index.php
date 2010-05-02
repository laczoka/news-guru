<?php
$START_AMOUNT = 1000;
$DAILY_AMOUNT = 20;

include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

$body = list_entities('object','predictions',0,10,false);

// Get categories, if they're installed
global $CONFIG;

error_log('test');

$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot
                . 'search/?subtype=blog&owner_guid='.$page_owner->guid
                . '&tagtype=universal_categories&tag=','subtype' => 'predictions',
        'owner_guid' => $page_owner->guid));


// Get the current page's viewer
gatekeeper();
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


// Display them in the page
$left  = '<br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left .= '<br/>This is a <i><b>preview</b></i> of the upcomping prediction markets module.  We will be in <b>testng mode</b> this month, betting is expected in the first week of May, with a possible <b>balance reset</b> on 1 June, as necessary.<br/><br/>Click to <a href="add.php">Add</a> a prediction market.';
$left .= '<br/><br/>List your <a href="transactions.php">transactions</a>';
$left .= '<br/><br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';
$body  = elgg_view_layout("two_column_left_sidebar", $left, $body);

page_draw("Predictions",$body);

?>
