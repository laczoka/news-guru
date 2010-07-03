<?php
interface PredictionFacade
{
	public function savemarket($title, $body, $owner_guid, $tags, $options, $suspend, $settlement);
}
?>