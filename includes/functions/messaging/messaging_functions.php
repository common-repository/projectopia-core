<?php
function pto_retrieve_messageble_users( $user_id, $type = NULL ) {
	$users = array();
	if ( $type == 'client' ) {
		$user = wp_get_current_user();
		$args = array(
			'post_type'      => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$projects = get_posts( $args );
		foreach ( $projects as $project ) { 
			$project_details = get_post_meta( $project->ID, 'project_details', true ); 
			$client_id = isset( $project_details['client_id'] ) ? $project_details['client_id'] : '';
			$client_contact = isset($project_details['client_contact'] ) ? $project_details['client_contact'] : '';
			$owner = get_user_by( 'id', $client_contact );
			$client_details = get_post_meta( $client_id, 'client_details', true );
			$client_ids = get_post_meta( $client_id, 'client_ids', true );
			$client_user_id = isset( $client_details['user_id'] ) ? $client_details['user_id'] : '';
			if ( ! is_array( $client_ids ) ) {
				$client_ids = array( $client_ids );
			}
			$closed = isset( $project_details['closed'] ) ? $project_details['closed'] : ''; 
			if ( $client_user_id == $user->ID && empty( $closed ) || in_array( $user->ID, $client_ids ) && empty( $closed ) ) {  
				$project_contributors = get_post_meta( $project->ID, 'project_contributors', true ); 
				if ( ! empty( $project_contributors ) ) {
					foreach ( $project_contributors as $contrib ) {
						$team_details = get_post_meta( $contrib['team_id'], 'team_details', true );
						$contrib_obj = get_user_by( 'id', $team_details['user_id'] );
						$users[ $contrib_obj->ID ] = $contrib_obj->display_name;
					}
				}
			}
		}
	} else {
		$user = get_user_by( 'id', $user_id );
		$args = array(
			'post_type'      => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$members = get_posts( $args );
		foreach ( $members as $member ) {
			$team_details = get_post_meta( $member->ID, 'team_details', true );
			if ( ! empty( $team_details['user_id'] ) && $team_details['user_id'] == $user->ID ) {
				$assigned = $member->ID;
			}
		}
		$args = array(
			'post_type'      => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$members = get_posts( $args );
		foreach ( $members as $member ) {
			$team_details = get_post_meta( $member->ID, 'team_details', true );
			if ( ! empty( $team_details['user_id'] ) ) {
				$member = get_user_by( 'id', $team_details['user_id'] );
				$users[ $member->ID ] = $member->display_name . sprintf( ' (%s)', __( 'Team Member', 'projectopia-core' ) );
			}
		}
		if ( get_option( 'cqpim_messages_allow_client' ) == 1 ) {
			if ( user_can( $user->ID, 'cqpim_message_clients_from_projects' ) ) {
				$args = array(
					'post_type'      => 'cqpim_project',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$projects = get_posts( $args );
				if ( ! empty( $projects ) ) {
					foreach ( $projects as $project ) {
						$access = false;
						$project_contributors = get_post_meta( $project->ID, 'project_contributors', true );
						if ( ! empty( $project_contributors ) ) {
							foreach ( $project_contributors as $contributor ) {
								if ( ! empty($contributor['team_id']) && $assigned == $contributor['team_id'] ) {
									$access = true;
								}
							}
						}   
						if ( $access === true ) {
							$project_details = get_post_meta( $project->ID, 'project_details', true );
							$client_id = isset( $project_details['client_id'] ) ? $project_details['client_id'] : '';
							$client_contacts = get_post_meta( $client_id, 'client_contacts', true );
							$client_contact = isset( $project_details['client_contact'] ) ? $project_details['client_contact'] : '';
							$team_details = get_post_meta( $client_id, 'client_details', true );
							$company = isset( $team_details['client_company'] ) ? $team_details['client_company'] : '';
							$closed = isset( $project_details['closed'] ) ? $project_details['closed'] : '';
							if ( ! empty( $team_details['user_id'] ) && empty( $closed ) ) {
								$member = get_user_by( 'id', $team_details['user_id'] );
								$company = isset( $team_details['client_company'] ) ? $team_details['client_company'] : '';
								$users[ $member->ID ] = sprintf( '%1$s - %2$s (%3$s)', $member->display_name, $company, __( 'Client', 'projectopia-core' ) );
								if ( ! empty( $client_contacts ) ) {
									foreach ( $client_contacts as $contact ) {
										$contact_obj = get_user_by( 'id', $contact['user_id'] );
										if ( empty( $contact_obj ) || ! is_object( $contact_obj ) ) {
											continue;
										}
										$users[ $contact_obj->ID ] = sprintf( '%1$s - %2$s (%3$s)', $contact_obj->display_name, $company, __( 'Client', 'projectopia-core' ) );                     
									}
								}
							}                       
						}
					}
				}
			}
			if ( user_can( $user->ID, 'cqpim_message_all_clients' ) ) {
				$args = array(
					'post_type'      => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$members = get_posts( $args );
				foreach ( $members as $member ) {
					$team_details = get_post_meta( $member->ID, 'client_details', true );
					$client_contacts = get_post_meta( $member->ID, 'client_contacts', true );
					if ( ! empty( $team_details['user_id'] ) ) {
						$member = get_user_by( 'id', $team_details['user_id'] );
						$company = isset( $team_details['client_company'] ) ? $team_details['client_company'] : '';
						$users[ $member->ID ] = sprintf( '%1$s - %2$s (%3$s)', $member->display_name, $company, __( 'Client', 'projectopia-core' ) );
					}
					if ( ! empty( $client_contacts ) ) {
						foreach ( $client_contacts as $contact ) {
							$contact_obj = get_user_by( 'id', $contact['user_id'] );
							if ( empty( $contact_obj ) || ! is_object( $contact_obj ) ) {
								continue;
							}
							$users[ $contact_obj->ID ] = $contact_obj->display_name . ' - ' . $company . ' ' . __('(Client)', 'projectopia-core');                                   
						}
					}               
				}           
			}
		}
		if ( ! empty( $users ) ) {
			foreach ( $users as $key => $contact ) {
				if ( $key == $user->ID ) {
					unset( $users[ $key ] );
				}
			}
		}
	}

	return $users;
}

function pto_get_conversation_id() {
	$args = array(
		'post_type'      => 'cqpim_conversations',
		'posts_per_page' => 1,
		'orderby'        => 'ID',
		'order'          => 'DESC',
		'post_status'    => 'private',
	);
	$conversations = get_posts($args);
	if ( ! empty($conversations) ) {
		foreach ( $conversations as $conversation ) {
			$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
		}
		$conversation_id++;
	} else {
		$conversation_id = 1;
	}
	return $conversation_id;
}

function pto_fetch_conversations( $user_id, $show_all = false ) {
	if ( current_user_can( 'access_cqpim_messaging_admin' ) && $show_all ) {
		$args = array(
			'post_type'      => 'cqpim_conversations',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'order'          => 'DESC',
			'orderby'        => 'meta_value',
			'meta_key'       => 'updated',
		);      
	} else {
		$args = array(
			'post_type'      => 'cqpim_conversations',
			'post_status'    => 'private',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'member_' . $user_id,
					'value'   => $user_id,
					'compare' => '=',
				),
			),
			'order'          => 'DESC',
			'orderby'        => 'meta_value',
			'meta_key'       => 'updated',
		);
	}
	
	$conversations = get_posts( $args );

	return $conversations;
}

function pto_new_messages( $user_id ) {
	$read_val = false;
	$conversations = pto_fetch_conversations($user_id);
	$i = 0;
	foreach ( $conversations as $conversation ) {
		$id = get_post_meta($conversation->ID, 'conversation_id', true);
		$args = array(
			'post_type'      => 'cqpim_messages',
			'posts_per_page' => -1,
			'post_status'    => 'private',
			'meta_query'     => array(
				array(
					'key'     => 'conversation_id',
					'value'   => $id,
					'compare' => '=',
				),
			),
		);
		$messages = get_posts($args);
		foreach ( $messages as $message ) {
			$read = get_post_meta($message->ID, 'read', true);
			if ( ! in_array($user_id, $read) ) {
				$read_val = true;
				$i++;
			}               
		}
	}
	return array(
		'read_val'     => $read_val,
		'new_messages' => $i,
	);
}

add_action( "wp_ajax_pto_create_conversation", "pto_create_conversation" );
function pto_create_conversation() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$recipients = isset($_POST['recipients']) ? sanitize_text_field( wp_unslash( $_POST['recipients'] ) ) : '';
	$subject = isset($_POST['subject']) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$update = isset($_POST['message']) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$attachments = isset($_POST['attachments']) ? sanitize_text_field( wp_unslash( $_POST['attachments'] ) ) : array();
	$client = isset($_POST['client']) ? sanitize_text_field( wp_unslash( $_POST['client'] ) ) : '';
	if ( empty($recipients) || empty($subject) || empty($update) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('Please add recipients, subject and message.', 'projectopia-core'),
		) ); 
	}
	// Create a convo
	$args = array(
		'post_type'      => 'cqpim_conversations',
		'posts_per_page' => 1,
		'orderby'        => 'ID',
		'order'          => 'DESC',
		'post_status'    => 'private',
	);
	$conversations = get_posts($args);
	if ( ! empty($conversations) ) {
		foreach ( $conversations as $conversation ) {
			$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
		}
		$conversation_id++;
	} else {
		$conversation_id = 1;
	}
	$conversation = array(
		'post_type'    => 'cqpim_conversations',
		'post_status'  => 'private',
		'post_title'   => $subject,
		'post_content' => '',
	);
	$conversation = wp_insert_post($conversation);
	if ( ! is_wp_error($conversation) ) {
		update_post_meta($conversation, 'conversation_id', $conversation_id);
		update_post_meta($conversation, 'created', time());
		update_post_meta($conversation, 'author', $user->ID);
		update_post_meta($conversation, 'updated', array(
			'by' => $user->ID,
			'at' => time(),
		));
		$recip_ids = explode(',', $recipients);
		$recip_ids[] = $user->ID;
		update_post_meta($conversation, 'recipients', $recip_ids);
		foreach ( $recip_ids as $id ) {
			update_post_meta($conversation, 'member_' . $id, $id);
		}
	}
	$message = array(
		'post_type'    => 'cqpim_messages',
		'post_status'  => 'private',
		'post_title'   => '',
		'post_content' => '',
	);
	$message = wp_insert_post($message);
	if ( ! is_wp_error($message) ) {
		update_post_meta($message, 'conversation_id', $conversation_id);
		update_post_meta($message, 'sender', $user->ID);
		update_post_meta($message, 'message', wpautop($update));
		update_post_meta($message, 'stamp',  time());
		update_post_meta($message, 'read', array( $user->ID ));
		$attachments = explode(',', $attachments);
		$attachment_links = array();
		foreach ( $attachments as $attachment ) {
			$attachment_updated = array(
				'ID'          => $attachment,
				'post_parent' => $message,
			);
			wp_update_post($attachment_updated);
			update_post_meta($attachment, 'cqpim', true);       
			$attachment_links[] = get_attached_file( $attachment );
		}
	}
	$recipients = explode(',', $recipients);
	$content = get_option('cqpim_new_message_content');
	$subject_template = get_option('cqpim_new_message_subject');
	foreach ( $recipients as $recipient ) {
		$content = get_option('cqpim_new_message_content');
		$subject_template = get_option('cqpim_new_message_subject');
		$recip = get_user_by('id', $recipient);
		$subject_template = str_replace('%%CONVERSATION_ID%%', '[' . $conversation . ']', $subject_template);
		$subject_template = str_replace('%%SENDER_NAME%%', $user->display_name, $subject_template);
		$content = str_replace('%%RECIPIENT_NAME%%', $recip->display_name, $content);
		$content = str_replace('%%SENDER_NAME%%', $user->display_name, $content);
		$content = str_replace('%%CONVERSATION_SUBJECT%%', $subject, $content);
		$content = str_replace('%%MESSAGE%%', $update, $content);
		$content = pto_replacement_patterns($content, $message, '');
		if ( $recip->ID != $user->ID ) {
			pto_send_emails( $recip->user_email, $subject_template, $content, '', $attachment_links, 'sales' );
		}
	}
	if ( ! empty($client) ) {
		$dash_page = get_option('cqpim_client_page');
		pto_send_json( array( 
			'error'    => false,
			'redirect' => get_the_permalink($dash_page) . '?page=messages&conversation=' . $conversation_id . '&convcreated=true',
		) );             
	} else {
		pto_send_json( array( 
			'error'    => false,
			'redirect' => admin_url() . 'admin.php?page=pto-messages&conversation=' . $conversation_id . '&convcreated=true',
		) ); 
	}
}

