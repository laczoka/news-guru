<?php echo elgg_view_title($vars['entity']->title); ?>
 
<div class="contentWrapper">
 
<p><?php echo $vars['entity']->description; ?></p>
 
<?php echo elgg_view('output/tags', array('tags' => $vars['entity']->tags)); ?>
 
</div>
