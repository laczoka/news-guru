<?php 
	$performed_by = get_entity($vars['item']->subject_guid);
	$report = get_entity($vars['item']->object_guid);
	$market = get_entity($report->market);
	$market_desc = strip_tags($market->description);
	$market_short_desc = 
		(strlen($market_desc) > 200) ? 
			substr($market_desc, 0, strpos($market_desc, ' ', 200)) . "..." : $market_desc;
?>
<a href="<?php echo $performed_by->getURL() ?>"><?php echo $performed_by->name ?></a> has settled the question <a href="<?php echo $market->getURL() ?>"><?php echo $market->title ?></a>.<br />
Check out how much you won <a href="<?php echo $report->getURL() ?>">here</a>!
<div class="river_content_display">
	<?php echo $market_short_desc ?>
</div>