<?php
function pto_get_contract_status( $project_id ) {
	$checked = get_option('enable_project_contracts'); 
	if ( empty($checked) ) {
		return 2;
	}
	$project_details = get_post_meta($project_id, 'project_details', true); 
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : ''; 
	//$client_contract = get_post_meta($client_id, 'client_contract', true);
	$client_contract = metadata_exists('post', $client_id, 'client_contract');
	if ( ! empty($client_contract) ) {
		return 2;
	}
	return 1;
}

add_action( "wp_ajax_nopriv_pto_process_contract_emails", "pto_process_contract_emails" );
add_action( "wp_ajax_pto_process_contract_emails", "pto_process_contract_emails" );
function pto_process_contract_emails( $project_id ) {
	if ( ! empty($_POST['project_id']) ) {
		check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
		$project_id = sanitize_text_field( wp_unslash( $_POST['project_id'] ) );
		$ajax_post = true;
	} 
	$project_details = get_post_meta($project_id, 'project_details', true);
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$client_id = $project_details['client_id'];
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$client_main_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	if ( empty($client_contacts) ) {
		$client_contacts = array();
	}
	if ( ! empty($client_contact) ) {
		if ( $client_contact == $client_main_id ) {
			$to = $client_details['client_email'];
		} else {
			$to = $client_contacts[ $client_contact ]['email'];
		}
	} else {
		$to = $client_details['client_email'];
	}
	$email_content = get_option('client_contract_email');
	if ( $client_contact == $client_main_id ) {
		$email_content = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', $client_details['client_email'], $email_content);
	} else {
		$email_content = str_replace('%%CLIENT_NAME%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '', $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['email'] : '', $email_content);
	}
	if ( empty($ajax_post) ) {
		$current_user_tag = '%%CURRENT_USER%%';
		$email_content = str_replace($current_user_tag, '', $email_content);
	}
	$message = pto_replacement_patterns($email_content, $project_id, 'project');
	$attachments = array();
	$subject = get_option('client_contract_subject');
	$subject = pto_replacement_patterns($subject, $project_id, 'project');
	if ( $to && $subject && $message ) {
		if ( pto_send_emails( $to, $subject, $message, '', $attachments, 'sales' ) ) :
			if ( ! empty($ajax_post) ) {
				$current_user = wp_get_current_user();
				$current_user_obj = $current_user->display_name;
			} else {
				$current_user_obj = __('System', 'projectopia-core');
				$current_user = get_user_by('id', 1);
			}
			$project_details = get_post_meta($project_id, 'project_details', true);
			$project_details['sent_details'] = array(
				'date' => time(),
				'by'   => $current_user_obj,
				'to'   => $to,
			);
			unset($project_details['confirmed']);
			unset($project_details['confirmed_details']);
			$project_details['sent'] = true;
			update_post_meta($project_id, 'project_details', $project_details );
			if ( ! empty($ajax_post) ) {
				$project_progress = get_post_meta($project_id, 'project_progress', true);
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$project_progress[] = array(
					'update' => __('Contract sent', 'projectopia-core'),
					'date'   => time(),
					'by'     => $current_user_obj,
				);
				update_post_meta($project_id, 'project_progress', $project_progress );
				pto_add_team_notification($client_id, $current_user->ID, $project_id, 'contract_sent', $ctype = 'contract');
			} else {
				$project_progress = get_post_meta($project_id, 'project_progress', true);
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$project_progress[] = array(
					'update' => __('Contract sent', 'projectopia-core'),
					'date'   => time(),
					'by'     => __('System', 'projectopia-core'),
				);
				update_post_meta($project_id, 'project_progress', $project_progress );
				pto_add_team_notification($client_id, 1, $project_id, 'contract_sent', $ctype = 'contract');
			}
			if ( ! empty($ajax_post) ) {
				pto_send_json( array( 
					'error'   => false,
					'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Email sent successfully...', 'projectopia-core') . '</div>',
				) );  
			};          
		else :
			if ( ! empty($ajax_post) ) {
				pto_send_json( array( 
					'error'  => true,
					'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There was a problem sending the email. Check that you have completed the email subject and content templates in the settings.', 'projectopia-core'),
					'</div>',
				) );
			};
		endif;  
	} else {
		if ( $ajax_post ) {
			pto_send_json( array( 
				'error'  => true,
				'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There was a problem sending the email, check that you have completed ALL email subject and content fields in the settings.', 'projectopia-core') . '</div>',
			) );
		};
	}
	if ( ! empty($ajax_post) ) {
		exit();
	};
}

