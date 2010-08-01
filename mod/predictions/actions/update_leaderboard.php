<?php
elgg_set_ignore_access(TRUE);

$tr = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'transaction',
    metadata_name => 'status', metadata_value => 'open',
    'full_view' => FALSE, limit => 0 ));

$size = 100.0;
$factor = 100.0;    // sensitivity factor
$total_net_asset_value = array();

foreach ($tr as $t) {

    $m = get_entity($t->market);

    // Get the integral of the weighted price
    /*  if ($t->option == 'option1') {
        $ev = $size * (1/$t->price) ;
    } else {
        $ev = $size * (1/$t->price) ;
    }*/
    
    // Get the integral of the weighted price
    $ev = $size * (1/$t->price) ;

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

    if (array_key_exists($t->owner_guid, $total_net_asset_value)) {
        $total_net_asset_value[$t->owner_guid] += round($fair2);
    } else {
        $user = get_entity($t->owner_guid);
        $total_net_asset_value[$t->owner_guid] = $user->opendollars; 
        $total_net_asset_value[$t->owner_guid] += round($fair2);
    }
}

// update Total Net Asset Value for each user
foreach ($total_net_asset_value as $user_guid => $value) {
	$user = get_entity($user_guid);
	$user->report_total_net_asset_value = $value;
	$user->save();
}

forward('mod/predictions/leaderboard.php');
?>