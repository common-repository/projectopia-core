<?php
if ( ! function_exists('pto_messages_cpt') ) {
	function pto_messages_cpt() {
		$labels = array(
			'name'               => _x( 'Messages', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Messages', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Messages', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Messages:', 'projectopia-core' ),
			'all_items'          => __( 'Messages', 'projectopia-core' ),
			'view_item'          => __( 'Messages', 'projectopia-core' ),
			'add_new_item'       => __( 'Messages', 'projectopia-core' ),
			'add_new'            => __( 'Messages', 'projectopia-core' ),
			'edit_item'          => __( 'Messages', 'projectopia-core' ),
			'update_item'        => __( 'Messages', 'projectopia-core' ),
			'search_items'       => __( 'Messages', 'projectopia-core' ),
			'not_found'          => __( 'Messages', 'projectopia-core' ),
			'not_found_in_trash' => __( 'Messages', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'Messages', 'projectopia-core' ),
			'description'         => __( 'Messages', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => false, 
			'show_in_admin_bar'   => false,
			'menu_position'       => 22,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
		);
		register_post_type( 'cqpim_messages', $args );
	}
	add_action( 'init', 'pto_messages_cpt', 11 );
}
if ( ! function_exists('pto_conversations_cpt') ) {
	function pto_conversations_cpt() {
		$labels = array(
			'name'               => _x( 'Conversations', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Conversations', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Conversations', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Conversations:', 'projectopia-core' ),
			'all_items'          => __( 'Conversations', 'projectopia-core' ),
			'view_item'          => __( 'Conversations', 'projectopia-core' ),
			'add_new_item'       => __( 'Conversations', 'projectopia-core' ),
			'add_new'            => __( 'Conversations', 'projectopia-core' ),
			'edit_item'          => __( 'Conversations', 'projectopia-core' ),
			'update_item'        => __( 'Conversations', 'projectopia-core' ),
			'search_items'       => __( 'Conversations', 'projectopia-core' ),
			'not_found'          => __( 'Conversations', 'projectopia-core' ),
			'not_found_in_trash' => __( 'Conversations', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'Conversations', 'projectopia-core' ),
			'description'         => __( 'Conversations', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => false, 
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
		);
		register_post_type( 'cqpim_conversations', $args );
	}
	add_action( 'init', 'pto_conversations_cpt', 11 );
}