<?php
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING ORDER CHECKLIST FEATURE FOR DRIVERS */

function UpdateOrderStatus($NSID,$NSPostStatus){
  wp_update_post(array(
    'ID'    =>  $NSID,
    'post_status'   =>  $NSPostStatus
  ));
}


add_shortcode("ds-orderchecklist", "DeliveryShare_ChecklistAPI");

function DeliveryShare_ChecklistAPI($incomingfromhandler) {

global $post;
global $woocommerce;
global $product;

// get query parameters for order id, customer name and phone, delivery address
$OrderID = sanitize_text_field( $_GET['order'] );
$ds_statusquery = sanitize_text_field( $_GET['status'] );
$ds_actualtotal = sanitize_text_field( $_GET['actual'] );
$ds_mode = sanitize_text_field( $_GET['mode'] );
$ds_confirmcancel = sanitize_text_field( $_GET['confirmcancel'] );
$ds_substituted_items = sanitize_text_field( $_GET['subbed'] );
$ds_refundable_items = sanitize_text_field( $_GET['refundable'] );
$ds_checklist_view_mode = sanitize_text_field( $_GET['viewmode'] );

$ds_this_id = 0; //reset non-unique ID for loop counter


//retrieve and reset other variables
$order = new WC_Order($OrderID); //get woocommerce order as object to work with
$product = new WC_Product($order_id);

//get user info by ID
$user_id = $order->user_id;

//get some woocommerce order info
$ds_customeremail = get_the_author_meta('user_email',$user_id);
$ds_customerphone = get_post_meta( $OrderID, 'billing_phone', true);

//check for training mode and pre-build a simple notice if found
if( $ds_statusquery == 'demo' || $ds_mode == 'demo' ){
$ds_training_header == '<div class="ds-training-notice">DeliveryShare Training Mode - No changes will be saved</div>';
} else {
$ds_training_header == '';
}
echo 

//get speedygrocer-specific custom fields (for each, check if the old woocommerce CF is filled or if it's a post-WC2 order
//note : the duplicates with differing meta field names are to accomodate older versions of DeliveryShare

$ds_driver = get_post_meta( $OrderID, 'Do you have a preferred driver?', true);
$ds_driver_WC2 = get_post_meta( $OrderID, 'Do you have a preferred driver?', true);
if(!$ds_driver && $ds_driver_WC2){$ds_driver = $ds_driver_WC2;}

$ds_deliverytime = get_post_meta( $OrderID, 'When would you like your groceries delivered?', true);
$ds_deliverytime_WC2 = get_post_meta( $OrderID, 'Whenwouldyoulikeyourgroceriesdelivered', true);
if(!$ds_deliverytime && $ds_deliverytime_WC2){$ds_deliverytime = $ds_deliverytime_WC2;}

$ds_deliverydate = get_post_meta( $OrderID, 'What day would you like this delivery made on?', true);
$ds_deliverydate_WC2 = get_post_meta( $OrderID, 'Whatdaywouldyoulikethisdeliverymadeon', true);
if(!$ds_deliverydate && $ds_deliverydate_WC2){$ds_deliverydate = $ds_deliverydate_WC2;}

$ds_selectedstore = get_post_meta( $OrderID, 'What store(s) would you like us to shop at?', true);
$ds_selectedstore_WC2 = get_post_meta( $OrderID, 'Whatstoreswouldyoulikeustoshopat', true);
if(!$ds_selectedstore && $ds_selectedstore_WC2){$ds_selectedstore = $ds_selectedstore_WC2;}

$ds_substitution = get_post_meta( $OrderID, 'If an item is out of stock, would you like us to substitute?', true);
$ds_substitution_WC2 = get_post_meta( $OrderID, 'Ifanitemisoutofstockwouldyoulikeustosubstitute', true);
if(!$ds_substitution && $ds_substitution_WC2){$ds_substitution = $ds_substitution_WC2;}


$ds_edit_order_link = '<a href="' . get_edit_post_link( $OrderID ) . '" class="WD-order-edit-link button">Edit Order</a>';

//if view mode passed as query param, create main container class
if($ds_checklist_view_mode){
$ds_checklist_container_css_class = ' ds-checklist-mode-' . $ds_checklist_view_mode;
} else {
$ds_checklist_container_css_class = '';
}


//get order status from woocommerce
$ds_current_order_status = get_post_status($OrderID);

// ------------- email notifications system for order status changes triggered by checklist actions

// ===== VARIABLES
$ds_admin_email = 'ryan@austinthemes.com';
$ds_notification_message = '';
$ds_notification_subject_line = '';

// ===== create function for setting html content type, to be ref'd in the email compilation functions
function set_html_content_type() { 	return 'text/html'; }

// ===== function for Email Status Change Notifications
   
//prepare subject line and message body for each status update
		if ( $ds_statusquery == '' ){ }
		else if ( $ds_statusquery == 'shopping' && $ds_mode != 'demo' && $ds_current_order_status != 'wc-completed'){ 
        $ds_notification_message = 'Your SpeedyGrocer Driver for Order ' . $OrderID . ' is now Shopping for your items.';
        $ds_notification_subject_line = 'We\'re now shopping for your order!';
        }
		else if ( $ds_statusquery == 'checkedout' && $ds_mode != 'demo' ){ 
        $ds_notification_message = 'Your SpeedyGrocer Driver is Done Shopping for order #' . $OrderID .'.';
        $ds_notification_subject_line = 'Order ' . $OrderID . ' out for delivery';
        }
		else if ( $ds_statusquery == 'Need To Refund' && $ds_mode != 'demo' ){ 
        $ds_notification_message = 'Your SpeedyGrocer Order ' . $OrderID . ' has been marked as needing a refund. Your order will be reviewed and refunded if approved by one of our managers.';
        $ds_notification_subject_line = 'Order now pending refund approval';
        }
		else if ( $ds_statusquery == 'Order Refused' && $ds_mode != 'demo' ){ 
        $ds_notification_message = 'Your SpeedyGrocer Order ' . $OrderID . ' could not be delivered, and has been marked as Refused. Please contact us for further action.';
        $ds_notification_subject_line = 'Order could not be delivered';
        }
		else if ( $ds_statusquery == 'Partial Refund Due' && $ds_mode != 'demo' ){ 
        $ds_notification_message = 'Your SpeedyGrocer Order ' . $OrderID . ' has been marked as needing a partial refund. Please await further action from our staff, and we\'re sorry for the inconvenience!';
        $ds_notification_subject_line = 'Order now pending partial refund';
        }
		else {}
	
//set html content type using filter
		add_filter( 'wp_mail_content_type', 'set_html_content_type' );
    
//+ prepare headers
$headers = 'From: SpeedyGrocer <info@speedygrocer.com>' . "\r\n";

//+ if all variables in place, send email using wordpress wp_mail function 
		if($ds_notification_message && $ds_current_order_status != 'wc-completed' && $ds_statusquery != ''){ 
			wp_mail( $ds_admin_email, $ds_notification_subject_line, $ds_notification_message, $headers );
      wp_mail( $ds_customeremail, $ds_notification_subject_line, $ds_notification_message, $headers );
		}

		// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
		remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

// ===== continue checklist work

//generate back button 
$ds_return_button_label = 'Back to Incoming Orders';
$ds_return_button_URL = get_option('ds_path_incomingorders');
$ds_return_button_string = '<div class="ds-return-button-row"><a href="' . $ds_return_button_URL . '" class="ds-button button">' . $ds_return_button_label . '</a></div>';

//if mode = demo, create query string addition for all generated links to maintain demo mode
if ($ds_mode == 'demo'){ $ds_mode_query = '&mode=demo'; };

//get assigned driver and current user for driver security and anti-cheating checks
$ds_order_assigneddriver = get_post_meta( $OrderID, 'order_assigned_driver', true);
$current_user_object = wp_get_current_user();
$ds_order_currentuser = $current_user_object->user_login;

//if the status query parameter is shopping and the assigned driver cf is blank, set assigned driver cf to current user id
if ($ds_order_assigneddriver == '' && $ds_mode != 'demo' && $ds_current_order_status != 'wc-completed'){
update_post_meta( $OrderID, 'order_assigned_driver', $ds_order_currentuser);
$ds_order_assigneddriver = $ds_order_currentuser;
}

//Check if assigned driver still isn't current user, and return out of function to prevent cheating if so. Driver security check 1
if ($ds_order_assigneddriver != $ds_order_currentuser && !current_user_can('manage_options')){ return '<div class="ds-message-error">Sorry - You must be the assigned driver to view or deliver this order.</div>'; };

//get shipping address from woocommerce
$ds_deliveryaddress = $order->get_formatted_shipping_address();

//set default action button text
if ($ds_statusquery == 'checkedout'){$ds_DefaultButtonText = 'Delivery Complete'; } else { $ds_DefaultButtonText = 'Not Ready to Submit - Please find all items';}

//errors
$ds_OvercheckedErrorButtonText = 'Not Ready to Submit - Item checked more than once';
$ds_SubmitButtonOnclickAddress = '#';





// -------------------------------------------------------
//if $ds_statusquery = 'demo' then just display a message about being for info only
if ($ds_mode == 'demo'){
?>
<div class="ds-message-normal ds-message-training">You are viewing this checklist in training mode. No changes will be made to the order. </div>
<?php }









// =========== Start Status System

// -------------------------------------------------------
//if $ds_statusquery = 'shopping' then 1)update status custom field, 2)change button to "Order Now Delivered" for the next status update
if ($ds_statusquery == 'shopping' && $ds_current_order_status != 'wc-completed'){
if ($ds_mode != 'demo'){
UpdateOrderStatus($OrderID,'ds_now_shopping');
}
$ds_SubmitButtonOnclickAddress = '?order=' . $OrderID . '&status=checkedout' . $ds_mode_query;
?>
<div class="ds-message-normal">Step 1/3 - You are now starting the delivery for order #<?php echo $OrderID; ?>. Please drive to the nearest <strong><?php echo $ds_selectedstore; ?></strong> store and start shopping for the items below.</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'resumedelivery' then 1)update status custom field, 2)change button to "Order Now Delivered" for the next status update
else if ($ds_statusquery == 'resumedelivery' && $ds_current_order_status != 'wc-completed'){
$ds_SubmitButtonOnclickAddress = '?order=' . $OrderID . '&status=checkedout' . $ds_mode_query;
?>
<div class="ds-message-normal">
  Step 1/3 - You are now resuming the delivery for order #<?php echo $OrderID; ?>. Please drive to the nearest <strong>
    <?php echo $ds_selectedstore; ?>
  </strong> store and start shopping for the items below.
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'checkedout' then 1)update status custom field, 2)prompt actual total field, 3) change submit button function to submit updated actual total
// 4) set 'order_items_substituted' and 'order_items_to_be_refunded' custom fields for orderID to store substituted items and items to refund if query params passed
else if ($ds_statusquery == 'checkedout'){
$ds_SubmitButtonOnclickAddress = '?order=' . $OrderID . '&status=delivered' . $ds_mode_query;
}
else if ($ds_statusquery == 'checkedout' && $ds_current_order_status != 'wc-completed'){
UpdateOrderStatus($OrderID,'ds_done_shopping');
if($ds_substituted_items && $ds_mode != 'demo'){update_post_meta($OrderID, 'order_items_substituted', $ds_substituted_items);};
if($ds_refundable_items && $ds_mode != 'demo'){update_post_meta($OrderID, 'order_items_to_be_refunded', $ds_refundable_items);};
if($ds_actualtotal && $ds_mode != 'demo'){update_post_meta($OrderID, 'actual_cost', $ds_actualtotal);};
$ds_SubmitButtonOnclickAddress = '?order=' . $OrderID . '&status=delivered' . $ds_mode_query;
//get unformatted address for google maps embed used for checkedout status
$ds_deliveryaddress_map_street1 = $order->shipping_address_1;
$ds_deliveryaddress_map_street2 = $order->shipping_address_2;
$ds_deliveryaddress_map_city = $order->shipping_city;
$ds_deliveryaddress_map_state = $order->shipping_state;
$ds_deliveryaddress_map_zip = $order->shipping_postcode;
$ds_deliveryaddress_map_address_string = $ds_deliveryaddress_map_street1 . ',' . $ds_deliveryaddress_map_street2 . ',' . $ds_deliveryaddress_map_city . ',' . $ds_deliveryaddress_map_state . ' ' . $ds_deliveryaddress_map_zip;

//create google map based on the delivery address.
$GoogleAPIkey = get_option('ds_google_api_key'); // Google GeoCoding API key
$GoogleMapsIframeHTML = '<iframe width="100%" height="480" frameborder="0" class="WD-google-map-iframe" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=' . $GoogleAPIkey . '&q=' . $ds_deliveryaddress_map_address_string .'"></iframe>';


?>
<div class="ds-message-normal">Step 2/3 - Ready to Go! Actual total logged at $<?php echo $ds_actualtotal; ?>. Please drive to the customer's address and deliver the order. Remember to Drive Safe!</div>
<div class="ds-google-map-checklist-row"><?php echo $GoogleMapsIframeHTML; ?></div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'delivered' then 1)update status custom field, 2)prompt actual total field, 3) change submit button function to submit updated actual total
else if ($ds_statusquery == 'delivered'){
if ($ds_mode != 'demo' && $ds_current_order_status != 'wc-completed'){ 
UpdateOrderStatus($OrderID,'wc-completed');
};
?>
<div class="ds-message-normal">Step 3/3 - This Grocery Delivery is now complete! Please check for the next available order, or proceed as directed otherwise. <?php echo $ds_return_button_string; ?></div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'unassign' but confirmcancl  ""
else if ($ds_statusquery == 'unassign' && $ds_confirmcancel != "confirmed" && $ds_current_order_status != 'wc-completed'){
$ds_ConfirmReleaseButtonOnclickAddress = '?order=' . $OrderID . '&status=unassign&confirmcancel=confirmed' . $ds_mode_query;
$ds_ResumeDeliveryButtonOnclickAddress = '?order=' . $OrderID . '&status=resumedelivery' . $ds_mode_query;

?>
<div class="ds-message-normal">
  Are you sure you want to unassign yourself from this order?<br/><a href="<?php echo $ds_ConfirmReleaseButtonOnclickAddress; ?>">Yes, Release the order for other drivers</a><br/>
  <a href="<?php echo $ds_ResumeDeliveryButtonOnclickAddress; ?>">No, Return to Order</a>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'unassign' and the cancel confirmation check is true,then change the order status to cancelled and add all passed refunded item IDs to a custom field for refunding
else if ($ds_statusquery == 'unassign' && $ds_confirmcancel == "confirmed" && $ds_current_order_status != 'wc-completed'){
if ($ds_mode != 'demo'){ 
UpdateOrderStatus($OrderID,'wc-processing');
update_post_meta( $OrderID, 'order_assigned_driver', '');
}
?>
<div class="ds-message-error">
  Order status set back to processing. The order has been unassigned and released for other drivers to claim.<?php echo $ds_return_button_string; ?>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'refund' then just display a message about being for info only
else if ($ds_statusquery == 'refund' && $ds_confirmcancel != "confirmed" && $ds_current_order_status != 'wc-completed'){
$ds_ConfirmCancelAndRefundButtonOnclickAddress = '?order=' . $OrderID . '&status=unassign&confirmcancel=confirmed' . $ds_mode_query;
$ds_ResumeDeliveryButtonOnclickAddress = '?order=' . $OrderID . '&status=resumedelivery' . $ds_mode_query;
?>
<div class="ds-message-normal">Are you sure you want to cancel and refund this order?<br/><a href="<?php echo $ds_ConfirmCancelAndRefundButtonOnclickAddress; ?>">Yes, Cancel and Refund</a><br/>
  <a href="<?php echo $ds_ResumeDeliveryButtonOnclickAddress; ?>">No, Return to Order</a>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'refund' and the cancel confirmation check is true,then change the order status to cancelled and add all items to a custom field for refunding
else if ($ds_statusquery == 'refund' && $ds_confirmcancel == "confirmed" && $ds_current_order_status != 'wc-completed'){
if ($ds_mode != 'demo'){ 
UpdateOrderStatus($OrderID,'ds_need_to_refund');
};
?>
<div class="ds-message-error">
  Order Cancelled and order added to Refund Queue.<?php echo $ds_return_button_string; ?>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'refused' then just display a message about being for info only
else if ($ds_statusquery == 'refused' && $ds_confirmcancel != "confirmed" && $ds_current_order_status != 'wc-completed'){
$ds_ConfirmRefusedOrderButtonOnclickAddress = '?order=' . $OrderID . '&status=refused&confirmcancel=confirmed' . $ds_mode_query;
$ds_ResumeDeliveryButtonOnclickAddress = '?order=' . $OrderID . '&status=resumedelivery' . $ds_mode_query;
?>
<div class="ds-message-normal">Do you confirm that this order was refused by the customer?<br/>
<a href="<?php echo $ds_ConfirmRefusedOrderButtonOnclickAddress; ?>">Yes, The order was refused</a><br/>
<a href="<?php echo $ds_ResumeDeliveryButtonOnclickAddress; ?>">No, Return to Order</a>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'refused' and the cancel confirmation check is true,then change the order status to cancelled and add all items to a custom field for refunding
else if ($ds_statusquery == 'refused' && $ds_confirmcancel == 'confirmed' && $ds_current_order_status != 'wc-completed'){
if ($ds_mode != 'demo'){ 
UpdateOrderStatus($OrderID,'ds_order_refused');
};
?>
<div class="ds-message-error">
  Order Marked as Refused. Please return to the store that the order was purchased from, and return as much of the order as allowed by the store return policy.<?php echo $ds_return_button_string; ?>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = '' then just display a message about being for info only
else if ($ds_statusquery == '' && $ds_current_order_status != 'wc-completed'){
?>
<div class="ds-message-normal">You are not actively delivering or working with this order. The order information below is for reference only. </div>
<?php }



// -------------------------------------------------------
//if $ds_statusquery = 'wc-completed' and the cancel confirmation check is true,then change the order status to cancelled and add all items to a custom field for refunding
else if ($ds_current_order_status == 'wc-completed'){
?>
<div class="ds-message-error">
  Order already completed. No changes will be saved. If any order details need to be changed, please contact an administrator or manager to change the order data manually.<?php echo $ds_return_button_string; ?>
</div>
<?php }

// -------------------------------------------------------
//if $ds_statusquery = 'demo' or $ds_mode = 'demo', display a training banner
else if ($ds_mode == 'demo' || $ds_statusquery == 'demo'){
?>
<div class="ds-training-mode">
Training Mode
</div>
<?php }


// -------------------------------------------------------
//display error if no status or invalid status query passed.
else {
?>
<div class="ds-message-error">ERROR : Invalid status in URL (?status=examplehere). <?php echo $ds_return_button_string; ?></div>
<?php }




//CHECK FOR AND ADD CUSTOMER NOTES
if($ds_statusquery == 'shopping' || $ds_statusquery == 'checkedout'){
$ds_ordernotes = get_post_meta( $OrderID, 'Do you have any directions for our driver?', true);
if($ds_ordernotes){ ?>
<div class="ds-message-normal">
  <strong>Customer Delivery Notes:</strong>
  <?php echo $ds_ordernotes; ?>
</div>
<?php }}



// =========== Start UI and Products Table




// add receipt container and table header
$DeliveryShare_orderchecklisttable = '<div class="DeliveryShareOrderWrapper woocommerce shop_table cart' . $ds_checklist_container_css_class . '">
<div id="DeliveryShareOrderHeader">
<div id="ds-checklist-section-details" class="ds-checklist-section ds-checklist-section-details col-md-12">
<h2 class="ds-sectiontitle">Step 1 : Review Order Information</h2>
<div class="col-md-1"><strong>#' . $OrderID . '</strong></div>
<div class="col-md-3">' . $ds_selectedstore . '</div>
<div class="col-md-2">' . $ds_deliverydate . '</div>
<div class="col-md-2">' . $ds_deliverytime . '</div>
<div class="col-md-4">' . $ds_edit_order_link . '</div>
</div>

<div id="ds-checklist-section-details2" class="ds-checklist-section ds-checklist-section-details2 col-md-12">
<h2 class="ds-sectiontitle">Step 2 : Review Customer Details</h2>
<div class="col-md-6">' . $ds_deliveryaddress . '<br/>' . $ds_customerphone . '</div>
<div class="col-md-6">
<strong>Selected Store:</strong> ' . $ds_selectedstore . '<br/>
<strong>Preferred Driver:</strong> ' . $ds_driver . '<br/>
<strong>Assigned Driver:</strong>' . $ds_order_assigneddriver . '<br/>
<strong>Current User:</strong>' . $ds_order_currentuser . '<br/>
<strong>Substitute if items not found?:</strong>' . $ds_substitution . '
</div></div>
<div style="clear:both;"></div>
</div>
<div id="DeliveryShareOrderList"><h2 class="ds-sectiontitle">Step 3 : Shop for Items</h2>'; 

// big picture: loop through the order's products and add a row with aisle, item, and checkbox


//get the order items as an array
$ds_order_item_array = $order->get_items();

//sort order items array alphabetically to group by aisle

 uasort( $ds_order_item_array, 
          function( $a, $b ) {
            
            $product_cats_A = wp_get_post_terms( $a['product_id'], 'product_cat' );
            foreach($product_cats_A as $product_cat_A) {
                if ($product_cat_A->parent == 0){
                $ds_this_categories_A .= $product_cat_A->name . ',';
                }
            }
            
             $product_cats_B = wp_get_post_terms( $b['product_id'], 'product_cat' );
            foreach($product_cats_B as $product_cat_B) {
                if ($product_cat_B->parent == 0){
                  $ds_this_categories_B .= $product_cat_B->name . ',';
                }
            }
            
            return strnatcmp( $ds_this_categories_A, $ds_this_categories_B ); 
          }
        );

// Getting names of items and quantity throught foreach
foreach($ds_order_item_array as $item) 
{
$product = new WC_Product($OrderID);
$ds_this_id += 1;
$ds_this_name = $item['name']; /* Item */
$ds_this_size = $item['product_size'];
$ds_order_item_product_id = $item['product_id'];
$ds_this_qty = $item['qty']; /* Quantity */
$ds_this_custom_product = $item['CustomItemName']; /* Custom Item Name */
  
  
  if($ds_this_custom_product){ $ds_this_name = '<strong>Custom Item : </strong>' . $ds_this_custom_product;}

//show checkboxes if status = shopping
if ($ds_statusquery == 'shopping' || $ds_statusquery == 'demo' || $ds_statusquery == 'resumedelivery'){
$ds_this_found = '<a href="javascript:CheckTheBox(\'ds-checkboxA-' . $ds_this_id .'\')" class="btn btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> Found</a><input type="radio" onClick="wdAnalyzeCheckboxes();" class="ds-radiobutton" name="ds-RadioButtonForID' . $ds_this_id .'" id="ds-checkboxA-' . $ds_this_id .'" name="' . $ds_this_name . '" value="' . $ds_this_name . '">'; /* Found */
$ds_this_substituted = '<a href="javascript:CheckTheBox(\'ds-checkboxB-' . $ds_this_id .'\')" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-transfer"></span> Sub</a><input type="radio" onClick="wdAnalyzeCheckboxes();" class="ds-radiobutton" name="ds-RadioButtonForID' . $ds_this_id .'" id="ds-checkboxB-' . $ds_this_id .'" name="' . $ds_this_name . '" value="' . $ds_this_name . '">'; /* Substituted */
$ds_this_refunded = '
<a href="javascript:CheckTheBox(\'ds-checkboxC-' . $ds_this_id . '\')" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-remove-sign"></span> Ref</a>
<a href="javascript:ToggleImageForItem(' . $ds_this_id . ')" class="btn btn-danger btn-lg"><span class="glyphicon glyphicon-picture"></span></a>
<input type="radio" onClick="wdAnalyzeCheckboxes();" class="ds-radiobutton" name="ds-RadioButtonForID' . $ds_this_id .'" id="ds-checkboxC-' . $ds_this_id .'" name="' . $ds_this_name . '" value="' . $ds_this_name . '">'; /* Not Found - Refund Item */
} else {
$ds_this_found = '';
$ds_this_substituted = '';
$ds_this_refunded = '';
}

// ------------------------------------- get categories -------------------------------

//clear then get product categories and accumulate in $ds_this_categories
$ds_last_categories = $ds_this_categories;
$ds_this_categories = '';
$product_cats = wp_get_post_terms( $item['product_id'], 'product_cat' );
            foreach($product_cats as $product_cat) {
              if ($product_cat->parent == 0){
                $ds_this_categories .= $product_cat->name . ',';
                }
            }
$ds_this_categories = rtrim($ds_this_categories,',');
$ds_this_categories_array = explode(",", $ds_this_categories);            
$ds_first_category_string = $ds_this_categories_array[0];            

// --------------------- end categories ------------------------------


// prepare thumbnail src for use in all display modes
$WDproductID = $item['product_id'];
$checklistitemthumbnailsrc = wp_get_attachment_image_src( get_post_thumbnail_id($WDproductID), 'thumbnail' );
$checklistitemfullsrc = wp_get_attachment_image_src( get_post_thumbnail_id($WDproductID), 'full' );
$checklistitemthumbnailurl = $checklistitemthumbnailsrc[0];
$checklistitemfullurl = $checklistitemfullsrc[0];
$checklistitemproductlinkstart = '<a class="WDchecklistorderitemtitlelink" target="newproductsingleview' . $WDproductID . '" href="'.get_permalink($WDproductID).'">';
$checklistitemproductlinkend = '</a>';

//if this categories = last categories, hide row header. otherwise, display row header 
          
//outut the order info
if ($ds_mode == 'image'){
$WDImageURLButton1 = '<a href="#" class="button"><img src="' . plugins_url( '../images/button-checkmark.jpg', __FILE__ ) . '"></a>';
$WDImageURLButton2 = '<a href="#" class="button"><img src="' . plugins_url( '../images/button-hand.jpg', __FILE__ ) . '"></a>';
$WDImageURLButton3 = '<a href="#" class="button"><img src="' . plugins_url( '../images/button-cancel.jpg', __FILE__ ) . '"></a>';
$DeliveryShare_orderchecklisttable .= '<div class="wdimagecellbox" id="wdimagechecklistitem' . $ds_this_id . '"><div class="wdimagecellproducttitle">' . $ds_this_name . '</div><img src="' . $checklistitemthumbnailurl . '" id="ds-checklist-image-item' . $ds_this_id . '" class="WDimagechecklistthumbnail"><div class="wdimagecellboxmenu">' . $WDImageURLButton1 . $WDImageURLButton2 . $WDImageURLButton3 . '</div></div>'; 
} else {
if ($ds_this_categories != $ds_last_categories) { $DeliveryShare_orderchecklisttable .= '<div class="WDchecklistcategoriestitle col-md-12"><span class="glyphicon glyphicon-chevron-right"></span> ' . $ds_first_category_string . '</div><div style="clear:both;"></div>';}
$DeliveryShare_orderchecklisttable .= '<div class="row form-row form-row-wide col-md-12" id="wdchecklistrowouter' . $ds_this_id . '">
<div class="col1 col-md-12 ds-checklist-imagerow wdchecklistrow' . $ds_this_id . '">
<a href="' . $checklistitemfullurl . '" target="lightbox" rel="lightbox" itemprop="image" class="woocommerce-main-image zoom" data-rel="prettyPhoto">
<img src="' . $checklistitemthumbnailurl . '" border="0" id="WDimagechecklistthumbnail' . $ds_this_id . '" class="WDimagechecklistthumbnail"></a>
</div><div class="col2 col-md-12 ds-checklist-titlerow wdchecklistrow' . $ds_this_id . '">' . $checklistitemproductlinkstart . $ds_this_name . $checklistitemproductlinkend . ' (qty: ' . $ds_this_qty . ')</div>
<div class="ds-checklistcontrols col-md-12">
<div id="ds-checkboxA-outer-' . $ds_this_id . '" class="col-md-6 ds-checklist-fndrow text-left wdchecklistrow' . $ds_this_id . '">' . $ds_this_found . '</div>
<div id="ds-checkboxB-outer-' . $ds_this_id . '" class="col-md-6 ds-checklist-refsubrow text-right wdchecklistrow' . $ds_this_id . '">' . $ds_this_substituted . $ds_this_refunded . '</div>
</div><div style="clear:both;"></div>
<input type="hidden" id="WDhiddenproductIDforItem-' . $ds_this_id . '" value="' . $ds_order_item_product_id . '" /></div>';
}}

//start button container
$WDchecklistbuttoncontainer_start = '<div class="ds-orderchecklist-submitbutton-container"><div id="wdErrorMessage"></div>';
$WDchecklistbuttoncontainer_end = '</div>';

//prepare a string of order checklist menu buttons conditionally
$ds_checklist_contextbuttons = '';

$ds_RefundButtonOnclickAddress = '?order=' . $OrderID . '&status=refund' . $ds_mode_query;
$ds_ReleaseButtonOnclickAddress = '?order=' . $OrderID . '&status=unassign' . $ds_mode_query;
$ds_RefusedButtonOnclickAddress = '?order=' . $OrderID . '&status=refused' . $ds_mode_query;

$ds_checklist_contextbuttons .= '<div class="ds-checklist-footer-buttons-wrap">';
$ds_checklist_contextbuttons .= '<input type="button" class="button" id="ds-orderchecklist-submitbutton" value="' . $ds_DefaultButtonText . '" onMouseOver="wdAnalyzeCheckboxes()" onClick="loadURLwithActual()" />';
$ds_checklist_contextbuttons .= '<input type="button" class="button" id="ds-orderchecklist-cancelbutton" value="Cancel and Refund" onClick="parent.location = \'' . $ds_RefundButtonOnclickAddress . '\';" />';
$ds_checklist_contextbuttons .= '<input type="button" class="button" id="ds-orderchecklist-releasebutton" value="Release Order" onClick="parent.location = \'' . $ds_ReleaseButtonOnclickAddress . '\';" />';
$ds_checklist_contextbuttons .= '<input type="button" class="button" id="ds-orderchecklist-refusedbutton" value="Order Refused" onClick="parent.location = \'' . $ds_RefusedButtonOnclickAddress . '\';" />';
$ds_checklist_contextbuttons .= '<input type="button" class="button" id="ds-orderchecklist-checkallbutton" value="Check All" onClick="CheckAllFound()" />';
$ds_checklist_contextbuttons .= '</div>'; // end buttons wrap

$ds_checklist_contextbuttons_checkout .= '<div class="ds-checklist-footer-buttons-wrap">';
$ds_checklist_contextbuttons_checkout .= '<input type="button" class="button" id="ds-orderchecklist-submitbutton" value="Order Delivered" onClick="parent.location = \'' . $ds_SubmitButtonOnclickAddress . '\';" />';
$ds_checklist_contextbuttons_checkout .= '<input type="button" class="button" id="ds-orderchecklist-refusedbutton" value="Customer Refused Order" onClick="parent.location = \'' . $ds_RefusedButtonOnclickAddress . '\';" />';
$ds_checklist_contextbuttons_checkout .= '</div>'; // end buttons wrap


//CheckAllFound()

$DeliveryShare_orderchecklisttable .= '<h2 class="ds-sectiontitle">Step 4 : Checkout</h2>

<div id="WDitemsfoundbar" class="col-md-12">
<div class="col-md-6" class="WDitemsfoundCountLabel">Items Found:</div>
<div class="col-md-6" id="WDitemsfoundCountReadout">0</div>
<div class="col-md-12" id="WDitemCounterBarWrapper">
  <div class="col-md-12" id="WDitemCounterBarOuter">
    <div id="WDitemCounterBarInner">TEST</div>
  </div>
</div>
</div>


<div id="WDsubandrefundresultsbar" class="col-md-12">Substitutions and Refunds Here</div>
<input type="hidden" id="WDallitemstorefund" value="" /><input type="hidden" id="WDallitemssubstituted" value="" />';

// add submit button
if ($ds_statusquery == 'shopping' || $ds_statusquery == 'demo' || $ds_statusquery == 'resumedelivery'){
$DeliveryShare_orderchecklisttable .= $WDchecklistbuttoncontainer_start . '<div id="ds-actualtotalcontainer"><strong>Actual Total :</strong> $<input type="text" class="ds-actual-total-input" id="ds-actual-total-input" onClick="wdAnalyzeCheckboxes()" onChange="wdAnalyzeCheckboxes()" value="" /></div>' . $ds_checklist_contextbuttons . $WDchecklistbuttoncontainer_end;
} else if ($ds_statusquery != 'delivered' && $ds_statusquery != 'checkedout' && $ds_statusquery != ''){
$DeliveryShare_orderchecklisttable .= $WDchecklistbuttoncontainer_start . '<div class="ds-orderchecklist-submitbutton-container"><div id="wdErrorMessage"></div><input type="button" id="ds-orderchecklist-submitbutton" value="' . $ds_DefaultButtonText . '" onClick="parent.location = \'' . $ds_SubmitButtonOnclickAddress . '\';" />' . $ds_checklist_contextbuttons . $WDchecklistbuttoncontainer_end;
} else if ($ds_statusquery == 'checkedout'){
$DeliveryShare_orderchecklisttable .= $WDchecklistbuttoncontainer_start . '<div class="ds-orderchecklist-submitbutton-container"><div id="wdErrorMessage"></div>' . $ds_checklist_contextbuttons_checkout . $WDchecklistbuttoncontainer_end;
}
//end button container

// add table footer and close main wrapper
$DeliveryShare_orderchecklisttable .= '</div><div style="clear:both;"></div></div>'; 

if ($ds_statusquery == 'shopping' || $ds_statusquery == 'demo' || $ds_statusquery == 'resumedelivery' || $ds_statusquery == 'checkedout'){

// ================ add javascript for calculating order completion on the fly then unlocking the submit button once all items checked ?>

<script type="text/javascript">
    //initial order variables for calculation
    var $WDDifferentItems = <?php echo $ds_this_id; ?>; //qty of items in order, based on PHP loop increment counter
    var $WDCheckboxA; var $WDCheckboxB; var $ErrorMessage;
    
    //disable button until conditions met
    document.getElementById("ds-orderchecklist-submitbutton").disabled = true;
    
    //function for checking if all checked in one col or other - unlock button if all items in cart or substituted
    //Created by Ryan Bishop, AustinThemes.com, 2014
    function loadURLwithActual(){
    var $OrderID = <?php echo $OrderID; ?>;
  var $WDActualAmountValue = document.getElementById("ds-actual-total-input").value;
  var $WDItemsThatWereSubstituted = document.getElementById('WDallitemssubstituted').value;
  var $WDItemsThatNeedRefunds = document.getElementById('WDallitemstorefund').value;
  var $WDloadURLwithActualURLstring = '?order=' + $OrderID + '&status=checkedout<?php echo $ds_mode_query; ?>&actual=' + $WDActualAmountValue;
  if ($WDItemsThatWereSubstituted != ''){ $WDloadURLwithActualURLstring += '&subbed=' + $WDItemsThatWereSubstituted; };
  if ($WDItemsThatNeedRefunds != ''){ $WDloadURLwithActualURLstring += '&refundable=' + $WDItemsThatNeedRefunds; };
  parent.location = $WDloadURLwithActualURLstring;
  }

  function ColorChecked(x,color){
  var $wdCellToColor = document.getElementById('wdchecklistrowouter' + x).style.backgroundColor;
  if ( $wdCellToColor == 'transparent' || $wdCellToColor == ''){ document.getElementById('wdchecklistrowouter' + x).style.backgroundColor = color; } else { document.getElementById('wdchecklistrowouter' + x).style.backgroundColor = 'transparent'; }
  } //end function

  function ColorUnchecked(x){document.getElementById('wdchecklistrow' + x).style.backgroundColor = 'transparent';}

  function CheckTheBox($CheckBoxID){
  document.getElementById($CheckBoxID).checked = true;
  wdAnalyzeCheckboxes();
  }

  function CheckAllFound(){
  for(x = 1; x < $WDDifferentItems + 1;x++){
document.getElementById('ds-checkboxA-' + x).checked = true;
  } //end for loop
  wdAnalyzeCheckboxes();
  }//end function
  
  function ChangeCompletionBarToPercentage($R){
  var $WDcompletionbarWidth = document.getElementById('WDitemCounterBarOuter').offsetWidth;
  var $WDcompletionbarInnerNewWidth = ( $WDcompletionbarWidth ) * $R;
  document.getElementById('WDitemCounterBarInner').style.width = $WDcompletionbarInnerNewWidth + 'px';
   }
  
  function ToggleImageForItem(q){
  var $WDimageitemvisibility = document.getElementById('WDimagechecklistthumbnail' + q);
  if ($WDimageitemvisibility.style.display == 'block' || $WDimageitemvisibility.style.display == 'inline' || $WDimageitemvisibility.style.display == ''){ $WDimageitemvisibility.style.display = 'none'; } else {$WDimageitemvisibility.style.display = 'block';}
  }

  function wdAnalyzeCheckboxes(){
  var $WDItemsCheckedOff = 0;
  var $WDItemsRemaining = 0;
  var $WDItemsWithBothChecked = 0;
  var $WDItemsThatNeedRefunds = '';
  var $WDItemsThatWereSubstituted = '';
  var $WDActualAmountValue = document.getElementById("ds-actual-total-input").value;
  var $WDproductIdForRow;

  document.getElementById("ds-orderchecklist-submitbutton").disabled = true;
  document.getElementById("ds-orderchecklist-submitbutton").value = '<?php echo $ds_DefaultButtonText; ?>';
    
        //loop through items, determine each checkbox checked status and tally for items remaining or add error for more than one box checked
        for (var i = 1; i < $WDDifferentItems + 1; i++){
        
        $WDCheckboxA = document.getElementById('ds-checkboxA-' + i);
        $WDCheckboxB = document.getElementById('ds-checkboxB-' + i);
        $WDCheckboxC = document.getElementById('ds-checkboxC-' + i);
        
        $WDproductIdForRow = document.getElementById('WDhiddenproductIDforItem-' + i).value;  
        
        $WDCheckboxAouter = document.getElementById('ds-checkboxA-outer-' + i);
        $WDCheckboxBouter = document.getElementById('ds-checkboxB-outer-' + i);
        $WDCheckboxCouter = document.getElementById('ds-checkboxC-outer-' + i);
        
        if ($WDCheckboxB.checked){ $WDItemsThatWereSubstituted += $WDproductIdForRow + ','; }
        if ($WDCheckboxC.checked){ $WDItemsThatNeedRefunds += $WDproductIdForRow + ','; }
                  
               
        if ($WDCheckboxA.checked && $WDCheckboxB.checked && $WDCheckboxC.checked || $WDCheckboxB.checked && $WDCheckboxC.checked || $WDCheckboxA.checked && $WDCheckboxB.checked || $WDCheckboxA.checked && $WDCheckboxC.checked) {$WDItemsWithBothChecked += 1;};
        if ($WDCheckboxA.checked || $WDCheckboxB.checked || $WDCheckboxC.checked){ $WDItemsCheckedOff += 1; };
        if (!($WDCheckboxA.checked) && !($WDCheckboxB.checked) && !($WDCheckboxC.checked)){ $WDItemsRemaining += 1; };

          //id style = wdchecklistrowouter4
          if ($WDCheckboxA.checked){document.getElementById('wdchecklistrowouter' + i).style.backgroundColor = 'lightgreen';}
          else if ($WDCheckboxB.checked){document.getElementById('wdchecklistrowouter' + i).style.backgroundColor = 'yellow';}
          else if ($WDCheckboxC.checked){document.getElementById('wdchecklistrowouter' + i).style.backgroundColor = 'pink';}
          else {document.getElementById('wdchecklistrowouter' + i).style.backgroundColor = 'transparent';}



          }// end do loop


          //prepare and change status bar
          var $WDItemsTotalCount = $WDItemsCheckedOff + $WDItemsRemaining;
          var $WDItemsFound = $WDItemsTotalCount - $WDItemsRemaining;
          var $WDItemsFoundPercentage = ($WDItemsFound / $WDItemsTotalCount);
          ChangeCompletionBarToPercentage($WDItemsFoundPercentage);

          document.getElementById('WDallitemssubstituted').value = $WDItemsThatWereSubstituted;
          document.getElementById('WDallitemstorefund').value = $WDItemsThatNeedRefunds;
          document.getElementById('WDsubandrefundresultsbar').innerHTML = '<div class="col-md-12 ds-checklist-stats-title">Items Substituted :</div> <div class="col-md-12 ds-checklist-stats-data">' +  $WDItemsThatWereSubstituted + '</div><div class="col-md-12 ds-checklist-stats-title">Items to be Refunded</div><div class="col-md-12 ds-checklist-stats-data">' + $WDItemsThatNeedRefunds + '</div>';

          //trigger further functions if all items checked off, or alert with items remaining
          if ($WDItemsRemaining == 0 && $WDItemsWithBothChecked == 0 && $WDActualAmountValue != ''){ 
                document.getElementById('wdErrorMessage').innerHTML = '<div class="ds-message-normal">Now checked out, and the actual amount has been entered! You are ready to click below to deliver this order.</div>';
                document.getElementById("ds-orderchecklist-submitbutton").disabled = false;
                document.getElementById("ds-orderchecklist-submitbutton").value = 'Now Delivering Order';
                
                }
            else if ($WDItemsRemaining == 0 && $WDItemsWithBothChecked == 0 && $WDActualAmountValue == ''){ 
                document.getElementById('wdErrorMessage').innerHTML = '<div class="ds-message-normal">All groceries found! Please check out, then enter the actual total from the receipt below.</div>';
                document.getElementById("ds-orderchecklist-submitbutton").value = 'Not ready - Actual Total missing.';}
            else if ($WDItemsRemaining != 0){ 
                document.getElementById('wdErrorMessage').innerHTML = $WDItemsRemaining + ' items remaining.';}
            else if ($WDItemsWithBothChecked != 0){
                document.getElementById('wdErrorMessage').innerHTML = 'Error - some items checked more than once per row';
                document.getElementById("ds-orderchecklist-submitbutton").value = '<?php echo $ds_OvercheckedErrorButtonText; ?>';}
            } // end function
            
            
            

        </script>

<?php
} //end statusquery check
// ================== finally, reset query then output the results of the shortcode through html_entity_decode()

  wp_reset_query();
  
    if(current_user_can( 'manage_options' ) || current_user_can( 'read_shop_order' ) || current_user_can('read_private_shop_orders')){ 
        if ($ds_statusquery == 'shopping' || $ds_statusquery == 'demo' || $ds_statusquery == '' || $ds_statusquery == 'resumedelivery' || $ds_statusquery == 'checkedout'){ return html_entity_decode($DeliveryShare_orderchecklisttable); } else { return ''; }
        } else { return 'Sorry, you don\'t have the necessary user role to access shop orders.'; 
        } //close user role check

} // close the big function

?>