<?php
add_action( "wp_ajax_pto_add_step_to_template", "pto_add_step_to_template" );
function pto_add_step_to_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$quote_id = isset($_POST['ID']) ? sanitize_text_field( wp_unslash( $_POST['ID'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$offset = isset($_POST['offset']) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : '';
	$milestone_id = isset($_POST['milestone_id']) ? sanitize_text_field( wp_unslash( $_POST['milestone_id'] ) ) : '';
	$cost = isset($_POST['cost']) ? sanitize_text_field( wp_unslash( $_POST['cost'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$order = isset($_POST['order']) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
	$include_weekends = isset($_POST['include_weekends']) ? sanitize_text_field( wp_unslash( $_POST['include_weekends'] ) ) : 0;
	if ( $title ) {
		$quote_elements = get_post_meta($quote_id, 'project_template', true);
		if ( empty($quote_elements) ) {
			$quote_elements = array();
		}
		$i = 0;
		if ( ! empty($quote_elements) ) {
			foreach ( $quote_elements as $element ) {
				$i++;
			}
		}
		$element_to_add = array(
			'title'            => $title,
			'id'               => $quote_id . '-' . $milestone_id,
			'offset'           => $offset,
			'cost'             => $cost,
			'weight'           => $order,
			'include_weekends' => $include_weekends,
			'tasks'            => array(),
		);
		$quote_elements['ms_key'] = $milestone_id + 1;
		$quote_elements['milestones'][ $quote_id . '-' . $milestone_id ] = $element_to_add;
		update_post_meta($quote_id, 'project_template', $quote_elements);
		pto_send_json( array( 
			'error'  => false,
			'errors' => __('Milestone Added.', 'projectopia-core'),
		) );
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('You must fill in the title as a minimum.', 'projectopia-core'),
		) );
	}
}

add_action( "wp_ajax_pto_create_task_template", "pto_create_task_template" );
function pto_create_task_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( isset($_POST['task_title']) ) {
		$task_title = isset($_POST['task_title']) ? sanitize_text_field( wp_unslash( $_POST['task_title'] ) ) : '';
		$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
		$task_milestone_id = isset($_POST['task_milestone_id']) ? sanitize_text_field( wp_unslash( $_POST['task_milestone_id'] ) ) : '';
		
		$task_offset = isset($_POST['offset']) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : '';
		$assignee = isset($_POST['assignee']) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
		$description = isset($_POST['description']) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
		$ppid = isset($_POST['task_project_id']) ? sanitize_text_field( wp_unslash( $_POST['task_project_id'] ) ) : '';
		$weight = isset($_POST['task_weight']) ? sanitize_text_field( wp_unslash( $_POST['task_weight'] ) ) : '';
		$milestones = get_post_meta($ppid, 'project_template', true);
		$milestones['milestones'][ $task_milestone_id ]['tasks']['task_id'] = $task_id + 1;
		$milestones['milestones'][ $task_milestone_id ]['tasks']['task_arrays'][ $task_id ] = array(
			'id'          => $task_id,
			'title'       => $task_title,
			'description' => $description,
			'offset'      => $task_offset,
			'weight'      => $weight,
			
			'assignee'    => $assignee,
		);
		update_post_meta($ppid, 'project_template', $milestones);
		pto_send_json( array( 
			'error'  => false,
			'errors' => __('The Task was successfully created.', 'projectopia-core'),
		) );
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('The Task could not be created at this time, please try again.', 'projectopia-core'),
		) );              
	}
}

add_action( "wp_ajax_pto_create_subtask_template", "pto_create_subtask_template" );
function pto_create_subtask_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( isset($_POST['task_title']) ) {
		$task_title = isset($_POST['task_title']) ? sanitize_text_field( wp_unslash( $_POST['task_title'] ) ) : '';
		$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
		$task_milestone_id = isset($_POST['task_milestone_id']) ? sanitize_text_field( wp_unslash( $_POST['task_milestone_id'] ) ) : '';
		$parent_id = isset($_POST['parent_id']) ? sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) : '';
		$offset = isset($_POST['offset']) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : '';
		$assignee = isset($_POST['assignee']) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
		$description = isset($_POST['description']) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
		$ppid = isset($_POST['task_project_id']) ? sanitize_text_field( wp_unslash( $_POST['task_project_id'] ) ) : '';
		$weight = isset($_POST['task_weight']) ? sanitize_text_field( wp_unslash( $_POST['task_weight'] ) ) : '';
		$milestones = get_post_meta($ppid, 'project_template', true);
		$milestones['milestones'][ $task_milestone_id ]['tasks']['task_arrays'][ $parent_id ]['subtasks']['task_id'] = $task_id + 1;
		$milestones['milestones'][ $task_milestone_id ]['tasks']['task_arrays'][ $parent_id ]['subtasks']['task_arrays'][ $task_id ] = array(
			'id'          => $task_id,
			'title'       => $task_title,
			'description' => $description,
			'offset'      => $offset,
			'weight'      => $weight,
			'assignee'    => $assignee,
		);
		update_post_meta($ppid, 'project_template', $milestones);
		pto_send_json( array( 
			'error'  => false,
			'errors' => __('The Task was successfully created.', 'projectopia-core'),
		) );
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('The Task could not be created at this time, please try again.', 'projectopia-core'),
		) );              
	}
}

