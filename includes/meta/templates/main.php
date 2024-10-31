<?php
add_action( 'add_meta_boxes_cqpim_templates', 'add_pto_templates_cpt_metaboxes' );
function add_pto_templates_cpt_metaboxes( $post ) {
	add_meta_box( 
		'templates_template', 
		__('Milestones & Tasks', 'projectopia-core'),
		'pto_templates_metabox_callback', 
		'cqpim_templates', 
		'normal',
		'high'
	);
	if ( ! current_user_can('publish_cqpim_templates') ) {
		remove_meta_box( 'submitdiv', 'cqpim_templates', 'side' );
	}
}

function pto_templates_metabox_callback( $post ) {
	$next_ms_date = time();
	$args = array(
		'post_type'      => 'cqpim_templates',
		'post_status'    => 'private',
		'posts_per_page' => -1,
	);
	$templates = get_posts($args);
	foreach ( $templates as $template ) {
		$mstemplate = get_post_meta($template->ID, 'project_template', true);
		if ( ! empty($mstemplate) ) {
			$milestones = isset($mstemplate['milestones']) ? $mstemplate['milestones'] : array();
			if ( empty($milestones) ) {
				$new_format = array();
				$msids = array( 0 );
				foreach ( $mstemplate as $key => $milestone ) {
					$msids[] = $milestone['id'];
					$tasks = isset($milestone['tasks']) ? $milestone['tasks'] : array();
					unset($milestone['tasks']);
					$new_format['milestones'][ $key ] = $milestone;
					if ( ! empty($tasks) ) {
						$check = isset($milestone['tasks']['task_arrays']) ? $milestone['tasks']['task_arrays'] : array();
						if ( empty($check) ) {
							$tids = array( 0 );
							foreach ( $tasks as $tkey => $task ) {
								if ( empty($task['offset']) ) {
									$task['offset'] = 0;
								}
								$tids[] = $task['id'];
								$subtasks = isset($task['subtasks']) ? $task['subtasks'] : array();
								$check2 = isset($task['subtasks']['task_arrays']) ? $task['subtasks']['task_arrays'] : array();
								unset($task['subtasks']);
								$new_format['milestones'][ $key ]['tasks']['task_arrays'][ $tkey ] = $task;
								if ( ! empty($subtasks) ) {
									if ( empty($check2) ) {
										$stids = array( 0 );
										foreach ( $subtasks as $stkey => $subtask ) {
											if ( empty($subtask['offset']) ) {
												$subtask['offset'] = 0;
											}
											$new_format['milestones'][ $key ]['tasks']['task_arrays'][ $tkey ]['subtasks']['task_arrays'][ $stkey ] = $subtask;
											$stids[] = $subtask['id'];
										}
										$high_stid = max($stids);
										$new_format['milestones'][ $key ]['tasks']['task_arrays'][ $tkey ]['subtasks']['task_id'] = $high_stid + 1;                                     
									}
								}
							}
							$high_tid = max($tids);
							$new_format['milestones'][ $key ]['tasks']['task_id'] = $high_tid + 1;
						}
					}
				}
				$high_id = max($msids);
				$new_format['ms_key'] = $high_id + 1;
				update_post_meta($template->ID, 'project_template', $new_format);
			}
		}
	}

	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private', 
	);
	$team_array = array();
	$team_members = get_posts($args);
	foreach ( $team_members as $team ) {
		$team_details = get_post_meta($team->ID, 'team_details', true);
		$team_array[ $team->ID ] = array(
			'id'   => $team->ID,
			'name' => isset($team_details['team_name']) ? $team_details['team_name'] : __('Name not set', 'projectopia-core'),
		);
	}
	$offset_ranges = range( 1, 100 );
	$offset_options = [];
	foreach ( $offset_ranges as $day ) {
		/* translators: %s: No of days */
		$offset_options[ $day ] = sprintf( _n( '%s day', '%s days', $day, 'projectopia-core' ), number_format_i18n( $day ) );
	}
	$team_options = [];
	foreach ( $team_array as $team_member ) {
		$team_options[ $team_member['id'] ] = $team_member['name'];
	}

 	wp_nonce_field( 'templates_metabox', 'templates_metabox_nonce' );

	$template = get_post_meta($post->ID, 'project_template', true);
	$test_date = get_post_meta($post->ID, 'test_date', true);
	if ( empty($template) ) {
		$template = array();
	}
	$milestone_key = isset($template['ms_key']) ? $template['ms_key'] : 1;
	$milestones = isset($template['milestones']) ? $template['milestones'] : '';
	$mstotal = count($template);
	$mstotal = $mstotal + 1;
	if ( empty($mstotal) ) {
		$mstotal = 1;
	}
	$title_set = get_post_meta($post->ID, 'title_set', true);
	echo '<input type="hidden" id="title_set" value="' . esc_attr( $title_set ) . '" />';
	if ( empty($milestones) ) {
		echo '<p>' . esc_html__('You haven\'t added any milestones or tasks to this template.', 'projectopia-core') . '</p>';
	} else {
		if ( ! empty( $test_date ) ) {
			$next_ms_date = $test_date;
		}

		pto_generate_fields( array(
			'id'    => 'test_date',
			'label' => __( 'Starting Date:', 'projectopia-core' ),
			'class' => 'datepicker',
			'value' => wp_date( get_option( 'cqpim_date_format' ), $next_ms_date ),
		) );

		//echo '<input type="submit" class="mt-10 piaBtn btn btn-primary ml-2 caribbeanGreen right" value="' . esc_attr__('Update Milestone Template', 'projectopia-core') . '"/><div class="clear"></div><br />';
		$currency = get_option('currency_symbol');
		$cost_title = __('Cost', 'projectopia-core');
		$ordered = array();
		foreach ( $milestones as $key => $element ) { 
			$ordered[ $element['weight'] ] = $element;
		}
		ksort($ordered);
		echo '<div id="dd-container" class="milestone-content">';
		foreach ( $ordered as $key => $element ) { 
			$cost = preg_replace("/[^\\d.]+/","", $element['cost']); 
			$task_deadline = isset($element['deadline']) ? $element['deadline'] : '';
			$this_milestone_start = $next_ms_date;
			$inc_weekend = isset($element['include_weekends']) ? $element['include_weekends'] : 0;
			?>
			<div class="dd-milestone" id="milestone-<?php echo esc_attr( $element['id'] ); ?>">
				<input type="hidden" class="element_weight" name="element_weight[<?php echo esc_attr( $element['id'] ); ?>]" id="element_weight[<?php echo esc_attr( $element['id'] ); ?>]" data-msid="<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $element['weight'] ); ?>" />
				<div class="dd-milestone-title">
					<span class="ms-title" style="color: #337ab7;"><?php esc_html_e('Milestone:', 'projectopia-core'); ?></span>
					<span class="ms-title" id="ms_title_<?php echo esc_attr( $element['id'] ); ?>"><?php echo esc_html( $element['title'] ); ?></span>
					<div class="dd-milestone-actions">
						<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
							<button class="btn px-3" type="button"
								data-toggle="dropdown" aria-haspopup="true"
								aria-expanded="false">
								<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
									class="img-fluid" />
							</button>
							<div class="dropdown-menu">
								<button data-ms="<?php echo esc_attr( $element['id'] ); ?>" 
										data-project="<?php echo esc_attr( $post->ID ); ?>" 
										value="<?php echo esc_attr( $element['id'] ); ?>"
										class="add_task dropdown-item d-flex align-items-center"
										type="button">
									<?php esc_html_e('Add Task', 'projectopia-core'); ?>
								</button>
								<button value="<?php echo esc_attr( $element['id'] ); ?>"
										class="edit-milestone dropdown-item d-flex align-items-center"
										type="button">
									<?php esc_html_e('Edit Milestone', 'projectopia-core'); ?>
								</button>
								<button class="delete_stage_conf dropdown-item d-flex align-items-center"
									type="button"
									data-id="<?php echo esc_attr( $element['id'] ); ?>" 
									value="<?php echo esc_attr( $element['id'] ); ?>">
									<?php esc_html_e('Delete Milestone', 'projectopia-core'); ?>
								</button>
							</div>
						</div>
						<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" 
							value="<?php echo esc_attr( $element['id'] ); ?>" 
							title="<?php esc_html_e('Reorder Milestone', 'projectopia-core'); ?>">
							<i class="fa fa-sort" aria-hidden="true"></i>
						</button>
					</div>
					<div class="clear"></div>
					<div class="dd-milestone-info mileStone-content-taskBar d-block flex-wrap d-md-flex justify-content-between align-items-center">
						<div class="mileStone-content-deadline d-block d-sm-flex">
							<?php if ( ! empty( $element['cost'] ) ) { ?>
								<div class="mileStone-content-singleDeadline d-flex">
									<div class="mileStone-content-icon">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/usacoin.svg' ); ?>" class="icon img-fluid mr-2" />
									</div>
									<div class="mileStone-content-info">
										<h5 class="mb-1"><?php esc_html_e('Budget', 'projectopia-core'); ?></h5>
										<p id="ms_cost_<?php echo esc_attr( $element['id'] ); ?>" class="mb-0"><?php echo esc_html( pto_calculate_currency( $post->ID, $element['cost'] ) ); ?></p>
									</div>
								</div>
							<?php } ?>
 							<?php if ( ! empty( $next_ms_date ) ) { ?>
								<div class="mileStone-content-singleDeadline d-flex">
									<div class="mileStone-content-icon">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/clander.svg' ); ?>" class="icon img-fluid mr-2" />
									</div>
									<div class="mileStone-content-info">
										<h5 class="mb-1"><?php esc_html_e('Start Date', 'projectopia-core'); ?></h5>
										<p id="ms_start_<?php echo esc_attr( $element['id'] ); ?>" class="mb-0"><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ), $next_ms_date ) ); ?></p>
									</div>
								</div>
							<?php } ?>
  							<?php if ( ! empty( $element['offset'] ) ) { ?>
								<div class="mileStone-content-singleDeadline d-flex">
									<div class="mileStone-content-icon">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/clander.svg' ); ?>" class="icon img-fluid mr-2" />
									</div>
									<div class="mileStone-content-info">
										<h5 class="mb-1"><?php esc_html_e('Deadline', 'projectopia-core'); ?></h5>
										<p class="mb-0" id="ms_deadline_<?php echo esc_attr( $element['id'] ); ?>"><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ), pto_convert_date_range( $next_ms_date, $element['offset'], $inc_weekend ) ) ); ?></p>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<?php 
				$this_milestone_end = pto_convert_date_range($next_ms_date, $element['offset'],$inc_weekend);
				$next_task_date = $this_milestone_start;
				$next_ms_date = pto_convert_date_range($next_ms_date, $element['offset'],$inc_weekend); ?>
				<div class="dd-tasks" data-ms="<?php echo esc_attr( $key ); ?>">
					<?php 
					$task_id = isset($element['tasks']['task_id']) ? $element['tasks']['task_id'] : 1;
					$tasks = isset($element['tasks']['task_arrays']) ? $element['tasks']['task_arrays'] : array();  
					$tordered = array();
					foreach ( $tasks as $task ) {
						$tordered[ $task['weight'] ] = $task;
					}
					ksort($tordered);
					foreach ( $tordered as $tkey => $task ) { 
						?>	
						<div class="dd-task" id="task-<?php echo esc_attr( $task['id'] ); ?>">
							<input class="task_weight" type="hidden" name="task_weight_<?php echo esc_attr( $task['id'] ); ?>" id="task_weight_<?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $task['weight'] ); ?>" />
							<input class="task_id" type="hidden" name="task_id_<?php echo esc_attr( $task['id'] ); ?>" id="task_id_<?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $task['id'] ); ?>" />
							<input class="task_msid" type="hidden" name="task_msid_<?php echo esc_attr( $task['id'] ); ?>" id="task_msid_<?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>" />
							<span class="ms-title" style="color: #36c6d3;"><?php esc_html_e('Task:', 'projectopia-core'); ?> </span>
							<span class="ms-title" id="task_title_<?php echo esc_attr( $task['id'] ); ?>"><?php echo esc_html( $task['title'] ); ?></span>
							<div class="dd-task-actions">
								<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
									<button class="btn px-3" type="button"
										data-toggle="dropdown" aria-haspopup="true"
										aria-expanded="false">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
											class="img-fluid" />
									</button>
									<div class="dropdown-menu">
										<button data-ms="<?php echo esc_attr( $element['id'] ); ?>"
												value="<?php echo esc_attr( $task['id'] ); ?>"
												class="edit-task dropdown-item d-flex align-items-center"
												type="button">
											<?php esc_html_e('Edit Task', 'projectopia-core'); ?>
										</button>
										<button class="delete_task dropdown-item d-flex align-items-center"
												type="button"
												data-tid="<?php echo esc_attr( $post->ID ); ?>"	
												data-ms="<?php echo esc_attr( $element['id'] ); ?>" 
												value="<?php echo esc_attr( $task['id'] ); ?>">
												<?php esc_html_e('Delete Task', 'projectopia-core'); ?>
										</button>
										<button data-ms="<?php echo esc_attr( $element['id'] ); ?>"
												data-project="<?php echo esc_attr( $post->ID ); ?>" 
												value="<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>"
												class="add_subtask dropdown-item d-flex align-items-center"
												type="button">
											<?php esc_html_e('Add Subtask', 'projectopia-core'); ?>
										</button>
									</div>
								</div>
								<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" value="<?php echo esc_attr( $tkey ); ?>" title="<?php esc_html_e('Reorder Task', 'projectopia-core'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
							</div>
							<div class="dd-task-info">
								<div class="mileStone-addSchedule">
									<div class="d-flex flex-wrap">
  										<?php if ( ! empty( $task['offset'] ) ) { ?>
											<div class="addSchedule">
												<label><?php esc_html_e('Start Date', 'projectopia-core') ?></label>
												<span> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $next_task_date) ); ?> </span>
											</div>
										<?php } ?>
										<?php if ( ! empty( $task['offset'] ) ) { ?>
											<div class="addSchedule">
												<label><?php esc_html_e('Deadline', 'projectopia-core') ?></label>
												<span> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), pto_convert_date_range($next_task_date, $task['offset'],$element['include_weekends'])) ); ?> </span>
											</div>
										<?php } ?>
										<?php if ( ! empty( $task['assignee'] ) ) { ?>
											<div class="addSchedule">
												<label><?php esc_html_e('Assigned To', 'projectopia-core') ?></label>
												<span> <?php echo esc_html( $team_array[ $task['assignee'] ]['name'] ); ?> </span>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php $this_task_end = pto_convert_date_range($next_task_date, $task['offset'],$element['include_weekends']);
							$next_subtask_date = $next_task_date;
							$next_task_date = pto_convert_date_range($next_task_date, $task['offset'],$element['include_weekends']);
							if ( $this_task_end > $this_milestone_end ) { ?>
								<div class="font-red sbold mt-2"><?php esc_html_e('This task deadline is later than the deadline of the parent Milestone. Either adjust the offset of this task or extend the offset of the parent milestone!', 'projectopia-core'); ?></div>
							<?php } ?>
							<div class="clear"></div>	
							<?php
							$subtask_id = isset($task['subtasks']['task_id']) ? $task['subtasks']['task_id'] : 1;
							$subtasks = isset($task['subtasks']['task_arrays']) ? $task['subtasks']['task_arrays'] : array();   
							$ttordered = array();
							foreach ( $subtasks as $subtask ) {
								$ttordered[ $subtask['weight'] ] = $subtask;
							}
							ksort($ttordered);
							if ( ! empty($ttordered) ) {
								ksort($ttordered);
								echo '<div class="dd-subtasks">';
								foreach ( $ttordered as $stkey => $subtask ) { 
									$sweight = isset($subtask['weight']) ? $subtask['weight'] : ''; ?>
									<div class="dd-subtask">
										<input class="task_weight" type="hidden" value="<?php echo esc_attr( $sweight ); ?>" />
										<input class="task_id" type="hidden" value="<?php echo esc_attr( $subtask['id'] ); ?>" />
										<input class="ms_id" type="hidden" value="<?php echo esc_attr( $element['id'] ); ?>" />
										<input class="parent_id" type="hidden" value="<?php echo esc_attr( $task['id'] ); ?>" />
										<span class="ms-title" style="color: #36c6d3;"><?php esc_html_e('Subtask:', 'projectopia-core'); ?> </span> 
 										<span class="ms-title" id="task_title_<?php echo esc_attr( $subtask['id'] ); ?>"><?php echo esc_html( $subtask['title'] ); ?></span>
										<div class="dd-task-actions">
											<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
												<button class="btn px-3" type="button"
													data-toggle="dropdown" aria-haspopup="true"
													aria-expanded="false">
													<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
														class="img-fluid" />
												</button>
												<div class="dropdown-menu">
													<button data-ms="<?php echo esc_attr( $element['id'] ); ?>" 
														data-parent="<?php echo esc_attr( $task['id'] ); ?>" 
														value="<?php echo esc_attr( $subtask['id'] ); ?>"
														class="edit-subtask dropdown-item d-flex align-items-center"
														type="button">
													<?php esc_html_e('Edit Subtask', 'projectopia-core'); ?>
													</button>
													<button class="delete_subtask dropdown-item d-flex align-items-center"
														type="button"
														data-tid="<?php echo esc_attr( $post->ID ); ?>" 
														data-ms="<?php echo esc_attr( $element['id'] ); ?>" 
														data-parent="<?php echo esc_attr( $task['id'] ); ?>" 
														value="<?php echo esc_attr( $subtask['id'] ); ?>">
														<?php esc_html_e('Delete Subtask', 'projectopia-core'); ?>
													</button>
												</div>
											</div>
											<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" value="<?php echo esc_attr( $stkey ); ?>" title="<?php esc_html_e('Reorder Subtask', 'projectopia-core'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
										</div>
										<div class="dd-task-info">
											<div class="mileStone-addSchedule">
												<div class="d-flex flex-wrap">
													<?php if ( ! empty( $subtask['offset'] ) ) { ?>
														<div class="addSchedule">
															<label><?php esc_html_e('Start Date', 'projectopia-core') ?></label>
															<span> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $next_subtask_date) ); ?> </span>
														</div>
													<?php } ?>
													<?php if ( ! empty( $subtask['offset'] ) ) { ?>
														<div class="addSchedule">
															<label><?php esc_html_e('Deadline', 'projectopia-core') ?></label>
															<span> <?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ), pto_convert_date_range( $next_subtask_date, $subtask['offset'],$element['include_weekends'] ) ) ); ?> </span>
														</div>
													<?php } ?>
													<?php if ( ! empty( $subtask['assignee'] ) ) { ?>
														<div class="addSchedule">
															<label><?php esc_html_e('Assigned To', 'projectopia-core') ?></label>
															<span> <?php echo esc_html( $team_array[ $subtask['assignee'] ]['name'] ); ?> </span>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
										<?php $this_subtask_end = pto_convert_date_range($next_subtask_date, $subtask['offset'],$element['include_weekends']);
										$next_subtask_date = pto_convert_date_range($next_subtask_date, $subtask['offset'],$element['include_weekends']);
										if ( $this_subtask_end > $this_milestone_end ) { ?>
											<div class="font-red sbold mt-2"><?php esc_html_e('This task deadline is later than the deadline of the parent Milestone. Either adjust the offset of this task or extend the offset of the parent milestone!', 'projectopia-core'); ?></div>
										<?php }
										if ( $this_subtask_end > $this_task_end ) { ?>
											<div class="font-red sbold mt-2"><?php esc_html_e('This task deadline is later than the deadline of the parent task. Either adjust the offset of this subtask or extend the offset of the parent task!', 'projectopia-core'); ?></div>
										<?php } ?>
										<div class="clear"></div>	
									</div>
									<div id="edit-subtask-div-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ) ?><?php echo esc_attr( $subtask['id'] ); ?>-container" style="display:none">
										<div id="edit-subtask-div-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ) ?><?php echo esc_attr( $subtask['id'] ); ?>" class="edit-task-div">
											<div style="padding: 12px;">
												<h3><?php esc_html_e( 'Edit Subtask', 'projectopia-core' ); ?></h3>
												<input type="hidden" id="task_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?><?php echo esc_attr( $subtask['id'] ); ?>" name="task_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?><?php echo esc_attr( $subtask['id'] ); ?>" value="<?php echo esc_attr( $subtask['id'] ); ?>" />
												<input type="hidden" id="task_ms_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?><?php echo esc_attr( $subtask['id'] ); ?>" name="task_ms_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?><?php echo esc_attr( $subtask['id'] ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>" />
												<?php

												pto_generate_fields( array(
													'id' => 'task_title_' . $element['id'] . $task['id'] . $subtask['id'],
													'label' => __( 'Title:', 'projectopia-core' ),
													'value' => $subtask['title'],
												) );

												pto_generate_fields( array(
													'type' => 'textarea',
													'id'   => 'task_description_' . $element['id'] . $task['id'] . $subtask['id'],
													'label' => __( 'Description:', 'projectopia-core' ),
													'value' => $subtask['description'],
												) );

												pto_generate_fields( array(
													'type' => 'select',
													'id'   => 'task_offset_' . $element['id'] . $task['id'] . $subtask['id'],
													'class' => 'full-width',
													'label' => __( 'Deadline Offset:', 'projectopia-core' ),
													'value' => $subtask['offset'],
													'options' => $offset_options,
													'default' => __( 'Choose...', 'projectopia-core' ),
													'tooltip' => __( 'When you apply this template to a project, the start date will be set to the same as the deadline of the preceeding task. If this is the first subtask under the parent task then it will be set to the start date of the parent task. You can set a day offset for the deadline of this subtask. For example, if the start date is 01/01/2018, a day offset of 3 days would set the deadline to 04/01/2018', 'projectopia-core' ),
												) );

												pto_generate_fields( array(
													'type' => 'select',
													'id'   => 'sub_task_assignee_' . $element['id'] . $task['id'] . $subtask['id'],
													'class' => 'full-width',
													'label' => __( 'Select Team Member:', 'projectopia-core' ),
													'value' => $subtask['assignee'],
													'options' => $team_options,
													'default' => __( 'Choose...', 'projectopia-core' ),
												) );

												?>
												<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
												<button class="update-subtask mt-10 piaBtn right" data-ms="<?php echo esc_attr( $element['id'] ); ?>" data-parent="<?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $subtask['id'] ); ?>"><?php esc_html_e('Save', 'projectopia-core'); ?></button>
												<div class="clear"></div>
												<div id="subtask-messages-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?><?php echo esc_attr( $subtask['id'] ); ?>"></div>
											</div>
										</div>	
									</div>
								<?php }
								echo '</div>';
							} ?>
							<div id="add-subtask-div-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>-container" style="display:none">
								<div id="add-subtask-div-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" class="add-task-div">
									<div style="padding: 12px;">
										<h3><?php esc_html_e( 'Add Subtask', 'projectopia-core' ); ?></h3>
										<input type="hidden" id="sub_task_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="sub_task_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $subtask_id ); ?>" />
										<input type="hidden" id="sub_task_weight_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="sub_task_weight_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $subtask_id ); ?>" />
										<input type="hidden" id="sub_task_milestone_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="sub_task_milestone_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>" />
										<input type="hidden" id="sub_task_project_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="sub_task_project_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $post->ID ); ?>" />
										<input id="sub_task_parent_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="sub_task_parent_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" type="hidden" value="<?php echo esc_attr( $task['id'] ); ?>" />
										<?php

										pto_generate_fields( array(
											'id'          => 'sub_task_title_' . $element['id'] . $task['id'],
											'label'       => __( 'Title:', 'projectopia-core' ),
											'placeholder' => __( 'Task title', 'projectopia-core' ),
										) );

										pto_generate_fields( array(
											'type'        => 'textarea',
											'id'          => 'sub_task_description_' . $element['id'] . $task['id'],
											'label'       => __( 'Description:', 'projectopia-core' ),
 											'placeholder' => __( 'Task description', 'projectopia-core' ),
										) );
											
										pto_generate_fields( array(
											'type'    => 'select',
											'id'      => 'sub_task_offset_' . $element['id'] . $task['id'],
											'class'   => 'full-width',
											'label'   => __( 'Deadline Offset:', 'projectopia-core' ),
											'options' => $offset_options,
											'default' => __( 'Choose...', 'projectopia-core' ),
											'tooltip' => __( 'When you apply this template to a project, the start date will be set to the same as the deadline of the preceeding task. If this is the first subtask under the parent task then it will be set to the start date of the parent task. You can set a day offset for the deadline of this subtask. For example, if the start date is 01/01/2018, a day offset of 3 days would set the deadline to 04/01/2018', 'projectopia-core' ),
										) );

										pto_generate_fields( array(
											'type'    => 'select',
											'id'      => 'sub_task_assignee_' . $element['id'] . $task['id'],
											'class'   => 'full-width',
											'label'   => __( 'Select Team Member:', 'projectopia-core' ),
											'options' => $team_options,
											'default' => __( 'Choose...', 'projectopia-core' ),
										) );

										?>
										<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
										<button class="save-subtask mt-10 piaBtn right" value="<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>"><?php esc_html_e('Add Subtask', 'projectopia-core'); ?></button>
										<div class="clear"></div>
										<div id="subtask-messages-<?php echo esc_attr( $task['id'] ); ?>"></div>
									</div>
								</div>	
							</div>
						</div>
						<div id="edit-task-div-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ) ?>-container" style="display:none">
							<div id="edit-task-div-<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ) ?>" class="edit-task-div">
								<div style="padding: 12px;">
									<h3><?php esc_html_e( 'Edit Task', 'projectopia-core' ); ?></h3>
									<span class="label"><strong><?php esc_html_e('Title:', 'projectopia-core'); ?></strong></span>
									<input type="hidden" id="task_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="task_id_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $task['id'] ); ?>" />
									<input type="hidden" id="task_ms_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" name="task_ms_<?php echo esc_attr( $element['id'] ); ?><?php echo esc_attr( $task['id'] ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>" />
									<?php

									pto_generate_fields( array(
										'id'    => 'task_title_' . $element['id'] . $task['id'],
										'label' => __( 'Title:', 'projectopia-core' ),
										'value' => $task['title'],
									) );

									pto_generate_fields( array(
										'type'  => 'textarea',
										'id'    => 'task_description_' . $element['id'] . $task['id'],
										'label' => __( 'Description:', 'projectopia-core' ),
										'value' => $task['description'],
									) );

									pto_generate_fields( array(
										'type'    => 'select',
										'id'      => 'task_offset_' . $element['id'] . $task['id'],
										'class'   => 'full-width',
										'label'   => __( 'Deadline Offset:', 'projectopia-core' ),
										'value'   => $task['offset'],
										'options' => $offset_options,
										'default' => __( 'Choose...', 'projectopia-core' ),
										'tooltip' => __( 'When you apply this template to a project, the start date will be set to the same as the deadline of the preceeding task. If this is the first subtask under the parent task then it will be set to the start date of the parent task. You can set a day offset for the deadline of this subtask. For example, if the start date is 01/01/2018, a day offset of 3 days would set the deadline to 04/01/2018', 'projectopia-core' ),
									) );

									pto_generate_fields( array(
										'type'    => 'select',
										'id'      => 'task_assignee_' . $element['id'] . $task['id'],
										'class'   => 'full-width',
										'label'   => __( 'Select Team Member:', 'projectopia-core' ),
										'value'   => $task['assignee'],
										'options' => $team_options,
										'default' => __( 'Choose...', 'projectopia-core' ),
									) );

									?>
									<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
									<button class="update-task mt-10 piaBtn right" data-ms="<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $task['id'] ); ?>"><?php esc_html_e('Save', 'projectopia-core'); ?></button>
									<div class="clear"></div>
									<div id="task-messages-<?php echo esc_attr( $key ); ?><?php echo esc_attr( $task['id'] ); ?>"></div>
								</div>
							</div>	
						</div>
					<?php } 
					?>
				</div>
			</div>
			<div id="delete-milestone-div-<?php echo esc_attr( $element['id'] ); ?>-container" style="display:none">
				<div id="delete-milestone-div-<?php echo esc_attr( $element['id'] ); ?>" class="delete-milestone-div">
					<div style="padding: 12px;">
						<h3><?php esc_html_e( 'Are you sure?', 'projectopia-core' ); ?></h3>
						<p><?php esc_html_e('Deleting this milestone will also delete related tasks. Are you sure you want to do this?', 'projectopia-core'); ?></p>
						<button class="cancel_delete_stage mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button> <button class="delete_stage mt-10 piaBtn right" data-id="<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>"><?php esc_html_e('Delete', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>
			<div id="add-task-div-<?php echo esc_attr( $element['id'] ); ?>-container" style="display:none">
				<div id="add-task-div-<?php echo esc_attr( $element['id'] ); ?>" class="add-task-div">
					<div style="padding: 12px">
						<h3><?php esc_html_e('Add Task', 'projectopia-core'); ?></h3>
						<input type="hidden" id="task_id_<?php echo esc_attr( $element['id'] ); ?>" name="task_id_<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $task_id ); ?>" />
						<input type="hidden" id="task_weight_<?php echo esc_attr( $element['id'] ); ?>" name="task_weight_<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $task_id ); ?>" />
						<input type="hidden" id="task_milestone_id_<?php echo esc_attr( $element['id'] ); ?>" name="task_milestone_id_<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>" />
						<input type="hidden" id="task_project_id_<?php echo esc_attr( $element['id'] ); ?>" name="task_project_id_<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $post->ID ); ?>" />
						<?php

						pto_generate_fields( array(
							'id'          => 'task_title_' . $element['id'],
							'label'       => __( 'Title:', 'projectopia-core' ),
							'placeholder' => __( 'Task title', 'projectopia-core' ),
						) );

						pto_generate_fields( array(
							'type'        => 'textarea',
							'id'          => 'task_description_' . $element['id'],
							'label'       => __( 'Description:', 'projectopia-core' ),
							'placeholder' => __( 'Task description', 'projectopia-core' ),
						) );
							
						pto_generate_fields( array(
							'type'    => 'select',
							'id'      => 'task_offset_' . $element['id'],
							'class'   => 'full-width',
							'label'   => __( 'Deadline Offset:', 'projectopia-core' ),
							'options' => $offset_options,
							'default' => __( 'Choose...', 'projectopia-core' ),
							'tooltip' => __( 'When you apply this template to a project, the start date will be set to the same as the deadline of the preceeding task. If this is the first subtask under the parent task then it will be set to the start date of the parent task. You can set a day offset for the deadline of this subtask. For example, if the start date is 01/01/2018, a day offset of 3 days would set the deadline to 04/01/2018', 'projectopia-core' ),
						) );
							
						pto_generate_fields( array(
							'type'    => 'select',
							'id'      => 'task_assignee_' . $element['id'],
							'class'   => 'full-width',
							'label'   => __( 'Select Team Member:', 'projectopia-core' ),
							'options' => $team_options,
							'default' => __( 'Choose...', 'projectopia-core' ),
						) );

						?>
						<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button class="save-task mt-10 piaBtn right" value="<?php echo esc_attr( $element['id'] ); ?>"><?php esc_html_e('Add Task', 'projectopia-core'); ?></button>
						<div class="clear"></div>
						<div id="task-messages-<?php echo esc_attr( $key ); ?>"></div>
					</div>
				</div>	
			</div>
			<div id="edit-milestone-<?php echo esc_attr( $element['id'] ); ?>-container" style="display:none">
				<div id="edit-milestone-<?php echo esc_attr( $element['id'] ); ?>" class="edit_milestone <?php echo esc_attr( $element['id'] ); ?>">
					<div style="padding:12px">
						<h3><?php esc_html_e('Edit Milestone', 'projectopia-core'); ?></h3>
						<input type="hidden" name="added_element_milestone_id[<?php echo esc_attr( $element['id'] ); ?>]" id="added_element_milestone_id[<?php echo esc_attr( $element['id'] ); ?>]" value="<?php echo esc_attr( $element['id'] ); ?>" />
						<?php

						pto_generate_fields( array(
							'id'    => 'added_element_title[' . $element['id'] . ']',
							'label' => __( 'Title:', 'projectopia-core' ),
							'value' => esc_html( $element['title'] ),
						) );

						pto_generate_fields( array(
							'id'    => 'added_element_cost[' . $element['id'] . ']',
							'label' => __( 'Estimated Cost:', 'projectopia-core' ),
							'value' => $element['cost'],
						) );
							
						pto_generate_fields( array(
							'type'    => 'select',
							'id'      => 'added_element_offset[' . $element['id'] . ']',
							'class'   => 'full-width',
							'label'   => __( 'Deadline Offset:', 'projectopia-core' ),
							'options' => $offset_options,
							'default' => __( 'Choose...', 'projectopia-core' ),
							'value'   => $element['offset'],
							'tooltip' => __( 'When you apply this template to a project, the start date will be set to the same as the deadline of the preceeding milestone. If this is the first milestone in the template then it will be set to the start date of the project. You can set a day offset for the deadline of this milestone. For example, if the start date is 01/01/2018, a day offset of 3 days would set the deadline to 04/01/2018', 'projectopia-core' ),
						) );

						$inc_weekend = isset( $element['include_weekends'] ) ? $element['include_weekends'] : 0;
						pto_generate_fields( array(
							'type'    => 'checkbox',
							'id'      => 'chk_added_weekends[' . $element['id'] . ']',
							'label'   => __( 'Include Weekends', 'projectopia-core' ),
							'checked' => 1 == $inc_weekend,
						) );
							
						?>
						<div id="update-ms-message-<?php echo esc_attr( $element['id'] ); ?>"></div>
						<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button class="save-milestone mt-10 piaBtn right" value="<?php echo esc_attr( $element['id'] ); ?>"><?php esc_html_e('Save', 'projectopia-core'); ?></button>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		<?php
		}
		echo '</div>';      
	}
	?>
	<?php echo '<input type="submit" class="mt-10 piaBtn btn btn-primary ml-2 caribbeanGreen right" value="' . esc_html__('Update Milestone Template', 'projectopia-core') . '"/>'; ?>
	<a href="#add-milestone-div" id="add-milestone" class="mt-10 piaBtn right colorbox"><?php esc_html_e('Add Milestone', 'projectopia-core'); ?></a>
	<a href="#clear-all-div" id="clear-all" class="mt-10 piaBtn btn btn-primary redColor mx-2 right colorbox"><?php esc_html_e('Clear All', 'projectopia-core'); ?></a>
	<div class="clear"></div>
	<div id="add-milestone-div-container" style="display:none">
		<div id="add-milestone-div">
			<div style="padding: 12px;">
				<h3><?php esc_html_e('Add Milestone', 'projectopia-core'); ?></h3>
				<input type="hidden" id="add_milestone_id" name="add_milestone_id" value="<?php echo esc_attr( $milestone_key ); ?>" />
				<input type="hidden" id="add_milestone_order" name="add_milestone_order" value="<?php echo esc_attr( $milestone_key ); ?>" />
				<?php

				pto_generate_fields( array(
					'id'    => 'quote_element_title',
					'label' => __( 'Milestone title, eg. \'Design Phase\':', 'projectopia-core' ),
				) );

				pto_generate_fields( array(
					'id'    => 'quote_element_cost',
					'label' => __( 'Estimated Cost:', 'projectopia-core' ),
				) );
					
				pto_generate_fields( array(
					'type'    => 'select',
					'id'      => 'add_milestone_range',
					'class'   => 'full-width',
					'label'   => __( 'Deadline Offset:', 'projectopia-core' ),
					'options' => $offset_options,
					'default' => __( 'Choose...', 'projectopia-core' ),
					'tooltip' => __( 'When you apply this template to a project, the start date will be set to the same as the deadline of the preceeding milestone. If this is the first milestone in the template then it will be set to the start date of the project. You can set a day offset for the deadline of this milestone. For example, if the start date is 01/01/2018, a day offset of 3 days would set the deadline to 04/01/2018', 'projectopia-core' ),
				) );

				pto_generate_fields( array(
					'type'      => 'checkbox',
					'id'        => 'chk_weekends',
					'label'     => __( 'Include Weekends', 'projectopia-core' ),
					'class'     => 'chk-weekends',
					'checked'   => true,
					'attribute' => 'class="chk-weekends"',
				) );
					
				?>
				<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
				<button id="add_quote_element" class="mt-10 piaBtn right"><?php esc_html_e('Add Milestone Template', 'projectopia-core'); ?></button>
			</div>
		</div>	
	</div>
	<div id="clear-all-div-container" style="display:none">
		<div id="clear-all-div">
			<div style="padding:12px">
				<h3><?php esc_html_e('Clear All', 'projectopia-core'); ?></h3>
				<p><?php esc_html_e('Are you sure you want to clear all Milestones and Tasks? This cannot be undone.', 'projectopia-core'); ?></p>
				<button class="cancel-colorbox mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
				<button id="clear-all-action" class="mt-10 piaBtn right" value="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('Clear All', 'projectopia-core'); ?></button>
			</div>
		</div>	
	</div>
	<div id="set-title-div-container" style="display:none">
		<div id="set-title-div">
			<div style="padding:12px">
				<h3><?php esc_html_e('Template Title', 'projectopia-core'); ?></h3>
				<?php
				pto_generate_fields( array(
					'id'    => 'set-title',
					'label' => __( 'Please set a title for this Milestone Template:', 'projectopia-core' ),
				) );
				?>
				<button id="set-title-action" class="mb-2 piaBtn op right" value="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('Set Title', 'projectopia-core'); ?></button>
			</div>
		</div>	
	</div>
	<?php
}

