<?php
include dirname(dirname(__FILE__)).'/lib/lock.php';

$page_viewer = get_loggedin_user();

$p = get_entity(get_input('predictions'));
if (empty($p)) {
    register_error('No market found');
    forward('mod/predictions/index.php');
}

$mutex = LOCK_RESOURCE($p->guid);
if (NULL === $mutex) {
    register_error("Couldn't acquire exclusive access to market");
    forward('mod/predictions/index.php');
}
if ($page_viewer->guid != $p->owner_guid && !$page_viewer->isAdmin()) {
	// release lock on the market
    UNLOCK_RESOURCE($mutex);
	
    system_message('You are not authorized to perform this operation...');
    forward('mod/predictions/index.php');
}

// set state and time of suspension
$p->status = 'suspended';
$p->suspended_utc = time();
$p->save();
// release lock on the market
UNLOCK_RESOURCE($mutex);
system_message('Market has been suspended');

// forward user to a page that displays the predictions
forward('mod/predictions/index.php');

?>
