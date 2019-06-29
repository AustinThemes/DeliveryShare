<?php
function deliveryshare_register_settings() {
	
add_option( 'ds_google_api_key', '1');
add_option( 'ds_path_incomingorders', 'alpha');
add_option( 'ds_path_checklist', '1');
add_option( 'ds_whitelabel_text', '1');
add_option( 'ds_paypal_email_address ', '1');
add_option( 'ds_hide_driver_profiles', '1');
add_option( 'ds_tipping_thanks', '1');
add_option( 'ds_logging', '1');
add_option( 'ds_status_alert_email', '1');
add_option( 'ds_sort_checklist_by', '1');
add_option( 'ds_checklist_show_images', '1');
add_option( 'ds_checklist_button_class', '1');
add_option( 'ds_input_class', '1');
add_option( 'ds_checklist_table_or_css', '1');
add_option( 'ds_checklist_disable_refusal', '1');
add_option( 'ds_checklist_disable_cancelandrefund', '1');
add_option( 'ds_checklist_radio_or_checkbox', '1');
add_option( 'ds_use_assigned_driver_system', '1');
add_option( 'ds_use_cost_tracking', '1');
add_option( 'ds_use_map', '1');
add_option( 'ds_use_map_google_geocoding_api', '1');
add_option( 'ds_enabled_modes', '1');
add_option( 'ds_unique_id_prefix', '1');
add_option( 'ds_checklist_thumbnail_size', '1');
add_option( 'ds_display_skin', '1');
add_option( 'ds_drivers_list_is_public', '1');
add_option( 'ds_drivers_list_label', '1');
  
register_setting( 'default', 'ds_google_api_key' ); 
register_setting( 'default', 'ds_path_incomingorders' );
register_setting( 'default', 'ds_path_checklist' );
register_setting( 'default', 'ds_whitelabel_text' );
register_setting( 'default', 'ds_paypal_email_address ' );
register_setting( 'default', 'ds_hide_driver_profiles' );
register_setting( 'default', 'ds_tipping_thanks' );
register_setting( 'default', 'ds_logging' );
register_setting( 'default', 'ds_status_alert_email' );
register_setting( 'default', 'ds_sort_checklist_by' );
register_setting( 'default', 'ds_checklist_show_images' );
register_setting( 'default', 'ds_checklist_button_class' );
register_setting( 'default', 'ds_input_class' );
register_setting( 'default', 'ds_checklist_table_or_css' );
register_setting( 'default', 'ds_checklist_disable_refusal' );
register_setting( 'default', 'ds_checklist_disable_cancelandrefund' );
register_setting( 'default', 'ds_checklist_radio_or_checkbox' );
register_setting( 'default', 'ds_use_assigned_driver_system' );
register_setting( 'default', 'ds_use_cost_tracking' );
register_setting( 'default', 'ds_use_map' );
register_setting( 'default', 'ds_use_map_google_geocoding_api' );
register_setting( 'default', 'ds_enabled_modes' );
register_setting( 'default', 'ds_unique_id_prefix' );
register_setting( 'default', 'ds_checklist_thumbnail_size' );
register_setting( 'default', 'ds_display_skin' );
register_setting( 'default', 'ds_drivers_list_is_public' );
register_setting( 'default', 'ds_drivers_list_label' );
  
} 
add_action( 'admin_init', 'deliveryshare_register_settings' );
 
function deliveryshare_register_options_page() {
	add_options_page('DeliveryShare', 'DeliveryShare', 'manage_options', 'deliveryshare-options', 'deliveryshare_options_page');
}
add_action('admin_menu', 'deliveryshare_register_options_page');
 
