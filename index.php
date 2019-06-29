<?php
if ( ! defined( 'ABSPATH' ) ) die();
/*
Plugin Name: DeliveryShare
Plugin URI: http://www.austinthemes.com
Description: A premium wordpress plugin that adds a suite of delivery driver management features to your WooCommerce-powered online store using WordPress.
Version: 1.0
Author: Ryan Bishop, Austin Themes
Author URI: http://www.austinthemes.com
*/

/* Create custom fields for driver */

/* load custom css */
wp_enqueue_style( 'DeliveryShareStylesheet',  plugins_url( 'deliveryshare/DeliveryShare.css' ), false );

//define plugin defaults
DEFINE("DeliveryShare_QTY", "15");

//load security functions for calling later
//include("includes/ds-security-and-roles.php");

//create options menu
include("includes/ds-optionsmenu.php");

//create custom post type for Drivers (singular: driver)
include("includes/ds-driverposttype.php");

//add shortcut links to admin bar
//include("includes/ds-menubar-hooks.php");

//add and hook driver custom post type single.php to display custom field details as driver profile
include("includes/ds-import-post-content-template.php");

//include modular shortcode files. each shortcode file contains the full shortcode function and register_shortcode
include("shortcodes/ds-tipform.php");
include("shortcodes/ds-current_user_orders.php");
include("shortcodes/ds-claimloop.php");
include("shortcodes/ds-driverpool.php");
include("shortcodes/ds-ordersbydriver.php");
include("shortcodes/ds-map.php");
include("shortcodes/ds-orderchecklist.php");
include("shortcodes/ds-scheduler.php");
include("shortcodes/ds-drivers_list.php");
include("shortcodes/ds-refunds.php");
include("shortcodes/ds-ratingsform.php");
include("shortcodes/ds-flaggingform.php");
include("shortcodes/ds-driverstatusbar.php");

//add custom order statuses for WooCommerce to handle delivery-related fields
include("includes/ds-custom-order-statuses.php");



?>