<?php
function pto_get_team_from_userid( $user = NULL ) {
	if ( empty($user) ) {
		$user = wp_get_current_user();
	}
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$members = get_posts($args);
	foreach ( $members as $member ) {
		$team_details = get_post_meta($member->ID, 'team_details', true);
		if ( ! empty($team_details['user_id']) && $team_details['user_id'] == $user->ID ) {
			$assigned = $member->ID;
		}
	}
	if ( ! empty($assigned) ) {
		return $assigned;
	}
	return false;
}
add_action( 'profile_update', 'pto_team_user_update_profile', 10, 2 );
function pto_team_user_update_profile( $user_id, $old_user_data ) {
	$user = get_userdata( $user_id );
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$team_members = get_posts($args);
	foreach ( $team_members as $member ) {
		$team_details = get_post_meta($member->ID, 'team_details', true);
		if ( $team_details['user_id'] == $user_id ) {
			$team_details['team_name'] = $user->display_name;
			$team_details['team_email'] = $user->user_email;
			$roles = $user->roles;          
			$role = $roles[0];          
			$team_details['team_perms'] = $role;
			update_post_meta($member->ID, 'team_details', $team_details);
		}
	}
}
function send_pto_team_email( $team_id, $password ) {
	$team_details = get_post_meta($team_id, 'team_details', true);
	$email_subject = get_option('team_account_subject');
	$email_content = get_option('team_account_email');
	$email_subject = pto_replacement_patterns($email_subject, $team_id, 'team');
	$email_content = pto_replacement_patterns($email_content, $team_id, 'team');
	$email_content = str_replace('%%TEAM_PASSWORD%%', $password, $email_content);
	$to = $team_details['team_email'];
	$attachments = array();
	pto_send_emails( $to, $email_subject, $email_content, '', $attachments, 'sales' );
}
add_action( "wp_ajax_pto_create_team_from_admin", "pto_create_team_from_admin");
function pto_create_team_from_admin() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user_id = isset( $_POST['user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
	$user = get_user_by('id', $user_id);
	if ( $user->display_name ) {
		$new_team_member = array(
			'post_type'    => 'cqpim_teams',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $user->display_name,
		);
		$args = array(
			'post_type'      => 'cqpim_teams',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'meta_key'       => 'admin',
			'meta_value'     => $user->ID,
		);
		$posts = get_posts($args);
		if ( empty($posts) ) { 
			$team_pid = wp_insert_post( $new_team_member );
		}
		if ( ! empty($team_pid) && ! is_wp_error( $team_pid ) ) {    
			update_post_meta($team_pid, 'admin', $user->ID);
			$team_details = array(
				'team_name'  => $user->display_name,
				'team_email' => $user->user_email,
				'team_perms' => 'administrator',
				'user_id'    => $user->ID,
			);
			update_post_meta($team_pid, 'team_details', $team_details);
			pto_send_json( array( 
				'error'   => false,
				'message' => '',
			) ); 
		}
	} else {
		pto_send_json( array( 
			'error'   => true,
			'message' => '',
		) );         
	}
}
function pto_create_team_from_user( $actions, $user_object ) {
	$user = get_user_by('id', $user_object->ID);
	$roles = $user->roles;
	$role = isset($roles[0]) ? $roles[0] : '';
	if ( strpos($role, 'projectopia-core') !== false ) {
		$cqpim_role = 1;
	}
	if ( ! in_array('administrator', $roles) && ! in_array('ptouploader', $roles) && empty($cqpim_role) ) {
		$actions['add_cqpim_team'] = "<a class='create_team' href='" . admin_url( "users.php?action=create_cqpim_team&amp;user=$user_object->ID") . "'>" . __( 'Convert to CQPIM Team Member', 'projectopia-core' ) . "</a>";
	}
	return $actions;
}
add_filter('user_row_actions', 'pto_create_team_from_user', 10, 2); 
add_action('current_screen', 'pto_create_team_from_user_callback');
function pto_create_team_from_user_callback() {
	$screen = get_current_screen();
	$base = isset($screen->base) ? $screen->base : '';
	$action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
	if ( $base == 'users' && $action == 'create_cqpim_team' ) {
		$user_id = isset($_GET['user']) ? sanitize_text_field(wp_unslash($_GET['user'])) : '';
		$user = get_user_by('id', $user_id);
		$new_team = array(
			'post_type'    => 'cqpim_teams',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $user->display_name,
		);
		$client_pid = wp_insert_post( $new_team, true );    
		if ( ! is_wp_error( $client_pid ) ) {
			$client_updated = array(
				'ID'        => $client_pid,
				'post_name' => $client_pid,
			);                      
			wp_update_post( $client_updated );
			$client_details = array(
				'team_name'  => $user->display_name,
				'team_email' => $user->user_email,
				'team_perms' => 'cqpim_user',
			);
			$client_details['user_id'] = $user_id;
			update_post_meta($client_pid, 'team_details', $client_details);
			$user_data = array(
				'ID'   => $user_id,
				'role' => 'cqpim_user',
			);
			wp_update_user($user_data); 
		}
	}
}
add_action( 'before_delete_post', 'pto_delete_team_user' );
function pto_delete_team_user( $post_id ) {
	global $post_type;   
	if ( $post_type != 'cqpim_teams' ) return;
	require_once(ABSPATH.'wp-admin/includes/user.php' );
	$team_details = get_post_meta($post_id, 'team_details', true);
	$wp_user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
	$user = get_user_by( 'id', $wp_user_id );
	if ( is_object( $user ) ) {
		if ( ! in_array( 'administrator', $user->roles ) ) {
			wp_delete_user( $wp_user_id );
		}
	}
}
add_action( "wp_ajax_pto_reset_password", "pto_reset_password");
function pto_reset_password() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user_id = isset($_POST['user_id']) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
	$entity_id = isset($_POST['entity_id']) ? sanitize_text_field( wp_unslash( $_POST['entity_id'] ) ) : '';
	$new_password = isset($_POST['new_password']) ? sanitize_text_field( wp_unslash( $_POST['new_password'] ) ) : '';
	$confirm_password = isset($_POST['confirm_password']) ? sanitize_text_field( wp_unslash( $_POST['confirm_password'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$send = isset($_POST['send']) ? sanitize_text_field( wp_unslash( $_POST['send'] ) ) : '';
	wp_set_password($confirm_password, $user_id);
	if ( $send == 1 ) {
		if ( $type == 'client' ) {
			$email_subject = get_option('password_reset_subject');
			$email_content = get_option('password_reset_content');
			$client_details = get_post_meta($entity_id, 'client_details', true);
			$to = $client_details['client_email'];
			$email_subject = pto_replacement_patterns($email_subject, $entity_id, 'client');
			$email_content = pto_replacement_patterns($email_content, $entity_id, 'client');
		} elseif ( $type == 'team' ) {
			$email_subject = get_option('team_reset_subject');
			$email_content = get_option('team_reset_email');
			$team_details = get_post_meta($entity_id, 'team_details', true);                
			$to = $team_details['team_email'];  
			$email_subject = pto_replacement_patterns($email_subject, $entity_id, 'team');
			$email_content = pto_replacement_patterns($email_content, $entity_id, 'team');              
		}
		$email_content = str_replace('%%NEW_PASSWORD%%', $new_password, $email_content);
		$attachments = array();
		if ( pto_send_emails($to, $email_subject, $email_content, '', $attachments, 'sales') ) {
			pto_send_json( array( 
				'error'   => false,
				'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Password was successfully reset. An email has been sent.', 'projectopia-core') . '</div>',
			) );
		} else {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('Password was successfully reset. There was an error sending the email. Check you\'ve completed the email subject and content fields in the settings.', 'projectopia-core') . '</div>',
			) );
		}
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Password was successfully reset.', 'projectopia-core') . '</div>',
	) );
}

