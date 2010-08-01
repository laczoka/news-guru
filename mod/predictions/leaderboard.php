<?php
// Get the current page's viewer
// Get categories, if they're installed
global $CONFIG;
include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

gatekeeper();
$page_viewer = get_loggedin_user();

$START_AMOUNT = 1000;
$DAILY_AMOUNT = 20;

$offset = get_input("offset", 0);
$users_per_page  = 50;

$users_query = array('type' => 'user', 
                     'limit' => $users_per_page,
                     'offset' => $offset, 
                     'full_view' => FALSE,
                     'order_by_metadata' 
                        => array('name' => 'opendollars', 'direction' => 'DESC', 'as' => 'integer'));

$users = elgg_get_entities_from_metadata($users_query);

$no_of_users = elgg_get_entities_from_metadata(array_merge(array(count => TRUE), $users_query));

function cmp( $a, $b ) {
    $a = $a->opendollars;
    $b = $b->opendollars;
    if ( $a  == $b ) return 0;
    return ( $a < $b )? 1 : -1 ;
}
usort($users,'cmp');

set_view_location('user/user', $CONFIG->pluginspath . 'predictions/views/default/leaderboard/');

$nav = elgg_view('navigation/pagination',array(
        'baseurl' => $_SERVER['REQUEST_URI'],
        'offset' => $offset,
        'count' => $no_of_users,
        'limit' => $users_per_page,
    ));

$body .= $nav;

$body .= "<div style='width:100%;text-align:center'>Last updated ".friendly_time((int)(time() / 60*5) * 60*5)."</div>";

if ($page_viewer->isAdmin()) {
    $body .= '<div style="width:100%;text-align:center"><a href="'.elgg_add_action_tokens_to_url($CONFIG->wwwroot."action/predictions/update_leaderboard").'">Update NOW!</a></div>';
}
foreach ($users as $i => $user)
{
   $body .= elgg_view('user/user',array('entity' => $user, 'order_no' => ($offset+$i+1)));
}

$body .= $nav;



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

$left = elgg_echo('predictions:disclaimer');
$left .= '<br/><br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left .= '<br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';

// layout the page
$body = elgg_view_layout('two_column_left_sidebar', $left, $body);

page_draw("Predictions",$body);

?>
