<?php 
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING ORDERS WITH EITHER A FULL OR PARTIAL REFUND DUE */

add_shortcode("ds-refunds", "DeliveryShare_refundsloop");



function DeliveryShare_refundsloop($incomingfrompost) {
//process incoming attributes assigning defaults if required
  $incomingfrompost=shortcode_atts(array(
    "qty" => DeliveryShare_QTY,
    "sortby" => 'date'
  ), $incomingfrompost);  
  //run functions that actually do the work of the plugin
  $DeliveryShare_output = deliveryshare_refund_process_queries($incomingfrompost);
  $DeliveryShare_output .= deliveryshare_refund_calcs($incomingfrompost);
  $DeliveryShare_output .= deliveryshare_refund_whole_orders($incomingfrompost);
  //send back text to replace shortcode in post
  return $DeliveryShare_output;
} //close function DeliveryShare_refundsloop



function deliveryshare_refund_process_queries($incomingfromhandler){
//get query parameters
$ds_refund_refundstatus = sanitize_text_field( $_GET['refstatus'] ); //refund status. options : 'full' or 'partial'
$ds_refund_refundedorder = sanitize_text_field( $_GET['reforder'] ); //order ID that was partially or fully refunded
$ds_refund_refundamount = sanitize_text_field( $_GET['refamt'] ); //refunded amount for 'order_refunded_amount' cf
if($ds_refund_refundstatus && $ds_refund_refundedorder && $ds_refund_refundamount){
//get woocommerce order as object
$order = new WC_Order($ds_refund_refundedorder);
//get associated custom field values
$ds_refund_order_items_to_be_refunded = get_post_meta( $ds_refund_refundedorder, 'order_items_to_be_refunded', true );
$ds_refund_order_items_refunded = get_post_meta( $ds_refund_refundedorder, 'order_items_refunded', true );
$ds_refund_previous_refunded_total = get_post_meta( $ds_refund_refundedorder, 'order_refunded_amount', true );
//move refunded items custom field value to 'order_items_refunded' and delete value for items to be refunded
update_post_meta($ds_refund_refundedorder, 'order_items_refunded', $ds_refund_order_items_to_be_refunded, $ds_refund_order_items_refunded);
update_post_meta($ds_refund_refundedorder, 'order_items_to_be_refunded', '', $ds_refund_order_items_to_be_refunded);
//update total amount refunded for order
$ds_refund_new_refunded_total = $ds_refund_refundamount;
update_post_meta($ds_refund_refundedorder, 'order_refunded_amount', $ds_refund_new_refunded_total);
//create refund status type to represent full or partial refunds
if($ds_refund_refundstatus == 'full'){ $ds_refund_confirmation_type = 'fully'; } else { $ds_refund_confirmation_type = 'partially'; };
//set order status to complete and add status note
$ds_refund_confirmation = 'Order #' . $ds_refund_refundedorder . ' ' . $ds_refund_confirmation_type . ' refunded in the amount of ' . $ds_refund_refundamount;
$order->update_status('completed', $ds_refund_confirmation);
//output status bar once completed
$ds_refund_confirmation_string = '<div class="ds-message-normal">' . $ds_refund_confirmation . '</div>';
//return compiled string
return $ds_refund_confirmation_string;
} // close if check for query value emptiness
} //close function deliveryshare_refund_process_queries



function deliveryshare_refund_calcs($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['driverqty'] );
if($order_qty == ''){$order_qty = 25;}
//loop arguments
 $args = array (
 'post_type' => 'shop_order',
 'posts_per_page' => $order_qty,
 'order'=> 'ASC',
 'orderby'=> 'date',
 'tax_query' => array(
		array(
			'taxonomy' => 'shop_order_status',
			'field' => 'slug',
			'terms' => array('Partial Refund Due')
			)
		)
 );
 $posts = new WP_Query($args);
