<?php
function pto_get_client_from_userid( $user = NULL ) {
	if ( empty($user) ) {
		$user = wp_get_current_user();
	}
	$args = array(
		'post_type'      => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$members = get_posts($args);
	foreach ( $members as $member ) {
		$team_details = get_post_meta($member->ID, 'client_details', true);
		if ( ! empty($team_details['user_id']) && $team_details['user_id'] == $user->ID ) {
			$assigned = $member->ID;
			$client_type = 'admin';
		}
	} 
	if ( empty($assigned) ) {
		foreach ( $members as $member ) {
			$team_ids = get_post_meta($member->ID, 'client_ids', true);
			if ( ! is_array($team_ids) ) {
				$team_ids = array( $team_ids );
			}
			if ( in_array($user->ID, $team_ids) ) {
				$assigned = $member->ID;
				$client_type = 'contact';
			}
		}           
	}
	if ( ! empty($assigned) ) {
		return array(
			'assigned' => $assigned,
			'type'     => $client_type,
		);
	}
	return false;
}

add_action( 'profile_update', 'pto_client_user_update_profile', 10, 2 );
function pto_client_user_update_profile( $user_id, $old_user_data ) {
	$user = get_userdata( $user_id );
	$args = array(
		'post_type'      => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$clients = get_posts($args);
	foreach ( $clients as $client ) {
		$team_details = get_post_meta($client->ID, 'client_details', true);
		if ( ! empty($team_details['user_id']) && $team_details['user_id'] == $user_id ) {
			$team_details['client_contact'] = $user->display_name;
			$team_details['client_email'] = $user->user_email;
			update_post_meta($client->ID, 'client_details', $team_details);
		}
	}
}

function send_pto_welcome_email( $client_id, $password ) {
	$client_details = get_post_meta($client_id, 'client_details', true);
	$email_subject = get_option('auto_welcome_subject');
	$email_content = get_option('auto_welcome_content');
	$email_subject = pto_replacement_patterns($email_subject, $client_id, '');
	$email_content = pto_replacement_patterns($email_content, $client_id, '');
	$email_content = str_replace('%%CLIENT_PASSWORD%%', $password, $email_content);
	$to = $client_details['client_email'];
	$attachments = array();
	pto_send_emails( $to, $email_subject, $email_content, '', $attachments, 'sales' );
}

function pto_create_client_from_user( $actions, $user_object ) {
	$user = get_user_by('id', $user_object->ID);
	$roles = $user->roles;
	$role = isset($roles[0]) ? $roles[0] : '';
	if ( strpos($role, 'projectopia-core') !== false ) {
		$cqpim_role = 1;
	}
	if ( ! in_array('administrator', $roles) && ! in_array('ptouploader', $roles) && empty($cqpim_role) ) {
		$actions['add_cqpim_client'] = "<a class='create_client' href='" . admin_url( "users.php?action=create_cqpim_client&amp;user=$user_object->ID") . "'>" . __( 'Convert to PTO Client', 'projectopia-core' ) . "</a>";
	}
	return $actions;
}

add_filter('user_row_actions', 'pto_create_client_from_user', 10, 2);
add_action('current_screen', 'pto_create_client_from_user_callback');
function pto_create_client_from_user_callback() {
	$screen = get_current_screen();
	$base = isset($screen->base) ? $screen->base : '';
	$action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';
	if ( $base == 'users' && $action == 'create_cqpim_client' ) {
		$user_id = isset($_GET['user']) ? sanitize_text_field(wp_unslash($_GET['user'])) : '';
		$user = get_user_by('id', $user_id);
		$new_client = array(
			'post_type'    => 'cqpim_client',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $user->display_name,
		);
		$client_pid = wp_insert_post( $new_client, true );  
		if ( ! is_wp_error( $client_pid ) ) {
			$client_updated = array(
				'ID'        => $client_pid,
				'post_name' => $client_pid,
			);                      
			wp_update_post( $client_updated );
			$client_details = array(
				'client_ref'     => $client_pid,
				'client_contact' => $user->display_name,
				'client_company' => $user->display_name,
				'client_email'   => $user->user_email,
			);
			$client_details['user_id'] = $user_id;
			$client_ids = array();
			$client_ids[] = $user_id;
			update_post_meta($client_pid, 'client_details', $client_details);
			update_post_meta($client_pid, 'client_ids', $client_ids);   
			$user_data = array(
				'ID'   => $user_id,
				'role' => 'cqpim_client',
			);
			wp_update_user($user_data); 
		}
	}
}

/**
 * Action to call after create new user.
 */
add_action( 'user_register', 'pto_create_user_as_client_from_user_page', 10, 1 );

/**
 * Function to convert the new user as client.
 *
 * @param int $user_id User ID.
 */
function pto_create_user_as_client_from_user_page( $user_id ) {

	if ( empty( $user_id ) ) {
		return;
	}

	$new_user = get_user_by( 'id', $user_id );

	if ( empty( $new_user ) || empty( $new_user->roles ) ) {
		return;
	}

	if ( in_array( 'cqpim_client', $new_user->roles , true ) ) {
		$new_client = array(
			'post_type'    => 'cqpim_client',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $new_user->display_name,
		);

		$client_id = wp_insert_post( $new_client, true );

		if ( ! empty( $client_id ) ) {

			$client_updated = array(
				'ID'        => $client_id,
				'post_name' => $client_id,
			);

			wp_update_post( $client_updated );

			$client_details = array(
				'client_ref'     => $client_id,
				'client_contact' => $new_user->display_name,
				'client_company' => $new_user->display_name,
				'client_email'   => $new_user->user_email,
			);

			$client_details['user_id'] = $user_id;
			update_post_meta( $client_id, 'client_details', $client_details );
			update_post_meta( $client_id, 'client_ids', array( $user_id ) );
		}
	}
}

add_filter( 'post_row_actions', 'pto_edit_client_trash_action', 99, 2 );
function pto_edit_client_trash_action( $actions, $post ) {
	if ( 'cqpim_client' === $post->post_type && current_user_can( 'delete_cqpim_clients' ) ) {
		$actions['trash'] = '<a class="delete_client" data-id="' . esc_attr( $post->ID ) . '" href="#">' . esc_html__( 'Delete Client' , 'projectopia-core' ) . '</a>'; ?>
			<div id="delete_client_warning_container_<?php echo esc_attr( $post->ID ); ?>" style="display: none;">
				<div id="delete_client_warning_<?php echo esc_attr( $post->ID ); ?>" class="contact_edit">
					<div style="padding: 12px;max-width: 600px;">
						<h3><?php esc_html_e( 'Delete Client Warning', 'projectopia-core' ); ?></h3>
						<p><?php esc_html_e( 'Deleting this client will also delete the associated user account and the user account of all related contacts.', 'projectopia-core' ); ?></p>						
						<p><?php esc_html_e( 'If this is not desired then you should first unlink the user account from the client.', 'projectopia-core' ); ?></p>
						<button class="piaBtn mt-3 uldc" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Unlink User and Delete Client', 'projectopia-core' ); ?></button>
						<button class="piaBtn right mt-3 dcu" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Delete Client and User', 'projectopia-core' ); ?></button>
						<div id="client_messages_<?php echo esc_attr( $post->ID ); ?>"></div>
						<div id="client_spinner_<?php echo esc_attr( $post->ID ); ?>" class="ajax_spinner" style="display: none;"></div>
					</div>
				</div>
			</div>
		<?php
	}
	return $actions;
}

add_action( 'wp_ajax_pto_unlink_delete_client', 'pto_unlink_delete_client' );
function pto_unlink_delete_client() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$client_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
	if ( empty( $client_id ) ) {
		$return = array( 
			'error'   => true,
			'message' => '<span class="task_over">' . __( 'The Client ID is missing. The client could not be deleted', 'projectopia-core' ) . '</span>',
		);
	} else {
		$client_details = get_post_meta( $client_id, 'client_details', true );
		unset( $client_details['user_id'] );
		update_post_meta( $client_id, 'client_details', $client_details );
		wp_delete_post( $client_id, true );
		$return = array( 
			'error'   => false,
			'message' => '<span class="task_complete">' . __( 'The user was successfully unlinked and the client was deleted', 'projectopia-core' ) . '</span>',
		);
	}
	pto_send_json( $return );
}

add_action( 'wp_ajax_pto_delete_client_user_confirm', 'pto_delete_client_user_confirm' );
function pto_delete_client_user_confirm() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$client_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
	if ( empty( $client_id ) ) {
		$return = array( 
			'error'   => true,
			'message' => '<span class="task_over">' . esc_html__('The Client ID is missing. The client could not be deleted', 'projectopia-core') . '</span>',
		);
	} else {
		$client_details = get_post_meta( $client_id, 'client_details', true );
		$client_contacts = get_post_meta( $client_id, 'client_contacts', true );
		
		$user_id = isset( $client_details['user_id'] ) ? $client_details['user_id'] : '';
		if ( ! empty( $user_id ) ) {
			wp_delete_user( $user_id );
		}
		foreach ( $client_contacts as $key => $contact ) {
			wp_delete_user( intval( $key ) );
		}
		wp_delete_post( $client_id, true );
		$return = array( 
			'error'   => false,
			'message' => '<span class="task_complete">' . __( 'The user and the client was deleted successfully', 'projectopia-core' ) . '</span>',
		);
	}
	pto_send_json( $return );
}