add_action( "wp_ajax_pto_update_task_weight_template", "pto_update_task_weight_template");
function pto_update_task_weight_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$template_id = isset($_POST['template_id']) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';
	$weights = isset($_POST['weights']) ? pto_sanitize_rec_array( wp_unslash( $_POST['weights'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$template = get_post_meta($template_id, 'project_template', true);
	foreach ( $weights as $weight ) {
		$template['milestones'][ $weight['ms_id'] ]['tasks']['task_arrays'][ $weight['task_id'] ]['weight'] = $weight['weight'];
	}
	update_post_meta($template_id, 'project_template', $template);
	pto_send_json( array( 
		'error'  => false,
		'errors' => 'Task updated.',
	) );
}

add_action( "wp_ajax_pto_update_subtask_weight_template", "pto_update_subtask_weight_template");
function pto_update_subtask_weight_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$template_id = isset($_POST['template_id']) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';
	$weights = isset($_POST['weights']) ? pto_sanitize_rec_array( wp_unslash( $_POST['weights'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$template = get_post_meta($template_id, 'project_template', true);
	foreach ( $weights as $weight ) {
		$template['milestones'][ $weight['ms_id'] ]['tasks']['task_arrays'][ $weight['parent_id'] ]['subtasks']['task_arrays'][ $weight['task_id'] ]['weight'] = $weight['weight'];
	}
	update_post_meta($template_id, 'project_template', $template);
	pto_send_json( array( 
		'error'  => false,
		'errors' => 'Task updated.',
	) );
}

add_action( "wp_ajax_pto_update_task_template", "pto_update_task_template" );
function pto_update_task_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$tid = isset($_POST['tid']) ? sanitize_text_field( wp_unslash( $_POST['tid'] ) ) : '';
	$ms = isset($_POST['ms']) ? sanitize_text_field( wp_unslash( $_POST['ms'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$description = isset($_POST['description']) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
	$offset = isset($_POST['offset']) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : '';
	$assignee = isset($_POST['assignee']) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
	if ( empty( $task_id ) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => 'Task ID cannot be found.',
		) );          
	} else {
		$template = get_post_meta($tid, 'project_template', true);
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $task_id ]['title'] = $title;
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $task_id ]['description'] = $description;
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $task_id ]['offset'] = $offset;
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $task_id ]['assignee'] = $assignee;
		update_post_meta($tid, 'project_template', $template);
		pto_send_json( array( 
			'error'  => false,
			'errors' => 'Task updated.',
		) );
	}
}

add_action( "wp_ajax_pto_update_subtask_template", "pto_update_subtask_template" );
function pto_update_subtask_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$tid = isset($_POST['tid']) ? sanitize_text_field( wp_unslash( $_POST['tid'] ) ) : '';
	$ms = isset($_POST['ms']) ? sanitize_text_field( wp_unslash( $_POST['ms'] ) ) : '';
	$parent = isset($_POST['parent']) ? sanitize_text_field( wp_unslash( $_POST['parent'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$description = isset($_POST['description']) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
	$offset = isset($_POST['offset']) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : '';
	$assignee = isset($_POST['assignee']) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
	if ( empty( $task_id ) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => 'Task ID cannot be found.',
		) );  
	} else {
		$template = get_post_meta($tid, 'project_template', true);
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_arrays'][ $task_id ]['title'] = $title;
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_arrays'][ $task_id ]['description'] = $description;
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_arrays'][ $task_id ]['offset'] = $offset;
		$template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_arrays'][ $task_id ]['assignee'] = $assignee;
		update_post_meta($tid, 'project_template', $template);
		pto_send_json( array( 
			'error'  => false,
			'errors' => 'Task updated.',
		) );
	}
}

