<?php
function pto_task_details_metabox_callback( $post ) {
 	wp_nonce_field( 'task_details_metabox', 'task_details_metabox_nonce' );

 	$data = get_option( 'cqpim_custom_fields_task' );
	$pid = get_post_meta( $post->ID, 'project_id', true );
	$mid = get_post_meta( $post->ID, 'milestone_id', true );
	$project_details = get_post_meta( $pid, 'project_details', true );
	$client_id = isset( $project_details['client_id'] ) ? $project_details['client_id'] : '';
	$client_ids = get_post_meta( $client_id, 'client_ids', true );
	$owner = get_post_meta( $post->ID, 'owner', true );
	$task_details = get_post_meta( $post->ID, 'task_details', true );
	$task_watchers = get_post_meta( $post->ID, 'task_watchers', true );
	$task_description = isset( $task_details['task_description'] ) ? $task_details['task_description'] : '';
	$task_status = isset( $task_details['status'] ) ? $task_details['status'] : '';
	$task_priority = isset( $task_details['task_priority'] ) ? $task_details['task_priority'] : '';
	$task_start = isset( $task_details['task_start'] ) ? $task_details['task_start'] : '';
	$task_deadline = isset( $task_details['deadline'] ) ? $task_details['deadline'] : '';
	$task_est_time = isset( $task_details['task_est_time'] ) ? $task_details['task_est_time'] : '';
	$task_pc = isset( $task_details['task_pc'] ) ? $task_details['task_pc'] : '';
	$project_contributors = get_post_meta( $pid, 'project_contributors', true );
	$parent_object = get_post( $pid );
	$parent_type = isset( $parent_object->post_type ) ? $parent_object->post_type : '';
	$user = wp_get_current_user();
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$members = get_posts( $args );
	if ( empty( $task_watchers ) ) {
		$task_watchers = array();
	} ?>
	<div class="form-group">
		<label for="task_description"><?php esc_html_e( 'Description', 'projectopia-core' ); ?></label>
		<div class="input-group">
			<textarea id="task_description" class="form-control input pto-textarea pto-h-200" name="task_description"><?php echo esc_html( $task_description ); ?></textarea>
		</div>
	</div>
	<div class="row">
	    <div class="col-6">
	        <div class="form-group">
				<label for="task_status"><?php esc_html_e( 'Task Status', 'projectopia-core' ); ?></label>
				<div class="input-group">
					<select id="task_status" class="form-control input" name="task_status">
						<?php
						$pto_task_status = pto_get_task_status_kv();
						foreach ( $pto_task_status as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $task_status, false ) . '>' . esc_html( ucwords( $value ) ) . '</option>';
						} 
						
						/**
						 * Add new task status
						 */
						do_action( 'pto_add_task_status', $task_status );
						?>
					</select>
				</div>
	        </div>
		</div>
		<div class="col-6">
		    <div class="form-group">
		    	<label for="task_priority"><?php esc_html_e( 'Task Priority', 'projectopia-core' ); ?></label>
		    	<div class="input-group">
					<select id="task_priority" class="form-control input" name="task_priority">
						<option value="normal" <?php if ( $task_priority == 'normal' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Normal', 'projectopia-core'); ?></option>
						<option value="low" <?php if ( $task_priority == 'low' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Low', 'projectopia-core'); ?></option>
						<option value="high" <?php if ( $task_priority == 'high' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('High', 'projectopia-core'); ?></option>
						<option value="immediate" <?php if ( $task_priority == 'immediate' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Immediate', 'projectopia-core'); ?></option>
					</select>	
				</div>
			</div>
		</div>
	</div>
	<div class="row">
	    <div class="col-6">
	        <div class="form-group">
				<label for="task_start"><?php esc_html_e( 'Start Date', 'projectopia-core' ); ?></label>
				<div class="input-group">
					<input class="form-control input datepicker" type="text" name="task_start" value="<?php if ( is_numeric($task_start) ) { echo esc_attr( wp_date(get_option('cqpim_date_format'), $task_start) ); } else { echo esc_attr( $task_start ); } ?>" />
				</div>
	        </div>
		</div>
		<div class="col-6">
		    <div class="form-group">
		    	<label for="task_deadline"><?php esc_html_e( 'Deadline', 'projectopia-core' ); ?></label>
		    	<div class="input-group">
					<input class="form-control input datepicker" type="text" name="task_deadline" value="<?php if ( is_numeric($task_deadline) ) { echo esc_attr( wp_date(get_option('cqpim_date_format'), $task_deadline) ); } else { echo esc_attr( $task_deadline ); } ?>" />		
				</div>
			</div>
		</div>
	</div>
	<div class="row">
	    <div class="col-6">
	        <div class="form-group">
				<label for="task_est_time"><?php esc_html_e( 'Estimated Time (Hours)', 'projectopia-core' ); ?></label>
				<div class="input-group">
					<input class="form-control input" type="text" name="task_est_time" value="<?php echo esc_attr( $task_est_time ); ?>" />
				</div>
	        </div>
		</div>
		<div class="col-6">
		    <div class="form-group">
		    	<label for="task_pc"><?php esc_html_e( 'Percentage Complete', 'projectopia-core' ); ?></label>
		    	<div class="input-group">
					<select id="task_pc" class="form-control input" name="task_pc">
						<option value="0" <?php if ( $task_pc == '0' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('0%', 'projectopia-core'); ?></option>
						<option value="10" <?php if ( $task_pc == '10' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('10%', 'projectopia-core'); ?></option>
						<option value="20" <?php if ( $task_pc == '20' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('20%', 'projectopia-core'); ?></option>
						<option value="30" <?php if ( $task_pc == '30' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('30%', 'projectopia-core'); ?></option>
						<option value="40" <?php if ( $task_pc == '40' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('40%', 'projectopia-core'); ?></option>
						<option value="50" <?php if ( $task_pc == '50' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('50%', 'projectopia-core'); ?></option>
						<option value="60" <?php if ( $task_pc == '60' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('60%', 'projectopia-core'); ?></option>
						<option value="70" <?php if ( $task_pc == '70' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('70%', 'projectopia-core'); ?></option>
						<option value="80" <?php if ( $task_pc == '80' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('80%', 'projectopia-core'); ?></option>
						<option value="90" <?php if ( $task_pc == '90' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('90%', 'projectopia-core'); ?></option>
						<option value="100" <?php if ( $task_pc == '100' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('100%', 'projectopia-core'); ?></option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<?php 
	
	pto_get_custom_fields( $data, $post );

	if ( ! empty( $pid ) ) { 
		if ( $parent_type == 'cqpim_project' ) { ?>
			<p class="underline"><?php esc_html_e( 'Project / Milestone', 'projectopia-core' ); ?></p>
			<div class="row">
			    <div class="col-6">
			        <div class="form-group">
						<label for="task_project_id"><?php esc_html_e( 'Assigned Project', 'projectopia-core' ); ?></label>
						<div class="input-group">
						<?php                   
							$args = array(
								'post_type'      => 'cqpim_project',
								'posts_per_page' => -1,
								'post_status'    => 'private',
							);
							$projects = get_posts( $args ); ?>
							<select id="task_project_id" class="form-control input" name="task_project_id">
								<?php if ( current_user_can( 'cqpim_view_all_projects' ) ) { ?>
									<?php if ( ! empty( $projects ) ) { ?>
										<option value=""><?php esc_html_e( 'Do not assign to a project (Personal Task)', 'projectopia-core' ); ?></option>
										<?php foreach ( $projects as $project ) { 
											$project_details = get_post_meta( $project->ID, 'project_details', true );
											if ( empty( $project_details['closed'] ) ) { ?>
												<option value="<?php echo esc_attr( $project->ID ); ?>"<?php if ( $pid == $project->ID ) { echo ' selected="selected"'; } ?>><?php echo esc_html( $project->post_title ); ?></option>
											<?php }
										} ?>
									<?php } else { ?>
										<option value=""><?php esc_html_e( 'No projects available', 'projectopia-core' ); ?></option>
									<?php } ?>
								<?php } else { ?>
									<?php if ( ! empty( $projects ) ) { ?>
										<option value=""><?php esc_html_e( 'Do not assign to a project (Personal Task)', 'projectopia-core' ); ?></option>
										<?php 
										foreach ( $members as $member ) {
											$team_details = get_post_meta( $member->ID, 'team_details', true );
											if ( $team_details['user_id'] == $user->ID ) {
												$assigned = $member->ID;
											}
										}

										$project_details = get_post_meta( $project->ID, 'project_details', true );
										$project_contributors = get_post_meta( $project->ID, 'project_contributors', true );
										$project_contrib_ids = array();
										foreach ( $project_contributors as $contrib ) {
											$project_contrib_ids[] = $contrib['team_id'];
										}

										if ( empty( $project_details['closed'] ) ) {
											if ( in_array( $assigned, $project_contrib_ids ) ) {
												echo '<option value="' . esc_attr( $project->ID ) . '" ' . selected( $pid, $project->ID, false ) . '>' . esc_html( $project->post_title ) . '</option>';
										 	}               
										} ?>
									<?php } else { ?>
										<option value=""><?php esc_html_e( 'No projects assigned to you', 'projectopia-core' ); ?></option>
									<?php } ?>
								<?php } ?>
							</select>	
						</div>
			        </div>
				</div>
				<div class="col-6">
				    <div class="form-group">
				    	<label for="task_milestone_id"><?php esc_html_e( 'Assigned Milestone', 'projectopia-core' ); ?></label>
				    	<div class="input-group">
							<select id="task_milestone_id" class="form-control input" name="task_milestone_id">
								<?php
								$milestones = get_post_meta( $pid, 'project_elements', true );
								if ( ! empty( $milestones ) ) {
									foreach ( $milestones as $milestone ) {
										if ( empty( $milestone['id'] ) || empty( $milestone['title'] ) ) {
											continue;
										}
										echo '<option value="' . esc_attr( $milestone['id'] ) . '" ' . selected( $mid, $milestone['id'], false ) . '>' . esc_html( $milestone['title'] ) . '</option>';
									}
								} else { ?>
									<option value=""><?php esc_html_e( 'No milestones available (Choose a project first)', 'projectopia-core' ); ?></option>
								<?php } ?>			
							</select>
						</div>
					</div>
				</div>
			</div>
			<p class="underline"><?php esc_html_e( 'Main Assignee', 'projectopia-core' ); ?></p>
			<div class="form-group">
				<div class="input-group">
					<select id="task_owner" class="form-control input" name="task_owner">
						<option value=""><?php esc_html_e( 'Choose...', 'projectopia-core' ); ?></option>
						<?php $client_details = get_post_meta( $client_id, 'client_details', true );
						$client_contact_name = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
						if ( $client_id != '' && $client_contact_name != '' ) { ?>
						<optgroup label="<?php esc_html_e( 'Client', 'projectopia-core' ); ?>">
							<option value="<?php echo esc_attr( $client_id ); ?>" <?php selected( $owner, $client_id ); ?>>
								<?php echo esc_html( $client_contact_name ); ?>
							</option> 
						</optgroup>
						<?php } ?>
						<optgroup label="<?php esc_html_e( 'Project Team Members', 'projectopia-core' ); ?>">
							<?php if ( empty( $pid ) ) { ?>
								<option value="<?php echo esc_attr( $assigned ); ?>"><?php esc_html_e( 'Me', 'projectopia-core' ); ?></option>
							<?php } else {
								$contribs = get_post_meta( $pid, 'project_contributors', true );
								if ( ! empty( $contribs ) ) {
									foreach ( $contribs as $contrib ) {
										$team_details = get_post_meta( $contrib['team_id'], 'team_details', true );
										echo '<option value="' . esc_attr( $contrib['team_id'] ) . '" ' . selected( $owner, $contrib['team_id'], false ) . '>' . esc_html( $team_details['team_name'] ) . '</option>';
									}
								} else {
									echo '<option value="' . esc_attr( $assigned ) . '">' . esc_html__( 'Me', 'projectopia-core' ) . '</option>';
								}
							} ?>
						</optgroup>
					</select>
				</div>
				<p><?php esc_html_e( 'If you assign a task to a client you should add yourself as a Secondary Assignee to continue to receive updates.', 'projectopia-core' ); ?></p>
			</div>
			<?php
				echo '<p class="underline">' . esc_html__( 'Secondary Assignees', 'projectopia-core' ) . '</p>';
				echo '<p>' . esc_html__( 'People other than the Assignee and Admins who can view/update this task and get notifications', 'projectopia-core' ) . '</p>';
				$project_contrib_ids = array();
				$project_contributors = get_post_meta( $pid, 'project_contributors', true );
				if ( ! empty( $project_contributors ) ) {
					foreach ( $project_contributors as $contrib ) {
						$project_contrib_ids[] = $contrib['team_id'];
					}   
				}
				if ( ! empty( $project_contrib_ids ) ) {
					foreach ( $members as $member ) {
						$team_details = get_post_meta( $member->ID, 'team_details', true );
						if ( in_array( $member->ID, $project_contrib_ids ) ) {
							echo '<div class="task_watcher pto-inline-item-wrapper"><input type="checkbox" value="' . esc_attr( $member->ID ) . '" name="task_watchers[]" ' . checked( in_array( $member->ID, $task_watchers ), 1, false ) . ' />' . esc_html( $team_details['team_name'] ) . '</div>'; 
						}
					}
				}
			?>
			<input type="hidden" name="task_project_id" value="<?php echo esc_attr( $pid ); ?>" />
			<input type="hidden" name="task_milestone_id" value="<?php echo esc_attr( $mid ); ?>" />
		<?php } else { ?>
			<p class="underline"><?php esc_html_e( 'Main Assignee', 'projectopia-core' ); ?></p>
			<div class="form-group">
				<div class="input-group">
					<select id="task_owner" class="form-control input" name="task_owner">
						<option value=""><?php esc_html_e( 'Choose...', 'projectopia-core' ); ?></option>
						<optgroup label="<?php esc_html_e( 'Support Team Members', 'projectopia-core' ); ?>">
							<?php foreach ( $members as $member ) {
								$team_details = get_post_meta( $member->ID, 'team_details', true );
								$user = get_user_by( 'id', $team_details['user_id'] );
								$caps = $user->allcaps;
								if ( ! empty( $caps['cqpim_view_tickets'] ) ) {
									echo '<option value="' . esc_attr( $member->ID ) . '" ' . selected( $member->ID, $owner, false ) . '>' . esc_html( $team_details['team_name'] ) . '</option>'; 
								}
							} ?>
						</optgroup>
					</select>
				</div>
			</div>
		<?php }
	} else { ?>
		<p class="underline"><?php esc_html_e( 'Main Assignee', 'projectopia-core' ); ?></p>
		<div class="form-group">
			<div class="input-group">
				<select id="task_owner" class="form-control input" name="task_owner">
					<option value=""><?php esc_html_e( 'Choose...', 'projectopia-core' ); ?></option>
					<?php foreach ( $members as $member ) {
						$team_details = get_post_meta( $member->ID, 'team_details', true );
						echo '<option value="' . esc_attr( $member->ID ) . '" ' . selected( $member->ID, $owner, false ) . '>' . esc_html( $team_details['team_name'] ) . '</option>'; 
					} ?>
				</select>
			</div>
		</div>
		<?php
			echo '<p class="underline">' . esc_html__( 'Secondary Assignees', 'projectopia-core' ) . '</p>';
			echo '<p>' . esc_html__( 'People other than the Assignee and Admins who can view/update this task and get notifications', 'projectopia-core' ) . '</p>';
			foreach ( $members as $member ) {
				$team_details = get_post_meta( $member->ID, 'team_details', true );
				echo '<div class="task_watcher pto-inline-item-wrapper"><input type="checkbox" value="' . esc_attr( $member->ID ) . '" name="task_watchers[]" ' . checked( in_array( $member->ID, $task_watchers ), 1, false ) . ' />' . esc_html( $team_details['team_name'] ) . '</div>'; 
			}
		?>
	<?php } ?>
	<div class="clear" style="margin-bottom: -10px;"></div>
	<?php
}

add_action( 'save_post_cqpim_tasks', 'save_pto_task_details_metabox_data' );
function save_pto_task_details_metabox_data( $post_id ) {
	if ( ! isset( $_POST['task_details_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['task_details_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'task_details_metabox' ) ) {
	    return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$duplicate = get_post_meta( $post_id, 'duplicate', true );
	$duplicate = $duplicate ? $duplicate : 0;
	$now = time();
	$diff = $now - $duplicate;
	if ( $diff > 3 ) {
		$user = wp_get_current_user();
		$args = array(
			'post_type'      => 'cqpim_teams',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$members = get_posts( $args );
		foreach ( $members as $member ) {
			$team_details = get_post_meta( $member->ID, 'team_details', true );
			if ( $team_details['user_id'] == $user->ID ) {
				$assigned = $member->ID;
			}
		}
		$task_details_new = get_post_meta( $post_id, 'task_details', true );
		$task_details_new = $task_details_new ? $task_details_new : array();
		$published = get_post_meta( $post_id, 'published', true );
		$ticket_changes = array();
		if ( ! empty( $_POST['custom-field'] ) ) {
			update_post_meta( $post_id, 'custom_fields', pto_sanitize_rec_array( wp_unslash( $_POST['custom-field'] ) ) );  //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
		if ( isset( $_POST['task_status'] ) ) {
			$task_details_new['status'] = sanitize_text_field( wp_unslash( $_POST['task_status'] ) );
		}
		if ( isset( $_POST['task_priority'] ) ) {
			$task_details_new['task_priority'] = sanitize_text_field( wp_unslash( $_POST['task_priority'] ) );
		}   
		if ( isset( $_POST['task_start'] ) ) {
			$task_details_new['task_start'] = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['task_start'] ) ) );
		}
		if ( isset( $_POST['task_deadline'] ) ) {
			$task_details_new['deadline'] = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['task_deadline'] ) ) );
		}
		if ( isset( $_POST['task_est_time'] ) ) {
			$task_details_new['task_est_time'] = sanitize_text_field( wp_unslash( $_POST['task_est_time'] ) );
		}
		if ( isset( $_POST['task_pc'] ) ) {
			$task_details_new['task_pc'] = sanitize_text_field( wp_unslash( $_POST['task_pc'] ) );
		}
		if ( isset( $_POST['task_description'] ) ) {
			$task_details_new['task_description'] = sanitize_textarea_field( wp_unslash( $_POST['task_description'] ) );
		}
		update_post_meta( $post_id, 'task_details', $task_details_new );

		if ( isset( $_POST['task_owner'] ) ) {
			$assignee = sanitize_text_field( wp_unslash( $_POST['task_owner'] ) );
			$assignee_obj = get_post( $assignee );
			$old_assignee = get_post_meta( $post_id, 'owner', true );
			if ( $old_assignee != $assignee && $assignee_obj->post_type == 'cqpim_teams' ) {
				$new_assignee = true;
			} else {
				$new_assignee = false;
			}
			update_post_meta( $post_id, 'owner', $assignee );
		}

		if ( isset( $_POST['task_project_id'] ) ) {
			$ppid = sanitize_text_field( wp_unslash( $_POST['task_project_id'] ) );
			if ( $published == true ) {
				$current_user = wp_get_current_user();
				$project_progress = get_post_meta( $ppid, 'project_progress', true );
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$task_object = get_post( $post_id );
				$task_title = $task_object->post_title;
				/* translators: %s: Task Title */
				$text = sprintf( __('Task Updated: %s', 'projectopia-core' ), $task_title );
				$project_progress[] = array(
					'update' => $text,
					'date'   => time(),
					'by'     => $current_user->display_name,
				);
				update_post_meta( $ppid, 'project_progress', $project_progress );
			}
			update_post_meta( $post_id, 'project_id', $ppid );
		}

		if ( isset( $_POST['task_milestone_id'] ) ) {
			$tms_id = sanitize_text_field( wp_unslash( $_POST['task_milestone_id'] ) );
			update_post_meta( $post_id, 'milestone_id', $tms_id );

            // Get task post children
            $args = array(
                'post_type'      => 'cqpim_tasks',
                'posts_per_page' => -1,
                'post_parent'    => $post_id,
                'orderby'        => 'date',
                'order'          => 'ASC',
            );
            $task_children = get_posts( $args );

            // Update children milestone post meta
            if ( ! empty($task_children) ) {
                foreach ( $task_children as $key => $value ) {
                    update_post_meta( $value->ID, 'milestone_id', $tms_id );
                }
            }        
		}

		$task_watchers = isset( $_POST['task_watchers'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['task_watchers'] ) ) : array();
		update_post_meta( $post_id, 'task_watchers', $task_watchers );

		$attachments = isset( $_POST['image_id'] ) ? sanitize_text_field( wp_unslash( $_POST['image_id'] ) ) : '';
		$attachments_to_send = array();
		if ( ! empty( $attachments ) ) {
			$attachments = explode( ',', $attachments );
			foreach ( $attachments as $attachment ) {
				global $wpdb;
				$wpdb->update( $wpdb->posts, [ 'post_parent' => $post_id ], [ 'ID' => $attachment ] );
				update_post_meta( $attachment, 'cqpim', true );
				$filename = basename( get_attached_file( $attachment ) );
				$attachments_to_send[] = get_attached_file( $attachment );
				/* translators: %s: Uploaded File Name */
				$ticket_changes[] = sprintf( __( 'Uploaded file: %s', 'projectopia-core' ), $filename );
			}
		}

		$message = isset( $_POST['add_task_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['add_task_message'] ) ) : '';
		$message = make_clickable( $message );

		if ( empty( $_POST['delete_file'] ) ) {
			$project_id = isset( $_POST['task_project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['task_project_id'] ) ) : '';
			$task_owner = isset( $_POST['task_owner'] ) ? sanitize_text_field( wp_unslash( $_POST['task_owner'] ) ) : '';
			$task_watchers = isset( $_POST['task_watchers'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['task_watchers'] ) ) : '';
			update_post_meta( $post_id, 'client_updated', false );
			update_post_meta( $post_id, 'team_updated', true );
			pto_send_task_updates( $post_id, $project_id, $task_owner, $task_watchers, $message, '', $attachments_to_send, $new_assignee );

			if ( ! empty( $_POST['task_project_id'] ) && $published != true ) {
				$ppid = sanitize_text_field( wp_unslash( $_POST['task_project_id'] ) );
				$current_user = wp_get_current_user();
				$project_progress = get_post_meta( $ppid, 'project_progress', true );
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$task_object = get_post( $post_id );
				$task_title = $task_object->post_title;
				/* translators: %s: Task Title */
				$text = sprintf( __( 'Task Updated: %s', 'projectopia-core' ), $task_title );
				$project_progress[] = array(
					'update' => $text,
					'date'   => time(),
					'by'     => $current_user->display_name,
				);
				update_post_meta( $ppid, 'project_progress', $project_progress );
			}   
		}
		update_post_meta( $post_id, 'published', true );
		update_post_meta( $post_id, 'active', true );

		if ( ! empty( $message ) || ! empty( $ticket_changes ) ) {
			$task_messages = get_post_meta( $post_id, 'task_messages', true );
			$task_messages = $task_messages && is_array( $task_messages ) ? $task_messages : array();
			$date = time();
			$current_user = wp_get_current_user();
			$task_messages[] = array(
				'date'    => $date,
				'message' => $message,
				'by'      => $current_user->display_name,
				'author'  => $current_user->ID,
				'changes' => $ticket_changes,
			);      
			update_post_meta( $post_id, 'task_messages', $task_messages );
		}

		if ( isset( $_POST['delete_file'] ) ) {
			$att_to_delete = array_map( 'sanitize_text_field', wp_unslash( $_POST['delete_file'] ) );
			foreach ( $att_to_delete as $key => $attID ) {
				$file = get_post( $attID );
				$task_object = get_post( $post_id );
				$task_link = '<a class="cqpim-link" href="' . get_the_permalink( $post_id ) . '">' . $task_object->post_title . '</a>';
				$current_user = wp_get_current_user();
				$project_id = get_post_meta( $post_id, 'project_id', true );
				$project_progress = get_post_meta($project_id, 'project_progress', true);
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$project_progress[] = array(
					/* translators: %1$s: Task Title, %2$s: Task Link */
					'update' => sprintf(esc_html__('File "%1$s" Deleted from - %2$s', 'projectopia-core'), $file->post_title, $task_link),
					'date'   => time(),
					'by'     => $current_user->display_name,
				);
				update_post_meta( $project_id, 'project_progress', $project_progress );
				global $wpdb;
				$wpdb->update( $wpdb->posts, [ 'post_parent' => '' ], [ 'ID' => $attID ] );
			}
		}

		update_post_meta( $post_id, 'duplicate', time() );
	}
}