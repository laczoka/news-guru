<?php
global $CONFIG;

require_once dirname(__FILE__).'/lib/predictionfacadeeav.php';

function predictions_init() {

    // Load system configuration
    global $CONFIG;

    // Set up menu for logged in users
    if (isloggedin()) {

        add_menu(elgg_echo('Predictions'), $CONFIG->wwwroot . "mod/predictions/index.php");

        // And for logged out users
    } else {
        add_menu(elgg_echo('Predictions'), $CONFIG->wwwroot . "mod/predictions/index.php");
    }
    if (!isset($CONFIG->predictions))
        $CONFIG->predictions = array();
    
    $CONFIG->predictions['backend'] = new PredicitionFacadeEAV();
}

// Make sure the blog initialisation function is called on initialisation
register_elgg_event_handler('init','system','predictions_init');

register_action("predictions/save", false, $CONFIG->pluginspath . "predictions/actions/save.php");
register_action("predictions/tradeout", false, $CONFIG->pluginspath . "predictions/actions/tradeout.php");
register_action("predictions/bet", false, $CONFIG->pluginspath . "predictions/actions/bet.php");
register_action("predictions/settle", false, $CONFIG->pluginspath . "predictions/actions/settle.php");
register_action("predictions/void", false, $CONFIG->pluginspath . "predictions/actions/void.php");
?>