add_action("wp_ajax_nopriv_pto_client_accept_contract", "pto_client_accept_contract" );
add_action("wp_ajax_pto_client_accept_contract", "pto_client_accept_contract" );
function pto_client_accept_contract() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$current_user = wp_get_current_user();
	$project_id = isset( $_POST['project_id'] ) ? (int) $_POST['project_id'] : 0;
	$signed_name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$pm_name = isset( $_POST['pm_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pm_name'] ) ) : '';
	$project_details = get_post_meta( $project_id, 'project_details', true );
	$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	$deposit_invoice_id = isset($project_details['deposit_invoice_id']) ? $project_details['deposit_invoice_id'] : '';
	$contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : '';
	$terms = get_post_meta($project_id, 'terms', true);
	if ( empty($terms) ) {
		if ( empty($contract) ) {
			$text = get_post_meta($contract_text, 'terms', true);
			$text = pto_replacement_patterns($text, $project_id, 'project');
			update_post_meta($project_id, 'terms', $text);
		} else {
			$text = get_post_meta($contract, 'terms', true);
			$text = pto_replacement_patterns($text, $project_id, 'project');
			update_post_meta($project_id, 'terms', $text);          
		}       
	}
	$quote_ref = $project_details['quote_ref'];
	$ip = pto_get_client_ip();
	if ( ! empty($signed_name) ) {
		$project_details['confirmed_details'] = array(
			'date' => time(),
			'by'   => $signed_name,
			'ip'   => $ip,
		);
		$project_details['confirmed'] = true;
		update_post_meta( $project_id, 'project_details', $project_details );
		// Send email to confirm acceptance
		$sender_email = get_option('company_sales_email');
		$pm_email = array();
		$pm_email[] = $sender_email;
		$project_contributors = get_post_meta($project_id, 'project_contributors', true);
		if ( empty($project_contributors) ) {
			$project_contributors = array();
		}
		foreach ( $project_contributors as $contrib ) {
			if ( $contrib['pm'] == true ) {
				$team_details = get_post_meta($contrib['team_id'], 'team_details', true);
				$pm_email[] = isset($team_details['team_email']) ? $team_details['team_email'] : '';
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
			if ( ! empty($user_obj) && user_can($user_obj, 'cqpim_view_project_contract') ) {
				pto_add_team_notification($member->ID, $current_user->ID, $project_id, 'contract_accepted');
			}
		}
		$attachments = array();
		$admin_quote = admin_url() . 'post.php?post=' . $project_id . '&action=edit';
		/* translators: %1$s: Signed Author, %2$s: Quote Number */
		$subject = sprintf(esc_html__('%1$s has just accepted Contract %2$s', 'projectopia-core'), $signed_name, $quote_ref);
		/* translators: %1$s: Signed Author, %2$s: Quote Number, %3$s: Quote Page Link */
		$content = sprintf(esc_html__('%1$s has just accepted Contract %2$s. You can view the details by clicking here - %3$s', 'projectopia-core'), $signed_name, $quote_ref, $admin_quote);
		foreach ( $pm_email as $address ) {
			pto_send_emails( $address, $subject, $content, '', $attachments, 'sales' );
		}
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		$project_progress[] = array(
			'update' => __('Contract for Project has been confirmed', 'projectopia-core') . ': ' . $quote_ref,
			'date'   => time(),
			'by'     => $signed_name,
		);
		update_post_meta($project_id, 'project_progress', $project_progress );
		$project_elements = get_post_meta($project_id, 'project_elements', true);
		$project_total = 0;
		foreach ( $project_elements as $element ) {
			$element_cost = isset($element['cost']) ? $element['cost'] : 0;
			$cost = preg_replace("/[^\\d.]+/","", $element_cost);
			$element_total = $cost;
			$project_total = $project_total + $element_total;
		}
		if ( ! empty($deposit) && $deposit != 'none' && empty($deposit_invoice_id) && $project_total > 0 ) {
			pto_create_deposit_invoice($project_id, $pm_name);
		}
		$project_elements = get_post_meta($project_id, 'project_elements', true);
		if ( empty($project_elements) ) {
			$project_elements = array();
		}
		foreach ( $project_elements as $element ) {
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
				update_post_meta($task->ID, 'active', true);
				$task_details = get_post_meta($task->ID, 'task_details', true);
				$task_details['status'] = 'pending';
				update_post_meta($task->ID, 'task_details', $task_details);
				$args = array(
					'post_type'      => 'cqpim_tasks',
					'posts_per_page' => -1,
					'meta_key'       => 'milestone_id',
					'meta_value'     => $element['id'],
					'post_parent'    => $task->ID,
					'orderby'        => 'date',
					'order'          => 'ASC',
				);
				$subtasks = get_posts($args);
				foreach ( $subtasks as $subtask ) {
					update_post_meta($subtask->ID, 'active', true); 
					$task_details = get_post_meta($subtask->ID, 'task_details', true);
					$task_details['status'] = 'pending';
					update_post_meta($subtask->ID, 'task_details', $task_details);                  
				}
			}                   
		}
		pto_send_json( array( 
			'error'   => false,
			'message' => __('All good!', 'projectopia-core'),
		) );
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There was a problem, please try again.', 'projectopia-core') . '</div>',
		) );      
	}
	die();
}