add_action( "wp_ajax_pto_delete_task_template", "pto_delete_task_template" );
function pto_delete_task_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$ms = isset($_POST['ms']) ? sanitize_text_field( wp_unslash( $_POST['ms'] ) ) : '';
	$tid = isset($_POST['tid']) ? sanitize_text_field( wp_unslash( $_POST['tid'] ) ) : '';
	$template = get_post_meta($tid, 'project_template', true);  
	unset($template['milestones'][ $ms ]['tasks']['task_arrays'][ $task_id ]);
	if ( empty($template['milestones'][ $ms ]['tasks']['task_arrays']) ) {
		unset($template['milestones'][ $ms ]['tasks']['task_id']);
	}
	update_post_meta($tid, 'project_template', $template);      
	pto_send_json( array( 
		'error'    => false,
		'messages' => __('The task was successfully deleted.', 'projectopia-core'),
	) );
}

add_action( "wp_ajax_pto_delete_subtask_template", "pto_delete_subtask_template" );
function pto_delete_subtask_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$ms = isset($_POST['ms']) ? sanitize_text_field( wp_unslash( $_POST['ms'] ) ) : '';
	$parent = isset($_POST['parent']) ? sanitize_text_field( wp_unslash( $_POST['parent'] ) ) : '';
	$tid = isset($_POST['tid']) ? sanitize_text_field( wp_unslash( $_POST['tid'] ) ) : '';
	$template = get_post_meta($tid, 'project_template', true);  
	unset($template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_arrays'][ $task_id ]);
	if ( empty($template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_arrays']) ) {
		unset($template['milestones'][ $ms ]['tasks']['task_arrays'][ $parent ]['subtasks']['task_id']);
	}
	update_post_meta($tid, 'project_template', $template);      
	pto_send_json( array( 
		'error'    => false,
		'messages' => __('The task was successfully deleted.', 'projectopia-core'),
	) );
}

add_action( "wp_ajax_pto_clear_all_template", "pto_clear_all_template" );
function pto_clear_all_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$tid = isset($_POST['tid']) ? sanitize_text_field( wp_unslash( $_POST['tid'] ) ) : '';
	delete_post_meta($tid, 'project_template');
	pto_send_json( array( 
		'error'    => false,
		'messages' => __('The template was successfully cleared.', 'projectopia-core'),
	) );     
}

