<?php
global $CONFIG;

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
    
    register_plugin_hook('cron', 'halfhour', 'predictions_update_leaderboard_cron');
    
    elgg_extend_view('css', 'predictions/css');
    elgg_extend_view('metatags', 'predictions/js');
    
}

function predictions_update_leaderboard_cron() {
	try {
	   // indicate that this is a cron job to prevent response generation and save CPU
	   set_input('cron',1);
	   include dirname(__FILE__).'/actions/update_leaderboard.php';
	} catch (Exception $e) {
		error_log("Exception occured while executing update_leaderboard: ".$e->getMessage());
	}
}



// Make sure the blog initialisation function is called on initialisation
register_elgg_event_handler('init','system','predictions_init');

register_action("predictions/save", false, $CONFIG->pluginspath . "predictions/actions/save.php");
register_action("predictions/tradeout", false, $CONFIG->pluginspath . "predictions/actions/tradeout.php");
register_action("predictions/bet", false, $CONFIG->pluginspath . "predictions/actions/bet.php");
register_action("predictions/settle", false, $CONFIG->pluginspath . "predictions/actions/settle.php");
register_action("predictions/void", false, $CONFIG->pluginspath . "predictions/actions/void.php");
register_action("predictions/suspend", false, $CONFIG->pluginspath . "predictions/actions/suspend.php");
register_action("predictions/update_leaderboard", false, $CONFIG->pluginspath . "predictions/actions/update_leaderboard.php");

?>