//start output
  $DeliveryShare_output2 = '<div id="DeliveryShareList"><h2>Orders with a Partial Refund Due</h2><ul><li>
    <div class="WDclaimcol1">Order #</div>
    <div class="WDclaimcol2">Date</div>
    <div class="WDclaimcol3">Item IDs</div>
    <div class="WDclaimcol4">Total Due to Customer</div>
    <div class="WDclaimcol5">Refunded</div>
    <div style="clear:both;"></div></li>';
 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $order = new WC_Order($GLOBALS['post']->ID);
            $orderID = $GLOBALS['post']->ID;            
            $deliveryDate = $custom["What day would you like this delivery made on?"][0];
            $ds_refund_due_total = 0; //reset refund due 
            $ds_refund_order_email = $order->billing_email;            
            $ds_ItemsToBeRefunded = $custom["order_items_to_be_refunded"][0]; 
            //split items to be refunded into array
           $ds_ItemsToBeRefunded_Array = explode(',',$ds_ItemsToBeRefunded);            
           //start loop of order items to check against refunds needed
           $ds_order_item_array = $order->get_items();
           foreach($ds_order_item_array as $item) 
            {
              $product = new WC_Product($OrderID);
              $ds_this_id += 1;
              $ds_order_item_product_id = $item['product_id'];             
             //if product id matches one of the itemstoberefunded array, get quantity of item and price for each item             
             if ( in_array($ds_order_item_product_id, $ds_ItemsToBeRefunded_Array) ){
               $price = get_post_meta( $ds_order_item_product_id, '_regular_price', true);
               $ds_this_qty = $item['qty']; /* Quantity */               
               //use qty and price of matched item to determine total refund for this item. add value to accrued refund due amount
               $ds_refund_due_total += $price * $ds_this_qty;
             }
            } //close items to be refunded loop
            $ds_refund_done_link = '?refstatus=partial&reforder=' . $orderID . '&refamt=' . $ds_refund_due_total;
	$DeliveryShare_output2 .= '<li>
    <div class="WDclaimcol1"><a href="' . get_the_permalink() . '">#' . $orderID . '</a></div>
    <div class="WDclaimcol2">' . $deliveryDate . '</div>
    <div class="WDclaimcol3">' . $ds_ItemsToBeRefunded . '</div>
    <div class="WDclaimcol4">Refund $' . $ds_refund_due_total . ' via PayPal to <a href="mailto:' . $ds_refund_order_email . '">' . $ds_refund_order_email . '</a></div>
    <div class="WDclaimcol5"><input type="button" class="button" value="Done" onClick="parent.location=\'' . $ds_refund_done_link . '\'" /></div>
    <div style="clear:both;"></div></li>';           
   endwhile;} else {
 return '<h2>Orders with a Partial Refund Due</h2> Sorry, no orders requiring a partial refund found.' ; // no posts found 
 }
$DeliveryShare_output2 .= '</ul></div>'; //close #DeliveryShareList
  wp_reset_query();
   if(current_user_can( 'manage_options' )){ return html_entity_decode($DeliveryShare_output2); } else { return 'Sorry, you must be an administrator to view refunds.'; }
} // close function deliveryshare_refund_calcs()




function deliveryshare_refund_whole_orders($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['driverqty'] );
if($order_qty == ''){$order_qty = 25;}
//loop arguments
 $args = array (
 'post_type' => 'shop_order',
 'posts_per_page' => $order_qty,
 'order'=> 'ASC',
 'orderby'=> 'date',
 'tax_query' => array(
		array(
			'taxonomy' => 'shop_order_status',
			'field' => 'slug',
			'terms' => array('Need To Refund')
			)
		)
 );
 $posts = new WP_Query($args);
//start output
  $DeliveryShare_output3 = '<div id="DeliveryShareList"><h2>Orders with a Full Refund Due</h2><ul><li>
    <div class="WDclaimcol1">Order #</div>
    <div class="WDclaimcol2">Date</div>
    <div class="WDclaimcol4">Total Due to Customer</div>
    <div class="WDclaimcol5">Done</div>
    <div style="clear:both;"></div></li>';
 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $order = new WC_Order($GLOBALS['post']->ID);
            $orderID = $GLOBALS['post']->ID;            
            $deliveryDate = $custom["What day would you like this delivery made on?"][0];
            $ds_refund_order_email = $order->billing_email;
            $ds_order_total = $order->get_subtotal_to_display();
            $ds_refund_done_link = '?refstatus=full&reforder=' . $orderID . '&refamt=' . $ds_order_total;
	          $DeliveryShare_output3 .= '<li>
            <div class="WDclaimcol1"><a href="' . get_the_permalink() . '">#' . $orderID . '</a></div>
            <div class="WDclaimcol2">' . $deliveryDate . '</div>
            <div class="WDclaimcol4">Refund ' . $ds_order_total . ' via PayPal to <a href="mailto:' . $ds_refund_order_email . '">' . $ds_refund_order_email . '</a></div>
            <div class="WDclaimcol5"><input type="button" class="button" value="Done" onClick="parent.location=\'' . $ds_refund_done_link . '\'" /></div>
            <div style="clear:both;"></div></li>';           
   endwhile;} else {
 return '<h2>Orders with a Full Refund Due</h2> Sorry, no orders requiring a full refund found.' ; // no posts found 
 }
$DeliveryShare_output3 .= '</ul></div>'; //close #DeliveryShareList
  wp_reset_query();
   if(current_user_can( 'manage_options' )){ return html_entity_decode($DeliveryShare_output3); } else { return 'Sorry, you must be an administrator to view refunds.'; }
} // close function deliveryshare_refund_whole_orders()

?>