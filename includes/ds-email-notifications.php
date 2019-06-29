<?php
// ------------- email notifications system for order status changes

// ===== VARIABLES
$ds_admin_email = 'ryan@austinthemes.com';

// ===== create function for setting html content type, to be ref'd in main function
function set_html_content_type() { 	return 'text/html'; }

// ===== create function WDstatusAlert() for notifying drivers and customers of deliveryshare status changes
function WDstatusAlert($ds_order_id, $ds_email_address_to_alert, $ds_new_status){

		//prepare recipients
		$ds_notification_multiple_to_recipients = array(
			$ds_admin_email,
			$ds_email_address_to_alert
		);

		//prepare subject line
		$ds_notification_subject_line = 'Order #' . $ds_order_id . ' - Status updated';

		//prepare message body depending on status
		if ( $ds_new_status == '' ){ }
		else if ( $ds_new_status == 'Now Shopping' ){ $ds_notification_message = 'Your Driver for Order (#' . $ds_order_id . ') is now Shopping for your items.'; }
		else if ( $ds_new_status == 'Done Shopping' ){ $ds_notification_message = 'Your Driver is Done Shopping for order #' . $ds_order_id .'.'; }
		else if ( $ds_new_status == 'Need To Refund' ){ $ds_notification_message = 'Your Order (#' . $ds_order_id . ') has been marked as needing a refund. Your order will be reviewed and refunded if approved by one of our managers.'; }
		else if ( $ds_new_status == 'Order Refused' ){ $ds_notification_message = 'Your Order (#' . $ds_order_id . ') has been marked as Refused. Please contact us for further action.'; }
		else if ( $ds_new_status == 'Partial Refund Due' ){ $ds_notification_message = 'Your Order (#' . $ds_order_id . ') has been marked as needing a partial refund. Please await further action from our staff!'; }
		else {$ds_notification_message = '';}
	
		//set html content type using filter
		add_filter( 'wp_mail_content_type', 'set_html_content_type' );

		//+ if all variables in place, send email using wordpress wp_mail function 
		if($ds_notification_message && $ds_notification_subject_line && $ds_notification_multiple_to_recipients){ 
			wp_mail( $ds_notification_multiple_to_recipients, $ds_notification_subject_line, '<p>The <em>HTML</em> message</p>' ); 
		}

		// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
		remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

} // close function WDstatusupdate()


// ===== hook woocommerce functions for order status changes to send notification automatically


?>