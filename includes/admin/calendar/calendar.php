<?php
/**
 * Calendar Page
 *
 * This is calendar page showing task and events in calendar.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

add_action( 'admin_menu' , 'register_pto_calendar_page', 29 );
function register_pto_calendar_page() {
	$my_page = add_submenu_page(    
		'pto-dashboard',
		__('My Calendar', 'projectopia-core'),             
		'<span class="pto-sm-hidden">' . esc_html__('My Calendar', 'projectopia-core') . '</span>',             
		'edit_cqpim_projects',          
		'pto-calendar',         
		'pto_calendar'
	);

	add_action( 'load-' . $my_page, 'pto_enqueue_plugin_option_scripts' );
}

function pto_calendar() { 

	$user = wp_get_current_user(); 
	$roles = $user->roles;
	$assigned = pto_get_team_from_userid();

	$filters = pto_get_transient('cal_filters');
	$range = pto_get_transient('cal_range');
	$calendar_filters = ! empty($filters) ? $filters : array( 'invoices', 'projects', 'milestones', 'tasks' );
	$calendar_range = ! empty($range) ? $range : 'end';

?>
<div class="dashboardWrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header d-block d-md-flex">
						<div class="card-header-info d-flex align-items-center">
							<span class="mr-2"> <i class="fa fa-calendar font-blue-sharp" aria-hidden="true"></i> </span>
							<h5 class="mb-0"><?php esc_html_e( 'My Calendar', 'projectopia-core'); ?> </h5>
						</div>
						<div class="card-header-btn mt-2 mt-md-0">
							<div class="selectDropdown d-block d-sm-flex flex-wrap align-items-center">

								<div class="dropdownInner padding-ls-medium px-3" >
									<span class="pr-1"> <?php esc_html_e('Show : ', 'projectopia-core'); ?> </span>
									<?php if ( current_user_can('edit_cqpim_invoices') ) { ?>
										<span class="pr-2">
											<input type="checkbox" class="calendar_filter" 
											value="invoices" <?php if ( in_array('invoices', $calendar_filters) ) { echo 'checked="checked"'; } ?> />
											<?php esc_html_e('Invoices', 'projectopia-core'); ?>
										</span>
									<?php } ?>
									<span class="px-2">		
										<input type="checkbox" class="calendar_filter" value="projects" 
											<?php if ( in_array('projects', $calendar_filters) ) { 
												echo 'checked="checked"'; } ?> /> 
											<?php esc_html_e('Projects', 'projectopia-core'); ?>
									</span>

									<span class="px-2">
										<input type="checkbox" class="calendar_filter" value="milestones" 
											<?php if ( in_array('milestones', $calendar_filters) ) { 
												echo 'checked="checked"'; } ?> /> 
												<?php esc_html_e('Milestones', 'projectopia-core'); ?>
									</span>

									<span class="px-2">	
										<input type="checkbox" class="calendar_filter" value="tasks" 
											<?php if ( in_array('tasks', $calendar_filters) ) { 
											echo 'checked="checked"'; } ?> />
											<?php esc_html_e('Tasks', 'projectopia-core'); ?>
									</span>

								</div>

								<div class="dropdownInner padding-ls-medium" data-prev-content="Show">
									<select id="calendar_range" class="selectDropdown-init form-control">
										<option value="end" <?php selected('end', $calendar_range); ?>><?php esc_html_e('Deadlines Only', 'projectopia-core'); ?></option>
										<option value="range" <?php selected('range', $calendar_range); ?>><?php esc_html_e('Entire Date Range', 'projectopia-core'); ?></option>
									</select>
								</div>

							</div>
						</div>
					</div>

					<div class="card-body mt-3">
						<?php	      
							$args = array(
								'post_type'      => 'cqpim_project',
								'posts_per_page' => -1,
								'post_status'    => 'private',
							);
							$projects = get_posts($args);
							$projects_to_add = array();
							$index = 0;
							foreach ( $projects as $project ) {
								$project_details = get_post_meta($project->ID, 'project_details', true);
								if ( current_user_can('cqpim_view_all_projects') ) { $index++; 
									$contract_status = pto_get_contract_status($project->ID);
									if ( ! empty($project_details['confirmed']) || $contract_status == 2 ) {
										$projects_to_add[] = $project;  
									}
								} else {
									$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
									$project_contributors = $project_contributors && is_array($project_contributors) ? $project_contributors : array();
									if ( ! is_array($project_contributors) ) {
										$project_contributors = array( $project_contributors );
									}
									$contrib_ids = array();
									foreach ( $project_contributors as $contrib ) {
											$contrib_ids[] = $contrib['team_id'];
									}
									if ( in_array($assigned, $contrib_ids) ) { $index++; 
										$contract_status = pto_get_contract_status($project->ID);
										if ( ! empty($project_details['confirmed']) || $contract_status == 2 ) {
											$projects_to_add[] = $project;  
										}
									}
								}           
							}
							$args = array(
								'post_type'      => 'cqpim_invoice',
								'posts_per_page' => -1,
								'post_status'    => 'publish',
							);
							$invoices = get_posts($args);
							$this_client = array();
							foreach ( $invoices as $invoice ) {
								$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
								$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
								if ( 1 ) {
									$this_client[] = $invoice;
								}
							}   
							$args = array(
								'post_type'      => 'cqpim_tasks',
								'posts_per_page' => -1,
								'meta_query'     => array(
									'relation' => 'OR',
									array(
										'key'     => 'owner',
										'value'   => $assigned,
										'compare' => '=',
									),
									array(
										'key'     => 'task_watchers',
										'value'   => $assigned,
										'compare' => 'LIKE',
									),
								),
							);              
							$tasks = get_posts($args);              
						?>
						<script>
							document.addEventListener('DOMContentLoaded', function() {
								var calendarEl = document.getElementById('calendar');

								var calendar = new FullCalendar.Calendar(calendarEl, {
									plugins: [ 'interactive', 'dayGrid' ],
									header: {
										left: 'prevYear,prev,next,nextYear',
										center: 'title',
										right: 'dayGridMonth,dayGridWeek,dayGridDay'
									},
									navLinks: false,
									editable: false,
									firstDay: <?php echo esc_html( get_option( 'start_of_week' ) ); ?>,
									buttonText: {
										today: '<?php echo esc_html_e( 'today', 'projectopia-core' ); ?>',
										month: '<?php echo esc_html_e( 'month', 'projectopia-core' ); ?>',
										week: '<?php echo esc_html_e( 'week', 'projectopia-core' ); ?>',
										day: '<?php echo esc_html_e( 'day', 'projectopia-core' ); ?>',
										list: '<?php echo esc_html_e( 'list', 'projectopia-core' ); ?>'
									},
									locale: '<?php echo esc_html( get_bloginfo( 'language' ) ); ?>',
									events: [
									<?php 
									if ( in_array('projects', $calendar_filters) ) {
										foreach ( $projects_to_add as $project ) {
											$project_details = get_post_meta($project->ID, 'project_details', true);
											$project_colours = get_post_meta($project->ID, 'project_colours', true);
											$project_object = get_post($project->ID);
											$url = get_edit_post_link($project->ID);
											$url = str_replace('&amp;', '&', $url);
											$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
											$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
											$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
											if ( ! empty($start_date) ) {
												if ( $calendar_range == 'range' ) {
													$start_date = gmdate('Y-m-d', $start_date);
												} else {
													$start_date = gmdate('Y-m-d', $finish_date);
												}
											}
											if ( ! empty($finish_date) ) {
												$finish_date = gmdate('Y-m-d', $finish_date);
											}
											if ( is_numeric($finish_date) ) {
												$finish_date = $finish_date + 86400;
											}
											if ( ! empty($start_date) && ! empty($finish_date) ) {
												echo '{';
												echo 'title : "' . esc_html__('PROJECT', 'projectopia-core') . ': ' . esc_html($project_object->post_title) . '",';
												echo 'start : "' . esc_html( $start_date ) . '",';
												echo 'end : "' . esc_html( $finish_date ) . '",';
												if ( ! empty($project_colours['project_colour']) ) {
													echo 'color : "' . esc_js( $project_colours['project_colour'] ) . '",';
												} else {
													echo 'color : "#6576ff",';
												}
												echo 'url : "' . esc_url_raw( $url ) . '"';
												echo '},';
											}
										} 
									}
									if ( in_array('milestones', $calendar_filters) ) {
										foreach ( $projects_to_add as $project ) {
											$project_elements = get_post_meta($project->ID, 'project_elements', true);
											$project_colours = get_post_meta($project->ID, 'project_colours', true);
											$url = get_edit_post_link($project->ID);
											$url = str_replace('&amp;', '&', $url);
											if ( empty($project_elements) ) {
												$project_elements = array();
											}
											foreach ( $project_elements as $element ) {
												$project_object = get_post($project->ID);
												$task_title = isset($element['title']) ? $element['title'] : '';
												$task_start = isset($element['start']) ? $element['start'] : '';
												$task_deadline = isset($element['deadline']) ? $element['deadline'] : '';
												if ( ! empty($task_start) ) {
													if ( $calendar_range == 'range' ) {
														$task_start = gmdate('Y-m-d', $task_start);
													} else {
														$task_start = gmdate('Y-m-d', $task_deadline);
													}
												}
												if ( is_numeric($task_deadline) ) {
													$task_deadline = $task_deadline + 86400;
												}
												if ( ! empty($task_deadline) ) {
													$task_deadline = gmdate('Y-m-d', $task_deadline);
												}
												if ( ! empty($task_start) && ! empty($task_deadline) ) {
													echo '{';
													echo 'title : "' . esc_html__('MILESTONE', 'projectopia-core') . ': ' . esc_html($project_object->post_title) . ' - ' . esc_html($task_title) . '",';                                      
													if ( ! empty($task_start) ) {
														echo 'start : "' . esc_js( $task_start ) . '",';
													}
													if ( ! empty($task_deadline) ) {
														echo 'end : "' . esc_js( $task_deadline ) . '",';
													}
													if ( ! empty($project_colours['ms_colour']) ) {
														echo 'color : "' . esc_js( $project_colours['ms_colour'] ) . '",';
													} else {
														echo 'color : "#6576ff",';
													}
													echo 'url : "' . esc_url_raw( $url ) . '"';
													echo '},';
												}
											}
										}
									}
									if ( current_user_can('edit_cqpim_invoices') && in_array('invoices', $calendar_filters) ) {
										foreach ( $this_client as $invoice ) {
											$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
											$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : ''; 
											$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
											$due = gmdate('Y-m-d', $due);
											$url = get_edit_post_link($invoice->ID);
											$url = str_replace('&amp;', '&', $url);
											echo '{';
											echo 'title : "' . esc_html__('INVOICE DUE', 'projectopia-core') . ': ' . esc_html( $invoice_id ) . '",';                              
											echo 'start : "' . esc_js( $due ) . '",';
											echo 'end : "' . esc_js( $due ) . '",';
											echo 'color : "#F1C40F",';
											echo 'url : "' . esc_url_raw( $url ) . '"';
											echo '},';                                  
										}
									}
									if ( in_array('tasks', $calendar_filters) ) {
										foreach ( $tasks as $task ) {
											$task_object = get_post($task->ID);
											$url = get_edit_post_link($task->ID);
											$url = str_replace('&amp;', '&', $url);
											$task_details = get_post_meta($task->ID, 'task_details', true);
											$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
											$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
											$pid = get_post_meta($task->ID, 'project_id', true);
											$project_colours = get_post_meta($pid, 'project_colours', true);
											if ( ! empty($task_start) ) {
												if ( $calendar_range == 'range' ) {
													$task_start = gmdate('Y-m-d', $task_start);
												} else {
													$task_start = gmdate('Y-m-d', $task_deadline);
												}
											}
											if ( is_numeric($task_deadline) ) {
												$task_deadline = $task_deadline + 86400;
											}
											if ( ! empty($task_deadline) ) {
												$task_deadline = gmdate('Y-m-d', $task_deadline);
											}
											if ( ! empty($task_start) && ! empty($task_deadline) ) {
												echo '{';
												echo 'title : "' . esc_html__('TASK', 'projectopia-core') . ': ' . esc_html($task_object->post_title) . '",';
												if ( ! empty($task_start) ) {
													echo 'start : "' . esc_js( $task_start ) . '",';
												}
												if ( ! empty($task_deadline) ) {
													echo 'end : "' . esc_js( $task_deadline ) . '",';
												}
												if ( pto_is_task_overdue($task->ID) == 1 ) {
													echo 'color : "#e7505a",';
												} else {
													if ( ! empty($project_colours['task_colour']) ) {
														echo 'color : "' . esc_js( $project_colours['task_colour'] ) . '",';
													} else {
														echo 'color : "#36c6d3",';
													}
												}
												echo 'url : "' . esc_url_raw( $url ) . '"';
												echo '},';
											}
										}
									}
									?>
									],
								});

								calendar.render();
							});
						</script>

						<div id="calendar_container">
							<div id="calendar">
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php }