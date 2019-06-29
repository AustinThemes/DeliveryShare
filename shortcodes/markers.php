<?php
// ======== ADD GOOGLE MAPS MARKERS BY GETTING SHOP_ORDERS FROM WOOCOMMERCE


//first, get the google maps geocoding API key from plugin option "ds_google_api_key"
$GoogleAPIkey = get_option('ds_google_api_key');



// Add PHPfunction for adding each marker to save space
//AddMarker(1,29.8406,-97.9980);

function AddMarker($OrderNum,$Lat,$Long,$DeliveryAddress,$deliveryDate,$deliveryTime,$orderlink,$orderShopNowLink,$orderCurrentStatus,$OrderZipCode,$deliveryAssignedDriver,$orderItemsCount){
$wdsEstimatedLowTime = ($orderItemsCount * 1.5) + 10;
$wdsEstimatedHighTime = ($orderItemsCount * 1.75) + 20;
$wdsEstimateString = $orderItemsCount . ' item(s) | ' . $wdsEstimatedLowTime . ' to ' . $wdsEstimatedHighTime . ' Minutes';
?>

var contentString<?php echo $OrderNum; ?> = '<div id="content">'+
      '<h2 id="firstHeading" class="firstHeading ds-delivery-map-captionheading"><?php echo '#' . $OrderNum . ' : ' . $deliveryDate . '<br/>' . $deliveryTime; ?></h2>'+
      '<div id="bodyContent" class="ds-delivery-map-captioncontainer">'+
      '<b>Address:</b><br/><?php echo $DeliveryAddress; ?><br/><?php echo $orderlink . '<br/>' . $orderShopNowLink; ?>'+'<br/><br/><p><strong>Status:</strong> <?php echo $orderCurrentStatus; ?></p><p><strong>Driver:</strong> <?php echo $deliveryAssignedDriver; ?></p><p><strong>Delivery: <?php echo $wdsEstimateString; ?></strong></p>'+
      '</div>'+
      '</div>';

  var infowindow<?php echo $OrderNum; ?> = new google.maps.InfoWindow({
      content: contentString<?php echo $OrderNum; ?>
  });

var myLatLng<?php echo $OrderNum; ?> = new google.maps.LatLng('<?php echo $Lat; ?>','<?php echo $Long; ?>');

var DeliveryMarker<?php echo $OrderNum; ?> = new google.maps.Marker({
   position: myLatLng<?php echo $OrderNum; ?>,
   map: map,
   title: 'order <?php echo $OrderNum; ?> - <?php echo $DeliveryAddress; ?>',
});

google.maps.event.addListener(DeliveryMarker<?php echo $OrderNum; ?>, 'click', function() {
    infowindow<?php echo $OrderNum; ?>.open(map,DeliveryMarker<?php echo $OrderNum; ?>);
  });


<?php } //end function

//LOOP TO ADD MARKERS BASED ON THE LATITUDE AND LONGITUDE CUSTOM FIELDS IN THE SHOP_ORDERS CUSTOM POST TYPE

global $woocommerce;
global $wpdb; 
global $product;

$ds_order_statuses_to_map = sanitize_text_field($_GET['mapordertypes']);
$ds_order_zipcodes_to_map = sanitize_text_field($_GET['mapzipcodes']);
$ds_order_orders_by_driver = sanitize_text_field($_GET['mapbydriver']);
$ds_order_meta_key = sanitize_text_field($_GET['mapkey']);
$ds_order_meta_value = sanitize_text_field($_GET['mapvalue']);

//---- process query param "mapordertype"

if($ds_order_statuses_to_map == 'current'){ $ds_order_statuses_to_map2 = '\'wc-processing\''; }
else if($ds_order_statuses_to_map == 'completed'){ $ds_order_statuses_to_map2 = '\'wc-completed\''; }
else if($ds_order_statuses_to_map == 'cancelled'){ $ds_order_statuses_to_map2 = '\'wc-cancelled\''; }
else if($ds_order_statuses_to_map == 'refunded'){ $ds_order_statuses_to_map2 = '\'wc-refunded\''; }
else if($ds_order_statuses_to_map == 'failed'){ $ds_order_statuses_to_map2 = '\'wc-failed\''; }
else {$ds_order_statuses_to_map2 = '\'wc-processing\'';}

//---- Slice passed zipcodes into an Array if zip codes passed via query param
$ds_order_zipcodes_to_map_array = explode(',', $ds_order_zipcodes_to_map);


//$ds_order_orders_by_driver


//---- PREPARE WP QUERY ARGUMENTS CONDITIONALLY BASED ON QUERY URL PARAMETERS
if($ds_order_statuses_to_map == 'all' && !$ds_order_orders_by_driver){
$args = array(
 'post_type'   => 'shop_order',
 'posts_per_page'   => '-1',
 'post_status' => array('wc-processing','wc-completed','wc-refunded','wc-failed','wc-failed','wc-cancelled','publish','processing','cancelled','refunded','failed')
);
} else if($ds_order_orders_by_driver){
$args = array(
 'post_type'   => 'shop_order',
 'posts_per_page'   => '-1',
 'post_status' => array('wc-processing','wc-completed','wc-refunded','wc-failed','wc-failed','wc-cancelled','publish','processing','cancelled','refunded','failed'),
 'meta_key' => 'order_assigned_driver',
 'meta_value' => array($ds_order_orders_by_driver)
);
} else if($ds_order_meta_key && $ds_order_meta_value){
$args = array(
 'post_type'   => 'shop_order',
 'posts_per_page'   => '-1',
 'post_status' => array('wc-processing','wc-completed','wc-refunded','wc-failed','wc-failed','wc-cancelled','publish','processing','cancelled','refunded','failed'),
 'meta_key' => $ds_order_meta_key,
 'meta_value' => $ds_order_meta_value
);
} else {
$args = array(
	 'post_type'   => 'shop_order',
	 'posts_per_page'   => '-1',
     'post_status' => array( $ds_order_statuses_to_map2 )
);
}//end if=all





