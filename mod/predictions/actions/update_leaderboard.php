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
// THIS CODE HAS BEEN REPLACED BY
/*foreach ($total_net_asset_value as $user_guid => $value) {
	$user = get_entity($user_guid);
	$user->report_total_net_asset_value = $value;
}*/
// THE FOLLOWING
// We need to make sure "report_total_net_asset_value" is set for every users in order to be able to
// retrieve all users ordered by it

$number_of_users_processed_per_run = 100;
$user_query = array('type' => 'user', 'full_view' => FALSE, 
                    'limit' => $number_of_users_processed_per_run, 
                    'offset' => 0 );
$total_number_of_users = elgg_get_entities( array_merge(  $user_query, array('count' =>  TRUE, 'limit' => 0 )) );

while ($user_query['offset'] < $total_number_of_users) {
	$users = elgg_get_entities( $user_query );
	
	foreach ($users as $user) {
		// HACK: to fix a sporadic issue where "report_total_net_asset_value" becomes an array
		// Possible cause: db race condition or some elgg bug, other than that unknown at this time
        if (is_array($user->report_total_net_asset_value))
          remove_metadata($user->guid, "report_total_net_asset_value"); 
          
		$user->report_total_net_asset_value 
		 = isset($total_net_asset_value[$user->guid]) ? 
		          $total_net_asset_value[$user->guid] 
		        : $user->opendollars;
		$user->save();
	}
	
	$user_query['offset'] += $number_of_users_processed_per_run;
}

// save the update date
$leaderboard = elgg_get_entities(array('type' => 'object', 'subtype' => 'leaderboard'));
if ( !$leaderboard || !is_array($leaderboard)) {
	    $leaderboard = new ElggObject();
        $leaderboard->subtype = "leaderboard";
        $leaderboard->title = "Leaderboard";
        $leaderboard->access_id = ACCESS_PUBLIC;
        $leaderboard->save();
}
if (is_array($leaderboard))
	$leaderboard = $leaderboard[0];

$leaderboard->last_updated = time();

if (1 != (int)get_input("cron"))
    forward('mod/predictions/leaderboard.php');
?>