add_action( "wp_ajax_pto_create_conversation_reply", "pto_create_conversation_reply" );
function pto_create_conversation_reply() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$conversation = isset($_POST['conversation']) ? sanitize_text_field( wp_unslash( $_POST['conversation'] ) ) : '';
	$update = isset($_POST['message']) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';
	$attachments = isset($_POST['attachments']) ? sanitize_text_field( wp_unslash( $_POST['attachments'] ) ) : array();
	$client = isset($_POST['client']) ? sanitize_text_field( wp_unslash( $_POST['client'] ) ) : '';
	if ( empty($conversation) || empty($update) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('Please add a message.', 'projectopia-core'),
		) ); 
	}
	$conversation = get_post($conversation);
	$subject = $conversation->post_title;
	$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
	$recipients = get_post_meta($conversation->ID, 'recipients', true);
	update_post_meta($conversation->ID, 'updated', array(
		'by' => $user->ID,
		'at' => time(),
	));
	$message = array(
		'post_type'    => 'cqpim_messages',
		'post_status'  => 'private',
		'post_title'   => '',
		'post_content' => '',
	);
	$message = wp_insert_post($message);
	if ( ! is_wp_error($message) ) {
		update_post_meta($message, 'conversation_id', $conversation_id);
		update_post_meta($message, 'sender', $user->ID);
		update_post_meta($message, 'message', wpautop($update));
		update_post_meta($message, 'stamp', time());
		update_post_meta($message, 'read', array( $user->ID ));
		$attachments = explode(',', $attachments);
		$attachment_links = array();
		foreach ( $attachments as $attachment ) {
			$attachment_updated = array(
				'ID'          => $attachment,
				'post_parent' => $message,
			);
			wp_update_post($attachment_updated);
			update_post_meta($attachment, 'cqpim', true);   
			$attachment_links[] = get_attached_file( $attachment );
		}
	}
	foreach ( $recipients as $recipient ) {
		$recip = get_user_by('id', $recipient);
		$content = get_option('cqpim_new_message_content');
		$subject_template = get_option('cqpim_new_message_subject');
		$subject_template = str_replace('%%CONVERSATION_ID%%', '[' . $conversation->ID . ']', $subject_template);
		$subject_template = str_replace('%%SENDER_NAME%%', $user->display_name, $subject_template);
		$content = str_replace('%%RECIPIENT_NAME%%', $recip->display_name, $content);
		$content = str_replace('%%SENDER_NAME%%', $user->display_name, $content);
		$content = str_replace('%%CONVERSATION_SUBJECT%%', $subject, $content);
		$content = str_replace('%%MESSAGE%%', $update, $content);
		$content = pto_replacement_patterns($content, $message, '');
		if ( $recip->ID != $user->ID ) {
			pto_send_emails( $recip->user_email, $subject_template, $content, '', $attachment_links, 'sales' );
		}
	}
	pto_send_json( array( 
		'error' => false,
	) );         
}

