<?php
// only logged in users can add predictions
gatekeeper();
elgg_set_ignore_access(TRUE);

// get the form input
$title = get_input('title');
$body = get_input('body');
$option1 = get_input('option1');
$value1 = get_input('value1');
$option2 = get_input('option2');
$value2 = get_input('value2');
$settlement = get_input('settlement');

$value1 = $value1 / 100.0;
$value2 = $value2 / 100.0;

$suspend = get_input('suspend');
$tags = string_to_tag_array(get_input('tags'));

// validation
if (empty($value1) || empty($value2) || empty($option1) || empty($option2)
        ||empty($title) || empty($body) ) {
    register_error('Please fill in description, title, and at least 2 options');
    forward('mod/predictions/index.php');
}

if ( $value1 + $value2 != 1.0 ) {
    register_error('Percentages must add up to 100%');
    forward('mod/predictions/index.php');
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

$prediction->suspend = $suspend;

$prediction->settlement = $settlement;

$prediction->status = 'open';

// save to database
$prediction->save();

// forward user to a page that displays the post
forward('mod/predictions/index.php');
?>
