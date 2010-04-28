<?php
/**
 * Elgg add friend action
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

// Ensure we are logged in
gatekeeper();

// Get the GUID of the user to friend
$friend_guid = get_input('friend');
$friend = get_entity($friend_guid);

$errors = false;

// Get the user
try {
	if (!$_SESSION['user']->addFriend($friend_guid)) {
		$errors = true;
	}
} catch (Exception $e) {
	register_error(sprintf(elgg_echo("friends:add:failure"),$friend->name));
	$errors = true;
}
if (!$errors){
	// add to river
	add_to_river('friends/river/create','friend',$_SESSION['user']->guid,$friend_guid);
	system_message(sprintf(elgg_echo("friends:add:successful"),$friend->name));
}

// Forward back to the page you friended the user on
forward($_SERVER['HTTP_REFERER']);