add_action( "wp_ajax_pto_delete_conversation", "pto_delete_conversation" );
function pto_delete_conversation() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$conversation = isset($_POST['conversation']) ? sanitize_text_field( wp_unslash( $_POST['conversation'] ) ) : '';
	$client = isset($_POST['client']) ? sanitize_text_field( wp_unslash( $_POST['client'] ) ) : '';
	if ( empty($conversation) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('The conversation ID is missing, please try again.', 'projectopia-core'),
		) ); 
	}   
	$conversation_obj = get_post($conversation);
	$conversation_id = get_post_meta($conversation_obj->ID, 'conversation_id', true);
	$args = array(
		'post_type'      => 'cqpim_messages',
		'posts_per_page' => -1,
		'post_status'    => 'private',
		'meta_query'     => array(
			array(
				'key'     => 'conversation_id',
				'value'   => $conversation_id,
				'compare' => '=',
			),
		),
		'order'          => 'DESC',
		'orderby'        => 'meta_value',
		'meta_key'       => 'stamp',
	);
	$messages = get_posts($args);
	foreach ( $messages as $message ) {
		wp_delete_post($message->ID, true);
	}
	wp_delete_post($conversation_obj->ID, true);
	if ( ! empty($client) ) {
		$dash_page = get_option('cqpim_client_page');
		pto_send_json( array( 
			'error'    => false,
			'redirect' => get_the_permalink($dash_page) . '?page=messages&convdeleted=true',
		) );             
	} else {
		pto_send_json( array( 
			'error'    => false,
			'redirect' => admin_url() . 'admin.php?page=pto-messages&convdeleted=true',
		) ); 
	}
}

