<?php

add_action( "wp_ajax_pto_remove_time_entry", "pto_remove_time_entry" );
function pto_remove_time_entry() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';       
	if ( ! $task_id ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('There is some missing data. Delete unsuccessful.', 'projectopia-core'),
		) );
	} else {
		$task_time = get_post_meta($task_id, 'task_time_spent', true);
		unset($task_time[ $key ]);
		$task_time = array_filter($task_time);
		update_post_meta($task_id, 'task_time_spent', $task_time);
		pto_send_json( array( 
			'error'   => false,
			'message' => '',
		) );
	}
	exit();
}
/* Calculates the Weight of new Tasks */
function pto_calculate_task_weight( $msid ) {
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key'       => 'milestone_id',
		'meta_value'     => $msid,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$tasks = get_posts($args);
	$tasks = $tasks && is_array($tasks) ? $tasks : array();
	$weights = array();
	foreach ( $tasks as $task ) {
		$task_details = get_post_meta($task->ID, 'task_details', true);
		$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
		$weights[] = $weight;
	}
	$highest_weight = count($weights) ? max($weights) : "";
	if ( empty($highest_weight) ) {
		return '1';
	} else {
		return $highest_weight + 1;
	}
}
/* Calculates the Weight of new SubTasks */
function pto_calculate_subtask_weight( $parent_task ) {
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'post_parent'    => $parent_task,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$tasks = get_posts( $args );
	$tasks = $tasks && is_array( $tasks ) ? $tasks : array();
	$weights = array();
	if ( ! empty( $tasks ) ) {
    	foreach ( $tasks as $task ) {
    		$task_details = get_post_meta($task->ID, 'task_details', true);
    		$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
    		$weights[] = $weight;
    	}

		if ( ! empty( $weights ) ) {
    	    $highest_weight = max($weights);
    	    if ( ! empty( $highest_weight ) ) {
    	    	return $highest_weight + 1;
    	    }
		}
    }
	
	return '1';
}
/* Adds a Task to a Milestone */
add_action( "wp_ajax_nopriv_pto_create_task", "pto_create_task" );
add_action( "wp_ajax_pto_create_task", "pto_create_task" );
function pto_create_task() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_title = isset($_POST['task_title']) ? sanitize_text_field( wp_unslash( $_POST['task_title'] ) ) : '';
	$task_milestone_id = isset($_POST['task_milestone_id']) ? sanitize_text_field( wp_unslash( $_POST['task_milestone_id'] ) ) : '';
	$task_project_id = isset($_POST['task_project_id']) ? sanitize_text_field( wp_unslash( $_POST['task_project_id'] ) ) : '';
	$task_deadline = isset($_POST['task_finish']) ? sanitize_text_field( wp_unslash( $_POST['task_finish'] ) ) : '';
	$task_time = isset($_POST['task_time']) ? sanitize_text_field( wp_unslash( $_POST['task_time'] ) ) : 0;
	$ttype = isset($_POST['ttype']) ? sanitize_text_field( wp_unslash( $_POST['ttype'] ) ) : '';
	$parent = isset($_POST['parent']) ? sanitize_text_field( wp_unslash( $_POST['parent'] ) ) : '';
	$milestone_dates = array();
	if ( ! empty($task_deadline) ) {
		$task_deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $task_deadline)->getTimestamp();
	}
	$start = isset($_POST['start']) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : '';
	if ( ! empty($start) ) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	if ( empty($task_title) || empty($start) || empty($task_deadline) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Tasks require a title, start date and deadline', 'projectopia-core') . '</div>',
		) );
	}
	$description = isset($_POST['description']) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
	$owner = isset($_POST['owner']) ? sanitize_text_field( wp_unslash( $_POST['owner'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$ppid = isset($_POST['ppid']) ? sanitize_text_field( wp_unslash( $_POST['ppid'] ) ) : '';
	if ( empty($owner) && $type == 'project' ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Tasks require an assignee', 'projectopia-core') . '</div>',
		) );
	}
	if ( $ttype == 'parent' ) {
		$task_weight = pto_calculate_task_weight($task_milestone_id);
	} else {
		$task_weight = pto_calculate_subtask_weight($parent);
	}
	if ( $ttype == 'parent' ) {
		$new_task = array(
			'post_type'     => 'cqpim_tasks',
			'post_status'   => 'publish',
			'post_content'  => '',
			'post_title'    => $task_title,
			'post_password' => pto_random_string(10),
		);
	} else {
		$new_task = array(
			'post_type'     => 'cqpim_tasks',
			'post_status'   => 'publish',
			'post_content'  => '',
			'post_title'    => $task_title,
			'post_parent'   => $parent,
			'post_password' => pto_random_string(10),
		);
	}
	$task_pid = wp_insert_post( $new_task, true );
	if ( ! is_wp_error( $task_pid ) ) {
		$task_updated = array(
			'ID'        => $task_pid,
			'post_name' => $task_pid,
		);                      
		wp_update_post( $task_updated );
		update_post_meta($task_pid, 'project_id', $task_project_id);
		if ( ! empty($task_project_id) ) {
			update_post_meta($task_pid, 'active', true);
			update_post_meta($task_pid, 'published', true);
		}
		update_post_meta($task_pid, 'milestone_id', $task_milestone_id);
		if ( ! empty($owner) ) {
			update_post_meta($task_pid, 'owner', $owner);
		} else {
			$assigned = '';
			$owner = wp_get_current_user();
			$args = array(
				'post_type'      => 'cqpim_teams',
				'posts_per_page' => -1,
				'post_status'    => 'private',
			);
			$members = get_posts($args);
			foreach ( $members as $member ) {
				$team_details = get_post_meta($member->ID, 'team_details', true);
				if ( $team_details['user_id'] == $owner->ID ) {
					$assigned = $member->ID;
				}
			}
			update_post_meta($task_pid, 'owner', $assigned);
		}
		$task_details = array(
			'weight'           => $task_weight,
			'deadline'         => $task_deadline,
			'status'           => 'pending',
			'task_start'       => $start,
			'task_description' => $description,
			'task_pc'          => 0,
			'task_priority'    => 'normal',
			'task_est_time'    => $task_time,
		);
		update_post_meta($task_pid, 'task_details', $task_details);
		if ( $type == 'project' ) {
			$current_user = wp_get_current_user();
			$current_user = $current_user->display_name;
			$project_progress = get_post_meta($ppid, 'project_progress', true);
			$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

			$project_progress[] = array(
				'update' => __('Task Created', 'projectopia-core') . ': ' . $task_title,
				'date'   => time(),
				'by'     => $current_user,
			);
			update_post_meta($ppid, 'project_progress', $project_progress );
		}
		if ( $type == 'quote' ) {
			$post_link = get_edit_post_link($task_pid);
			$project_id_obj = get_post($task_project_id);
			if ( $project_id_obj->post_type == 'cqpim_support' ) {
				$title = '<a href="' . $post_link . '"><span class="ms-title" id="task_title_' . $task_pid . '">' . $task_title . '</span></a>';
			} else { 
				$title = '<span class="ms-title" id="task_title_' . $task_pid . '">' . $task_title . '</span>';
			}           
			if ( $ttype == 'parent' ) {
				$markup = '<div class="dd-task" id="task-' . $task_pid . '">';
			} else {
				$markup = '<div class="dd-subtask" id="task-' . $task_pid . '">';
			}
				$markup .= '<input class="task_weight" type="hidden" name="task_weight_' . $task_pid . '" id="task_weight_' . $task_pid . '" data-id="' . $task_pid . '" value="' . $task_weight . '" />';
					if ( $ttype == 'parent' ) {
						$markup .= '<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2">' . esc_html__('Task', 'projectopia-core') .'</span> ' . $title;
					} else {
						$markup .= '<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2">' . esc_html__('Subtask', 'projectopia-core') .'</span> ' . $title;                    
					}
					$markup .= '<div class="dd-task-actions">';
						$markup .= '<button class="edit-task cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" value="' . $task_pid . '" title="' . esc_attr__('Edit Task', 'projectopia-core') . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
						if ( $ttype == 'parent' ) {
							$markup .= '<button class="delete_task_trigger cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="' . $task_pid . '" value="' . $task_pid . '" title="' . esc_attr__('Delete Task', 'projectopia-core') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
						} else {
							$markup .= '<button class="delete_subtask_trigger cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="' . $task_pid . '" value="' . $task_pid . '" title="' . esc_attr__('Delete Task', 'projectopia-core') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
						}
						if ( $ttype == 'parent' ) {
							$markup .= '<button class="add_subtask cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="' . $task_milestone_id . '" data-project="' . $task_project_id . '" value="' . $task_pid . '" title="' . esc_attr__('Add Subtask', 'projectopia-core') . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						}
						$markup .= '<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="' . $task_milestone_id . '" title="' . esc_attr__('Reorder Task', 'projectopia-core') . '"><i class="fa fa-sort" aria-hidden="true"></i></button>';
					$markup .= '</div>';
				$markup .= '<div class="dd-task-info">';
					$markup .= '<strong>' . esc_html__('Start Date:', 'projectopia-core') . '</strong> <span id="task_start_' . $task_pid . '">' . wp_date(get_option('cqpim_date_format'), $start) . '</span>';
					$markup .= '<i class="fa fa-circle dd-circle"></i> <strong>' . esc_html__('Deadline:', 'projectopia-core') . '</strong> <span id="task_deadline_' . $task_pid . '">' . wp_date(get_option('cqpim_date_format'), $task_deadline) . '</span>';
					$markup .= '<i class="fa fa-circle dd-circle"></i> <strong>' . esc_html__('Est. Time:', 'projectopia-core') . '</strong> <span id="task_time_' . $task_pid . '">' . $task_time . '</span>';                
				$markup .= '</div>';
				$markup .= '<div class="clear"></div>';
				if ( $ttype == 'parent' ) {
					$markup .= '<div class="dd-subtasks">';
					$markup .= '</div>';
				}
			$markup .= '</div>';
			if ( $ttype == 'parent' ) {

				ob_start();
				require PTO_PATH . '/includes/meta/task/load-task-quote-ajax.php';
				$markup = ob_get_contents();
				ob_end_clean();	
			}
			else {
				ob_start();
				require PTO_PATH . '/includes/meta/task/load-task-quote-ajax.php';
				$markup = ob_get_contents();
				ob_end_clean();		
			}
		}
		if ( $type == 'project' ) {
			$task_details = get_post_meta($task_pid, 'task_details', true);
			$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
			$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
			$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
			$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
			$task_owner = get_post_meta($task_pid, 'owner', true);
			$task_owner_id = get_post_meta($task_pid, 'owner', true);
			$client_check = preg_replace('/[0-9]+/', '', $task_owner);
			$project_details = get_post_meta($task_project_id, 'project_details', true);
			$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : ''; 
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			unset($client);
			if ( $client_check == 'C' ) {
				$client = true;
			}
			if ( $task_owner ) {
				if ( ! empty($client) && $client == true ) {
					$id = preg_replace("/[^0-9,.]/", "", $task_owner);
					$client_object = get_user_by('id', $id);
					$task_owner = $client_object->display_name;
				} else {
					$team_details = get_post_meta($task_owner, 'team_details', true);
					$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
					if ( ! empty($team_name) ) {
						$task_owner = $team_name;
					}
				}
			} else {
				$task_owner = '';
			}
			$task_status = 'pending';
			if ( ! is_numeric($task_deadline) ) {
				$str_deadline = str_replace('/','-', $task_deadline);
				$deadline_stamp = strtotime($str_deadline);
			} else {
				$deadline_stamp = $task_deadline;
			}
			/*$now = time();
			if ( $task_status != 'complete' ) {
				if ( ! empty($deadline_stamp) && $now > $deadline_stamp ) {
					$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op nolink rounded_2">' . esc_html__('OVERDUE', 'projectopia-core') . '</span>';
				} else {
					$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
					if ( ! $task_status_string || $task_status_string == 'pending' ) {
						$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-amber rounded_2 op nolink rounded_2">' . esc_html__('Pending', 'projectopia-core') . '</span>';
					} elseif ( $task_status_string == 'on_hold' ) {
						$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op nolink rounded_2">' . esc_html__('On Hold', 'projectopia-core') . '</span>';
					} elseif ( $task_status_string == 'progress' ) {
						$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-amber rounded_2 op nolink rounded_2">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
					}
				}
			} else {
				$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-green rounded_2 op nolink rounded_2">' . esc_html__('Complete', 'projectopia-core') . '</span>';
			}*/
			$task_status_string = pto_get_task_status_html( $task_status, $deadline_stamp );
			/**
			 * Filter Task Status Display in Project page
			 */
			// $task_status_string = apply_filters('pto_project_task_status_string', $task_status_string, $task_details['status'], $task_details);
			// if ( $ttype == 'parent' ) {
			// 	if ( pto_is_task_overdue($task_pid) == 1 ) {
			// 		$markup = '<div class="dd-task overdue" id="task-' . $task_pid . '">';
			// 	} else {
			// 		$markup = '<div class="dd-task" id="task-' . $task_pid . '">';
			// 	}
			// } else {
			// 	if ( pto_is_task_overdue($task_pid) == 1 ) {
			// 		$markup = '<div class="dd-subtask overdue" id="task-' . $task_pid . '">';
			// 	} else {
			// 		$markup = '<div class="dd-subtask" id="task-' . $task_pid . '">';
			// 	}
			// }
			// 	$markup .= '<input class="task_weight" type="hidden" name="task_weight_' . $task_pid . '" id="task_weight_' . $task_pid . '" data-id="' . $task_pid . '" value="' . $task_weight . '" />';
			// 		if ( $ttype == 'parent' ) {
			// 			$markup .= '<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2">' . esc_html__('Task', 'projectopia-core') .'</span> <span class="ms-title" id="task_title_' . $task_pid . '">' . $task_title . '</span>';
			// 		} else {
			// 			$post_link = get_edit_post_link($task_pid);
			// 			$markup .= '<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2">' . esc_html__('Subtask', 'projectopia-core') .'</span> <a href="' . $post_link . '"><span class="ms-title" id="task_title_' . $task_pid . '">' . $task_title . '</span></a>';                 
			// 		}
			// 		$markup .= '<div class="dd-task-actions">';
			// 			$markup .= '<button class="edit-task cqpim_button cqpim_small_button font-white bg-amber op rounded_2 cqpim_tooltip" value="' . $task_pid . '" title="' . esc_attr__('Edit Task', 'projectopia-core') . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
			// 			if ( $ttype == 'parent' ) {
			// 				$markup .= '<button class="delete_task_trigger cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="' . $task_pid . '" value="' . $task_pid . '" title="' . esc_attr__('Delete Task', 'projectopia-core') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
			// 			} else {
			// 				$markup .= '<button class="delete_subtask_trigger cqpim_button cqpim_small_button font-white bg-red op rounded_2 cqpim_tooltip" data-id="' . $task_pid . '" value="' . $task_pid . '" title="' . esc_attr__('Delete Task', 'projectopia-core') . '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
			// 			}
			// 			$markup .= '<button class="item_complete cqpim_button cqpim_small_button font-white bg-grey-cascade op rounded_2 cqpim_tooltip" data-type="task" value="' . $task_pid . '" title="' . esc_attr__('Mark Task Complete', 'projectopia-core') . '"><i class="fa fa-check" aria-hidden="true"></i></button>';
			// 			if ( $ttype == 'parent' ) {
			// 				$markup .= '<button class="add_subtask cqpim_button cqpim_small_button font-white bg-green op rounded_2 cqpim_tooltip" data-ms="' . $task_milestone_id . '" data-project="' . $task_project_id . '" value="' . $task_pid . '" title="' . esc_attr__('Add Subtask', 'projectopia-core') . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
			// 			}
			// 			$markup .= '<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue op rounded_2 cqpim_tooltip" value="' . $task_milestone_id . '" title="' . esc_attr__('Reorder Task', 'projectopia-core') . '"><i class="fa fa-sort" aria-hidden="true"></i></button>';
			// 		$markup .= '</div>';
			// 	$markup .= '<div class="dd-task-status">' . $task_status_string . '</div>';         
			// 	$markup .= '<div class="dd-task-info">';
			// 		$markup .= '<strong>' . esc_html__('Start Date:', 'projectopia-core') . '</strong> <input type="text" class="datepicker task_input start_editable" id="start_' . $task_pid . '" data-id="' . $task_pid . '" value="' . wp_date(get_option('cqpim_date_format'), $start) . '" />';              
			// 		$markup .= '<strong style="padding-left:10px"> ' . __('Deadline:', 'projectopia-core') . '</strong> <input type="text" class="datepicker task_input end_editable" id="end_' . $task_pid . '" data-id="' . $task_pid . '" value="' . wp_date(get_option('cqpim_date_format'), $task_deadline) . '" />';             
			// 		$markup .= '<strong style="padding-left:10px"> ' . __('Assigned To:', 'projectopia-core') . '</strong> <select class="admin_task_assignee task_input_select assignee_editable" data-id="' . $task_pid . '">';
			// 			$markup .= '<option value="">' . esc_html__('Choose...', 'projectopia-core') . '</option>';
			// 			$assignees = pto_get_available_assignees($task_pid); 
			// 			if ($client_id != '' && $client_contact_name != '')
			// 				$markup .= '<option value="' . $client_id . '" ' . selected($task_owner_id, $client_id, false) . '>' . esc_html( $client_contact_name ) . '</option>';
			// 			if ( ! empty($assignees) ) {
			// 				foreach ( $assignees as $available ) { 
			// 					$av_team_details = get_post_meta($available, 'team_details', true); 
			// 					$av_team_name = isset($av_team_details['team_name']) ? $av_team_details['team_name'] : '';
			// 					$markup .= '<option value="' . $available . '" ' . selected($task_owner_id, $available, false) . '>' . $av_team_name . '</option>';
			// 				}
			// 			} else {
			// 				$markup .= '<option value="' . $owner . '" ' . selected($task_owner_id, $owner, false) . '>' . $team_name . '</option>';
			// 			}
			// 		$markup .= '</select>';                 
			// 		if ( ! empty($task_time) ) {
			// 			$markup .= '<strong style="padding-left:10px"> ' . __('Est. Time:', 'projectopia-core') . '</strong> ' . $task_time;
			// 		}
			// 	$markup .= '</div>';
			// 	$markup .= '<div class="clear"></div>';
			// 	if ( $ttype == 'parent' ) {
			// 		$markup .= '<div class="dd-subtasks">';
			// 		$markup .= '</div>';
			// 	}
			// $markup .= '</div>';
			$milestone_dates = pto_calculate_milestone_dates($ppid, $task_milestone_id);
			if ( $ttype == 'parent' ) {

				ob_start();
				require PTO_PATH . '/includes/meta/task/load-task-project-ajax.php';
				$markup = ob_get_contents();
				ob_end_clean();	
			}
			else {
				ob_start();
				require PTO_PATH . '/includes/meta/task/load-task-project-ajax.php';
				$markup = ob_get_contents();
				ob_end_clean();		
			}
			
		}
		if ( ! empty($task_project_id) ) {
			pto_send_task_updates($task_pid, $task_project_id, $owner, array(), '');
		}
		$milestone_status = array();
		if ( $type == 'project' ) {
			$project_elements = get_post_meta($task_project_id, 'project_elements', true);
			$project_elements[ $task_milestone_id ]['status'] = 'pending';
			update_post_meta($task_project_id, 'project_elements', $project_elements);
			$milestone_status = array(
				'milestone_id' => $task_milestone_id,
				'status'       => '<span class="cqpim_button cqpim_small_button nolink op bg-amber font-white rounded_2">' . esc_html__('Pending', 'projectopia-core') . '</span>',
			);
		}
        // Check if task deadline is greater than start date
        if ( $task_deadline < $start ) {
            pto_send_json( array(
                'error'  => true,
                'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The Task deadline should be greater than the start date, please try again.', 'projectopia-core') . '</div>',
            ) );
        } else {
            pto_send_json( array(
                'error'            => false,
                'message'          => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The Task was successfully created.', 'projectopia-core') . '</div>',
                'markup'           => $markup,
                'milestone_dates'  => isset( $milestone_dates ) ? $milestone_dates : '',
                'milestone_status' => $milestone_status,
            ) );
        }
	} else {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The Task could not be created at this time, please try again.', 'projectopia-core') . '</div>',
		) );              
	}
	exit();
}
/* Retrieve Milestone Data for Editing */
add_action( "wp_ajax_pto_retrieve_task_data", "pto_retrieve_task_data" );
function pto_retrieve_task_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	if ( empty($task_id) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Either the Task ID or the Item ID are missing.', 'projectopia-core') . '</div>',
		) );
	}
	$task_obj = get_post($task_id);
	$task_details = get_post_meta($task_id, 'task_details', true);
	$project_id = get_post_meta($task_id, 'project_id', true);
	$post_type = get_post_type( $project_id );
	if ( $post_type == 'cqpim_quote' ) {
		$project_elements = get_post_meta( $project_id, 'quote_elements', true );
	} else {
		$project_elements = get_post_meta( $project_id, 'project_elements', true );
	}
	$milestone_id = get_post_meta( $task_id, 'milestone_id', true );

	if ( $milestone_id && isset( $project_elements[ $milestone_id ] ) ) {
		$ms_start = isset( $project_elements[ $milestone_id ]['start'] ) ? wp_date(get_option('cqpim_date_format'), $project_elements[ $milestone_id ]['start']) : '';
		$ms_deadline = isset( $project_elements[ $milestone_id ]['deadline'] ) ? wp_date(get_option('cqpim_date_format'), $project_elements[ $milestone_id ]['deadline']) : '';
	}
	$data_to_return = array(
		'id'       => $task_id,
		'title'    => $task_obj->post_title,
		'start'    => isset($task_details['task_start']) && ! empty($task_details['task_start']) ? wp_date(get_option('cqpim_date_format'), $task_details['task_start']) : $ms_start,
		'deadline' => isset($task_details['deadline']) && ! empty($task_details['deadline']) ? wp_date(get_option('cqpim_date_format'), $task_details['deadline']) : $ms_deadline,
		'desc'     => isset($task_details['task_description']) ? $task_details['task_description'] : '',
		'time'     => isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '',
	);
	pto_send_json( array( 
		'error' => false,
		'data'  => $data_to_return,
	) );
}
/* Update Task Data After Editing */
add_action( "wp_ajax_pto_update_task_data", "pto_update_task_data" );
function pto_update_task_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$title = isset($_POST['title']) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$desc = isset($_POST['desc']) ? wp_kses_post( wp_unslash( $_POST['desc'] ) ) : '';
	$time = isset($_POST['time']) ? sanitize_text_field( wp_unslash( $_POST['time'] ) ) : '';
	$start = isset($_POST['start']) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : '';
	if ( ! empty($start) ) {
		$start = DateTime::createFromFormat(get_option('cqpim_date_format'), $start)->getTimestamp();
	}
	$deadline = isset($_POST['deadline']) ? sanitize_text_field( wp_unslash( $_POST['deadline'] ) ) : '';
	if ( ! empty($deadline) ) {
		$deadline = DateTime::createFromFormat(get_option('cqpim_date_format'), $deadline)->getTimestamp();
	}
	if ( empty($task_id) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Either the Task ID or the Item ID are missing.', 'projectopia-core') . '</div>',
		) );
	}
	if ( empty($title) || empty($start) || empty($deadline) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must enter a title, start date and deadline for this task', 'projectopia-core') . '</div>',
		) );
	}   
	$task_details = get_post_meta($task_id, 'task_details', true);
	$task_details['deadline'] = $deadline;
	$task_details['task_start'] = $start;
	$task_details['task_description'] = $desc;
	$task_details['task_est_time'] = $time;
	update_post_meta($task_id, 'task_details', $task_details);
	$task_updated = array(
		'ID'         => $task_id,
		'post_title' => $title,
	);
	wp_update_post($task_updated);
	$task_details = get_post_meta($task_id, 'task_details', true);
	$data_to_return = array(
		'id'       => $task_id,
		'title'    => $title,
		'start'    => isset($task_details['task_start']) ? wp_date(get_option('cqpim_date_format'), $task_details['task_start']) : '',
		'deadline' => isset($task_details['deadline']) ? wp_date(get_option('cqpim_date_format'), $task_details['deadline']) : '',
		'desc'     => isset($task_details['task_description']) ? $task_details['task_description'] : '',
		'time'     => isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '',
	);
	$milestone_id = get_post_meta($task_id, 'milestone_id', true);
	$project_id = get_post_meta($task_id, 'project_id', true);
	$milestone_dates = pto_calculate_milestone_dates($project_id, $milestone_id);
	pto_send_json( array( 
		'error'           => false,
		'data'            => $data_to_return,
		'message'         => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The task was successfully updated', 'projectopia-core') . '</div>',
		'milestone_dates' => $milestone_dates,
	) );
}
/* Delete the Task and attached subtasks */
add_action( "wp_ajax_pto_delete_task_data", "pto_delete_task_data" );
function pto_delete_task_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	if ( empty($task_id) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Either the Task ID or the Item ID are missing.', 'projectopia-core') . '</div>',
		) );
	}   
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'post_parent'    => $task_id,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$subtasks = get_posts($args);   
	foreach ( $subtasks as $subtask ) {        
		wp_delete_post($subtask->id);
	}   
	$milestone_id = get_post_meta($task_id, 'milestone_id', true);
	$project_id = get_post_meta($task_id, 'project_id', true);
	wp_delete_post($task_id);
	$milestone_dates = pto_calculate_milestone_dates($project_id, $milestone_id);
	pto_send_json( array( 
		'error'           => false,
		'container'       => '#task-' . $task_id,
		'message'         => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The task was deleted successfully', 'projectopia-core') . '</div>',
		'milestone_dates' => $milestone_dates,
	) );
}
/* Reorder Task Data */
add_action( "wp_ajax_pto_reorder_task_data", "pto_reorder_task_data" );
function pto_reorder_task_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$weights = isset($_POST['weights']) ? pto_sanitize_rec_array( wp_unslash( $_POST['weights'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	foreach ( $weights as $weight ) {
		if ( isset( $weight['task_id'] ) ) {
	    	$task_id = $weight['task_id'];
	    	$weight = $weight['weight'] + 1;
	    	$task_details = get_post_meta($task_id, 'task_details', true);
	    	if ( is_array( $task_details ) ) {
	    	    $task_details['weight'] = $weight;
	    	    update_post_meta($task_id, 'task_details', $task_details);
	    	}
	    }
	}
	pto_send_json( array( 
		'error' => false,
	) );
}
add_action( "wp_ajax_nopriv_pto_add_timer_time", "pto_add_timer_time" );
add_action( "wp_ajax_pto_add_timer_time", "pto_add_timer_time" );
function pto_add_timer_time() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$time = isset($_POST['time']) ? sanitize_text_field( wp_unslash( $_POST['time'] ) ) : '';
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$timer_note = isset($_POST['timer_note']) ? sanitize_textarea_field( wp_unslash( $_POST['timer_note'] ) ) : '';
	if ( empty($task_id) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('The Task ID is missing, make sure you have selected a from the list.', 'projectopia-core'),
		) );         
	} else {
		if ( empty($time) ) {
			pto_send_json( array( 
				'error'   => true,
				'message' => __('There is no time to add.', 'projectopia-core'),
			) );             
		} else {
			$time    = explode(':', $time);
			$hours   = 0;
			$minutes = 0;

			//Timer hours value checking.
			if ( ! empty( $time[0] ) ) {
				$hours = $time[0];
			}

			//Timer minutes value checking.
			if ( ! empty( $time[1] ) ) {
				$minutes = $time[1];
			}

			$min_to_hours = 0;
			if ( $minutes > 0 ) {
				$min_to_hours = $minutes / 60;
			}

			$time = $hours + $min_to_hours;
			$time_spent = get_post_meta($task_id, 'task_time_spent', true);
			$user = wp_get_current_user();
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
				}
			}

			$time_spent = $time_spent && is_array($time_spent) ? $time_spent : array();
			$time       = round( $time, 2 );

			if ( $time <= 0 ) {
				pto_send_json( array(
					'error'   => true,
					'message' => __('Please add atleast 1 min.', 'projectopia-core'),
				) ); 
			}

			$time_spent[] = array(
				'team'       => $user->display_name,
				'team_id'    => $assigned,
				'time'       => $time,
				'stamp'      => time(),
				'timer_note' => $timer_note,
			);
			update_post_meta($task_id, 'task_time_spent', $time_spent);
			pto_send_json( array( 
				'error'   => false,
				'message' => __('Time added successfully.', 'projectopia-core'),
			) ); 
		}
	}
}
add_action( "wp_ajax_nopriv_pto_populate_project_milestone", "pto_populate_project_milestone" );
add_action( "wp_ajax_pto_populate_project_milestone", "pto_populate_project_milestone" );
function pto_populate_project_milestone() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
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
		}
	}
	$project_id = isset($_POST['ID']) ? sanitize_text_field( wp_unslash( $_POST['ID'] ) ) : '';
	$milestones = get_post_meta($project_id, 'project_elements', true);
	$milestones_to_display = '';
	if ( empty($milestones) ) {
		$milestones = array();
	}
	foreach ( $milestones as $milestone ) {
		$milestones_to_display .= '<option value="' . $milestone['id'] . '">' . $milestone['title'] . '</option>';
	}
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	$project_contributors_to_display = '';
	if ( empty($project_contributors) ) {
		$project_contributors = array();
	}
	foreach ( $project_contributors as $contributor ) {
		$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
		$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
		$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
		$project_contributors_to_display .= '<option value="' . $contributor . '">' . $team_name . ' - ' . $team_job . '</option>';
	}
	if ( ! $milestones_to_display && ! $project_contributors_to_display ) {
		pto_send_json( array( 
			'error'        => true,
			'options'      => '<option value="">' . esc_html__('No milestones available', 'projectopia-core') . '</option>',
			'team_options' => '<option value="' . esc_attr( $assigned ) . '">' . esc_html__('Me', 'projectopia-core') . '</option>',
		) );
	} elseif ( ! $milestones_to_display && $project_contributors_to_display ) {
		pto_send_json( array( 
			'error'        => true,
			'options'      => '<option value="">' . esc_html__('No milestones available', 'projectopia-core') . '</option>',
			'team_options' => '<option value="">' . esc_html__('Choose a team member', 'projectopia-core') . '</option>' . $project_contributors_to_display,
		) );
	} elseif ( ! $project_contributors_to_display && $milestones_to_display ) {
		pto_send_json( array( 
			'error'        => true,
			'options'      => $milestones_to_display,
			'team_options' => '<option value="">' . esc_html__('No team members available', 'projectopia-core') . '</option>',
		) );
	} elseif ( $project_contributors_to_display && $milestones_to_display ) {
		pto_send_json( array( 
			'error'        => true,
			'options'      => $milestones_to_display,
			'team_options' => '<option value="' . esc_attr( $assigned ) . '">' . esc_html__('Choose a team member', 'projectopia-core') . '</option>' . $project_contributors_to_display,
		) );
	}
	exit();
}
add_action( "wp_ajax_nopriv_pto_delete_task_page", "pto_delete_task_page" );
add_action( "wp_ajax_pto_delete_task_page", "pto_delete_task_page" );
function pto_delete_task_page() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$ppid = get_post_meta($task_id, 'project_id', true);
	if ( ! empty($ppid) ) {
		$current_user = wp_get_current_user();
		$project_progress = get_post_meta($ppid, 'project_progress', true);
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		$task_object = get_post($task_id);
		$task_title = $task_object->post_title;
		$project_progress[] = array(
			'update' => __('Task Deleted', 'projectopia-core') . ': ' . $task_title,
			'date'   => time(),
			'by'     => $current_user->display_name,
		);
		update_post_meta($ppid, 'project_progress', $project_progress );        
	}
	wp_delete_post($task_id, true);
	pto_send_json( array( 
		'error'    => false,
		'redirect' => admin_url() . 'admin.php?page=pto-tasks',
	) );
}

