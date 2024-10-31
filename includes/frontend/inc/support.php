<?php 
include('header.php');
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper ? $looper : 0;
if ( time() - $looper > 5 ) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$p_title = get_the_title();
	$p_title = str_replace('Private:', '', $p_title);
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		/* translators: %s: Ticket Title */
		'page' => sprintf(esc_html__('Support Ticket - %s', 'projectopia-core'), $p_title),
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
$ticket_client = get_post_meta($post->ID, 'ticket_client', true);
?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php 
			if ( $post->post_author == $user_id OR $assigned == $ticket_client ) { 
				$p_title = get_the_title();
				$p_title = str_replace('Private:', '', $p_title);
				echo '<a href="' . esc_url(get_the_permalink($client_dash)) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> <a href="' . esc_url(get_the_permalink($client_dash)) . '?pto-page=support">' . esc_html__('Support Tickets', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html( $p_title ); 
			} else {
				esc_html_e('ACCESS DENIED', 'projectopia-core');
			} ?>
		</div>
		<div id="cqpim-cdash-inside">
			<?php
			$user = wp_get_current_user(); 
			$user_id = $user->ID;
			$dash_page = get_option('cqpim_client_page');
			$dash_url = get_the_permalink($dash_page);
			$ticket_author = $post->post_author;
			$author_details = get_user_by('id', $ticket_author);
			$client_name = $author_details->display_name;
			$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
			$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
			$ticket_updated = get_post_meta($post->ID, 'last_updated', true);
			if ( is_numeric($ticket_updated) ) { $ticket_updated = wp_date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }
			$support_ticket_status = get_option('support_status');
			$key_arr = $support_ticket_status['key'];
			$value_arr = $support_ticket_status['value'];
			$color_arr = $support_ticket_status['color'];

			$pos = array_search($ticket_status, $key_arr);
			$val = $value_arr[ $pos ];
			$col = $color_arr[ $pos ];
			$t_status = '<span class="cqpim_button cqpim_small_button op" style="border: 1px solid '.esc_attr( $col ).'; color:'.esc_attr( $col ).'">' . esc_html( $val ) . '</span>';

			$priority = '';
			if ( ! empty( $ticket_priority ) ) {
				$support_ticket_priorities = get_option( 'support_ticket_priorities');
				if ( ! empty( $support_ticket_priorities[ $ticket_priority ] ) ) {
					$color_code = $support_ticket_priorities[ $ticket_priority ];
					$priority = '<span style="text-transform:capitalize;border:solid 1px '. esc_attr( $color_code ) .' !important;color:'. esc_attr( $color_code ) .' !important" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . esc_html( $ticket_priority ) . '</span>';
				} else {
					$priority = '<span style="text-transform:capitalize;" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . esc_html( $ticket_priority ) . '</span>';
				}
			}

			if ( $post->post_author == $user_id OR $assigned == $ticket_client ) {
				if ( $post->post_author == $user_id OR $assigned == $ticket_client ) {
					update_post_meta($post->ID, 'unread', 0);
				} ?>
			<?php
			$show_open_warning = get_option('pto_support_opening_warning');
			if ( ! empty($show_open_warning) ) {
				$open = pto_return_open();
				if ( $open == 1 ) {
					$message = get_option('pto_support_closed_message');
					echo '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_textarea( $message ) . '</div>';
				} elseif ( $open == 2 ) {
					$message = get_option('pto_support_open_message');
					echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_textarea( $message ) . '</div>';
				}
			}
			?>
				<div class="masonry-grid">
					<div class="grid-sizer"></div>
					<div class="cqpim-dash-item-double grid-item">
						<div id="ticket_container" class="cqpim_block">
							<div class="cqpim_block_title">
								<div class="caption">
									<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Ticket Details', 'projectopia-core'); ?></span>
								</div>
							</div>
							<table class="cqpim_table dash">
								<thead>
									<tr>
										<th><strong><?php esc_html_e('Info', 'projectopia-core'); ?></strong></th>
										<th><?php esc_html_e('Content', 'projectopia-core'); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><strong><?php esc_html_e('Ticket ID', 'projectopia-core'); ?></strong></td>
										<td><?php echo esc_html( $post->ID ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e('Ticket Title', 'projectopia-core'); ?></strong></td>
										<td><?php echo esc_html( $post->post_title ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e('Ticket Created', 'projectopia-core'); ?></strong></td>
										<td><?php echo esc_html( get_the_date('d/m/Y H:i') ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e('Last Updated', 'projectopia-core'); ?></strong></td>
										<td><?php echo esc_html( $ticket_updated ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e('Ticket Priority', 'projectopia-core'); ?></strong></td>
										<td><?php echo wp_kses_post( $priority ); ?></td>
									</tr>
									<tr>
										<td><strong><?php esc_html_e('Ticket Status', 'projectopia-core'); ?></strong></td>
										<td><?php echo wp_kses_post( $t_status ); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="cqpim-dash-item-triple grid-item">
						<div id="ticket_container" class="cqpim_block">
							<div class="cqpim_block_title">
								<div class="caption">
									<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Ticket Updates', 'projectopia-core'); ?></span>
								</div>
							</div>
							<?php
							$ticket_updates = get_post_meta($post->ID, 'ticket_updates', true);
							$ticket_status = get_post_meta($post->ID, 'ticket_status', true);
							$ticket_priority = get_post_meta($post->ID, 'ticket_priority', true);
							if ( empty($ticket_updates) ) {
								$ticket_updates = array();
								echo '<p>' . esc_html_e('There are no updates on this ticket.', 'projectopia-core') . '</p>';
							} ?>
							<div style="max-height:900px" class="project_messages">
								<ul class="project_summary_progress" style="margin:0; overflow:auto; max-height:600px">
									<?php $ticket_updates = array_reverse($ticket_updates);
									foreach ( $ticket_updates as $key => $update ) {
										if ( $update['type'] == 'client' ) {
											$user = get_post_meta($update['user'], 'client_details', true);
											$email = isset($user['client_email']) ? $user['client_email'] : '';
											$name = isset($user['client_contact']) ? $user['client_contact'] : '';
										} else {
											$user_obj = wp_get_current_user();
											$user = get_post_meta($update['user'], 'team_details', true);
											$email = isset($user['team_email']) ? $user['team_email'] : '';
											$name = isset($user['team_name']) ? $user['team_name'] : '';
											if ( empty($ticket_updates[ $key ]['seen']) ) {
												$ticket_updates[ $key ]['seen'] = array(
													'time' => time(),
													'user' => $user_obj->ID,
												);
											}
											update_post_meta($post->ID, 'ticket_updates', array_reverse($ticket_updates));
										}
										$changes = isset($update['changes']) ? $update['changes'] : array();
										$size = 80;
										if ( isset($update['email']) ) {
											$email = $update['email'];
										}           
										?>
										<li style="margin-bottom:0">
											<div class="timeline-entry">
												<?php 
												$avatar = get_option('cqpim_disable_avatars');
												if ( empty($avatar) ) {
													$user = get_user_by('email', $email);
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
														<?php echo wp_kses_post( $update['name'] ); ?>
													</div>
													<div class="clear"></div>
													<div class="timeline-update font-grey-cascade"><?php echo wp_kses_post( wpautop($update['details']) ); ?></div>
													<div class="clear"></div>
													<div class="timeline-date font-grey-cascade">
														<?php echo esc_html( wp_date(get_option('cqpim_date_format') . ' H:i', $update['time']) ); ?>
														<span>&nbsp;</span>
														<span>
															<?php
															if ( $changes ) {
																foreach ( $changes as $change ) {
																	echo ' | ' . wp_kses_post( $change );
																}
															}
															?>
														</span>
													</div>
												</div>
												<div class="clear"></div>
											</div>
										</li>
										<?php	      
									} ?>
								</ul>
							</div>
							<?php $string = pto_random_string(10);
							
							pto_set_transient('upload_ids','');
							pto_set_transient('ticket_changes','');
							?>
							<div id="add_ticket_update" style="margin-top: 15px;">
								<div class="cqpim_block_title">
									<div class="caption">
										<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Update Ticket', 'projectopia-core'); ?></span>
									</div>
								</div>
								<input type="hidden" name="action" value="update_ticket" />
								<input type="hidden" id="post_id" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
								<div class="cqpim-meta-left">
									<h4><?php esc_html_e('Ticket Status:', 'projectopia-core'); ?></h4>
									<?php $support_ticket_status = get_option('support_status'); ?>
									<select id="ticket_status_new" class="full-width" name="ticket_status_new">
									<?php
									for ( $i = 0 ;$i < count($support_ticket_status['key']); $i++ ) {
										$key = $support_ticket_status['key'][ $i ];
										$desc = $support_ticket_status['value'][ $i ];
										echo '<option value="'.esc_attr($key).'" '.selected($ticket_status, $key) .'>'.esc_html($desc).'</option>';
									}
									?>
									</select>
								</div>

								<div class="cqpim-meta-right">			
									<h4><?php esc_html_e('Ticket Priority:', 'projectopia-core'); ?></h4>
									<?php
										$support_ticket_priorities = get_option( 'support_ticket_priorities');
										if ( empty( $support_ticket_priorities ) ) {
											$support_ticket_priorities = array(
												'low'    => '#5c9bd1',
												'normal' => '#8ec165',
												'high'   => '#f1c40f',
												'immediate' => '#f10f0f',
											);
											update_option( 'support_ticket_priorities', $support_ticket_priorities );
										}
									?>
									<select id="ticket_priority_new" class="full-width" name="ticket_priority_new">
										<?php
											foreach ( $support_ticket_priorities as $key => $priority_color ) {
												printf(
													'<option value="%s" %s>%s</option>',
													esc_attr( $key ),
													selected( $key, $ticket_priority, false ),
													esc_html( $key )
												);
											}
										?>
									</select>
								</div>

								<div class="clear"></div>
								<div>
									<h4><?php esc_html_e('Upload Files', 'projectopia-core'); ?></h4>
									<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
									<div id="upload_attachments"></div>
									<div class="clear"></div>
									<input type="hidden" name="image_id" id="upload_attachment_ids">
								</div>
								<div class="clear"></div>
								<?php
								$data = get_option('cqpim_custom_fields_support');
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
											echo '<input type="url" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
										} elseif ( $field->type == 'number' ) {
											echo '<input type="number" class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
										} elseif ( $field->type == 'textarea' ) {
											echo '<textarea class="cqpim-custom ' . esc_attr( $p_class_name ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '">' . esc_textarea( $value ) . '</textarea>';
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
											echo '<select class="cqpim-custom ' . esc_attr( $p_class_name ) . ' full-width" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '">';
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
											echo '<span class="cqpim-field-description">' . wp_kses_post( $field->description ) . '</span>';
										}
										echo '</div>';
									}
									echo '</div>';
								}
								?>
								<h4><?php esc_html_e('Message', 'projectopia-core'); ?></h4>
								<textarea id="ticket_update_new" required ></textarea>
								<div class="clear"></div>
								<br />
								<a href="#" id="update_support" class="cqpim_button op right font-white bg-violet rounded_2 mt-20"><?php esc_html_e('Update Ticket', 'projectopia-core'); ?></a>
								<div class="clear"></div>
							</div>					
						</div>
					</div>
					<div class="cqpim-dash-item-double grid-item">
						<div id="ticket_container" class="cqpim_block">
							<div class="cqpim_block_title">
								<div class="caption">
									<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Ticket Files', 'projectopia-core'); ?></span>
								</div>
							</div>
							<?php 
							$all_attached_files = get_attached_media( '', $post->ID );
							if ( ! $all_attached_files ) {
								echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('There are no files uploaded to this ticket.', 'projectopia-core') . '</div>';
							} else {
								echo '<br /><table class="cqpim_table dash"><thead><tr>';
								echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
								echo '</tr></thead><tbody>';
								foreach ( $all_attached_files as $file ) {
									$file_object = get_post($file->ID);
									$user = get_user_by( 'id', $file->post_author );
									echo '<tr>';
									echo '<td style="text-align:left"><a class="cqpim-link" href="' . esc_url( $file->guid ) . '" download="' . esc_attr( $file->post_title ) . '">' . esc_html( $file->post_title ) . '</a><p style="margin:0; text-align:left; padding-left:0; padding-bottom:0">' . esc_html__('Uploaded on', 'projectopia-core') . ' ' . esc_html( $file->post_date ) . ' ' . esc_html__('by', 'projectopia-core') . ' ' . ( isset( $user->display_name ) ? esc_html( $user->display_name ) : esc_html__('System', 'projectopia-core') ) . '</p></td>';
									echo '<td><a href="' . esc_url( $file->guid ) . '" download="' . esc_attr( $file->post_title ) . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . esc_attr( $file->ID ) . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
									echo '</tr>';
								}
								echo '</tbody></table>';
							}
							?>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<br />
				<div class="cqpim-dash-item-full grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Access Denied', 'projectopia-core'); ?></span>
							</div>
						</div>
						<p><?php esc_html_e('Cheatin\' uh? We can\'t let you see this item because it\'s not yours', 'projectopia-core'); ?></p>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include('footer_inc.php'); ?>