add_action( "wp_ajax_pto_edit_conversation_title", "pto_edit_conversation_title" );
function pto_edit_conversation_title() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$conversation = isset($_POST['conversation']) ? sanitize_text_field( wp_unslash( $_POST['conversation'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$attachments = isset($_POST['attachments']) ? sanitize_text_field( wp_unslash( $_POST['attachments'] ) ) : array();
	if ( empty($title) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('Please add a subject.', 'projectopia-core'),
		) ); 
	}
	$conversation = get_post($conversation);
	$conversation_updated = array(
		'ID'         => $conversation->ID,
		'post_title' => $title,
	);
	wp_update_post($conversation_updated);
	$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
	update_post_meta($conversation->ID, 'updated', array(
		'by' => $user->ID,
		'at' => time(),
	));
	$message = array(
		'post_type'    => 'cqpim_messages',
		'post_status'  => 'private',
		'post_title'   => '',
		'post_content' => '',
	);
	$message = wp_insert_post($message);
	if ( ! is_wp_error($message) ) {
		update_post_meta($message, 'conversation_id', $conversation_id);
		update_post_meta($message, 'sender', $user->ID);
		/* translators: %1$s: User Name, %2$s: Conversatition Title */
		$update = sprintf(esc_html__('%1$s changed the conversation subject to "%2$s"', 'projectopia-core'), $user->display_name, $title);
		update_post_meta($message, 'message', $update);
		update_post_meta($message, 'system', true);
		update_post_meta($message, 'stamp', time());
		update_post_meta($message, 'read', array( $user->ID ));
	}
	pto_send_json( array( 
		'error' => false,
	) );         
}

