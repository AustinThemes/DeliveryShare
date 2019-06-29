<?php 
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING ORDERS ASSIGNED TO THE CURRENT DRIVER */
add_shortcode("ds-current-user-orders", "DeliveryShare_handler2");
function DeliveryShare_handler2($incomingfrompost) {
//process incoming attributes assigning defaults if required
  $incomingfrompost=shortcode_atts(array(
    "qty" => DeliveryShare_QTY,
    "sortby" => 'date'
  ), $incomingfrompost);  
  //run function that actually does the work of the plugin
  $DeliveryShare_output = DeliveryShare_PerDriver($incomingfrompost);  
  //send back text to replace shortcode in post
  return $DeliveryShare_output;
}
function DeliveryShare_PerDriver($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['driverqty'] );
$order_sort = sanitize_text_field( $_GET['sortby'] );
if(!$order_sort){$order_sort = 'date';}




$current_user = wp_get_current_user();

$CurrentUser = $current_user->user_login;






if($order_qty == ''){$order_qty = 25;}

//loop arguments
 $args = array (
 'post_type' => 'shop_order',
 'posts_per_page' => $order_qty,
 'order'=> 'ASC',
 'orderby'=> $order_sort,
 'meta_key' => 'order_assigned_driver',
 'meta_value' => $CurrentUser,
 'post_status' => array('wc-processing','wc-complete','wc-completed')
 );
 $posts = new WP_Query($args);

//start output
  $DeliveryShare_output2 = '<div id="DeliveryShareList" class="ds-my-assigned-orders-list-container"><ul class="ds-my-assigned-orders-list-UL"><li class="ds-my-assigned-orders-list-titlerow">
  <div class="ds-my-assigned-orders-list-title col-xs-12  col-sm-2"><strong>Delivery Date</strong></div>
  <div class="ds-my-assigned-orders-list-title col-xs-12  col-sm-2"><strong>Delivery Status</strong></div>
  <div class="ds-my-assigned-orders-list-title col-xs-12  col-sm-5"><strong>Address</strong></div>
  <div class="ds-my-assigned-orders-list-title col-xs-12  col-sm-3"><strong>Order ID</strong></div>
  <div style="clear:both;"></div></li>';
  
  

 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $order = new WC_Order($GLOBALS['post']->ID);
            $orderID = $GLOBALS['post']->ID;
            $orderStatus = get_post_status($orderID);
                
            $deliveryDate = $custom["What day would you like this delivery made on?"][0];            
            $currentDate = date("m/d/Y");
            if($deliveryDate < $currentDate){$DateClass = 'WD-date-expired';} else {$DateClass = 'WD-date-current';}
            
            $deliveryTime = $custom["What time would you like this delivery made?"][0];
            $deliveryAddress = $order->get_shipping_address();
            $deliveryAddressForLink = $order->get_shipping_address();
            $deliveryMapLink = '<a href="http://maps.google.com/?q=' . $deliveryAddressForLink . '" target="_new" class="TransInputSubmit WD-ClaimButton">Map</a>';
            $cancelorderlink = '<a href="?unclaim=' . $orderID . '" class="TransInputSubmit WD-ClaimButton">Unclaim</a>';
            $orderlink = '<a href="' . get_edit_post_link() .'" class="TransInputSubmit WD-ClaimButton">i</a>';
           

	$DeliveryShare_output2 .= '<li>
  <div class="col-xs-12  col-sm-2"><strong class="' . $DateClass . '">' . $deliveryDate . '</strong></div>
  <div class="col-xs-12  col-sm-2">' . $deliveryTime . ' (' . $orderStatus . ')</div>
  <div class="col-xs-12  col-sm-5">' . $deliveryAddress . '</div>
  <div class="col-xs-12  col-sm-3">' . get_the_id() . '</div>
  <div style="clear:both;"></div></li>';
           
   endwhile;} else {
 return 'Sorry, no orders found with your criteria.' ; // no posts found
}

$DeliveryShare_output2 .= '</ul></div>'; //close #DeliveryShareList
  wp_reset_query();
   if(current_user_can( 'manage_options' ) || current_user_can( 'read_shop_order' ) || current_user_can('read_private_shop_orders')){ return html_entity_decode($DeliveryShare_output2); } else { return 'Sorry, you don\'t have the necessary user role to access shop orders.'; }

} // close the big function
?>