<?php
function pto_send_unknown_account_email( $fromEmail, $fromName, $type ) {
	if ( empty($type) ) {
		$type = __('item', 'projectopia-core');
	}
	$email_subject = get_option('cqpim_bounce_subject');
	$email_content = get_option('cqpim_bounce_content');
	$email_content = str_replace('%%SENDER_NAME%%', $fromName, $email_content);
	$email_content = str_replace('%%TYPE%%', $type, $email_content);
	$email_content = pto_replacement_patterns($email_content, 0, '');
	$attachments = array();
	$to = $fromEmail;               
	pto_send_emails($to, $email_subject, $email_content, '', $attachments, 'sales');
}

/**
 * Function to create new support for unknown emails.
 *
 * @since 4.3.4
 *
 * @param Object $user      User object.
 * @param string $title     Email subject.
 * @param string $content   Email content.
 * @param string $fromName  Email sender name.
 * @param string $fromEmail Sender email address. 
 */
function pto_create_support_ticket_unknown_email( $user, $title, $content, $attached_media, $fromName, $fromEmail ) {
	$array = current_datetime();
	$now = $array->getTimestamp() + $array->getOffset();
	$title                = isset( $title ) ? $title : ( "Email Ticket - " . gmdate( "Y-m-d H:i:s", $now ) );
	$client_object_id     = 0;
	$user_obj_id          = ! empty($user) ? $user->ID : 0;
	$unknown_email_sender = $fromName . " ($fromEmail) ";
	$client_details       = get_post_meta( $client_object_id, 'client_details', true);

	$new_ticket = array(
		'post_type'    => 'cqpim_support',
		'post_status'  => 'private',
		'post_content' => '',
		'post_title'   => $title,
		'post_author'  => 0,
	);
	$ticket_pid = wp_insert_post($new_ticket, true);

	if ( ! is_wp_error( $ticket_pid ) ) {
		wp_update_post(
			array(
				'ID'        => $ticket_pid,
				'post_name' => $ticket_pid, 
			)
		);

		$ticket_changes = array();
		$dirs           = wp_upload_dir();
		// Add all attchements in support ticket. 
		if ( ! empty ( $attached_media ) ) {
			$allowed = get_allowed_mime_types($user);
			$extensions = array();
			foreach ( $allowed as $key => $mime ) {
				$keys = explode('|', $key);
				foreach ( $keys as $extension ) {
					$extensions[] = $extension;
				}
			}
			foreach ( $attached_media as $file ) {
				$extension = pathinfo($file->name, PATHINFO_EXTENSION);
				if ( ! in_array(strtolower($extension), $extensions) ) {
					break;
				}
				$filename = basename($file->filePath);
				$file_URL = $dirs['baseurl'] . '/pto-uploads/' . $filename;
				$attachment = array(
					'post_mime_type' => $extension,
					'post_title'     => $filename,
					'post_content'   => '',
					'post_parent'    => $ticket_pid,
					'post_status'    => 'publish',
					'guid'           => $file_URL,
					'post_author'    => $client_object_id,
				);
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attachment_id = wp_insert_attachment( $attachment, $file_URL, $ticket_pid );
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
				wp_update_attachment_metadata($attachment_id, $attachment_data);
				update_post_meta( $attachment_id, 'cqpim', true );
				/* translators: %s: Uploaded File Name */
				$ticket_changes[] = sprintf( __('Uploaded file: %s', 'projectopia-core'), $filename );
				$attachments[]    = $file_URL;
			}
		}

		// Update support ticket meta.
		update_post_meta($ticket_pid, 'ticket_client', $client_object_id);
		update_post_meta($ticket_pid, 'ticket_status', 'open');
		$ticket_updates = array();
		$ticket_updates[] = array(
			'details' => $content,
			'time'    => time(),
			'name'    => $unknown_email_sender,
			'email'   => $fromEmail,
			'user'    => $client_object_id,
			'type'    => 'client',
			'changes' => $ticket_changes,
		);
		update_post_meta($ticket_pid, 'ticket_updates', $ticket_updates);
		update_post_meta($ticket_pid, 'ticket_priority', 'normal');
		update_post_meta($ticket_pid, 'last_updated', time() );

		// bell-icon Notification to all other team member.
		$team_members = get_posts(
			array(
				'post_type'      => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status'    => 'private',
			)
		);
		foreach ( $team_members as $member ) {
			$team_details = get_post_meta($member->ID, 'team_details', true);
			$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
			$user_obj = get_user_by('id', $user_id);
			if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_supports') ) {
				pto_add_team_notification($member->ID, $user_obj_id, $ticket_pid, 'new_ticket');
			}
		}

		// Prepare and Send email notification.
		$email_subject = get_option('client_create_ticket_subject');
		$email_content = get_option('client_create_ticket_email');
		$email_subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $ticket_pid . ']', $email_subject);
		$email_subject = str_replace('%%CLIENT_NAME%%', $unknown_email_sender, $email_subject);
		$email_content = str_replace('%%CLIENT_NAME%%', $unknown_email_sender, $email_content);
		$email_subject = pto_replacement_patterns($email_subject, $client_object_id, 'client');
		$email_subject = pto_replacement_patterns($email_subject, $ticket_pid, 'ticket');
		$email_content = pto_replacement_patterns($email_content, $client_object_id, 'client');
		$email_content = pto_replacement_patterns($email_content, $ticket_pid, 'ticket');

		$recipient = get_option('company_support_email');
		if ( ! empty( $recipient ) ) {
			pto_send_emails($recipient, $email_subject, $email_content, '', $attachments, 'support');
		}
	}
}

