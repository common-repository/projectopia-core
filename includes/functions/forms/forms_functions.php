<?php
add_action( "wp_ajax_nopriv_pto_frontend_register_submission", "pto_frontend_register_submission" );
add_action( "wp_ajax_pto_frontend_register_submission", "pto_frontend_register_submission" );
function pto_frontend_register_submission() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty( $_POST ) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('There is missing data, please try again filling in every field.', 'projectopia-core') . '</span>',
		) );         
	} else {
		unset($_POST['action']);
		unset($_POST['pto_nonce']);
		
		$name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
		unset($_POST['name']);
		$company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
		unset($_POST['company']);
		$address = isset($_POST['address']) ? sanitize_text_field(wp_unslash($_POST['address'])) : '';
		unset($_POST['address']);
		$postcode = isset($_POST['postcode']) ? sanitize_text_field(wp_unslash($_POST['postcode'])) : '';
		unset($_POST['postcode']);
		$telephone = isset($_POST['telephone']) ? sanitize_text_field(wp_unslash($_POST['telephone'])) : '';
		unset($_POST['telephone']);
		$email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
		unset($_POST['email']);
		if ( username_exists( $email ) || email_exists( $email ) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('The email address entered is already in our system, please try again with a different email address or contact us.', 'projectopia-core') . '</span>',
			) );             
		} else {

			/** Verify the google recaptcha if it is enable for frontend forms. */
			if ( ! empty( get_option( 'pto_frontend_form_google_recaptcha') ) && ! empty( $_POST['g_captacha_response'] ) ) {
				$recaptcha_url    = 'https://www.google.com/recaptcha/api/siteverify';
				$recaptcha_secret = get_option( 'google_recaptcha_secret_key' );
				$recaptcha_token  = sanitize_text_field(wp_unslash($_POST['g_captacha_response']));
				$response         = wp_remote_get( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token );
				$recaptcha        = json_decode( wp_remote_retrieve_body( $response ) );
				if ( $recaptcha->success != 1 ) {
					pto_send_json( array(
						'error'   => true,
						'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Please verify the google recaptcha.', 'projectopia-core') . '</span>',
					) );
				}
			}
			// Remove this user_register action before register new client.
			remove_action( 'user_register', 'pto_create_user_as_client_from_user_page', 10, 1);
			$new_client = array(
				'post_type'    => 'cqpim_client',
				'post_status'  => 'private',
				'post_content' => '',
				'post_title'   => $company,
			);
			$client_pid = wp_insert_post( $new_client, true );
			if ( ! is_wp_error( $client_pid ) ) {
				$client_updated = array(
					'ID'        => $client_pid,
					'post_name' => $client_pid,
				);                      
				wp_update_post( $client_updated );
				$client_details = array(
					'client_ref'       => $client_pid,
					'client_company'   => $company,
					'client_contact'   => $name,
					'client_address'   => $address,
					'client_postcode'  => $postcode,
					'client_telephone' => $telephone,
					'client_email'     => $email,
				);
				update_post_meta($client_pid, 'client_details', $client_details);               
				$require_approval = get_option('pto_creg_approve');
				if ( $require_approval == 1 ) {
					update_post_meta($client_pid, 'pending', 1);
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
						if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients') ) {
							pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_auth');
						}
					}
				} else {
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
						if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients') ) {
							pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_noauth');
						}
					}
					$passw = pto_random_string(10);
					$login = $email;
					$user_id = wp_create_user( $login, $passw, $email );
					$user = new WP_User( $user_id );
					$user->set_role( 'cqpim_client' );
					$client_details = get_post_meta($client_pid, 'client_details', true);
					$client_details['user_id'] = $user_id;
					update_post_meta($client_pid, 'client_details', $client_details);
					$client_ids = array();
					$client_ids[] = $user_id;               
					update_post_meta($client_pid, 'client_ids', $client_ids);
					$user_data = array(
						'ID'           => $user_id,
						'display_name' => $name,
						'first_name'   => $name,
					);
					wp_update_user($user_data); 
					$form_auto_welcome = get_option('form_reg_auto_welcome');
					if ( $form_auto_welcome == 1 ) {
						send_pto_welcome_email($client_pid, $passw);
					}   
				}
				pto_send_json( array( 
					'error'   => false,
					'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#8ec165">' . esc_html__('Account created, please check your email for your password.', 'projectopia-core') . '</span>',
				) );                     
			} else {
				pto_send_json( array( 
					'error'   => true,
					'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Unable to create client entry, please try again or contact us.', 'projectopia-core') . '</span>',
				) ); 
			}               
		}
	}
	exit(); 
}

