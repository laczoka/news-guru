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

$tr = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'transaction',
    metadata_name => 'status', metadata_value => 'open',
    'full_view' => FALSE, limit => 0 ));

$size = 100.0;
$factor = 100.0;    // sensitivity factor
$leaderboard = array();
foreach ($tr as $t) {
    // Get the integral of the weighted price
    $ev = $size * (1/$t->price) ;

    $m = get_entity($t->market);


    // Get the integral of the weighted price
    if ($t->option == 'option1') {
        $ev = $size * (1/$t->price) ;
    } else {
        $ev = $size * (1/$t->price) ;
    }

    // How far approximately will we move?
    if ($t->option == 'option1') {
        $stretch =  $ev * $m->value1;
    } else {
        $stretch =  $ev * $m->value2;
    }

    // Get the approximate price difference
    if ($t->option == 'option1') {
        $diff = $stretch/$size * ($m->value2 / $factor);
    } else {
        $diff = $stretch/$size * ($m->value1 / $factor);
    }


    // Get the approximate trade out value
    if ($t->option == 'option1') {
        $fair = ($m->value1 - $diff/2.0 )*$ev;
    } else {
        $fair = ($m->value2 - $diff/2.0 )*$ev;
    }

    // 2nd approximation
    if ($t->option == 'option1') {
        $stretch2 =  $fair;
    } else {
        $stretch2 =  $fair;
    }

    $f = $stretch2 / ($size * $factor);
    if ($t->option == 'option1') {
        $diff2 = $m->value1 - (( $m->value1 - $f) / ( 1 - $f))  ;
    } else {
        $diff2 = $m->value2 - (( $m->value2 - $f) / ( 1 - $f))  ;
    }

    if ($t->option == 'option1') {
        $fair2 = ($m->value1 - $diff2/2.0 )*$ev;
    } else {
        $fair2 = ($m->value2 - $diff2/2.0 )*$ev;
    }

    if (array_key_exists($t->owner_guid, $leaderboard)) {
        $leaderboard[$t->owner_guid] += round($fair2);
//error_log(' leaderboard ' . $leaderboard[$t->owner_guid] . ' for ' . $t->owner_guid );
    } else {
        $user = get_entity($t->owner_guid);
        $leaderboard[$t->owner_guid] = $user->opendollars;
        $leaderboard[$t->owner_guid] += round($fair2);
//error_log(' leaderboard ' . $leaderboard[$t->owner_guid] . ' for ' . $t->owner_guid );
    }
}


function cmp( $a, $b ) {
    //global $leaderboard;
    /*
    if( (!empty($leaderboard[$a->guid])?$leaderboard[$a->guid]:$a->opendollars) ==  (!empty($leaderboard[$b->guid])?$leaderboard[$b->guid]:$b->opendollars) ){ return 0 ; }
    return ( (!empty($leaderboard[$a->guid])?$leaderboard[$a->guid]:$a->opendollars) < (!empty($leaderboard[$b->guid])?$leaderboard[$b->guid]:$b->opendollars) ) ? 1 : -1;
     *
     */
    $a = $a->opendollars;
    $b = $b->opendollars;
    //$a = (!empty($leaderboard[$a->guid])?$leaderboard[$a->guid]:$a->opendollars);
    //$b = (!empty($leaderboard[$b->guid])?$leaderboard[$b->guid]:$b->opendollars);
    if ( $a  == $b ) return 0;
    return ( $a < $b )? 1 : -1 ;
}
usort($e,'cmp');

//$body = elgg_view_entity_list($body, 0, 0, 30, FALSE, FALSE);
$body = 'All Time Net Worth (market value)<br/><br/>';
foreach ($e as $i) {
    if (!empty($i->opendollars)) {
        $body .=  ++$j . '. ' . $i->username . ' $' . $i->opendollars . ' ($' .
                (!empty($leaderboard[$i->guid])?$leaderboard[$i->guid]:$i->opendollars)
                . ') <br/><br/> ';
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

$left = elgg_echo('predictions:disclaimer');
$left .= '<br/><br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
$left .= '<br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';

// layout the page
$body = elgg_view_layout('two_column_left_sidebar', $left, $body);

page_draw("Predictions",$body);

?>
