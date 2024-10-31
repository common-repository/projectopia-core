<?php
add_action( "wp_ajax_pto_update_client_contacts", "pto_update_client_contacts" );
function pto_update_client_contacts() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$client_id = isset($_POST['client_id']) ? intval( $_POST['client_id'] ) : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contact = '';
	$main_user_id = $client_details['user_id'];
	$client_contact .= '<option value="' . esc_attr( $main_user_id ) . '">' . sprintf( esc_html__( '%s (Main Contact)', 'projectopia-core' ), $client_details['client_contact'] ) . '</option>';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	if ( empty($client_contacts) ) {
		$client_contacts = array();
	}
	foreach ( $client_contacts as $contact ) {
		$client_contact .= '<option value="' . esc_attr( $contact['user_id'] ) . '">' . esc_html( $contact['name'] ) . '</option>';
	}
	pto_send_json( array( 
		'error'    => false,
		'contacts' => $client_contact,
	) );
}

/* Create Milestone in Quotes */
add_action( "wp_ajax_pto_add_step_to_quote", "pto_add_step_to_quote" );
function pto_add_step_to_quote() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$quote_id = isset($_POST['ID']) ? sanitize_text_field( wp_unslash( $_POST['ID'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$start = isset($_POST['start']) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

	if ( ! empty($start) ) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	$deadline = isset($_POST['deadline']) ? sanitize_text_field( wp_unslash( $_POST['deadline'] ) ) : '';
	if ( ! empty($deadline) ) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	$weight = pto_calculate_ms_weight($quote_id, $type);
	$milestone_id = pto_calculate_ms_id($quote_id, $type);
	$cost = isset($_POST['cost']) ? sanitize_text_field( wp_unslash( $_POST['cost'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

	$milestone_toggles = get_post_meta($quote_id, 'milestone_toggles', true);
	$milestone_toggles = $milestone_toggles && is_array($milestone_toggles) ? $milestone_toggles : array();

	$project_colours = get_post_meta($quote_id, 'project_colours', true);
	$user = wp_get_current_user();
	$current_team = pto_get_team_from_userid($user);
	if ( empty( $milestone_toggles[ $user->ID ] ) && ! empty( $quote_elements ) ) {
		foreach ( $quote_elements as $key => $element ) {
			$milestone_toggles[ $user->ID ][ $element['id'] ] = 'on';
		}
	}

	if ( $title && $deadline && $start ) {
		if ( $type == 'project' ) {
			$quote_elements = get_post_meta($quote_id, 'project_elements', true);
		} else {
			$quote_elements = get_post_meta($quote_id, 'quote_elements', true);
		}
		$quote_elements = $quote_elements && is_array($quote_elements) ? $quote_elements : array();
		$element_to_add = array(
			'title'    => $title,
			'id'       => $milestone_id,
			'deadline' => $deadline,
			'start'    => $start,
			'cost'     => $cost,
			'weight'   => $weight,
			'status'   => 'pending',
		);
		$quote_elements[ $milestone_id ] = $element_to_add;

		if ( $type == 'project' ) {
			update_post_meta($quote_id, 'project_elements', $quote_elements);
		} else {
			update_post_meta($quote_id, 'quote_elements', $quote_elements);
		}
		if ( $type == 'project' ) {
			
			$task_status = 'pending';
			$task_deadline = isset($deadline) ? $deadline : '';
			if ( ! is_numeric($task_deadline) ) {
				$str_deadline = str_replace('/','-', $task_deadline);
				$deadline_stamp = strtotime($str_deadline);
			} else {
				$deadline_stamp = $task_deadline;
			}
			$now = time();
			if ( $task_status != 'complete' ) {
				if ( ! empty($deadline_stamp) && $now > $deadline_stamp ) {
					$milestone_status_string = '<span class="badgeOverdue">' . esc_html__('Overdue', 'projectopia-core') . '</span>';
				} else {
					$milestone_status_string = isset($element['status']) ? $element['status'] : '';
					if ( ! $milestone_status_string || $milestone_status_string == 'pending' ) {
						$milestone_status_string = '<span class="badgeOverdue  clientApproval">' . __( 'Pending', 'projectopia-core' ) . '</span>';
					} elseif ( $milestone_status_string == 'on_hold' ) {
						$milestone_status_string = '<span class="badgeOverdue clientApproval">' . esc_html__('On Hold', 'projectopia-core') . '</span>';
					}
				}
			} else {
				$milestone_status_string = '<span class="badgeOverdue approved">' . esc_html__('Complete', 'projectopia-core') . '</span>';
			}
			
			$el_p_s = isset($element['paid_status']) ? $element['paid_status'] : "";
			$markup = '<div class="dd-milestone" id="milestone-' . $milestone_id . '">';
				$markup .= '<input type="hidden" class="element_weight" name="element_weight[' . $milestone_id . ']" id="element_weight[' . $milestone_id . ']" data-msid="'. $milestone_id .'" value="' . $weight . '">';
				$markup .= '<div class="dd-milestone-title">';
					$markup .= '<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2">' . esc_html__('Milestone', 'projectopia-core') . '</span> ';
					$markup .= ' <span class="ms-title" id="ms_title_' . $milestone_id . '">' . $title . '</span>';
					$markup .= '<div class="dd-milestone-actions">';
						$markup .= '<button class="edit-milestone cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" value="' . $milestone_id . '" title="' . esc_attr__('Edit Milestone', 'projectopia-core') . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
						$markup .= '<button class="delete_stage_conf cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="' . $milestone_id . '" value="' . $milestone_id . '" title="' . esc_attr__('Delete Milestone', 'projectopia-core') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
						$markup .= '<button class="add_task cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="' . $milestone_id . '" data-project="' . $quote_id . '" value="' . $milestone_id . '" title="' . esc_attr__('Add Task to Milestone', 'projectopia-core') . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$markup .= '<button class="assign_all cqpim_button cqpim_small_button font-white bg-purple-sharp op rounded_2 cqpim_tooltip" data-ms="' . $milestone_id . '" data-project="' . $quote_id . '" value="' . $milestone_id . '" title="' . esc_attr__('Assign all Tasks', 'projectopia-core') . '"><i class="fa fa-user-circle" aria-hidden="true"></i></button>';             
						$markup .= '<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip ui-sortable-handle" value="' . $milestone_id . '" title="' . esc_attr__('Reorder Milestone', 'projectopia-core') . '"><i class="fa fa-sort" aria-hidden="true"></i></button>';
						$markup .= '<button id="toggle-' . $milestone_id . '" class="toggle_tasks cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" data-ms="' . $milestone_id . '" data-project="' . $quote_id . '" value="show" title="' . esc_attr__('Toggle Tasks', 'projectopia-core') . '"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>';              
					$markup .= '</div>';
					$markup .= '<div class="dd-milestone-status">' . $milestone_status_string . '</div>';
					$markup .= '<div class="clear"></div>';
					$markup .= '<div class="dd-milestone-info">';
						if ( current_user_can('cqpim_view_project_financials') ) {
							$markup .= '<span id="ms_cost_' . $milestone_id . '">' . pto_calculate_currency($quote_id, $cost) . '</span>';
						}
						$markup .= ' <i class="fa fa-circle dd-circle"></i> <strong>Start Date:</strong> <span id="ms_start_' . $milestone_id . '">' . wp_date(get_option('cqpim_date_format'), $start) . '</span>';
						$markup .= ' <i class="fa fa-circle dd-circle"></i> <strong>Deadline:</strong> <span id="ms_deadline_' . $milestone_id . '">' . wp_date(get_option('cqpim_date_format'), $deadline) . '</span>';
					$markup .= '</div>';
					$markup .= '<div class="clear"></div>';
				$markup .= '</div>';
				$markup .= '<div class="dd-tasks ui-sortable" data-ms="' . $milestone_id . '">';
				$markup .= '</div>';
			$markup .= '</div>';
			ob_start();
				require PTO_PATH . '/includes/meta/project/load-milestone-ajax.php';
			$markup = ob_get_contents();
			ob_end_clean();
			
		} elseif ( $type == 'quote' ) {
			
			$markup = '<div class="dd-milestone" id="milestone-' . $milestone_id . '">';
				$markup .= '<input type="hidden" class="element_weight" name="element_weight[' . $milestone_id . ']" id="element_weight[' . $milestone_id . ']" data-msid="'. $milestone_id .'" value="' . $weight . '">';
				$markup .= '<div class="dd-milestone-title">';
					$markup .= '<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2">' . esc_html__('Milestone', 'projectopia-core') . '</span> ';
					$markup .= ' <span class="ms-title" id="ms_title_' . $milestone_id . '">' . $title . '</span>';
					$markup .= '<div class="dd-milestone-actions">';
						$markup .= '<button class="edit-milestone cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" value="' . $milestone_id . '" title="' . esc_attr__('Edit Milestone', 'projectopia-core') . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
						$markup .= '<button class="delete_stage_conf cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="' . $milestone_id . '" value="' . $milestone_id . '" title="' . esc_attr__('Delete Milestone', 'projectopia-core') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
						$markup .= '<button class="add_task cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="' . $milestone_id . '" data-project="' . $quote_id . '" value="' . $milestone_id . '" title="' . esc_attr__('Add Task to Milestone', 'projectopia-core') . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$markup .= '<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip ui-sortable-handle" value="' . $milestone_id . '" title="' . esc_attr__('Reorder Milestone', 'projectopia-core') . '"><i class="fa fa-sort" aria-hidden="true"></i></button>';
					$markup .= '</div>';
					$markup .= '<div class="dd-milestone-info">';
						$markup .= '<span id="ms_cost_' . $milestone_id . '">' . pto_calculate_currency($quote_id, $cost) . '</span>';
						$markup .= ' <i class="fa fa-circle dd-circle"></i> <strong>Start Date:</strong> <span id="ms_start_' . $milestone_id . '">' . wp_date(get_option('cqpim_date_format'), $start) . '</span>';
						$markup .= ' <i class="fa fa-circle dd-circle"></i> <strong>Deadline:</strong> <span id="ms_deadline_' . $milestone_id . '">' . wp_date(get_option('cqpim_date_format'), $deadline) . '</span>';
					$markup .= '</div>';
					$markup .= '<div class="clear"></div>';
				$markup .= '</div>';
				$markup .= '<div class="dd-tasks ui-sortable" data-ms="' . $milestone_id . '">';
				$markup .= '</div>';
			$markup .= '</div>';
			ob_start();
				require PTO_PATH . '/includes/meta/quote/load-milestone-ajax.php';
			$markup = ob_get_contents();
			ob_end_clean();
		} else {
			
			// Support Ticket MS Layout
			
		}
		
		// Update project pregress
		
		if ( $type == 'project' ) {
			$current_user = wp_get_current_user();
			$current_user = $current_user->display_name;
			$project_progress = get_post_meta($quote_id, 'project_progress', true);
			$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

			$project_progress[] = array(
				'update' => __('Milestone Created', 'projectopia-core') . ': ' . $title,
				'date'   => time(),
				'by'     => $current_user,
			);
			update_post_meta($quote_id, 'project_progress', $project_progress );
		}
        // Check if task deadline is greater than start date
        if ( $deadline < $start ) {
            pto_send_json( array(
                'error'  => true,
                'errors' => __('Deadline should be greater than the start date.', 'projectopia-core'),
            ) );
        } else {
            pto_send_json( array(
                'error'  => false,
                'markup' => $markup,
            ) );
        }
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('You must fill in the title, start date and deadline as a minimum.', 'projectopia-core'),
		) );
	}
	exit();
}

/* Retrieve Milestone Data for Editing */
add_action( "wp_ajax_pto_retrieve_milestone_data", "pto_retrieve_milestone_data" );
function pto_retrieve_milestone_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	if ( empty($item) || empty($key) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('Either the Milestone ID or the Item ID are missing.', 'projectopia-core'),
		) );
	}
	if ( $type == 'quote' ) {
		$elements = get_post_meta($item, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item, 'project_elements', true);
	}
	$elements = $elements && is_array($elements) ? $elements : array();
	$data_to_return = array(
		'title'    => $elements[ $key ]['title'],
		'id'       => $elements[ $key ]['id'],
		'deadline' => wp_date(get_option('cqpim_date_format'), $elements[ $key ]['deadline']),
		'start'    => wp_date(get_option('cqpim_date_format'), $elements[ $key ]['start']),
		'cost'     => isset($elements[ $key ]['cost']) ? $elements[ $key ]['cost'] : '',
		'fcost'    => isset($elements[ $key ]['acost']) ? $elements[ $key ]['acost'] : '',
		'status'   => isset($elements[ $key ]['status']) ? $elements[ $key ]['status'] : '',
	);
	pto_send_json( array( 
		'error' => false,
		'data'  => $data_to_return,
	) );
}