add_action( "wp_ajax_nopriv_pto_frontend_quote_submission", "pto_frontend_quote_submission" );
add_action( "wp_ajax_pto_frontend_quote_submission", "pto_frontend_quote_submission" );
function pto_frontend_quote_submission() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty( $_POST ) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('There is missing data, please try again filling in every field.', 'projectopia-core') . '</span>',
		) );         
	} else {
		/** Verify the google recaptcha if it is enable for frontend forms. */
		if ( ! empty( get_option( 'pto_frontend_form_google_recaptcha') ) && ! empty( $_POST['g_captacha_response'] ) ) {
			$recaptcha_url    = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = get_option( 'google_recaptcha_secret_key' );
			$recaptcha_token  = sanitize_text_field(wp_unslash($_POST['g_captacha_response']));
			$response         = wp_remote_get( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token );
			$recaptcha        = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $recaptcha->success != 1 ) {
				pto_send_json( array(
					'error'   => true,
					'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Please verify the google recaptcha.', 'projectopia-core') . '</span>',
				) );
			}
		}

		unset($_POST['action']);
		unset($_POST['pto_nonce']);

		$name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
		unset($_POST['name']);
		$company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
		unset($_POST['company']);
		$address = isset($_POST['address']) ? sanitize_text_field(wp_unslash($_POST['address'])) : '';
		unset($_POST['address']);
		$postcode = isset($_POST['postcode']) ? sanitize_text_field(wp_unslash($_POST['postcode'])) : '';
		unset($_POST['postcode']);
		$telephone = isset($_POST['telephone']) ? sanitize_text_field(wp_unslash($_POST['telephone'])) : '';
		unset($_POST['telephone']);
		$email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
		unset($_POST['email']);

		//patch
		unset($_POST['g_captacha_response']);

		if ( username_exists( $email ) || email_exists( $email ) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('The email address entered is already in our system, please try again with a different email address or contact us.', 'projectopia-core') . '</span>',
			) );             
		} else {
			// Remove this user_register action before register new client.
			remove_action( 'user_register', 'pto_create_user_as_client_from_user_page', 10, 1);
			$new_client = array(
				'post_type'    => 'cqpim_client',
				'post_status'  => 'private',
				'post_content' => '',
				'post_title'   => $company,
			);
			$client_pid = wp_insert_post( $new_client, true );
			if ( ! is_wp_error( $client_pid ) ) {
				$client_updated = array(
					'ID'        => $client_pid,
					'post_name' => $client_pid,
				);                      
				wp_update_post( $client_updated );
				$client_details = array(
					'client_ref'       => $client_pid,
					'client_company'   => $company,
					'client_contact'   => $name,
					'client_address'   => $address,
					'client_postcode'  => $postcode,
					'client_telephone' => $telephone,
					'client_email'     => $email,
				);
				update_post_meta( $client_pid, 'client_details', $client_details );
				$require_approval = get_option('pto_cquo_approve');
				if ( $require_approval == 1 ) {
					update_post_meta( $client_pid, 'pending', 1 );
				} else {
					$passw = pto_random_string( 10 );
					$login = $email;
					$user_id = wp_create_user( $login, $passw, $email );
					$user = new WP_User( $user_id );
					$user->set_role( 'cqpim_client' );
					$client_details = get_post_meta( $client_pid, 'client_details', true );
					$client_details['user_id'] = $user_id;
					update_post_meta( $client_pid, 'client_details', $client_details );
					$client_ids = array();
					$client_ids[] = $user_id;
					update_post_meta( $client_pid, 'client_ids', $client_ids );
					$user_data = array(
						'ID'           => $user_id,
						'display_name' => $name,
						'first_name'   => $name,
					);
					wp_update_user( $user_data ); 
					$form_auto_welcome = get_option( 'form_auto_welcome' );
					if ( $form_auto_welcome == 1 ) {
						send_pto_welcome_email( $client_pid, $passw );
					}
				}
				$new_quote = array(
					'post_type'    => 'cqpim_quote',
					'post_status'  => 'private',
					'post_content' => '',
					'post_title'   => '',
				);
				$quote_pid = wp_insert_post( $new_quote, true );
				if ( ! is_wp_error( $quote_pid ) ) {                  
					$title = $company . ' - ' . esc_html__( 'Quote', 'projectopia-core' ) . ': ' . $quote_pid;
					$quote_updated = array(
						'ID'         => $quote_pid,
						'post_title' => $title,
						'post_name'  => $quote_pid,
					);                      
					wp_update_post( $quote_updated );
					$uploaded_files = $original_fields = array();
					$summary = '';
					$form = get_option( 'cqpim_frontend_form' );
					$form_data = get_post_meta( $form, 'builder_data', true );
					if ( is_array( $form_data ) ) {
						$form_data = '';
					}
					$form_data = json_decode( $form_data );
					$fields = $form_data;
					foreach ( $fields as $field ) {
						$id = isset( $field->name ) ? strtolower( $field->name ) : '';
						$id = str_replace( ' ', '_', $id );
						$id = str_replace( '-', '_', $id );
						$id = preg_replace( '/[^\w-]/', '', $id );
						if ( ! empty( $id ) ) {
							if ( $field->type == 'file' ) {
								$original_fields[ 'cqpimuploader_' . $id ] = $field->label;
							} else {
								$original_fields[ $id ] = $field->label;
							}
						}
					}
					$data = $_POST;
					foreach ( $data as $key => $field ) {
						if ( is_array( $field ) ) {
							$field = implode( ', ', $field );
						}
						$title = isset( $original_fields[ $key ] ) ? $original_fields[ $key ] : 'N/A';
						$key = ucwords( $key );
						$title = ucwords( $title );
						if ( strpos( $key, 'Cqpimuploader' ) !== false ) {
							$vl = explode( ',', $field );
							if ( is_array( $vl ) && count( $vl ) > 0 ) {
								foreach ( $vl as $vl_id ) {
									$file_object = get_post( $vl_id );
									$title = str_replace( 'Cqpimuploader ', '', $title );
									$summary .= '<p><strong>' . $title . ': </strong> ' . $file_object->post_title . '</p>';
									$attachment_updated = array(
										'ID'          => $vl_id,
										'post_parent' => $quote_pid,
									);
									wp_update_post( $attachment_updated );
									update_post_meta( $vl_id, 'cqpim', true );
								}
							}                       
						} else {
							$summary .= '<p><strong>' . $title . ': </strong> ' . $field . '</p>';
						}
					}
					$header = get_option( 'quote_header' );
					$header = str_replace('%%CLIENT_NAME%%', $name, $header );
					$footer = get_option( 'quote_footer' );
					$footer = str_replace( '%%CURRENT_USER%%', '', $footer );
					$currency = get_option( 'currency_symbol' );
					$currency_code = get_option( 'currency_code' );
					$currency_position = get_option( 'currency_symbol_position' );
					$currency_space = get_option( 'currency_symbol_space' ); 
					update_post_meta( $quote_pid, 'currency_symbol', $currency );
					update_post_meta( $quote_pid, 'currency_code', $currency_code );
					update_post_meta( $quote_pid, 'currency_position', $currency_position );
					update_post_meta( $quote_pid, 'currency_space', $currency_space );
					$quote_details = array(
						'quote_type'     => 'quote',
						'quote_ref'      => $quote_pid,
						'client_id'      => $client_pid,
						'quote_summary'  => $summary,
						'quote_header'   => $header,
						'quote_footer'   => $footer,
						'client_contact' => $user_id,
					);
					update_post_meta( $quote_pid, 'quote_details', $quote_details );
					$args = array(
						'post_type'      => 'cqpim_teams',
						'posts_per_page' => -1,
						'post_status'    => 'private',
					);
					$team_members = get_posts( $args );
					$quote_email_recipient = array();
					foreach ( $team_members as $member ) {
						$team_details = get_post_meta( $member->ID, 'team_details', true );
						$user_id = isset( $team_details['user_id'] ) ? $team_details['user_id'] : '';
						$user_obj = get_user_by( 'id', $user_id);
						if ( ! empty( $user_obj ) && user_can( $user_obj, 'edit_cqpim_quotes' ) ) {
							$quote_email_recipient[] = $user_obj->user_email;
							pto_add_team_notification( $member->ID, $user_id, $quote_pid, 'new_quote' );
						}
					}
					$quote_email_recipient[] = get_option( 'company_sales_email' );
					$attachments = array();
					$subject = get_option( 'new_quote_subject' );
					$content = get_option( 'new_quote_email' );
					$name_tag = '%%NAME%%';
					$link_tag = '%%QUOTE_URL%%';
					$company_tag = '%%COMPANY_NAME%%';
					$quote_link = admin_url() . 'post.php?post=' . $quote_pid . '&action=edit';
					$subject = str_replace( $name_tag, $name, $subject );
					$content = str_replace( $name_tag, $name, $content );
					$content = str_replace( $link_tag, $quote_link, $content );
					$content = str_replace( $company_tag, get_option( 'company_name' ), $content );
					foreach ( $quote_email_recipient as $to ) {
						pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' );
					}
					pto_send_json( array( 
						'error'   => false,
						'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#8ec165">' . esc_html__('Quote request submitted, we\'ll get back to you soon!', 'projectopia-core') . '</span>',
					) );                     
				} else {
					pto_send_json( array( 
						'error'   => true,
						'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Unable to create quote, please try again or contact us.', 'projectopia-core') . '</span>',
					) );                     
				}
			} else {
				pto_send_json( array( 
					'error'   => true,
					'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Unable to create client entry, please try again or contact us.', 'projectopia-core') . '</span>',
				) ); 
			}               
		}
	}
	exit(); 
}

