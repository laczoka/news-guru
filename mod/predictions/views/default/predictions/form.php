<div class="contentWrapper">
<form action="<?php echo $vars['url']; ?>action/predictions/save" method="post">
 
<p><?php echo elgg_echo("title"); ?><br />
<?php echo elgg_view('input/text',array('internalname' => 'title')); ?></p>
 
<p><?php echo elgg_echo("body"); ?><br />
<?php echo elgg_view('input/longtext',array('internalname' => 'body')); ?></p>
 
<p><?php echo elgg_echo("tags"); ?><br />
<?php echo elgg_view('input/tags',array('internalname' => 'tags')); ?></p>
 
<?php echo elgg_view('input/securitytoken'); ?>
 
<p><?php echo elgg_view('input/submit', array('value' => elgg_echo('save'))); ?></p>
 
</form>
</div>
