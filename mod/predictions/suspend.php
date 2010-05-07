<?php
// Load Elgg engine
include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// make sure only logged in users can see this page	
gatekeeper();
$page_viewer = get_loggedin_user();

if ($page_viewer->guid != 2 ) {
    system_message('Edit market is disabled for the next day or so...');
    forward('mod/predictions/index.php');
}
$p = get_entity(get_input('predictions'));

// set the title
$p->status = 'suspended';


?>