add_action( "wp_ajax_nopriv_pto_client_add_contact", "pto_client_add_contact" );
add_action( "wp_ajax_pto_client_add_contact", "pto_client_add_contact" );
function pto_client_add_contact() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$client_id = isset($_POST['entity_id']) ? sanitize_text_field(wp_unslash($_POST['entity_id'])) : '';
	$contact_name = isset($_POST['contact_name']) ? sanitize_text_field(wp_unslash($_POST['contact_name'])) : '';
	$contact_telephone = isset($_POST['contact_telephone']) ? sanitize_text_field(wp_unslash($_POST['contact_telephone'])) : '';
	$contact_email = isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '';
	$send = isset($_POST['send']) ? sanitize_text_field(wp_unslash($_POST['send'])) : '';
	if ( empty($client_id) || empty($contact_name) || empty($contact_telephone) || empty($contact_email) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Please complete all fields.', 'projectopia-core') . '</div>',
		) );         
	} else {
		$email = email_exists($contact_email);
		$username = username_exists($contact_email);
		if ( ! empty($email) || ! empty($username) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The email address entered is already in the system, please try another.', 'projectopia-core') . '</div>',
			) );             
		} else {
			$password = pto_random_string(10);
			remove_action( 'user_register', 'pto_create_user_as_client_from_user_page', 10, 1);
			$user_id = wp_create_user( $contact_email, $password, $contact_email );
			$user = new WP_User( $user_id );
			$user->set_role( 'cqpim_client' );
			$user_data = array(
				'ID'           => $user_id,
				'display_name' => $contact_name,
				'first_name'   => $contact_name,
			);
			wp_update_user($user_data);
			$contacts = get_post_meta($client_id, 'client_contacts', true);
			$contacts = $contacts && is_array($contacts) ? $contacts : array();
			$contacts[ $user->ID ] = array(
				'user_id'   => $user->ID,
				'name'      => $contact_name,
				'email'     => $contact_email,
				'telephone' => $contact_telephone,
			);
			update_post_meta($client_id, 'client_contacts', $contacts);
			$ids = get_post_meta($client_id, 'client_ids', true);
			$ids = $ids && is_array($ids) ? $ids : array();
			$ids[] = $user_id;
			update_post_meta($client_id, 'client_ids', $ids);
			if ( $send == 1 ) {                            
				$email_subject = get_option('added_contact_subject');
				$email_content = get_option('added_contact_content');
				$email_subject = pto_replacement_patterns($email_subject, $client_id, '');
				$email_content = pto_replacement_patterns($email_content, $client_id, '');
				$email_content = str_replace('%%CONTACT_NAME%%', $contact_name, $email_content);
				$email_content = str_replace('%%CONTACT_EMAIL%%', $contact_email, $email_content);
				$email_content = str_replace('%%CONTACT_PASSWORD%%', $password, $email_content);
				$to = $contact_email;
				$attachments = array();
				if ( pto_send_emails( $to, $email_subject, $email_content, '', $attachments, 'sales' ) ) {
					pto_send_json( array( 
						'error'   => false,
						'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Contact has been created. An email has been sent to the contact.', 'projectopia-core') . '</div>',
					) ); 
				} else {
					pto_send_json( array( 
						'error'   => true,
						'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('The contact was added but the email failed to send. Check you have completed the email subject and content fields in the plugin settings.', 'projectopia-core') . '</div>',
					) );                     
				}
			} else {
				pto_send_json( array( 
					'error'   => false,
					'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Contact has been created. No email has been sent.', 'projectopia-core') . '</div>',
				) ); 
			}
		}
	}
}