add_action( "wp_ajax_pto_remove_conversation_user", "pto_remove_conversation_user" );
function pto_remove_conversation_user() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$current_user = wp_get_current_user();
	$conversation = isset($_POST['conversation']) ? sanitize_text_field( wp_unslash( $_POST['conversation'] ) ) : '';
	$user = isset($_POST['user']) ? sanitize_text_field( wp_unslash( $_POST['user'] ) ) : '';
	$client = isset($_POST['client']) ? sanitize_text_field( wp_unslash( $_POST['client'] ) ) : '';
	if ( empty($user) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('No user selected.', 'projectopia-core'),
		) );             
	}
	$user = get_user_by('ID', $user);
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$conversation = get_post($conversation);
	delete_post_meta($conversation->ID, 'member_' . $user->ID);
	$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
	$recipients = get_post_meta($conversation->ID, 'recipients', true);
	foreach ( $recipients as $key => $recipient ) {
		if ( $recipient == $user->ID ) {
			unset($recipients[ $key ]);
		}
	}
	update_post_meta($conversation->ID, 'recipients', $recipients);
	update_post_meta($conversation->ID, 'updated', array(
		'by' => $current_user->ID,
		'at' => time(),
	));
	$message = array(
		'post_type'    => 'cqpim_messages',
		'post_status'  => 'private',
		'post_title'   => '',
		'post_content' => '',
	);
	$message = wp_insert_post($message);
	if ( ! is_wp_error($message) ) {
		update_post_meta($message, 'conversation_id', $conversation_id);
		update_post_meta($message, 'sender', $user->ID);
		if ( $type == 'leave' ) {
			/* translators: %s: Participants Name */
			$update = sprintf(esc_html__('%s has left the conversation', 'projectopia-core'), $user->display_name);
		} else {
			/* translators: %1$s: Remover, %2$s: Participants Name */
			$update = sprintf(esc_html__('%1$s has removed %2$s from the conversation', 'projectopia-core'), $current_user->display_name, $user->display_name);
		}
		update_post_meta($message, 'message', $update);
		update_post_meta($message, 'system', true);
		update_post_meta($message, 'stamp', time());
		update_post_meta($message, 'read', array( $user->ID ));
	}
	$recipients = get_post_meta($conversation->ID, 'recipients', true);
	if ( empty($recipients) ) {
		$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
		$args = array(
			'post_type'      => 'cqpim_messages',
			'posts_per_page' => -1,
			'post_status'    => 'private',
			'meta_query'     => array(
				array(
					'key'     => 'conversation_id',
					'value'   => $conversation_id,
					'compare' => '=',
				),
			),
			'order'          => 'DESC',
			'orderby'        => 'meta_value',
			'meta_key'       => 'stamp',
		);
		$messages = get_posts($args);
		foreach ( $messages as $message ) {
			wp_delete_post($message->ID, true);
		}
		wp_delete_post($conversation->ID, true);            
	}
	if ( $type == 'leave' ) {
		if ( ! empty($client) ) {
			$dash_page = get_option('cqpim_client_page');
			pto_send_json( array( 
				'error'    => false,
				'redirect' => get_the_permalink($dash_page) . '?page=messages&conversation=' . $conversation_id . '&convleft=true',
			) );             
		} else {
			pto_send_json( array( 
				'error'    => false,
				'redirect' => admin_url() . 'admin.php?page=pto-messages&convleft=true',
			) ); 
		}
	} else {
		if ( ! empty($client) ) {
			$dash_page = get_option('cqpim_client_page');
			pto_send_json( array( 
				'error'    => false,
				'redirect' => get_the_permalink($dash_page) . '?page=messages&conversation=' . $conversation_id . '&convremoved=true',
			) );                 
		} else {
			pto_send_json( array( 
				'error'    => false,
				'redirect' => admin_url() . 'admin.php?page=pto-messages&conversation=' . $conversation_id . '&convremoved=true',
			) ); 
		}
	}
}