add_action( "wp_ajax_nopriv_pto_backend_quote_submission", "pto_backend_quote_submission" );
add_action( "wp_ajax_pto_backend_quote_submission", "pto_backend_quote_submission" );
function pto_backend_quote_submission() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty( $_POST ) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There is missing data, please try again filling in every field.', 'projectopia-core') . '</div>',
		) );         
	} else {
		unset($_POST['action']);
		unset($_POST['pto_nonce']);

		$client = isset($_POST['client']) ? intval($_POST['client']) : '';
		unset($_POST['client']);

		$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : '';
		unset($_POST['form_id']);

		$client_details = get_post_meta($client, 'client_details', true);
		$name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		if ( empty($client) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Missing Client ID, please try again.', 'projectopia-core') . '</div>',
			) );             
		} else {    
			$new_quote = array(
				'post_type'    => 'cqpim_quote',
				'post_status'  => 'private',
				'post_content' => '',
				'post_title'   => '',
			);
			$quote_pid = wp_insert_post( $new_quote, true );
			if ( ! is_wp_error( $quote_pid ) ) {                  
				$title = $company . ' - ' . esc_html__('Quote', 'projectopia-core') . ': ' . $quote_pid;
				$quote_updated = array(
					'ID'         => $quote_pid,
					'post_title' => $title,
					'post_name'  => $quote_pid,
				);                      
				wp_update_post( $quote_updated );
				$summary = '';
				$form = get_option( 'cqpim_backend_form' );
				if ( ! empty( $form_id ) ) {
					$form = $form_id;
				}

				$form_data = get_post_meta( $form, 'builder_data', true );
				if ( is_array( $form_data ) ) {
					$form_data = '';
				}
				$form_data = json_decode( $form_data );
				$fields = $form_data;
				foreach ( $fields as $field ) {
					$id = isset( $field->name ) ? strtolower( $field->name ) : '';
					$id = str_replace( ' ', '_', $id );
					$id = str_replace( '-', '_', $id );
					$id = preg_replace( '/[^\w-]/', '', $id );
					if ( ! empty( $id ) ) {
						if ( $field->type == 'file' ) {
							$original_fields[ 'cqpimuploader_' . $id ] = $field->label;
						} else {
							$original_fields[ $id ] = $field->label;
						}
					}
				}
				$data = $_POST;
				foreach ( $data as $key => $field ) {
					if ( is_array( $field ) ) {
						$field = implode(', ', $field);
					}
					$title = isset( $original_fields[ $key ] ) ? $original_fields[ $key ] : 'N/A';
					$key = ucwords( $key );
					$title = ucwords( $title );
					if ( strpos( $key, 'Cqpimuploader' ) !== false ) {
						$vl = explode( ',', $field );
						if ( is_array( $vl ) && count( $vl ) > 0 ) {
							foreach ( $vl as $vl_id ) {
								$file_object = get_post( $vl_id );
								$title = str_replace( 'Cqpimuploader ', '', $title );
								$summary .= '<p><strong>' . $title . ': </strong> ' . $file_object->post_title . '</p>';
								$attachment_updated = array(
									'ID'          => $vl_id,
									'post_parent' => $quote_pid,
								);
								wp_update_post( $attachment_updated );
								update_post_meta( $vl_id, 'cqpim', true );
							}
						}
					} else {
						$summary .= '<p><strong>' . $title . ': </strong> ' . $field . '</p>';
					}
				}
				$user = wp_get_current_user();
				$header = get_option( 'quote_header' );
				$header = str_replace('%%CLIENT_NAME%%', $user->display_name, $header);
				$footer = get_option( 'quote_footer' );
				$footer = str_replace('%%CURRENT_USER%%', '', $footer);
				$currency = get_option('currency_symbol');
				$currency_code = get_option('currency_code');
				$currency_position = get_option('currency_symbol_position');
				$currency_space = get_option('currency_symbol_space'); 
				update_post_meta($quote_pid, 'currency_symbol', $currency);
				update_post_meta($quote_pid, 'currency_code', $currency_code);
				update_post_meta($quote_pid, 'currency_position', $currency_position);
				update_post_meta($quote_pid, 'currency_space', $currency_space);
				$quote_details = array(
					'quote_type'     => 'quote',
					'quote_ref'      => $quote_pid,
					'client_id'      => $client,
					'quote_summary'  => $summary,
					'quote_header'   => $header,
					'quote_footer'   => $footer,
					'client_contact' => $user->ID,
				);
				update_post_meta($quote_pid, 'quote_details', $quote_details);
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
					if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_quotes') ) {
						pto_add_team_notification($member->ID, $user->ID, $quote_pid, 'new_quote');
					}
				}
				$to = get_option('company_sales_email');
				$attachments = array();
				$subject = get_option('new_quote_subject');
				$content = get_option('new_quote_email');
				$name_tag = '%%NAME%%';
				$link_tag = '%%QUOTE_URL%%';
				$company_tag = '%%COMPANY_NAME%%';
				$quote_link = admin_url() . 'post.php?post=' . $quote_pid . '&action=edit';
				$subject = str_replace($name_tag, $user->display_name, $subject);
				$content = str_replace($name_tag, $user->display_name, $content);
				$content = str_replace($link_tag, $quote_link, $content);
				$content = str_replace($company_tag, get_option('company_name'), $content);
				pto_send_emails( $to, $subject, $content, '', $attachments, 'other' );
				pto_send_json( array( 
					'error'   => false,
					'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Quote request submitted, we\'ll get back to you soon!', 'projectopia-core') . '</div>',
				) );                     
			} else {
				pto_send_json( array( 
					'error'   => true,
					'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Unable to create quote, please try again or contact us.', 'projectopia-core') . '</div>',
				) );                     
			}               
		}
	}
	exit(); 
}