add_action( "wp_ajax_nopriv_pto_remove_client_contact", "pto_remove_client_contact" );
add_action( "wp_ajax_pto_remove_client_contact", "pto_remove_client_contact" );
function pto_remove_client_contact() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$key = isset($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : '';
	$client_id = isset($_POST['project_id']) ? sanitize_text_field(wp_unslash($_POST['project_id'])) : '';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$user_id = $client_contacts[ $key ]['user_id'];
	wp_delete_user($user_id);
	if ( ! empty($client_contacts[ $key ]) ) {
		unset($client_contacts[ $key ]);
	}
	$client_contacts = array_filter($client_contacts);
	update_post_meta($client_id, 'client_contacts', $client_contacts);
	$client_ids = get_post_meta($client_id, 'client_ids', true);
	if ( ! is_array($client_ids) ) {
		$client_ids = array( $client_ids );
	}
	foreach ( $client_ids as $key => $client_id_ind ) {
		if ( $client_id_ind == $user_id ) {
			unset($client_ids[ $key ]);
		}
	}
	update_post_meta($client_id, 'client_ids', $client_ids);
	pto_send_json( array( 
		'error'   => false,
		'message' => '',
	) ); 
}

add_action( "wp_ajax_nopriv_pto_edit_client_contact", "pto_edit_client_contact" );
add_action( "wp_ajax_pto_edit_client_contact", "pto_edit_client_contact" );
function pto_edit_client_contact() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$client_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : '';
	$key = isset($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : '';
	$admin = isset($_POST['admin']) ? sanitize_text_field(wp_unslash($_POST['admin'])) : '';
	$contact_name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
	$contact_telephone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
	$contact_email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
	$password = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
	$password2 = isset($_POST['password2']) ? sanitize_text_field(wp_unslash($_POST['password2'])) : '';
	$send = isset($_POST['send']) ? sanitize_text_field(wp_unslash($_POST['send'])) : '';
	$no_tasks = isset($_POST['no_tasks']) ? sanitize_text_field(wp_unslash($_POST['no_tasks'])) : 0;
	$no_tasks_comment = isset($_POST['no_tasks_comment']) ? sanitize_textarea_field(wp_unslash($_POST['no_tasks_comment'])) : 0;
	$no_tickets = isset($_POST['no_tickets']) ? sanitize_text_field(wp_unslash($_POST['no_tickets'])) : 0;
	$no_tickets_comment = isset($_POST['no_tickets_comment']) ? sanitize_textarea_field(wp_unslash($_POST['no_tickets_comment'])) : 0;
	$no_bugs = isset($_POST['no_bugs']) ? sanitize_text_field(wp_unslash($_POST['no_bugs'])) : 0;
	$no_bugs_comment = isset($_POST['no_bugs_comment']) ? sanitize_textarea_field(wp_unslash($_POST['no_bugs_comment'])) : 0;
	if ( empty($contact_name) || empty($contact_telephone) || empty($contact_email) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Missing Data. Please ensure you complete all fields.', 'projectopia-core') . '</div>',
		) );         
	} else {
		$contacts = get_post_meta($client_id, 'client_contacts', true);
		$user_id = $contacts[ $key ]['user_id'];
		$email = email_exists($contact_email);
		$username = username_exists($contact_email);
		if ( ! empty($email) && $email != $user_id || ! empty($username) && $username != $user_id ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The email address entered is already in the system, please try another.', 'projectopia-core') . '</div>',
			) );             
		} else {
			if ( ! empty($password) || ! empty($password2) ) {
				if ( $password != $password2 ) {
					pto_send_json( array( 
						'error'   => true,
						'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The passwords do not match.', 'projectopia-core') . '</div>',
					) );             
				} else {
					wp_set_password($password2, $user_id);
					if ( empty($admin) ) {
						wp_set_auth_cookie( $user_id, '', '' );
					}
					if ( $send == 1 ) {
						$email_subject = get_option('password_reset_subject');
						$email_content = get_option('password_reset_content');
						$email_content = str_replace('%%CLIENT_NAME%%', $contact_name, $email_content);
						$email_content = str_replace('%%CLIENT_EMAIL%%', $contact_email, $email_content);
						$email_subject = pto_replacement_patterns($email_subject, $entity_id, '');
						$email_content = pto_replacement_patterns($email_content, $entity_id, '');
						$to = $contact_email;
						$email_content = str_replace('%%NEW_PASSWORD%%', $password2, $email_content);
						$attachments = array();
						pto_send_emails($to, $email_subject, $email_content, '', $attachments, 'sales');                        
					}
				}
			}
			$contacts[ $key ] = array(
				'user_id'   => $user_id,
				'name'      => $contact_name,
				'email'     => $contact_email,
				'telephone' => $contact_telephone,           
			);
			$contacts[ $key ]['notifications'] = array(
				'no_tasks'           => $no_tasks,
				'no_tasks_comment'   => $no_tasks_comment,
				'no_tickets'         => $no_tickets,
				'no_tickets_comment' => $no_tickets_comment,
				'no_bugs'            => $no_bugs,
				'no_bugs_comment'    => $no_bugs_comment,
			);  
			update_post_meta($client_id, 'client_contacts', $contacts);
			$user_details = array(
				'ID'           => $user_id,
				'display_name' => $contact_name,
				'first_name'   => $contact_name,
				'user_email'   => $contact_email,             
			);
			wp_update_user($user_details);
			pto_send_json( array( 
				'error'   => false,
				'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Update Successful.', 'projectopia-core') . '</div>',
			) ); 
		}
	}
}

