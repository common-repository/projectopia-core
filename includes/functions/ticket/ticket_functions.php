<?php
add_action( "wp_ajax_nopriv_pto_delete_support_message", "pto_delete_support_message" );
add_action( "wp_ajax_pto_delete_support_message", "pto_delete_support_message" );    
function pto_delete_support_message() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$project_messages = get_post_meta($project_id, 'ticket_updates', true);
	$project_messages = array_reverse($project_messages);
	unset($project_messages[ $key ]);
	$project_messages = array_filter($project_messages);
	$project_messages = array_reverse($project_messages);
	update_post_meta($project_id, 'ticket_updates', $project_messages);
	wp_send_json_success( [ 'message' => 'success' ] );
	exit();
}

add_action( "wp_ajax_nopriv_pto_client_raise_support_ticket", "pto_client_raise_support_ticket" );
add_action( "wp_ajax_pto_client_raise_support_ticket", "pto_client_raise_support_ticket" );
function pto_client_raise_support_ticket( $data, $files = array(), $attachments = array() ) {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$current_user = wp_get_current_user();
	$data = isset($_POST['data']) ? pto_sanitize_rec_array( wp_unslash( $_POST['data'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$custom_fields = get_option('cqpim_custom_fields_support'); 
	$custom_fields = json_decode($custom_fields);
	$custom = isset($data['custom']) ? $data['custom'] : array();
	if ( is_array( $custom_fields ) ) {
		foreach ( $custom_fields as $custom_field ) {
			if ( empty($custom[ $custom_field->name ]) && ! empty($custom_field->required) ) {
				pto_send_json( array( 
					'error'   => true,
					'title'   => __('Required Fields Missing', 'projectopia-core'),
					'message' => __('Please complete all required fields.', 'projectopia-core'),
				) );                
			}
		}
	}
	$title = isset($data['ticket_title']) ? $data['ticket_title'] : '';
	$priority = isset($data['ticket_priority_new']) ? $data['ticket_priority_new'] : 'normal';
	$details = isset($data['ticket_update_new']) ? $data['ticket_update_new'] : '';
	$details = make_clickable($details);
	$item = 1;
	$reject = 0;
	if ( empty($title) || empty($details) || empty($item) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('You must enter a title and message.', 'projectopia-core'),
		) );     
	} else {
		$user = wp_get_current_user();
		$args = array(
			'post_type'      => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$clients = get_posts($args);
		foreach ( $clients as $client ) {
			$client_details = get_post_meta($client->ID, 'client_details', true);
			$client_user_id = $client_details['user_id'];
			if ( $user->ID === $client_user_id ) {
				$client_object_id = $client->ID;
			}
		}
		if ( empty($client_object_id) ) {
			foreach ( $clients as $client ) {
				$client_ids = get_post_meta($client->ID, 'client_ids', true);
				if ( in_array($user->ID, $client_ids) ) {
					$client_object_id = $client->ID;
					$client_type = 'contact';
				}
			}           
		}
		$client_details = get_post_meta($client_object_id, 'client_details', true);
		$new_ticket = array(
			'post_type'    => 'cqpim_support',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $title,
			'post_author'  => $user->ID,             
		);
		$ticket_pid = wp_insert_post( $new_ticket, true );
		if ( ! is_wp_error( $ticket_pid ) ) {
			$ticket_updated = array(
				'ID'        => $ticket_pid,
				'post_name' => $ticket_pid,
			);                      
			wp_update_post( $ticket_updated );
			$custom = isset($data['custom']) ? $data['custom'] : array();
			update_post_meta($ticket_pid, 'custom_fields', $custom);
			update_post_meta($ticket_pid, 'ticket_client', $client_object_id);
			$currency = get_option('currency_symbol');
			$currency_code = get_option('currency_code');
			$currency_position = get_option('currency_symbol_position');
			$currency_space = get_option('currency_symbol_space'); 
			$client_currency = get_post_meta($client_object_id, 'currency_symbol', true);
			$client_currency_code = get_post_meta($client_object_id, 'currency_code', true);
			$client_currency_space = get_post_meta($client_object_id, 'currency_space', true);      
			$client_currency_position = get_post_meta($client_object_id, 'currency_position', true);
			if ( ! empty($client_currency) ) {
				update_post_meta($ticket_pid, 'currency_symbol', $client_currency);
			} else {
				update_post_meta($ticket_pid, 'currency_symbol', $currency);
			}
			if ( ! empty($client_currency_code) ) {
				update_post_meta($ticket_pid, 'currency_code', $client_currency_code);
			} else {
				update_post_meta($ticket_pid, 'currency_code', $currency_code);
			}
			if ( ! empty($client_currency_space) ) {
				update_post_meta($ticket_pid, 'currency_space', $client_currency_space);
			} else {
				update_post_meta($ticket_pid, 'currency_space', $currency_space);
			}
			if ( ! empty($client_currency_position) ) {
				update_post_meta($ticket_pid, 'currency_position', $client_currency_position);
			} else {
				update_post_meta($ticket_pid, 'currency_position', $currency_position);
			}
			$ticket_status = 'open';
			update_post_meta($ticket_pid, 'ticket_status', $ticket_status);
			$tchanges = pto_get_transient('ticket_changes');
			$ticket_changes = ! empty($tchanges) ? $tchanges : array();
			$attachments = isset($data['ticket_files']) ? $data['ticket_files'] : array();
			$attachments_to_send = array();
			if ( ! empty($attachments) ) {
				$attachments = explode(',', $attachments);
				foreach ( $attachments as $attachment ) {
					global $wpdb;
					$wpdb->update( $wpdb->posts, [ 'post_parent' => $ticket_pid ], [ 'ID' => $attachment ] );
					update_post_meta($attachment, 'cqpim', true);
					$filename = basename( get_attached_file( $attachment ) );
					$attachments_to_send[] = get_attached_file( $attachment );
					/* translators: %s: Uploaded File Name */
					$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename);
				}
			}
			$ticket_updates = array();
			$ticket_updates[] = array(
				'details' => $details,
				'time'    => time(),
				'name'    => $user->display_name,
				'email'   => $user->user_email,
				'user'    => $client_object_id,
				'type'    => 'client',
				'changes' => $ticket_changes,
			);
			update_post_meta($ticket_pid, 'ticket_updates', $ticket_updates);
			update_post_meta($ticket_pid, 'ticket_priority', $priority);
			update_post_meta($ticket_pid, 'ticket_client', $client_object_id);
			if ( ! empty($client_details['ticket_assignee']) ) {
				update_post_meta($ticket_pid, 'ticket_owner', $client_details['ticket_assignee']);
				pto_add_team_notification($client_details['ticket_assignee'], $current_user->ID, $ticket_pid, 'support_assignee');
			}
			$last_updated = time();
			update_post_meta($ticket_pid, 'last_updated', $last_updated);
			$sender_name = get_option('company_name');
			$sender_name = $sender_name;
			$sender_email = get_option('company_support_email');
			$to = array();
			$to[] = $sender_email;
			if ( ! empty($client_details['ticket_assignee']) ) {
				$assignee_details = get_post_meta($client_details['ticket_assignee'], 'team_details', true);
				if ( ! empty($assignee_details['team_email']) ) {
					$to[] = $assignee_details['team_email'];
				}
			}
			if ( $priority == 'high' || $priority == 'immediate' ) {
				add_filter('phpmailer_init','update_priority_mailer');
			}           
			$args = array(          
				'post_type'      => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status'    => 'private',          
			);          
			$team_members = get_posts($args);           
			foreach ( $team_members as $member ) {             
				$team_details = get_post_meta($member->ID, 'team_details', true);               
				$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';             
				if ( ! empty($user_id) ) {              
					if ( user_can($user_id, 'edit_cqpim_supports') ) {                     
						pto_add_team_notification($member->ID, $user->ID, $ticket_pid, 'new_ticket');                       
					}               
				}
			}
			$email_subject = get_option('client_create_ticket_subject');
			$email_content = get_option('client_create_ticket_email');
			$email_subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $ticket_pid . ']', $email_subject);
			$email_subject = str_replace('%%CLIENT_NAME%%', $user->display_name, $email_subject);
			$email_content = str_replace('%%CLIENT_NAME%%', $user->display_name, $email_content);               
			$email_subject = pto_replacement_patterns($email_subject, $client_object_id, 'client');
			$email_subject = pto_replacement_patterns($email_subject, $ticket_pid, 'ticket');
			$email_content = pto_replacement_patterns($email_content, $client_object_id, 'client');
			$email_content = pto_replacement_patterns($email_content, $ticket_pid, 'ticket');
			foreach ( $to as $recip ) {
				pto_send_emails($recip, $email_subject, $email_content, '', $attachments_to_send, 'support');
			}
			$dashboard = get_option('cqpim_client_page');
			$permalink = get_the_permalink($dashboard);
			$support_slug = get_option('cqpim_support_slug');
			pto_send_json( array( 
				'error'   => false,
				'message' => home_url() . '/' . $support_slug . '/' . $ticket_pid,
			) );
		} else {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('The ticket could not be created', 'projectopia-core'),
			) ); 
		}
	}
}

