<?php
//IF THE CUSTOM POST TYPE IS 'DRIVER', REPLACE NORMAL SINGLE POST TEMPLATE WITH MODIFIED TEMPLATE TO DISPLAY CUSTOM FIELDS FOR CPT 'DRIVER'

function new_default_content($content) {
global $post;
if ($post->post_type == 'driver' && is_single() ) {

//first, get the custom field data for the post
$ds_driverpool_var_1 = get_post_meta( get_the_ID(), 'driver_associated_wordpress_user_id', true );
$ds_driverpool_var_2 = get_post_meta( get_the_ID(), 'driver_current_status', true );
$ds_driverpool_var_3 = get_post_meta( get_the_ID(), 'driver_zip_codes_served', true );
$ds_driverpool_var_4 = get_post_meta( get_the_ID(), 'driver_preferred_stores', true );

//then, start filtering the content using those variables

$content .='<h2>Driver Info</h2>';
$content .= '<ul>';

if ($ds_driverpool_var_1 && current_user_can( 'manage_options' )){$content .= '<li style="color:red;"><strong>Associated WordPress User ID: </strong>' . $ds_driverpool_var_1 . '</li>';};
if ($ds_driverpool_var_2){$content .= '<li><strong>Driver Current Status: </strong>' . $ds_driverpool_var_2 . '</li>';};
if ($ds_driverpool_var_3){$content .= '<li><strong>Zip Codes Served: </strong>' . $ds_driverpool_var_3 . '</li>';};
if ($ds_driverpool_var_4){$content .= '<li><strong>Preferred Stores: </strong>' . $ds_driverpool_var_4 . '</li>';};

$content .= '</ul>';
}
return $content;
}
add_filter('the_content', 'new_default_content');


?>