add_action( "wp_ajax_nopriv_pto_client_update_details", "pto_client_update_details" );
add_action( "wp_ajax_pto_client_update_details", "pto_client_update_details" );
function pto_client_update_details() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user_id = isset($_POST['user_id']) ? sanitize_text_field(wp_unslash($_POST['user_id'])) : '';
	$client_object = isset($_POST['client_object']) ? sanitize_text_field(wp_unslash($_POST['client_object'])) : '';
	$client_type = isset($_POST['client_type']) ? sanitize_text_field(wp_unslash($_POST['client_type'])) : '';
	$client_email = isset($_POST['client_email']) ? sanitize_email(wp_unslash($_POST['client_email'])) : '';
	$client_phone = isset($_POST['client_phone']) ? sanitize_text_field(wp_unslash($_POST['client_phone'])) : '';
	$client_name = isset($_POST['client_name']) ? sanitize_text_field(wp_unslash($_POST['client_name'])) : '';
	$company_name = isset($_POST['company_name']) ? sanitize_text_field(wp_unslash($_POST['company_name'])) : '';
	$company_address = isset($_POST['company_address']) ? sanitize_text_field(wp_unslash($_POST['company_address'])) : '';
	$company_postcode = isset($_POST['company_postcode']) ? sanitize_text_field(wp_unslash($_POST['company_postcode'])) : '';
	$client_pass = isset($_POST['client_pass']) ? sanitize_text_field(wp_unslash($_POST['client_pass'])) : '';
	$client_pass_rep = isset($_POST['client_pass_rep']) ? sanitize_text_field(wp_unslash($_POST['client_pass_rep'])) : '';
	$photo = isset($_POST['photo']) ? sanitize_text_field(wp_unslash($_POST['photo'])) : '';
	$no_tasks = isset($_POST['no_tasks']) ? sanitize_text_field(wp_unslash($_POST['no_tasks'])) : 0;
	$no_tasks_comment = isset($_POST['no_tasks_comment']) ? sanitize_textarea_field(wp_unslash($_POST['no_tasks_comment'])) : 0;
	$no_tickets = isset($_POST['no_tickets']) ? sanitize_text_field(wp_unslash($_POST['no_tickets'])) : 0;
	$no_tickets_comment = isset($_POST['no_tickets_comment']) ? sanitize_textarea_field(wp_unslash($_POST['no_tickets_comment'])) : 0;
	$no_bugs = isset($_POST['no_bugs']) ? sanitize_text_field(wp_unslash($_POST['no_bugs'])) : 0;
	$no_bugs_comment = isset($_POST['no_bugs_comment']) ? sanitize_textarea_field(wp_unslash($_POST['no_bugs_comment'])) : 0;
	$custom_fields = get_option('cqpim_custom_fields_client');  
	$custom_fields = json_decode($custom_fields);
	$custom = isset($_POST['custom']) ? pto_sanitize_rec_array( wp_unslash( $_POST['custom'] ) ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	foreach ( $custom_fields as $custom_field ) {
		if ( empty($custom[ $custom_field->name ]) && ! empty($custom_field->required) ) {
			pto_send_json( array( 
				'error'        => true,
				'message'      => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Please complete all required fields', 'projectopia-core') . '</div>',
				'custom_field' => $custom_field,
				'custom'       => $custom,
			) );                     
		}
	}
	update_post_meta($client_object, 'custom_fields', $custom);
	if ( empty($user_id) || empty($client_object) || empty($client_type) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There is some missing data. The update has failed.', 'projectopia-core') . '</div>',
		) );     
	} else {
		$email = email_exists($client_email);
		$username = username_exists($client_email);
		if ( ! empty($email) && $email != $user_id || ! empty($username) && $username != $user_id ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The email address entered is already in the system, please try another.', 'projectopia-core') . '</div>',
			) );         
		} else {
			if ( ! empty($client_pass) || ! empty($client_pass_rep) ) {
				if ( $client_pass != $client_pass_rep ) {
					pto_send_json( array( 
						'error'   => true,
						'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The Passwords do not match. Please try again.', 'projectopia-core') . '</div>',
					) );                     
				} else {
					$user = wp_get_current_user();
					if ( $user->ID != $user_id ) {
						pto_send_json( array( 
							'error'   => true,
							'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Cheatin\' uh? Better luck next time.', 'projectopia-core') . '</div>',
						) );                         
					} else {
						wp_set_password( $client_pass, $user_id );
						wp_set_auth_cookie( $user_id, '', '' );
					}
				}
			}
			$user_data = array(
				'ID'           => $user_id,
				'display_name' => $client_name,
				'first_name'   => $client_name,
				'user_email'   => $client_email,
			);
			wp_update_user($user_data); 
			$client_notifications = array(
				'no_tasks'           => $no_tasks,
				'no_tasks_comment'   => $no_tasks_comment,
				'no_tickets'         => $no_tickets,
				'no_tickets_comment' => $no_tickets_comment,
				'no_bugs'            => $no_bugs,
				'no_bugs_comment'    => $no_bugs_comment,
			);  
			if ( $client_type == 'admin' ) {
				$client_details = get_post_meta($client_object, 'client_details', true);
				$client_details['client_contact'] = $client_name;
				$client_details['client_telephone'] = $client_phone;
				$client_details['client_email'] = $client_email;
				$client_details['client_company'] = $company_name;
				$client_details['client_address'] = $company_address;
				$client_details['client_postcode'] = $company_postcode;
				update_post_meta($client_object, 'client_details', $client_details);
				$client_updated = array(
					'ID'         => $client_object,
					'post_title' => $company_name,
				);
				wp_update_post($client_updated);                
				update_post_meta($client_object, 'client_notifications', $client_notifications);
				if ( ! empty($photo) ) {
					update_post_meta($client_object, 'team_avatar', $photo);
				}
			} else {
				$user = wp_get_current_user();
				$client_contacts = get_post_meta($client_object, 'client_contacts', true);
				$client_contacts[ $user->ID ]['telephone'] = $client_phone;
				$client_contacts[ $user->ID ]['name'] = $client_name;
				$client_contacts[ $user->ID ]['email'] = $client_email;
				$client_contacts[ $user->ID ]['notifications'] = $client_notifications;
				if ( ! empty($photo) ) {
					$client_contacts[ $user->ID ]['team_avatar'] = $photo;
				}
				update_post_meta($client_object, 'client_contacts', $client_contacts);
			}
			pto_send_json( array( 
				'error'   => false,
				'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Details successfully updated.', 'projectopia-core') . '</div>',
			) ); 
		}
	}           
}

