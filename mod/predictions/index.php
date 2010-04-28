<?php

include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

$body = list_entities('object','predictions',0,10,false);

		// Get categories, if they're installed
		global $CONFIG;
		$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=blog&owner_guid='.$page_owner->guid.'&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));

	// Display them in the page
        $body = elgg_view_layout("two_column_left_sidebar", '', $body, $area3);
 
page_draw("Predictions",$body);

?>