add_action( "wp_ajax_pto_apply_template", "pto_apply_template" );
function pto_apply_template() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$item_ref = isset( $_POST['quote_id'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_id'] ) ) : '';
	$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$template = isset( $_POST['template'] ) ? sanitize_text_field( wp_unslash( $_POST['template'] ) ) : '';
	// We need to send over the highest MS ID and weight
	$hwe = pto_calculate_ms_weight( $item_ref, $type );
	$hid = pto_calculate_ms_id( $item_ref, $type );
  	if ( empty( $template ) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must choose a template!', 'projectopia-core') . '</div>',
		) );     
	} else {
		$start_date = time();
		if ( $type == 'quote' ) {
			$elements = get_post_meta( $item_ref, 'quote_elements', true );
  			$item_details = get_post_meta( $item_ref, 'quote_details', true );
			$start_date = isset( $item_details['start_date'] ) ? $item_details['start_date'] : $start_date;
		} else {
			$elements = get_post_meta( $item_ref, 'project_elements', true );
			$contract_status = pto_get_contract_status( $item_ref );
			$item_details = get_post_meta( $item_ref, 'project_details', true );
			$start_date = isset( $item_details['start_date'] ) ? $item_details['start_date'] : $start_date;
		}

		$template_contents = get_post_meta( $template, 'project_template', true );
		if ( empty( $template_contents['milestones'] ) ) {
			pto_send_json( array( 
				'error'  => true,
				'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The chosen template does not contain any Milestones or Tasks.', 'projectopia-core') . '</div>',
			) );       
		} else {
			$milestones = ! empty( $elements ) ? $elements : array();
			$i = $hid;
			$next_ms_date = $start_date;

			$index = 0;
			$filtered_milestones = [];
			foreach ( $template_contents['milestones'] as $milestone ) {
				$mweight = isset( $milestone['weight'] ) ? $milestone['weight'] : $index;
				$filtered_milestones[ $mweight ] = $milestone;
				$index++;
			}
			ksort( $filtered_milestones );
			
			foreach ( $filtered_milestones as $milestone ) {
				$this_milestone_start = $next_ms_date;

				$mtitle = isset( $milestone['title'] ) ? $milestone['title'] : '';
				$mcost = isset( $milestone['cost'] ) ? $milestone['cost'] : '';
				$mweight = isset( $milestone['weight'] ) ? $milestone['weight'] : 0;
				if ( ! empty( $hwe ) ) {
					$mweight = $mweight + $hwe;
				}

				$project_end = pto_convert_date_range( $next_ms_date, $milestone['offset'], $milestone['include_weekends'] );
				$milestones[ $i ] = array(
					'title'            => $milestone['title'],
					'id'               => $i,
					'deadline'         => $project_end,
					'start'            => $next_ms_date,
					'cost'             => $milestone['cost'],
					'weight'           => $mweight,
					'offset'           => $milestone['offset'],
					'include_weekends' => $milestone['include_weekends'],
					'status'           => 'pending',
				);

				if ( ! empty( $milestone['tasks']['task_arrays'] ) ) {
					$next_task_date = $this_milestone_start;
					foreach ( $milestone['tasks']['task_arrays'] as $key => $task ) {
						$title = isset( $task['title'] ) ? $task['title'] : '';
						$description = isset( $task['description'] ) ? $task['description'] : '';
						$weight = isset( $task['weight'] ) ? $task['weight'] : $key;
						$assignee = isset( $task['assignee'] ) ? $task['assignee'] : '';
						$new_task = array(
							'post_type'     => 'cqpim_tasks',
							'post_status'   => 'publish',
							'post_content'  => '',
							'post_title'    => $title,
							'post_password' => pto_random_string( 10 ),
						);
						$task_pid = wp_insert_post( $new_task, true );
						if ( is_wp_error( $task_pid ) ) {
							pto_send_json( array( 
								'error'  => true,
								'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem creating the tasks. Please try again later.', 'projectopia-core') . '</div>',
							) );
						} else {
							$task_updated = array(
								'ID'        => $task_pid,
								'post_name' => $task_pid,
							);                      
							wp_update_post( $task_updated );
							update_post_meta( $task_pid, 'milestone_id', $i );    
							update_post_meta( $task_pid, 'project_id', $item_ref );
							if ( $type == 'project' ) {
								if ( ! empty( $contract_status ) && $contract_status == 2 ) {
									update_post_meta( $task_pid, 'active', true );
								}
							} else {
								update_post_meta( $task_pid, 'active', true );
							}
							$this_task_end = pto_convert_date_range( $next_task_date, $task['offset'], $milestone['include_weekends'] );
							$task_details = array(
								'deadline'         => $this_task_end,
								'status'           => 'pending',
								'task_start'       => $next_task_date,
								'task_description' => $description,
								'task_pc'          => 0,
								'task_priority'    => 'normal',
								'weight'           => $weight,
							);
							update_post_meta( $task_pid, 'task_details', $task_details ); 
							update_post_meta( $task_pid, 'offset', $task['offset'] );
							$next_subtask_date = $next_task_date;
							$next_task_date = $this_task_end;                           
							if ( ! empty( $assignee ) && pto_is_team_on_project( $item_ref, $assignee ) ) {
								update_post_meta( $task_pid, 'owner', $assignee );
							}                           

							if ( ! empty( $task['subtasks']['task_arrays'] ) ) {
								foreach ( $task['subtasks']['task_arrays'] as $sub_key => $subtask ) {
									$title = isset( $subtask['title'] ) ? $subtask['title'] : '';
									$description = isset( $subtask['description'] ) ? $subtask['description'] : '';
									$weight = isset( $subtask['weight'] ) ? $subtask['weight'] : $sub_key;
									$assignee = isset( $subtask['assignee'] ) ? $subtask['assignee'] : '';
									$new_subtask = array(
										'post_type'     => 'cqpim_tasks',
										'post_status'   => 'publish',
										'post_content'  => '',
										'post_title'    => $title,
										'post_parent'   => $task_pid,
										'post_password' => pto_random_string( 10 ),
									);
									$subtask_pid = wp_insert_post( $new_subtask, true );
									if ( is_wp_error( $subtask_pid ) ) {
										pto_send_json( array( 
											'error'  => true,
											'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . __('There was a problem creating the tasks. Please try again later.', 'projectopia-core') . '</div>',
										) );                         
									} else {
										$task_updated = array(
											'ID'        => $subtask_pid,
											'post_name' => $subtask_pid,
										);                      
										wp_update_post( $task_updated );
										update_post_meta( $subtask_pid, 'milestone_id', $i );
										update_post_meta( $subtask_pid, 'project_id', $item_ref );
										if ( $type == 'project' ) {
											if ( ! empty( $contract_status ) && $contract_status == 2 ) {
												update_post_meta( $subtask_pid, 'active', true );
											}
										} else {
											update_post_meta( $subtask_pid, 'active', true );
										}
										$this_subtask_end = pto_convert_date_range( $next_subtask_date, $subtask['offset'], $milestone['include_weekends'] );
										$task_details = array(
											'deadline'   => $this_subtask_end,
											'status'     => 'pending',
											'task_start' => $next_subtask_date,
											'task_description' => $description,
											'task_pc'    => 0,
											'task_priority' => 'normal',
											'weight'     => $weight,
										);
										update_post_meta( $subtask_pid, 'task_details', $task_details );
										update_post_meta( $subtask_pid, 'offset', $subtask['offset'] );
										$next_subtask_date = $this_subtask_end; 
										if ( ! empty( $assignee ) && pto_is_team_on_project( $item_ref, $assignee ) ) {
											update_post_meta( $subtask_pid, 'owner', $assignee );
										}                                   
									}                                   
								}
							}
						}
					}
				}
				$i++;
				$next_ms_date = pto_convert_date_range( $next_ms_date, $milestone['offset'], $milestone['include_weekends'] );
			}

			if ( $type == 'quote' ) {
				update_post_meta( $item_ref, 'quote_elements', $milestones );
				$item_details = get_post_meta( $item_ref, 'quote_details', true );
				$item_details['finish_date'] = $project_end;
				update_post_meta( $item_ref, 'quote_details', $item_details );
			} else {
				update_post_meta( $item_ref, 'project_elements', $milestones );
				$item_details = get_post_meta( $item_ref, 'project_details', true );
				$item_details['finish_date'] = $project_end;
				update_post_meta( $item_ref, 'project_details', $item_details );
			}

			pto_send_json( array( 
				'error'    => false,
				'messages' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The template was successfully applied.', 'projectopia-core') . '</div>',
			) );
		}   
	}
}

