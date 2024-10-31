<?php
function pto_team_calendar_metabox_callback( $post ) { 
	$assigned = $post->ID; ?>

	<div class="actions">
		<?php 
		$filters = pto_get_transient('cal_filters');
		$calendar_filters = ! empty($filters) ? $filters : array( 'projects', 'milestones', 'tasks' ); ?>
		<?php esc_html_e('Show: ', 'projectopia-core'); ?> &nbsp;&nbsp;
		<input type="checkbox" class="calendar_filter" value="projects" <?php if ( in_array('projects', $calendar_filters) ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e('Projects', 'projectopia-core'); ?> &nbsp;&nbsp;&nbsp;
		<input type="checkbox" class="calendar_filter" value="milestones" <?php if ( in_array('milestones', $calendar_filters) ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e('Milestones', 'projectopia-core'); ?> &nbsp;&nbsp;&nbsp;
		<input type="checkbox" class="calendar_filter" value="tasks" <?php if ( in_array('tasks', $calendar_filters) ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e('Tasks', 'projectopia-core'); ?>
	</div>

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
		$project_details = get_post_meta( $project->ID, 'project_details', true );
		$project_contributors = get_post_meta( $project->ID, 'project_contributors', true );
		$project_contributors = $project_contributors && is_array( $project_contributors ) ? $project_contributors : array();
		if ( ! is_array( $project_contributors ) ) {
			$project_contributors = array( $project_contributors );
		}
		$contrib_ids = array();
		foreach ( $project_contributors as $contrib ) {
			$contrib_ids[] = $contrib['team_id'];
		}
		if ( in_array( $assigned, $contrib_ids ) ) {
			$index++; 
			$contract_status = pto_get_contract_status( $project->ID );
			if ( ! empty( $project_details['confirmed'] ) || $contract_status == 2 ) {
				$projects_to_add[] = $project;  
			}
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
	$tasks = get_posts( $args );                  
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
				firstDay: <?php echo esc_html( get_option( 'start_of_week' ) ); ?>,
				buttonText: {
					today: '<?php echo esc_html_e( 'today', 'projectopia-core' ); ?>',
					month: '<?php echo esc_html_e( 'month', 'projectopia-core' ); ?>',
					week: '<?php echo esc_html_e( 'week', 'projectopia-core' ); ?>',
					day: '<?php echo esc_html_e( 'day', 'projectopia-core' ); ?>',
					list: '<?php echo esc_html_e( 'list', 'projectopia-core' ); ?>'
				},
				locale: '<?php echo esc_html( get_bloginfo( 'language' ) ); ?>',
				navLinks: false,
				editable: false,
				events: [
					<?php 
					if ( in_array( 'projects', $calendar_filters ) ) {
						foreach ( $projects_to_add as $project ) {
							$project_details = get_post_meta( $project->ID, 'project_details', true );
							$project_object = get_post( $project->ID );
							$url = get_edit_post_link( $project->ID );
							$url = str_replace( '&amp;', '&', $url );
							$quote_ref = isset( $project_details['quote_ref'] ) ? $project_details['quote_ref'] : '';
							$start_date = isset( $project_details['start_date'] ) ? $project_details['start_date'] : '';
							if ( ! empty( $start_date ) ) {
								$start_date = gmdate( 'Y-m-d', $start_date );
							}
							$finish_date = isset( $project_details['finish_date'] ) ? $project_details['finish_date'] : '';
							if ( ! empty( $finish_date ) ) {
								$finish_date = gmdate( 'Y-m-d', $finish_date );
							}
							if ( is_numeric( $finish_date ) ) {
								$finish_date = $finish_date + 86400;
							}
							if ( ! empty( $start_date ) && ! empty( $finish_date ) ) {
								echo '{';
								echo 'title : "' . esc_html__( 'PROJECT', 'projectopia-core' ) . ': ' . esc_html( $project_object->post_title ) . '",';
								echo 'start : "' . esc_html( $start_date ) . '",';
								echo 'end : "' . esc_html( $finish_date ) . '",';
								echo 'color : "#3B3F51",';
								echo 'url : "' . esc_url_raw( $url ) . '"';
								echo '},';
							}
						} 
					}
					if ( in_array( 'milestones', $calendar_filters ) ) {
						foreach ( $projects_to_add as $project ) {
							$project_elements = get_post_meta( $project->ID, 'project_elements', true );
							$url = get_edit_post_link( $project->ID );
							$url = str_replace( '&amp;', '&', $url );
							if ( empty($project_elements) ) {
								$project_elements = array();
							}
							foreach ( $project_elements as $element ) {
								$project_object = get_post( $project->ID );
								$task_title = isset( $element['title'] ) ? $element['title'] : '';
								$task_start = isset( $element['start'] ) ? $element['start'] : '';
								if ( ! empty( $task_start ) ) {
									$task_start = gmdate( 'Y-m-d', $task_start );
								}
								$task_deadline = isset( $element['deadline'] ) ? $element['deadline'] : '';
								if ( is_numeric( $task_deadline ) ) {
									$task_deadline = $task_deadline + 86400;
								}
								if ( ! empty( $task_deadline ) ) {
									$task_deadline = gmdate( 'Y-m-d', $task_deadline );
								}
								if ( ! empty( $task_start ) && ! empty( $task_deadline ) ) {
									echo '{';
									echo 'title : "' . esc_html__( 'MILESTONE', 'projectopia-core' ) . ': ' . esc_html( $project_object->post_title ) . ' - ' . esc_html( $task_title ) . '",';                                      
									if ( ! empty( $task_start ) ) {
										echo 'start : "' . esc_html( $task_start ) . '",';
									}
									if ( ! empty( $task_deadline ) ) {
										echo 'end : "' . esc_html( $task_deadline ) . '",';
									}
									echo 'color : "#337ab7",';
									echo 'url : "' . esc_url_raw( $url ) . '"';
									echo '},';
								}
							}
						}
					}
					if ( in_array( 'tasks', $calendar_filters ) ) {
						foreach ( $tasks as $task ) {
							$task_object = get_post( $task->ID );
							$url = get_edit_post_link( $task->ID );
							$url = str_replace( '&amp;', '&', $url );
							$task_details = get_post_meta( $task->ID, 'task_details', true );
							$task_start = isset( $task_details['task_start'] ) ? $task_details['task_start'] : '';
							$task_deadline = isset( $task_details['deadline'] ) ? $task_details['deadline'] : '';
							if ( ! empty( $task_start ) ) {
								$task_start = gmdate( 'Y-m-d', $task_start );
							}
							if ( is_numeric( $task_deadline ) ) {
								$task_deadline = $task_deadline + 86400;
							}
							if ( ! empty( $task_deadline ) ) {
								$task_deadline = gmdate( 'Y-m-d', $task_deadline );
							}
							if ( ! empty( $task_start ) && ! empty( $task_deadline ) ) {
								echo '{';
								echo 'title : "' . esc_html__( 'TASK', 'projectopia-core' ) . ': ' . esc_html( $task_object->post_title ) . '",';
								if ( ! empty( $task_start ) ) {
									echo 'start : "' . esc_html( $task_start ) . '",';
								}
								if ( ! empty( $task_deadline ) ) {
									echo 'end : "' . esc_html( $task_deadline ) . '",';
								}
								echo 'color : "#36c6d3",';
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
	<div class="clear"></div>
	<br />
	<div id="calendar_container">
		<div id="calendar">
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
<?php }