/* Update Milestone Data After Editing */
add_action( "wp_ajax_pto_update_milestone_data", "pto_update_milestone_data" );
function pto_update_milestone_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	// Get Post Data
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$cost = isset($_POST['cost']) ? sanitize_text_field( wp_unslash( $_POST['cost'] ) ) : '';
	$start = isset($_POST['start']) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : '';
	$fcost = isset($_POST['fcost']) ? sanitize_text_field( wp_unslash( $_POST['fcost'] ) ) : $cost;
	$status = isset($_POST['status']) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'pending';
	
	// Convert Dates
	
	if ( ! empty($start) ) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	$deadline = isset($_POST['deadline']) ? sanitize_text_field( wp_unslash( $_POST['deadline'] ) ) : '';
	if ( ! empty($deadline) ) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	
	// Handle Errors
	
	if ( empty($item) || empty($key) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Either the Milestone ID or the Item ID are missing.', 'projectopia-core') . '</div>',
		) );
	}

	if ( empty($title) || empty($start) || empty($deadline) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must enter a title, start date and deadline for this milestone', 'projectopia-core') . '</div>',
		) );
	}

	if ( $type == 'project' && $status == 'complete' && empty($fcost) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must enter a finished cost before marking a milestone complete', 'projectopia-core') . '</div>',
		) );
	}
	
	// Get MS Meta
	
	if ( $type == 'quote' ) {
		$elements = get_post_meta($item, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item, 'project_elements', true);
	}
	
	// Update MS Keys
	
	$elements[ $key ]['title'] = $title;
	$elements[ $key ]['start'] = $start;
	$elements[ $key ]['deadline'] = $deadline;
	$elements[ $key ]['cost'] = $cost;
	$elements[ $key ]['acost'] = $fcost;
	$elements[ $key ]['status'] = $status;
	
	// Add Project Progress & Send MS Invoice if required
	
	if ( $type == 'project' && $status == 'complete' && $elements[ $key ]['already_comp'] != 1 ) {
		if ( empty($fcost) && $fcost !== "0" ) {
			$quote_elements[ $key ]['acost'] = $cost;
		}                   
		$project_progress = get_post_meta($item, 'project_progress', true);
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		$current_user = wp_get_current_user();
		$current_user = $current_user->display_name;
		/* translators: %s: Milestone Title */
		$text = sprintf(esc_html__('Milestone Completed: %s', 'projectopia-core'), $title);
		$project_progress[] = array(
			'update' => $text,
			'date'   => time(),
			'by'     => $current_user,
		);
		update_post_meta($item, 'project_progress', $project_progress );    
		$checked = get_option('invoice_workflow');
		if ( $checked == 1 ) {
			pto_create_ms_completion_invoice($item, $elements[ $key ]);
		}
		$elements[ $key ]['already_comp'] = true;
	}
	
	// Update Milestone Values
	if ( $type == 'quote' ) {
		update_post_meta($item, 'quote_elements', $elements);
	} else {
		update_post_meta($item, 'project_elements', $elements);
	}
	
	// Retrieve updated values
	if ( $type == 'quote' ) {
		$elements = get_post_meta($item, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item, 'project_elements', true);
	}
	$elements = $elements && is_array($elements) ? $elements : array();
	
	// If project, also dynamically update Milestone status string
	if ( $type == 'project' ) {
		$now = time();
		if ( $now > $deadline ) {
			$milestone_status_string = '<span class="badgeOverdue">' . esc_html__('Overdue', 'projectopia-core') . '</span>';  
		} else {    
			if ( $status == 'complete' ) {
				$milestone_status_string = '<span class="badgeOverdue approved">' . esc_html__('Complete', 'projectopia-core') . '</span>';
			} elseif ( $status == 'on_hold' ) {
				$milestone_status_string = '<span class="badgeOverdue clientApproval">' . esc_html__('On Hold', 'projectopia-core') . '</span>';
			} elseif ( $status == 'pending' ) {
				$milestone_status_string = '<span class="badgeOverdue clientApproval">' . esc_html__('Pending', 'projectopia-core') . '</span>';
			}   
		}
	} else {    
		$milestone_status_string = '';  
	}   
	
	// prep return data
	$data_to_return = array(
		'item'          => $item,
		'title'         => $elements[ $key ]['title'],
		'id'            => $elements[ $key ]['id'],
		'deadline'      => wp_date(get_option('cqpim_date_format'), $elements[ $key ]['deadline']),
		'start'         => wp_date(get_option('cqpim_date_format'), $elements[ $key ]['start']),
		'cost'          => pto_calculate_currency($item, $elements[ $key ]['cost']),
		'fcost'         => pto_calculate_currency($item, $elements[ $key ]['acost']),
		'status'        => $elements[ $key ]['status'],
		'status_string' => $milestone_status_string,
	);
	
	// return
	pto_send_json( array( 
		'error'   => false,
		'data'    => $data_to_return,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The milestone was successfully updated', 'projectopia-core') . '</div>',
	) );
}

