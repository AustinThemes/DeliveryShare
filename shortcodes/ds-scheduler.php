<?php 
if ( ! defined( 'ABSPATH' ) ) die();


/* =================================== SHORTCODE FOR DISPLAYING SCHEDULING CALENDAR */
add_shortcode("ds-calendar", "DeliveryShare_scheduler_function");

function DeliveryShare_scheduler_function(){
?>

<style type="text/css">
  @import url('http://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.3.1/fullcalendar.min.css');
</style>

<script src='http://www.speedygrocer.com/wp-content/plugins/deliveryshare2/calendar/moment.min.js'></script>
<script src='http://www.speedygrocer.com/wp-content/plugins/deliveryshare2/calendar/jquery.min.js'></script>
<script src="http://www.speedygrocer.com/wp-content/plugins/deliveryshare2/calendar/jquery-ui.custom.min.js"></script>
<script src='http://www.speedygrocer.com/wp-content/plugins/deliveryshare2/calendar/fullcalendar.min.js'></script>



<script>

  $(document).ready(function() {

  $('#calendar').fullCalendar({
  header: {
  left: 'prev,next today',
  center: 'title',
  right: 'month,basicWeek,basicDay'
  },
  defaultDate: '2015-04-12',
  editable: true,
  eventLimit: true, // allow "more" link when too many events
  events: [
 
  <?php 
  // ================ start getting the calendar info from the woocommerce orders 
  
  //loop arguments
 
  


$ds_orders_to_show_on_calendar = sanitize_text_field($_GET['view']);


//---- PREPARE WP QUERY ARGUMENTS CONDITIONALLY BASED ON QUERY URL PARAMETERS

if($ds_orders_to_show_on_calendar == 'all'){
$args = array(
 'post_type'   => 'shop_order',
 'posts_per_page'   => '200',
 'order'=> 'ASC',
 'orderby'=> 'date',
 'post_status' => array('wc-processing','wc-completed','wc-refunded','publish','processing','refunded')
);
} else {
$args = array(
	 'post_type'   => 'shop_order',
	 'posts_per_page'   => '200',
   'order'=> 'ASC',
   'orderby'=> 'date',
   'post_status' => array('wc-processing')
);
}//end if=all
  
  
  
  
  $posts = new WP_Query($args); 
  
   //start the list loop
    if ($posts->have_posts()){
        while ($posts->have_posts()):
            $posts->the_post();
            $custom = get_post_custom();
            global $woocommerce;
            $OrderID = $GLOBALS['post']->ID;
            $orderObject = new WC_Order($OrderID);
            $orderItemsCount = $orderObject->get_item_count();           
            $wdsADDRESS = $orderObject->get_shipping_address();
            
            $deliveryAssignedDriver = esc_attr( get_post_meta( $OrderID, 'order_assigned_driver', true) );
            
            $deliveryTime = esc_attr( get_post_meta( $OrderID, 'When would you like your groceries delivered?', true) );
            $deliveryTime2 = esc_attr( get_post_meta( $OrderID, 'Whenwouldyoulikeyourgroceriesdelivered', true) );

            if(!$deliveryTime && $deliveryTime2){$deliveryTime = $deliveryTime2;}

            $deliveryDate = esc_attr( get_post_meta( $OrderID, 'What day would you like this delivery made on?', true) );
            $deliveryDate2 = esc_attr( get_post_meta( $OrderID, 'Whatdaywouldyoulikethisdeliverymadeon', true) );
            if(!$deliveryDate && $deliveryDate2){$deliveryDate = $deliveryDate2;}
         
            $DeliveryDateSystemReadable = date('Y-m-d',strtotime($deliveryDate));            
            
            $DeliveryInfoJSurl = 'javascript:PreviewOrderDetails(' . $OrderID . ',\'' . $deliveryDate . '\',\'' . $deliveryTime . '\', \'' . $wdsADDRESS . '\',\'' . $orderItemsCount . '\',\'' . $deliveryAssignedDriver . '\')';  
              
             //call JS for PreviewOrderDetails($wdsID,$wdsDATE,$wdsTIME,$wdsADDRESS)
              
              
            $DeliveryShare_output .= '              
                 {
                  title: \'#' . $OrderID . ' at ' . $deliveryTime . ' (' . $orderItemsCount . ')' . '\',
                  start: \'' . $DeliveryDateSystemReadable . '\',
                  url: "' . $DeliveryInfoJSurl . '",
                  },           
              ';
              
              

              
              
           
   endwhile;} else {
 return '' ; // no posts found
}
  
  echo $DeliveryShare_output;
  
  
  // ============= end getting calendar dates from orders 
  
  
  ?>
  
  
  
  ]
  });

  });

</script>


  
  
<style>

  #calendar {
  max-width: 90%;
  margin: 0 auto;
  }

