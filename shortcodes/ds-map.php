<?php 
if ( ! defined( 'ABSPATH' ) ) die();
/* add shortcode for displaying orders map */
add_shortcode("ds-orders-map", "DeliveryShare_OrderMap");
function DeliveryShare_OrderMap() {
if(current_user_can( 'manage_options' ) || current_user_can( 'read_shop_order' ) || current_user_can('read_private_shop_orders')){
$ds_order_statuses_to_map = sanitize_text_field($_GET['mapordertypes']);
$ds_order_zipcodes_to_map = sanitize_text_field($_GET['mapzipcodes']);
if($ds_order_zipcodes_to_map){ $ds_order_zipcodes_for_input_label = $ds_order_zipcodes_to_map; } else { $ds_order_zipcodes_for_input_label = "Enter Zip Codes"; }
?>
<style type="text/css">#map-canvas img { max-width: none;width:auto; }</style>
<div id="orders-map-topbar">
  <img src="http://www.speedygrocer.com/images/sg-delivery-map-title.png" style="margin-right:30px;" />
  <strong>Filter:</strong>
  <select id="ds-map-filter-select-status">
    <?php if($ds_order_statuses_to_map){ ?><option value="<?php echo $ds_order_statuses_to_map; ?>"><?php echo $ds_order_statuses_to_map; ?></option>
    <option value="<?php echo $ds_order_statuses_to_map; ?>">-----------</option><?php } else { ?>
    <option value="">- Order Status - </option>
    <?php } ?>
    <option value="all">all</option>
    <option value="pending">pending</option>
    <option value="processing">processing</option>
    <option value="completed">completed</option>
    <option value="refunded">refunded</option>
    <option value="failed">failed</option>
  </select>
  <input type="text" value="<?php echo $ds_order_zipcodes_for_input_label; ?>" id="ds-map-filter-input-zipcode" onfocus="DeliveryMapFiltersSelectFocus()" onunfocus="DeliveryMapFiltersSelectUnfocus()" onmouseout="DeliveryMapFiltersSelectUnfocus()" />
  <input type="button" value="Go" class="button" onClick="LoadDeliveryMapFilters()"  />
 
  
</div>
<div id="map-canvas">MAP HERE</div>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>

      function DeliveryMapFiltersSelectFocus(){
      var $wdmapOrderZipCode = document.getElementById('ds-map-filter-input-zipcode').value;
      if($wdmapOrderZipCode == 'Enter Zip Codes'){ document.getElementById('ds-map-filter-input-zipcode').value = ''; }
      } //end function

      function DeliveryMapFiltersSelectUnfocus(){
      var $wdmapOrderZipCode = document.getElementById('ds-map-filter-input-zipcode').value;
      if($wdmapOrderZipCode == ''){ document.getElementById('ds-map-filter-input-zipcode').value = 'Enter Zip Codes'; }
      } //end function

      function LoadDeliveryMapFilters(){
      var $wdmapOrderZipCode = document.getElementById('ds-map-filter-input-zipcode').value;
      var $wdmapOrderStatusInput = document.getElementById("ds-map-filter-select-status");
      var $wdmapOrderStatus = $wdmapOrderStatusInput.options[$wdmapOrderStatusInput.selectedIndex].value;
      var $wdmapNewURL = '?mapordertypes=' + $wdmapOrderStatus;
      if($wdmapOrderZipCode != 'Enter Zip Codes'){ $wdmapNewURL += '&mapzipcodes=' + $wdmapOrderZipCode; };
      parent.location = $wdmapNewURL;
      } //end function


      // Initialize and Zoom into Texas
      function initialize() {
      var mapOptions = {
      zoom: 6,
      center: new google.maps.LatLng(31.203,-98.393)
      }
      var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

      var styles = [
    {
        "featureType": "all",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "hue": "#6dff00"
            },
            {
                "saturation": "36"
            },
            {
                "lightness": "-5"
            },
            {
                "gamma": "1.26"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#006400"
            },
            {
                "weight": "0.61"
            },
            {
                "gamma": "0.51"
            },
            {
                "lightness": "54"
            },
            {
                "saturation": "-44"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "lightness": "46"
            },
            {
                "gamma": "2.15"
            },
            {
                "saturation": "56"
            },
            {
                "weight": "0.75"
            },
            {
                "color": "#0b1d09"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "weight": "4.20"
            },
            {
                "gamma": "1.09"
            },
            {
                "color": "#ffffff"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "gamma": "10.00"
            },
            {
                "weight": "0.53"
            },
            {
                "color": "#006400"
            },
            {
                "lightness": "-8"
            }
        ]
    },
    {
        "featureType": "road.highway.controlled_access",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#1aa006"
            },
            {
                "lightness": "45"
            },
            {
                "gamma": "0.51"
            }
        ]
    },
    {
        "featureType": "road.highway.controlled_access",
        "elementType": "labels.text",
        "stylers": [
            {
                "gamma": "0.00"
            },
            {
                "lightness": "8"
            },
            {
                "saturation": "-19"
            },
            {
                "weight": "4.56"
            },
            {
                "invert_lightness": true
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#86bf03"
            },
            {
                "gamma": "10.00"
            },
            {
                "lightness": "68"
            },
            {
                "saturation": "33"
            },
            {
                "weight": "2.73"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#dbffc0"
            }
        ]
    }
];

      map.setOptions({styles: styles});

      //colorize google map

      // ====== add markers

      <?php include("markers.php"); ?>

      } //close initialize()

      google.maps.event.addDomListener(window, 'load', initialize);

      



    </script>
<?php 
} else { echo 'Sorry, you don\'t have the necessary user role to access shop orders.'; }

} //close function ?>