/* Update Milestone paid status */
add_action( "wp_ajax_pto_update_milestone_paid_status", "pto_update_milestone_paid_status" );
function pto_update_milestone_paid_status() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	// Get Post Data
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$paid_status = isset($_POST['paid_status']) ? sanitize_text_field( wp_unslash( $_POST['paid_status'] ) ) : 'unpaid';
	
	// Handle Errors
	if ( empty($item) || empty($key) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Either the Milestone ID or the Item ID are missing.', 'projectopia-core') . '</div>',
		) );
	}

	// Get MS Meta
	if ( $type == 'quote' ) {
		$elements = get_post_meta($item, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item, 'project_elements', true);
	}
	
	// Update MS Keys
	$elements[ $key ]['paid_status'] = $paid_status;
	
	// Update Milestone Values
	if ( $type == 'quote' ) {
		update_post_meta($item, 'quote_elements', $elements);
	} else {
		update_post_meta($item, 'project_elements', $elements);
	}
	
	// return
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The milestone was successfully updated', 'projectopia-core') . '</div>',
	) );
}

/* Delete the Milestone and attached tasks */
add_action( "wp_ajax_pto_delete_milestone_data", "pto_delete_milestone_data" );
function pto_delete_milestone_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	if ( empty($item) || empty($key) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Either the Milestone ID or the Item ID are missing.', 'projectopia-core') . '</div>',
		) );
	}
	
	if ( $type == 'quote' ) {
		$elements = get_post_meta($item, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item, 'project_elements', true);
	}
	unset($elements[ $key ]);
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key'       => 'milestone_id',
		'meta_value'     => $key,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$tasks = get_posts($args);
	foreach ( $tasks as $task ) {
		wp_delete_post($task->ID);      
	}
	if ( $type == 'quote' ) {
		update_post_meta($item, 'quote_elements', $elements);
	} else {
		update_post_meta($item, 'project_elements', $elements);
	}
	pto_send_json( array( 
		'error'     => false,
		'container' => '#milestone-' . $key,
		'message'   => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The milestone was deleted successfully', 'projectopia-core') . '</div>',
	) );
}

