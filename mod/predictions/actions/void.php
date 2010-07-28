<?php

gatekeeper();
$page_viewer = get_loggedin_user();

$START_AMOUNT = 1000;
$DAILY_AMOUNT = 20;

elgg_set_ignore_access(TRUE);

$m = get_entity(get_input('market'));

$e = elgg_get_entities_from_metadata(array(
        'type'                  => "object",
        'subtype'               => "transaction",
        'limit'                 =>  0,
        'offset'                =>  0, 
        "metadata_name_value_pairs" => array( "name" => "market", "value" => $m->guid) )
     );

$body = 'Market = ' . $m->guid . '<br/><br/>';
foreach ($e as $t) {
    if ($m->guid == $t-> market) {
        $body .=  ++$j . '. owner : ' . $t->owner_guid . ' $' . $t->size  . ' status ' . $t->status . ' market ' . $t->market . '<br/><br/> ';
        if ($t->status == 'open') {
            $owner = get_entity($t->owner_guid);
            $t->status = 'void';
            $owner->opendollars += $t->size;
            $t->save();
        } else if ($t->status == 'settled') {
            $owner = get_entity($t->owner_guid);
        	$t->status = 'void';
            $owner->opendollars += $t->size;
            $owner->opendollars -= $t->settlementPrice;
            $t->save();
        }
    }
    $m->status = 'void';
        //print_r($i);
}


//unset($page_viewer->opendollars);
//unset($page_viewer->lastdaily);
if (!isset($page_viewer->opendollars) || $page_viewer->opendollars==null) {
    $page_viewer->opendollars = $START_AMOUNT;
    $page_viewer->lastdaily = time();
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
