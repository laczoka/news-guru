<?php
// only logged in users can add predictions
gatekeeper();

// init
$page_viewer = get_loggedin_user();
$size = 100.0;
$status = 'open';
$factor = 100.0;    // sensitivity factor

// get the form input
$m = get_entity(get_input('market'));
$price = get_input('price');
$option = get_input('option');

//system_message('Betting is not currently available.  It is expected to be turned on in the next week.');
//forward('mod/predictions/index.php');

// validation
if ($page_viewer->opendollars < $size) {
    register_error('$100 is currently required to bet on this market');
    forward('mod/predictions/index.php');
}

if (empty($m)) {
    register_error('No market found');
    forward('mod/predictions/index.php');
}

// create a new predictions object
$t = new ElggObject();
$t->title = 'Transaction ' . $t->guid;
$t->description = '$100 wagered';
$t->subtype = "transaction";
// set values
if (!empty($m)) {
    $t->market = $m->guid;
}
if (!empty($option) ) {
    $t->option = $option;
}
if (!empty($size) ) {
    $t->size = $size;
}
if (!empty($status) ) {
    $t->status = $status;
}

// for now make all predictions public
$t->access_id = ACCESS_PUBLIC;

// owner is logged in user
$t->owner_guid = get_loggedin_userid();


// adjust balance
$page_viewer->opendollars = round($page_viewer->opendollars - $size, 0);

// Get the approximated market move
if ($option == 'option1') {
    $diff = (1.0 - $m->value1) / $factor; // one percent to home
} else {
    $diff = (1.0 - $m->value2) / $factor; // one percent to home
}

// Set transaction price as midpoint
if ($option == 'option1') {
    $t->price = $m->value1 + $diff/2.0;
} else {
    $t->price = $m->value2 - $diff/2.0;
}

// adjust market
if ($option == 'option1') {
    $m->value1 = $m->value1 +$diff;
    $m->value2 = $m->value2 -$diff;
} else {
    $m->value1 = $m->value1 +$diff;
    $m->value2 = $m->value2 -$diff;
}


// save to database
$t->save();

// forward user 
forward('mod/predictions/index.php');
?>