add_action( "wp_ajax_pto_client_add_alert", "pto_client_add_alert" );
function pto_client_add_alert() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$post_id         = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
	$level           = isset($_POST['alert_level']) ? sanitize_text_field(wp_unslash($_POST['alert_level'])) : '';
	$message         = isset($_POST['alert_message']) ? sanitize_textarea_field(wp_unslash($_POST['alert_message'])) : '';
	$global          = isset($_POST['global']) ? sanitize_text_field(wp_unslash($_POST['global'])) : '';
	$is_sms_allow    = isset($_POST['is_sms_allow']) ? sanitize_text_field(wp_unslash($_POST['is_sms_allow'])) : '';
	$client_post_ids = $post_id;

	if ( empty( $message ) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('You must choose a level and add a message.', 'projectopia-core') . '</div>',
		) );     
	}

	if ( empty( $global ) ) {
		$custom_alerts = get_post_meta($post_id, 'custom_alerts', true);
		if ( empty($custom_alerts) ) {
			$custom_alerts = array();
		}
		$custom_alerts[] = array(
			'level'   => $level,
			'message' => $message,
			'seen'    => '',
			'cleared' => '',
			'global'  => 0,
		);
		update_post_meta($post_id, 'custom_alerts', $custom_alerts);
	} else {
		$client_post_ids = array();
		$args = array(
			'post_type'      => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$clients = get_posts($args);
		$digits = 5;
		$gid = str_pad(wp_rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
		foreach ( $clients as $client ) {
			$custom_alerts = get_post_meta($client->ID, 'custom_alerts', true);
			$client_post_ids[] = $client->ID;
			if ( empty($custom_alerts) ) {
				$custom_alerts = array();
			}
			$custom_alerts[ 'G-' . $gid ] = array(
				'level'   => $level,
				'message' => $message,
				'seen'    => '',
				'cleared' => '',
				'global'  => 1,
			);
			update_post_meta($client->ID, 'custom_alerts', $custom_alerts);     
		}       
	}

	/**
	 * Create action hook to perform other task on this.
	 * @since 4.3.5
	 * @param array  $client_post_ids List of client post ID's.
	 * @param string $message         Actual message string.
	 * @param string $level           Alert level.
	 * @param int    $global          Is for global or not.
	 * @param int    $is_sms_allow    Is allow for sms or not.
	 */
	do_action( 'pto_client_add_alert', $client_post_ids, $message, $level, $global, $is_sms_allow );

	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Alert Added Successfully', 'projectopia-core') . '</div>',
	) );     
}