add_action( "wp_ajax_pto_add_conversation_user", "pto_add_conversation_user" );
function pto_add_conversation_user() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$current_user = wp_get_current_user();
	$conversation = isset($_POST['conversation']) ? sanitize_text_field( wp_unslash( $_POST['conversation'] ) ) : '';
	$new_recipients = isset($_POST['recipients']) ? sanitize_text_field( wp_unslash( $_POST['recipients'] ) ) : '';
	if ( empty($new_recipients) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('No users selected.', 'projectopia-core'),
		) );                     
	}
	$conversation = get_post($conversation);
	$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
	$new_recipients = explode(',', $new_recipients);
	$recipients = get_post_meta($conversation->ID, 'recipients', true);
	foreach ( $new_recipients as $key => $recipient ) {
		$recipients[] = $recipient;
		update_post_meta($conversation->ID, 'member_' . $recipient, $recipient);
	}
	update_post_meta($conversation->ID, 'recipients', $recipients);
	update_post_meta($conversation->ID, 'updated', array(
		'by' => $current_user->ID,
		'at' => time(),
	));
	foreach ( $new_recipients as $recipient ) {
		$recip = get_user_by('id', $recipient);
		$message = array(
			'post_type'    => 'cqpim_messages',
			'post_status'  => 'private',
			'post_title'   => '',
			'post_content' => '',
		);
		$message = wp_insert_post($message);
		if ( ! is_wp_error($message) ) {
			update_post_meta($message, 'conversation_id', $conversation_id);
			update_post_meta($message, 'sender', $user->ID);
			/* translators: %1$s: User Name, %2$s: New Participants */
			$update = sprintf(esc_html__('%1$s has added %2$s to the conversation', 'projectopia-core'), $current_user->display_name, $recip->display_name);               
			update_post_meta($message, 'message', $update);
			update_post_meta($message, 'system', true);
			update_post_meta($message, 'stamp', time());
			update_post_meta($message, 'read', array( $user->ID ));
		}
		$content = get_option('cqpim_new_message_content');
		$subject_template = get_option('cqpim_new_message_subject');
		$subject_template = str_replace('%%CONVERSATION_ID%%', '[' . $conversation->ID . ']', $subject_template);
		$subject_template = str_replace('%%SENDER_NAME%%', $current_user->display_name, $subject_template);
		$content = str_replace('%%RECIPIENT_NAME%%', $recip->display_name, $content);
		$content = str_replace('%%SENDER_NAME%%', $current_user->display_name, $content);
		$content = str_replace('%%CONVERSATION_SUBJECT%%', $conversation->post_title, $content);
		$content = str_replace('%%MESSAGE%%', $update, $content);
		$content = pto_replacement_patterns($content, $message, '');
		if ( $recip->ID != $user->ID ) {
			pto_send_emails( $recip->user_email, $subject_template, $content, '', '', 'sales' );
		}
	}
	pto_send_json( array( 
		'error'    => false,
		'redirect' => admin_url() . 'admin.php?page=pto-messages&conversation=' . $conversation_id . '&convadded=true',
	) ); 
}