/* Reorder Milestone Data */
add_action( "wp_ajax_pto_reorder_milestone_data", "pto_reorder_milestone_data" );
function pto_reorder_milestone_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$weights = isset($_POST['weights']) ? pto_sanitize_rec_array( wp_unslash( $_POST['weights'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( $type == 'quote' ) {
		$elements = get_post_meta($item, 'quote_elements', true);
	} else {
		$elements = get_post_meta($item, 'project_elements', true);
	}

	foreach ( $weights as $weight ) {
		$msid = $weight['ms_id'];
		$weight = $weight['weight'] + 1;
		$elements[ $msid ]['weight'] = $weight;
	}

	if ( $type == 'quote' ) {
		update_post_meta($item, 'quote_elements', $elements);
	} else {
		update_post_meta($item, 'project_elements', $elements);
	}
	pto_send_json( array( 
		'error' => false,
	) );
}

/* Calaulate new Weights on Milestones */
function pto_calculate_ms_weight( $quote_id, $type ) {
	if ( $type == 'quote' ) {
		$quote_elements = get_post_meta($quote_id, 'quote_elements', true);
	} else {
		$quote_elements = get_post_meta($quote_id, 'project_elements', true);
	}
	$quote_elements = $quote_elements && is_array($quote_elements) ? $quote_elements : array();
	$weights = array();
	foreach ( $quote_elements as $element ) {
		$weights[] = $element['weight'];
	}
	$highest_weight = count($weights) ? max($weights) : "";
	if ( empty($highest_weight) ) {
		return '1';
	} else {
		return $highest_weight + 1;
	}
}

function pto_calculate_ms_id( $quote_id, $type ) {
	if ( $type == 'quote' ) {
		$quote_elements = get_post_meta($quote_id, 'quote_elements', true);
	} else {
		$quote_elements = get_post_meta($quote_id, 'project_elements', true);
	}
	$quote_elements = $quote_elements && is_array($quote_elements) ? $quote_elements : array();
	$ids = array();
	foreach ( $quote_elements as $element ) {
		$eid = str_replace($quote_id . '-', '', $element['id']);
		$ids[] = $eid;
	}
	$highest_id = count($ids) ? max($ids) : "";
	if ( empty($highest_id) ) {
		return $quote_id . '-1';
	} else {
		$id = $highest_id + 1;
		return $quote_id . '-' . $id;
	}
}

/**
 * Function to prepare html for quote pdf file.
 * 
 * @param int $quote_id Quote ID.
 * 
 * @return string $HTML
 */
function pto_quote_pdf_markup( $quote_id ) {
	ob_start();
	$logo             = get_option('company_logo');
	$logo_url         = isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : '';
	$quote_details    = get_post_meta($quote_id, 'quote_details', true);
	$quote_elements   = get_post_meta($quote_id, 'quote_elements', true);
	$quote_summary    = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
	$start_date       = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
	$client_id        = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$client_contact   = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
	$client_details   = get_post_meta($client_id, 'client_details', true);
	$client_contacts  = get_post_meta($client_id, 'client_contacts', true);
	$quote_client_ids = get_post_meta($client_id, 'client_ids', true);
	$client_user_id   = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$client_terms     = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
	$finish_date      = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
	$quote_header     = isset($quote_details['quote_header']) ? $quote_details['quote_header'] : '';
	$quote_footer     = isset($quote_details['quote_footer']) ? $quote_details['quote_footer'] : '';

	if ( empty( $client_contacts ) ) {
		$client_contacts = array();
	}

	if ( $client_contact == $client_user_id ) {
		$quote_header = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $quote_header);
	} else {
		$quote_header = str_replace('%%CLIENT_NAME%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '', $quote_header);
	}

	$quote_header = pto_replacement_patterns($quote_header, $quote_id, 'quote');
	$quote_footer = pto_replacement_patterns($quote_footer, $quote_id, 'quote');
	$deposit      = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
	$type         = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	$upper_type   = ucfirst($type);
	$currency     = get_option('currency_symbol');
	$vat          = get_post_meta($quote_id, 'tax_applicable', true);

	if ( ! empty( $vat ) ) {
		$vat = get_post_meta($quote_id, 'tax_rate', true);
	}

	if ( ! empty( $client_terms ) ) {
		$invoice_terms = $client_terms;
	} else {
		$invoice_terms = get_option('company_invoice_terms');
	}

	$tax_name = get_option('sales_tax_name');

	$vat_string = '';
	if ( ! empty( $vat ) ) {
		$vat_string = '+' . $tax_name;
	}

	$user = wp_get_current_user();
	if ( is_array( $user->roles ) ) {
		if ( in_array( 'cqpim_client', $user->roles ) ) {
			$client_logs = get_post_meta($quote_details['client_id'], 'client_logs', true);
			if ( empty( $client_logs ) ) {
				$client_logs = array();
			}
			$now   = time();
			$title = get_the_title();
			$title = str_replace('Private:', '', $title);
			$client_logs[ $now ] = array(
				'user' => $user->ID,
				/* translators: %1$s: Quote ID, %2$s: Quote Title */
				'page' => sprintf(esc_html__('Quote %1$s - %2$s', 'projectopia-core'), get_the_ID(), $title),
			);
			update_post_meta($quote_details['client_id'], 'client_logs', $client_logs);
		}
	}
	?>

	<!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <style>
                body {
                    font-family: DejaVu Sans !important;
                    background-color: #fff !important;
					font-size: 13px;
                }
				h2 {
                    font-family: DejaVu Sans !important;
					font-size: 18px;
                }
            </style>
        </head>
		<body class="content">
			<div class="cqpim-dash-item-full grid-item">
				<div>
					<?php if ( ! empty( $logo_url ) ) { ?>
						<div style="float: left;padding-top: 20px;">
							<img src="<?php echo esc_url( $logo_url ); ?>" />
						</div>
					<?php } ?>
					<div style="float:right;text-align:right;padding-top: 20px;">
						<?php echo esc_html( get_option('company_name') ); ?><br />
						<?php esc_html_e('Tel:', 'projectopia-core'); ?> <?php echo esc_html( get_option('company_telephone') ); ?><br />
						<?php esc_html_e('Email:', 'projectopia-core'); ?> <a href="mailto:<?php echo esc_attr( get_option('company_sales_email') ); ?>"><?php echo esc_html( get_option('company_sales_email') ); ?></a>
					</div>
					<div class="clear"></div>
					<?php
					if ( ! empty( $quote_header ) ) {
						echo wp_kses_post( wpautop( $quote_header ) );
					}
					if ( ! empty( $quote_summary ) ) {
						echo '<h2> ' . esc_html__( 'Summary', 'projectopia-core' ) . '</h2>';
						echo wp_kses_post( wpautop( $quote_summary ) );
					}
					if ( $start_date || $finish_date ) {
						echo '<h2>' . esc_html__( 'Project Dates', 'projectopia-core' ) . '</h2>';
					}
					if ( $start_date ) {
						if ( is_numeric( $start_date ) ) { 
							$start_date = wp_date(get_option('cqpim_date_format'), $start_date);
						}
						echo '<p>' . esc_html__('Start Date', 'projectopia-core') . ' - ' . esc_html( $start_date ) . '</p>';
					}
					if ( $finish_date ) {
						if ( is_numeric( $finish_date ) ) {
							$finish_date = wp_date(get_option('cqpim_date_format'), $finish_date);
						}
						echo '<p>' . esc_html__('Completion/Launch Date', 'projectopia-core') . ' - ' . esc_html( $finish_date ) . '</p>';
					}
					/** Prepare milestone and task list */
					if ( ! empty( $quote_elements ) ) {
						echo '<h2>' . esc_html__('Milestones', 'projectopia-core') . '</h2>';
						$msordered = array();
						$i  = 0;
						$mi = 0;
						foreach ( $quote_elements as $key => $element ) {
							$weight = isset($element['weight']) ? $element['weight'] : $mi;
							$msordered[ $weight ] = $element;
							$mi++;
						}
						ksort($msordered);
						foreach ( $msordered as $element ) {  ?>
							<div class="dd-milestone" style="border:1px solid #e5e5e5;padding: 10px;margin-bottom: 10px;border-radius: 2px;">
								<div class="dd-milestone-title">
									<span style="padding: 5px 10px;background-color: #578ebe;color:white;"><?php esc_html_e('Milestone', 'projectopia-core'); ?></span>  <span class="ms-title"><?php echo esc_html( $element['title'] ); ?></span>
									<div class="dd-milestone-info">
										<?php
										if ( ! empty( $element['cost'] ) ) { 
											echo '<span style="font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $element['cost']) ) . '</span>';
										}
										if ( ! empty( $element['start']) ) { ?>
											<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Start Date:', 'projectopia-core'); ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element['start']) ); ?>
										<?php }
										if ( ! empty( $element['deadline'] ) ) { ?>
											<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Deadline:', 'projectopia-core'); ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element['deadline']) ); ?>
										<?php } ?>
									</div>
									<div class="clear"></div>								
									<div class="dd-tasks">
										<?php
										$args = array(
											'post_type'  => 'cqpim_tasks',
											'posts_per_page' => -1,
											'meta_key'   => 'milestone_id',
											'meta_value' => $element['id'],
											'orderby'    => 'date',
											'order'      => 'ASC',
										);

										$tasks = get_posts( $args );
										if ( ! empty( $tasks ) ) {
											$ti      = 0;
											$ordered = array();
											$wi      = 0;
											foreach ( $tasks as $task ) {
												$task_details = get_post_meta($task->ID, 'task_details', true);
												$weight       = isset($task_details['weight']) ? $task_details['weight'] : $wi;
												if ( empty($task->post_parent ) ) {
													$ordered[ $weight ] = $task;
												}
												$wi++;
											}
											ksort($ordered);
											foreach ( $ordered as $task ) {
												$task_details  = get_post_meta($task->ID, 'task_details', true);
												$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
												$start         = isset($task_details['task_start']) ? $task_details['task_start'] : '';
												$description   = isset($task_details['task_description']) ? $task_details['task_description'] : '';
												$weight        = isset($task_details['weight']) ? $task_details['weight'] : 0;
												$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : ''; ?>
												<div class="dd-task" style="border:1px solid #e5e5e5;padding: 10px;margin:10px 0 0 0;border-radius: 2px;">
													<span style="padding: 5px 10px;background-color: #95a5a6;color:white;"><?php esc_html_e('Task', 'projectopia-core'); ?></span> <span class="ms-title"><?php echo esc_html( $task->post_title ); ?></span>
													<div class="dd-task-info">
														<?php if ( ! empty( $start ) ) { ?>
															<strong><?php esc_html_e('Start Date:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?>
														<?php } ?>
														<?php if ( ! empty( $task_deadline ) ) { ?>
															<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Deadline:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?>
														<?php } ?>
														<?php if ( ! empty($task_est_time) ) { ?>
															<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Est. Time:', 'projectopia-core') ?></strong> <?php echo esc_html( $task_est_time ); ?>
														<?php } ?>					
													</div>
													<div class="clear"></div>
													<div class="dd-subtasks">
														<?php $ti++;
														$args = array(
															'post_type'      => 'cqpim_tasks',
															'posts_per_page' => -1,
															'meta_key'       => 'milestone_id',
															'meta_value'     => $element['id'],
															'post_parent'    => $task->ID,
															'orderby'        => 'date',
															'order'          => 'ASC',
														);
														$subtasks = get_posts( $args );
														if ( ! empty($subtasks) ) {
															$subordered = array();
															$sti        = 0;
															$ssti       = 0;
															foreach ( $subtasks as $subtask ) {
																$task_details = get_post_meta($subtask->ID, 'task_details', true);
																$weight       = isset($task_details['weight']) ? $task_details['weight'] : $sti;
																$subordered[ $weight ] = $subtask;
																$sti++;
															}
															ksort($subordered);
															foreach ( $subordered as $subtask ) {
																$task_details  = get_post_meta($subtask->ID, 'task_details', true);
																$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
																$start         = isset($task_details['task_start']) ? $task_details['task_start'] : '';
																$description   = isset($task_details['task_description']) ? $task_details['task_description'] : '';
																$sweight       = isset($task_details['weight']) ? $task_details['weight'] : 0;
																$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : ''; ?>
																<div class="dd-task" style="border:1px solid #e5e5e5;padding: 10px;margin:10px 0 0 0;border-radius: 2px;">
																	<span style="padding: 5px 10px;background-color: #95a5a6;color:white;"><?php esc_html_e('Subtask', 'projectopia-core'); ?></span> <span class="ms-title"><?php echo esc_html( $subtask->post_title ); ?></span>
																	<div class="dd-task-info">
																		<?php
																			if ( ! empty( $start ) ) { ?>
																				<strong><?php esc_html_e('Start Date:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?>
																			<?php } ?>
																			<?php if ( ! empty($task_deadline) ) { ?>
																				<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Deadline:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?>
																			<?php } ?>	
																			<?php if ( ! empty($task_est_time) ) { ?>
																				<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Est. Time:', 'projectopia-core') ?></strong> <?php echo esc_html( $task_est_time ); ?>
																			<?php } ?>										
																		</div>	
																	</div>												
																<?php $ssti++;
															}
														} ?>
													</div>
												</div>
											<?php }
										}
									?>
									</div>
								</div>
							</div>
						<?php $i++;
						}
					}
					if ( ! empty($quote_elements) ) {
						echo  '<h2>' . esc_html__('Cost Breakdown', 'projectopia-core') . '</h2>';
						echo '<table style="width: 100%;border:1px solid gray;text-align:left;"><thead><tr>';
						echo '<th style="text-align:left;border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 2px solid #e7ecf1;padding: 10px 18px;">' . esc_html__('Milestone', 'projectopia-core') . '</th>';
						if ( $type == 'estimate' ) {
							echo '<th style="text-align:left;border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 2px solid #e7ecf1;padding: 10px 18px;">' . esc_html__('Estimated Cost', 'projectopia-core') . '</th>';
						} else {
							echo '<th style="text-align:left;border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 2px solid #e7ecf1;padding: 10px 18px;">' . esc_html__('Cost', 'projectopia-core') . '</th>';
						}
						echo '</tr></thead>';
						echo '<tbody>';
						$subtotal = 0;
						foreach ( $msordered as $key => $element ) {
							$cost = preg_replace("/[^\\d.]+/","", $element['cost']);
							if ( ! empty( $cost ) ) {
								$subtotal = $subtotal + $cost;
							}
							echo '<tr><td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;">' . esc_html( $element['title'] ) . '</td>';
							echo '<td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $cost) ) . '</td></tr>';
						}
						$project_details = get_post_meta($quote_id, 'quote_details', true);
						$client_id       = isset($project_details['client_id']) ? $project_details['client_id'] : '';
						$client_details  = get_post_meta($client_id, 'client_details', true);
						$client_tax      = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
						$client_stax     = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : ''; 
						$vat             = get_post_meta($quote_id, 'tax_applicable', true);
						if ( ! empty($vat) ) {
							$vat = get_post_meta($quote_id, 'tax_rate', true);
						}
						if ( ! empty($vat) && empty($client_tax) ) {
							$stax_rate = get_option('secondary_sales_tax_rate');
							$total_vat = $subtotal / 100 * $vat;
							if ( ! empty($stax_rate) ) {
								$total_stax = $subtotal / 100 * $stax_rate;
							}
							if ( ! empty($stax_rate) && empty($client_stax) ) {
								$total = $subtotal + $total_vat + $total_stax;
							} else {
								$total = $subtotal + $total_vat;
							}
							$tax_name = get_option('sales_tax_name');
							$stax_name = get_option('secondary_sales_tax_name');
							echo '<tr><td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;"><strong>' . esc_html__('Subtotal', 'projectopia-core') . ': </strong></td>
							<td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $subtotal) ) . '</td></tr>';
							echo '<tr><td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;"><strong>' . esc_html( $tax_name ) . ': </strong></td>
							<td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $total_vat) ) . '</td></tr>';
							if ( ! empty($stax_rate) && empty($client_stax) ) {
								echo '<tr><td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;"><strong>' . esc_html( $stax_name ) . ': </strong></td>
								<td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $total_stax) ) . '</td></tr>';
							}
							echo '<tr><td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;"><strong>' . esc_html__('TOTAL', 'projectopia-core') . ': </strong></td>
							<td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $total) ) . '</td></tr>';
						} else {
							echo '<tr><td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;"><strong>' . esc_html__('TOTAL', 'projectopia-core') . ': </strong></td>
							<td style="border-left: 1px solid #e7ecf1;border-top: 1px solid #e7ecf1;border-bottom: 1px solid #e7ecf1;text-align: left;padding: 10px 18px;font-family: DejaVu Sans !important;">' . esc_html( pto_calculate_currency($quote_id, $subtotal) ) . '</td></tr>'; 
						}
						echo '</tbody></table>'; 
					}
					if ( $type == 'estimate' ) { ?>
						<br />
						<h4><strong><?php esc_html_e('NOTE:', 'projectopia-core'); ?> </strong><?php esc_html_e('THIS IS AN ESTIMATE, SO THESE PRICES MAY NOT REFLECT THE FINAL PROJECT COST.', 'projectopia-core'); ?></h4>
					<?php } ?>
					<h2><?php esc_html_e('Payment Plan', 'projectopia-core'); ?></h2>
					<p><strong><?php esc_html_e('Deposit', 'projectopia-core'); ?></strong></p>
					<?php
					if ( ! $deposit || $deposit == 'none' ) {
						echo '<p>' . esc_html__('We do not require an up-front deposit payment on this project.', 'projectopia-core') . '</p>';
					} else {
						if ( empty( $subtotal ) ) {
							$subtotal = 0;
						}
						$deposit_amount = (int)$subtotal / 100 * (int)$deposit;
						echo '<p>';
						/* translators: %s: Deposit Percentage */
						printf(esc_html__('We require an initial deposit payment of %s percent on this project which will be invoiced on acceptance.', 'projectopia-core'), esc_html( $deposit ));
						echo '</p>';
					}
					$terms = get_option( 'enable_quote_terms' );
					$default_contract = get_option( 'default_contract_text' );
					$quote_contract = isset( $quote_details['default_contract_text'] ) ? intval( $quote_details['default_contract_text'] ) : '';
					if ( empty( $quote_contract ) ) {
						$quote_contract = $default_contract;
					}
					if ( $quote_contract ) {
						$quote_contract_text = get_post_meta( $quote_contract, 'terms', true );
						if ( $quote_contract_text ) {
							$text = pto_replacement_patterns( $quote_contract_text, $post->ID, 'quote' );
						}
					}
					if ( $terms == 1 && ! empty( $text ) ) {
						echo '<h2>' . esc_html__( 'TERMS &amp; CONDITIONS', 'projectopia-core' ) . '</h2>';
						echo wp_kses_post( wpautop( $text ) );  
					}
					if ( ! empty( $quote_footer ) ) {
						echo wp_kses_post( wpautop( $quote_footer ) ); 
					} ?>
					<div class="clear"></div>
				</div>
			</div>
		</body>
	</html>
	<?php
	return ob_get_clean();
}

