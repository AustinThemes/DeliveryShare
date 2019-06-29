<?php 
if ( ! defined( 'ABSPATH' ) ) die();


/* =================================== SHORTCODE FOR DISPLAYING DRIVER POOL */
add_shortcode("ds-driver-status-bar", "DeliveryShare_driverstatusbar_function");

function DeliveryShare_driverstatusbar_function(){
global $post;

// get current user id
// get current user name from id
$current_user = wp_get_current_user();
$current_user_username = $current_user->user_login;
$current_user_driver_status = '';

//get, validate and process driver status "timeclock" change
$ds_driverstatusbar_newdriverstatus = sanitize_text_field( $_GET['driverstatus'] );
$possible_driver_statuses_array = array('available','unavailable','active');



// loop through drivers, find driver with associated wordpress id of current user name
 $args = array (
       'post_type' => 'driver',
       'posts_per_page' => '-1',
       'meta_key' => 'driver_associated_wordpress_user_id',
       'meta_value' => $current_user_username
      );          
$posts = new WP_Query($args); 
 //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $driverobjectID = $GLOBALS['post']->ID;
            
            if($ds_driverstatusbar_newdriverstatus && in_array($ds_driverstatusbar_newdriverstatus, $possible_driver_statuses_array, true)){ update_post_meta( $driverobjectID, 'driver_current_status', $ds_driverstatusbar_newdriverstatus ); }
            
            $CurrentDriverStatus = get_post_meta( $driverobjectID, 'driver_current_status', true );
            if($CurrentDriverStatus){$current_user_driver_status = $CurrentDriverStatus;}
            
            
  endwhile;} else {
 $current_user_driver_status = 'No driver status found for username "' . $current_user_username . '"'; // no posts found
}
wp_reset_query();

$current_user_driver_status_button = '';
if($current_user_driver_status == 'available'){ $current_user_driver_status_button = '<a href="?driverstatus=unavailable" class="button">Change to Unavailable</a>'; }
else if($current_user_driver_status == 'unavailable'){ $current_user_driver_status_button = '<a href="?driverstatus=available" class="button">Change to Available</a>'; }

if(!$current_user_driver_status){ $current_user_driver_status = 'No driver status found for username "' . $current_user_username . '"'; } else { $current_user_driver_status = '<strong>Your Current Driver Status:</strong> ' . $current_user_driver_status; }
// if result exists, check CF for driver_current_status.

?>
<div class="ds-driver-status-bar">  <?php echo $current_user_driver_status . ' ' . $current_user_driver_status_button; ?>
  <div style="clear:both"></div>
</div>


<?php

} // close the big function
?>