<?php
// Get categories, if they're installed
global $CONFIG;
include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the current page's viewer
gatekeeper();
$page_viewer = get_loggedin_user();


$START_AMOUNT = 1000;


//$body = elgg_list_entities(array('type' => 'object', 'subtype' => 'transaction', 'owner_guids' => 0, 'limit' => 10, 'full_view' => TRUE, 'status' => 'open', metadata_name => 'status', metadata_value => 'open'));
$e = elgg_get_entities (array('type' => 'object', 'subtype' => 'transaction', limit => 0,
    'offset' => 0, 'full_view' => FALSE));
$e = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'transaction',
    metadata_name => 'status', metadata_value => 'open',
    'full_view' => FALSE, limit => 0 ));

foreach ($e as $k => $t) {
    if ($page_viewer->guid != $t->owner_guid) {
        //unset($e[$k]);
    }
}
$body = elgg_view_entity_list($e, 0, 0, 0);





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
