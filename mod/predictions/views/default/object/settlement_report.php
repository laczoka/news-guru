<?php $settlement_report = $vars['entity'];
      $market = get_entity($settlement_report->market);
      // this is a HACK should return deserialized stuff immediatelly
      $report_content = unserialize($vars['entity']->report);
 ?>
<?php echo elgg_view_title('<a href="' . $settlement_report->getURL() . '">'
        . $settlement_report->title . '</a>'); ?>
<div class="contentWrapper">
<table style="margin: 10px;font-size: 16px;">
    <tbody>
    <tr>
        <td style="font-weight: bold">Market&nbsp&nbsp</td><td ><a href="<?php $market->getURL() ?>"><?php echo $market->title ?></a></td>
    </tr>
    <?php if (isset($market->suspended_utc)): /* legacy markets may not have this attribute */?>
    <tr>
        <td style="font-weight: bold">Trade suspended</td><td><?php echo date("d/M/Y H:i:s",$market->suspended_utc) ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td style="font-weight: bold">Settled &nbsp&nbsp</td><td><?php echo date("d/M/Y H:i:s",$settlement_report->getTimeCreated()) ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold">Outcome &nbsp&nbsp</td><td><?php echo $market->outcome ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold; border-bottom: dashed 1px " colspan="2">Public belief at settlement</td>
    </tr>
    <tr>
        <td><?php echo $market->option1 ?></td><td style="font-weight:bold"><?php echo round($market->value1*100, 0) ?>%</td>
    </tr>
    <tr>
        <td><?php echo $market->option2 ?> </td><td style="font-weight:bold"> <?php echo round($market->value2*100, 0) ?>%</td>
    </tr>
    </tbody>
</table>
<br />
<table id="settlementReport" class="predictions_table tablesorter">
<thead>
<tr class="odd">
    <th scope="col" class="col_3_of_12">Owner</th>
    <th scope="col" class="col_2_of_12">Wager</th>
    <th scope="col" class="col_1_of_12">Trade time</th>
    <th scope="col" class="col_1_of_12">Price</th>
    <th scope="col" class="col_1_of_12">Return</th>
</tr>
</thead>
<tbody>
<?php $rowcnt = 0 ;?>
<?php foreach ($report_content as $win): ?>
<tr <?php echo ($rowcnt++ % 2) ? 'class="odd"' : 'class="even"' ?> >
    <td><a href="<?php echo $win['owner_url'] ?>"><?php echo $win['owner_name'] ?></a></td>
    <td><a href="<?php echo $win['tr_url'] ?>"><?php echo $win['option_name'] ?></a></td>
    <td ts="<?php echo $win['tr_created'] ?>"><?php echo str_replace(" ","<br />",date("d/M H:i:s",$win['tr_created'])) ?></td>
    <td><?php echo round($win['price'] * 100) ?>%</td>
    <td>+$<?php echo $win['win'] ?></td> 
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<script type="text/javascript">

//add parser for +/-$[number] format 
$.tablesorter.addParser({ 
    // set a unique id 
    id: 'opendollar', 
    is: function(s) { 
        // return false so this parser is not auto detected 
        return false; 
    }, 
    format: function(s) { 
        // format your data for normalization 
        return s.replace("$","").replace("+",""); 
    }, 
    // set type, either numeric or text 
    type: 'numeric' 
});

$.tablesorter.addParser({ 
    // set a unique id 
    id: 'attrib-ts', 
    is: function(s) { 
        // return false so this parser is not auto detected 
        return false; 
    }, 
    format: function(s,table,cell) { 
        var ts = $(cell).attr("ts"); 
        return ts ? ts : 0; 
    }, 
    // set type, either numeric or text 
    type: 'numeric' 
});

$(document).ready(function() 
{ 
    $("#settlementReport").tablesorter({
        // initial sort by trade date, most recent first
        sortList: [[2,1]],
        // enable handling of "zebra" rows
        widgets: ['zebra'],

        // disable sorting on "Actions" tab
        // activate opendollar parser
        headers: { 2 : { sorter: 'attrib-ts' },
                   4 : { sorter: 'opendollar' }
                                            }}); 
}); 
</script>