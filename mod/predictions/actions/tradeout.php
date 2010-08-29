<?php
// only logged in users can add predictions
elgg_set_ignore_access(TRUE);

// init
$page_viewer = get_loggedin_user();
$size = 100.0;
$factor = 100.0;    // sensitivity factor

// get the form input
$transaction = get_entity(get_input('transaction'));
$market = get_entity($transaction->market);
$option = $transaction->option;

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

// vaidation
if ($transaction->status == 'closed') {
    register_error('This position is already closed');
    forward('mod/predictions/index.php');
}

// vaidation
if ($market->status != 'open') {
    register_error('This market is no longer open.');
    forward('mod/predictions/index.php');
}

// Get the integral of the weighted price
$ev = $size * (1/$transaction->price) ;


// Get the integral of the weighted price
if ($option == 'option1') {
    $ev = $size * (1/$transaction->price) ;
} else {
    $ev = $size * (1/$transaction->price) ;
}

// How far approximately will we move?
if ($option == 'option1') {
    $stretch =  $ev * $market->value1;
} else {
    $stretch =  $ev * $market->value2;
}

// Get the approximate price difference
if ($option == 'option1') {
    $diff = $stretch/$size * ($market->value2 / $factor);
} else {
    $diff = $stretch/$size * ($market->value1 / $factor);
}


// Get the approximate trade out value
if ($option == 'option1') {
    $fair = ($market->value1 - $diff/2.0 )*$ev;
} else {
    $fair = ($market->value2 - $diff/2.0 )*$ev;
}

// 2nd approximation
if ($option == 'option1') {
    $stretch2 =  $fair;
} else {
    $stretch2 =  $fair;
}

$f = $stretch2 / ($size * $factor);
if ($option == 'option1') {
    $diff2 = $market->value1 - (( $market->value1 - $f) / ( 1 - $f))  ;
} else {
    $diff2 = $market->value2 - (( $market->value2 - $f) / ( 1 - $f))  ;
}

if ($option == 'option1') {
    $fair2 = ($market->value1 - $diff2/2.0 )*$ev;
} else {
    $fair2 = ($market->value2 - $diff2/2.0 )*$ev;
}


if ($option == 'option1') {
    $market->value1 = $market->value1 - $diff;
    $market->value2 = $market->value2 + $diff;
} else {
    $market->value1 = $market->value1 + $diff;
    $market->value2 = $market->value2 - $diff;
}


$page_viewer->opendollars = $page_viewer->opendollars + round($fair, 0);
system_message('$' . round($fair) . ' has been credited to your account.');
$transaction->settlementPrice = round($fair);
$transaction->settlementDate = time();

$transaction->status = 'closed';

$transaction->save();

// forward user to a page that displays the predictions
forward('mod/predictions/transactions.php');
?>