add_action( "wp_ajax_pto_add_team_to_project", "pto_add_team_to_project");
/**
 * Function to add team members in the project.
 */
function pto_add_team_to_project() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty( $_POST['project_id'] ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Project ID is missing. Please try again.', 'projectopia-core') . '</div>',
		) );
	}

	$project_id = sanitize_text_field( wp_unslash( $_POST['project_id'] ) );
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	if ( empty( $project_contributors ) ) {
		$project_contributors = array();
	}

	if ( empty( $_POST['team_ids'] ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Please select Team Members. Please try again.', 'projectopia-core') . '</div>',
		) );
	}

	$team_ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['team_ids'] ) );
	$team_members = array();
	//Get exisitng team memebers of project.
	if ( ! empty( $project_contributors ) && is_array( $project_contributors ) ) {
		foreach ( $project_contributors as $project_contributor ) {
			if ( ! empty( $project_contributor['team_id'] ) ) {
				$team_members[] = $project_contributor['team_id'];
			}
		}
	}

	$new_member_count = 0;

	//Add all new members.
	foreach ( $team_ids as $team_id ) {
		//Check if team member is already in project.
		if ( ! in_array( $team_id, $team_members,true ) ) {
			$new_member_count++;
			$project_contributors[] = array(
				'team_id' => $team_id,
			);

			update_post_meta($project_id, 'project_contributors', $project_contributors);

			$team_details = get_post_meta($team_id, 'team_details', true);
			$current_user = wp_get_current_user();
			$project_progress = get_post_meta($project_id, 'project_progress', true);
			$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

			$project_progress[] = array(
				'update' => __('Team Member Added', 'projectopia-core') . ': ' . $team_details['team_name'],
				'date'   => time(),
				'by'     => $current_user->display_name,
			);
			update_post_meta($project_id, 'project_progress', $project_progress );
			$team_details = get_post_meta($team_id, 'team_details', true);
			pto_add_team_notification($team_id, $current_user->ID, $project_id, 'team_project');
			$to = $team_details['team_email'];
			$email_subject = get_option('team_project_subject');
			$email_content = get_option('team_project_email');
			$email_subject = pto_replacement_patterns($email_subject, $team_id, 'team');
			$email_subject = pto_replacement_patterns($email_subject, $project_id, 'project');
			$email_content = pto_replacement_patterns($email_content, $team_id, 'team');
			$email_content = pto_replacement_patterns($email_content, $project_id, 'project');
			pto_send_emails($to, $email_subject, $email_content, '', array(), 'sales');
		}
	}


	if ( $new_member_count > 0 ) {
		pto_send_json( array(
			'error'   => false,
			'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Team members were successfully added. An email has been sent.', 'projectopia-core') . '</div>',
		) );
	} else {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Team Members are already in project. Please try again.', 'projectopia-core') . '</div>',
		) );
	}
	exit();
}

