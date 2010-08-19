<?php

/* Expect date-time in a string format
 * e.g. RFC3339 2010-08-18T18:07:53+00:00
 * 
 * OR
 * 
 * UTC timestamp (Unix timestamp)
 * e.g. 1282154873
 */ 
function ng_has_date_passed($date_with_tz)
{
	$now_UTC_ts = time();
	$date_UTC_ts = is_numeric($date_with_tz) ? 
		$date_with_tz : strtotime($date_with_tz);
	return !(false === $date_UTC_ts || -1 == $date_UTC_ts)
	   && ($date_UTC_ts <= $now_UTC_ts);
}

?>