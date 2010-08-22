<div class="contentWrapper">
    <form action="<?php echo $vars['url']; ?>action/predictions/save" method="post">

        <p><?php echo elgg_echo("title"); ?><br />
            <?php echo elgg_view('input/text',array('internalname' => 'title', 'value' => $vars['title'])); ?></p>

        <p><?php echo elgg_echo("body"); ?><br />
            <?php echo elgg_view('input/longtext',array('internalname' => 'body', 'value' => $vars['body'])); ?></p>

        <p><?php echo elgg_echo("tags"); ?><br />
            <?php echo elgg_view('input/tags',array('internalname' => 'tags', 'value' => $vars['tags'])); ?></p>

        <p><?php echo elgg_echo("Left Hand Box : Option 1 / Starting Value % (do not include % sign)"); ?><br />

            <input type="text" style="border-color: black" name="option1"  value="<?php echo $vars['option1']?>" class="xinput-text"/>
            <input type="text" style="border-color: black" name="value1"  value="<?php echo $vars['value1']?>" class="xinput-text"/> <b>%</b></p>

        <p><?php echo elgg_echo("Left Hand Box : Option 2 / Starting Value % (do not include % sign)"); ?><br />

            <input type="text" style="border-color: black" name="option2"  value="<?php echo $vars['option2']?>" class="xinput-text"/>
            <input type="text" style="border-color: black" name="value2"  value="<?php echo $vars['value2']?>" class="xinput-text"/> <b>%</b></p>


          <p><?php echo elgg_echo("Suspend Time"); ?><br />
          <?php echo elgg_view('input/text',array('internalname' => 'suspend', 'internalid' => 'create_new_market_suspend_time',
                                                 'value' => $vars['suspend'])); ?>
          <input id="create_new_market_suspend_time_utc" name="suspend_utc" type="hidden" value="<?php echo $vars['suspend_utc']?>" />
        
          <script type="text/javascript">
                var date_format = "%b/%e/%z %l:%i %p (%@)";
                var dateConv = new AnyTime.Converter({format:date_format});
                var tomorrow = dateConv.format(new Date((new Date).getTime()+24*60*60*1000));
                if ($.cookie("news-guru_tz")) {
                    tomorrow = tomorrow.replace(/\(.*\)/, "("+$.cookie("news-guru_tz")+")" );
                }
                $("#create_new_market_suspend_time")
<?php if (!isset($vars['suspend'])): ?>
                .val(tomorrow) 
<?php endif; ?>
                .AnyTime_picker
                ({
             	   format : date_format   
              	 })
              	.change(function(){
              	    // set a cookie to remember user preference
              	    var tzregex = /\(.*\)/;
              	    var tz = tzregex.exec($(this).val())[0];
              	    if ((tz.charAt(0) == "(") && (tz.charAt(tz.length-1) == ")"))
                  	    tz = tz.substring(1,tz.length - 1) ;            	
              		$.cookie("news-guru_tz", tz);
              		// set UTC timestamp
              		$("#create_new_market_suspend_time_utc")
              		    .val(dateConv.parse($(this).val()).toUTCString());
              	});
<?php if (!isset($vars['suspend_utc'])): ?>
                $("#create_new_market_suspend_time_utc")
                .val(dateConv.parse($("#create_new_market_suspend_time").val()).toUTCString());
<?php endif; ?>
          </script>
        <p><?php echo elgg_echo("Settlement Details"); ?><br />
            <?php echo elgg_view('input/text',array('internalname' => 'settlement', 'value' => $vars['settlement'])); ?></p>


        <?php echo elgg_view('input/securitytoken'); ?>

        <p><?php echo elgg_view('input/submit', array('value' => elgg_echo('save'))); ?></p>

    </form>
</div>