/**
 * Function to generate the pdf file for quote.
 * 
 * @param int $quote_id Quote ID.
 * 
 * @return string $filename
 */
function pto_generate_pdf_quote( $quote_id ) {
	if ( empty( $quote_id ) ) {
		return;
	}
	$html = pto_quote_pdf_markup( $quote_id );
	require_once( PTO_PATH . '/assets/dompdf/autoload.inc.php' );
	$upload_dir = wp_upload_dir();
	$dompdf = new \Dompdf\Dompdf();
	$dompdf->loadHtml( $html );
	$dompdf->setPaper( 'A4', 'portrait' );
	$dompdf->set_option( 'isHtml5ParserEnabled', true );
	$dompdf->set_option( 'isFontSubsettingEnabled', true );
	$dompdf->set_option( 'isRemoteEnabled', true );
	$dompdf->render();
	$output = $dompdf->output();
	$filename = trailingslashit( $upload_dir['basedir'] ) . "pto-uploads/quote_$quote_id.pdf";
	file_put_contents( $filename, $output ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
	return $filename;
}

add_action( "wp_ajax_pto_process_quote_emails", "pto_process_quote_emails" );
function pto_process_quote_emails() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$quote_id = isset( $_POST['quote_id'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_id'] ) ) : '';
	$quote_object = get_post($quote_id);
	$quote_details = get_post_meta($quote_id, 'quote_details', true);
	$client_id = $quote_details['client_id'];
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : $client_details['user_id'];
	$client_main_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	pto_add_team_notification($client_id, $user->ID, $quote_id, 'quote_sent', 'quote');
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
	$email_content = get_option('quote_default_email');
	if ( $client_contact == $client_main_id ) {
		$email_content = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', $client_details['client_email'], $email_content);
	} else {
		$email_content = str_replace('%%CLIENT_NAME%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '', $email_content);
		$email_content = str_replace('%%CLIENT_EMAIL%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['email'] : '', $email_content);
	}

	$email_content = str_replace( '%%QUOTE_CLIENT_URL%%', get_the_permalink( $quote_id ).'?pto-page=quote', $email_content );
	$message = pto_replacement_patterns($email_content, $quote_id, 'quote');
	$subject = get_option('quote_email_subject');
	if ( $client_contact == $client_main_id ) {
		$subject = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $subject);
	} else {
		$subject = str_replace('%%CLIENT_NAME%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '', $subject);
	}
	$subject = pto_replacement_patterns($subject, $quote_id, 'quote');

	//Attach pdf for quote.
	$attachments = array();
	$file_name   = '';
	if ( ! empty( get_option( 'quote_email_pdf_attach') ) ) {
		$file_name = pto_generate_pdf_quote( $quote_id );
		$attachments = array( $file_name );
	}

	if ( $to && $subject && $message ) {
		if ( pto_send_emails( $to, $subject, $message, '', $attachments, 'sales' ) ) :
			$current_user = wp_get_current_user();
			$current_user = $current_user->display_name;
			$quote_details = get_post_meta($quote_id, 'quote_details', true);
			$quote_details['quote_create_by'] = get_current_user_id();
			$quote_details['sent_details'] = array(
				'date' => time(),
				'by'   => $current_user,
				'to'   => $to,
			);
			unset($quote_details['confirmed']);
			unset($quote_details['confirmed_details']);
			$quote_details['sent'] = true;
			update_post_meta($quote_id, 'quote_details', $quote_details );
			// Remove file after send email.
			if ( ! empty( $file_name ) ) {
				unlink( $file_name );
			}
			pto_send_json( array( 
				'error'   => false,
				'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Email sent successfully...', 'projectopia-core') . '</div>',
			) );      
		else :
			pto_send_json( array(
				'error'  => true,
				'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There was a problem with WP Mail, check that your installation is able to send emails and try again.', 'projectopia-core') . '</div>',
			) );
		endif;  
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('There was a problem sending the email, check that you have completed ALL email subject and content fields in the settings.', 'projectopia-core') . '</div>',
		) );
	}
	exit();
}