add_action( "wp_ajax_pto_check_template_assignees", "pto_check_template_assignees");
function pto_check_template_assignees() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$template = isset($_POST['template']) ? sanitize_text_field( wp_unslash( $_POST['template'] ) ) : '';
	$project_contributors = get_post_meta($project, 'project_contributors', true);
	if ( empty($project_contributors) ) {
		$project_contributors = array();
	}
	$template = get_post_meta($template, 'project_template', true);
	if ( empty($template) ) {
		$template = array();
	}
	$milestones = isset($template['milestones']) ? $template['milestones'] : '';
	$assignees = array();
	foreach ( $milestones as $key => $element ) {
		$tasks = isset($element['tasks']['task_arrays']) ? $element['tasks']['task_arrays'] : array();
		foreach ( $tasks as $task ) {
			if ( ! empty($task['assignee']) ) {
				$assignees[] = $task['assignee'];
			}
			$subtasks = isset($task['subtasks']['task_arrays']) ? $task['subtasks']['task_arrays'] : array();   
			foreach ( $subtasks as $subtask ) {
				if ( ! empty($subtask['assignee']) ) {
					$assignees[] = $subtask['assignee'];
				}           
			}           
		}
	}
	$assignees = array_unique($assignees);
	foreach ( $assignees as $key => $assignee ) {
		foreach ( $project_contributors as $contributor ) {
			if ( $contributor['team_id'] == $assignee ) {
				unset($assignees[ $key ]);
			}
		}
	}
	if ( ! empty($assignees) ) {
		$message = '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('The selected template contains tasks that are assigned to team members who have not been added to this project. Please either add the following team members to the project or click Apply Template to skip assignment of the affected tasks:', 'projectopia-core');    
		$message .= '<br /><br />';
		foreach ( $assignees as $assignee ) {
			$team_details = get_post_meta($assignee, 'team_details', true);
			$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : __('Team Member name not set', 'projectopia-core');
			$message .= $team_name . '<br />';
		}
		pto_send_json( array( 
			'error'   => true,
			'message' => $message . '</div>',
		) );
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '',
	) );
}

function pto_convert_date_range( $next_date, $offset, $include_weekends = 0 ) {
	if ( ! $offset || empty( $offset ) ) {
		return $next_date;
	}

	$date = gmdate( 'Y-m-d H:i:s', $next_date );
	if ( is_null( $include_weekends ) || $include_weekends == 0 ) {
		$new_date = strtotime( $date . ' +' . $offset . ' weekdays' );
	} else {
		$new_date = strtotime( $date . ' +' . $offset . ' days' );
	}
	return $new_date;
}