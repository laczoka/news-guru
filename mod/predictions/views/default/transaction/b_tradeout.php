<form action="<?php echo $vars['url']; ?>action/predictions/tradeout" method="post">
        <input type="hidden" name="transaction"  value="<?php echo $vars['transaction']->guid ?>" />
        <?php echo elgg_view('input/securitytoken'); ?>
        <input type="submit" value="Trade Out" />
</form>