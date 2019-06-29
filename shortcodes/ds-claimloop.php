<?php 
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING CLAIMABLE (UNASSIGNED) ORDERS and CREATE CLAIM API */
add_shortcode("ds-claimloop", "DeliveryShare_handler");
function DeliveryShare_handler($incomingfrompost) {
//process incoming attributes assigning defaults if required
  $incomingfrompost=shortcode_atts(array(
    "qty" => DeliveryShare_QTY,
    "sortby" => 'date'
  ), $incomingfrompost);
  //run function that actually does the work of the plugin
  $DeliveryShare_output = DeliveryShare_function($incomingfrompost);  
  //send back text to replace shortcode in post
  return $DeliveryShare_output;
}
function DeliveryShare_function($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['qty'] );
$order_claim = sanitize_text_field( $_GET['claim'] );
$order_sort = sanitize_text_field( $_GET['sortby'] );
if(!$order_sort){$order_sort = 'date';}
$order_unclaim = sanitize_text_field( $_GET['unclaim'] );
if($order_qty == ''){$order_qty = 25;}

//if a claim id is passed, check to make sure a driver is not assigned, then assign that order to driver before running updated loop
if($order_claim){
//get current user's id
$CurrentUserID = get_current_user_id();
//find whether order with claim id already has a driver assigned
$ClaimCurrentDriverStatus = get_post_meta( $order_claim, 'driver', true );
//set driver meta field for order if no driver assigned
if(!$ClaimCurrentDriverStatus){update_post_meta($order_claim, 'driver', $CurrentUserID);echo '<div class="woocommerce-message">You have claimed order #' . $order_claim . '</div>';}
} //close claim check

//unclaim order if unclaim id passed
if($order_unclaim){
$CurrentUserID = get_current_user_id(); //get current user's id 
$ClaimCurrentDriverStatus = get_post_meta( $order_unclaim, 'driver', true ); //get driver user id currently associated
if( $ClaimCurrentDriverStatus == $CurrentUserID ){ update_post_meta($order_unclaim, 'driver', '');echo '<div class="woocommerce-message">You have unclaimed and released order #' . $order_claim . ' for reassignment.</div>'; } else { echo '<div class="woocommerce-message">Error : order not unclaimed. Only orders assigned to the current user will be unclaimed. If you feel this is an error, please contact your webmaster.</div>'; }
} //close unclaim check



//loop arguments
 $args = array (
 'post_type' => 'shop_order',
 'posts_per_page' => $order_qty,
 'order'=> 'ASC',
 'orderby'=> $order_sort,
 'tax_query' => array(
		array(
			'taxonomy' => 'shop_order_status',
			'field' => 'slug',
			'terms' => array('processing')
			)
		)
);
 $posts = new WP_Query($args); 
 
  $DeliveryShare_output = '<div id="DeliveryShareList"><ul>'; //end var $DeliveryShare_output 

 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $order = new WC_Order($GLOBALS['post']->ID);
            $orderID = $GLOBALS['post']->ID;
            $driverID = $custom["driver"][0];
            
            $deliveryDate = $custom["What day would you like this delivery made on?"][0];            
            $currentDate = date("m/d/Y");
            if($deliveryDate < $currentDate){$DateClass = 'WD-date-expired';} else {$DateClass = 'WD-date-current';}
                      
            $deliveryTime = $custom["What time would you like this delivery made?"][0];
            $deliveryAddress = $order->get_shipping_address();
            $deliveryAddressForLink = $order->get_shipping_address();
            $CurrentPage = $_SERVER['REQUEST_URI'];
            $deliveryMapLink = '<a href="http://maps.google.com/?q=' . $deliveryAddressForLink . '" target="_new" class="TransInputSubmit WD-ClaimButton">Map</a>';
            $orderlink = '<a href="http://www.speedygrocer.com/driver-order-checklist/?order=' . $orderID . '" class="TransInputSubmit WD-ClaimButton">View</a>';
            $user_info = get_userdata($driverID);
            $driverName = $user_info->first_name . ' ' . $user_info->last_name;           

            if ($driverID == ''){
            $WooClaimButton = '<a href="http://www.speedygrocer.com/driver-order-checklist/?order=' . $orderID . '&status=shopping" class="TransInputSubmit WD-ClaimButton">Shop Now</a>';
            } else {
            $WooClaimButton = '<div class="WD-Claimed">Claimed by:<br/><strong>' . $driverName . '</strong></div>';
            }

	if ($driverID == ''){$DeliveryShare_output .= '<li><div class="WDclaimcol1"><strong class="' . $DateClass . '">' . $deliveryDate . '</strong></div><div class="WDclaimcol2">' . $deliveryTime . '</div><div class="WDclaimcol3">' . $deliveryAddress . '</div><div class="WDclaimcol4">' . $WooClaimButton . ' ' . $deliveryMapLink . $orderlink . '</div><div style="clear:both;"></div></li>';}
           
   endwhile;} else {
 return 'Sorry, no orders found with your criteria.' ; // no posts found
}

$DeliveryShare_output .= '</ul></div>'; //close #DeliveryShareList
  wp_reset_query();
  
  if(current_user_can( 'manage_options' ) || current_user_can( 'read_shop_order' ) || current_user_can('read_private_shop_orders')){ return html_entity_decode($DeliveryShare_output); } else { return 'Sorry, you don\'t have the necessary user role to access shop orders.'; }
   

} // close the big function
?>