add_action( "wp_ajax_pto_mark_project_complete", "pto_mark_project_complete" );
function pto_mark_project_complete() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset( $_POST['project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$project_details = get_post_meta($project_id, 'project_details', true);
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$project_details['signoff'] = true;
	$current_user = wp_get_current_user();
	$current_user = $current_user->display_name;
	$project_details['signoff_details'] = array(
		'by' => $current_user,
		'at' => time(),
	);
	update_post_meta($project_id, 'project_details', $project_details);
	$project_progress = get_post_meta($project_id, 'project_progress', true);
	$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

	$project_progress[] = array(
		'update' => __('Project has been marked as Complete: ', 'projectopia-core') . ': ' . $quote_ref,
		'date'   => time(),
		'by'     => $current_user,
	);
	update_post_meta($project_id, 'project_progress', $project_progress );
		
	$checked = get_option('invoice_workflow');
	$project_elements = get_post_meta($project_id, 'project_elements', true);
	$project_total = 0;

	foreach ( $project_elements as $element ) {
		$element_cost = isset($element['cost']) ? $element['cost'] : 0;
		$cost = preg_replace("/[^\\d.]+/","", $element_cost);
		$element_total = $cost;
		$project_total = $project_total + $element_total;
	}

	//get total paid -- new
	$total_paid = 0;
	$args = array(
		'post_type'      => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	$invoices = get_posts($args);
	foreach ( $invoices as $invoice ) {
		$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
		$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
		if ( empty($invoice_payments) ) {
			$invoice_payments = array();
		}
		$pid = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
		if ( ! empty($pid) && $pid == $project_id ) {
			foreach ( $invoice_payments as $payment ) {
				$amount = isset($payment['amount']) ? $payment['amount'] : 0;
				$total_paid = $total_paid + (float) $amount;
			}
		}
	}
	$outstanding = $project_total - $total_paid;
	//end

	if ( $checked == 0 && (int)$outstanding > 0 ) {
		pto_create_completion_invoice($project_id);
	}

	pto_send_json( array( 
		'error'    => false,
		'messages' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Project successfully updated...', 'projectopia-core') . '</div>',
	) );
}

add_action( "wp_ajax_pto_mark_project_incomplete", "pto_mark_project_incomplete" );
function pto_mark_project_incomplete() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset( $_POST['project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$project_details = get_post_meta($project_id, 'project_details', true);
	$completion_invoice_id = isset($project_details['completion_invoice_id']) ? $project_details['completion_invoice_id'] : '';
	$project_details['signoff'] = false;
	unset($project_details['signoff_details']);
	unset($project_details['completion_invoice_id']);
	update_post_meta($project_id, 'project_details', $project_details);
	wp_delete_post($completion_invoice_id);
	pto_send_json( array( 
		'error'    => false,
		'messages' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Project successfully marked as in progress.', 'projectopia-core') . '</div>',
	) );
}

add_action( "wp_ajax_pto_mark_project_closed", "pto_mark_project_closed" );
function pto_mark_project_closed() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset( $_POST['project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$project_details = get_post_meta($project_id, 'project_details', true);
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	update_post_meta($project_id, 'closed', 1);
	$project_details['closed'] = true;
	$project_details['signoff'] = true;
	$current_user = wp_get_current_user();
	$current_user = $current_user->display_name;
	$project_details['closed_details'] = array(
		'by' => $current_user,
		'at' => time(),
	);
	update_post_meta($project_id, 'project_details', $project_details);
	$project_progress = get_post_meta($project_id, 'project_progress', true);
	$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

	$project_progress[] = array(
		'update' => __('Project has been marked as Closed: ', 'projectopia-core') . ': ' . $quote_ref,
		'date'   => time(),
		'by'     => $current_user,
	);
	update_post_meta($project_id, 'project_progress', $project_progress );
	pto_send_json( array( 
		'error'    => false,
		'messages' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Project successfully updated...', 'projectopia-core') . '</div>',
	) );
}

add_action( "wp_ajax_pto_mark_project_open", "pto_mark_project_open" );
function pto_mark_project_open() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset( $_POST['project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$project_details = get_post_meta($project_id, 'project_details', true);
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$project_details['closed'] = false;
	delete_post_meta($project_id, 'closed');
	$current_user = wp_get_current_user();
	$current_user = $current_user->display_name;
	unset($project_details['closed_details']);
	update_post_meta($project_id, 'project_details', $project_details);
	$project_progress = get_post_meta($project_id, 'project_progress', true);
	$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

	$project_progress[] = array(
		'update' => __('Project has been re-opened', 'projectopia-core') . ': ' . $quote_ref,
		'date'   => time(),
		'by'     => $current_user,
	);
	update_post_meta($project_id, 'project_progress', $project_progress );
	pto_send_json( array( 
		'error'    => false,
		'messages' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Project successfully re-opened...', 'projectopia-core') . '</div>',
	) );
}

add_action( "wp_ajax_pto_mark_item_complete", "pto_mark_item_complete" );
function pto_mark_item_complete() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$p_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$task_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : '';
	if ( ! empty( $p_type ) ) {
		if ( $p_type == 'task' ) {
			$milestone_edit = array();
			$task_details = get_post_meta($task_id, 'task_details', true);
			$task_details['status'] = 'complete';
			$task_details['task_pc'] = 100;
			update_post_meta($task_id, 'task_details', $task_details);
			$complete = true;
			$milestone_id = get_post_meta($task_id, 'milestone_id', true);
			$project_id = get_post_meta($task_id, 'project_id', true);
			$args = array(
				'post_type'      => 'cqpim_tasks',
				'meta_key'       => 'milestone_id',
				'meta_value'     => $milestone_id,
				'posts_per_page' => -1,
			);
			$tasks = get_posts($args);
			foreach ( $tasks as $task ) {
				$task_details = get_post_meta($task->ID, 'task_details', true);
				$task_complete = isset($task_details['status']) ? $task_details['status'] : '';
				if ( $task_complete != 'complete' ) {
					$complete = false;
				}
			}
			if ( $complete == true ) {
				$project_elements = get_post_meta($project_id, 'project_elements', true);
				$project_elements = $project_elements && is_array($project_elements) ? $project_elements : array();
				$project_elements[ $milestone_id ]['acost'] = $project_elements[ $milestone_id ]['cost'];
				$project_elements[ $milestone_id ]['status'] = 'complete';
				if ( $project_elements[ $milestone_id ]['already_comp'] != 1 ) {             
					$project_progress = get_post_meta($project_id, 'project_progress', true);
					$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

					$current_user = wp_get_current_user();
					$current_user = $current_user->display_name;
					/* translators: %s: Milestone Title */
					$text = sprintf(esc_html__('Milestone Completed: %s', 'projectopia-core'), $project_elements[ $milestone_id ]['title']);
					$project_progress[] = array(
						'update' => $text,
						'date'   => time(),
						'by'     => $current_user,
					);
					update_post_meta($project_id, 'project_progress', $project_progress );  
					$checked = get_option('invoice_workflow');
					if ( $checked == 1 ) {
						pto_create_ms_completion_invoice($project_id, $project_elements[ $milestone_id ]);
					}
					$project_elements[ $milestone_id ]['already_comp'] = true;
				}
				update_post_meta($project_id, 'project_elements', $project_elements);
				$milestone_edit = array(
					'milestone_id'  => $milestone_id,
					'status_string' => '<span class="badgeOverdue approved">' . esc_html__('Complete', 'projectopia-core') . '</span>',
				);
			}
			$task_owner = get_post_meta($task_id, 'owner', true);
			$task_watchers = get_post_meta($task_id, 'task_watchers', true);
			$message = '';
			if ( isset($_POST['ppid']) ) {
				$project_id = intval( $_POST['ppid'] );
				$current_user = wp_get_current_user();
				$project_progress = get_post_meta($project_id, 'project_progress', true);
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$task_object = get_post($task_id);
				$task_title = $task_object->post_title;
				$project_progress[] = array(
					'update' => __('Task Completed', 'projectopia-core') . ': ' . $task_title,
					'date'   => time(),
					'by'     => $current_user->display_name,
				);
				update_post_meta($project_id, 'project_progress', $project_progress );
			}               
			pto_send_task_updates($task_id, $project_id, $task_owner, $task_watchers, $message);
		}
		$task_status_string = '<span class="badgeOverdue approved">' . esc_html__('Complete', 'projectopia-core') . '</span>';
		pto_send_json( array( 
			'error'       => false,
			'status'      => $task_status_string,
			'ms_complete' => $milestone_edit,
		) );  
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('The item type is missing, please try again', 'projectopia-core'),
		) );      
	}
	exit;           
}

add_action( "wp_ajax_pto_toggle_complete", "pto_toggle_complete" );
function pto_toggle_complete() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( ! empty($_POST['ppid']) ) {
		$project_id = intval( $_POST['ppid'] );
		$quote_details = get_post_meta($project_id, 'project_details', true);
		$quote_details = $quote_details && is_array($quote_details) ? $quote_details : array();
		if ( empty($quote_details['hide_complete']) ) {
			$quote_details['hide_complete'] = false;
		}
		if ( $quote_details['hide_complete'] == false ) {
			$quote_details['hide_complete'] = true;
		} else {
			$quote_details['hide_complete'] = false;
		}
		update_post_meta($project_id, 'project_details', $quote_details);
		pto_send_json( array( 
			'error'  => false,
			'errors' => '',
		) );
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('The project ID is missing, please try again', 'projectopia-core'),
		) );      
	}
	exit;
}

