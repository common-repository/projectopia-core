<?php
if ( ! function_exists('pto_projects_cpt') ) {
	function pto_projects_cpt() {
		if ( current_user_can('cqpim_create_new_project') && current_user_can('publish_cqpim_projects') ) {
			$project_caps = array();
		} else {
			$project_caps = array( 'create_posts' => false );
		}   
		$labels = array(
			'name'               => _x( 'Projects', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Project', 'Post Type Singular Name','projectopia-core' ),
			'menu_name'          => __( 'Projects', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Project:', 'projectopia-core' ),
			'all_items'          => __( 'Projects', 'projectopia-core' ),
			'view_item'          => __( 'View Project', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Project', 'projectopia-core' ),
			'add_new'            => __( 'New Project', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Project', 'projectopia-core' ),
			'update_item'        => __( 'Update Project', 'projectopia-core' ),
			'search_items'       => __( 'Search', 'projectopia-core' ),
			'not_found'          => __( 'No projects found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No projects found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'project', 'projectopia-core' ),
			'description'         => __( 'Projects', 'projectopia-core' ),
			'labels'              => $labels,
			'capabilities'        => $project_caps,
			'map_meta_cap'        => true, 
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
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
			'capability_type'     => array( 'cqpim_project', 'cqpim_projects' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => get_option('cqpim_project_slug') ),
		);
		register_post_type( 'cqpim_project', $args );
	}
	add_action( 'init', 'pto_projects_cpt', 10 );
}
	add_action( 'init', 'pto_project_cats', 0 );
	function pto_project_cats() {
		$labels = array(
			'name'              => __( 'Project Type', 'projectopia-core' ),
			'singular_name'     => __( 'Project Type', 'projectopia-core' ),
			'search_items'      => __( 'Search Project Types', 'projectopia-core' ),
			'all_items'         => __( 'All Project Types', 'projectopia-core' ),
			'parent_item'       => __( 'Parent Project Type', 'projectopia-core' ),
			'parent_item_colon' => __( 'Parent Project Type:', 'projectopia-core' ),
			'edit_item'         => __( 'Edit Project Type', 'projectopia-core' ),
			'update_item'       => __( 'Update Project Type', 'projectopia-core' ),
			'add_new_item'      => __( 'Add New Project Type', 'projectopia-core' ),
			'new_item_name'     => __( 'New Genre Project Type', 'projectopia-core' ),
			'menu_name'         => __( 'Project Types', 'projectopia-core' ),
		);
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
		);
		register_taxonomy( 'cqpim_project_cat', array( 'cqpim_project' ), $args );
	}
if ( ! function_exists( 'pto_project_cpt_custom_columns' ) ) {
	function pto_project_cpt_custom_columns( $columns ) {

		unset( $columns['date'] );
		$new_columns = array(
			'title'            => __('Title', 'projectopia-core'),
			'p_status'         => __( 'Status', 'projectopia-core' ),
			'modified_date'    => __( 'Modified Date', 'projectopia-core' ),
			'client_details'   => __( 'Client', 'projectopia-core'),
			'project_progress' => __( 'Progress', 'projectopia-core' ),
			//'project_progress_per' => '',
		);

		return array_merge( $columns, $new_columns );
	}

	add_filter('manage_cqpim_project_posts_columns' , 'pto_project_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_project_posts_custom_column', 'content_pto_project_cpt_columns', 10, 2 );
function content_pto_project_cpt_columns( $column, $post_id ) {
	global $post;
	$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
	if ( ! $project_contributors ) {
		$project_contributors = array();
	}
	$user = wp_get_current_user();
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$assigned = 0;
	$members = get_posts($args);
	foreach ( $members as $member ) {
		$team_details = get_post_meta($member->ID, 'team_details', true);
		if ( $team_details['user_id'] == $user->ID ) {
			$assigned = $member->ID;
		}
	}
	foreach ( $project_contributors as $contributor ) {
		if ( ! empty($contributor['team_id']) && $assigned == $contributor['team_id'] ) {
			$access = true;
		}
	}
	$project_details = get_post_meta( $post_id, 'project_details', true );
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contract = get_post_meta($client_id, 'client_contract', true);
	if ( ! empty($client_contact) ) {
		if ( ! empty($client_details['user_id']) && $client_details['user_id'] == $client_contact ) {
			$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
			$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
		} else {
			$client_contact_name = isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_contacts[ $client_contact ]['telephone']) ? $client_contacts[ $client_contact ]['telephone'] : '';
			$client_email = isset($client_contacts[ $client_contact ]['email']) ? $client_contacts[ $client_contact ]['email'] : '';        
		}
	} else {
		$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
		$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';      
	}
	$project_sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$project_accepted = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$contract_status = get_post_meta($post->ID, 'contract_status', true);
	$project_elements = get_post_meta($post->ID, 'project_elements', true);

	//Get count the no of task for this particular project.
	$task_count = 0;
	$task_total_count = 0;
	$task_complete_count = 0;
	if ( ! empty( $project_elements ) ) {
		foreach ( $project_elements as $element ) {

			if ( empty( $element ) || empty( $element['id'] ) ) {
				continue;
			}

			$args = array(
				'post_type'      => 'cqpim_tasks',
				'posts_per_page' => -1,
				'meta_key'       => 'milestone_id',
				'meta_value'     => $element['id'],
				'orderby'        => 'date',
				'order'          => 'ASC',
			);

			$tasks = get_posts($args);  
			foreach ( $tasks as $task ) {
				$task_total_count++;
				$task_details = get_post_meta($task->ID, 'task_details', true);
				$status = isset($task_details['status']) ? $task_details['status'] : '';
				if ( $status != 'complete' ) {
					$task_count++;
				}
				if ( $status == 'complete' ) {
					$task_complete_count++;
				}
			}
		}
	}

	//Calculate project completed in percentage.
	$project_complete_per = 0;
	if ( ! empty( $task_total_count ) ) {
		$pc_per_task = 100 / $task_total_count;
		$project_complete_per = round( $pc_per_task * $task_complete_count );
	}

	switch ( $column ) {
		case 'project_progress':
			?>
			<div class="circleProgressBar">
				<div id="<?php echo 'progress' . esc_attr( $post_id ) . '-circle'; ?>"
					data-percent="<?php echo esc_attr( $project_complete_per ); ?>"
					class="small">
				</div>
			</div>
			<?php
		    break;
		case 'modified_date':
			$modified_time = get_post_modified_time( 'h:i A', null, $post_id );
			$modified_date = get_post_modified_time( 'M d, Y', null, $post_id );

			printf( '<p>%s</p>', esc_html( $modified_date ) );
			printf( '<p><a href="mailto:%1$s">%1$s</a></p>', esc_html( $modified_time ) );
		    break;
		/*case 'project_progress_per':
			?>
			<div class="completedProject">
				<p><?php esc_html_e('Complete: ' , 'projectopia-core') ?></p>
				<span><?php echo $project_complete_per; ?>%</span>
			</div>
			<?php
		    break;*/
		case 'client_details':
			if ( ! empty( $client_id ) &&
				! empty( $access ) || current_user_can( 'cqpim_view_project_client_info' ) || ! empty( $client_id ) &&
				in_array( 'administrator', $user->roles ) || ! empty( $client_id ) &&
				in_array( 'cqpim_admin', $user->roles ) ) {

				printf( '<p>%s</p>', esc_html( $client_company_name ) );
				printf( '<p><a href="mailto:%1$s">%1$s</a></p>', esc_html( $client_email ) );
				printf( '<p><a href="tel:%1$s">%1$s</a></p>', esc_html( $client_telephone ) );

			} else {
				echo '<p>' . esc_html__( 'Details Not Available', 'projectopia-core' ) . '</p>';
			}
		    break;
		case 'p_status':
		$checked = get_option('enable_project_contracts'); 
		if ( $client_id ) {
			if ( ! $closed ) {
				if ( ! $signoff ) {
					if ( $contract_status == 1 ) {
						if ( ! $project_accepted ) {
							if ( empty($project_sent) ) {
								printf( '<p class="status notSent">%s</p>', esc_html__('Contract Not Sent', 'projectopia-core') );     
							}
							if ( $project_sent ) {
								printf( '<p class="status clientApproval">%s</p>', esc_html__('Contract Sent', 'projectopia-core') );      
							}
						} else {
							printf( '<p class="status clientApproval">%s</p>', esc_html__('Contract Accepted', 'projectopia-core') );
						}
					} else {
						printf( '<p class="status clientApproval">%s</p>', esc_html__('In Progress', 'projectopia-core') );        
					}
				} else {
					printf( '<p class="status normal">%s</p>', esc_html__('Signed off', 'projectopia-core') );     
				}
			} else {
				printf( '<p class="status approved">%s</p>', esc_html__( 'Closed', 'projectopia-core') );                  
			}
		} else {
			if ( ! $closed ) {
				printf( '<p class="status approved">%s</p>', esc_html__( 'Not a client project', 'projectopia-core') );        
			} else {
				printf( '<p class="status approved">%s</p>', esc_html__( 'Closed', 'projectopia-core') );      
			}       
		}
		    break;
		default:
		    break;
	}
}

add_action( 'pre_get_posts', 'pto_control_project_order' );
function pto_control_project_order( $query ) {
	if ( ! is_admin() || ( 'cqpim_project' !== $query->get( 'post_type' ) ) ) {
		return;
	}

	if ( $query->get( 'orderby' ) || $query->get( 'order' ) ) {
		return;
	}
    
    if ( ! $query->is_main_query() ) {
    	return;
    }

	$sort = get_option( 'pto_default_project_sort' );
	$order = get_option( 'pto_default_project_order' );
	$closed = get_option( 'pto_default_drop_closed' );
	$sortby = ( $sort == 1 ) ? 'title' : 'date';

	if ( ! empty( $closed ) ) {
		$meta_query = array(
			'relation' => 'OR',
			array(
				'key'     => 'closed',
				'compare' => 'NOT EXISTS',
			),
			array(
				'relation' => 'OR',
				array(
					'key'   => 'closed',
					'value' => 1,
				),
				array(
					'key'     => 'closed',
					'value'   => 1,
					'compare' => '!=',
				),
			),
		);
		$query->set( 'meta_query', $meta_query );
		$query->set( 'orderby', array(
			'meta_value' => 'asc',
			$sortby      => 'desc',
		) );
	} else {
		$query->set( 'orderby', $sortby );
		$query->set( 'order', $order );
	}
}