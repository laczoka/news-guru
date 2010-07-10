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
            <?php echo elgg_view('input/text',array('internalname' => 'suspend', 'value' => $vars['suspend'])); ?></p>

        <p><?php echo elgg_echo("Settlement Details"); ?><br />
            <?php echo elgg_view('input/text',array('internalname' => 'settlement', 'value' => $vars['settlement'])); ?></p>


        <?php echo elgg_view('input/securitytoken'); ?>

        <p><?php echo elgg_view('input/submit', array('value' => elgg_echo('save'))); ?></p>

    </form>
</div>
