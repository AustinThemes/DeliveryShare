<?php
if ( ! defined( 'ABSPATH' ) ) die();
/* =================================== SHORTCODE FOR DISPLAYING DRIVER TIP FORM FOR POST-ORDER TIPPING */
add_shortcode("ds-tipform", "DeliveryShare_TipForm");
FUNCTION DeliveryShare_TipForm(){ 
$DriverName = sanitize_text_field( $_GET['drivername'] );
$OrderID = sanitize_text_field( $_GET['order'] );

$TippingFormThanksText = get_option('ds_tipping_thanks');

//generate paypal item name depending on which variables have been passed as query parameters
if (!empty($DriverName) && !empty($OrderID)){ $PaypalItemName = 'Driver Tip for Order ' . $OrderID . ' - ' . $DriverName; }
else if (!empty($DriverName) && empty($OrderID)){ $PaypalItemName = 'Driver Tip for Driver - ' . $DriverName; }
else if (empty($DriverName) && !empty($OrderID)){ $PaypalItemName = 'Driver Tip for Order ' . $OrderID;}
else { $PaypalItemName = 'Driver Tip for an Order'; }

//generate tipping form title based on query parameters passed
if (!empty($DriverName) && !empty($OrderID)){ $WDtiptitle = 'Send a Tip for your Grocery Driver, ' . $DriverName . ', for Grocery Order #' . $OrderID; } 
else if (!empty($DriverName) && empty($OrderID)){ $WDtiptitle = 'Send a Tip for your Driver, ' . $DriverName; }
else if (empty($DriverName) && !empty($OrderID)){ $WDtiptitle = 'Send a Tip for your Order #' . $OrderID; }
else {$WDtiptitle = 'Tip your Driver'; }

//generate paypal email address based on order, default to info@speedygrocer with driver name passed for distribution
if($OrderID){
$TipDriver = get_post_meta($OrderID, 'order_assigned_driver', true);





$args2 = array (
       'post_type' => 'driver',
       'posts_per_page' => -1,
       'meta_key' => 'driver_associated_wordpress_user_id',
       'meta_value' => $TipDriver
       );
$posts2 = new WP_Query($args2); 
if ($posts2->have_posts()){
        while ($posts2->have_posts()):
            $posts2->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $driverobjectID = $GLOBALS['post']->ID;
            $foundPaypalEmail = get_post_meta($driverobjectID,'driver_paypal_email_address',true);
        endwhile;
        }

    if ($foundPaypalEmail){ $TipPaypalEmailAddress = $foundPaypalEmail; } else { $TipPaypalEmailAddress = 'info@speedygrocer.com'; }

} else {
$TipPaypalEmailAddress = 'info@speedygrocer.com';
}

?>
<div class="WD-TipForm woocommerce">
    <h2>
        <?php echo $WDtiptitle; ?>
    </h2>
    <?php echo $TippingFormThanksText; ?>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paypal_form" class="track_order">

        <script type="text/javascript">
            function UpdateHiddenPaypalItemNumberString(){
            var $WDordernumber = document.getElementById('GrocerOrderNumber').value;
            var $WDdrivername = document.getElementById('GroceryDriverName').value;
            var $WDitemnumberstring = 'Order-' + $WDordernumber + ', Driver: ' + $WDdrivername;
            document.getElementById('item_number').value = $WDitemnumberstring;
            } //close function

        </script>
        <input name="cmd" type="hidden" value="_donations" />
        <input name="business" type="hidden" value="<?php echo $TipPaypalEmailAddress; ?>" />
        <input name="item_name" type="hidden" value="<?php echo $PaypalItemName; ?>" />
        
        <p class="form-row form-row-first">
            <label>Tip Amount ($)</label><br/>
            <input name="amount" type="text" value="5.00" onChange="UpdateHiddenPaypalItemNumberString()" class="input-text" />
        </p>
            
        <?php // -------------------------
        if (empty($DriverName)){ ?>
          <p class="form-row form-row-first">
            <label>Driver Name</label><br/>
            <input name="GroceryDriverName" id="GroceryDriverName" type="text" onChange="UpdateHiddenPaypalItemNumberString()" value="<?php echo $DriverName; ?>" class="input-text" />
          </p>
            <?php } else { ?><p class="form-row form-row-first">
            <input name="GroceryDriverName" id="GroceryDriverName" type="hidden" value="<?php echo $DriverName; ?>" /></p>
            <?php } ?>

        <?php // -------------------------
        if (empty($OrderID)){ ?>
          <p class="form-row form-row-first">        
            <label>Order Number (#)</label><br/>
            <input name="GroceryOrderNumber" id="GrocerOrderNumber" type="text" onChange="UpdateHiddenPaypalItemNumberString()" value="<?php echo $OrderID; ?>" class="input-text" /><br/>You can find your order number in your order confirmation email or receipt.
         </p>
        <?php } else { ?>
          <p class="form-row form-row-first">
             <input name="GroceryOrderNumber" id="GrocerOrderNumber" type="hidden" value="Order-<?php echo $OrderID; ?>" class="input-text" /></p>
             <?php } 
             //------------------------ ?>


                 <input name="item_number" id="item_number" type="hidden" value="Order-<?php echo $OrderID; ?>" class="input-text" />
                 <input name="currency_code" type="hidden" value="USD" />
            <p align="center">
                <input type="submit" class="button" value="Send Tip" onmouseover="UpdateHiddenPaypalItemNumberString()" />
            </p>
    </form>
</div>

<?php
}
?>