add_action( "wp_ajax_pto_client_edit_alert", "pto_client_edit_alert" );
function pto_client_edit_alert() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$post_id = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
	$level = isset($_POST['alert_level']) ? sanitize_text_field(wp_unslash($_POST['alert_level'])) : '';
	$message = isset($_POST['alert_message']) ? sanitize_textarea_field(wp_unslash($_POST['alert_message'])) : '';
	$key = isset($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : '';
	if ( empty($level) || empty($message) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('You must choose a level and add a message.', 'projectopia-core') . '</div>',
		) );     
	}
	$custom_alerts = get_post_meta($post_id, 'custom_alerts', true);
	if ( empty($custom_alerts[ $key ]['global']) ) {
		$custom_alerts[ $key ]['level'] = $level;
		$custom_alerts[ $key ]['message'] = $message;
		update_post_meta($post_id, 'custom_alerts', $custom_alerts);                
	} else {        
		$args = array(
			'post_type'      => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$clients = get_posts($args);
		foreach ( $clients as $client ) {
			$custom_alerts = get_post_meta($client->ID, 'custom_alerts', true);
			$custom_alerts[ $key ]['level'] = $level;
			$custom_alerts[ $key ]['message'] = $message;         
			update_post_meta($client->ID, 'custom_alerts', $custom_alerts);     
		}       
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Alert Edited Successfully', 'projectopia-core') . '</div>',
	) );     
}

