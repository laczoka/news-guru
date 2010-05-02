<?php
// only logged in users can add predictions
gatekeeper();

// init
$page_viewer = get_loggedin_user();
$size = 100.0;
$error1 = 0.005;    // rounding error term
$error2 = 0.00005;  // rounding error term
$factor = 100.0;    // sensitivity factor

// get the form input
$transaction = get_entity(get_input('transaction'));
$market = get_entity($transaction->market);
$option = $transaction->option;

// vaidation
if ($transaction->status == 'closed') {
    register_error('This position is already closed');
    forward('mod/predictions/index.php');
}


// Get the integral of the weighted price
$ev = 100.0 * (1/$transaction->price) ;

// How far approximately will we move?
if ($option == 'option1') {
    $stretch =  $ev * $market->value1;
} else {
    $stretch =  $ev * $market->value2;
}

// Get the approximate price difference
if ($option == 'option1') {
    $diff = $stretch/$size * ($market->value1 / $factor);
} else {
    $diff = $stretch/$size * ($market->value2 / $factor);
}

// Get the approximate trade out value
if ($option == 'option1') {
    $fair = ($market->value1 - $diff/2.0)*$ev;
} else {
    $fair = ($market->value2 + $diff/2.0)*$ev;
}

// Change the balance
$page_viewer->opendollars = round( $page_viewer->opendollars + $fair + $error1, 2);

$market->value1 = $market->value1 - $diff+$error2;
$market->value2 = $market->value2 + $diff-$error2;



$transaction->status = 'closed';

$transaction->save();

// forward user to a page that displays the post
forward('mod/predictions/index.php');
?>