add_action("wp_ajax_nopriv_pto_client_accept_quote", "pto_client_accept_quote" );
add_action("wp_ajax_pto_client_accept_quote", "pto_client_accept_quote" );
function pto_client_accept_quote() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$quote_id = isset( $_POST['quote_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['quote_id'] ) ) : 0;
	$signed_name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$pm_name = isset( $_POST['pm_name'] ) ? sanitize_text_field( wp_unslash( $_POST['pm_name'] ) ) : '';
	$quote_details = get_post_meta( $quote_id, 'quote_details', true );
	$quote_ref = $quote_details['quote_ref'];
	$quote_type = $quote_details['quote_type'];
	$ip = pto_get_client_ip();
	if ( $signed_name ) {
		$quote_details['confirmed_details'] = array(
			'date' => time(),
			'by'   => $signed_name,
			'ip'   => $ip,
		);
		$quote_details['confirmed'] = true;
		update_post_meta( $quote_id, 'quote_details', $quote_details );
		$sender_email = get_option('company_sales_email');
		$to = $sender_email;
		$attachments = array();
		$admin_quote = admin_url() . 'post.php?post=' . $quote_id . '&action=edit';
		/* translators: %1$s: Signed Author, %2$s: Quote Number */
		$subject = sprintf(esc_html__('%1$s has just accepted Quote: %2$s', 'projectopia-core'), $signed_name, $quote_ref);
		/* translators: %1$s: Signed Author, %2$s: Quote Number, %3$s: Quote Page Link */
		$content = sprintf(esc_html__('%1$s has just accepted Quote: %2$s. You can view the details by clicking here - %3$s', 'projectopia-core'), $signed_name, $quote_ref, $admin_quote);
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
				pto_add_team_notification($member->ID, $user->ID, $quote_id, 'quote_accepted');
			}
		}
		pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' );
		if ( get_option('enable_project_creation') == 1 ) {
			pto_create_project_from_quote($quote_id, $pm_name);
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

function pto_create_project_from_quote( $quote_id, $pm_name = NULL ) {
	$quote_details = get_post_meta($quote_id, 'quote_details', true);
	$quote_milestones = get_post_meta($quote_id, 'quote_elements', true);
	$tax_app = get_post_meta($quote_id, 'tax_applicable', true);    
	$tax_rate = get_post_meta($quote_id, 'tax_rate', true);
	$stax_app = get_post_meta($quote_id, 'stax_applicable', true);  
	$stax_rate = get_post_meta($quote_id, 'stax_rate', true);
	$quote_ref = isset($quote_details['quote_ref']) ? $quote_details['quote_ref'] : '';
	$quote_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	$deposit = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
	$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
	$contract = isset($quote_details['default_contract_text']) ? $quote_details['default_contract_text'] : '';
	$start_date = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
	$finish_date = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
	$project_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_team_member = get_post_meta($client_id, 'team_member', true);
	/* translators: %1$s: Company Name, %2$s: Quote Ref */
	$project_title = sprintf(esc_html__('%1$s - Project: %2$s', 'projectopia-core'), $client_company, $quote_ref);
	$new_project = array(
		'post_type'    => 'cqpim_project',
		'post_status'  => 'private',
		'post_content' => '',
		'post_title'   => $project_title,
	);
	$project_pid = wp_insert_post( $new_project, true );
	if ( ! is_wp_error( $project_pid ) ) {
		$project_updated = array(
			'ID'        => $project_pid,
			'post_name' => $project_pid,
		);                      
		wp_update_post( $project_updated );
		$project_details = array(
			'client_id'             => $client_id,
			'quote_ref'             => $quote_ref,
			'start_date'            => $start_date,
			'finish_date'           => $finish_date,
			'pm_name'               => $pm_name,
			'deposit_amount'        => $deposit,
			'default_contract_text' => $contract,
			'quote_id'              => $quote_id,
			'quote_type'            => $quote_type,
			'client_contact'        => $client_contact,
			'project_summary'       => $project_summary,
		);
		$project_progress = array();
		$project_progress[] = array(
			'update' => __('Project created', 'projectopia-core'),
			'date'   => time(),
			'by'     => __('System', 'projectopia-core'),
		);
		update_post_meta($project_pid, 'project_details', $project_details);
		if ( ! empty($client_team_member) ) {
			$contrib = array();
			$contrib[] = array(
				'pm'      => 0,
				'demail'  => 0,
				'team_id' => $client_team_member,
			);
			update_post_meta($project_pid, 'project_contributors', $contrib);
		}
		update_post_meta($project_pid, 'tax_applicable', $tax_app);
		update_post_meta($project_pid, 'tax_set', 1);
		update_post_meta($project_pid, 'tax_rate', $tax_rate);  
		update_post_meta($project_pid, 'stax_applicable', $stax_app);
		update_post_meta($project_pid, 'stax_set', 1);
		update_post_meta($project_pid, 'stax_rate', $stax_rate);
		update_post_meta($project_pid, 'project_elements', $quote_milestones);
		update_post_meta($project_pid, 'project_progress', $project_progress);
		$contract = pto_get_contract_status($project_pid);
		update_post_meta($project_pid, 'contract_status', $contract);

		update_post_meta($quote_id, 'converted_project', $project_pid);

		$currency = get_option('currency_symbol');
		$currency_code = get_option('currency_code');
		$currency_position = get_option('currency_symbol_position');
		$currency_space = get_option('currency_symbol_space'); 
		$client_currency = get_post_meta($project_details['client_id'], 'currency_symbol', true);
		$client_currency_code = get_post_meta($project_details['client_id'], 'currency_code', true);
		$client_currency_space = get_post_meta($project_details['client_id'], 'currency_space', true);      
		$client_currency_position = get_post_meta($project_details['client_id'], 'currency_position', true);
		$quote_currency = get_post_meta($project_details['quote_id'], 'currency_symbol', true);
		$quote_currency_code = get_post_meta($project_details['quote_id'], 'currency_code', true);
		$quote_currency_space = get_post_meta($project_details['quote_id'], 'currency_space', true);    
		$quote_currency_position = get_post_meta($project_details['quote_id'], 'currency_position', true);
		if ( ! empty($quote_currency) ) {
			update_post_meta($project_pid, 'currency_symbol', $quote_currency);
		} else {
			if ( ! empty($client_currency) ) {
				update_post_meta($project_pid, 'currency_symbol', $client_currency);
			} else {
				update_post_meta($project_pid, 'currency_symbol', $currency);
			}
		}
		if ( ! empty($quote_currency_code) ) {
			update_post_meta($project_pid, 'currency_code', $quote_currency_code);
		} else {
			if ( ! empty($client_currency_code) ) {
				update_post_meta($project_pid, 'currency_code', $client_currency_code);
			} else {
				update_post_meta($project_pid, 'currency_code', $currency_code);
			}
		}
		if ( ! empty($quote_currency_space) ) {
			update_post_meta($project_pid, 'currency_space', $quote_currency_space);
		} else {
			if ( ! empty($client_currency_space) ) {
				update_post_meta($project_pid, 'currency_space', $client_currency_space);
			} else {
				update_post_meta($project_pid, 'currency_space', $currency_space);
			}
		}
		if ( ! empty($quote_currency_position) ) {
			update_post_meta($project_pid, 'currency_position', $quote_currency_position);
		} else {
			if ( ! empty($client_currency_position) ) {
				update_post_meta($project_pid, 'currency_position', $client_currency_position);
			} else {
				update_post_meta($project_pid, 'currency_position', $currency_position);
			}
		}       
		if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
			$option = get_option('cqpim_bugs_auto');
			update_post_meta($project_pid, 'bugs_activated', $option);
		}
		$client_contracts = get_post_meta($client_id, 'client_contract', true);
		$auto_contract = get_option('auto_contract');
		$checked = get_option('enable_project_contracts');  
		if ( $auto_contract && $checked == 1 && empty($client_contracts) ) {
			pto_process_contract_emails($project_pid);
		}       
		if ( empty($checked) || ! empty($client_contracts) ) {
			$project_details = get_post_meta($project_pid, 'project_details', true);
			$project_details['sent'] = true;
			update_post_meta($project_pid, 'project_details', $project_details);
			$project_elements = get_post_meta($project_pid, 'project_elements', true);
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
					update_post_meta($task->ID, 'project_id', $project_pid);
					update_post_meta($task->ID, 'active', true);
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
						update_post_meta($subtask->ID, 'project_id', $project_pid);
						update_post_meta($subtask->ID, 'active', true);                     
					}
				}                   
			}
			if ( ! empty($deposit) && $deposit != 'none' ) {
				pto_create_deposit_invoice($project_pid);
			}
		} else {
			$project_elements = get_post_meta($project_pid, 'project_elements', true);
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
					update_post_meta($task->ID, 'project_id', $project_pid);
					update_post_meta($task->ID, 'owner', $client_team_member);
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
						update_post_meta($subtask->ID, 'project_id', $project_pid);
						update_post_meta($subtask->ID, 'owner', $client_team_member);
					}
				}                   
			}           
		}
		return $project_pid;
	} else {
		exit();
	}
}