add_action( "wp_ajax_pto_add_message_to_project", "pto_add_message_to_project" );    
function pto_add_message_to_project() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$visibility = isset($_POST['visibility']) ? sanitize_text_field( wp_unslash( $_POST['visibility'] ) ) : '';
	$message = isset($_POST['message']) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$message = make_clickable($message);
	$who = isset($_POST['who']) ? sanitize_text_field( wp_unslash( $_POST['who'] ) ) : '';
	$date = time();
	$current_user = wp_get_current_user();
	$subject = '';
	$content = '';
	if ( $project_id && $visibility && $message && $who ) {
		$project_messages = get_post_meta($project_id, 'project_messages', true);
		if ( ! $project_messages ) {
			$project_messages = array();
		}
		$project_messages[] = array(
			'visibility' => $visibility,
			'date'       => $date,
			'message'    => $message,
			'by'         => $current_user->display_name,
			'author'     => $current_user->ID,
		);
		update_post_meta($project_id, 'project_messages', $project_messages);
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		$project_progress[] = array(
			'update' => $current_user->display_name . ' ' . __('just sent a message', 'projectopia-core'),
			'date'   => time(),
			'by'     => $current_user->display_name,
		);
		update_post_meta($project_id, 'project_progress', $project_progress );
		if ( $visibility == 'all' ) {
			if ( $who == 'client' ) {
				$addresses_to_send = array();
				$project_contributors = get_post_meta($project_id, 'project_contributors', true);
				foreach ( $project_contributors as $contributor ) {
					$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
					$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
					$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
					$addresses_to_send[] = array(
						'mail' => $team_email,
						'name' => $team_name,
					);
					if ( $current_user->ID != $user_id ) {
						pto_add_team_notification($contributor['team_id'], $current_user->ID, $project_id, 'project_message');
					}
				}
				$addresses_to_send[] = get_option('company_sales_email');
				pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'teams');
			} elseif ( $who == 'admin' ) {
				$send_to_team = isset($_POST['send_to_team']) ? sanitize_text_field( wp_unslash( $_POST['send_to_team'] ) ) : '';
				$send_to_client = isset($_POST['send_to_client']) ? sanitize_text_field( wp_unslash( $_POST['send_to_client'] ) ) : '';
				$addresses_to_send = array();
				$project_details = get_post_meta($project_id, 'project_details', true);
				$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_contacts = get_post_meta($client_id, 'client_contacts', true);
				$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
				if ( ! empty($client_contact) ) {
					if ( $client_details['user_id'] == $client_contact ) {
						$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
						$client_name = isset($client_details['client_name']) ? $client_details['client_name'] : '';
					} else {
						$client_email = isset($client_contacts[ $client_contact ]['email']) ? $client_contacts[ $client_contact ]['email'] : '';
						$client_name = isset($client_details['name']) ? $client_details['name'] : '';                       
					}
				} else {
					$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
					$client_name = isset($client_details['client_name']) ? $client_details['client_name'] : '';
				}                   
				$addresses_to_send[] = get_option('company_sales_email');
				if ( $send_to_client == 1 ) {
					$addresses_to_send[] = array(
						'mail' => $client_email,
						'name' => $client_name,
					);

					// Bell-icon notification to client on project message.
					pto_add_team_notification( $client_id, $current_user->ID, $project_id, 'project_message', 'project_message' );
					pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'client');
				}
				$addresses_to_send = array();
				if ( $send_to_team == 1 ) {
					$project_contributors = get_post_meta($project_id, 'project_contributors', true);
					foreach ( $project_contributors as $contributor ) {
						$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
						$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
						$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
						$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
						$addresses_to_send[] = array(
							'mail' => $team_email,
							'name' => $team_name,
						);
						if ( $current_user->ID != $user_id ) {
							pto_add_team_notification($contributor['team_id'], $current_user->ID, $project_id, 'project_message');
						}
					}
					pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'teams');
				}
			}
		} else {
			$send_to_team = isset($_POST['send_to_team']) ? sanitize_text_field( wp_unslash( $_POST['send_to_team'] ) ) : '';
			if ( $send_to_team == 1 ) {
				$addresses_to_send = array();
				$project_contributors = get_post_meta($project_id, 'project_contributors', true);
				foreach ( $project_contributors as $contributor ) {
					$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
					$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
					$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
					$addresses_to_send[] = array(
						'mail' => $team_email,
						'name' => $team_name,
					);
					if ( $current_user->ID != $user_id ) {
						pto_add_team_notification($contributor['team_id'], $current_user->ID, $project_id, 'project_message');
					}
				}
				$addresses_to_send[] = get_option('company_sales_email');
				pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'teams');
			}
		}
		pto_send_json( array( 
			'error'  => false,
			'errors' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Message added successfully', 'projectopia-core') . '</div>',
		) );              
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You have not entered a message!', 'projectopia-core') . '</div>',
		) );          
	}
	exit();
}

