<?php 
include('header.php');
$client_details = get_post_meta($assigned, 'client_details', true);
$client_ids = get_post_meta($assigned, 'client_ids', true);
$ppid = get_post_meta($post->ID, 'project_id', true); 
$project_details = get_post_meta($ppid, 'project_details', true);
$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper ? $looper : 0;
if ( time() - $looper > 5 ) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($client_id, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$p_title = get_the_title();
	$p_title = str_replace('Protected:', '', $p_title);
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		/* translators: %s: Task Title */
		'page' => sprintf(esc_html__('Task - %1$s', 'projectopia-core'), $p_title),
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php if ( $assigned == $client_id ) {
			$ptitle = get_post($ppid);
			$ptitle = $ptitle->post_title;
			$p_title = get_the_title(); 
			$p_title = str_replace('Protected:', '', $p_title);
			echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> <a href="' . esc_url( get_the_permalink($ppid) ) . '?pto-page=summary&sub=updates">' . esc_html( $ptitle ) . '</a> <i class="fa fa-circle"></i> ' . esc_html( $p_title );
			} else {
				esc_html_e('ACCESS DENIED', 'projectopia-core');
			}
			?>
		</div>
		<div id="cqpim-cdash-inside">
			<?php
			if ( $assigned == $client_id ) { ?>
			<div class="masonry-grid">
				<div class="grid-sizer"></div>
				<div class="cqpim-dash-item-double grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Task Details', 'projectopia-core'); ?> </span>
							</div>
						</div>
						<?php
						$pid = get_post_meta($post->ID, 'project_id', true);
						$mid = get_post_meta($post->ID, 'milestone_id', true);
						$owner = get_post_meta($post->ID, 'owner', true);
						$client_check = preg_replace('/[0-9]+/', '', $owner);
						if ( $client_check == 'C' ) {
							$client = true;
						} else {
							$client = false;
						}
						if ( $owner ) {
							if ( $client == true ) {
								$n_id = preg_replace("/[^0-9,.]/", "", $owner);
								$client_object = get_user_by('id', $n_id);
								$task_owner = $client_object->display_name;
							} else {
								$team_details = get_post_meta($owner, 'team_details', true);
								$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
								if ( ! empty($team_name) ) {
									$task_owner = $team_name;
								}
							}
						} else {
							$task_owner = '';
						}
						$task_details = get_post_meta($post->ID, 'task_details', true);
						$task_watchers = get_post_meta($post->ID, 'task_watchers', true);
						$task_description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
						$task_status = isset($task_details['status']) ? $task_details['status'] : '';
						$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : '';
						$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
						$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
						if ( is_numeric($task_start) ) { $task_start = wp_date(get_option('cqpim_date_format'), $task_start); } else { $task_start = $task_start; }
						if ( is_numeric($task_deadline) ) { $task_deadline = wp_date(get_option('cqpim_date_format'), $task_deadline); } else { $task_deadline = $task_deadline; }
						$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
						$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '';
						echo '<p><strong>' . esc_html__('Description', 'projectopia-core') . ':</strong></p>';
						echo wp_kses_post(wpautop($task_description)); 
						?>
						<div class="">
							<p><strong>
							<?php 
							esc_html_e('Assigned To', 'projectopia-core');
							echo ':</strong> '                          
							?>								
							<select id="task_owner" name="task_owner">
								<optgroup label="<?php esc_html_e('Client', 'projectopia-core'); ?>">
								<?php 
								$client_details = get_post_meta($client_id, 'client_details', true);
 								$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
								if ( ! empty($client_id) ) {?>
								<option value="<?php echo esc_attr($client_id); ?>" <?php selected($owner, $client_id); ?>>
									<?php echo esc_html($client_contact_name); ?>
								</option>
								<?php }  ?>
								</optgroup>
								<?php 
									$contribs = get_post_meta($pid, 'project_contributors', true);
									if ( ! empty($contribs) ) { ?>
										<optgroup label="<?php esc_html_e('Team Members', 'projectopia-core'); ?>">
										<?php foreach ( $contribs as $contrib ) {
											$team_details = get_post_meta($contrib['team_id'], 'team_details', true);
											echo '<option value="' . esc_attr($contrib['team_id']) . '" ' . selected($owner,  $contrib['team_id'], false) . '>' . esc_html($team_details['team_name']) . '</option>';
										} ?>
										</optgroup>
									<?php }
								?>
							</select>	
							</p>
						</div>
						<div class="clear"></div>
						<div class="">
							<p><strong><?php 
							esc_html_e( 'Task Status', 'projectopia-core' );
							/**
							 * Filter Task Status Display
							 */
							$task_status = apply_filters( 'pto_task_status_string', pto_get_task_status_value_by_key( $task_status ), $task_details['status'], $task_details );
							echo ':</strong> ' . esc_html( ucwords( $task_status ) );
							?>
							</p>
						</div>
						<div class="">
							<p><strong>
							<?php 
							esc_html_e('Task Priority', 'projectopia-core');  
							if ( $task_priority == 'normal' ) { $task_priority = __('Normal', 'projectopia-core'); } 
							if ( $task_priority == 'low' ) { $task_priority = __('Low', 'projectopia-core'); } 
							if ( $task_priority == 'high' ) { $task_priority = __('High', 'projectopia-core'); } 
							if ( $task_priority == 'immediate' ) { $task_priority = __('Immediate', 'projectopia-core'); } 
							echo ':</strong> ' . esc_html( ucwords($task_priority) );                                   
							?>
							</p>
						</div>
						<div class="clear"></div>
						<div class="">
							<p><strong>
							<?php 
							esc_html_e('Start Date', 'projectopia-core');  
							echo ':</strong> ' . esc_html( ucwords($task_start) );                              
							?>
							</p>
						</div>
						<div class="">
							<p><strong>
							<?php 
							esc_html_e('Deadline', 'projectopia-core');  
							echo ':</strong> ' . esc_html( ucwords($task_deadline) );                               
							?>
							</p>
						</div>
						<div class="clear"></div>
						<div class="">
							<p><strong>
							<?php 
							esc_html_e('Estimated Time (Hours)', 'projectopia-core'); 
							echo ':</strong> ' . esc_html( ucwords($task_est_time) );                               
							?>
							</p>
						</div>
						<div class="">
							<p><strong>
							<?php 
							esc_html_e('Percentage Complete', 'projectopia-core');
							echo ':</strong> ' . esc_html( ucwords($task_pc) ) . '%';                               
							?>
							</p>	
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="cqpim-dash-item-triple grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Task Messages', 'projectopia-core'); ?></span>
							</div>
						</div>
						<?php
						$string = pto_random_string(10);
						$messages = get_post_meta($post->ID, 'task_messages', true);
						if ( empty($messages) ) {
							echo '<p>' . esc_html__('No messages to show', 'projectopia-core') . '</p>';
						} else { ?>
							<div style="max-height:500px; overflow:auto">
								<ul class="project_summary_progress" style="margin:0">
								<?php $messages = array_reverse($messages);
								foreach ( $messages as $key => $message ) { 
									$user = get_user_by('id', $message['author']);
									$email = $user->user_email;
									$size = 80;
									$changes = isset($message['changes']) ? $message['changes'] : array();
									?>
									<li style="margin-bottom:0">
										<div class="timeline-entry">
											<?php 
											$avatar = get_option('cqpim_disable_avatars');
											if ( empty($avatar) ) {
												echo '<div class="update-who">';
												echo get_avatar( $user->ID, 60, '', false, array( 'force_display' => true ) );
												echo '</div>';
											} ?>
											<?php if ( empty($avatar) ) { ?>
												<div class="update-data">
											<?php } else { ?>
												<div style="width:100%; float:none" class="update-data">
											<?php } ?>
												<div class="timeline-body-arrow"> </div>
												<div class="timeline-by font-blue-madison sbold">
													<?php echo wp_kses_post($message['by']); ?>
												</div>
												<div class="clear"></div>
												<div class="timeline-update font-grey-cascade"><?php echo wp_kses_post(wpautop($message['message'])); ?></div>
												<div class="clear"></div>
												<div class="timeline-date font-grey-cascade">
													<?php echo esc_html(wp_date(get_option('cqpim_date_format') . ' H:i', $message['date'])); ?>
													<?php if ( ! empty($changes) ) {
														foreach ( $changes as $change ) {
															echo ' | ' . wp_kses_post( $change );
														}
													} ?>													
												</div>
											</div>
											<div class="clear"></div>
										</div>
									</li>
								<?php } ?>	
								</ul>
							</div>
						<?php } ?>
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Update Task', 'projectopia-core'); ?></span>
							</div>
						</div>
						<?php
						$data = get_option('cqpim_custom_fields_task');
						if ( ! empty($data) ) {
							$form_data = json_decode($data);
							$fields = $form_data;
						}
						$values = get_post_meta($post->ID, 'custom_fields', true);
						if ( ! empty($fields) ) {
							echo '<div id="cqpim-custom-fields">';
							foreach ( $fields as $field ) {
								$p_class_name = isset($field->className) ? $field->className : '';
								$value = isset($values[ $field->name ]) ? $values[ $field->name ] : '';
								$n_id = strtolower($field->label);
								$n_id = str_replace(' ', '_', $n_id);
								$n_id = str_replace('-', '_', $n_id);
								$n_id = preg_replace('/[^\w-]/', '', $n_id);
								if ( ! empty($field->required) ) {
									$required = 'required';
									$ast = '<span style="color:#F00">*</span>';
								} else {
									$required = '';
									$ast = '';
								}
								echo '<div style="padding-bottom:12px" class="cqpim_form_item">';
								if ( $field->type != 'header' ) {
									echo '<label style="display:block; padding-bottom:5px" for="' . esc_attr( $n_id ) . '">' . esc_html( $field->label ) . ' ' . wp_kses_post( $ast ) . '</label>';
								}
								if ( $field->type == 'header' ) {
									echo '<' . esc_attr( $field->subtype ) . ' class="cqpim-custom ' . esc_attr( $p_class_name ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
								} elseif ( $field->type == 'text' ) {          
									echo '<input type="text" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
								} elseif ( $field->type == 'website' ) {
									echo '<input type="url" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ). '" />';
								} elseif ( $field->type == 'number' ) {
									echo '<input type="number" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
								} elseif ( $field->type == 'textarea' ) {
									echo '<textarea class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '">' . esc_textarea( $value ). '</textarea>';
								} elseif ( $field->type == 'date' ) {
									echo '<input class="cqpim-custom ' . esc_attr( $p_class_name ) . ' datepicker" type="text" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
								} elseif ( $field->type == 'email' ) {
									echo '<input type="email" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
								} elseif ( $field->type == 'checkbox-group' ) {
									if ( ! is_array($value) ) {
										$value = array( $value );
									}
									$options = $field->values;
									foreach ( $options as $option ) {
										echo '<input type="checkbox" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $field->name ) . '" ' . checked( in_array( $option->value, $value ), 1, false ) . ' /> ' . esc_html( $option->label ) . '<br />';
									}
								} elseif ( $field->type == 'radio-group' ) {
									$options = $field->values;
									foreach ( $options as $option ) {
										echo '<input type="radio" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $field->name ) . '" ' . esc_attr( $required ) . ' ' . checked( $value, $option->value, false ) . ' /> ' . esc_html( $option->label ) . '<br />';
									}
								} elseif ( $field->type == 'select' ) {
									$options = $field->values;
									echo '<select class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '">';
										foreach ( $options as $option ) {  
											echo '<option value="' . esc_attr( $option->value ) . '" ' . selected( $value, $option->value, false ) . '>' . esc_html( $option->label ) . '</option>';
										}
									echo '</select>';
								}
								if ( ! empty($field->other) && $field->other == 1 ) {
									echo '<br />';
									echo esc_html__('Other:', 'projectopia-core') . '<input class="cqpim-custom " style="width:100%" type="text" id="' . esc_attr( $n_id ) . '_other" name="custom-field[' . esc_attr( $field->name ) . '_other]" />';
								}
								if ( ! empty($field->description) ) {
									echo '<span class="cqpim-field-description">' . esc_textarea( $field->description ) . '</span>';
								}
								echo '</div>';
							}
							echo '</div>';
						}
						?>
						<h4><?php esc_html_e('Upload Files', 'projectopia-core'); ?></h4>
						<input type="hidden" id="file_task_id" name="file_task_id" value="<?php echo esc_attr( $post->ID ); ?>" />
						<input type="hidden" name="action" value="new_file" />
						<?php wp_nonce_field( 'new-post' ); ?>
						<input type="hidden" name="ip_address" value="<?php echo esc_attr( pto_get_client_ip() ); ?>" />
						<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
						<div id="upload_attachments"></div>
						<div class="clear"></div>
						<input type="hidden" name="image_id" id="upload_attachment_ids">
						<?php echo '<h4>' . esc_html__('Add Message', 'projectopia-core') . '</h4>'; ?>
						<textarea id="add_task_message" name="add_task_message"></textarea>
						<a href="#" id="update_task" class="cqpim_button font-white bg-violet mt-20 right op rounded_2"><?php esc_html_e('Update Task', 'projectopia-core'); ?></a>
						<div class="clear"></div>							
					</div>
				</div>
				<?php $hide_time = get_post_meta($post->ID, 'hide_front', true);
				if ( pto_has_addon_active_license( 'pto_te', 'timeentries' ) && empty( $hide_time ) ) { ?>
					<div class="cqpim-dash-item-double grid-item">
						<div class="cqpim_block">
							<div class="cqpim_block_title">
								<div class="caption">
									<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Time Entries', 'projectopia-core'); ?></span>
								</div>
							</div>
							<?php
							$time_spent = get_post_meta($post->ID, 'task_time_spent', true);
							if ( $time_spent ) {
								$total = 0;
								echo '<ul class="time_spent">';
								foreach ( $time_spent as $key => $time ) {
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
									if ( $assigned == $time['team_id'] || current_user_can('cqpim_dash_view_all_tasks') ) {
										$delete = ' - <a class="time_remove" href="#" data-key="'. esc_attr( $key ) .'" data-task="'. esc_attr( $post->ID ) .'">' . esc_html__('REMOVE', 'projectopia-core') . '</a>';
									} else {
										$delete = '';
									}
									echo '<li>' . wp_kses_post( $time['team'] ) . ' <span style="float:right" class="right"><strong>' . number_format( (float)$time['time'], 2, '.', '') . ' ' . esc_html__('HOURS', 'projectopia-core') . '</strong> ' . wp_kses_post( $delete ) . '</span></li>';
									$total = $total + $time['time'];
								}
								echo '</ul>';
								$total = str_replace(',','.', $total);
								$time_split = explode('.', $total);
								if ( ! empty($time_split[1]) ) {
									$minutes = '0.' . $time_split[1];
									$minutes = $minutes * 60;
									$minutes = number_format( (float)$minutes, 0, '.', '');
								} else {
									$minutes = '0';
								}
								if ( $time_split[0] > 1 ) {
									$hours  = __('hours', 'projectopia-core');
								} else {
									$hours = __('hour', 'projectopia-core');
								}
								echo '<br /><span><strong>TOTAL: ' . number_format( (float)$total, 2, '.', '') . ' ' . esc_html__('hours', 'projectopia-core') . '</strong> (' . esc_html( $time_split[0] . ' ' . $hours . ' + ' . $minutes ) . ' ' . esc_html__('minutes', 'projectopia-core') . ')</span> <div id="ajax_spinner_remove_time_'. esc_attr( $post->ID ).'" class="ajax_spinner" style="display:none"></div>';
							} else {
								echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('This task does not have any time assigned to it', 'projectopia-core') . '</div>';
							}
							?>
						</div>
					</div>
				<?php } ?>
				<div class="cqpim-dash-item-double grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Task Files', 'projectopia-core'); ?></span>
							</div>
						</div>
						<div id="uploaded_files">
						<?php 
						$all_attached_files = get_attached_media( '', $post->ID );
						if ( ! $all_attached_files ) {
							echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('There are no files uploaded to this task.', 'projectopia-core') . '</div>';
						} else {
							echo '<br /><table class="cqpim_table"><thead><tr>';
							echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
							echo '</tr></thead><tbody>';
							foreach ( $all_attached_files as $file ) {
								$file_object = get_post($file->ID);
								$user = get_user_by( 'id', $file->post_author );
								echo '<tr>';
								echo '<td><a class="cqpim-link" href="' . esc_url( $file->guid ) . '" download="' . esc_attr( $file->post_title ) . '">' . esc_html( $file->post_title ) . '</a><p>' . esc_html__('Uploaded on', 'projectopia-core') . ' ' . esc_html( $file->post_date ) . ' ' . esc_html__('by', 'projectopia-core') . ' ' . ( isset( $user->display_name ) ? esc_html( $user->display_name ) : esc_html__('System', 'projectopia-core') ) . '</p></td>';
								echo '<td><a href="' . esc_url( $file->guid ) . '" download="' . esc_attr( $file->post_title ) . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . esc_attr( $file->ID ) . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
								echo '</tr>';
							}
							echo '</tbody></table>';
						}
						?>
						</div>
					</div>
				</div>					
			</div>
			<?php } else {
				echo '<h1 style="margin-top:0">' . esc_html__('ACCESS DENIED', 'projectopia-core') . '</h1>';
			}
			?>	
		</div>
	</div>
</div>
<?php include('footer_inc.php'); ?>