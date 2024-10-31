<?php
if ( ! function_exists('pto_teams_cpt') ) {
	function pto_teams_cpt() {
		if ( current_user_can('cqpim_create_new_team') && current_user_can('publish_cqpim_teams') ) {
			$team_caps = array();
		} else {
			$team_caps = array( 'create_posts' => false );
		}
		$labels = array(
			'name'               => _x( 'Team Members', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Team Member', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Team Members', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Team Member:', 'projectopia-core' ),
			'all_items'          => __( 'Team Members', 'projectopia-core' ),
			'view_item'          => __( 'View Team Member', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Team Member', 'projectopia-core' ),
			'add_new'            => __( 'New Team Member', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Team Member', 'projectopia-core' ),
			'update_item'        => __( 'Update Team Member', 'projectopia-core' ),
			'search_items'       => __( 'Search Team Members', 'projectopia-core' ),
			'not_found'          => __( 'No Team Members found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No Team Members found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'team', 'projectopia-core' ),
			'description'         => __( 'Team Members', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $team_caps,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => 'cqpim-dashboard', 
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array( 'cqpim_team', 'cqpim_teams' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
		);
		register_post_type( 'cqpim_teams', $args );
	}
	add_action( 'init', 'pto_teams_cpt', 15 );
}
if ( ! function_exists( 'pto_teams_cpt_custom_columns' ) ) {
	function pto_teams_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title'           => __('Title', 'projectopia-core'),
			'user_account'    => __('Associated User', 'projectopia-core'),
			'contact_details' => __('Contact Details', 'projectopia-core'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_teams_posts_columns' , 'pto_teams_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_teams_posts_custom_column', 'content_pto_teams_cpt_columns', 10, 2 );
function content_pto_teams_cpt_columns( $column, $post_id ) {
	global $post;
	$team_details = get_post_meta( $post_id, 'team_details', true );
	switch ( $column ) {
		case 'user_account':
			$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
			$user_object = get_user_by('id', $user_id);
			$user_edit_link = get_edit_user_link( $user_id );
			$roles = $user_object->roles;
			$role_prefix = apply_filters( 'pto/role_prefix_display', 'PTO ' );
			$role_names = [];
            foreach ( $roles as $role_name ) {
                if ( 'administrator' === $role_name ) {
                    $role_names[] = translate_user_role( 'Administrator' );
                }
                if ( strpos( $role_name, 'cqpim' ) !== false ) {
					$new_role_name = str_replace( '_', ' ', $role_name );
                    $role_names[] = ucwords( str_replace( 'cqpim', $role_prefix, $new_role_name ) );
                }
            }
            $role_name = ! empty( $role_names ) ? join( ', ', $role_names ) : __( 'No Role', 'projectopia-core' );
			$avatar = get_option('cqpim_disable_avatars');  
			?>
			<div style="text-align:center;float:left; padding-right:10px;">
				<?php if ( empty($avatar) ) { ?>
					<div class="cqpim_avatar">
						<?php echo get_avatar( $user_id, 57, '', false, array( 'force_display' => true ) ); ?>
					</div>
				<?php } ?>
			</div>
			<?php
			echo '<strong>' . esc_html__('User ID:', 'projectopia-core') . ' </strong>' . esc_html( $user_object->ID ) . '<br />';
			echo '<strong>' . esc_html__('Login:', 'projectopia-core') . ' </strong>' . esc_html( $user_object->user_email ) . '<br />';
			echo '<strong>' . esc_html__('Permission Level:', 'projectopia-core') . ' </strong>' . esc_html( $role_name );
			if ( empty( $user_id ) ) {
				echo '<div class="cqpim-alert cqpim-alert-danger">' . esc_html__('This Team Member does not have a user account linked to it and will not work correctly. Update this Team Member to find out why', 'projectopia-core') . '</div>';
			}           
		    break;
		case 'contact_details':
			$company_contact = isset($team_details['team_name']) ? $team_details['team_name'] : '';
			$company_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
			$company_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
			echo '<strong>' . esc_html__('Contact Name:', 'projectopia-core') . ' </strong>' . esc_html( $company_contact ) . '<br />';
			echo '<strong>' . esc_html__('Email:', 'projectopia-core') . ' </strong>' . esc_html( $company_email ) . '<br />';
			echo '<strong>' . esc_html__('Telephone:', 'projectopia-core') . ' </strong>' . esc_html( $company_telephone ) . '<br />';
		    break;
		default:
			break;
	}
}
function pto_post_classes( $classes ) {
	if ( is_admin() ) {
		global $post;
		if ( $post->post_type == 'cqpim_project' ) {
			$user = wp_get_current_user();
			$args = array(
				'post_type'      => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status'    => 'private',
			);
			$members = get_posts($args);
			foreach ( $members as $member ) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if ( $team_details['user_id'] == $user->ID ) {
					$assigned = $member->ID;
				}
			}
			if ( ! current_user_can('cqpim_view_all_projects') ) {
				$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
				if ( empty($project_contributors) ) {
					$project_contributors = array();
				}
				foreach ( $project_contributors as $contributor ) {
					if ( $assigned == $contributor['team_id'] ) {
						$access = true;
					}
				}
				if ( empty($access) ) {
					$classes[] = 'no_access';
				} else {
					$classes[] = 'can_access';
				}
			} else {
				$classes[] = 'can_access';
			}
		}
	}
	return $classes;
}
if ( is_admin() ) {
	add_filter('post_class', 'pto_post_classes'); 
}