add_action("wp_ajax_pto_manual_quote_convert", "pto_manual_quote_convert" );
function pto_manual_quote_convert() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$quote_id = isset($_POST['quote_id']) ? sanitize_text_field( wp_unslash( $_POST['quote_id'] ) ) : '';
	if ( empty($quote_id) ) {      
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The project could not be created. The Quote ID is missing.', 'projectopia-core') . '</div>',
		) );
	} 
	$url = pto_create_project_from_quote($quote_id);
	$url = get_edit_post_link($url);
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The project has been created, redirecting now...', 'projectopia-core') . '</div>',
		'url'     => $url,
	) );
}

// Action to call ajax function quote message delete.
add_action( "wp_ajax_nopriv_pto_delete_quote_message", "pto_delete_quote_message" );
add_action( "wp_ajax_pto_delete_quote_message", "pto_delete_quote_message" );
/**
 * Function to delete the quote message.
 */
function pto_delete_quote_message() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$quote_id = filter_input( INPUT_POST, "quote_id", FILTER_VALIDATE_INT );
	if ( empty( $quote_id ) ) {
		return;
	}

	$key = filter_input(INPUT_POST, "key", FILTER_VALIDATE_INT);
	if ( ! isset( $key ) ) {
		return;
	}

	$quote_messages = get_post_meta( $quote_id, 'quote_messages', true );
	$quote_messages = array_reverse( $quote_messages );
	unset( $quote_messages[ $key ] );
	$quote_messages = array_reverse( $quote_messages );
	update_post_meta( $quote_id, 'quote_messages', $quote_messages );
	return;
}

