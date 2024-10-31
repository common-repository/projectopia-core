<?php
if ( ! function_exists('pto_terms_cpt') ) {
	function pto_terms_cpt() {
		if ( current_user_can('cqpim_create_new_terms') && current_user_can('publish_cqpim_terms') ) {
			$team_caps = array();
		} else {
			$team_caps = array( 'create_posts' => false );
		}
		$labels = array(
			'name'               => _x( 'Terms Templates', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Terms Template', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Terms Templates', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Terms Template:', 'projectopia-core' ),
			'all_items'          => __( 'Terms Templates', 'projectopia-core' ),
			'view_item'          => __( 'View Terms Template', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Terms Template', 'projectopia-core' ),
			'add_new'            => __( 'New Terms Template', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Terms Template', 'projectopia-core' ),
			'update_item'        => __( 'Update Terms Template', 'projectopia-core' ),
			'search_items'       => __( 'Search Terms Templates', 'projectopia-core' ),
			'not_found'          => __( 'No Terms Templates found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No Terms Templates found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'terms', 'projectopia-core' ),
			'description'         => __( 'Terms Templates', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $team_caps,
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
			'capability_type'     => array( 'cqpim_term', 'cqpim_terms' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
		);
		register_post_type( 'cqpim_terms', $args );
	}
	add_action( 'init', 'pto_terms_cpt', 10 );
}
if ( ! function_exists( 'pto_terms_cpt_custom_columns' ) ) {
	function pto_terms_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' => __('Title', 'projectopia-core'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_terms_posts_columns' , 'pto_terms_cpt_custom_columns', 10, 1 );
}
function pto_screen_layout_cqpim_terms() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_cqpim_terms', 'pto_screen_layout_cqpim_terms' );