add_action( "wp_ajax_pto_client_delete_alert", "pto_client_delete_alert" );
function pto_client_delete_alert() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$post_id = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
	$key = isset($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : '';
	$global = isset($_POST['global']) ? sanitize_text_field(wp_unslash($_POST['global'])) : '';
	if ( empty($global) ) {
		$custom_alerts = get_post_meta($post_id, 'custom_alerts', true);
		unset($custom_alerts[ $key ]);
		update_post_meta($post_id, 'custom_alerts', $custom_alerts);
	} else {
		$args = array(
			'post_type'      => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$clients = get_posts($args);
		foreach ( $clients as $client ) {
			$custom_alerts = get_post_meta($client->ID, 'custom_alerts', true);
			if ( ! empty($custom_alerts[ $key ]) ) {
				unset($custom_alerts[ $key ]);
			}
			update_post_meta($client->ID, 'custom_alerts', $custom_alerts);     
		}       
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Alert Deleted Successfully', 'projectopia-core') . '</div>',
	) );     
}

function pto_get_alert_names() {
	$alerts = array(
		'info'    => __('Notice', 'projectopia-core'),
		'success' => __('Success', 'projectopia-core'),
		'warning' => __('Warning', 'projectopia-core'),
		'danger'  => __('Error', 'projectopia-core'),
	);
	return $alerts;
}
/*function pto_filter_avatar_client( $avatar, $id, $size, $default, $alt, $args ) {
    $user = get_user_by( 'id', $id );
	$team = pto_get_client_from_userid( $user );
	if ( empty( $team ) || empty( $team['assigned'] ) ) {
		return $avatar;
	}

	$assigned = $team['assigned'];
	$new_avatar = '';
	if ( ! empty( $team['type'] ) && 'admin' === $team['type'] ) {
		$team_avatar = get_post_meta( $assigned, 'team_avatar', true );
		if ( ! empty( $team_avatar ) ) {
			$new_avatar = wp_get_attachment_image( $team_avatar, [ $size, $size ], false, [
				'class' => is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'],
				'alt' => $alt
			] );
		}
	} else {
		$client_contacts = get_post_meta( $assigned, 'client_contacts', true );
		$client_contacts = $client_contacts && is_array( $client_contacts ) ? $client_contacts : array();
		foreach ( $client_contacts as $key => $contact ) {
			if ( $key == $user->ID && ! empty( $contact['team_avatar'] ) ) {
				$team_avatar = $contact['team_avatar'];
			}
		}
		if ( ! empty( $team_avatar ) ) {
			$new_avatar = wp_get_attachment_image( $team_avatar, [ $size, $size ], false, [
  				'class' => is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'],
				'alt' => $alt
			] );
		}
	}

	if ( empty( $new_avatar ) ) {
		$new_avatar = $avatar;
	}

	return $new_avatar;
}
add_filter( 'get_avatar', 'pto_filter_avatar_client', 1, 6 );*/
add_action( "wp_ajax_nopriv_pto_remove_current_client_photo", "pto_remove_current_client_photo" );
add_action( "wp_ajax_pto_remove_current_client_photo", "pto_remove_current_client_photo" );
function pto_remove_current_client_photo() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	if ( empty( $team ) || empty( $team['assigned'] ) ) {
		return;
	}

	$assigned = $team['assigned'];
	if ( ! empty( $team['type'] ) && 'admin' === $team['type'] ) {
		update_post_meta($assigned, 'team_avatar', '');
	} else {
		$client_contacts = get_post_meta($assigned, 'client_contacts', true);
		$client_contacts[ $user->ID ]['team_avatar'] = '';
		update_post_meta($assigned, 'client_contacts', $client_contacts);
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => __('The photo was removed successfully', 'projectopia-core'),
	) ); 
}

