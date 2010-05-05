<?php
// Get the current page's viewer
// Get categories, if they're installed
global $CONFIG;
include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

gatekeeper();
$page_viewer = get_loggedin_user();

$START_AMOUNT = 1000;
$DAILY_AMOUNT = 20;


$e = elgg_get_entities(array('type' => 'user', 'limit' => 0,
    'offset' => $offset, 'full_view' => FALSE));

function cmp( $a, $b )
{
  if(  $a->opendoallars ==  $b->opendollars ){ return 0 ; }
  return ($a->opendollars < $b->opendollars) ? 1 : -1;
}
usort($e,'cmp');

//$body = elgg_view_entity_list($body, 0, 0, 30, FALSE, FALSE);
$body = 'All Time Net Worth<br/><br/>';
foreach ($e as $i) {
    if (!empty($i->opendollars)) {
        $body .=  ++$j . '. ' . $i->username . ' $' . $i->opendollars  . ' <br/><br/> ';
    }
    //print_r($i);
}


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

// layout the page
$body = elgg_view_layout('two_column_left_sidebar', $left, $body);

page_draw("Predictions",$body);

?>
