<?php 


//=================================== New order status - Now Shopping

function ds_register_deliveryshare_status_steps() {
  register_post_status( 'ds_now_shopping', array(
    'label' => _x( 'Now Shopping', 'Order status', 'textdomain' ),
    'public' => true,
    'exclude_from_search' => false,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop( 'Now Shopping <span class="count">(%s)</span>', 'Now Shopping <span class="count">(%s)</span>', 'textdomain' )
  ) );

  register_post_status( 'ds_done_shopping', array(
    'label' => _x( 'Done Shopping', 'Order status', 'textdomain' ),
    'public' => true,
    'exclude_from_search' => false,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop( 'Done <span class="count">(%s)</span>', 'Done Shopping <span class="count">(%s)</span>', 'textdomain' )
  ) );

  register_post_status( 'ds_need_to_refund', array(
    'label' => _x( 'Need to Refund', 'Order status', 'textdomain' ),
    'public' => true,
    'exclude_from_search' => false,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop( 'Need to Refund <span class="count">(%s)</span>', 'Need to Refund <span class="count">(%s)</span>', 'textdomain' )
  ) );

  register_post_status( 'ds_order_refused', array(
    'label' => _x( 'Order Refused', 'Order status', 'textdomain' ),
    'public' => true,
    'exclude_from_search' => false,
    'show_in_admin_all_list' => true,
    'show_in_admin_status_list' => true,
    'label_count' => _n_noop( 'Order Refused <span class="count">(%s)</span>', 'Order Refused <span class="count">(%s)</span>', 'textdomain' )
  ) );
}
  
add_action( 'init', 'ds_register_deliveryshare_status_steps' );
  
//==================================== Register in wc_order_statuses.
function ds_new_wc_order_status( $order_statuses ) {
  $order_statuses['ds_now_shopping'] = _x( 'Now Shopping', 'Order status', 'textdomain' );
  $order_statuses['ds_done_shopping'] = _x( 'Done Shopping', 'Order status', 'textdomain' );
  $order_statuses['ds_need_to_refund'] = _x( 'Need to Refund', 'Order status', 'textdomain' );
  $order_statuses['ds_order_refused'] = _x( 'Order Refused', 'Order status', 'textdomain' );
  
  return $order_statuses;
}
  
add_filter( 'wc_order_statuses', 'ds_new_wc_order_status' );



?>