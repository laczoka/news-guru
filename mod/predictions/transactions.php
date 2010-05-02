<?php
$START_AMOUNT = 1000;

include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

$body = list_entities('object','transaction',0,10,false);

// Get categories, if they're installed
global $CONFIG;
$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot
                . 'search/?subtype=blog&owner_guid='.$page_owner->guid
                . '&tagtype=universal_categories&tag=','subtype' => 'predictions',
        'owner_guid' => $page_owner->guid));


// Get the current page's viewer
gatekeeper();
$page_viewer = get_loggedin_user();

if (!isset($page_viewer->opendollars)) {
    $page_viewer->opendollars = $START_AMOUNT;
    system_message ('Thank you for playing the Prediction Markets, $'
            . $START_AMOUNT . ' have been credited to your account!');
}

// Display them in the page
$cash = '<br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left = $cash . '<br/>This is a <i><b>preview</b></i> of the upcomping prediction markets module.  Please bear with us while we build out the necessary features.</br><br/><a href="add.php">Add</a> a prediction market.';
$left .= '<br/><br/>List <a href="transactions.php">transactions</a>';
$body = elgg_view_layout("two_column_left_sidebar", $left, $body);

page_draw("Predictions",$body);

?>