</style>



<!-- ===================================================== old calendar format =============================================== -->
    
<div class="ds-scheduler-container col-md-12">
  
 <div class="ds-scheduler-title col-md-12"><h2><span class="glyphicon glyphicon-calendar"></span> SpeedyGrocer Scheduler</h2></div>
  
  
   <!-- scheduler control panel -->
  <div id="ds-scheduler-control-panel" class="col-md-4">
    <span class="ds-scheduler-control-panel-label">Actions > </span>
    <a href="http://www.speedygrocer.com/what-are-your-rates/scheduler/?view=all" class="button">Show All</a>
  </div>
  
  <!-- start the speedygrocer calendar itself -->
 <div class="ds-scheduler-col1 col-md-12"> 
  <div class="ds-calendar-container">
    <div id="calendar"></div>
		</div>
 </div>
  <!-- end speedygrocer calendar itself -->
  
  
<!--  hidden inputs for temporarily storing the selected order details -->
  <input type="hidden" id="ds-scheduler-hidden-number" value="" />
  <input type="hidden" id="ds-scheduler-hidden-date" value="" />
  <input type="hidden" id="ds-scheduler-hidden-time" value="" />
  <input type="hidden" id="ds-scheduler-hidden-address" value="" />
  <input type="hidden" id="ds-scheduler-hidden-qty" value="" />
  <input type="hidden" id="ds-scheduler-hidden-driver" value="" />
  