function pto_update_project_message_piping( $post, $user, $type, $update = NULL ) {
	$project_id = $post;
	$update = isset($update) ? $update : '';
	$message = make_clickable($update);
	$who = isset($type) ? $type : '';
	$date = time();
	$current_user = get_user_by('id', $user);
	$subject = '';
	$content = '';
	if ( $project_id && $message && $who ) {
		$project_messages = get_post_meta($project_id, 'project_messages', true);
		if ( ! $project_messages ) {
			$project_messages = array();
		}
		$project_messages[] = array(
			'visibility' => 'all',
			'date'       => $date,
			'message'    => $message,
			'by'         => $current_user->display_name,
			'author'     => $current_user->ID,
		);
		update_post_meta($project_id, 'project_messages', $project_messages);
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		$project_progress[] = array(
			'update' => $current_user->display_name . ' ' . __('just sent a message', 'projectopia-core'),
			'date'   => time(),
			'by'     => $current_user->display_name,
		);
		update_post_meta($project_id, 'project_progress', $project_progress );
		if ( 1 ) {
			if ( $who == 'client' ) {
				$addresses_to_send = array();
				$project_contributors = get_post_meta($project_id, 'project_contributors', true);
				foreach ( $project_contributors as $contributor ) {
					$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
					$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
					$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
					$addresses_to_send[] = array(
						'mail' => $team_email,
						'name' => $team_name,
					);
				}
				$addresses_to_send[] = get_option('company_sales_email');
				pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'teams', $current_user->display_name);
			} elseif ( $who == 'admin' ) {
				$send_to_team = 0;
				$send_to_client = 1;
				$addresses_to_send = array();
				$project_details = get_post_meta($project_id, 'project_details', true);
				$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_contacts = get_post_meta($client_id, 'client_contacts', true);
				$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
				if ( ! empty($client_contact) ) {
					if ( $client_details['user_id'] == $client_contact ) {
						$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
						$client_name = isset($client_details['client_name']) ? $client_details['client_name'] : '';
					} else {
						$client_email = isset($client_contacts[ $client_contact ]['email']) ? $client_contacts[ $client_contact ]['email'] : '';
						$client_name = isset($client_details['name']) ? $client_details['name'] : '';                       
					}
				} else {
					$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
					$client_name = isset($client_details['client_name']) ? $client_details['client_name'] : '';
				}                   
				$addresses_to_send[] = get_option('company_sales_email');
				if ( $send_to_client == 1 ) {
					$addresses_to_send[] = array(
						'mail' => $client_email,
						'name' => $client_name,
					);
					pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'client', $current_user->display_name);
				}
				$addresses_to_send = array();
				if ( $send_to_team == 1 ) {
					$project_contributors = get_post_meta($project_id, 'project_contributors', true);
					foreach ( $project_contributors as $contributor ) {
						$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
						$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
						$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
						$addresses_to_send[] = array(
							'mail' => $team_email,
							'name' => $team_name,
						);
					}
					pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'teams', $current_user->display_name);
				}
			}
		} else {
			$send_to_team = 0;
			if ( $send_to_team == 1 ) {
				$addresses_to_send = array();
				$project_contributors = get_post_meta($project_id, 'project_contributors', true);
				foreach ( $project_contributors as $contributor ) {
					$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
					$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
					$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
					$addresses_to_send[] = array(
						'mail' => $team_email,
						'name' => $team_name,
					);
				}
				$addresses_to_send[] = get_option('company_sales_email');
				pto_project_message_mailer($addresses_to_send, $subject, $content, $project_id, $message, 'teams');
			}
		}
	}
}

