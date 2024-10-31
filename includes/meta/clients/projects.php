<?php
function pto_client_projects_metabox_callback( $post ) {
	$args = array(
		'post_type'      => 'cqpim_project',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);              
	$projects = get_posts( $args );
	if ( $projects ) {
		$i = 0;
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_projects_table"  data-sort="[[ 0, \'desc\' ]]" data-rows="5">';
		echo '<thead>';
		echo '<th>' . esc_html__('Project Title', 'projectopia-core') . '</th><th>' . esc_html__('Open Tasks', 'projectopia-core') . '</th><th>' . esc_html__('Progress', 'projectopia-core') . '</th><th>' . esc_html__('Team Members', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th>';
		echo '</thead>';
		echo '<tbody>';
		foreach ( $projects as $project ) {
			$project_contributors = get_post_meta($project->ID, 'project_contributors', true);
			$project_details = get_post_meta($project->ID, 'project_details', true);
			$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
			$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
			$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
			$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
			$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
			$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
			$contract_status = get_post_meta($project->ID, 'contract_status', true);
			if ( $post->ID == $client_id ) {
				$project_edit = get_edit_post_link($project->ID);
				$project_title = get_the_title($project->ID);
				$project_elements = get_post_meta($project->ID, 'project_elements', true);
				if ( ! empty($client_id) ) {
					if ( ! $closed ) {
						if ( ! $signoff ) {
							if ( $contract_status == 1 ) {
								if ( ! $confirmed ) {
									if ( ! $sent ) {
										$status = '<span class="status normal">' . esc_html__('New', 'projectopia-core') . '</span>';
									} else {
										$status = '<span class="status clientApproval">' . esc_html__('Awaiting Contracts', 'projectopia-core') . '</span>';
									}
								} else {
									$status = '<span class="status approved">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
								}
							} else {
								$status = '<span class="status approved">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
							}
						} else {
							$status = '<span class="status off">' . esc_html__('Signed Off', 'projectopia-core') . '</span>';
						}
					} else {
						$status = '<span class="status closed">' . esc_html__('Closed', 'projectopia-core') . '</span>';
					}
				} else {
					if ( ! $project_details['closed'] ) {
						$status = '<div class="status approved">' . esc_html__('In Progress', 'projectopia-core') . '</div>';
					} else {
						$status = '<div class="status closed">' . esc_html__('Closed', 'projectopia-core') . '</div>';
					}
				}
				$task_count = 0;
				$task_total_count = 0;
				$task_complete_count = 0;
				if ( empty($project_elements) ) {
					$project_elements = array();
				}
				foreach ( $project_elements as $element ) {

					if ( empty( $element ) || empty( $element['id'] ) ) {
						continue;
					}

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
						$task_details = get_post_meta($task->ID, 'task_details', true);
						$task_total_count++;
						$task_details = get_post_meta($task->ID, 'task_details', true);
						if ( $task_details['status'] != 'complete' ) {
							$task_count++;
						}
						if ( $task_details['status'] == 'complete' ) {
							$task_complete_count++;
						}
						$pc_per_task = 100 / $task_total_count;
						$pc_complete = $pc_per_task * $task_complete_count;
					}
				}
				if ( empty($pc_complete) ) {
					$pc_complete = 0;
				}
				echo '<tr>';
				echo '<td><a href="' . esc_url( $project_edit ) . '">' . esc_html( $project_title ) . '</a></td>';
				echo '<td>' . esc_html( $task_count ) . '</td>';
				echo '<td>' . number_format( (float)$pc_complete, 2, ".", "") . '%</td>';
				echo '<td>';
				$names = array();
				if ( $project_contributors ) {
					foreach ( $project_contributors as $contributor ) {
						$team_details = get_post_meta($contributor['team_id'], 'team_details', true);
						$team_edit = get_edit_post_link($contributor['team_id']);
						$names[] = '<a href="' . $team_edit . '" target="_blank">' . $team_details['team_name'] . '</a>';
					}
				}
				if ( ! empty( $names ) ) {
					echo wp_kses_post( implode( ', ', $names ) );
				}
				echo '</td>';
				echo '<td>' . wp_kses_post( $status ) . '</td>';
				echo '</tr>';                       
				$i++;
			}
		}
		echo '</tbody>';
		echo '</table></div>';
		if ( $i == 0 ) {
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('This client has not been assigned to any projects.', 'projectopia-core') . '</div>';
		}
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('There are no active projects available', 'projectopia-core') . '</div>';
	}
}