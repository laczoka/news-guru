<?php
// only logged in users can add predictions
gatekeeper();


// init
$page_viewer = get_loggedin_user();
$size = 100.0;
$status = 'open';
$factor = 20000.0;    // sensitivity factor

// get the form input
$m = get_entity(get_input('market'));
$price = get_input('price');
$option = get_input('option');
$m->status = 'void';

$m->disable();
system_message('Market Settled.');
forward('mod/predictions/index.php');

?>