function pto_project_message_mailer( $addresses, $subject, $content, $project_id, $message, $type, $display_name = NULL ) {
	$attachments = array();
	$current_user = wp_get_current_user();
	foreach ( $addresses as $to ) {
		if ( $type == 'client' ) {
			$subject = get_option('client_message_subject');
			$content = get_option('client_message_email');
			$content = str_replace('%%MESSAGE%%', $message, $content);          
		} else {
			$subject = get_option('company_message_subject');
			$content = get_option('company_message_email');
			$content = str_replace('%%MESSAGE%%', $message, $content);          
		}
		$mailto = isset($to['mail']) ? $to['mail'] : '';
		$name = isset($to['name']) ? $to['name'] : '';
		$to_user = get_user_by('email', $mailto);
		if ( ! empty($display_name) ) {
			$content = str_replace('%%CURRENT_USER%%', $display_name, $content);
			$subject = str_replace('%%CURRENT_USER%%', $display_name, $subject);
		}
		$content = str_replace('%%TEAM_NAME%%', $name, $content);
		$subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $project_id . ']', $subject);
		$subject = pto_replacement_patterns($subject, $project_id, 'project');
		$content = pto_replacement_patterns($content, $project_id, 'project');
		if ( $current_user->ID != $to_user->ID ) {
			pto_send_emails($mailto, $subject, $content, '', $attachments, 'sales');
		}
	}
}