add_action( "wp_ajax_pto_test_piping", "pto_test_piping" );
function pto_test_piping() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$mail_server2 = isset($_POST['cqpim_mail_server']) ? sanitize_text_field( wp_unslash( $_POST['cqpim_mail_server'] ) ) : '';
	$mail_server = '{' . $mail_server2 . '}';
	$mailbox_name = isset($_POST['cqpim_mailbox_name']) ? sanitize_text_field( wp_unslash( $_POST['cqpim_mailbox_name'] ) ) : '';
	$mailbox_pass = isset($_POST['cqpim_mailbox_pass']) ? sanitize_text_field( wp_unslash( $_POST['cqpim_mailbox_pass'] ) ) : '';
	if ( empty($mail_server2) || empty($mailbox_name) || empty($mailbox_pass) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('You must complete all fields', 'projectopia-core'),
		) );         
	}
	$mbox = imap_open($mail_server, $mailbox_name, $mailbox_pass);
	if ( ! empty($mbox) ) {
		pto_send_json( array( 
			'error'   => false,
			'message' => __('Settings are correct.', 'projectopia-core'),
		) );             
	} else {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('Failed to connect to mailbox', 'projectopia-core'),
		) );     
	}
}
add_action('pto_check_email_pipe', 'pto_check_email_pipe_minute');
function pto_check_email_pipe_minute() {
	$mail_server = '{' . get_option('cqpim_mail_server') . '}';
	$mailbox_name = get_option('cqpim_mailbox_name');
	$mailbox_pass = get_option('cqpim_mailbox_pass');

	//Check imap extension is exist ot not.
	if ( ! function_exists( 'imap_open') ) {
		return;
	}

	//check for variables
	if ( empty($mail_server) || empty($mailbox_name) || empty($mailbox_pass) ) {
		return;
	}

	$test = imap_open($mail_server, $mailbox_name, $mailbox_pass);
	if ( ! empty($test) ) {
		require_once(PTO_PATH . 'assets/php-imap/src/PhpImap/IncomingMail.php');
		require_once(PTO_PATH . 'assets/php-imap/src/PhpImap/Mailbox.php');
		$dirs = wp_upload_dir();
		$mailbox = new PhpImap\Mailbox($mail_server, $mailbox_name, $mailbox_pass, $dirs['basedir'] . '/pto-uploads');
		$mailsIds = $mailbox->searchMailbox('UNSEEN');
		if ( ! $mailsIds ) {
			die('Mailbox is empty');
		}  
	$delete = get_option('cqpim_piping_delete');
	foreach ( $mailsIds as $key => $message ) {
		$mail = $mailbox->getMail($message);
		$attached_media = $mail->getAttachments();
		$body = $mail->textPlain;
		$fromName = $mail->fromName;
		$toName = $mail->toString;
		$toEmail = $mail->to;
		$fromEmail = $mail->fromAddress;
		$subject = $mail->subject;  
		$subject_full = $mail->subject;
		// Remove the original message
		$body_array = explode("\n",$mail->textPlain);
		$content = "";
		foreach ( $body_array as $key => $value ) {
			if ( $value == "_________________________________________________________________" ) {
				break;
			} elseif ( preg_match("/^From:(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^-*(.*)Original Message(.*)-*/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^On(.*)wrote:(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^On(.*)$fromName(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^On(.*)$toName(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^(.*)$toEmail(.*)wrote:(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^(.*)$fromEmail(.*)wrote:(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^>(.*)/i",$value,$matches) ) {
				break;
			} elseif ( preg_match("/^---(.*)On(.*)wrote:(.*)/i",$value,$matches) ) {
				break;
			} else {
				$content .= "$value\n";
			}
		}
		// Get the ID of the item	
		preg_match("/\[(.*?)\]/", $subject, $subject);
		$subject = $subject[1];
		$post_id = preg_replace('/[^0-9]/','', $subject);
		// Get the Item
		$attachments = array();
		if ( empty($post_id) ) {
			// This is where we create a new ticket, but only for registered
			$value = get_option('cqpim_create_support_on_email');
			if ( ! empty($value) ) {
				$item_type = __('support ticket', 'projectopia-core');
				$user = get_user_by('email', $fromEmail);
				if ( empty($user) ) {
					/** Create support ticket from unknown emails. */
					if ( ! empty( get_option('cqpim_create_support_on_unknown_email') ) ) {
						pto_create_support_ticket_unknown_email( $user, $subject_full, $content, $attached_media, $fromName, $fromEmail );
					}
					/** Send reject email to unknown emails. */
					$value = get_option('cqpim_send_piping_reject');
					if ( ! empty($value) ) {
						pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					break;
				}
				$args = array(
					'post_type'      => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$members = get_posts($args);
				foreach ( $members as $member ) {
					$team_details = get_post_meta($member->ID, 'client_details', true);
					if ( $team_details['user_id'] == $user->ID ) {
						$assigned = $member->ID;
						$client_type = 'admin';
					}
					if ( $team_details['client_email'] == $fromEmail ) {
						$valid = true;
					}
				} 
				if ( empty($assigned) ) {
					foreach ( $members as $member ) {
						$team_ids = get_post_meta($member->ID, 'client_ids', true);
						$client_contacts = get_post_meta($member->ID, 'client_contacts', true);
						if ( ! is_array($team_ids) ) {
							$team_ids = array( $team_ids );
						}
						if ( in_array($user->ID, $team_ids) ) {
							$assigned = $member->ID;
							$client_type = 'contact';
						}
						if ( empty($client_contacts) ) {
							$client_contacts = array();
						}
						foreach ( $client_contacts as $contact ) {
							if ( $contact['email'] == $fromEmail ) {
								$valid = true;
							}
						}
					}           
				}
				if ( empty($valid) ) {
					/** Create support ticket from unknown emails. */
					if ( ! empty( get_option('cqpim_create_support_on_unknown_email') ) ) {
						pto_create_support_ticket_unknown_email( $user, $subject_full, $content, $attached_media, $fromName, $fromEmail );
					}
					/** Send reject email to unknown emails. */
					$value = get_option('cqpim_send_piping_reject');
					if ( ! empty($value) ) {
						pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					break;                  
				}
				$title = isset($subject_full) ? $subject_full : __('Email ticket', 'projectopia-core');
				$priority = 'normal';
				$details = $content;
				$client_object_id = $assigned;
				$ticket_status = 'open';
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
					if ( empty( $title ) ) {
						$ticket_updated['post_title'] = "Email Ticket - " . gmdate( "Y-m-d H:i:s" );
					}
					wp_update_post( $ticket_updated );  
					if ( ! empty($attached_media) ) {
						$allowed = get_allowed_mime_types($user);
						$extensions = array();
						foreach ( $allowed as $key => $mime ) {    
							$keys = explode('|', $key); 
							foreach ( $keys as $extension ) {      
								$extensions[] = $extension;     
							}   
						}
						$ticket_changes = array();
						$i = 0;
						foreach ( $attached_media as $file ) {
							$extension = pathinfo($file->name, PATHINFO_EXTENSION);
							if ( ! in_array(strtolower($extension), $extensions) ) {
								break;
							}
							$filename = $file->filePath;
							$filename = substr($filename, strrpos($filename, '/') + 1);
							$attachment = array(
								'post_mime_type' => $extension,
								'post_title'     => $file->name,
								'post_content'   => '',
								'post_parent'    => $ticket_pid,
								'post_status'    => 'publish',
								'guid'           => $dirs['baseurl'] . '/pto-uploads/' . $filename,
								'post_author'    => $user->ID,
							);
							$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/pto-uploads/' . $filename );   
							require_once(ABSPATH . 'wp-admin/includes/image.php');
							$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
							wp_update_attachment_metadata( $attachment_id, $attachment_data );          
							$attachment_obj = wp_get_attachment_metadata( $attachment );
							update_post_meta($attachment_id, 'cqpim', true);
							$attachment = get_post($attachment_id);
							$filename = $attachment->post_title;
							/* translators: %s: Uploaded File Name */
							$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename); 
							$i++;
							$attachments[] = $file->filePath;
						}
					}
					update_post_meta($ticket_pid, 'ticket_client', $client_object_id);
					update_post_meta($ticket_pid, 'ticket_status', $ticket_status);
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
					if ( ! empty($client_details['ticket_assignee']) ) {
						update_post_meta($ticket_pid, 'ticket_owner', $client_details['ticket_assignee']);
					}
					$last_updated = time();
					update_post_meta($ticket_pid, 'last_updated', $last_updated);
					$to = array();
					$to[] = get_option('company_support_email');
					if ( ! empty($client_details['ticket_assignee']) ) {
						$assignee_details = get_post_meta($client_details['ticket_assignee'], 'team_details', true);
						if ( ! empty($assignee_details['team_email']) ) {
							$to[] = $assignee_details['team_email'];
						}
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
						$user_obj = get_user_by('id', $user_id);
						if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_supports') ) {
							pto_add_team_notification($member->ID, $user->ID, $ticket_pid, 'new_ticket');
						}
					}                   
					if ( $priority == 'high' || $priority == 'immediate' ) {
						add_filter('phpmailer_init','update_priority_mailer');
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
						pto_send_emails($recip, $email_subject, $email_content, '', $attachments, 'support');
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					exit;                   
				}
			} else {
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;
			}
		}
		$post = get_post($post_id);
		if ( empty($post) ) {
			die('No post');
		}
		$post_type = $post->post_type;
		if ( $post_type == 'cqpim_support' ) {
			$item_type = __('support ticket', 'projectopia-core');
			$user = get_user_by('email', $fromEmail);
			if ( empty($user) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;
			}
			foreach ( $user->roles as $role ) {
				if ( $role == 'cqpim_client' ) {
					$type = 'client';
				} else {
					$type = 'team';
				}
			}
			if ( $type == 'client' ) {
				$args = array(
					'post_type'      => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$members = get_posts($args);
				foreach ( $members as $member ) {
					$team_details = get_post_meta($member->ID, 'client_details', true);
					if ( $team_details['user_id'] == $user->ID ) {
						$assigned = $member->ID;
						$client_type = 'admin';
					}
					if ( $team_details['client_email'] == $fromEmail ) {
						$valid = true;
					}
				} 
				if ( empty($assigned) ) {
					foreach ( $members as $member ) {
						$team_ids = get_post_meta($member->ID, 'client_ids', true);
						$client_contacts = get_post_meta($member->ID, 'client_contacts', true);
						if ( in_array($user->ID, $team_ids) ) {
							$assigned = $member->ID;
							$client_type = 'contact';
						}
						if ( empty($client_contacts) ) {
							$client_contacts = array();
						}
						foreach ( $client_contacts as $contact ) {
							if ( $contact['email'] == $fromEmail ) {
								$valid = true;
							}
						}
					}           
				}
				$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
				if ( empty($valid) || $ticket_client != $assigned ) {
					$value = get_option('cqpim_send_piping_reject');
					if ( ! empty($value) ) {
						pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					exit;                   
				}
				if ( ! empty($attached_media) ) {
					$allowed = get_allowed_mime_types($user);
					$extensions = array();
					foreach ( $allowed as $key => $mime ) {    
						$keys = explode('|', $key); 
						foreach ( $keys as $extension ) {      
							$extensions[] = $extension;     
						}   
					}
					$ticket_changes = array();
					$i = 0;
					foreach ( $attached_media as $file ) { 
						$extension = pathinfo($file->name, PATHINFO_EXTENSION);
						if ( ! in_array(strtolower($extension), $extensions) ) {
							break;
						}                   
						$filename = $file->filePath;
						$filename = substr($filename, strrpos($filename, '/') + 1);
						$attachment = array(
							'post_mime_type' => $extension,
							'post_title'     => $file->name,
							'post_content'   => '',
							'post_parent'    => $post->ID,
							'post_status'    => 'publish',
							'guid'           => $dirs['baseurl'] . '/pto-uploads/' . $filename,
							'post_author'    => $user->ID,
						);
						$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/pto-uploads/' . $filename );   
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );          
						$attachment_obj = wp_get_attachment_metadata( $attachment );
						update_post_meta($attachment_id, 'cqpim', true);
						$attachment = get_post($attachment_id);
						$filename = $attachment->post_title;
						/* translators: %s: Uploaded File Name */
						$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename); 
						$i++;
						$attachments[] = $file->filePath;
					}
				}
				$status = get_post_meta($post_id, 'ticket_status', true);   
				$priority = get_post_meta($post_id, 'ticket_priority', true);
				$owner = get_post_meta($post_id, 'ticket_owner', true);
				$watchers = get_post_meta($post_id, 'ticket_watchers', true);
				$data = array(
					'ticket_update_new'   => $content,
					'ticket_priority_new' => $priority,
					'ticket_status_new'   => $status,
					'ticket_owner'        => $owner,
					'task_watchers'       => $watchers,
					'ticket_changes'      => $ticket_changes,
				);
				$files = array();
				pto_update_support_ticket($post_id, $data, $files, 'client', $user, $attachments);
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
			}               
			if ( $type == 'team' ) {
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
						$client_type = 'admin';
					}
					if ( $team_details['team_email'] == $fromEmail ) {
						$valid = true;
					}
				} 
				if ( empty($valid) ) {
					$value = get_option('cqpim_send_piping_reject');
					if ( ! empty($value) ) {
						pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					exit;                   
				}
				if ( ! empty($attached_media) ) {
					$allowed = get_option('cqpim_allowed_extensions');
					$allowed = explode(',', $allowed);
					$ticket_changes = array();
					$i = 0;
					foreach ( $attached_media as $file ) {
						$extension = pathinfo($file->name, PATHINFO_EXTENSION);
						if ( ! in_array(strtolower($extension), $allowed) ) {
							break;
						}                                   
						$filename = $file->filePath;
						$filename = substr($filename, strrpos($filename, '/') + 1);
						$attachment = array(
							'post_mime_type' => $extension,
							'post_title'     => $file->name,
							'post_content'   => '',
							'post_parent'    => $post->ID,
							'post_status'    => 'publish',
							'guid'           => $dirs['baseurl'] . '/cqpim-uploads/' . $filename,
							'post_author'    => $user->ID,
						);
						$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/cqpim-uploads/' . $filename ); 
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );          
						$attachment_obj = wp_get_attachment_metadata( $attachment );
						update_post_meta($attachment_id, 'cqpim', true);
						$attachment = get_post($attachment_id);
						$filename = $attachment->post_title;
						/* translators: %s: Uploaded File Name */
						$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename); 
						$i++;
						$attachments[] = $file->filePath;
					}
				}
				$status = get_post_meta($post_id, 'ticket_status', true);   
				$priority = get_post_meta($post_id, 'ticket_priority', true);
				$owner = get_post_meta($post_id, 'ticket_owner', true);
				$watchers = get_post_meta($post_id, 'ticket_watchers', true);
				$data = array(
					'ticket_update_new'   => $content,
					'ticket_priority_new' => $priority,
					'ticket_status_new'   => $status,
					'ticket_owner'        => $owner,
					'task_watchers'       => $watchers,
					'ticket_changes'      => $ticket_changes,
				);
				$files = array();
				pto_update_support_ticket($post_id, $data, $files, 'admin', $user, $attachments);
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
			}               
		}
		if ( $post_type == 'cqpim_tasks' ) {
			$item_type = __('task', 'projectopia-core');
			$user = get_user_by('email', $fromEmail);
			if ( empty($user) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;
			}
			foreach ( $user->roles as $role ) {
				if ( $role == 'cqpim_client' ) {
					$type = 'client';
				} else {
					$type = 'team';
				}
			}
			if ( $type == 'client' ) {
				$args = array(
					'post_type'      => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$members = get_posts($args);
				foreach ( $members as $member ) {
					$team_details = get_post_meta($member->ID, 'client_details', true);
					if ( $team_details['user_id'] == $user->ID ) {
						$assigned = $member->ID;
						$client_type = 'admin';
					}
					if ( $team_details['client_email'] == $fromEmail ) {
						$valid = true;
					}
				} 
				if ( empty($assigned) ) {
					foreach ( $members as $member ) {
						$team_ids = get_post_meta($member->ID, 'client_ids', true);
						$client_contacts = get_post_meta($member->ID, 'client_contacts', true);
						if ( in_array($user->ID, $team_ids) ) {
							$assigned = $member->ID;
							$client_type = 'contact';
						}
						if ( empty($client_contacts) ) {
							$client_contacts = array();
						}
						foreach ( $client_contacts as $contact ) {
							if ( $contact['email'] == $fromEmail ) {
								$valid = true;
							}
						}
					}           
				}
				if ( empty($valid) ) {
					$value = get_option('cqpim_send_piping_reject');
					if ( ! empty($value) ) {
						pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					exit;                   
				}
				update_post_meta($post_id, 'client_updated', false);
				$project_id = get_post_meta($post_id, 'project_id', true);
				$task_watchers = get_post_meta($post_id, 'task_watchers', true);
				$task_owner = get_post_meta($post_id, 'owner', true);
				$project_progress = get_post_meta($project_id, 'project_progress', true);
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$task_object = get_post($post_id);
				$task_title = $task_object->post_title;
				/* translators: %s: Task Title */
				$text = sprintf(esc_html__('Task Updated: %s', 'projectopia-core'), $task_title );
				$project_progress[] = array(
					'update' => $text,
					'date'   => time(),
					'by'     => $user->display_name,
				);
				update_post_meta($project_id, 'project_progress', $project_progress );  
				// Add message
				$task_messages = get_post_meta($post_id, 'task_messages', true);
				$date = time();
				$current_user = wp_get_current_user();
				$task_messages[] = array(
					'date'    => $date,
					'message' => $content,
					'by'      => $user->display_name,
					'author'  => $user->ID,
				);      
				update_post_meta($post_id, 'task_messages', $task_messages);
				if ( ! empty($attached_media) ) {
					$allowed = get_option('cqpim_allowed_extensions');
					$allowed = explode(',', $allowed);
					$ticket_changes = array();
					$i = 0;
					foreach ( $attached_media as $file ) {
						$extension = pathinfo($file->name, PATHINFO_EXTENSION);
						if ( ! in_array(strtolower($extension), $allowed) ) {
							break;
						}                                   
						$filename = $file->filePath;
						$filename = substr($filename, strrpos($filename, '/') + 1);
						$attachment = array(
							'post_mime_type' => $extension,
							'post_title'     => $file->name,
							'post_content'   => '',
							'post_parent'    => $post_id,
							'post_status'    => 'publish',
							'guid'           => $dirs['baseurl'] . '/cqpim-uploads/' . $filename,
							'post_author'    => $user->ID,
						);
						$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/cqpim-uploads/' . $filename ); 
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );          
						$attachment_obj = wp_get_attachment_metadata( $attachment );
						update_post_meta($attachment_id, 'cqpim', true);
						$attachment = get_post($attachment_id);
						$filename = $attachment->post_title;
						/* translators: %s: Uploaded File Name */
						$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename); 
						$i++;
						$attachments[] = $file->filePath;
					}
				}
				pto_send_task_updates($post_id, $project_id, $task_owner, $task_watchers, $content, $user, $attachments);                   
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
			}               
			if ( $type == 'team' ) {
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
						$client_type = 'admin';
					}
					if ( $team_details['team_email'] == $fromEmail ) {
						$valid = true;
					}
				} 
				if ( empty($valid) ) {
					$value = get_option('cqpim_send_piping_reject');
					if ( ! empty($value) ) {
						pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
					}
					$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
					exit;                   
				}
				update_post_meta($post_id, 'team_updated', true);
				$project_id = get_post_meta($post_id, 'project_id', true);
				$task_watchers = get_post_meta($post_id, 'task_watchers', true);
				$task_owner = get_post_meta($post_id, 'owner', true);
				$project_progress = get_post_meta($project_id, 'project_progress', true);
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$task_object = get_post($post_id);
				$task_title = $task_object->post_title;
				/* translators: %s: Task Title */
				$text = sprintf(esc_html__('Task Updated: %s', 'projectopia-core'), $task_title );
				$project_progress[] = array(
					'update' => $text,
					'date'   => time(),
					'by'     => $user->display_name,
				);
				update_post_meta($project_id, 'project_progress', $project_progress );  
				$task_messages = get_post_meta($post_id, 'task_messages', true);
				$date = time();
				$current_user = wp_get_current_user();
				$task_messages[] = array(
					'date'    => $date,
					'message' => $content,
					'by'      => $user->display_name,
					'author'  => $user->ID,
				);      
				update_post_meta($post_id, 'task_messages', $task_messages);
				if ( ! empty($attached_media) ) {
					$allowed = get_option('cqpim_allowed_extensions');
					$allowed = explode(',', $allowed);
					$ticket_changes = array();
					$i = 0;
					foreach ( $attached_media as $file ) {
						$extension = pathinfo($file->name, PATHINFO_EXTENSION);
						if ( ! in_array(strtolower($extension), $allowed) ) {
							break;
						}                                   
						$filename = $file->filePath;
						$filename = substr($filename, strrpos($filename, '/') + 1);
						$attachment = array(
							'post_mime_type' => $extension,
							'post_title'     => $file->name,
							'post_content'   => '',
							'post_parent'    => $post_id,
							'post_status'    => 'publish',
							'guid'           => $dirs['baseurl'] . '/cqpim-uploads/' . $filename,
							'post_author'    => $user->ID,
						);
						$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/cqpim-uploads/' . $filename ); 
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );          
						$attachment_obj = wp_get_attachment_metadata( $attachment );
						update_post_meta($attachment_id, 'cqpim', true);
						$attachment = get_post($attachment_id);
						$filename = $attachment->post_title;
						/* translators: %s: Uploaded File Name */
						$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename); 
						$i++;
						$attachments[] = $file->filePath;
					}
				}
				pto_send_task_updates($post_id, $project_id, $task_owner, $task_watchers, $content, $user, $attachments);
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
			}   
		}
		if ( $post_type == 'cqpim_conversations' ) {
			$item_type = __('conversation', 'projectopia-core');
			$user = get_user_by('email', $fromEmail);
			if ( empty($user) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;
			}
			foreach ( $user->roles as $role ) {
				if ( $role == 'cqpim_client' ) {
					$type = 'client';
				} else {
					$type = 'team';
				}
			}
			$conversation_obj = get_post($post->ID);
			$recipients = get_post_meta($conversation_obj->ID, 'recipients', true);
			if ( ! in_array($user->ID, $recipients) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;                   
			}
			$conversation_id = get_post_meta($conversation_obj->ID, 'conversation_id', true);
			update_post_meta($conversation_obj->ID, 'updated', array(
				'by' => $user->ID,
				'at' => time(),
			));
			// Set Variable for use in Email content replacement
			$conversation_title = $conversation_obj->post_title; // get conversation post title
			$last_message_update = $content; // save last message update before $content is updated
			$new_message = array(
				'post_type'    => 'cqpim_messages',
				'post_status'  => 'private',
				'post_title'   => '',
				'post_content' => '',
			);
			$new_message = wp_insert_post($new_message);
			if ( ! is_wp_error($new_message) ) {
				update_post_meta($new_message, 'conversation_id', $conversation_id);
				update_post_meta($new_message, 'sender', $user->ID);
				update_post_meta($new_message, 'message', $content);
				update_post_meta($new_message, 'piping', true);
				update_post_meta($new_message, 'stamp', time());
				update_post_meta($new_message, 'read', array( $user->ID ));
				$attachments = array();
				if ( ! empty($attached_media) ) {
					$allowed = get_option('cqpim_allowed_extensions');
					$allowed = explode(',', $allowed);
					$i = 0;
					foreach ( $attached_media as $file ) {
						$extension = pathinfo($file->name, PATHINFO_EXTENSION);
						if ( ! in_array(strtolower($extension), $allowed) ) {
							break;
						}                                   
						$filename = $file->filePath;
						$filename = substr($filename, strrpos($filename, '/') + 1);
						$attachment = array(
							'post_mime_type' => $extension,
							'post_title'     => $file->name,
							'post_content'   => '',
							'post_parent'    => $new_message,
							'post_status'    => 'publish',
							'guid'           => $dirs['baseurl'] . '/cqpim-uploads/' . $filename,
							'post_author'    => $user->ID,
						);
						$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/cqpim-uploads/' . $filename ); 
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
						wp_update_attachment_metadata( $attachment_id, $attachment_data );          
						$attachment_obj = wp_get_attachment_metadata( $attachment );
						update_post_meta($attachment_id, 'cqpim', true);
						$attachment = get_post($attachment_id);
						$filename = $attachment->post_title;
						$i++;
						$attachments[] = $file->filePath;
					}
				}                   
			}
			foreach ( $recipients as $recipient ) {
				$recip = get_user_by('id', $recipient);
				$content = get_option('cqpim_new_message_content');
				$subject_template = get_option('cqpim_new_message_subject');
				$subject_template = str_replace('%%CONVERSATION_ID%%', '[' . $conversation_obj->ID . ']', $subject_template);
				$subject_template = str_replace('%%SENDER_NAME%%', $user->display_name, $subject_template);
				$content = str_replace('%%RECIPIENT_NAME%%', $recip->display_name, $content);
				$content = str_replace('%%SENDER_NAME%%', $user->display_name, $content);
				$content = str_replace('%%CONVERSATION_SUBJECT%%', $conversation_title, $content);
				$content = str_replace('%%MESSAGE%%', $last_message_update, $content);
				$content = cqpim_replacement_patterns($content, $new_message, '');
				if ( $recip->ID != $user->ID ) {
					pto_send_emails( $recip->user_email, $subject_template, $content, '', $attachments, 'sales' );
				}
			}               
			$mailbox->markMailAsRead($message);
				if ( ! empty($delete) ) {
					$mailbox->deleteMail($message);
				}               
		}
		if ( $post_type == 'cqpim_bug' ) {
			$item_type = __('bug', 'projectopia-core');
			$user = get_user_by('email', $fromEmail);
			if ( empty($user) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;
			}
			$bug_obj = get_post($post->ID);
			$project = get_post_meta($post->ID, 'bug_project', true);
			$allowed_to_update = array();
			$project_contributors = get_post_meta($project, 'project_contributors', true);
			if ( ! empty($project_contributors) ) {
				foreach ( $project_contributors as $contributor ) {
					$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : ''; 
					$allowed_to_update[] = $user_id;
				}
			}
			$project_details = get_post_meta($project, 'project_details', true);
			$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
			$client_contacts = get_post_meta($client_id, 'client_contacts', true);
			$client_details = get_post_meta($client_id, 'client_details', true);
			$allowed_to_update[] = $client_details['user_id'];
			if ( ! empty($client_contacts) ) {
				foreach ( $client_contacts as $contact ) {
					$allowed_to_update[] = $contact['user_id'];
				}
			}           
			if ( ! in_array($user->ID, $allowed_to_update) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;                   
			}
			$files = array();
			if ( ! empty($attached_media) ) {
				$allowed = get_option('cqpim_allowed_extensions');
				$allowed = explode(',', $allowed);
				$i = 0;
				foreach ( $attached_media as $file ) {
					$extension = pathinfo($file->name, PATHINFO_EXTENSION);
					if ( ! in_array(strtolower($extension), $allowed) ) {
						break;
					}                                   
					$filename = $file->filePath;
					$filename = substr($filename, strrpos($filename, '/') + 1);
					$attachment = array(
						'post_mime_type' => $extension,
						'post_title'     => $file->name,
						'post_content'   => '',
						'post_parent'    => $new_message,
						'post_status'    => 'publish',
						'guid'           => $dirs['baseurl'] . '/cqpim-uploads/' . $filename,
						'post_author'    => $user->ID,
					);
					$attachment_id = wp_insert_attachment( $attachment, $dirs['baseurl'] . '/cqpim-uploads/' . $filename ); 
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file->filePath );
					wp_update_attachment_metadata( $attachment_id, $attachment_data );          
					$attachment_obj = wp_get_attachment_metadata( $attachment );
					update_post_meta($attachment_id, 'cqpim', true);
					$attachment = get_post($attachment_id);
					$filename = $attachment->post_title;
					$i++;
					$attachments[] = $file->filePath;
					$files[] = $attachment->ID;
				}
			}
			$title = $post->post_title;
			$bug_pid = $post->ID;
			$desc = get_post_meta($post->ID, 'bug_desc', true);
			$assignee = get_post_meta($post->ID, 'bug_assignee', true);
			$status = get_post_meta($post->ID, 'bug_status', true);
			$update = $content;
			pto_update_bug($bug_pid, $title, $project, $desc, $assignee, $status, $update, $files, $user);
			$mailbox->markMailAsRead($message);
			if ( ! empty($delete) ) {
				$mailbox->deleteMail($message);
			}               
		}
		if ( $post_type == 'cqpim_project' ) {
			$item_type = __('Project Message', 'projectopia-core');
			$user = get_user_by('email', $fromEmail);
			if ( empty($user) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;
			}
			$project_obj = get_post($post->ID);
			$allowed_to_update = array();
			$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
			if ( ! empty($project_contributors) ) {
				foreach ( $project_contributors as $contributor ) {
					$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : ''; 
					$allowed_to_update[] = $user_id;
				}
			}
			$project_details = get_post_meta($post->ID, 'project_details', true);
			$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
			$client_contacts = get_post_meta($client_id, 'client_contacts', true);
			$client_details = get_post_meta($client_id, 'client_details', true);
			$allowed_to_update[] = $client_details['user_id'];
			if ( ! empty($client_contacts) ) {
				foreach ( $client_contacts as $contact ) {
					$allowed_to_update[] = $contact['user_id'];
				}
			}           
			if ( ! in_array($user->ID, $allowed_to_update) ) {
				$value = get_option('cqpim_send_piping_reject');
				if ( ! empty($value) ) {
					pto_send_unknown_account_email($fromEmail, $fromName, $item_type);
				}
				$mailbox->markMailAsRead($message);
					if ( ! empty($delete) ) {
						$mailbox->deleteMail($message);
					}
				exit;                   
			}
			if ( in_array('cqpim_client', $user->roles) ) {
				$type = 'client';
			} else {
				$type = 'admin';
			}
			pto_update_project_message_piping($post->ID, $user->ID, $type, $content);
			$mailbox->markMailAsRead($message);
			if ( ! empty($delete) ) {
				$mailbox->deleteMail($message);
			}               
		}
	}
	} else {
		die();
	}
	exit;
}