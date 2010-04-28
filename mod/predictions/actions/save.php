<?php
  // only logged in users can add predictions
  gatekeeper();
 
  // get the form input
  $title = get_input('title');
  $body = get_input('body');
  $tags = string_to_tag_array(get_input('tags'));
 
  // create a new predictions object
  $prediction = new ElggObject();
  $prediction->title = $title;
  $prediction->description = $body;
  $prediction->subtype = "predictions";
 
  // for now make all predictions public
  $prediction->access_id = ACCESS_PUBLIC;
 
  // owner is logged in user
  $prediction->owner_guid = get_loggedin_userid();
 
  // save tags as metadata
  $prediction->tags = $tags;
 
  // save to database
  $prediction->save();
 
  // forward user to a page that displays the post
  forward($prediction->getURL());
?>