// Action to call ajax function to add a new message in quote.
add_action( "wp_ajax_pto_add_message_to_quote", "pto_add_message_to_quote" );

/**
 * Function to add new message in quote.
 */
function pto_add_message_to_quote() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$quote_id = filter_input( INPUT_POST, "quote_id", FILTER_VALIDATE_INT );
	if ( empty( $quote_id ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__( 'Quote ID is missing !', 'projectopia-core' ) . '</div>',
		) );
	}

	$visibility = '';
	if ( ! empty( $_POST['visibility'] ) ) {
		$visibility = filter_input( INPUT_POST, "visibility", FILTER_SANITIZE_STRING );
	}

	$message = filter_input( INPUT_POST, "message", FILTER_SANITIZE_STRING );
	$message = make_clickable( $message );
	if ( empty( $message ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You have not entered a message!', 'projectopia-core') . '</div>',
		) );
	}

	$who            = filter_input(INPUT_POST, "who", FILTER_SANITIZE_STRING);
	$date           = time();
	$current_user   = wp_get_current_user();
	$send_to_client = filter_input(INPUT_POST, "send_to_client", FILTER_VALIDATE_INT);
	$quote_messages = get_post_meta($quote_id, 'quote_messages', true);

	if ( empty( $quote_messages ) ) {
		$quote_messages = array();
	}

	$quote_messages[] = array(
		'visibility' => $visibility,
		'date'       => $date,
		'message'    => $message,
		'by'         => $current_user->display_name,
		'author'     => $current_user->ID,
	);

	update_post_meta($quote_id, 'quote_messages', $quote_messages);

	// Bell-icon notification to user when new message create. 
	if ( ! empty( $who ) ) {
		$quote_details = get_post_meta( $quote_id, 'quote_details', true );
		if ( ! empty( $send_to_client ) && 'client' !== $who && ! empty( $quote_details['client_id'] ) ) {
			$client_id      = $quote_details['client_id'];
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_email   = isset($client_details['client_email']) ? $client_details['client_email'] : '';
			$client_name    = isset($client_details['client_name']) ? $client_details['client_name'] : '';
			$addresses_to_send[] = array(
				'mail' => $client_email,
				'name' => $client_name,
			);
			pto_add_team_notification($client_id , $current_user->ID, $quote_id, 'quote_message', 'quote_message' );
			pto_quote_message_mailer($addresses_to_send, $quote_id, $message, 'client' );
		} elseif ( 'admin' !== $who ) {
			$admin_id = 1;
			if ( ! empty( $quote_details['quote_create_by'] ) ) {
				$admin_id = $quote_details['quote_create_by'];
			}
			$admin_user = get_user_by( 'id', $admin_id );
			$addresses_to_send[] = array(
				'mail' => $admin_user->user_email,
				'name' => $admin_user->display_name,
			);
			$team_id = pto_get_team_from_userid( $admin_user );
			pto_add_team_notification( $team_id, $current_user->ID, $quote_id, 'quote_message', 'cqpim_quote' );
			pto_quote_message_mailer($addresses_to_send, $quote_id, $message, 'team' );
		}
	}

	pto_send_json( array(
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Message added successfully', 'projectopia-core') . '</div>',
	) );
}

/**
 * Function to send emails for new quote message.
 *
 * @since 4.3.4
 *
 * @param array  $emails    List of recepient email ID.
 * @param int    @quote_id  Quote ID.
 * @param string $message   Message text.
 * @param string $type      Email for whom client/team.
 */
function pto_quote_message_mailer( $emails, $quote_id, $message, $type ) {
	$attachments = array();
	$subject = '';
	$content = '';
	$current_user = wp_get_current_user();
	foreach ( $emails as $to ) {
		if ( $type == 'client' ) {
			$subject = get_option('client_quote_message_subject');
			$content = get_option('client_quote_message_email');
			$content = str_replace('%%MESSAGE%%', $message, $content);
		} else {
			$subject = get_option('company_quote_message_subject');
			$content = get_option('company_quote_message_email');
			$content = str_replace('%%MESSAGE%%', $message, $content);
		}
		$mailto = isset($to['mail']) ? $to['mail'] : '';
		$name = isset($to['name']) ? $to['name'] : '';
		$to_user = get_user_by('email', $mailto);
		if ( ! empty( $current_user->display_name ) ) {
			$content = str_replace('%%CURRENT_USER%%', $current_user->display_name, $content);
			$subject = str_replace('%%CURRENT_USER%%', $current_user->display_name, $subject);
		}
		$content = str_replace('%%TEAM_NAME%%', $name, $content);
		$subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $quote_id . ']', $subject);
		$subject = pto_replacement_patterns($subject, $quote_id, 'quote');
		$content = pto_replacement_patterns($content, $quote_id, 'quote');
		if ( $current_user->ID != $to_user->ID ) {
			if ( $type == 'client' ) {
				pto_send_emails($mailto, $subject, $content, '', $attachments, 'sales');
			} else {
				pto_send_emails($mailto, $subject, $content, '', $attachments, 'other');
			}
		}
	}
}
