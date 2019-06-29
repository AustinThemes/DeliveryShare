<?php
//use function from appthemes for checking user role

/**
 * Checks if a particular user has a role. 
 * Returns true if a match was found.
 *
 * @param string $role Role name.
 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool
 */
function appthemes_check_user_role( $role, $user_id = null ) {
 
    if ( is_numeric( $user_id ) )
	$user = get_userdata( $user_id );
    else
        $user = wp_get_current_user();
 
    if ( empty( $user ) )
	return false;
 
    return in_array( $role, (array) $user->roles );
}


//add toolbar item for "incoming orders" if the user is admin or user role "driver"

if( current_user_can( 'manage_options' ) || appthemes_check_user_role( 'driver' ) ){add_action('admin_bar_menu', 'add_toolbar_items', 100);};

function add_toolbar_items($admin_bar){
    $admin_bar->add_menu( array(
        'id'    => 'incoming-orders',
        'title' => 'Incoming Orders',
        'href'  => 'http://www.speedygrocer.com/incoming-orders/',
        'meta'  => array(
            'title' => __('Incoming Orders'),            
        ),
    ));
}

?>