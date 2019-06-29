<?php
/************* DASHBOARD WIDGET *****************/

// RSS Dashboard Widget
function nova_rss_dashboard_widget_bbcnews_politics() {
	if ( function_exists( 'fetch_feed' ) ) {
		include_once( ABSPATH . WPINC . '/feed.php' );               
		$feed = fetch_feed( 'http://feeds.bbci.co.uk/news/politics/rss.xml' );        
		$limit = $feed->get_item_quantity(4);                      
		$items = $feed->get_items(0, $limit);                      
	}
	if ($limit == 0) echo '<div>The RSS Feed is either empty or unavailable.</div>';  
	else foreach ($items as $item) { ?>

	<h4 style="margin-bottom: 0;">
		<a href="<?php echo $item->get_permalink(); ?>" title="<?php echo mysql2date( __( 'j F Y @ g:i a', 'bbc-pol' ), $item->get_date( 'Y-m-d H:i:s' ) ); ?>" target="_blank">
			<?php echo $item->get_title(); ?>
		</a>
	</h4>
	<p style="margin-top: 0.5em;">
		<?php echo substr($item->get_description(), 0, 200); ?>
	</p>
	<?php }
}

function nova_custom_dashboard_widgets_bbcnews_politics() {
	wp_add_dashboard_widget( 'nova_rss_dashboard_widget_bbcnews_politics', __( 'Recently From BBC News Politics', 'bbc-pol' ), 'nova_rss_dashboard_widget_bbcnews_politics' );
}

add_action( 'wp_dashboard_setup', 'nova_custom_dashboard_widgets_bbcnews_politics' );

      ?>