<?php 
if ( ! defined( 'ABSPATH' ) ) die();


/* =================================== SHORTCODE FOR DISPLAYING DRIVER POOL */
add_shortcode("ds-ordersbydriver", "DeliveryShare_ordersbydriverpool_handler");
function DeliveryShare_ordersbydriverpool_handler($incomingfrompost) {
//process incoming attributes assigning defaults if required
  $incomingfrompost=shortcode_atts(array(
    "qty" => DeliveryShare_QTY,
    "sortby" => 'date',
    "status" => $driver_status,
  ), $incomingfrompost);
  //run function that actually does the work of the plugin
  $DeliveryShare_output = DeliveryShare_ordersbydriverpool_function($incomingfrompost);  
  //send back text to replace shortcode in post
  return $DeliveryShare_output;
}
function DeliveryShare_ordersbydriverpool_function($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['qty'] );
$order_sort = sanitize_text_field( $_GET['sortby'] );
if(!$order_sort){$order_sort = 'date';}
if($order_qty == ''){$order_qty = 25;}

//if no driver status passed, do not query meta fields


//loop arguments
$args = array (
       'post_type' => 'shop_order',
       'posts_per_page' => $order_qty,
       'order'=> 'ASC',
       'orderby'=> $order_sort,
       'post_status' => array('wc-processing','wc-complete','wc-completed')
);

 $posts = new WP_Query($args); 
 
  $DeliveryShare_output = '<div id="Wooordersbydriverpool-UL"><ul>';
if($driver_status){ $DeliveryShare_output .= '<h2 class="WDordersbydriverpool-h2">' . $driver_status . '</h2>';} else { $DeliveryShare_output .= '<h2 class="WDordersbydriverpool-h2">Orders by Driver</h2>'; }

 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $driverobjectID = $GLOBALS['post']->ID;
            $CurrentDriverStatus = get_post_meta( $driverobjectID, 'order_assigned_driver', true );
            $CurrentDriverRatingString = get_post_meta( $driverobjectID, 'order_driver_rating', true );

           
            	$DeliveryShare_output .= '
              <li class="WDordersbydriverpool-driver-listing">
              <div class="WDordersbydriverpool-col1"><strong class="' . $DateClass . '"><a href="' . get_the_permalink() . '">#' . get_the_ID() . '</a></strong></div><div class="WDordersbydriverpool-listing-info-container">
              <div class="WDordersbydriverpool-col2">' . $CurrentDriverStatus . '</div>
              <div class="WDordersbydriverpool-col3">' . $CurrentDriverRatingString . '</div>
                            <div style="clear:both;"></div>
              </li>';
           
   endwhile;} else {
 return 'Sorry, no drivers found with your criteria.' ; // no posts found
}

$DeliveryShare_output .= '</ul></div>'; //close #DeliveryShareList and get ready for the orders loop
wp_reset_query();

  
  if(current_user_can( 'manage_options' ) || current_user_can( 'read_shop_order' ) || current_user_can('read_private_shop_orders')){ return html_entity_decode($DeliveryShare_output); } else { return 'Sorry, you don\'t have the necessary user role to access shop orders.'; }
   

} // close the big function
?>