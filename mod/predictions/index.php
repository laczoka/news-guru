<?php
$START_AMOUNT = 1000;
$DAILY_AMOUNT = 20;

include_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

$markets_per_page = 10;
$offset = get_input('offset', 0);
$market_query = array(
        'type'                  => "object",
        'subtype'               => "predictions",
        'limit'                 =>  $markets_per_page,
        'offset'                =>  $offset,
        'full_view'             => FALSE,
        'metadata_name_value_pairs_operator'=>  'OR',
        "metadata_name_value_pairs" => array(
                                        array(name => 'status', value => 'open'),
                                        array(name => 'status', value => 'suspended'))
                                           );

$markets = elgg_get_entities_from_metadata($market_query);
$no_of_markets = elgg_get_entities_from_metadata(array_merge($market_query, array(count => TRUE)));

$body = elgg_view_entity_list($markets, $no_of_markets, $offset, 10, false, false, true );

// float_images_to_left to win some space
$body .= 
'<script type="text/javascript">
   $("div.contentWrapper img").css("float","left").css("padding","4px 4px 4px 4px");
</script>';


// Get categories, if they're installed
global $CONFIG;



// Get the current page's viewer
$page_viewer = get_loggedin_user();

if ($page_viewer)
    if (!isset($page_viewer->opendollars) || $page_viewer->opendollars==null) {
        $page_viewer->opendollars = $START_AMOUNT;
        $page_viewer->lastdaily = time();
        system_message ('Thank you for playing the Prediction Markets, $'
            . $START_AMOUNT . ' have been credited to your account!');
    } else {
        if (!isset($page_viewer->lastdaily) || $page_viewer->lastdaily==null  || time() - $page_viewer->lastdaily > 3600*23 ) {
            $page_viewer->opendollars += $DAILY_AMOUNT;
            $page_viewer->lastdaily = time();
            system_message ('A Daily reward of $'
                . $DAILY_AMOUNT . ' has been credited to your account!');
        }
    }

add_submenu_item( 'Prediction Markets', $CONFIG->wwwroot . "pg/mod/predictions/");
if ($page_viewer) {
    add_submenu_item( 'Add a Market', $CONFIG->wwwroot . "pg/mod/predictions/add.php");
    add_submenu_item( 'Your Account', $CONFIG->wwwroot . "pg/mod/predictions/transactions.php");
}
add_submenu_item( 'Leaderboard', $CONFIG->wwwroot . "pg/mod/predictions/leaderboard.php");

if ($page_viewer) {
	$left = elgg_echo('predictions:disclaimer');
	$left .= '<br/><br/>You have $' . $page_viewer->opendollars . ' remaining<br/>';
	$left .= '<br/>' .  round(((+(3600*23) - time() + $page_viewer->lastdaily)/3600.0),2)  . ' hours until your next reward';
}
		// Get categories, if they're installed
		global $CONFIG;
		$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?search_type=tags&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));


// layout the page
$body = elgg_view_layout('two_column_left_sidebar', $left, $body, $area3);

page_draw("Predictions",$body);

?>
