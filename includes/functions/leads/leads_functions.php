<?php
add_action( "wp_ajax_nopriv_pto_frontend_lead_submission", "pto_frontend_lead_submission" );
add_action( "wp_ajax_pto_frontend_lead_submission", "pto_frontend_lead_submission" );
function pto_frontend_lead_submission() {
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
			$recaptcha_token  = sanitize_text_field( wp_unslash( $_POST['g_captacha_response'] ) );
			$response         = wp_remote_get( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token );
			$recaptcha        = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $recaptcha->success != 1 ) {
				pto_send_json( array(
					'error'   => true,
					'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Please verify the google recaptcha.', 'projectopia-core') . '</span>',
				) );
			}
		}

		unset( $_POST['action'] );
		unset( $_POST['pto_nonce'] );
	
		// patch
		if ( isset( $_POST['g_captacha_response'] ) ) {
			unset( $_POST['g_captacha_response'] );
		}

		$new_lead = array(
			'post_type'    => 'cqpim_lead',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => __('New Lead', 'projectopia-core') . ' - ' . wp_date(get_option('cqpim_date_format') . ' H:i', time()),
		);
		$lead_pid = wp_insert_post( $new_lead, true );
		if ( ! is_wp_error( $lead_pid ) && isset( $_POST['leadform_id'] ) ) {
			$lead_form_id = intval( $_POST['leadform_id'] );
			unset( $_POST['leadform_id'] );
			update_post_meta( $lead_pid, 'lead_date', time() );
			update_post_meta( $lead_pid, 'form_type', 'cqpim' );
			update_post_meta( $lead_pid, 'leadform_id', $lead_form_id );
			$form_data = get_post_meta( $lead_form_id, 'builder_data', true );
			if ( is_array( $form_data ) ) {
				$form_data = '';
			}
			$form_data = json_decode( $form_data );
			$fields = $form_data;
			$original_fields = array();
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
			$uploaded_files = array();
			$summary = '';
			$lead_details = array();
			$data = $_POST;
			foreach ( $data as $key => $field ) {
				if ( is_array( $field ) ) {
					$field = implode( ', ', $field );
				}
				$title = isset( $original_fields[ $key ] ) ? $original_fields[ $key ] : 'N/A';
				$key = ucwords( $key );
				$title = ucwords( $title );
				if ( strpos( $key, 'Cqpimuploader' ) !== false ) {
					$file_object = get_post( $field );
					$title = str_replace( 'Cqpimuploader ', '', $title );
					$lead_details[] = array(
						'type'  => 'file',
						'name'  => $title,
						'value' => $file_object->post_title,
						'ID'    => $file_object->ID,
					);
					$attachment_updated = array(
						'ID'          => $field,
						'post_parent' => $lead_pid,
					);
					wp_update_post( $attachment_updated );
					update_post_meta( $field, 'cqpim', true );
				} else {
					$summary .= '<p><strong>' . $title . ': </strong> ' . $field . '</p>';
					$lead_details[] = array(
						'type'  => 'field',
						'name'  => $title,
						'value' => $field,
						'ID'    => '',
					);
				}
			}
			update_post_meta( $lead_pid, 'lead_summary', $summary );
			update_post_meta( $lead_pid, 'lead_details', $lead_details );
			do_action( 'pto_after_update_summary_details', $lead_pid, $lead_form_id, $data, $summary, $lead_details,$fields,$original_fields);
			$mail = pto_send_lead_notification( $lead_pid );
			pto_send_json( array( 
				'error'   => false,
				'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#8ec165">' . esc_html__('Request submitted, we\'ll get back to you soon!', 'projectopia-core') . '</span>',
			) );
		} else {
			pto_send_json( array( 
				'error'   => true,
				'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Unable to create entry, please try again or contact us.', 'projectopia-core') . '</span>',
			) );                 
		}
	}
	exit(); 
}
add_action( 'gform_after_submission', 'pto_check_gf_submission', 10, 2 );
function pto_check_gf_submission( $entry, $form ) {
	$args = array(
		'post_type'      => 'cqpim_leadform',
		'posts_per_page' => 1,
		'meta_key'       => 'gravity_form',
		'meta_value'     => $entry['form_id'],
		'post_status'    => 'private',
	);
	$leadforms = get_posts($args);
	if ( ! empty($leadforms) ) {
		$leadform = isset($leadforms[0]) ? $leadforms[0] : array();
		if ( ! empty($leadform->ID) ) {
			$new_lead = array(
				'post_type'    => 'cqpim_lead',
				'post_status'  => 'private',
				'post_content' => '',
				'post_title'   => __('New Lead', 'projectopia-core') . ' - ' . wp_date(get_option('cqpim_date_format') . ' H:i', time()),
			);  
			$lead_pid = wp_insert_post( $new_lead, true );
			if ( ! is_wp_error($lead_pid) ) {
				update_post_meta($lead_pid, 'lead_date', time());
				update_post_meta($lead_pid, 'form_type', 'gf');
				update_post_meta($lead_pid, 'leadform_id', $leadform->ID);
				update_post_meta($lead_pid, 'gf_submission_id', $entry['id']);
				$mail = pto_send_lead_notification($lead_pid);
			}
		} else {
			exit;
		}
	}
}
function pto_send_lead_notification( $lead_id ) {
	$emails_to_send = array();
	$emails_to_send[] = array(
		'name'  => get_option('company_name'),
		'email' => get_option('company_sales_email'),
	);
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);  
	$team_members = get_posts($args);   
	foreach ( $team_members as $team_member ) {        
		$team_details = get_post_meta($team_member->ID, 'team_details', true);      
		$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';     
		if ( ! empty($user_id) ) {          
			$user = get_user_by('id', $user_id);            
			if ( user_can($user, 'edit_cqpim_leads') ) {
				pto_add_team_notification($team_member->ID, 'system', $lead_id, 'new_lead');
				$emails_to_send[] = array(
					'name'  => $user->display_name,
					'email' => $user->user_email,
				);
			}           
		}       
	}
	if ( ! empty($emails_to_send) ) {       
		foreach ( $emails_to_send as $email ) {
			$email_subject = get_option('new_lead_email_subject');
			$email_content = get_option('new_lead_email_content');
			$email_subject = pto_replacement_patterns($email_subject, $lead_id);
			$email_content = pto_replacement_patterns($email_content, $lead_id);    
			$email_content = str_replace('%%TEAM_NAME%%', $email['name'], $email_content);
			$email_content = str_replace('%%LEAD_URL%%', admin_url() . 'post.php?post=' . $lead_id . '&action=edit', $email_content);
			pto_send_emails($email['email'], $email_subject, $email_content, '', '', 'sales');
		}
	}
}