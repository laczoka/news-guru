<?php 
	$created_by = get_entity($vars['item']->subject_guid);
	$market = get_entity($vars['item']->object_guid);
	$market_desc = strip_tags($market->description);
	$market_short_desc = 
		(strlen($market_desc) > 200) ? 
			substr($market_desc, 0, strpos($market_desc, ' ', 200)) . "..." : $market_desc;
?>
<a href="<?php echo $created_by->getURL() ?>"><?php echo $created_by->name ?></a> has created the question <a href="<?php echo $market->getURL() ?>"><?php echo $market->title ?></a>.
Be the first to place your bets!
<div class="river_content_display">
	<?php echo $market_short_desc ?>
</div>