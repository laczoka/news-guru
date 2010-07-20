<?php
// Get categories, if they're installed
global $CONFIG;
include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the current page's viewer
gatekeeper();
$page_viewer = get_loggedin_user();


$START_AMOUNT = 1000;
$count_per_page = 10;
$offset = get_input("offset", 0);

$transaction_query = array('type' => 'object', 'subtype' => 'transaction', 
    metadata_name => 'status', metadata_value => 'open', owner_guids => $page_viewer->guid,
    'full_view' => FALSE,
    'offset' => $offset,
    'limit' => $count_per_page );

$transactions = elgg_get_entities_from_metadata($transaction_query);

$no_of_transactions = elgg_get_entities_from_metadata(
						array_merge($transaction_query, array('count' => true)));

$body = elgg_view_entity_list($transactions, $no_of_transactions, $offset, $count_per_page);

if (!isset($page_viewer->opendollars)) {
    $page_viewer->opendollars = $START_AMOUNT;
    system_message ('Thank you for playing the Prediction Markets, $'
            . $START_AMOUNT . ' have been credited to your account!');
}

add_submenu_item( 'Prediction Markets', $CONFIG->wwwroot . "pg/mod/predictions/");
add_submenu_item( 'Add a Market', $CONFIG->wwwroot . "pg/mod/predictions/add.php");
add_submenu_item( 'Your Account', $CONFIG->wwwroot . "pg/mod/predictions/transactions.php");
add_submenu_item( 'Leaderboard', $CONFIG->wwwroot . "pg/mod/predictions/leaderboard.php");

// Display them in the page
$left = elgg_echo('predictions:disclaimer');
$left .= '<br/><br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left .= '<br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';

$body = '<br/>You have $' . $page_viewer->opendollars . ' remaining<br/>' . $body;


$body  = elgg_view_layout("two_column_left_sidebar", $left, $body);





page_draw("Predictions",$body);

?>
