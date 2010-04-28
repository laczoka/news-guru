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
                }

	// Make sure the blog initialisation function is called on initialisation
		register_elgg_event_handler('init','system','predictions_init');

register_action("predictions/save", false, $CONFIG->pluginspath . "predictions/actions/save.php");
?>
