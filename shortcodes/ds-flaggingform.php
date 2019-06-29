<?php
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING DRIVER RATINGS FORM  */
add_shortcode("ds-flaggingform", "DeliveryShare_FlaggingForm");

FUNCTION DeliveryShare_FlaggingForm(){
//first, just make sure the user is logged in.
if ( !is_user_logged_in() ){ return '<div class="ds-message-error">Sorry! You must be logged in to report an issue</div>'; die();}


// ----- get path to images folder including trailing slash
$WD_images_folder = 'http://www.speedygrocer.com/wp-content/plugins/deliveryshare2/images/';


// ----- GET QUERY PARAMS
$ds_abuse_report_order_id = sanitize_text_field( $_GET['orderid'] );
$ds_abuse_report_issue_id = sanitize_text_field( $_GET['issueid'] );
$ds_abuse_report_response_method = sanitize_text_field( $_GET['respondto'] );		

//Get Current Flags if order id passed
if($ds_abuse_report_order_id){
$ds_abuse_report_existing_flags = get_post_meta($ds_abuse_report_order_id, 'order_flags', true);
} else { $ds_abuse_report_existing_flags = ''; }

//Set default response method to email if not passed
if (!$ds_abuse_report_response_method){ $ds_abuse_report_response_method = 'email'; }

//Make Issue Human-Readable for confirmation message
$ds_abuse_issues_array = array(
    "0" => "Driver never delivered order",
    "1" => "Driver was late by less than 2 hrs",
    "2" => "Driver was late by more than 2 hrs",
    "3" => "Driver substituted without mention",
    "4" => "Driver did not get item without mention",
    "5" => "Driver brought less than specified quantity",
    "6" => "Driver brought unacceptable substitution",
    "7" => "Driver was Rude",
    "8" => "Driver made me uncomfortable",
    "9" => "Driver behaved in unprofessional manner",
    "10" => "Driver commited crime",
    "11" => "Driver could not find address",
    "12" => "Privacy Requested - Please contact me to resolve",
    "13" => "Other"
);

// ----- PREPARE EMAIL ALERT JUST IN CASE
$ds_admin_email = 'ryan@austinthemes.com';
$ds_notification_message = 'Order #' . $ds_abuse_report_order_id . ' on SpeedyGrocer.com has been flagged or reported for "' . $ds_abuse_issues_array[$ds_abuse_report_issue_id] . '"';
$ds_notification_subject_line = 'SPEEDYGROCER ORDER #' . $ds_abuse_report_order_id . ' FLAGGED';
$headers = 'From: SpeedyGrocer <info@speedygrocer.com>' . "\r\n";
function set_html_content_type() { 	return 'text/html'; }


// ----- TAKE ACTION THEN CONFIRM IF ENOUGH DETAILS PROVIDED
if ($ds_abuse_report_order_id && $ds_abuse_report_issue_id){
if(!$ds_abuse_report_existing_flags){
update_post_meta( $ds_abuse_report_order_id, 'order_flags', $ds_abuse_report_issue_id);
update_post_meta( $ds_abuse_report_order_id, 'order_flag_response_method', $ds_abuse_report_response_method);
add_filter( 'wp_mail_content_type', 'set_html_content_type' );
wp_mail( $ds_admin_email, $ds_notification_subject_line, $ds_notification_message, $headers );
wp_mail( $ds_customeremail, $ds_notification_subject_line, $ds_notification_message, $headers );
remove_filter( 'wp_mail_content_type', 'set_html_content_type' );


$ConditionalFlagConfirmation = 'You have now flagged Order #' . $ds_abuse_report_order_id . ' as having a problem. We will investigate any issues reported until the matter is resolved. <strong>Problem:</strong>' . $ds_abuse_issues_array[$ds_abuse_report_issue_id];
} else {
$ConditionalFlagConfirmation = 'Order already flagged. Please contact us to change any existing driver flags. Hint : Make sure you have selected the right order when selecting the driver.';
}
?>
<div class="ds-message-normal">
  <?php echo $ConditionalFlagConfirmation; ?>
</div>
<?php
}

?>
<script type="text/javascript">

  function FlagOrder(){
  //get flag data from form
  
  var $FlaggedOrderIDselect = document.getElementById('WDflaggingOrder');
  var $FlaggedOrderID = $FlaggedOrderIDselect.options[$FlaggedOrderIDselect.selectedIndex].value;

  var $FlaggedOrderIssueselect = document.getElementById('WDflaggingIssue');
  var $FlaggedOrderIssue = $FlaggedOrderIssueselect.options[$FlaggedOrderIssueselect.selectedIndex].value;

  var $FlaggedOrderRespondtoSelect = document.getElementById('WDflaggingRespondto');
  var $FlaggedOrderRespondto = $FlaggedOrderRespondtoSelect.options[$FlaggedOrderRespondtoSelect.selectedIndex].value;



  //prepare URL accordingly
  var $FlaggedOrderActionURL = 'http://www.speedygrocer.com/report-flag-problem-driver/?';
  if($FlaggedOrderID && $FlaggedOrderIssue && !$FlaggedOrderRespondto){
  $FlaggedOrderActionURL += 'orderid=' + $FlaggedOrderID + '&issueid=' + $FlaggedOrderIssue;
  parent.location = $FlaggedOrderActionURL; }
  else if($FlaggedOrderID && $FlaggedOrderIssue && $FlaggedOrderRespondto) {
  $FlaggedOrderActionURL += 'orderid=' + $FlaggedOrderID + '&issueid=' + $FlaggedOrderIssue + '&respondto=' + $FlaggedOrderRespondto;
  parent.location = $FlaggedOrderActionURL;
  } else { alert('Sorry! Not enough information has been filled in to flag an order. Please make sure the order is selected and that a reason for the complaint is selected.'); }
  }//end function


</script>
<div id="WD-RatingForm" class="WD-RatingForm woocommerce">
   
    <p class="form-row form-row-first">
    <label>SpeedyGrocer takes the satisfaction and safety of all of our customers seriously. Please select the order you've had an issue with, and the reason for the complaint, so that we can look into the issue further. Please allow time for a response, and we may need to contact you for additional information for some issues.</label><br/>
    
    <?php 
    //----------- start listing out orders
    $wdCurrentUserId = get_current_user_id();    
      
    $wdPostsFoundCount = 0;
    $args = array(
	   'post_type'   => 'shop_order',
     'post_status' => array( 'wc-complete' ),
    );  
    $posts = new WP_Query($args);    
    if ($posts->have_posts()){
      ?>
      <hr/>
      <br/>
      <label>Please select the order associated with this delivery</label>
      <br/>
    <select id="WDflaggingOrder">
              <?php
        while ($posts->have_posts()):
            $posts->the_post();
            $WDorderName = get_the_title();
            $WDorderID = get_the_ID(); 
            $WDorderOject = new WC_Order( $WDorderID );
            $WDuserID = $WDorderOject->user_id;
            $WDorderAssignedDriver = get_post_meta($WDorderID, 'order_assigned_driver', true);
            $WDorderDriverRating = get_post_meta($WDorderID, 'order_driver_rating', true);
            $WDorderExistingFlags = get_post_meta($WDorderID, 'order_flags', true);
            if($wdCurrentUserId == $WDuserID && !$WDorderExistingFlags){
            $wdPostsFoundCount += 1;
            ?>
      <option value="<?php echo $WDorderID; ?>"><?php echo '#' . $WDorderID . ' - Delivered by ' . $WDorderAssignedDriver; ?></option>
      <?php  }      
   endwhile;
   ?>
    </select><?php
   } else { echo '<div class="ds-message-error"><strong style="color:red;">Sorry, no un-rated orders found.</strong></div>'; }
    ?>
    
     <?php 
     //--------------------------------
     if ($wdPostsFoundCount == 0){ echo '<div class="ds-message-error"><strong style="color:red;">Sorry, no applicable orders found for your account.</strong></div>'; return;};
     
     //-------------------------------
     
     ?>
  </p>

  <p class="form-row form-row-first">
    <label>Select the issue or reason for your complaint</label><br/>

    <select id="WDflaggingIssue">
      <option value="">- Select Issue -</option>
      <?php
      
      foreach ($ds_abuse_issues_array as $key => $value) {
      echo '<option value="' . $key . '">' . $value . '</option>';
      } //end foreach
      
      ?>
      
    </select>    

     </p>


  <p class="form-row form-row-first">
    <label>What is the best way to get in touch with you for more details (as needed)?</label>
    <br/>

    <select id="WDflaggingRespondto">
      <option value="">- Select Issue -</option>
      <option value="1">By Email</option>
      <option value="2">By Phone</option>
      <option value="3">Please don't contact me about this issue</option>
    </select>

  </p>


  <p align="left">
                <input type="submit" class="button" id="WDratingSubmitButton" value="Report Driver Issue" onClick="FlagOrder()" />
            </p>
 
</div>
<!-- //.WD-RatingForm woocommerce -->

<?php
}
?>