add_action( "wp_ajax_pto_remove_team_member", "pto_remove_team_member");
function pto_remove_team_member() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$project_id = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$team = isset($_POST['team']) ? sanitize_text_field( wp_unslash( $_POST['team'] ) ) : '';
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	unset($project_contributors[ $key ]);
	$project_contributors = array_filter($project_contributors);
	update_post_meta($project_id, 'project_contributors', $project_contributors);
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key'       => 'owner',
		'meta_value'     => $team,
	);
	$tasks = get_posts($args);
	foreach ( $tasks as $task ) {
		delete_post_meta($task->ID, 'owner');
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '',
	) );
}
/*function pto_filter_avatar_team( $avatar, $id, $size, $default, $alt, $args ) {
    $user = get_user_by( 'id', $id );
	$team = pto_get_team_from_userid( $user );
	$team_avatar = get_post_meta( $team, 'team_avatar', true );
	if ( ! empty( $team_avatar ) ) {
		return wp_get_attachment_image( $team_avatar, [ $size, $size ], false, [
			'class' => is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'],
			'alt' => $alt
		] );
	}
	return $avatar;
}
add_filter( 'get_avatar', 'pto_filter_avatar_team', 2, 6 );*/
add_action( "wp_ajax_pto_update_team_profile", "pto_update_team_profile");
function pto_update_team_profile() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$team = pto_get_team_from_userid($user);
	if ( empty($team) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('Team Member ID Missing', 'projectopia-core'),
		) );         
	}
	if ( $type == 'personal' ) {
		$name = isset($_POST['name']) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email = isset($_POST['email']) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone = isset($_POST['phone']) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$job = isset($_POST['job']) ? sanitize_text_field( wp_unslash( $_POST['job'] ) ) : '';
		if ( empty($name) || empty($email) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('Name and Email are required', 'projectopia-core'),
			) );             
		}
		if ( ! is_email($email) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('The email address entered does not appear to be valid.', 'projectopia-core'),
			) );             
		}
		if ( email_exists($email) && $email != $user->user_email ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('The email address entered is already in use by another user, please try another one.', 'projectopia-core'),
			) );             
		}
		$team_details = get_post_meta($team, 'team_details', true);
		$team_details['team_name'] = $name;
		$team_details['team_email'] = $email;
		$team_details['team_telephone'] = $phone;
		$team_details['team_job'] = $job;
		update_post_meta($team, 'team_details', $team_details);
		$updated_post = array(
			'ID'         => $team,
			'post_title' => $name,
		);
		wp_update_post($updated_post);
		if ( $email != $user->user_email ) {
			$user_data = array(
				'ID'           => $user->ID,
				'display_name' => $name,
				'first_name'   => $name,
				'user_email'   => $email,
			);
		} else {
			$user_data = array(
				'ID'           => $user->ID,
				'display_name' => $name,
				'first_name'   => $name,
			);          
		}
		wp_update_user($user_data);
		pto_send_json( array( 
			'error'   => false,
			'message' => __('Contact details updated successfully', 'projectopia-core'),
		) ); 
	}
	if ( $type == 'photo' ) {
		$team_avatar = isset($_POST['photo']) ? sanitize_textarea_field( wp_unslash( $_POST['photo'] ) ) : '';
		if ( ! empty($team_avatar) ) {
			update_post_meta($team, 'team_avatar', $team_avatar);
			pto_send_json( array( 
				'error'   => false,
				'message' => __('Photo updated successfully', 'projectopia-core'),
			) );         
		} else {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('You have not uploaded a photo', 'projectopia-core'),
			) );         
		}   
	}   
	if ( $type == 'password' ) {       
		$pass = isset($_POST['password']) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
		$pass2 = isset($_POST['password2']) ? sanitize_text_field( wp_unslash( $_POST['password2'] ) ) : '';
		if ( empty($pass) || empty($pass2) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('You must fill in both password fields.', 'projectopia-core'),
			) );         
		} else {
			if ( $pass != $pass2 ) {
				pto_send_json( array( 
					'error'   => true,
					'message' => __('Passwords do not match.', 'projectopia-core'),
				) );         
			} else {
				if ( strlen($pass) < 8 || ! preg_match("#[0-9]+#", $pass) || ! preg_match("#[a-zA-Z]+#", $pass) ) {
					pto_send_json( array( 
						'error'   => true,
						'message' => __('Passwords should be at least 8 characters and should contain at least one letter and one number.', 'projectopia-core'),
					) );                 
				} else {
					$user = wp_get_current_user();
					if ( empty($user) ) {
						pto_send_json( array( 
							'error'   => true,
							'message' => __('Invalid User or reset link', 'projectopia-core'),
						) );                 
					} else {
						wp_set_password($pass, $user->ID);
						wp_set_auth_cookie( $user->ID, true);
						pto_send_json( array( 
							'error'   => false,
							'message' => __('Your password has been reset, you can now log in with your email address and new password.', 'projectopia-core'),
						) ); 
					}
				}
			}
		}   
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '',
	) );     
}
add_action( "wp_ajax_pto_remove_current_photo", "pto_remove_current_photo");
function pto_remove_current_photo() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	update_post_meta($team, 'team_avatar', '');
	pto_send_json( array( 
		'error'   => false,
		'message' => __('The photo was removed successfully', 'projectopia-core'),
	) );
}
function pto_is_team_on_project( $project_id, $team_id ) {
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	if ( ! empty( $project_contributors ) && is_array( $project_contributors ) ) {
		foreach ( $project_contributors as $contrib ) {
			if ( $contrib['team_id'] == $team_id ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Action to call after create new user.
 */
add_action( 'user_register', 'pto_create_team_member_from_users', 10, 1 );

/**
 * Function to convert the users to pto-CQPIM team member.
 *
 * @param int $user_id User ID.
 */
function pto_create_team_member_from_users( $user_id ) {

	if ( empty( $user_id ) ) {
		return;
	}

	$user = get_user_by( 'id', $user_id );

	if ( empty( $user ) || empty( $user->roles ) ) {
		return;
	}

	if ( in_array( 'cqpim_user', $user->roles , true ) ) {

		$new_team_member = array(
			'post_type'    => 'cqpim_teams',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $user->display_name,
		);

		$team_member_pid = wp_insert_post( $new_team_member, true );

		if ( ! empty( $team_member_pid ) ) {

			$team_member_updated = array(
				'ID'        => $team_member_pid,
				'post_name' => $team_member_pid,
			);

			wp_update_post( $team_member_updated );

			$team_member_details = array(
				'team_name'  => $user->display_name,
				'team_email' => $user->user_email,
				'team_perms' => 'cqpim_user',
				'user_id'    => $user_id,
			);

			update_post_meta( $team_member_pid, 'team_details', $team_member_details);
		}
	}
}

