<?php 

//-------------- FLAGGED ORDERS CHECK AND ALERT -----------------
  
function ds_flagged_orders_notice(){

        $args3 = array(
     'post_type'   => 'shop_order',
     'post_status' => array( 'wc-complete' ),
    );  
    $posts3 = new WP_Query($args3);
    $WDordersWithFlags = 0;
    $WDordersWithFlagsURL = '';    

    if ($posts3->have_posts()){
           
        while ($posts3->have_posts()):
            $posts3->the_post();
            $WDorderID = get_the_ID(); 
            $WDorderExistingFlags = get_post_meta($WDorderID, 'order_flags', true);
            if($WDorderExistingFlags){
		$WDordersWithFlags += 1;
		$WDordersWithFlagsURL .= '<a href="http://www.speedygrocer.com/wp-admin/post.php?post=' . $WDorderID  . '&action=edit" target="_new">#' . $WDorderID . '</a>,';
            }
                  
        endwhile;

   }//end if($posts..
wp_reset_query();

   //finally, generate and add the notice if flagged orders were detected

	if ( $WDordersWithFlags > 0 && current_user_can( 'manage_options' ) ){ echo ' <div class="updated ds-flagged-orders-alert">
<p><strong><span class="ds-flagged-orders-alert-intro" style="color:red;">DELIVERY SYSTEM ALERT!</span></strong> Flagged customer orders found. Orders affected: ' . $WDordersWithFlagsURL . '</p>
          </div>'; }      


}
add_action( 'admin_notices', 'ds_flagged_orders_notice' );


?>