function update_priority_mailer( $mailer ) {
	$mailer->Priority = 1;
	return $mailer;
}

add_action( "wp_ajax_nopriv_pto_switch_resolved_tickets", "pto_switch_resolved_tickets" );
add_action( "wp_ajax_pto_switch_resolved_tickets", "pto_switch_resolved_tickets" );
function pto_switch_resolved_tickets() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$ticket_status = pto_get_transient('ticket_status');
	$status = ! empty($ticket_status) ? $ticket_status : '';
	if ( $status == 'resolved' ) {
		$support_ticket_status = get_option('support_status');
		$key_arr = $support_ticket_status['key'];
		
		$res_index = array_search('resolved', $key_arr);
		unset($key_arr[ $res_index ]);
			
		pto_set_transient('ticket_status',$key_arr);
	} else {
		pto_set_transient('ticket_status','resolved');
	}
	pto_send_json( array( 
		'error' => false,
	) ); 
}

add_action( "wp_ajax_nopriv_pto_update_support_ticket", "pto_update_support_ticket" );
add_action( "wp_ajax_pto_update_support_ticket", "pto_update_support_ticket" );
function pto_update_support_ticket( $ticket_id = false, $data = array(), $files = array(), $type = NULL, $user = NULL, $attachments = array() ) {
	if ( empty( $ticket_id ) ) {
		check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
		$data = isset( $_POST['data'] ) ? pto_sanitize_rec_array( wp_unslash( $_POST['data'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $data['ticket_status_new'] ) ) {
			$data['ticket_status_new'] = 'open';
		}
		$ticket_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$files = isset($data['ticket_files']) ? sanitize_text_field( wp_unslash( $data['ticket_files'] ) ) : '';
		$ajax_call = true;
		$custom_fields = get_option( 'cqpim_custom_fields_support' ); 
		$custom_fields = json_decode( $custom_fields );
		$custom = isset( $data['custom'] ) ? $data['custom'] : array();
		foreach ( $custom_fields as $custom_field ) {
			if ( empty( $custom[ $custom_field->name ] ) && ! empty( $custom_field->required ) ) {
				pto_send_json( array( 
					'error'   => true,
					'title'   => __( 'Required Fields Missing', 'projectopia-core' ),
					'message' => __( 'Please complete all required fields.', 'projectopia-core' ),
				) );                 
			}
		}
	}
	$current_user = wp_get_current_user();
	$ticket_changes = isset( $data['ticket_changes'] ) && is_array( $data['ticket_changes'] ) ? $data['ticket_changes'] : array();
	$status = isset( $data['ticket_status_new'] ) ? $data['ticket_status_new'] : '';
	$priority = isset( $data['ticket_priority_new'] ) ? $data['ticket_priority_new'] : '';
	$update = isset( $data['ticket_update_new'] ) ? $data['ticket_update_new'] : '';
	$update = make_clickable( $update );
	$owner = isset( $data['ticket_owner'] ) ? $data['ticket_owner'] : '';
	$watchers = isset( $data['task_watchers'] ) ? $data['task_watchers'] : '';
	$files = isset( $files ) ? $files : array();
	$client_id = get_post_meta( $ticket_id, 'ticket_client', true );
	$attachments = isset( $files ) ? $files : '';
	$attachments_to_send = array();
	if ( ! empty( $attachments ) ) {
		$attachments = explode( ',', $attachments );
		foreach ( $attachments as $attachment ) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, [ 'post_parent' => $ticket_id ], [ 'ID' => $attachment ] );
			update_post_meta($attachment, 'cqpim', true);
			$filename = basename( get_attached_file( $attachment ) );
			$attachments_to_send[] = get_attached_file( $attachment );
			/* translators: %s: Uploaded File Name */
			$ticket_changes[] = sprintf( esc_html__( 'Uploaded file: %s', 'projectopia-core' ), $filename );
		}
	}

	if ( $type == 'admin' ) {
		update_post_meta( $ticket_id, 'unread', 1 );
		if ( empty( $user ) ) {
			$user = $current_user;
		}
		$args = array(
			'post_type'      => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$members = get_posts( $args );
		foreach ( $members as $member ) {
			$team_details = get_post_meta( $member->ID, 'team_details', true );
			if ( $team_details['user_id'] == $user->ID ) {
				$assigned = $member->ID;
			}
		}
		update_post_meta( $ticket_id, 'ticket_watchers', $watchers );
		$ticket_owner = get_post_meta( $ticket_id, 'ticket_owner', true );                
		if ( $ticket_owner != $owner ) {
			$owner_details = get_post_meta( $owner, 'team_details', true );
			$owner_cap = $owner_details['team_name'];
			/* translators: %s: New Assignee Name */
			$ticket_changes[] = sprintf( esc_html__( 'Assignee changed to %s', 'projectopia-core' ), $owner_cap );                   
			update_post_meta( $ticket_id, 'ticket_owner', $owner );
			pto_add_team_notification( $owner, $current_user->ID, $ticket_id, 'support_assignee' );
		}
		pto_add_team_notification( $client_id, $current_user->ID, $ticket_id, 'support_update' );
	}

	if ( $type == 'client' ) {
		if ( empty( $user ) ) {
			$user = $current_user;
		}
		$client = pto_get_client_from_userid( $user );
		$assigned = $client['assigned'];
		$ticket_status = get_post_meta( $ticket_id, 'ticket_status', true );
		if ( $ticket_status == 'waiting' || $ticket_status == 'resolved' ) {
			$status = 'open';
		}
	}

	if ( ! empty( $ajax_call ) ) {
		$custom = isset( $data['custom'] ) ? $data['custom'] : array();
		update_post_meta( $ticket_id, 'custom_fields', $custom );
	}

	$ticket_status = get_post_meta( $ticket_id, 'ticket_status', true );
	if ( $ticket_status != $status ) {
		$support_status = get_option( 'support_status' );
		$ticket_key = array_search( $ticket_status, $support_status['key'] );
		$status_cap = $support_status['value'][ $ticket_key ];

		/* translators: %s: Ticket New Status */
		$ticket_changes[] = sprintf( esc_html__( 'Ticket Status changed to %s', 'projectopia-core' ), $status_cap );
	}
	update_post_meta( $ticket_id, 'ticket_status', $status );

	$ticket_priority = get_post_meta( $ticket_id, 'ticket_priority', true );
	if ( $ticket_priority != $priority ) {
		$priority_cap = $priority;
		$priority_cap = ucfirst( $priority );
		/* translators: %s: Ticket Priority Name */
		$ticket_changes[] = sprintf( esc_html__( 'Ticket Priority changed to %s', 'projectopia-core' ), $priority_cap );
		update_post_meta( $ticket_id, 'ticket_priority', $priority );
	}

	$last_updated = time();
	update_post_meta( $ticket_id, 'last_updated', $last_updated );

	if ( ! empty( $ticket_changes ) || ! empty( $update ) ) {
		$ticket_updates = get_post_meta( $ticket_id, 'ticket_updates', true );
		$ticket_updates = $ticket_updates && is_array( $ticket_updates ) ? $ticket_updates : array();
		$ticket_updates[] = array(
			'details' => $update,
			'time'    => time(),
			'user'    => $assigned,
			'name'    => $user->display_name,
			'email'   => $user->user_email,
			'type'    => $type,
			'changes' => $ticket_changes,
		);          
		update_post_meta( $ticket_id, 'ticket_updates', $ticket_updates );
	}

	if ( $priority == 'high' || $priority == 'immediate' ) {
		add_filter( 'phpmailer_init','update_priority_mailer' );
	}

	$email_subject = get_option( 'client_update_ticket_subject' );
	$email_content = get_option( 'client_update_ticket_email' );
	$email_subject = pto_replacement_patterns( $email_subject, $ticket_id, 'ticket' );
	$email_content = pto_replacement_patterns( $email_content, $ticket_id, 'ticket' );
	$addresses_to_send = array();
	$ticket = get_post( $ticket_id );
	$client = $ticket->post_author;
	$client = get_user_by( 'id', $client );       
	if ( empty( $client_email ) ) {
		$args = array(
			'post_type'      => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$members = get_posts( $args );
		foreach ( $members as $member ) {
			$team_details = get_post_meta( $member->ID, 'client_details', true );
			if ( $team_details['user_id'] == $client->ID ) {
				$notifications = get_post_meta( $member->ID, 'client_notifications', true );
			}
		}
		if ( empty( $assigned ) ) {
			foreach ( $members as $member ) {
				$client_contacts = get_post_meta( $member->ID, 'client_contacts', true );
				if ( empty( $client_contacts ) ) {
					$client_contacts = array();
				}
				foreach ( $client_contacts as $contact ) {
					if ( $contact['user_id'] == $client->ID ) {
						$notifications = isset( $contact['notifications'] ) ? $contact['notifications'] : array();
					}
				}
			}           
		}
		$no_tickets = isset( $notifications['no_tickets'] ) ? $notifications['no_tickets'] : 0;
		$no_tickets_comment = isset( $notifications['no_tickets_comment'] ) ? $notifications['no_tickets_comment'] : 0;            
		$client_email = $client->user_email;
		if ( ! empty( $no_tickets ) ) {
			$client_email = '';
		}
		if ( ! empty( $no_tickets_comment ) && empty( $update ) ) {
			$client_email = '';
		}
	}
	if ( ! empty( $client_email ) ) {
		$addresses_to_send[] = array(
			'email' => $client->user_email,
			'name'  => $client->display_name,
		);
	}

	$ticket_owner = get_post_meta( $ticket_id, 'ticket_owner', true );
	if ( ! empty( $ticket_owner ) ) {
		$owner_details = get_post_meta( $ticket_owner, 'team_details', true );
		$user_id = isset( $owner_details['user_id'] ) ? $owner_details['user_id'] : '';
		if ( ! empty( $user_id ) && $user_id != $current_user->ID ) {
			pto_add_team_notification( $ticket_owner, $current_user->ID, $ticket_id, 'support_update' );
		}
		$owner_details = get_post_meta( $ticket_owner, 'team_details', true );
		$owner_email = isset( $owner_details['team_email'] ) ? $owner_details['team_email'] : '';
		$addresses_to_send[] = array(
			'email' => $owner_email,
			'name'  => $owner_details['team_name'],
		);
	} else {
		// Notification to admin if team member is not assigned to tickets.
		if ( ! empty( $type ) && 'client' === $type ) {
			$pto_admin_count = 0;
			// Check pto-admin is there then send notification.
			$admin_users = get_users( array( 'role' => 'cqpim_admin' ) );
			if ( ! empty( $admin_users ) ) {
				foreach ( $admin_users as $admin ) {
					$team_member_id = pto_get_team_from_userid( $admin );
					if ( ! empty( $team_member_id ) ) {
						$pto_admin_count++;
						pto_add_team_notification( $team_member_id, $current_user->ID, $ticket_id, 'support_update' );
						$addresses_to_send[] = array(
							'email' => $admin->user_email,
							'name'  => $admin->user_nicename,
						);
					}
				}
			}
			// If pto-admin is not there then send notification to wp admin.
			if ( $pto_admin_count == 0 ) {
				$admin_users = get_users( array( 'role' => 'administrator' ) );
				foreach ( $admin_users as $admin ) {
					$team_member_id = pto_get_team_from_userid( $admin );
					if ( ! empty( $team_member_id ) ) {
						pto_add_team_notification( $team_member_id, $current_user->ID, $ticket_id, 'support_update' );
						$addresses_to_send[] = array(
							'email' => $admin->user_email,
							'name'  => $admin->user_nicename,
						);
					}
				}
			}
		}
	}

	$watchers_send = get_post_meta( $ticket_id, 'ticket_watchers', true );
	if ( empty($watchers_send) ) {
		$watchers_send = array();
	}
	foreach ( $watchers_send as $watcher ) {
		$watcher_details = get_post_meta( $watcher, 'team_details', true );
		$user_id = isset( $watcher_details['user_id'] ) ? $watcher_details['user_id'] : '';
		if ( ! empty( $user_id ) && $user_id != $current_user->ID ) {
			pto_add_team_notification( $watcher, $current_user->ID, $ticket_id, 'support_update' );
		}
		$watcher_email = isset( $watcher_details['team_email'] ) ? $watcher_details['team_email'] : '';
		$addresses_to_send[] = array(
			'email' => $watcher_email,
			'name'  => $watcher_details['team_name'],
		);
	}
	foreach ( $addresses_to_send as $key => $address ) {
		if ( $address['email'] == $user->user_email ) {
			unset( $addresses_to_send[ $key ] );
		}
	}
	$current_name = $user->display_name;            
	if ( ! empty( $update ) || $ticket_owner != $owner ) {
		$i = 0;
		foreach ( $addresses_to_send as $address ) {
			$name = $address['name'];
			${"email_subject_" . $i} = str_replace('%%UPDATER_NAME%%', $current_name, $email_subject);
			${"email_subject_" . $i} = str_replace('%%NAME%%', $name, ${"email_subject_" . $i});
			${"email_subject_" . $i} = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $ticket_id . ']', ${"email_subject_" . $i});
			${"email_content_" . $i} = str_replace('%%NAME%%', $name, $email_content);
			${"email_content_" . $i} = str_replace('%%UPDATER_NAME%%', $current_name, ${"email_content_" . $i});
			pto_send_emails( $address['email'], ${"email_subject_" . $i}, ${"email_content_" . $i}, '', $attachments_to_send, 'support' );
			$i++;
		}
	}
	if ( ! empty( $ajax_call ) ) {
		pto_send_json( array( 
			'error'   => false,
			'title'   => __( 'Ticket Updated', 'projectopia-core' ),
			'message' => __( 'The ticket was successfully updated.', 'projectopia-core' ),
		) );             
	}
}

