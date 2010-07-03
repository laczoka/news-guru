<?php

require_once 'predictionfacade.php';

class PredicitionFacadeEAV implements PredictionFacade
{
	public function savemarket($title, $body, $owner_guid, $tags, $options, $suspend, $settlement) {
		// create a new object
        $prediction = new ElggObject();
        $prediction->title = $title;
        $prediction->description = $body;
        $prediction->subtype = "predictions";

        // for now make all predictions public
        $prediction->access_id = ACCESS_PUBLIC;

        // owner is logged in user
        $prediction->owner_guid = $owner_guid;
    
		// save tags as metadata
		$prediction->tags = $tags;
		
		
		// set options
		if (!empty($options[0]['option']) && !empty($options[0]['value'])) {
		    $prediction->option1 = $options[0]['option'];
		    $prediction->value1 = $options[0]['value'];
		}
		
		if (!empty($options[1]['option']) && !empty($options[1]['value'])) {
		    $prediction->option2 = $options[1]['option'];
            $prediction->value2 = $options[1]['value'];
		}
		
		$prediction->suspend = $suspend;
		
		$prediction->settlement = $settlement;
		
		$prediction->status = 'open';
		
		// save to database
		$prediction->save();
	}
}
?>