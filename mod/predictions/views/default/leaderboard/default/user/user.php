<?php $user = $vars['entity'] ?>
<?php $order_no = $vars['order_no'] ?>
<div style="padding-left: 10px; padding-bottom: 5px">
 <div style="float:left;"><?php echo $order_no."." ?></div>
 <div style="float:left;padding-left: 5px">
    <img style="float:left;" src="<?php echo $user->getIcon("small"); ?>" border="0" alt="<?php echo htmlentities($vars['entity']->username, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlentities($vars['entity']->username, ENT_QUOTES, 'UTF-8'); ?>" />
    <div style="padding-left: 5px;float:left;">
             Total Net Asset $ <?php echo isset($user->report_total_net_asset_value) ? $user->report_total_net_asset_value : $user->opendollars ?><br />
             Free Cash Balance $ <?php echo $user->opendollars ?>
    </div>
    <div style="clear:both;"><a href="<?php echo $user->getURL()?>" ><?php echo $user->username ?></a></div>
 </div>
 <div style="clear:both"></div>
</div>