<?php
// make sure only logged in users can execute this action
gatekeeper();
$page_viewer = get_loggedin_user();

$p = get_entity(get_input('predictions'));
if ($page_viewer->guid != 2 && $page_viewer->guid != $p->owner_guid && !$page_viewer->isAdmin()) {
    system_message('Suspend market is not currently available...');
    forward('mod/predictions/index.php');
}

// set the title
$p->status = 'suspended';

system_message('Market has been suspended');

// forward user to a page that displays the predictions
forward('mod/predictions/index.php');

?>
