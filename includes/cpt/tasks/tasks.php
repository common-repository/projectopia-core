<?php
if ( ! function_exists('pto_tasks_cpt') ) {
	function pto_tasks_cpt() {
		$labels = array(
			'name'               => _x( 'Tasks', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Task', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Tasks', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Task', 'projectopia-core' ),
			'all_items'          => __( 'Tasks', 'projectopia-core' ),
			'view_item'          => __( 'View Tasks', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Task', 'projectopia-core' ),
			'add_new'            => __( 'New Task', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Task', 'projectopia-core' ),
			'update_item'        => __( 'Update Task', 'projectopia-core' ),
			'search_items'       => __( 'Search Tasks', 'projectopia-core' ),
			'not_found'          => __( 'No Tasks found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No Tasks found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'tasks', 'projectopia-core' ),
			'description'         => __( 'Tasks', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 32,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array( 'cqpim_task', 'cqpim_tasks' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => get_option('cqpim_task_slug') ),
		);
		register_post_type( 'cqpim_tasks', $args );
	}
	add_action( 'init', 'pto_tasks_cpt', 0 );
}

//Filter to change status of the task
add_filter('wp_insert_post_data', 'filter_post_data', 99, 2);
function filter_post_data( $postData, $postarr ) {
	if ( $postData['post_type'] == 'cqpim_tasks' ) {
		if ( $postData['post_status'] == 'draft' || $postData['post_status'] == 'auto-draft' ) {
			$postData['post_status'] = 'private';
		}
	}
    return $postData;
}  