add_action( "wp_ajax_pto_manage_client_fe_files", "pto_manage_client_fe_files" );
function pto_manage_client_fe_files() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$client = isset($_POST['post']) ? sanitize_text_field(wp_unslash($_POST['post'])) : '';
	$file = isset($_POST['file']) ? sanitize_text_field(wp_unslash($_POST['file'])) : '';
	$fe = isset($_POST['fe']) ? sanitize_text_field(wp_unslash($_POST['fe'])) : '';
	$fe_files = get_post_meta($client, 'fe_files', true);
	$fe_files = $fe_files && is_array($fe_files) ? $fe_files : array();
	$fe_files[ $file ] = $fe;
	update_post_meta($client, 'fe_files', $fe_files);
	pto_send_json( array( 
		'error'   => false,
		'message' => __('Operation Completed', 'projectopia-core'),
	) ); 
}

add_action( 'bulk_actions-users', 'pto_convert_users_bulk_actions' );
/**
 * Custom bulk action register to convert the users.
 * @param array $action Action list.
 */
function pto_convert_users_bulk_actions( $actions ) {
    // Add custom bulk action
	$actions['bulk-convert-to-pto-client'] = __( 'Convert to PTO Client', 'projectopia-core' );
	//$actions['bulk-convert-to-pto-team-member'] = __( 'Convert to CQPIM Team Member', 'projectopia-core' );
    return $actions;
}

add_action( 'handle_bulk_actions-users', 'pto_convert_users_bulk_action_handle', 10, 3 );
/**
 * Function to handle the custom bulk operation on users.
 * @param string $redirect_to URL String.
 * @param string $do_action   Action to perform operation.
 * @param array  $user_ids    User id list.
 */
function pto_convert_users_bulk_action_handle( $redirect_to, $do_action, $user_ids ) {
	if ( ! empty( $do_action ) && ( 'bulk-convert-to-pto-client' === $do_action || 'bulk-convert-to-pto-team-member' === $do_action ) ) {
		$unchange_roles  = array( 'administrator', 'cqpim_client', 'cqpim_admin', 'cqpim_user', 'ptouploader' );
		$count_to_change = 0;
		foreach ( $user_ids as $user_id ) {
			$user = get_user_by( 'id', $user_id );
			//Check selected user is not from list $unchange_roles.
			if ( empty( array_intersect( $user->roles,$unchange_roles ) ) ) {
				$count_to_change++;
				if ( count($user->roles) === 1 ) {
					$user->remove_role( $user->roles[0] );
				}

				if ( 'bulk-convert-to-pto-client' === $do_action ) {
					$user->add_role( 'cqpim_client' );
					// Calling a function to creat new client.
					pto_create_user_as_client_from_user_page( $user_id );
				} elseif ( 'bulk-convert-to-pto-team-member' === $do_action ) {
					$user->add_role( 'cqpim_user' );
					// Calling a function to creat new team member as pto-user.
					pto_create_team_member_from_users( $user_id );
				}
			}
		}
		$redirect_to = add_query_arg( 'bulk_user_change_count', $count_to_change, $redirect_to );
		return $redirect_to;
	}

	return false;
}

add_action( 'admin_notices', 'pto_convert_users_bulk_action_admin_notice' );
/**
 * Function to show notice to admin after bulk operation on user convert.
 */
function pto_convert_users_bulk_action_admin_notice() {
	if ( ! empty( $_REQUEST['bulk_user_change_count'] ) ) {
		$user_change_count = intval( wp_unslash( $_REQUEST['bulk_user_change_count'] ) );
		printf(
			'<div id="message" class="updated notice is-dismissible"><p>%s User Converted.</p></div>',
			esc_html( $user_change_count )
		);
	}
}

add_action( 'wp_enqueue_scripts', 'pto_change_user_lang');

function pto_change_user_lang( ) {
    
    $user_id = get_current_user_id();
    $user_lang =  get_user_locale( $user_id );
    if( !empty($user_lang) ) {
    	
    	switch_to_locale($user_lang);	
    }
    
}