add_action( "wp_ajax_nopriv_pto_delete_project_message", "pto_delete_project_message" );
add_action( "wp_ajax_pto_delete_project_message", "pto_delete_project_message" );    
function pto_delete_project_message() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$project_messages = get_post_meta($project_id, 'project_messages', true);
	$project_messages = array_reverse($project_messages);
	unset($project_messages[ $key ]);
	$project_messages = array_filter($project_messages);
	$project_messages = array_reverse($project_messages);
	update_post_meta($project_id, 'project_messages', $project_messages);
	exit();
}

add_action( "wp_ajax_pto_clear_all_action", "pto_clear_all_action" );
function pto_clear_all_action() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$item_ref = isset($_POST['quote_id']) ? sanitize_text_field( wp_unslash( $_POST['quote_id'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	if ( $type == 'quote' ) {
		$elements = get_post_meta($item_ref, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item_ref, 'project_elements', true);
	}
	if ( ! empty($elements) ) {
		foreach ( $elements as $element ) {
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
				$args = array(
					'post_type'      => 'cqpim_tasks',
					'posts_per_page' => -1,
					'post_parent'    => $task->ID,
					'orderby'        => 'date',
					'order'          => 'ASC',
				);  
				$subtasks = get_posts($args);
				foreach ( $subtasks as $subtask ) {
					wp_delete_post($subtask->ID);
				}
				wp_delete_post($task->ID);      
			}               
		}
		if ( $type == 'quote' ) {
			delete_post_meta($item_ref, 'quote_elements');
		} else {
			delete_post_meta($item_ref, 'project_elements');
		}
		pto_send_json( array( 
			'error'    => false,
			'messages' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Milestones & Tasks cleared successfully.', 'projectopia-core') . '</div>',
		) );
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There is nothing to clear!', 'projectopia-core') . '</div>',
		) );
	}
}

