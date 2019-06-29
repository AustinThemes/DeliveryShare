<?php 
if ( ! defined( 'ABSPATH' ) ) die();


/* =================================== SHORTCODE FOR DISPLAYING LIST OF DRIVERS */
add_shortcode("ds-driverslist", "DeliveryShare_driverslist_handler");
function DeliveryShare_driverslist_handler($incomingfrompost) {
//process incoming attributes assigning defaults if required
  $incomingfrompost=shortcode_atts(array(
    "qty" => DeliveryShare_QTY,
    "sortby" => 'date',
    "status" => $driver_status,
  ), $incomingfrompost);
  //run function that actually does the work of the plugin
  $DeliveryShare_output = DeliveryShare_driverslist_function($incomingfrompost);  
  //send back text to replace shortcode in post
  return $DeliveryShare_output;
}
function DeliveryShare_driverslist_function($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['qty'] );
$order_sort = sanitize_text_field( $_GET['sortby'] );
if(!$order_sort){$order_sort = 'date';}
if($order_qty == ''){$order_qty = 25;}

//if no driver status passed, do not query meta fields


//if no driver status passed, do not query meta fields
if(!$driver_status){

        //loop arguments
       $args = array (
       'post_type' => 'driver',
       'posts_per_page' => $order_qty,
       'order'=> 'ASC',
       'orderby'=> $order_sort
       );
  
  } else { //if driver status passed, query cf "driver_status"
        //$driver_status = 'active';
        //loop arguments
       $args = array (
       'post_type' => 'driver',
       'posts_per_page' => $order_qty,
       'order'=> 'ASC',
       'orderby'=> $order_sort,
       'meta_key' => 'driver_current_status',
       'meta_value' => $driver_status
      );          
  }

 $posts = new WP_Query($args); 
 
  $DeliveryShare_output = '<div id="WooDriversList"><ul id="WooDriversListUL">';
 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $driverobjectID = $GLOBALS['post']->ID;
                     
            	$DeliveryShare_output .= '
              <li class="WDordersbydriverpool-driver-listing">
              <strong class="' . $DateClass . '"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></strong>                           
              </li>';
           
   endwhile;} else {
 return 'Sorry, no drivers found with your criteria.' ; // no posts found
}

$DeliveryShare_output .= '</ul></div>'; //close #DeliveryShareList and get ready for the orders loop
wp_reset_query();

  
  if(current_user_can( 'manage_options' ) || current_user_can( 'read_shop_order' ) || current_user_can('read_private_shop_orders')){ return html_entity_decode($DeliveryShare_output); } else { return 'Sorry, you don\'t have the necessary user role to access shop orders.'; }
   

} // close the big function
?>