function deliveryshare_options_page() {
	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>DeliveryShare Plugin Settings</h2>
  <p>Please fill out the following fields to set up your DeliveryShare System.</p>
	<form method="post" action="options.php"> 
		<?php settings_fields( 'default' ); ?>
		
			<p>Please configure the DeliveryShare plugin using the customization fields below.</p>
    
    
    <!--  + Settings Group : Big Picture Settings -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">General Settings</h3>

    <table class="form-table">
<tr valign="top">
        <th scope="row">
          <label for="ds_whitelabel_text">Plugin Name for Re-labeling</label>
        </th>
        <td>
          <input type="text" id="ds_whitelabel_text" name="ds_whitelabel_text" value="<?php echo get_option('ds_whitelabel_text'); ?>" />
        </td>
      </tr>
      
       <tr valign="top">
        <th scope="row">
          <label for="ds_logging">Enable Logging?</label>
        </th>
        <td>
          <input type="text" id="ds_logging" name="ds_logging" value="<?php echo get_option('ds_logging'); ?>" />
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">
          <label for="ds_enabled_modes">Enable or Disable Modes</label>
        </th>
        <td>
          <input type="text" id="ds_enabled_modes" name="ds_enabled_modes" value="<?php echo get_option('ds_enabled_modes'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_unique_id_prefix">Unique ID Prefix for Item Numbers, etc</label>
        </th>
        <td>
          <input type="text" id="ds_unique_id_prefix" name="ds_unique_id_prefix" value="<?php echo get_option('ds_unique_id_prefix'); ?>" />
        </td>
      </tr>
      
    </table>

    
    <!--  + Settings Group : Map -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">Map Settings</h3>

    <table class="form-table">

      <tr valign="top">
        <th scope="row">
          <label for="ds_google_api_key">Google API Key (must have GeoCoding API enabled)</label>
        </th>
        <td>
          <input type="text" id="ds_google_api_key" name="ds_google_api_key" value="<?php echo get_option('ds_google_api_key'); ?>" />
        </td>
      </tr>
      
      </table>
    
    
    <!--  + Settings Group : URLs for DeliveryShare shortcut buttons and menu -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">System Page URLs</h3>
    <table class="form-table">
      
      <tr valign="top">
        <th scope="row">
          <label for="ds_path_incomingorders">Incoming Orders URL</label>
        </th>
        <td>
          <input type="text" id="ds_path_incomingorders" name="ds_path_incomingorders" value="<?php echo get_option('ds_path_incomingorders'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_path_checklist">Checklist URL</label>
        </th>
        <td>
          <input type="text" id="ds_path_checklist" name="ds_path_checklist" value="<?php echo get_option('ds_path_checklist'); ?>" />
        </td>
      </tr>

    </table>


    <!--  + Settings Group : Email and Notifications -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">Email and Notifications</h3>




    <table class="form-table">

      <tr valign="top">
        <th scope="row">
          <label for="ds_paypal_email_address">Email Address for PayPal</label>
        </th>
        <td>
          <input type="text" id="ds_paypal_email_address" name="ds_paypal_email_address" value="<?php echo get_option('ds_paypal_email_address'); ?>" />
        </td>
      </tr>    

      <tr valign="top">
        <th scope="row">
          <label for="ds_status_alert_email">Send Customers Status Alerts</label>
        </th>
        <td>
          <input type="text" id="ds_status_alert_email" name="ds_status_alert_email" value="<?php echo get_option('ds_status_alert_email'); ?>" />
        </td>
      </tr>

    </table>
    <!--  + Settings Group : Checklist -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">Checklist</h3>
    <table class="form-table">


      <tr valign="top">
        <th scope="row">
          <label for="ds_sort_checklist_by">Sort Checklist By?</label>
        </th>
        <td>
          <input type="text" id="ds_sort_checklist_by" name="ds_sort_checklist_by" value="<?php echo get_option('ds_sort_checklist_by'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_show_images">Show Images on Checklist?</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_show_images" name="ds_checklist_show_images" value="<?php echo get_option('ds_checklist_show_images'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_button_class">CSS Class for Checklist Button?</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_button_class" name="ds_checklist_button_class" value="<?php echo get_option('ds_checklist_button_class'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_input_class">Custom CSS Class for Inputs</label>
        </th>
        <td>
          <input type="text" id="ds_input_class" name="ds_input_class" value="<?php echo get_option('ds_input_class'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_table_or_css">Use Table or CSS layout for the Checklist? (CSS Highly Recommended to support mobile devices)</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_table_or_css" name="ds_checklist_table_or_css" value="<?php echo get_option('ds_checklist_table_or_css'); ?>" />
        </td>
      </tr>
      

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_disable_refusal">Disable Customer Refusal Feature?</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_disable_refusal" name="ds_checklist_disable_refusal" value="<?php echo get_option('ds_checklist_disable_refusal'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_disable_cancelandrefund">Disable Refund Features, including Cancel and Refund Buttons?</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_disable_cancelandrefund" name="ds_checklist_disable_cancelandrefund" value="<?php echo get_option('ds_checklist_disable_cancelandrefund'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_radio_or_checkbox">Use Radio Buttons or Checkboxes for the Checklist Feature?</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_radio_or_checkbox" name="ds_checklist_radio_or_checkbox" value="<?php echo get_option('ds_checklist_radio_or_checkbox'); ?>" />
        </td>
      </tr>

    </table>

    <!--  + Settings Group : Driver Reports and Metrics -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">Driver Reports and Metrics</h3>
    <table class="form-table">

      <tr valign="top">
        <th scope="row">
          <label for="ds_use_assigned_driver_system">Use Assigned Driver System?</label>
        </th>
        <td>
          <input type="text" id="ds_use_assigned_driver_system" name="ds_use_assigned_driver_system" value="<?php echo get_option('ds_use_assigned_driver_system'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_use_cost_tracking">Use Actual Cost and Profit Tracking?</label>
        </th>
        <td>
          <input type="text" id="ds_use_cost_tracking" name="ds_use_cost_tracking" value="<?php echo get_option('ds_use_cost_tracking'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_use_map">Send Customers Status Alerts</label>
        </th>
        <td>
          <input type="text" id="ds_use_map" name="ds_use_map" value="<?php echo get_option('ds_use_map'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_use_map_google_geocoding_api">Use Google Geocoding API for latitude and longitudes not available? (note - addresses alone will not work, so these two variables must be supplied)</label>
        </th>
        <td>
          <input type="text" id="ds_use_map_google_geocoding_api" name="ds_use_map_google_geocoding_api" value="<?php echo get_option('ds_use_map_google_geocoding_api'); ?>" />
        </td>
      </tr>

      

      <tr valign="top">
        <th scope="row">
          <label for="ds_checklist_thumbnail_size">Checklist Thumbnail Size</label>
        </th>
        <td>
          <input type="text" id="ds_checklist_thumbnail_size" name="ds_checklist_thumbnail_size" value="<?php echo get_option('ds_checklist_thumbnail_size'); ?>" />
        </td>
      </tr>

    </table>

    <!--  + Settings Group : Driver Profile and Displays -->
    <h3 style="background:black;color:white;font-weight:bold;padding:8px;">Driver Profile and Displays</h3>
    <table class="form-table">

      <tr valign="top">
        <th scope="row">
          <label for="ds_hide_driver_profiles">Hide Driver Profiles</label>
        </th>
        <td>
          <input type="text" id="ds_hide_driver_profiles" name="ds_hide_driver_profiles" value="<?php echo get_option('ds_hide_driver_profiles'); ?>" />
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">
          <label for="ds_display_skin">WooDriver Layout</label>
        </th>
        <td>
          <input type="text" id="ds_display_skin" name="ds_display_skin" value="<?php echo get_option('ds_display_skin'); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="ds_drivers_list_is_public">Make Drivers List Public?</label>
        </th>
        <td>
          <input type="text" id="ds_drivers_list_is_public" name="ds_drivers_list_is_public" value="<?php echo get_option('ds_drivers_list_is_public'); ?>" />
        </td>
      </tr>

   </table>

      <!--  + Settings Group : Messages and Labels -->
      <h3 style="background:black;color:white;font-weight:bold;padding:8px;">Messages and Labels</h3>
      <table class="form-table">

      <tr valign="top">
        <th scope="row">
          <label for="ds_tipping_thanks">Tipping Thanks Message</label>
        </th>
        <td>
          <input type="text" id="ds_tipping_thanks" name="ds_tipping_thanks" value="<?php echo get_option('ds_tipping_thanks'); ?>" />
        </td>
      </tr>
        
           <tr valign="top">
        <th scope="row">
          <label for="ds_drivers_list_label">Drivers List Text</label>
        </th>
        <td>
          <input type="text" id="ds_drivers_list_label" name="ds_drivers_list_label" value="<?php echo get_option('ds_drivers_list_label'); ?>" />
        </td>
      </tr>


    </table>
		<?php submit_button(); ?>
	</form>
</div>
<?php
}
?>