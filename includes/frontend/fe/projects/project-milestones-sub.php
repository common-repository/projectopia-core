<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$p_title = get_the_title();
$p_title = str_replace('Private:', '', $p_title);
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	/* translators: %1$s: Project ID, %2$s: Project Title */
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Milestones Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('Milestones & Tasks', 'projectopia-core'); ?></span>
				</div>	
			</div>
		<?php if ( empty($project_elements) ) {
			echo '<p style="padding:30px">' . esc_html__('There are no Milestones or tasks on this project', 'projectopia-core') . '</p>';
			$milestone_counter = 0;
		} else {
			$milestone_counter = 0;
			$currency = get_option('currency_symbol');
			if ( $p_type == 'estimate' ) { 
				$cost_title = __('Estimated Cost', 'projectopia-core');
			} else {
				$cost_title = __('Cost', 'projectopia-core');
			}
			echo '<table class="cqpim_table">';
			echo '<thead>';
			echo '<tr><th>' . esc_html__('Type', 'projectopia-core') . '</th><th>' . esc_html__('Title', 'projectopia-core') . '</th><th>' . esc_html__('Start Date', 'projectopia-core') . '</th><th>' . esc_html__('Deadline', 'projectopia-core') . '</th><th>' . esc_html__('Progress / Status', 'projectopia-core') . '</th>';
			echo '</thead>';
			$ordered = array();
			$i = 0;
			$mi = 0;
			foreach ( $project_elements as $key => $element ) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[ $weight ] = $element;
				$mi++;
			}
			ksort($ordered);                        
			foreach ( $ordered as $element ) { 
			$cost = preg_replace("/[^\\d.]+/","", $element['cost']); 
				$task_status = isset($element['status']) ? $element['status'] : '';
				$task_deadline = isset($element['deadline']) ? $element['deadline'] : '';
				if ( ! is_numeric($task_deadline) ) {
					$str_deadline = str_replace('/','-', $task_deadline);
					$deadline_stamp = strtotime($str_deadline);
				} else {
					$deadline_stamp = $task_deadline;
				}
				$now = time();
				if ( $task_status != 'complete' ) {
					if ( $deadline_stamp && $now > $deadline_stamp ) {
						$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-red font-red sbold op nolink">' . esc_html__('OVERDUE', 'projectopia-core') . '</span>';
					} else {
						$milestone_status_string = isset($element['status']) ? $element['status'] : '';
						if ( ! $milestone_status_string || $milestone_status_string == 'pending' ) {
							$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-amber font-amber op nolink">' . esc_html__('Pending', 'projectopia-core') . '</span>';
						} elseif ( $milestone_status_string == 'on_hold' ) {
							$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-blue font-blue op nolink">' . esc_html__('On Hold', 'projectopia-core') . '</span>';
						}
					}
				} else {
					$milestone_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . esc_html__('Complete', 'projectopia-core') . '</span>';
				}
				if ( is_numeric($element['start']) ) { $start = wp_date(get_option('cqpim_date_format'), $element['start']); } else { $start = $element['start']; }
				if ( is_numeric($element['deadline']) ) { $deadline = wp_date(get_option('cqpim_date_format'), $element['deadline']); } else { $deadline = $element['deadline']; }
				?>
				<tr class="milestone">
					<td><span class="cqpim_button cqpim_small_button bg-dark-blue font-white op nolink rounded_2"><?php esc_html_e('Milestone', 'projectopia-core'); ?></span></td>
					<td><span class="nodesktop"><strong><?php esc_html_e('Title', 'projectopia-core'); ?></strong>: </span> <?php echo wp_kses_post( $element['title'] ); ?></td>
					<td><span class="nodesktop"><strong><?php esc_html_e('Start Date', 'projectopia-core'); ?></strong>: </span> <?php echo esc_html( $start ); ?></td>
					<td><span class="nodesktop"><strong><?php esc_html_e('Deadline', 'projectopia-core'); ?></strong>: </span> <?php echo esc_html( $deadline ); ?></td>
					<td><?php echo wp_kses_post( $milestone_status_string ); ?></td>
				</tr>
				<?php 
				$args = array(
					'post_type'      => 'cqpim_tasks',
					'posts_per_page' => -1,
					'meta_key'       => 'milestone_id',
					'meta_value'     => $element['id'],
					'orderby'        => 'date',
					'order'          => 'ASC',
				);
				$tasks = get_posts($args);
				$ti = 0;
				$ordered = array();
				$wi = 0;
				foreach ( $tasks as $task ) {
					$task_details = get_post_meta($task->ID, 'task_details', true);
					$weight = isset($task_details['weight']) ? $task_details['weight'] : $wi;
					if ( empty($task->post_parent) ) {
						$ordered[ $weight ] = $task;
					}
					$wi++;
				}
				ksort($ordered);
				foreach ( $ordered as $task ) {
					$task_details    = get_post_meta($task->ID, 'task_details', true);
					$project_id      = get_post_meta($task->ID, 'project_id', true);
					$project_details = get_post_meta($project_id, 'project_details', true);
					$client_id       = ! empty( $project_details['client_id'] ) ? $project_details['client_id'] : ''; 
					$task_owner      = get_post_meta($task->ID, 'owner', true);
					//Check task assignee.
					if ( empty( $task_owner ) ) {
						$task_owner = '';
					} elseif ( $client_id == $task_owner ) {
						$client_details      = get_post_meta($client_id, 'client_details', true);
						$client_contact_name = ! empty( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
						$task_owner          = ' (' . esc_html( $client_contact_name ) . ')';
					} else {
						$team_details = get_post_meta( $task_owner, 'team_details', true);
						if ( ! empty( $team_details['team_name'] ) ) {
							$task_owner = ' (' . $team_details['team_name'] . ')';
						}
					}

					$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
					$task_status = isset($task_details['status']) ? $task_details['status'] : '';
					$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
					if ( ! is_numeric($task_deadline) ) {
						$str_deadline = str_replace('/','-', $task_deadline);
						$deadline_stamp = strtotime($str_deadline);
					} else {
						$deadline_stamp = $task_deadline;
					}
					if ( is_numeric($task_deadline) ) { $task_deadline = wp_date(get_option('cqpim_date_format'), $task_deadline); } else { $task_deadline = $task_deadline; }
					/*$now = time();
					if ( $task_status != 'complete' ) {
						if ( $deadline_stamp && $now > $deadline_stamp ) {
							$task_status_string = '<span class="cqpim_button cqpim_small_button border-red font-red sbold op nolink">' . esc_html__('OVERDUE', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
						} else {
							$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
							if ( ! $task_status_string || $task_status_string == 'pending' ) {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-amber font-amber op nolink">' . esc_html__('Pending', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
							} elseif ( $task_status_string == 'on_hold' ) {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-blue font-blue op nolink">' . esc_html__('On Hold', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
							} elseif ( $task_status_string == 'progress' ) {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . esc_html__('In Progress', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
							}
						}
					} else {
						$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . esc_html__('Complete', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
					}*/
					$task_status_string = pto_get_task_status_html( $task_status, $deadline_stamp );
					/**
					 * Filter Task Status Display in Project page
					 */
					$task_status_string = apply_filters('pto_project_task_status_string', $task_status_string, $task_details['status'], $task_details);
					?>						
					<tr>
						<td><span class="cqpim_button cqpim_small_button bg-grey-cascade font-white op nolink rounded_2"><?php esc_html_e('Task', 'projectopia-core'); ?></span></td>
						<td colspan="2"><span class="nodesktop"><strong><?php esc_html_e('Title', 'projectopia-core'); ?></strong>: </span><a class="cqpim-link" href="<?php echo esc_url( get_the_permalink($task->ID) ); ?>"><?php echo esc_html( $task->post_title ); ?></a><br /><?php echo wp_kses_post( $task_owner ); ?></td>
						<td><span class="nodesktop"><strong><?php esc_html_e('Deadline', 'projectopia-core'); ?></strong>: </span><?php echo esc_html( $task_deadline ); ?></td>
						<td style="text-transform:capitalize"><?php echo wp_kses_post( $task_status_string ); ?></td>
					</tr>
					<?php 
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
					$sti = 0;
					$subordered = array();
					$swi = 0;
					foreach ( $subtasks as $subtask ) {
						$stask_details = get_post_meta($subtask->ID, 'task_details', true);
						$sweight = isset($stask_details['weight']) ? $stask_details['weight'] : $swi;
						$subordered[ $sweight ] = $subtask;
						$swi++;
					}
					ksort($subordered);
					foreach ( $subordered as $subtask ) {
						$task_details    = get_post_meta($subtask->ID, 'task_details', true);
						$project_id      = get_post_meta($subtask->ID, 'project_id', true);
						$project_details = get_post_meta($project_id, 'project_details', true);
						$client_id       = ! empty( $project_details['client_id'] ) ? $project_details['client_id'] : ''; 
						$task_owner      = get_post_meta($subtask->ID, 'owner', true);

						//Check subtask assignee.
						if ( empty( $task_owner ) ) {
							$task_owner = '';
						} elseif ( $client_id == $task_owner ) {
							$client_details      = get_post_meta($client_id, 'client_details', true);
							$client_contact_name = ! empty( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
							$task_owner          = ' (' . esc_html( $client_contact_name ) . ')';
						} else {
							$team_details = get_post_meta( $task_owner, 'team_details', true);
							if ( ! empty( $team_details['team_name'] ) ) {
								$task_owner = ' (' . $team_details['team_name'] . ')';
							}
						}

						$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
						$task_status = isset($task_details['status']) ? $task_details['status'] : '';
						$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
						if ( ! is_numeric($task_deadline) ) {
							$str_deadline = str_replace('/','-', $task_deadline);
							$deadline_stamp = strtotime($str_deadline);
						} else {
							$deadline_stamp = $task_deadline;
						}
						if ( is_numeric($task_deadline) ) { $task_deadline = wp_date(get_option('cqpim_date_format'), $task_deadline); } else { $task_deadline = $task_deadline; }
						/*$now = time();
						if ( $task_status != 'complete' ) {
							if ( $deadline_stamp && $now > $deadline_stamp ) {
								$task_status_string = '<span class="cqpim_button cqpim_small_button border-red font-red sbold op nolink">' . esc_html__('OVERDUE', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
							} else {
								$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
								if ( ! $task_status_string || $task_status_string == 'pending' ) {
									$task_status_string = '<span class="cqpim_button cqpim_small_button border-amber font-amber op nolink">' . esc_html__('Pending', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
								} elseif ( $task_status_string == 'on_hold' ) {
									$task_status_string = '<span class="cqpim_button cqpim_small_button border-blue font-blue op nolink">' . esc_html__('On Hold', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
								} elseif ( $task_status_string == 'progress' ) {
									$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . esc_html__('In Progress', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
								}
							}
						} else {
							$task_status_string = '<span class="cqpim_button cqpim_small_button border-green font-green op nolink">' . esc_html__('Complete', 'projectopia-core') . ' - ' . $task_pc . '%</span>';
						}*/
						$task_status_string = pto_get_task_status_html( $task_status, $deadline_stamp );
						/**
						 * Filter Task Status Display in Project page
						 */
						$task_status_string = apply_filters('pto_project_task_status_string', $task_status_string, $task_details['status'], $task_details);
						?>						
						<tr>
							<td><span class="cqpim_button cqpim_small_button bg-grey-cascade font-white op nolink rounded_2"><?php esc_html_e('Subtask', 'projectopia-core'); ?></span></td>
							<td colspan="2"><span class="nodesktop"><strong><?php esc_html_e('Title', 'projectopia-core'); ?></strong>: </span><a class="cqpim-link" href="<?php echo esc_url( get_the_permalink($subtask->ID) ); ?>"><?php echo esc_html( $subtask->post_title ); ?></a><br /><?php echo wp_kses_post( $task_owner ); ?></td>
							<td><span class="nodesktop"><strong><?php esc_html_e('Deadline', 'projectopia-core'); ?></strong>: </span><?php echo esc_html( $task_deadline ); ?></td>
							<td style="text-transform:capitalize"><?php echo wp_kses_post( $task_status_string ); ?></td>
						</tr>							
						<?php
					}                   
				} ?>
			<?php $milestone_counter++;
			} 
			echo '</table>';
			}
			?>
		</div>
	</div>
</div>