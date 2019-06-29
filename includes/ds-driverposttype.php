<?php 

add_action( 'init', 'create_deliveryshare_posttype' );
/**
 * -- Register a driver post type
 */
function create_deliveryshare_posttype() {
	$labels = array(
		'name'               => _x( 'Drivers', 'post type general name', 'deliveryshare-textdomain' ),
		'singular_name'      => _x( 'Driver', 'post type singular name', 'deliveryshare-textdomain' ),
		'menu_name'          => _x( 'Delivery Drivers', 'admin menu', 'deliveryshare-textdomain' ),
		'name_admin_bar'     => _x( 'Driver', 'add new on admin bar', 'deliveryshare-textdomain' ),
		'add_new'            => _x( 'Add New', 'driver', 'deliveryshare-textdomain' ),
		'add_new_item'       => __( 'Add New Driver', 'deliveryshare-textdomain' ),
		'new_item'           => __( 'New Driver', 'deliveryshare-textdomain' ),
		'edit_item'          => __( 'Edit Driver Info', 'deliveryshare-textdomain' ),
		'view_item'          => __( 'View Driver Info', 'deliveryshare-textdomain' ),
		'all_items'          => __( 'All Delivery Drivers', 'deliveryshare-textdomain' ),
		'search_items'       => __( 'Search Delivery Drivers', 'deliveryshare-textdomain' ),
		'parent_item_colon'  => __( 'Driver Supervisor:', 'deliveryshare-textdomain' ),
		'not_found'          => __( 'No drivers found.', 'deliveryshare-textdomain' ),
		'not_found_in_trash' => __( 'No drivers found in Trash.', 'deliveryshare-textdomain' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'driver' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail' )
	);

	register_post_type( 'driver', $args );
}

// change "enter title here" on entry form to "enter driver name"

function title_text_input ( $title ) {
                if ( get_post_type() == 'driver' ) {
                        $title = __( 'Enter Driver Name Here, First and Last' );
                }
                return $title;
        } // End title_text_input()
add_filter( 'enter_title_here', 'title_text_input' );

?>