add_action( "wp_ajax_pto_update_task_weight", "pto_update_task_weight" );
function pto_update_task_weight() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$weights = isset($_POST['weights']) ? pto_sanitize_rec_array( wp_unslash( $_POST['weights'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( empty($weights) ) {
		$weights = array();
	}
	foreach ( $weights as $key => $weight ) {
		$task_details = get_post_meta($key, 'task_details', true);
		$task_details['weight'] = isset($weight['weight']) ? $weight['weight'] : '';
		update_post_meta($key, 'task_details', $task_details);
	}
	pto_send_json( array( 
		'error'   => false,
		'weights' => $weights,
	) );
}

add_action( "wp_ajax_pto_toggle_project_ms", "pto_toggle_project_ms" );
function pto_toggle_project_ms() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$direction = isset($_POST['direction']) ? sanitize_text_field( wp_unslash( $_POST['direction'] ) ) : '';
	$ms = isset($_POST['ms']) ? sanitize_text_field( wp_unslash( $_POST['ms'] ) ) : '';
	$project = isset($_POST['project']) ? sanitize_text_field( wp_unslash( $_POST['project'] ) ) : '';
	$user = wp_get_current_user();
	if ( $direction == 'hide' ) {
		$status = 'off';
	} else {
		$status = 'on';
	}
	$milestone_toggles = get_post_meta($project, 'milestone_toggles', true);
	$milestone_toggles = $milestone_toggles && is_array($milestone_toggles) ? $milestone_toggles : array();
	$milestone_toggles[ $user->ID ][ $ms ] = $status;
	update_post_meta($project, 'milestone_toggles', $milestone_toggles);
	pto_send_json( array( 
		'error'  => false,
		'status' => $status,
		'ms'     => $ms,
	) );
}

add_action('current_screen', 'pto_hide_project_title');
function pto_hide_project_title() {
	$screen = get_current_screen();
	if ( $screen->post_type == 'cqpim_project' ) {
		if ( ! current_user_can('cqpim_edit_project_dates') ) {
			remove_post_type_support('cqpim_project', 'title');
		}
	}
}

add_action( "wp_ajax_pto_update_project_team_email", "pto_update_project_team_email" );
function pto_update_project_team_email() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['project_id']) || empty($_POST['key']) || empty($_POST['demail']) ) {
		pto_send_json( array( 
			'error' => true,
		) );     
	} else {
		$project_id = intval( $_POST['project_id'] );
		$key = sanitize_text_field( wp_unslash( $_POST['key'] ) );
		$email = sanitize_email( wp_unslash( $_POST['demail'] ) );
		$project_contributors = get_post_meta($project_id, 'project_contributors', true);
		$project_contributors[ $key ]['demail'] = $email;    
		update_post_meta($project_id, 'project_contributors', $project_contributors);
		pto_send_json( array( 
			'error' => false,
		) );
	}
}

add_action( "wp_ajax_pto_update_project_team_pm", "pto_update_project_team_pm" );
function pto_update_project_team_pm() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['project_id']) || ( empty($_POST['key']) && $_POST['key'] != '0' ) || empty($_POST['pm']) ) {
		pto_send_json( array( 
			'error' => true,
		) );     
	} else {
		$project_id = intval( $_POST['project_id'] );
		$key = sanitize_text_field( wp_unslash( $_POST['key'] ) );
		$pm = sanitize_textarea_field( wp_unslash( $_POST['pm'] ) );
		$project_contributors = get_post_meta($project_id, 'project_contributors', true);
		$project_contributors[ $key ]['pm'] = ( $pm == 'yes' ) ? 1 : 0;
		update_post_meta($project_id, 'project_contributors', $project_contributors);
		pto_send_json( array( 
			'error' => false,
		) );
	}
}

add_action( "wp_ajax_pto_delete_project_updates", "pto_delete_project_updates" );
function pto_delete_project_updates() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['project_id']) ) {
		pto_send_json( array(
			'error' => true,
		) );
	} else {
        $project_id = intval( $_POST['project_id'] );
		delete_post_meta($project_id, 'project_progress');
        pto_send_json( array(
			'error' => false,
		) );
	}
}