add_action( 'save_post_cqpim_templates', 'save_pto_templates_metabox_data' );
function save_pto_templates_metabox_data( $post_id ) {
	if ( ! isset( $_POST['templates_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['templates_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'templates_metabox' ) ) {
	    return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( ! empty( $_POST['test_date'] ) ) {
		$test_date = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['test_date'] ) ) );
		update_post_meta( $post_id, 'test_date', $test_date );
	} else {
		update_post_meta( $post_id, 'test_date', '' );
	}
	
	if ( isset( $_POST['added_element_title'] ) ) {
		$title_array = array_map( 'sanitize_text_field', wp_unslash( $_POST['added_element_title'] ) );
		$quote_elements = get_post_meta( $post_id, 'project_template', true );
		foreach ( $title_array as $key => $title ) {
			$milestone_id = isset( $_POST['added_element_milestone_id'][ $key ] ) ? sanitize_text_field( wp_unslash( $_POST['added_element_milestone_id'][ $key ] ) ) : '';
			$milestone_offset = isset( $_POST['added_element_offset'][ $key ] ) ? sanitize_text_field( wp_unslash( $_POST['added_element_offset'][ $key ] ) ) : '';   
			$milestone_weight = isset( $_POST['element_weight'][ $key ] ) ? sanitize_text_field( wp_unslash( $_POST['element_weight'][ $key ] ) ) : '';
			$include_weekends = isset( $_POST['chk_added_weekends'][ $key ] ) ? sanitize_text_field( wp_unslash( $_POST['chk_added_weekends'][ $key ] ) ) : 0;
			$cost = isset( $_POST['added_element_cost'][ $key ] ) ? sanitize_text_field( wp_unslash( $_POST['added_element_cost'][ $key ] ) ) : '';
			if ( isset( $quote_elements['milestones'][ $key ] ) ) {
				$quote_elements['milestones'][ $key ] = array(
					'title'            => $title,
					'offset'           => $milestone_offset,
					'id'               => $milestone_id,
					'include_weekends' => $include_weekends,
					'cost'             => $cost,
					'tasks'            => $quote_elements['milestones'][ $key ]['tasks'],
					'weight'           => $milestone_weight,
				);
			}
		}
		update_post_meta( $post_id, 'project_template', $quote_elements );
	}

	if ( isset( $_POST['delete_stage'] ) ) {
		$stages_to_delete = array_map( 'sanitize_text_field', wp_unslash( $_POST['delete_stage'] ) );
		$quote_elements = get_post_meta( $post_id, 'project_template', true );
		foreach ( $stages_to_delete as $key => $delete ) {
			if ( isset( $quote_elements['milestones'][ $delete ] ) ) {
				unset( $quote_elements['milestones'][ $delete ] );
			}
		}
		if ( ! empty( $quote_elements['milestones'] ) ) {
			update_post_meta( $post_id, 'project_template', $quote_elements );
		} else {
			delete_post_meta( $post_id, 'project_template' );
		}
	}

	if ( ! empty( $_POST['set-title'] ) ) {
		$quote_updated = array(
			'ID'         => $post_id,
			'post_title' => wp_kses_post( wp_unslash( $_POST['set-title'] ) ),
			'post_name'  => $post_id,
		);

		if ( ! wp_is_post_revision( $post_id ) ) {
			remove_action( 'save_post_cqpim_templates', 'save_pto_templates_metabox_data' );
			wp_update_post( $quote_updated );
			add_action( 'save_post_cqpim_templates', 'save_pto_templates_metabox_data' );
		}
		update_post_meta( $post_id, 'title_set', 1 );
	}
}