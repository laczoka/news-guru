<?php
// Load Elgg engine
include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// make sure only logged in users can see this page	
gatekeeper();
$page_viewer = get_loggedin_user();

if ($page_viewer->guid != 2 ) {
    //system_message('Add market is disabled for the next day or so...');
    //forward('mod/predictions/index.php');
}

// set the title
$title = "Create a new prediction";

// start building the main column of the page
$area2 = elgg_view_title($title);

// Add the form to this section
$area2 .= elgg_view("predictions/add");

add_submenu_item( 'Prediction Markets', $CONFIG->wwwroot . "pg/mod/predictions/");
add_submenu_item( 'Add a Market', $CONFIG->wwwroot . "pg/mod/predictions/add.php");
add_submenu_item( 'Your Account', $CONFIG->wwwroot . "pg/mod/predictions/transactions.php");
add_submenu_item( 'Leaderboard', $CONFIG->wwwroot . "pg/mod/predictions/leaderboard.php");

$left = elgg_echo('predictions:disclaimer');
$left .= '<br/><br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left .= '<br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';

// layout the page
$body = elgg_view_layout('two_column_left_sidebar', $left, $area2);

// draw the page
page_draw($title, $body);
?>
