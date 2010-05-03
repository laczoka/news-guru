<?php
// only logged in users can add predictions
gatekeeper();




// init
$page_viewer = get_loggedin_user();
$size = 100.0;
$factor = 100.0;    // sensitivity factor

// get the form input
$t = get_entity(get_input('transaction'));
$m = get_entity($t->market);
$option = $t->option;

// vaidation
if ($t->status == 'closed') {
    register_error('This position is already closed');
    forward('mod/predictions/index.php');
}


// Get the integral of the weighted price
$ev = $size * (1/$t->price) ;


// Get the integral of the weighted price
if ($option == 'option1') {
    $ev = $size * (1/$t->price) ;
} else {
    $ev = $size * (1/(1-$t->price)) ;
}

// How far approximately will we move?
if ($option == 'option1') {
    $stretch =  $ev * $m->value1;
} else {
    $stretch =  $ev * $m->value1;
}

// Get the approximate price difference
if ($option == 'option1') {
    $diff = $stretch/$size * ($m->value2 / $factor);
} else {
    $diff = $stretch/$size * ($m->value2 / $factor);
}


// Get the approximate trade out value
if ($option == 'option1') {
    $fair = ($m->value1 - $diff/2.0 )*$ev;
} else {
    $fair = ($m->value1 - $diff/2.0 )*$ev;
}

// 2nd approximation
if ($option == 'option1') {
    $stretch2 =  $fair;
} else {
    $stretch2 =  $fair;
}

$f = $stretch2 / ($size * $factor);
if ($option == 'option1') {
    $diff2 = $m->value1 - (( $m->value1 - $f) / ( 1 - $f))  ;
} else {
    $diff2 = $m->value1 - (( $m->value1 - $f) / ( 1 - $f))  ;
}

if ($option == 'option1') {
    $fair2 = ($m->value1 - $diff2/2.0 )*$ev;
} else {
    $fair2 = ($m->value1 - $diff2/2.0 )*$ev;
}


if ($option == 'option1') {
    $m->value1 = $m->value1 - $diff;
    $m->value2 = $m->value2 + $diff;
} else {
    $m->value1 = $m->value1 - $diff;
    $m->value2 = $m->value2 + $diff;
}


$page_viewer->opendollars = $page_viewer->opendollars + round($fair, 0);
system_message('$' . round($fair) . ' has been credited to your account.');

$t->status = 'closed';

$t->save();

// forward user to a page that displays the post
forward('mod/predictions/index.php');
?>

