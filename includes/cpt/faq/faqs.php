<?php
if ( ! function_exists('pto_faqs_cpt') ) {
	function pto_faqs_cpt() {
		if ( current_user_can('cqpim_create_new_faqs') && current_user_can('publish_cqpim_faqs') ) {
			$form_caps = array();
		} else {
			$form_caps = array( 'create_posts' => false );
		}   
		$labels = array(
			'name'               => _x( 'FAQ', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'FAQ', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'FAQ', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent FAQ:', 'projectopia-core' ),
			'all_items'          => __( 'FAQ', 'projectopia-core' ),
			'view_item'          => __( 'View FAQ', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New FAQ', 'projectopia-core' ),
			'add_new'            => __( 'New FAQ', 'projectopia-core' ),
			'edit_item'          => __( 'Edit FAQ', 'projectopia-core' ),
			'update_item'        => __( 'Update FAQ', 'projectopia-core' ),
			'search_items'       => __( 'Search FAQ', 'projectopia-core' ),
			'not_found'          => __( 'No FAQ found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No FAQ found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'faq', 'projectopia-core' ),
			'description'         => __( 'FAQ', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $form_caps,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => 'cqpim-dashboard', 
			'show_in_admin_bar'   => true,
			'menu_position'       => 1,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array( 'cqpim_faq', 'cqpim_faqs' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => get_option('cqpim_faq_slug') ),
		);
		register_post_type( 'cqpim_faq', $args );
	}
	add_action( 'init', 'pto_faqs_cpt', 14 );
}
add_action( 'init', 'pto_faq_cats', 0 );
function pto_faq_cats() {
	$labels = array(
		'name'              => __( 'FAQ Category', 'projectopia-core' ),
		'singular_name'     => __( 'FAQ Category', 'projectopia-core' ),
		'search_items'      => __( 'Search FAQ Categorys', 'projectopia-core' ),
		'all_items'         => __( 'All FAQ Categorys', 'projectopia-core' ),
		'parent_item'       => __( 'Parent FAQ Category', 'projectopia-core' ),
		'parent_item_colon' => __( 'Parent FAQ Category:', 'projectopia-core' ),
		'edit_item'         => __( 'Edit FAQ Category', 'projectopia-core' ),
		'update_item'       => __( 'Update FAQ Category', 'projectopia-core' ),
		'add_new_item'      => __( 'Add New FAQ Category', 'projectopia-core' ),
		'new_item_name'     => __( 'New Genre FAQ Category', 'projectopia-core' ),
		'menu_name'         => __( 'FAQ Categorys', 'projectopia-core' ),
	);
	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => false,
	);
	register_taxonomy( 'cqpim_faq_cat', array( 'cqpim_faq' ), $args );
}
if ( ! function_exists( 'pto_faq_cpt_custom_columns' ) ) {
	function pto_faq_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' => __('Title', 'projectopia-core'),
			'order' => __('Order', 'projectopia-core'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_faq_posts_columns' , 'pto_faq_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_faq_posts_custom_column', 'content_pto_faq_cpt_columns', 10, 2 );
function content_pto_faq_cpt_columns( $column, $post_id ) {
	global $post;
	switch ( $column ) {
		case 'order':
			$order = get_post_meta($post->ID, 'faq_order', true);
			$ranges = range( 0,500 );
			echo '<div class="form-group">
			<div class="input-group"><select class="faq_order form-control input" data-id="' . esc_attr( $post->ID ) . '">';
				foreach ( $ranges as $range ) {
					echo '<option value="' . esc_attr( $range ) . '" ' . selected( $order, $range, false ) . '>' . esc_html( $range ) . '</option>';
				}
			echo '</select></div></div>';
			break;
		default:
			break;
	}
}
function pto_screen_layout_cqpim_faq() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_cqpim_faq', 'pto_screen_layout_cqpim_faq' );
// Initial FAQ order when save post
add_action( 'save_post', 'save_pto_initial_faq_order' );
function save_pto_initial_faq_order( $post_id ) {
    $order = get_post_meta($post_id, 'faq_order', true);
    if ( empty($order) ) {
        update_post_meta($post_id, 'faq_order', '0');
    }
}