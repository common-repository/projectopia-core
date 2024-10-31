<?php
if ( ! function_exists('pto_leadform_cpt') ) {
	function pto_leadform_cpt() {
		if ( current_user_can('cqpim_create_new_leadform') && current_user_can('publish_cqpim_leadforms') ) {
			$form_caps = array();
		} else {
			$form_caps = array( 'create_posts' => false );
		}   
		$labels = array(
			'name'               => _x( 'Lead Forms', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Lead Form', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Lead Forms', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Lead Form:', 'projectopia-core' ),
			'all_items'          => __( 'Lead Forms', 'projectopia-core' ),
			'view_item'          => __( 'View Lead Form', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Lead Form', 'projectopia-core' ),
			'add_new'            => __( 'New Lead Form', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Lead Form', 'projectopia-core' ),
			'update_item'        => __( 'Update Lead Form', 'projectopia-core' ),
			'search_items'       => __( 'Search Lead Forms', 'projectopia-core' ),
			'not_found'          => __( 'No lead forms found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No lead forms found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'leadform', 'projectopia-core' ),
			'description'         => __( 'Lead Forms', 'projectopia-core' ),
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
			'capability_type'     => array( 'cqpim_leadform', 'cqpim_leadforms' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
		);
		register_post_type( 'cqpim_leadform', $args );
	}
	add_action( 'init', 'pto_leadform_cpt', 10 );
}


if ( ! function_exists( 'pto_leadform_cpt_custom_columns' ) ) {
	function pto_leadform_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title'     => __('Title', 'projectopia-core'),
			'shortcode' => __('Shortcode', 'projectopia-core'),
			'type'      => __('Type', 'projectopia-core'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_leadform_posts_columns' , 'pto_leadform_cpt_custom_columns', 10, 1 );
}


add_action( 'manage_cqpim_leadform_posts_custom_column', 'content_pto_leadform_cpt_columns', 10, 2 );
function content_pto_leadform_cpt_columns( $column, $post_id ) {
	global $post;
	switch ( $column ) {
		case 'shortcode': 
            ?>
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control input timer" name="timer" value='[projectopia_lead_form id="<?php echo esc_attr( $post->ID ); ?>"]' disabled="disabled" />
				</div>
			</div>
			<?php break;
		case 'type':
			$form_type = get_post_meta($post->ID, 'form_type', true);
			if ( ! empty($form_type) ) {
				if ( $form_type == 'cqpim' ) {
					echo esc_html__('Projectopia Form Builder', 'projectopia-core');
				} else {
					echo esc_html__('Gravity Form', 'projectopia-core');
				}
			}
			break;
		default:
			break;
	}
}
function pto_leadforms_single_column() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_cqpim_leadform', 'pto_leadforms_single_column' );