/**
 * Function to delete the task of given id.
 * 
 * @since 5.0.0
 * 
 * @param int $task_id
 * 
 * @return boolean 
 */
function pto_delete_task( $task_id ) {

	//Check task id is empty or not.
	if ( empty( $task_id ) ) {
		return false;
	}

	$task_post_id = get_post_meta( $task_id, 'project_id', true );

	//Check if given task is from project.
	if ( ! empty( $task_post_id ) ) {

		$task_object  = get_post( $task_id );
		$task_title   = $task_object->post_title;
		$current_user = wp_get_current_user();
		$project_progress = get_post_meta( $task_post_id, 'project_progress', true );
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		//Update the project status.
		$project_progress[] = [
			'update' => __( 'Task Deleted', 'projectopia-core' ) . ': ' . $task_title,
			'date'   => time(),
			'by'     => $current_user->display_name,
		];

		update_post_meta( $task_post_id, 'project_progress', $project_progress );       
	}

	wp_delete_post( $task_id, true );

	/**
	 * Fires after delete the task.
	 * 
	 * @since 5.0.0
	 * 
	 * @param int $task_id tasks.
	 */
	do_action( 'pto_delete_task', $task_id );

	return true;
}

add_action( 'wp_ajax_pto_delete_selected_tasks', 'pto_delete_selected_tasks' );
/**
 * Function to call the ajax operation for task deletion.
 * This function deletes the all tasks passed in task_ids.
 *
 * @return json
 */
