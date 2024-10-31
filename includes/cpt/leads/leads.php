<?php
if ( ! function_exists('pto_leads_cpt') ) {
	function pto_leads_cpt() {
		if ( current_user_can('cqpim_create_new_lead') && current_user_can('publish_cqpim_leads') ) {
			$form_caps = array();
		} else {
			$form_caps = array( 'create_posts' => false );
		}   
		$labels = array(
			'name'               => _x( 'Leads', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Lead', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Leads', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Lead:', 'projectopia-core' ),
			'all_items'          => __( 'Leads', 'projectopia-core' ),
			'view_item'          => __( 'View Lead', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Lead', 'projectopia-core' ),
			'add_new'            => __( 'New Lead', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Lead', 'projectopia-core' ),
			'update_item'        => __( 'Update Lead', 'projectopia-core' ),
			'search_items'       => __( 'Search Leads', 'projectopia-core' ),
			'not_found'          => __( 'No leads found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No leads found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'lead', 'projectopia-core' ),
			'description'         => __( 'Leads', 'projectopia-core' ),
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
			'capability_type'     => array( 'cqpim_lead', 'cqpim_leads' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
		);
		register_post_type( 'cqpim_lead', $args );
	}
	add_action( 'init', 'pto_leads_cpt', 10 );
}
add_action( 'init', 'pto_leads_cats', 0 );
function pto_leads_cats() {
	$labels = array(
		'name'              => __( 'Lead Type', 'projectopia-core' ),
		'singular_name'     => __( 'Lead Type', 'projectopia-core' ),
		'search_items'      => __( 'Search Lead Types', 'projectopia-core' ),
		'all_items'         => __( 'All Lead Types', 'projectopia-core' ),
		'parent_item'       => __( 'Parent Lead Type', 'projectopia-core' ),
		'parent_item_colon' => __( 'Parent Lead Type:', 'projectopia-core' ),
		'edit_item'         => __( 'Edit Lead Type', 'projectopia-core' ),
		'update_item'       => __( 'Update Lead Type', 'projectopia-core' ),
		'add_new_item'      => __( 'Add New Lead Type', 'projectopia-core' ),
		'new_item_name'     => __( 'New Genre Lead Type', 'projectopia-core' ),
		'menu_name'         => __( 'Lead Types', 'projectopia-core' ),
	);
	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => false,
	);
	register_taxonomy( 'cqpim_lead_cat', array( 'cqpim_lead' ), $args );
}
if ( ! function_exists( 'pto_lead_cpt_custom_columns' ) ) {
	function pto_lead_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title' => __('Title', 'projectopia-core'),
			'form'  => __('Lead Form', 'projectopia-core'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_lead_posts_columns' , 'pto_lead_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_lead_posts_custom_column', 'content_pto_lead_cpt_columns', 10, 2 );
function content_pto_lead_cpt_columns( $column, $post_id ) {
	global $post;
	switch ( $column ) {
		case 'form':
			$leadform_id = get_post_meta($post->ID, 'leadform_id', true);
			$leadform_obj = get_post($leadform_id);
			if ( ! empty($leadform_id) ) {
				echo '<a href="' . esc_url( get_edit_post_link($leadform_id) ) . '">' . esc_html( $leadform_obj->post_title ) . '</a>';
			} else {
				esc_html_e('Lead Added Manually', 'projectopia-core');
			}
			break;
		default:
			break;
	}
}