add_action( "wp_ajax_pto_delete_support_page", "pto_delete_support_page" );
/**
 * Function to delete the support ticket.
 */
function pto_delete_support_page() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty( $_POST['task_id'] ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => 'Support ticket ID is missing.',
		) );
	}

	$task_id = sanitize_text_field( wp_unslash( $_POST['task_id'] ) );

	//Remove attached files with support tickets.
	$attached_files = get_attached_media( '', $task_id );
	foreach ( $attached_files as $file ) {
		wp_delete_attachment( $file->ID );
	}

	wp_delete_post( $task_id, true );
	pto_send_json( array(
		'error'    => false,
		'redirect' => admin_url() . 'admin.php?page=pto-tickets',
	) );
}

add_action( "wp_ajax_pto_bulk_delete_support_tickets", "pto_bulk_delete_support_tickets" );
/**
 * Bulk delete support tickets.
 */
function pto_bulk_delete_support_tickets() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$data = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	if ( empty( $data['support_ticket_ids'] ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => 'No support ticket selected.',
		) );
	}

	$support_ticket_ids = $data['support_ticket_ids'];
	foreach ( $support_ticket_ids as $ticket_id ) {
        //Delete attached media before delete post.
        $attachments = get_attached_media( '', $ticket_id );
        foreach ( $attachments as $attachment ) {
            wp_delete_attachment( $attachment->ID, 'true' );
            //Delete the file on plugin upload dir
            $dirs = wp_upload_dir();
            $file = $dirs['basedir'] . '/pto-uploads/' . end( explode( '/', $attachment->guid ) );
            wp_delete_file( $file );
        }
		wp_delete_post($ticket_id, true);
	}

	pto_send_json( array(
		'error'   => false,
		'message' => 'Support tickets deleted successfully',
	) );
}