//---- START MARKERS ORDERS LOOP
$my_query = new WP_Query($args);
while ($my_query->have_posts()) : $my_query->the_post();
$custom = get_post_custom();

$ThisLongitude = $custom["longitude"][0];
$ThisLatitude = $custom["latitude"][0];
$OrderID = get_the_ID();
$order = new WC_Order($GLOBALS['post']->ID);
$deliveryAddress = $order->get_shipping_address();
$orderCurrentStatus = get_post_status($OrderID);

$orderItemsCount = $order->get_item_count();


$OrderZipCodeOption1 = $order->shipping_postcode;
$OrderZipCodeOption2 = $order->billing_postcode;    
if($OrderZipCodeOption1){$OrderZipCode = $OrderZipCodeOption1;} else if($OrderZipCodeOption2){ $OrderZipCode = $OrderZipCodeOption2; } else { $OrderZipCode = '';}

$deliveryAssignedDriver = get_post_meta( $OrderID, 'order_assigned_driver', true);

$deliveryTime = esc_attr( get_post_meta( $OrderID, 'When would you like your groceries delivered?', true) );
$deliveryTime2 = esc_attr( get_post_meta( $OrderID, 'Whenwouldyoulikeyourgroceriesdelivered', true) );

if(!$deliveryTime && $deliveryTime2){$deliveryTime = $deliveryTime2;}

$deliveryDate = esc_attr( get_post_meta( $OrderID, 'What day would you like this delivery made on?', true) );
$deliveryDate2 = esc_attr( get_post_meta( $OrderID, 'Whatdaywouldyoulikethisdeliverymadeon', true) );
if(!$deliveryDate && $deliveryDate2){$deliveryDate = $deliveryDate2;}


$orderlink = '<a href="http://www.speedygrocer.com/driver-order-checklist/?order=' . $OrderID . '&status=demo&mode=demo" class="button TransInputSubmit" style="float:left;">View Only</a>';

if($orderCurrentStatus == 'wc-processing'){
$orderShopNowLink = '<a href="http://www.speedygrocer.com/driver-order-checklist/?order=' . $OrderID . '&status=shopping" class="button TransInputSubmit WD-ClaimButton" style="float:left;">Start Shopping</a><br style="clear:both;">';
} else { $orderShopNowLink = '';};

//---- START GEOCODING IF NECESSARY
//0. Initiate function and set a few variables. This way, a simple check for contents of lat and long will find out if we can avoid a geocoding query first.
if(empty($ThisLongitude) || empty($ThisLatitude)){
$GeocodingAddress = urlencode($deliveryAddress);
$request_url = "https://maps.googleapis.com/maps/api/geocode/xml?address=".$GeocodingAddress."&sensor=false&key=".$GoogleAPIkey;
$xml = simplexml_load_file($request_url) or die("url not loading");
$GoogleAPIreturnedstatus = $xml->status;

  if($GoogleAPIreturnedstatus == "OK"){
	  
	  //get latitude and longitude from returned XML
      $ThisLatitude = (string)$xml->result->geometry->location->lat;
	  $ThisLongitude = (string)$xml->result->geometry->location->lng;
	  
	  echo '/* google returned ' . $ThisLatitude . ',' . $ThisLongitude . ' for order ' . $OrderID . ' */';
	  
	  //update latitude and longitude custom fields to avoid using more than one query per order
	  update_field( 'latitude', $ThisLatitude );
	  update_field( 'longitude', $ThisLongitude );

		
  } else {echo '//ERROR! - ' . $GoogleAPIreturned; }

}
//---- END GEOCODING



// --- THE BIG ONE - check conditions, then return AddMarker() as appropriate

if($ds_order_zipcodes_to_map){
if(!empty($ThisLongitude) && !empty($ThisLatitude) && in_array($OrderZipCode, $ds_order_zipcodes_to_map_array) == true ){ AddMarker($OrderID,$ThisLatitude,$ThisLongitude,$deliveryAddress,$deliveryDate,$deliveryTime,$orderlink,$orderShopNowLink,$orderCurrentStatus,$OrderZipCode,$deliveryAssignedDriver,$orderItemsCount); };
} else {
if(!empty($ThisLongitude) && !empty($ThisLatitude)){ AddMarker($OrderID,$ThisLatitude,$ThisLongitude,$deliveryAddress,$deliveryDate,$deliveryTime,$orderlink,$orderShopNowLink,$orderCurrentStatus,$OrderZipCode,$deliveryAssignedDriver,$orderItemsCount); };
 }

 // --- go to the next order in the loop then reset the query after everything else is done

endwhile;  wp_reset_query(); ?>
