<?php
 /* flock based lock on a resource identified by a unique integer 
  *  returns true if the lock was successfully acquired, false otherwise
  */
 function LOCK_RESOURCE($id) {
 	if (!is_numeric($id)) return NULL;
 	
 	$mutex = fopen("/tmp/resource_".$id."_lock","w+");
 	return ($mutex && flock($mutex, LOCK_EX)) ? $mutex : NULL;
 }
 /* 
  * release flock  
  */
 function UNLOCK_RESOURCE($mutex) {
    if ($mutex) {
    	flock($mutex, LOCK_UN);
    }
 }
 
?>