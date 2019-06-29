<?php 
if ( ! defined( 'ABSPATH' ) ) die();


/* =================================== SHORTCODE FOR DISPLAYING DRIVER POOL */
add_shortcode("ds-driverpool", "DeliveryShare_driverpool_handler");
function DeliveryShare_driverpool_handler($incomingfrompost) {
//process incoming attributes assigning defaults if required
  $incomingfrompost=shortcode_atts(array(
    "qty" => DeliveryShare_QTY,
    "sortby" => 'date',
    "status" => $driver_status,
  ), $incomingfrompost);
  //run function that actually does the work of the plugin
  $DeliveryShare_output = DeliveryShare_driverpool_function($incomingfrompost);  
  //send back text to replace shortcode in post
  return $DeliveryShare_output;
}
function DeliveryShare_driverpool_function($incomingfromhandler) {
global $post;
$order_qty = sanitize_text_field( $_GET['qty'] );
$order_sort = sanitize_text_field( $_GET['sortby'] );
if(!$order_sort){$order_sort = 'date';}
if($order_qty == ''){$order_qty = 25;}

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
 
  $DeliveryShare_output = '<div id="WooDriverPool-UL"><ul>';
if($driver_status){ $DeliveryShare_output .= '<h2 class="WDdriverpool-h2">' . $driver_status . '</h2>';} else { $DeliveryShare_output .= '<h2 class="WDdriverpool-h2">All Drivers</h2>'; }

 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $driverobjectID = $GLOBALS['post']->ID;
                        
            //get driver cpt custom field data
            $CurrentDriverStatus = get_post_meta( $driverobjectID, 'driver_current_status', true );
            $CurrentDriverZipCodes = get_post_meta( $driverobjectID, 'driver_zip_codes_served', true );
            $CurrentDriverPreferredStores = get_post_meta( $driverobjectID, 'driver_preferred_stores', true );
            $CurrentDriverSupervisor = get_post_meta( $driverobjectID, 'driver_supervisor', true );
            $CurrentDriverPersonalAvailability = get_post_meta( $driverobjectID, 'driver_personal_availability', true );
            if($CurrentDriverSupervisor){$CurrentDriverSupervisorString = 'Supervisor : ' . $CurrentDriverSupervisor;} else { $CurrentDriverSupervisorString = 'Manager';}
            $CurrentDriverWPusername = get_post_meta( $driverobjectID, 'driver_associated_wordpress_user_id', true );
            
             //translate availability score into human readable form
             $PersonalAvailabilityStatusArray = array(
              "4" => "On Call / Rapid Response",
              "3" => "May be Available Same Day",
              "2" => "Reserve Driver",
              "1" => "Scheduled Slots Only",
              "0" => "No Availability",
            );
            $PersonalAvailabilityStatusValue = $PersonalAvailabilityStatusArray[$CurrentDriverPersonalAvailability];
            // --------------------  start rating average sub loop ----------------------

//loop arguments
$args2 = array (
       'post_type' => 'shop_order',
       'posts_per_page' => '-1',
       'post_status' => array('wc-processing','wc-complete','wc-completed'),
       'meta_key' => 'order_assigned_driver',
       'meta_value' => $CurrentDriverWPusername
);

 $posts2 = new WP_Query($args2); 
 $sg_ratings_loop_for_this_driver = '';
$sg_delivery_count_for_driver = 0;

 //start the list loop
    if ($posts2->have_posts()){
        while ($posts2->have_posts()):
            $posts2->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $orderobjectID = $GLOBALS['post']->ID;
            $sg_delivery_count_for_driver += 1;
            $orderRating = get_post_meta( $orderobjectID, 'order_driver_rating', true );
            if($orderRating){ $sg_ratings_loop_for_this_driver .= $orderRating . ','; }           
  	endwhile;
}
wp_reset_query();

//now that we're done counting, tally ratings
$subloop_order_rating_array = explode(",", $sg_ratings_loop_for_this_driver);
$subloop_order_rating_array_count = count($subloop_order_rating_array) - 1;
$subloop_order_rating_average = round( array_sum($subloop_order_rating_array) / $subloop_order_rating_array_count );

//calculate driver score

//order score factor 1 : rating. weight: 80%;
if ($subloop_order_rating_average){
$scorefactor1 = ( $subloop_order_rating_average / 5 ) * 90;
} else { $scorefactor1 = 80; }

//order score factor 2 : availability. weight : 10%;
$scorefactor2 = ( $CurrentDriverPersonalAvailability / 4 ) * 5;

//order score factor 3 : orders driven count. weight : 1% each up to 5%;
$scorefactor3 = $sg_delivery_count_for_driver;
if($scorefactor3 > 5){ $scorefactor3 = 5; }

//tally score factors
$combined_driver_score = $scorefactor1 + $scorefactor2 + $scorefactor3;

//now, write it back to a variable for listing
if(!$sg_ratings_loop_for_this_driver){ $subloop_order_rating_string = 'No ratings found'; } else {
$subloop_order_rating_string = '<img src="http://www.speedygrocer.com/images/ds-star-' . $subloop_order_rating_average . '.png"> <strong>' . $subloop_order_rating_average . '</strong>'; 
}


//------------------ end rating sub loop ----------------------
            
            


           
            	$DeliveryShare_output .= '
              <li class="WDdriverpool-driver-listing">
              <div class="WDdriverpool-imgrow"><strong class="' . $DateClass . '"><a href="' . get_the_permalink($driverobjectID) . '">' . get_the_post_thumbnail( $driverobjectID, 'thumbnail' ) . '</a></div>
              <div class="WDdriverpool-col1"><strong class="' . $DateClass . '"><a href="' . get_the_permalink($driverobjectID) . '">' . get_the_title($driverobjectID) . '</a></strong></div><div class="WDdriverpool-listing-info-container">
              <div class="WDdriverpool-col2">' . $CurrentDriverStatus . '</div>
              <div class="WDdriverpool-col3">' . $CurrentDriverZipCodes . '</div>
              <div class="WDdriverpool-col4">' . $CurrentDriverPreferredStores . '</div>
              <div class="WDdriverpool-col5">' . $CurrentDriverSupervisorString . '</div>
              <div class="WDdriverpool-col5">' . $PersonalAvailabilityStatusValue . '</div></div>
              <div class="WDdriverpool-listing-rating-container">' . $subloop_order_rating_string . '</div>              
              <div class="WDdriverpool-listing-rating-container">' . $sg_delivery_count_for_driver . ' Orders Driven</div>
              <div class="WDdriverpool-listing-score-container">Driver Score : ' . $combined_driver_score . ' / 100</div>
              <div class="WDdriverpool-col6">
              <input type="button" value="1st Choice" class="button">
              <a href="https://www.speedygrocer.com/tip-your-driver/?drivername=' . get_the_title($driverobjectID) . '"><img src="http://www.speedygrocer.com/images/sg-icon-tip.jpg"></a>
              <img src="http://www.speedygrocer.com/images/sg-icon-block.png">
              <img src="http://www.speedygrocer.com/images/sg-icon-report.png">
              </div>
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