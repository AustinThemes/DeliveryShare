<?php
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING DRIVER RATINGS FORM  */
add_shortcode("ds-ratingsform", "DeliveryShare_DriverRatingForm");

FUNCTION DeliveryShare_DriverRatingForm(){
//first, just make sure the user is logged in.
if ( !is_user_logged_in() ){ return '<div class="ds-message-error">You must be logged in to Rate an Order</div>'; die();}


// ----- get path to images folder including trailing slash
$WD_images_folder = 'http://www.speedygrocer.com/wp-content/plugins/deliveryshare2/images/';

// ----- get get rating thanks message
$RatingFormThanksText = 'Thanks for rating your Driver!';

// ----- GET QUERY PARAMS
$ds_param_order_id = sanitize_text_field( $_GET['id'] );
$ds_param_order_rating = sanitize_text_field( $_GET['rating'] );

// ----- get current rating for re-displaying
$ds_param_order_current_rating = get_post_meta($ds_param_order_id, 'order_driver_rating', true);

// ----- PROCESS QUERY PARAM : id
if ($ds_param_order_id && $ds_param_order_rating){
if(!$ds_param_order_current_rating){
update_post_meta( $ds_param_order_id, 'order_driver_rating', $ds_param_order_rating);
$ConditionalAlreadyRatedMessage = 'You have now rated Order #' . $ds_param_order_id . ' as ' . $ds_param_order_rating . ' out of 5 Stars. Thanks for Rating your Driver!.';
} else {
$ConditionalAlreadyRatedMessage = 'Order already rated. Rating not applied.';
}
?>
<div class="ds-message-normal">
  <?php echo $ConditionalAlreadyRatedMessage; ?>
</div>
<?php
}

?>
<script type="text/javascript">
function ChangeRatingStarsTo($StarsCount){
      var $wdImagePath = '<?php echo $WD_images_folder; ?>';
      var $wdImageOn = '<img src="' + $wdImagePath + 'ds-rating-star-on.png' + '">';
      var $wdImageOff = '<img src="' + $wdImagePath + 'ds-rating-star-off.png' + '">';
      var $PreparedValueForStarsContainer = '';
      if($StarsCount > 5){ $StarsCount = 5; };
      
      //use for loop to add stars HTML to $PreparedValueForStarsContainer
      for (i = 1; i <= 5; i++) {
          //if less or equal to $StarsCount, add an active star, else add an inactive star
          $wdImageOn = '<img onClick="SetRatingTo(' + i + ')" onMouseOver="ChangeRatingStarsTo(' + i + ')" onMouseOut="ResetStars()" src="' + $wdImagePath + 'ds-rating-star-on.png' + '">';
          $wdImageOff = '<img onClick="SetRatingTo(' + i + ')" onMouseOver="ChangeRatingStarsTo(' + i + ')" onMouseOut="ResetStars()" src="' + $wdImagePath + 'ds-rating-star-off.png' + '">';
          if(i <= $StarsCount){ $PreparedValueForStarsContainer += $wdImageOn; } else { $PreparedValueForStarsContainer += $wdImageOff; }
            } //end for loop
            //get #ds-stars-container by getElementById and write prepared string value
            document.getElementById('ds-stars-container').innerHTML = $PreparedValueForStarsContainer;
            } //end function

            function SetRatingTo($StarsCount){
            document.getElementById('WDrating').value = $StarsCount;
            } //end function

            function ResetStars(){
            $CurrentStarsCount = document.getElementById('WDrating').value;
            ChangeRatingStarsTo($CurrentStarsCount);
            document.getElementById('WDratingSubmitButton').value = 'Rate Driver ' + $CurrentStarsCount + ' Stars';
            } //end function

            function LoadRating(){
            var $StarCounts = document.getElementById('WDrating').value;
            var $WDDriverSelect = document.getElementById("WDdriverSelect");
            var $WDLoadRatingSelectedID = $WDDriverSelect.options[$WDDriverSelect.selectedIndex].value;
            parent.location = '?id=' + $WDLoadRatingSelectedID + '&rating=' + $StarCounts;
            }


          </script>
<div id="WD-RatingForm" class="WD-RatingForm woocommerce">
   
    <p class="form-row form-row-first">
    <label>Please select the Order</label><br/>
    
    <?php 
    //----------- start listing out orders
    $wdCurrentUserId = get_current_user_id();    
      
    $wdPostsFoundCount = 0;
    $args = array(
	   'post_type'   => 'shop_order',
     'post_status' => array( 'wc-complete' )
);  
    $posts = new WP_Query($args);    
    if ($posts->have_posts()){
      ?>
    <select id="WDdriverSelect"><?php
        while ($posts->have_posts()):
            $posts->the_post();
            $WDorderName = get_the_title();
            $WDorderID = get_the_ID(); 
            $WDorderOject = new WC_Order( $WDorderID );
            $WDuserID = $WDorderOject->user_id;
            $WDorderAssignedDriver = get_post_meta($WDorderID, 'order_assigned_driver', true);
            $WDorderDriverRating = get_post_meta($WDorderID, 'order_driver_rating', true);
                       
            if($wdCurrentUserId == $WDuserID){
            $wdPostsFoundCount += 1;
            ?>
      <option value="<?php echo $WDorderID; ?>"><?php echo '#' . $WDorderID . ' - Delivered by ' . $WDorderAssignedDriver; ?></option>
      <?php  }      
   endwhile;
   ?></select><?php
   } else { echo '<div class="ds-message-error"><strong style="color:red;">Sorry, no un-rated orders found.</strong></div>'; }
    ?>
    
     <?php 
     //--------------------------------
     if ($wdPostsFoundCount == 0){ echo '<div class="ds-message-error"><strong style="color:red;">Sorry, no un-rated orders found.</strong></div>'; return;};
     
     //-------------------------------
     
     ?>
  </p>

  <p class="form-row form-row-first">
    <label>Click a star to Rate this Order on a scale of 1 to 5 stars, with 5 stars being an excellent delivery!</label><br/>
    <div id="ds-stars-container">
      <img src="<?php echo $WD_images_folder; ?>ds-rating-star-on.png" onClick="SetRatingTo(1)" onMouseOver="ChangeRatingStarsTo(1)" /><img src="<?php echo $WD_images_folder; ?>ds-rating-star-on.png" onClick="SetRatingTo(2)" onMouseOver="ChangeRatingStarsTo(2)" /><img src="<?php echo $WD_images_folder; ?>ds-rating-star-on.png" onClick="SetRatingTo(3)" onMouseOver="ChangeRatingStarsTo(3)" /><img src="<?php echo $WD_images_folder; ?>ds-rating-star-on.png" onClick="SetRatingTo(4)" onMouseOver="ChangeRatingStarsTo(4)" /><img src="<?php echo $WD_images_folder; ?>ds-rating-star-on.png" onClick="SetRatingTo(5)" onMouseOver="ChangeRatingStarsTo(5)" />
    </div> <!-- //#ds-stars-container -->
    <input id="WDrating" type="hidden" value="5" class="input-text" onChange="ChangeRatingStarsTo(this.value)" />
     </p>          
       

            <p align="left">
                <input type="submit" class="button" id="WDratingSubmitButton" value="Rate Driver 5 Stars" onClick="LoadRating()" />
            </p>
 
</div>
<!-- //.WD-RatingForm woocommerce -->

<?php
}
?>