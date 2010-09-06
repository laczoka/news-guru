<?php
elgg_set_ignore_access(TRUE);

// get the form input
$title = get_input('title');
$body = get_input('body');
$option1 = get_input('option1');
$value1 = get_input('value1');
$option2 = get_input('option2');
$value2 = get_input('value2');
$settlement = get_input('settlement');
// deprecated: remove $suspend in the future
$suspend = get_input('suspend');
$suspend_utc = get_input('suspend_utc');
$suspend_utc = is_numeric($suspend_utc) ? $suspend_utc : strtotime($suspend_utc);
$tags = string_to_tag_array(get_input('tags'));

$add_market_input = array(
    'title' => $title,
    'body' => $body,
    'tags' => get_input('tags'), // get the raw tag string
    'option1' => $option1,
    'value1' => $value1,
    'option2' => $option2,
    'value2' => $value2,
    'suspend' => $suspend,
    'suspend_utc' => $suspend_utc,
    'settlement' => $settlement 
);

$_SESSION['predictions/add_market'] = $add_market_input;

$value1 = $value1 / 100.0;
$value2 = $value2 / 100.0;

// validation
if ( isset($suspend_utc) && ($suspend_utc < time())) {
    register_error('Market suspension date lies in the past');
    forward('mod/predictions/add.php');
}

if (empty($value1) || empty($value2) || empty($option1) || empty($option2)
        ||empty($title) || empty($body) ) {
    register_error('Please fill in description, title, and at least 2 options');
    forward('mod/predictions/add.php');
}

if ( $value1 + $value2 != 1.0 ) {
    register_error('Percentages must add up to 100%');
    forward('mod/predictions/add.php');
}

// create a new object
$prediction = new ElggObject();
$prediction->title = $title;
$prediction->description = $body;
$prediction->subtype = "predictions";

// for now make all predictions public
$prediction->access_id = ACCESS_PUBLIC;

// owner is logged in user
$prediction->owner_guid = get_loggedin_userid();

// save tags as metadata
$prediction->tags = $tags;


// set options
if (!empty($option1) && !empty($value1)) {
    $prediction->option1 = $option1;
    $prediction->value1 = $value1;
}

if (!empty($option2) && !empty($value2)) {
    $prediction->option2 = $option2;
    $prediction->value2 = $value2;
}

// deprecated: remove $suspend in the future
$prediction->suspend = $suspend;
$prediction->suspend_utc = $suspend_utc;

$prediction->settlement = $settlement;

$prediction->status = 'open';

// save to database
$prediction->save();

// send update to the river
add_to_river('river/object/predictions/create','create',$prediction->owner_guid,$prediction->guid);

unset($_SESSION['predictions/add_market']);

// forward user to a page that displays the post
forward('mod/predictions/index.php');
?>