<!-- scheduler order info display panel (conditional) -->
  <div id="ds-scheduler-order-info-panel" class="col-md-12">
    <div id="ds-scheduler-order-info-header" class="col-md-12"><h3><span class="glyphicon glyphicon-user"></span>Order Details<a href="javascript:SchedulerLinkHide();" class="button alignright"><span class="glyphicon glyphicon-remove"></span> Hide</a></h3></div>
    
    <div id="ds-scheduler-order-info-number" class="col-md-4"></div>
    <div id="ds-scheduler-order-info-date" class="col-md-4"></div>
    <div id="ds-scheduler-order-info-time" class="col-md-4"></div>
    <div id="ds-scheduler-order-info-address" class="col-md-12"></div>
    <div id="ds-scheduler-order-info-qty" class="col-md-6"></div>
    <div id="ds-scheduler-order-info-driver" class="col-md-6"></div>
    
  <div id="ds-scheduler-order-actionsmenu">
    <a href="javascript:SchedulerLinkViewOrderInfo();" class="button"><span class="glyphicon glyphicon-zoom-in"></span> View Checklist</a> 
  <a href="javascript:SchedulerLinkEdit();" class="button"><span class="glyphicon glyphicon-pencil"></span> Edit</a> 
  <a href="javascript:SchedulerLinkMapOrder();" class="button"><span class="glyphicon glyphicon-road"></span> Map</a>
    <a href="javascript:SchedulerLinkMapOrder();" class="button"><span class="glyphicon glyphicon-remove"></span> Refund</a>
    <a href="javascript:SchedulerLinkMapOrder();" class="button"><span class="glyphicon glyphicon-pencil"></span> Mark Completed</a>
    <select id="ds-scheduler-reassign-select"><option value=""> - ReAssign Order - </option><option value="">Driver 1</option><option value="">Driver 2</option></select>
  </div>
  </div>
  
    <script type="text/javascript">
      
  
  function SchedulerLinkViewOrderInfo(){
  $wdsID = document.getElementById('ds-scheduler-hidden-number').value;
  parent.location = 'http://www.speedygrocer.com/driver-order-checklist/?order=' + $wdsID + '&status=demo&mode=demo';
  }
  
  function SchedulerLinkEdit(){
  $wdsID = document.getElementById('ds-scheduler-hidden-number').value;
  parent.location = 'http://www.speedygrocer.com/driver-order-checklist/?order=' + $wdsID + '&status=demo&mode=demo';
  }
  
  function SchedulerLinkMapOrder(){
  $wdsID = document.getElementById('ds-scheduler-hidden-number').value;
  parent.location = 'http://www.speedygrocer.com/driver-order-checklist/?order=' + $wdsID + '&status=demo&mode=demo';
  }
  
  function SchedulerLinkHide(){
  document.getElementById('ds-scheduler-order-info-panel').style.display = 'none';
  parent.location = '#ds-scheduler-control-panel';
  }
      
  function PreviewOrderDetails($wdsID,$wdsDATE,$wdsTIME,$wdsADDRESS,$wdsQTY,$wdsDRIVER){
  document.getElementById('ds-scheduler-order-info-panel').style.display = 'block';
  document.getElementById('ds-scheduler-order-info-number').innerHTML = '#' + $wdsID;
  document.getElementById('ds-scheduler-hidden-number').value = $wdsID;
  document.getElementById('ds-scheduler-order-info-date').innerHTML = $wdsDATE;
  document.getElementById('ds-scheduler-hidden-date').value = $wdsDATE;
  document.getElementById('ds-scheduler-order-info-time').innerHTML = $wdsTIME;
  document.getElementById('ds-scheduler-hidden-time').value = $wdsTIME;
  document.getElementById('ds-scheduler-order-info-address').innerHTML = $wdsADDRESS;
  document.getElementById('ds-scheduler-hidden-address').value = $wdsADDRESS;
  var $wdsEstimatedLowTime = ($wdsQTY * 1.5) + 10;
  var $wdsEstimatedHighTime = ($wdsQTY * 1.75) + 20;
  document.getElementById('ds-scheduler-order-info-qty').innerHTML = $wdsQTY + ' Item(s) / ' + $wdsEstimatedLowTime + ' to ' + $wdsEstimatedHighTime + ' mins est.';
  document.getElementById('ds-scheduler-hidden-qty').value = $wdsQTY;
  document.getElementById('ds-scheduler-order-info-driver').innerHTML = $wdsDRIVER;
  document.getElementById('ds-scheduler-hidden-driver').value = $wdsDRIVER;
  parent.location = '#ds-scheduler-order-info-panel';
  }
  
  // go ahead and hide the info panel
  document.getElementById('ds-scheduler-order-info-panel').style.display = 'none';
  </script>



  <div class="ds-scheduler-col2 col-md-12">Team Resources and Availability</div>
  
  
  <!-- scheduler stats-->  
  
<div class="ds-scheduler-col2 col-md-4">
  <h3><span class="glyphicon glyphicon-user"></span> Drivers  on Duty</h3>
  <?php echo do_shortcode('[ds-driverslist status="available"]');?>
</div>

  <div class="ds-scheduler-col3 col-md-4">
    <h3><span class="glyphicon glyphicon-user"></span> Drivers Currently Shopping</h3>
    <?php echo do_shortcode('[ds-driverslist status="active"]');?>
  </div>

  <div class="ds-scheduler-col5 col-md-4">
    <h3><span class="glyphicon glyphicon-user"></span> Drivers On Stand-By</h3>
    <?php echo do_shortcode('[ds-driverslist]');?>
  </div>



</div> <!-- close .ds-scheduler-container -->

<!-- end -==================================================== -->








<?php

} // close the big function
?>