function pto_delete_selected_tasks() {

	//Verify the nonce.
	check_ajax_referer( 'pto_nonce', 'ajax_nonce' );

	$task_ids = isset( $_POST['task_ids'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['task_ids'] ) ) : '';

	if ( empty( $task_ids ) ) {
		wp_send_json_error( $task_ids );
	}

	//Delete all the task.
	foreach ( $task_ids as $task_id ) {
		pto_delete_task( $task_id );
	}

	/**
	 * Fires after delete the all select tasks.
	 * 
	 * @since 5.0.0
	 * 
	 * @param array $task_ids List of checked task_ids.
	 */
	do_action( 'pto_delete_selected_tasks', $task_ids );

	wp_send_json_success(
		[ 'message' => __( 'Tasks are deleted', 'projectopia-core' ) ]
	);
}

add_action( "wp_ajax_nopriv_pto_client_update_task", "pto_client_update_task" );
add_action( "wp_ajax_pto_client_update_task", "pto_client_update_task" );
function pto_client_update_task() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset( $_POST['file_task_id'] ) ? sanitize_text_field( wp_unslash( $_POST['file_task_id'] ) ) : '';
	$message = isset( $_POST['add_task_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['add_task_message'] ) ) : '';
	if ( empty($message) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('You must enter a message.', 'projectopia-core'),
		) );                 
	} else {
		$custom_fields = get_option('cqpim_custom_fields_task');    
		$custom_fields = json_decode($custom_fields);
		$custom = isset($_POST['custom']) ? pto_sanitize_rec_array( wp_unslash( $_POST['custom'] ) ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( $custom_fields as $custom_field ) {
			if ( empty($custom[ $custom_field->name ]) && ! empty($custom_field->required) ) {
				pto_send_json( array( 
					'error'   => true,
					'title'   => __('Required Fields Missing', 'projectopia-core'),
					'message' => __('Please complete all required fields.', 'projectopia-core'),
				) );                     
			}
		}
		update_post_meta($task_id, 'custom_fields', $custom);
		$message = make_clickable($message);
		$owner = isset($_POST['task_owner']) ? sanitize_text_field( wp_unslash( $_POST['task_owner'] ) ) : '';
		$task_owner = get_post_meta($task_id, 'owner', true);
		update_post_meta($task_id, 'owner', $owner);
		$project_id = get_post_meta($task_id, 'project_id', true);
		$task_owner = get_post_meta($task_id, 'owner', true);
		$task_watchers = get_post_meta($task_id, 'task_watchers', true);
		$task_link = get_the_permalink($task_id);
		$task_object = get_post($task_id);
		$task_link = '<a class="cqpim-link" href="' . $task_link . '">' . $task_object->post_title . '</a>';
		$attachments = isset($_POST['files']) ? sanitize_text_field( wp_unslash( $_POST['files'] ) ) : array();
		$ticket_changes = array();
		if ( ! empty($attachments) ) {
			$attachments = explode(',', $attachments);
			$attachments_to_send = array();
			foreach ( $attachments as $attachment ) {
				global $wpdb;
				$wpdb->update( $wpdb->posts, [ 'post_parent' => $task_id ], [ 'ID' => $attachment ] );
				update_post_meta($attachment, 'cqpim', true);
				$filename = basename( get_attached_file( $attachment ) );
				$attachments_to_send[] = get_attached_file( $attachment );
				/* translators: %s: Uploaded File Name */
				$ticket_changes[] = sprintf(esc_html__('Uploaded file: %s', 'projectopia-core'), $filename);
			}
		}
		$task_messages = get_post_meta($task_id, 'task_messages', true);
		$task_messages = $task_messages && is_array($task_messages) ? $task_messages : array();
		$date = time();
		$current_user = wp_get_current_user();
		if ( empty($message) ) {
			$message = '';
		}
		$task_messages[] = array(
			'date'    => $date,
			'message' => $message,
			'by'      => $current_user->display_name,
			'author'  => $current_user->ID,
			'changes' => $ticket_changes,
		);      
		update_post_meta($task_id, 'task_messages', $task_messages);
		$project_progress = get_post_meta($project_id, 'project_progress', true);
		$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

		$project_progress[] = array(
			/* translators: %s: Task Name */
			'update' => sprintf(esc_html__('Message sent in task: %s', 'projectopia-core'), $task_object->post_title),
			'date'   => time(),
			'by'     => $current_user->display_name,
		);
		if ( ! empty($attachments) ) {
			foreach ( $attachments as $attachment ) {
				$post = get_post($attachment);
				$project_progress[] = array(
					/* translators: %1$s: File Name, %2$s: Task Name */
					'update' => sprintf(esc_html__('File "%1$s" uploaded to task: %2$s', 'projectopia-core'), $post->post_title, $task_object->post_title),
					'date'   => time(),
					'by'     => $current_user->display_name,
				);
			}
		}
		update_post_meta($project_id, 'project_progress', $project_progress );
	}
	update_post_meta($task_id, 'client_updated', true);
	update_post_meta($task_id, 'team_updated', false);
	pto_send_task_updates($task_id, $project_id, $task_owner, $task_watchers, $message, '', $attachments_to_send);
	pto_send_json( array( 
		'error' => false,
	) );     
}
function pto_send_task_updates( $post_id, $project_id = NULL, $task_owner = NULL, $task_watchers = array(), $message = NULL, $user = null, $attachments = array(), $new_owner = false ) {
	$user = wp_get_current_user();
	update_post_meta($post_id, 'last_updated', $user->ID);
	$emails_to_send = array();
	$project_details = get_post_meta($project_id, 'project_details', true);
	if ( is_object($task_owner) ) {
		$task_owner = isset($task_owner->ID) ? $task_owner->ID : '';
	} else {
		$task_owner = isset($task_owner) ? $task_owner : '';
	}
	$client_check = preg_replace('/[0-9]+/', '', $task_owner);
	$client = false;
	if ( $client_check == 'C' ) {
		$client = true;
	}
	if ( $task_owner ) {
		if ( $client == true ) {
			$id = preg_replace("/[^0-9,.]/", "", $task_owner);
			$client = get_user_by('id', $id);
			$client_email = $client->user_email;
		} else {
			$emails_to_send[] = $task_owner;
		}
	} else {
		$task_owner = '';
	}           
	if ( empty($client_email) ) {
		$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
		$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
		$client = get_user_by('id', $client_contact);
		$args = array(
			'post_type'      => 'cqpim_client',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$members = get_posts($args);
		foreach ( $members as $member ) {
			$team_details = get_post_meta( $member->ID, 'client_details', true );
			if ( ! empty( $team_details ) && is_array( $team_details ) && isset( $team_details['user_id'] ) ) {
				if ( isset( $client->ID ) && $team_details['user_id'] == $client->ID ) {
					$notifications = get_post_meta($member->ID, 'client_notifications', true);
				}
			}
		}
		if ( empty($assigned) ) {
			foreach ( $members as $member ) {
				$client_contacts = get_post_meta($member->ID, 'client_contacts', true);
				if ( empty( $client_contacts ) || ! is_array( $client_contacts ) ) {
					$client_contacts = array();
				}
				foreach ( $client_contacts as $contact ) {
					if ( isset( $client->ID ) && $contact['user_id'] == $client->ID ) {
						$notifications = isset($contact['notifications']) ? $contact['notifications'] : array();
					}
				}
			}           
		}
		$no_tasks = isset($notifications['no_tasks']) ? $notifications['no_tasks'] : 0;
		$no_tasks_comment = isset($notifications['no_tasks_comment']) ? $notifications['no_tasks_comment'] : 0;          
		$client_email = isset( $client->user_email ) ? $client->user_email : '';
		if ( ! empty($no_tasks) ) {
			$client_email = '';
		}
		if ( ! empty($no_tasks_comment) && empty($message) ) {
			$client_email = '';
		}
	}
	if ( empty($user) ) {
		$user = wp_get_current_user();
	}
	if ( ! empty($client_email) ) {
		$subject = get_option('team_assignment_subject');
		$content = get_option('team_assignment_email');
		$url = get_the_permalink($post_id);
		$subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $post_id . ']', $subject);
		$content = str_replace('%%TASK_UPDATE%%', $message, $content);
		$content = str_replace('%%CURRENT_USER%%', $user->display_name, $content);
		$content = str_replace('%%NAME%%', $client->display_name, $content);
		$content = str_replace('%%TASK_URL%%', $url, $content);
		$content = str_replace('%%ACCEPT_TASK_LINK%%', '', $content);
		$subject = pto_replacement_patterns($subject, $post_id, 'task');
		$content = pto_replacement_patterns($content, $post_id, 'task');
		if ( $user->user_email != $client_email ) {
			pto_add_team_notification($client_id, $user->ID, $post_id, 'task');
			pto_send_emails($client_email, $subject, $content, '', $attachments, 'sales');
		}
	}
	$project_contributors = get_post_meta($project_id, 'project_contributors', true);
	if ( empty($project_contributors) ) {
		$project_contributors = array();
	}
	foreach ( $project_contributors as $contrib ) {
		if ( ! empty( $contrib['pm'] ) && $contrib['pm'] == 1 ) {
			$emails_to_send[] = $contrib['team_id'];
		}
	}
	if ( empty($task_watchers) ) {
		$task_watchers = array();
	} else {
		$task_watchers = $task_watchers;
	}
	foreach ( $task_watchers as $watcher ) {
		$emails_to_send[] = $watcher;
	}
	$emails_to_send = array_unique($emails_to_send);
	foreach ( $emails_to_send as $email ) {
		$team_details = get_post_meta($email, 'team_details', true);
		$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
		if ( $user->user_email != $team_email ) {
			pto_add_team_notification($email, $user->ID, $post_id, 'task');
		}
	}
	foreach ( $emails_to_send as $key => $email ) {
		foreach ( $project_contributors as $contrib ) {
			if ( $contrib['team_id'] == $email ) {
				if ( ! empty( $contrib['demail'] ) && $contrib['demail'] == 1 ) {
					unset($emails_to_send[ $key ]);
				}
			}
		}
	}
	$rand = pto_random_string();
	foreach ( $emails_to_send as $email ) {
		$team_details = get_post_meta($email, 'team_details', true);
		$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
		$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';

		$subject = get_option('team_assignment_subject');
		$subject = str_replace('%%PIPING_ID%%', '[' . get_option('cqpim_string_prefix') . ':' . $post_id . ']', $subject);
		$content = get_option('team_assignment_email');
		$url = admin_url() . 'post.php?post=' . $post_id . '&action=edit';
		$content = str_replace('%%TASK_URL%%', $url, $content);
		$content = str_replace('%%NAME%%', $team_name, $content);
		$content = str_replace('%%CURRENT_USER%%', $user->display_name, $content);
		$content = str_replace('%%TASK_UPDATE%%', $message, $content);
		if ( get_option('pto_task_acceptance') == true && $new_owner == true ) {
			update_post_meta($post_id, 'accept_rand', $rand);
			$accept_url = '<strong>' . esc_html__('CLICK TO ACCEPT TASK', 'projectopia-core') . '</strong><br /><br />' . admin_url() . '/admin.php?page=pto-acceptance&task=' . $post_id . '&accept_string=' . $rand;
			$content = str_replace('%%ACCEPT_TASK_LINK%%', $accept_url, $content);
		} else {
			$content = str_replace('%%ACCEPT_TASK_LINK%%', '', $content);
		}
		$subject = pto_replacement_patterns($subject, $post_id, 'task');
		$content = pto_replacement_patterns($content, $post_id, 'task');
		if ( $user->user_email != $team_email ) {
			pto_send_emails($team_email, $subject, $content, '', $attachments, 'sales');
		}
	}
}
add_action( "wp_ajax_nopriv_pto_delete_task_message", "pto_delete_task_message" );
add_action( "wp_ajax_pto_delete_task_message", "pto_delete_task_message" );  
function pto_delete_task_message() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$project_id = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$project_messages = get_post_meta($project_id, 'task_messages', true);
	$project_messages = array_reverse($project_messages);
	unset($project_messages[ $key ]);
	$project_messages = array_filter($project_messages);
	$project_messages = array_reverse($project_messages);
	update_post_meta($project_id, 'task_messages', $project_messages);
	wp_send_json_success();
	exit();
}
add_action( "wp_ajax_pto_add_manual_task_time", "pto_add_manual_task_time" );
function pto_add_manual_task_time() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$hours = isset($_POST['hours']) ? sanitize_text_field( wp_unslash( $_POST['hours'] ) ) : '';
	$minutes = isset($_POST['minutes']) ? sanitize_text_field( wp_unslash( $_POST['minutes'] ) ) : '';   
    $time = $hours + round($minutes / 60, 2);
	$post_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$timer_note = isset($_POST['timer_note']) ? sanitize_textarea_field( wp_unslash( $_POST['timer_note'] ) ) : '';
	$entry_date = isset($_POST['entry_date']) ? sanitize_text_field( wp_unslash( $_POST['entry_date'] ) ) : '';
	if ( empty($time) ) {
		pto_send_json( array( 
			'error'  => true,
			'errors' => __('You must enter how many hours have been completed', 'projectopia-core'),
		) );     
	}
	$user = wp_get_current_user();
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
		}
	}
	
	$team_details = get_post_meta($assigned, 'team_details', true);
	$time_spent = get_post_meta($post_id, 'task_time_spent', true);
	if ( empty($time_spent) ) {
		$time_spent = array();
	}

	$timestamp = time();
	if ( ! empty( $entry_date ) ) {
		$format = get_option('cqpim_date_format');
		$date = DateTime::createFromFormat($format, $entry_date);
		$timestamp = $date->getTimestamp();
	}

	$time_spent[] = array(
		'team'       => $user->display_name,
		'team_id'    => $assigned,
		'time'       => $time,
		'stamp'      => $timestamp,
		'timer_note' => $timer_note,
	);
	update_post_meta($post_id, 'task_time_spent', $time_spent);
	pto_send_json( array( 
		'error' => false,
	) );
}
add_action( "wp_ajax_nopriv_pto_filter_tasks", "pto_filter_tasks" );
add_action( "wp_ajax_pto_filter_tasks", "pto_filter_tasks" );    
function pto_filter_tasks() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$filter = isset( $_POST['filter'] ) ? sanitize_text_field( wp_unslash( $_POST['filter'] ) ) : '';
	if ( empty( $filter ) ) {
		$status = array( 'pending', 'progress' );
	} elseif ( $filter == 'all' ) {
		$status = pto_get_task_status_keys();
		/**
		 * Set All Task Statuses for filtering
		 */
		$status = apply_filters('pto_all_task_statuses', $status);
	}else {
		$filter_arr = array();
		$filter_arr[] = $filter;
		$status = $filter_arr;
	}
	pto_set_transient('task_status', $status);
	pto_send_json( array( 
		'error' => false,
	) );
}
function pto_get_available_assignees( $task_id ) {
	$task_project = get_post_meta($task_id, 'project_id', true);
	$task_project = get_post($task_project);
	$parent_type = isset($task_project->post_type) ? $task_project->post_type : '';
	$user = wp_get_current_user();
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$team_members = get_posts($args);
	$available_members = array();
	if ( empty($task_project) ) {
		foreach ( $team_members as $team_member ) {
			$available_members[] = $team_member->ID;
		}
	} else {
		if ( $parent_type == 'cqpim_project' ) {           
			$contribs = get_post_meta($task_project->ID, 'project_contributors', true);
			if ( ! empty($contribs) ) {
				foreach ( $contribs as $contrib ) {
					$available_members[] = $contrib['team_id'];
				}
			}   
		} else {
			foreach ( $team_members as $team_member ) {
				$team_details = get_post_meta($team_member->ID, 'team_details', true);
				$user = get_user_by('id', $team_details['user_id']);
				$caps = $user->allcaps;
				if ( ! empty($caps['cqpim_view_tickets']) ) {
					$available_members[] = $team_member->ID;
				}
			}
		}
	}
	return $available_members;
}
add_action( "wp_ajax_pto_edit_assignee_from_admin", "pto_edit_assignee_from_admin" );
function pto_edit_assignee_from_admin() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$task_id = isset($_POST['task_id']) ? sanitize_text_field( wp_unslash( $_POST['task_id'] ) ) : '';
	$assignee = isset($_POST['assignee']) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
	$project = get_post_meta($task_id, 'project_id', true);
	$assignee_obj = get_post($assignee);
	$old_assignee = get_post_meta($task_id, 'owner', true);
	if ( $old_assignee != $assignee && $assignee_obj->post_type == 'cqpim_teams' ) {
		$new_assignee = true;
	} else {
		$new_assignee = false;
	}
	$project = $project ? $project : 0;
	update_post_meta($task_id, 'owner', $assignee);
	pto_send_task_updates($task_id, $project, $assignee, '', '', '', '', $new_assignee);
	pto_send_json( array( 
		'error' => false,
	) );
}
add_action( "wp_ajax_pto_assign_all_ms", "pto_assign_all_ms" );
function pto_assign_all_ms() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$ms = isset($_POST['ms']) ? sanitize_text_field( wp_unslash( $_POST['ms'] ) ) : '';
	$assignee = isset($_POST['assignee']) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
	$project = isset($_POST['project_id']) ? sanitize_text_field( wp_unslash( $_POST['project_id'] ) ) : '';
	$notify = isset($_POST['notify']) ? sanitize_text_field( wp_unslash( $_POST['notify'] ) ) : '';
	if ( empty($ms) || empty($project) || empty($assignee) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('There is missing data, please try again', 'projectopia-core') . '</div>',
		) );     
	}
	$task_ids = array();
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key'       => 'milestone_id',
		'meta_value'     => $ms,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$tasks = get_posts($args);
	foreach ( $tasks as $task ) {
		$task_ids[] = $task->ID;
		$assignee_obj = get_post($assignee);
		$old_assignee = get_post_meta($task->ID, 'owner', true);
		if ( $old_assignee != $assignee && $assignee_obj->post_type == 'cqpim_teams' ) {
			$new_assignee = true;
		} else {
			$new_assignee = false;
		}
		update_post_meta($task->ID, 'owner', $assignee);
		if ( ! empty($notify) ) {
			pto_send_task_updates($task->ID, $project, $assignee, '', '', '', '', $new_assignee);
		}
		$args = array(
			'post_type'      => 'cqpim_tasks',
			'posts_per_page' => -1,
			'meta_key'       => 'milestone_id',
			'meta_value'     => $ms,
			'post_parent'    => $task->ID,
			'orderby'        => 'date',
			'order'          => 'ASC',
		);
		$subtasks = get_posts($args);
		foreach ( $subtasks as $subtask ) {
			$task_ids[] = $subtask->ID;
			$assignee_obj = get_post($assignee);
			$old_assignee = get_post_meta($subtask->ID, 'owner', true);
			if ( $old_assignee != $assignee && $assignee_obj->post_type == 'cqpim_teams' ) {
				$new_assignee = true;
			} else {
				$new_assignee = false;
			}
			update_post_meta($subtask->ID, 'owner', $assignee);
			if ( ! empty($notify) ) {
				pto_send_task_updates($subtask->ID, $project, $assignee, '', '', '', '', $new_assignee);
			}
		}
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('The Assignees have been updated.', 'projectopia-core') . '</div>',
		'ids'     => $task_ids,
	) );
}
add_action( "wp_ajax_pto_editable_start", "pto_editable_start" );
function pto_editable_start() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['task_id']) || empty($_POST['date']) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('There is missing data, please try again', 'projectopia-core') . '</div>',
		) ); 
	}
	$date = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['date'] ) ) );
	$task_id = intval( $_POST['task_id'] );
	$task_details = get_post_meta($task_id, 'task_details', true);
	$task_details['task_start'] = $date;
	update_post_meta($task_id, 'task_details', $task_details);
	$milestone_id = get_post_meta($task_id, 'milestone_id', true);
	$project_id = get_post_meta($task_id, 'project_id', true);
	$milestone_dates = pto_calculate_milestone_dates($project_id, $milestone_id);
	pto_send_json( array( 
		'error'           => false,
		'message'         => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('Task Successfully Updated', 'projectopia-core') . '</div>',
		'milestone_dates' => $milestone_dates,
	) );
}
add_action( "wp_ajax_pto_editable_end", "pto_editable_end" );
function pto_editable_end() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['task_id']) || empty($_POST['date']) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('There is missing data, please try again', 'projectopia-core') . '</div>',
		) ); 
	}
	$tasks_to_update = array();
	$date = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['date'] ) ) );
	$task_id = intval( $_POST['task_id'] );
	$task_details = get_post_meta($task_id, 'task_details', true);
	$original_deadline = $task_details['deadline'];
	$task_details['deadline'] = $date;
	update_post_meta($task_id, 'task_details', $task_details);
	$milestone_id = get_post_meta($task_id, 'milestone_id', true);
	$project_id = get_post_meta($task_id, 'project_id', true);  
	$auto_dates = get_post_meta($project_id, 'auto_dates', true);   
	if ( $auto_dates == 1 ) {  
		if ( $date > $original_deadline ) {
			$offset = $date - $original_deadline;
			$args = array(
				'post_type'      => 'cqpim_tasks',
				'posts_per_page' => -1,
				'meta_key'       => 'milestone_id',
				'meta_value'     => $milestone_id,
			);
			$tasks = get_posts($args);
			$ordered = array();
			foreach ( $tasks as $task ) {
				$task_details = get_post_meta($task->ID, 'task_details', true);
				$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
				if ( empty($task->post_parent) ) {
					$ordered[ $weight ] = $task->ID;
				}
			}
			ksort($ordered);        
			$ordered_sorted = $ordered;
			foreach ( $ordered as $key => $value ) {
				if ( $value == $task_id ) {
					break;
				}
				unset($ordered_sorted[ $key ]);
			}   
			$tasks_to_update = array();
			foreach ( $ordered_sorted as $task ) {
				if ( $task != $task_id ) {
					$new_dates = pto_add_task_offset($task, $offset);
					$tasks_to_update[] = array(
						'task_id'  => $task,
						'start'    => wp_date(get_option('cqpim_date_format'), $new_dates['start']),
						'deadline' => wp_date(get_option('cqpim_date_format'), $new_dates['deadline']),
					);
					$args = array(
						'post_type'      => 'cqpim_tasks',
						'posts_per_page' => -1,
						'post_parent'    => $task,
					);
					$subtasks = get_posts($args);
					foreach ( $subtasks as $subtask ) {
						$new_dates = pto_add_task_offset($subtask->ID, $offset);
						$tasks_to_update[] = array(
							'task_id'  => $subtask->ID,
							'start'    => wp_date(get_option('cqpim_date_format'), $new_dates['start']),
							'deadline' => wp_date(get_option('cqpim_date_format'), $new_dates['deadline']),
						);
					}
				}   
			}
		}
	}
	$milestone_dates = pto_calculate_milestone_dates($project_id, $milestone_id);
	$overdue = pto_is_task_overdue($task_id, 'border');
	$task_details = get_post_meta($task_id, 'task_details', true);
	if ( $overdue == 'border-red overdue' ) {
		$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op nolink rounded_2">' . esc_html__('OVERDUE', 'projectopia-core') . '</span>';
	} else {
		/*if ( $task_details['status'] == 'pending' ) { 
			$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-amber rounded_2 op nolink rounded_2">' . esc_html__('Pending', 'projectopia-core') . '</span>';
		}
		if ( $task_details['status'] == 'progress' ) { 
			$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-amber rounded_2 op nolink rounded_2">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
		}
		if ( $task_details['status'] == 'on_hold' ) { 
			$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op nolink rounded_2">' . esc_html__('On Hold', 'projectopia-core') . '</span>';
		}
		if ( $task_details['status'] == 'complete' ) { 
			$task_status_string = '<span class="cqpim_button cqpim_small_button font-white bg-green rounded_2 op nolink rounded_2">' . esc_html__('Complete', 'projectopia-core') . '</span>';
		}*/
		$task_status_string = pto_get_task_status_html( $task_details['status'], false );
	}
	/**
	 * Filter Task Status Display in Project page
	 */
	$task_status_string = apply_filters('pto_project_task_status_string', $task_status_string, $task_details['status'], $task_details);
	pto_send_json( array( 
		'error'              => false,
		'message'            => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('Task Successfully Updated', 'projectopia-core') . '</div>',
		'task_id'            => $task_id,
		'overdue'            => $overdue,
		'task_status_string' => $task_status_string,
		'milestone_dates'    => $milestone_dates,
		'tasks_to_update'    => $tasks_to_update,
	) );
}
function pto_add_task_offset( $task_id, $offset ) {
	$task_details = get_post_meta($task_id, 'task_details', true);
	$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
	$deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
	if ( ! empty($task_start) ) {
		$task_start = $task_start + $offset;
		$task_details['task_start'] = $task_start;
	}
	if ( ! empty($deadline) ) {
		$deadline = $deadline + $offset;
		$task_details['deadline'] = $deadline;
	}
	update_post_meta($task_id, 'task_details', $task_details);
	return array(
		'start'    => $task_start,
		'deadline' => $deadline,
	);
}
add_action( "wp_ajax_pto_editable_assignee", "pto_editable_assignee" );
function pto_editable_assignee() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['task_id']) || empty($_POST['assignee']) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('There is missing data, please try again', 'projectopia-core') . '</div>',
		) ); 
	}
	$task_id = intval( $_POST['task_id'] );
	$assignee = isset( $_POST['assignee'] ) ? sanitize_text_field( wp_unslash( $_POST['assignee'] ) ) : '';
	$project = get_post_meta($task_id, 'project_id', true);
	$assignee_obj = get_post($assignee);
	$old_assignee = get_post_meta($task_id, 'owner', true);
	if ( $old_assignee != $assignee && $assignee_obj->post_type == 'cqpim_teams' ) {
		$new_assignee = true;
	} else {
		$new_assignee = false;
	}
	$project = $project ? $project : 0;
	update_post_meta($task_id, 'owner', $assignee);
	pto_send_task_updates($task_id, $project, $assignee, '', '', '', '', $new_assignee);
	update_post_meta($task_id, 'owner', $assignee);
	$current_user = wp_get_current_user();
	pto_add_team_notification($assignee, $current_user->ID, $task_id, 'task_assignee', $ctype = '');
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('Task Successfully Updated', 'projectopia-core') . '</div>',
	) );
}
function pto_is_task_overdue( $task_id, $type = '' ) {
	$task_details = get_post_meta( $task_id, 'task_details', true );
	$task_start = isset( $task_details['task_start'] ) ? $task_details['task_start'] : '';
	$task_deadline = isset( $task_details['deadline'] ) ? $task_details['deadline'] : '';
	$task_status = isset( $task_details['status'] ) ? $task_details['status'] : '';
	$class = '';
	if ( empty( $task_deadline ) || $task_status == 'complete' ) {
		return $class;
	}
	$three_days = strtotime( gmdate( 'Y-m-d', strtotime( '-3 days', $task_deadline ) ) );
	$today = strtotime( 'today' );
	$task_deadline = strtotime( gmdate( 'Y-m-d', $task_deadline ) );
	if ( $today > $three_days && $today <= $task_deadline ) {
		$class = $type . '-amber overdue';
 	} elseif ( $today > $task_deadline ) {
		$class = $type . '-red overdue';
	}
	return $class;
}
function pto_calculate_milestone_dates( $project_id, $milestone_id ) {
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'meta_key'       => 'milestone_id',
		'meta_value'     => $milestone_id,
		'posts_per_page' => -1,
	);
	$tasks = get_posts($args);
	$start_dates = array();
	$deadlines = array();
	foreach ( $tasks as $task ) {
		$task_details = get_post_meta($task->ID, 'task_details', true);
		$start_date = isset($task_details['task_start']) ? $task_details['task_start'] : '';
		if ( ! empty($start_date) ) {
			$start_dates[] = $start_date;
		}
		$deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
		if ( ! empty($deadline) ) {
			$deadlines[] = $deadline;
		}
	}
	if ( empty($start_dates) || empty($deadlines) ) {
		return false;
	}
	$start_date = min($start_dates);
	$deadline = max($deadlines);
	
	$post_type = get_post_type( $project_id );
	if ( $post_type == 'cqpim_quote' ) {
		$project_elements = get_post_meta( $project_id, 'quote_elements', true );
	} else {
		$project_elements = get_post_meta( $project_id, 'project_elements', true );
	}

	if ( empty( $project_elements ) ) {
		$project_elements = array();
	}

	$old_start_date = $project_elements[ $milestone_id ]['start'];
	if ( $old_start_date > $start_date ) {
		$project_elements[ $milestone_id ]['start'] = $start_date;
	}

	$old_end_date = $project_elements[ $milestone_id ]['deadline'];
	if ( $old_end_date < $deadline ) {
		$project_elements[ $milestone_id ]['deadline'] = $deadline;
	}

	if ( $post_type == 'cqpim_quote' ) {
		update_post_meta($project_id, 'quote_elements', $project_elements);
	} else {
		update_post_meta($project_id, 'project_elements', $project_elements);
	}
	return array(
		'project_id'   => $project_id,
		'milestone_id' => $milestone_id,
		'start'        => wp_date(get_option('cqpim_date_format'), $project_elements[ $milestone_id ]['start']),
		'deadline'     => wp_date(get_option('cqpim_date_format'), $project_elements[ $milestone_id ]['deadline']),
	);
}
//add_action( 'current_screen', 'pto_change_milestone_dates' );
function pto_change_milestone_dates() {
	$screen = get_current_screen();
	$action = isset($_GET['action']) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
	if ( $screen->post_type == 'cqpim_project' && $action == 'edit' ) {
		$post_id = isset( $_GET['post'] ) ? intval( wp_unslash( $_GET['post'] ) ) : '';
		$post = get_post($post_id);
		$project_elements = get_post_meta($post->ID, 'project_elements', true);
		if ( $post && ! empty($project_elements) ) {
			foreach ( $project_elements as $key => $element ) {
				$update = pto_calculate_milestone_dates($post_id, $key);
			}
		}
	}
}
add_action( "wp_ajax_pto_switch_auto_dates", "pto_switch_auto_dates");
function pto_switch_auto_dates() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['project_id']) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('There is missing data, please try again', 'projectopia-core') . '</div>',
		) ); 
	}

	$project_id = intval( $_POST['project_id'] );
	if ( isset( $_POST['dates'] ) ) {
		update_post_meta( $project_id, 'auto_dates', sanitize_text_field( wp_unslash( $_POST['dates'] ) ) );
	} else {
		delete_post_meta( $project_id, 'auto_dates' );
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('Project Successfully Updated', 'projectopia-core') . '</div>',
	) );
}
add_action( "wp_ajax_pto_accept_task", "pto_accept_task");
function pto_accept_task() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	if ( empty($_POST['task_id']) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('There is missing data, please try again', 'projectopia-core') . '</div>',
		) ); 
	}
	$task_id = intval( $_POST['task_id'] );
	$response = isset( $_POST['response'] ) ? sanitize_text_field( wp_unslash( $_POST['response'] ) ) : '';
	$reason = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';
	if ( $response == 2 ) {
		$choice = 'rejected';
		$current_user = wp_get_current_user();
		$current_team = pto_get_team_from_userid($current_user);
		$last_updated = get_post_meta($task_id, 'last_updated', true);
		$updated_user = get_user_by('id', $last_updated);
		$updated_team = pto_get_team_from_userid($updated_user);
		$task_obj = get_post($task_id);
		$task_url = admin_url() . '/post.php?post=' . $task_id . '&action=edit';    
		$subject = get_option('assignment_response_subject');
		$content = get_option('assignment_response_email');
		$subject = str_replace('%%CURRENT_USER%%', $current_user->display_name, $subject);
		$subject = str_replace('%%ACCEPT_CHOICE%%', $choice, $subject);
		$subject = str_replace('%%TASK_NAME%%', $task_obj->post_title, $subject);
		$content = str_replace('%%CURRENT_USER%%', $current_user->display_name, $content);
		$content = str_replace('%%ACCEPT_CHOICE%%', $choice, $content);
		$content = str_replace('%%TASK_NAME%%', $task_obj->post_title, $content);
		$content = str_replace('%%NAME%%', $updated_user->display_name, $content);
		$content = str_replace('%%NOTES%%', $reason, $content);
		$content = str_replace('%%TASK_URL%%', $task_url, $content);
		$content = pto_replacement_patterns($content, $current_team, 'team');
		pto_send_emails($updated_user->user_email, $subject, $content, array(), array(), 'sales');
		update_post_meta($task_id, 'accept_rand', '');
		pto_send_json( array( 
			'error'   => false,
			'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Your reason has been sent, you can now leave this page.', 'projectopia-core') . '</div>',
		) );
	} else {
		$choice = 'accepted';
		$current_user = wp_get_current_user();
		$current_team = pto_get_team_from_userid($current_user);
		$last_updated = get_post_meta($task_id, 'last_updated', true);
		$task_details = get_post_meta($task_id, 'task_details', true);
		$task_details['status'] = 'progress';
		update_post_meta($task_id, 'task_details', $task_details);
		$updated_user = get_user_by('id', $last_updated);
		$updated_team = pto_get_team_from_userid($updated_user);
		$task_obj = get_post($task_id);
		$task_url = admin_url() . '/post.php?post=' . $task_id . '&action=edit';    
		$subject = get_option('assignment_response_subject');
		$content = get_option('assignment_response_email');
		$subject = str_replace('%%CURRENT_USER%%', $current_user->display_name, $subject);
		$subject = str_replace('%%ACCEPT_CHOICE%%', $choice, $subject);
		$subject = str_replace('%%TASK_NAME%%', $task_obj->post_title, $subject);
		$content = str_replace('%%CURRENT_USER%%', $current_user->display_name, $content);
		$content = str_replace('%%ACCEPT_CHOICE%%', $choice, $content);
		$content = str_replace('%%TASK_NAME%%', $task_obj->post_title, $content);
		$content = str_replace('%%NAME%%', $updated_user->display_name, $content);
		$content = str_replace('%%NOTES%%', '', $content);
		$content = str_replace('%%TASK_URL%%', $task_url, $content);
		$content = pto_replacement_patterns($content, $current_team, 'team');
		pto_send_emails($updated_user->user_email, $subject, $content, array(), array(), 'sales');
		update_post_meta($task_id, 'accept_rand', '');
		pto_send_json( array( 
			'error'    => false,
			'message'  => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('You have accepted the task, redirecting.... ', 'projectopia-core') . '</div>',
			'redirect' => $task_url,
		) );     
	}
	pto_send_json( array( 
		'error'   => false,
		'message' => '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_html__('Project Successfully Updated', 'projectopia-core') . '</div>',
	) );
}

add_action( 'save_post_cqpim_tasks', 'pto_save_post_cqpim_tasks', 20, 2 );

function pto_save_post_cqpim_tasks( $post_id, $post ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if( isset( $_POST['task_status'] ) && !empty( $_POST['task_status'] )) {
		update_post_meta($post_id,'task_status',$_POST['task_status']);
	}

}