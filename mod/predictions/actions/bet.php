<?php

// init
$page_viewer = get_loggedin_user();
$size = 100.0;
$status = 'open';
$factor = 100.0;    // sensitivity factor

// get the form input
$market = get_entity(get_input('market'));
$price = get_input('price');
$option = get_input('option');

// check if the suspension deadline passed (expect UTC time)
if (is_numeric($market->suspend_utc) && (((int)$market->suspend_utc) < time()))
{
 	$market->status = 'suspended';
 	$market->save();
 	// this seems like a code duplication but it is not
 	// this makes sure the bet won't be placed after automatic suspension
 	// hfl13 reported an isssue 
 	// Bet http://news-guru.com/pg/view/22873 placed after
 	// http://news-guru.com/pg/view/22793 should have been suspended
 	system_message('Market is no longer open.');
    forward('mod/predictions/index.php');
}

if ($market->status != 'open') {
    system_message('Market is no longer open.');
    forward('mod/predictions/index.php');
}


// validation
if ($page_viewer->opendollars < $size) {
    register_error('$100 is currently required to bet on this market');
    forward('mod/predictions/index.php');
}

if (empty($market)) {
    register_error('No market found');
    forward('mod/predictions/index.php');
}

// create a new predictions object
elgg_set_ignore_access(TRUE);

$transaction = new ElggObject();
$transaction->title = 'Transaction ' . $transaction->guid;
$transaction->description = '$' . $size . ' wagered';
$transaction->subtype = "transaction";
$transaction->value1 = $market->value1;
$transaction->value2 = $market->value2;
// set values
if (!empty($market)) {
    $transaction->market = $market->guid;
}
if (!empty($option) ) {
    $transaction->option = $option;
}
if (!empty($size) ) {
    $transaction->size = $size;
}
if (!empty($status) ) {
    $transaction->status = $status;
}

// for now make all predictions public
$transaction->access_id = ACCESS_PUBLIC;

// owner is logged in user
$transaction->owner_guid = get_loggedin_userid();


// adjust balance
$page_viewer->opendollars = round($page_viewer->opendollars - $size, 0);

// Get the approximated market move
if ($option == 'option1') {
    $diff = (1.0 - $market->value1) / $factor; // one percent to home
} else {
    $diff = (1.0 - $market->value2) / $factor; // one percent to home
}

// Set transaction price as midpoint
if ($option == 'option1') {
    $transaction->price = $market->value1 + $diff/2.0;
} else {
    $transaction->price = $market->value2 + $diff/2.0;
}

// adjust market
if ($option == 'option1') {
    $market->value1 = $market->value1 +$diff;
    $market->value2 = $market->value2 -$diff;
} else {
    $market->value1 = $market->value1 -$diff;
    $market->value2 = $market->value2 +$diff;
}
$market->volume += $size;

// save to database
$transaction->save();
$market->save();

// forward user 
forward('mod/predictions/transactions.php');
?>

