<?php 
/**
 * My Task Page
 *
 * This is my task page showing list of tasks for team member.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

//Register my tasks as sub menu page.
add_action( 'admin_menu' , 'register_pto_task_page', 29 ); 
function register_pto_task_page() {
	$my_tasks_page = add_submenu_page(  
		'pto-dashboard',
		__( 'My Tasks', 'projectopia-core' ),      
		'<span class="pto-sm-hidden">' . __( 'My Tasks', 'projectopia-core' ) . '</span>',          
		'edit_cqpim_projects',          
		'pto-tasks',        
		'pto_tasks'
	);

	add_action( 'load-' . $my_tasks_page, 'pto_enqueue_plugin_option_scripts' );
}

/**
 * Function to show my tasks table with data.
 */
function pto_tasks() {

	$task_status = pto_get_transient('task_status');
	$sess_status = ! empty( $task_status ) ? $task_status : array( 'pending', 'progress' );
?>

	<!-- Markup for dashboardWrapper -->
	<div class="dashboardWrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card all-project-card">
						<div class="card-header d-block d-md-flex">
							<div class="card-header-info d-flex align-items-center">
								<img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
								<h5 class="mb-0"><?php esc_html_e('My Tasks', 'projectopia-core'); ?> </h5>
							</div>
							<div class="card-header-btn mt-2 mt-md-0">
								<div class="selectDropdown d-block d-sm-flex flex-wrap align-items-center">
									<div class="dropdownInner padding-ls-medium pr-3 mt-2" data-prev-content="Show">
										<select id="task_status_filter" class="selectDropdown-init form-control">
											<option value="" <?php if ( in_array( 'pending', $sess_status ) && in_array( 'progress', $sess_status ) ) { echo 'selected="selected"'; } ?>><?php esc_html_e( 'Pending & In Progress', 'projectopia-core' ); ?></option>
											<?php $pto_task_status = pto_get_task_status_kv();
											foreach ( $pto_task_status as $key => $value ) {
												$selected = false;
												if ( count( $sess_status ) == 1 && in_array( $key, $sess_status ) ) { 
													$selected = true;
												}
												echo '<option value="' . esc_attr( $key ) . '" ' . selected( $selected, true, false ) . '>' . esc_html( ucwords( $value ) ) . '</option>';
											}

											/**
											 * Add new task status for filter task list
											 */
											do_action( 'pto_task_status_filter', $sess_status );
											?>
											<option value="all" <?php if ( count( $sess_status ) > 2 ) { echo 'selected="selected"'; } ?>><?php esc_html_e( 'Show All', 'projectopia-core' ); ?></option>
										</select>
									</div>
									<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cqpim_tasks' ) ); ?>"
										class="piaBtn mt-2">
										<?php esc_html_e( 'Add Task', 'projectopia-core' ); ?>
									</a>
								</div>
							</div>
						</div>

						<div class="card-body">
							<?php

								$user = wp_get_current_user();
								$members = get_posts( [
									'post_type'      => 'cqpim_teams',
									'posts_per_page' => -1,
									'post_status'    => 'private',
								] );

								$assigned = '';
								foreach ( $members as $member ) {
									$team_details = get_post_meta( $member->ID, 'team_details', true);
									if ( $team_details['user_id'] == $user->ID ) {
										$assigned = $member->ID;
									}
								}

								$args = array(
									'post_type'      => 'cqpim_tasks',
									'posts_per_page' => -1,
								);

								$tasks = get_posts($args);
								$own_tasks = get_posts($args);
								$ordered = array();
								foreach ( $own_tasks as $task ) {
									$task_details = get_post_meta($task->ID, 'task_details', true);
									$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
									if ( ! empty($task_deadline) ) {
										$ordered[ $task_deadline ] = $task;
									}
								}
								ksort($ordered);
								foreach ( $own_tasks as $task ) {
									$task_details = get_post_meta($task->ID, 'task_details', true);
									$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
									if ( empty($task_deadline) ) {
										$ordered[] = $task;
									}
								}

								if ( ! empty( $tasks ) ) { ?>
									<table role="grid" class="piaTableData table-responsive-lg table table-bordered w-100 dataTable no-footer" id="pto-my-work-page-table">
										<thead>
											<tr role="row">
												<th><?php esc_html_e('Task Title', 'projectopia-core'); ?></th>
												<th><?php esc_html_e('Project', 'projectopia-core'); ?></th>
												<th><?php esc_html_e('Assigned', 'projectopia-core'); ?></th>
												<th><?php esc_html_e('Deadline', 'projectopia-core'); ?></th>
												<?php if ( pto_has_addon_active_license( 'pto_te', 'timeentries' ) ) { ?>
													<th><?php esc_html_e('Time Spent', 'projectopia-core'); ?></th>
													<th><?php esc_html_e('Add Time', 'projectopia-core'); ?></th>
												<?php } ?>
												<th><?php esc_html_e('Progress', 'projectopia-core'); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $ordered as $task ) { 
												$task_details = get_post_meta($task->ID, 'task_details', true); 
												$task_owner = get_post_meta($task->ID, 'owner', true);
												$owner = get_post_meta($task->ID, 'owner', true);
												$client_check = preg_replace('/[0-9]+/', '', $task_owner);
												if ( $client_check == 'C' ) {
													$client = true;
												} else {
													$client = false;
												}
												if ( $task_owner ) {
													if ( $client == true ) {
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
												$watchers = get_post_meta($task->ID, 'task_watchers', true); 
												if ( empty($watchers) ) {
													$watchers = array();
												}

												$watching = '';
												if ( in_array($assigned, $watchers) ) {
													$watching = '<img title="' . esc_attr__('Watched Task', 'projectopia-core') . '" src="' . PTO_PLUGIN_URL . '/img/watching.png" />';
												}

												$team_details = get_post_meta($owner, 'team_details', true);
												$project = get_post_meta($task->ID, 'project_id', true); 
												$active = get_post_meta($task->ID, 'active', true);
												$project_details = get_post_meta($project, 'project_details', true);
												$project_object = get_post($project);
												$project_ref = isset($project_object->post_title) ? $project_object->post_title : '';
												$project_url = get_edit_post_link($project);
												$task_status = isset($task_details['status']) ? $task_details['status'] : '';
												$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
												$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
												if ( ! is_numeric($task_deadline) ) {
													$str_deadline = str_replace('/','-', $task_deadline);
													$deadline_stamp = strtotime($str_deadline);
												} else {
													$deadline_stamp = $task_deadline;
												}
												$time_spent = get_post_meta($task->ID, 'task_time_spent', true);
												$total = (int) 0;
												if ( $time_spent ) {
													foreach ( $time_spent as $key => $time ) {
														$total = $total + $time['time'];
													}
													$total = str_replace(',','.', $total);
													$time_split = explode('.', $total);
													$minutes = 0;

													if ( ! empty( $time_split[1] ) ) {
														$minutes = '0.' . $time_split[1];
													}

													$minutes = $minutes * 60;
													$minutes = number_format( (float)$minutes, 0, '.', '');
													if ( $time_split[0] > 1 ) {
														$hours  = __('hours', 'projectopia-core');
													} else {
														$hours = __('hour', 'projectopia-core');
													}
													$time = '<span><strong>' . number_format( (float)$total, 2, '.', '') . ' ' . __('hours', 'projectopia-core') . '</strong> (' . $time_split[0] . ' ' . $hours . ' + ' . $minutes . ' ' . __('minutes', 'projectopia-core') . ')</span> <div id="ajax_spinner_remove_time_'. esc_attr( $task->ID ) .'" class="ajax_spinner" style="display:none"></div>';
												} else {
													$time = '<span>0</span>';
												}
												/*$now = time();
												if ( $task_status != 'complete' ) {
													if ( $deadline_stamp && $now > $deadline_stamp ) {
														$progress_class = 'red';
														$milestone_status_string = __('OVERDUE', 'projectopia-core');
													} else {
														$milestone_status_string = isset($task_details['status']) ? $task_details['status'] : '';
														if ( ! $milestone_status_string || $milestone_status_string == 'pending' ) {
															$progress_class = 'amber';
															$milestone_status_string = __('Pending', 'projectopia-core');
														} elseif ( $milestone_status_string == 'on_hold' ) {
															$progress_class = 'green';
															$milestone_status_string = __('On Hold', 'projectopia-core');
														} elseif ( $milestone_status_string == 'progress' ) {
															$progress_class = 'green';
															$milestone_status_string = __('In Progress', 'projectopia-core');
														}
													}
												} else {
													$milestone_status_string = __('Complete', 'projectopia-core');
												}
												if ( empty($progress_class) ) {
													$progress_class = 'green';
												}*/

												$status_string = '';
												$color = 'rgb(101, 118, 255)';
												if ( ! empty( $task_status ) ) {
													$status_string = pto_get_task_status_value_by_key( $task_status );
													$color = pto_get_task_status_value_by_key( $task_status, 'color' );
												}

												$now = time();
												if ( $task_status != 'complete' ) {
													if ( $deadline_stamp && $now > $deadline_stamp ) {
														$status_string = __( 'OVERDUE', 'projectopia-core' );
														$color = '#ff0000';
													}
												}

												/**
												 * Filter Task Status Display
												 */
												$milestone_status_string = apply_filters( 'pto_task_status_string', $status_string, $task_details['status'], $task_details );
												$milestone_status_string = ! empty( $milestone_status_string ) ? $milestone_status_string . ' - ' . $task_pc : $milestone_status_string;
												
												if ( ! is_array( $watchers ) ) {
													$watchers = array( $watchers );
												}
												if ( ! empty( $task->post_parent ) ) {
													$parent_object = get_post( $task->post_parent );
												}
												if ( ! empty( $active ) && in_array( $task_status, $sess_status ) && $owner == $assigned || ! empty( $active ) && in_array( $task_status, $sess_status ) && in_array( $assigned, $watchers ) ) {
												?>
												<tr role="row">
													<td>

														<a href="<?php echo esc_url( get_edit_post_link($task->ID) ); ?>">
																<?php echo esc_html( $task->post_title ); ?>
														</a>

														<!-- <p class="m-2"> -->
														<?php

														$task_type = __( 'Task', 'projectopia-core'); 
														if ( ! empty( $task->post_parent) ) {
															$task_type = __('Subtask', 'projectopia-core'); 
														}

														printf( '<span class="mx-2 status normal">%s</span>', esc_html( $task_type ) );

														if ( ! empty( pto_is_task_overdue( $task->ID, 'bg') ) ) {
															printf( '<span class="mx-2 status notSent">%s</span>', esc_html__( 'Overdue', 'projectopia-core' ) );
														}

														if ( ! empty( $task->post_parent ) ) {
															printf(
																'<span class="mx-2 status clientApproval">
																	<a href="%s"> %s </a>
																</span>',
																esc_url( get_edit_post_link($parent_object->ID) ),
																esc_html__( 'Check Parent Task', 'projectopia-core' )
															);
														}

														?>
														<!-- </p> -->

													</td>

													<?php if ( empty($project_ref) ) { ?>
														<td><?php esc_html_e('Ad-Hoc Task', 'projectopia-core'); ?></td>
													<?php } else { 
														$p_type = isset($project_object->post_type) ? $project_object->post_type : ''; ?>
														<td><?php if ( $p_type == 'cqpim_project' ) { esc_html_e('Project: ', 'projectopia-core'); } else { esc_html_e('Ticket: ', 'projectopia-core'); } ?><a href="<?php echo esc_url( $project_url ); ?>"><?php echo esc_html( $project_ref ); ?></td>
													<?php } ?>
													<td><?php echo wp_kses_post( $task_owner ); echo wp_kses_post( $watching ); ?> </td>
													<td data-order="<?php echo esc_html( $task_deadline ); ?>"><?php if ( is_numeric($task_deadline) ) { echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); } else { echo esc_html( $task_deadline ); } ?></td>
													<?php if ( pto_has_addon_active_license( 'pto_te', 'timeentries' ) ) { ?>
														<td><?php echo wp_kses_post( $time ); ?></td>
														<td><button class="add_timer legacy_button border-green font-green cqpim_button cqpim_xs_button op" value="<?php echo esc_attr( $task->ID ); ?>" data-title="<?php echo esc_html( $task->post_title ); ?>"><i class="fa fa-clock-o" aria-hidden="true" title="<?php esc_html_e('Add Time', 'projectopia-core'); ?>"></i></button></td>
													<?php } ?>
													<td>													
														<div class="circleProgressBar">
															<div id="<?php echo 'progress'. esc_attr( $task->ID ) .'-circle'; ?>"
																data-percent="<?php echo esc_attr( $task_pc ); ?>"
																class="extra-small" title="<?php echo esc_attr( $milestone_status_string ); ?>%" data-progressBarColor="<?php echo esc_attr( $color ); ?>">
															</div>
														</div>
													</td>
												</tr>
											<?php   }
											} ?>
										</tbody>
									</table>
							<?php } else { ?> 
									<div style="padding:5px">	
										<br />
										<h2 style="margin:0"><?php esc_html_e('Nothing Here!', 'projectopia-core'); ?></h2>
										<span><?php esc_html_e('No tasks to show...', 'projectopia-core'); ?></span>						
									</div>				
							<?php } ?>
							</div>
						</div>
				</div>
			</div>
		</div>
	</div>
	<!--/ Markup for dashboardWrapper -->
	<div id="tasks_filter_spinner" style="display:none" class="ajax_spinner"></div>

    <!-- Popup to add the time -->
	<div style="display:none">
		<div id="add-time-div" class="add-time-div">
			<div style="padding:12px;;min-width: 430px;">
				<h3><?php esc_html_e('Add Time', 'projectopia-core'); ?></h3>

				<div class="form-group">
					<div class="input-group">
						<input style="width:250px;border-radius: .375rem;" id="task_time_value" type="text" name="timer" class="form-control timer input" placeholder="<?php esc_attr_e('0 sec', 'projectopia-core'); ?>" />
						<button class="cqpim_button cqpim_small_button border-green font-green start-timer-btn" style="margin: 0 0 0 8px;border-radius: .375rem;"><i class="fa fa-play" aria-hidden="true" title="<?php esc_html_e('Start Timer', 'projectopia-core'); ?>"></i></button>
						<button class="cqpim_button cqpim_small_button border-green font-green resume-timer-btn hidden" style="margin: 0 0 0 8px;border-radius: .375rem;"><i class="fa fa-play" aria-hidden="true" title="<?php esc_html_e('Resume Timer', 'projectopia-core'); ?>"></i></button>
						<button class="cqpim_button cqpim_small_button border-amber font-amber pause-timer-btn hidden" style="margin: 0 0 0 8px;border-radius: .375rem;"><i class="fa fa-pause" aria-hidden="true" title="<?php esc_html_e('Pause Timer', 'projectopia-core'); ?>"></i></button>
						<button class="cqpim_button cqpim_small_button border-red font-red remove-timer-btn hidden" style="margin: 0 0 0 8px;border-radius: .375rem;"><i class="fa fa-trash" aria-hidden="true" title="<?php esc_html_e('Remove Timer', 'projectopia-core'); ?>"></i></button>					
					</div>
				</div>

				<div class="form-group">
					<label for="timer_note">Add notes</label>
					<div class="input-group">
						<textarea rows="3" cols="20" id="timer_note" class="form-control input pto-textarea"></textarea>
					</div>
				</div>

				<input type="hidden" id="task_time_task" value="" />

				<div id="time_messages" class="alert-display"></div>
				<button id="add_time_ajax" class="btn right piaBtn"><?php esc_html_e('Add Time', 'projectopia-core'); ?> <span id="add_time_loader" class="ajax_loader" style="display:none"></span></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>

<?php }