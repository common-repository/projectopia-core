<br />
<div class="cqpim_block">
<div class="cqpim_block_title">
	<div class="caption">
		<i class="fa fa-cog font-light-violet" aria-hidden="true"></i>
		<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Profile', 'projectopia-core'); ?></span>
	</div>
</div>
<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Settings Page', 'projectopia-core'),
);
update_post_meta($assigned, 'client_logs', $client_logs);
$client_settings = get_option('allow_client_settings');
$data = get_option('cqpim_custom_fields_client');
if ( ! empty($data) ) {
	$form_data = json_decode($data);
	$fields = $form_data;
}
$values = get_post_meta($assigned, 'custom_fields', true);
$frontend = get_post_meta($assigned, 'field_frontend', true);
if ( $client_settings == 1 ) { ?>
		<div id="cqpim_backend_quote">
			<?php
			if ( $client_type == 'admin' ) {
				$client_details = get_post_meta($assigned, 'client_details', true);
			} else {
				$client_details = get_post_meta($assigned, 'client_details', true);
				$client_contacts = get_post_meta($assigned, 'client_contacts', true);
				if ( empty($client_contacts) ) {
					$client_contacts = array();
				}
				foreach ( $client_contacts as $key => $contact ) {
					if ( $key == $user_id ) {
						$client_details['client_telephone'] = $contact['telephone'];
						$client_details['client_contact'] = $contact['name'];
						$client_details['client_email'] = $contact['email'];
					}
				}
			}
			?>
			<form id="client_settings">
				<h4><?php esc_html_e('My Details', 'projectopia-core'); ?></h4>
				<label for="client_email"><?php esc_html_e('Email Address', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="text" id="client_email" name="client_email" value="<?php echo esc_attr( $user->user_email ); ?>" required />
				<label for="client_phone"><?php esc_html_e('Telephone', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="text" id="client_phone" name="client_phone" value="<?php echo isset($client_details['client_telephone']) ? esc_attr($client_details['client_telephone']) : ''; ?>" required />
				<label for="client_email"><?php esc_html_e('Display Name', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="text" id="client_name" name="client_name" value="<?php echo esc_attr( $user->display_name ); ?>" required />
				<h4 style="margin-top:20px"><?php esc_html_e('Company Details', 'projectopia-core'); ?></h4>
				<label for="company_name"><?php esc_html_e('Company Name', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="text" id="company_name" name="company_name" value="<?php echo isset($client_details['client_company']) ? esc_html( $client_details['client_company'] ) : ''; ?>" required />
				<label for="company_address"><?php esc_html_e('Company Address', 'projectopia-core'); ?></label>
				<textarea style="width:98%; padding:1%; height:100px" id="company_address" name="company_address" required ><?php echo isset($client_details['client_address']) ? esc_textarea($client_details['client_address']) : ''; ?></textarea>
				<label for="company_postcode"><?php esc_html_e('Company Postcode', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="text" id="company_postcode" name="company_postcode" value="<?php echo isset($client_details['client_postcode']) ? esc_attr($client_details['client_postcode']) : ''; ?>" required />							
				<?php
				if ( ! empty($fields) ) {
					$i = 0;
					foreach ( $fields as $field ) {
						if ( $field->type == 'header' ) {
							$field->name = 'header-' . $i;
							$i++;
						}
					}
					echo '<div id="cqpim-custom-fields">';
					foreach ( $fields as $field ) {
						if ( ! empty($frontend[ $field->name ]) ) {
							$value = isset($values[ $field->name ]) ? $values[ $field->name ] : '';
							$n_id = strtolower($field->label);
							$n_id = str_replace(' ', '_', $n_id);
							$n_id = str_replace('-', '_', $n_id);
							$n_id = preg_replace('/[^\w-]/', '', $n_id);
							if ( ! empty($field->required) && $field->required == 1 ) {
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
								echo '<' . esc_attr( $field->subtype ) . ' class="' . esc_attr( $field->className ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
							} elseif ( $field->type == 'text' ) {          
								echo '<input type="text" class="cqpim-custom ' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
							} elseif ( $field->type == 'website' ) {
								echo '<input type="url" class="cqpim-custom ' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
							} elseif ( $field->type == 'number' ) {
								echo '<input type="number" class="cqpim-custom ' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
							} elseif ( $field->type == 'textarea' ) {
								echo '<textarea class="cqpim-custom ' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '">' . esc_attr( $value ) . '</textarea>';
							} elseif ( $field->type == 'date' ) {
								echo '<input class="cqpim-custom ' . esc_attr( $field->className ) . ' datepicker" type="text" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
							} elseif ( $field->type == 'email' ) {
								echo '<input type="email" class="cqpim-custom ' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $value ) . '" />';
							} elseif ( $field->type == 'checkbox-group' ) {
								if ( ! is_array($value) ) {
									$value = array( $value );
								}
								$options = $field->values;
								foreach ( $options as $option ) {
									echo '<input type="checkbox" class="cqpim-custom ' . esc_attr( $field->className ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $field->name ) . '" ' . checked( in_array( $option->value, $value ), 1, false ) . ' /> ' . esc_html( $option->label ) . '<br />';
								}
							} elseif ( $field->type == 'radio-group' ) {
								$options = $field->values;
								foreach ( $options as $option ) {
									echo '<input type="radio" class="cqpim-custom ' . esc_attr( $field->className ) . '" value="' . esc_attr( $option->value ) . '" name="' . esc_attr( $field->name ) . '" ' . esc_attr( $required ) . ' ' . checked( $value, $option->value, false ) . ' /> ' . esc_html( $option->label ) . '<br />';
								}
							} elseif ( $field->type == 'select' ) {
								$options = $field->values;
								echo '<select class="cqpim-custom ' . esc_attr( $field->className ) . '" id="' . esc_attr( $n_id ) . '" ' . esc_attr( $required ) . ' name="' . esc_attr( $field->name ) . '">';
									foreach ( $options as $option ) {
										echo '<option value="' . esc_attr( $option->value ) . '" ' . selected( $value, $option->value, false ) . '>' . esc_html( $option->label ) . '</option>';
									}
								echo '</select>';
							}
							if ( ! empty($field->other) && $field->other == 1 ) {
								echo '<br />';
								echo esc_html__('Other:', 'projectopia-core') . '<input class="cqpim-custom" style="width:100%" type="text" id="' . esc_attr( $n_id ) . '_other" name="' . esc_attr( $field->name ) . '_other" />';
							}
							if ( ! empty($field->description) ) {
								echo '<span class="cqpim-field-description">' . wp_kses_post( $field->description ) . '</span>';
							}
							echo '</div>';
						}
					}
					echo '</div>';
				}
				?>	
				<h4 style="margin-top:20px"><?php esc_html_e('Change Password', 'projectopia-core'); ?></h4>
				<label for="client_pass"><?php esc_html_e('New Password', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="password" id="client_pass" name="client_pass" value="" />
				<label for="client_pass_rep"><?php esc_html_e('Repeat New Password', 'projectopia-core'); ?></label>
				<input style="width:98%; padding:1%" type="password" id="client_pass_rep" name="client_pass_rep" value=""  />
				<h4 style="margin-top:20px"><?php esc_html_e('Change Photo', 'projectopia-core'); ?></h4>			
				<p><?php esc_html_e('Upload new Photo', 'projectopia-core'); ?></p>
				<div class="cqpim_upload_wrapper">
					<input type="file" class="cqpim-file-upload-avatar" name="async-upload" id="attachments" />
					<div id="upload_attachments"></div>
					<div class="clear"></div>
					<input type="hidden" name="image_id" id="upload_attachment_ids">
				</div>
				<div id="pto_avatar_preview_cont" style="display:none; float:left; margin-right:30px">
					<p><?php esc_html_e('New Photo Preview', 'projectopia-core'); ?></p>
					<div id="pto_avatar_preview"></div>
				</div>
				<?php 
				if ( $client_type == 'admin' ) {
					$team_avatar = get_post_meta($assigned, 'team_avatar', true);
				} else {
					$client_contacts = get_post_meta($assigned, 'client_contacts', true);
					if ( empty($client_contacts) ) {
						$client_contacts = array();
					}
					foreach ( $client_contacts as $key => $contact ) {
						if ( $key == $user->ID ) {
							$team_avatar = isset($contact['team_avatar']) ? $contact['team_avatar'] : '';
						}
					}                   
				}
				if ( ! empty($team_avatar) ) { ?>
					<div id="pto_avatar_current_cont" style="float:left">
						<p><?php esc_html_e('Current Photo', 'projectopia-core'); ?></p>
						<div id="pto_avatar_current"><?php echo wp_get_attachment_image($team_avatar, 'thumbnail', false, '' ); ?></div>
					</div>
				<?php } ?>
				<div class="clear"></div>
				<?php if ( ! empty($team_avatar) ) { ?>
					<div class="pto_remove_current_client_photo cqpim_button bg-red font-white rounded_4" data-type="photo"><?php esc_html_e('Remove Photo', 'projectopia-core'); ?></div>
				<?php } ?>
				<div class="clear"></div>				
				<h4 style="margin-top:20px"><?php esc_html_e('Email Notification Preferences', 'projectopia-core'); ?></h4>
				<p><strong><?php esc_html_e('Tasks', 'projectopia-core'); ?></strong></p>
				<?php 
				if ( $client_type == 'admin' ) { 
					$notifications = get_post_meta($assigned, 'client_notifications', true);
				} else {
					$client_contacts = get_post_meta($assigned, 'client_contacts', true);
					$notifications = isset($client_contacts[ $user->ID ]['notifications']) ? $client_contacts[ $user->ID ]['notifications'] : array();
				}
				$no_tasks = isset($notifications['no_tasks']) ? $notifications['no_tasks'] : 0;
				$no_tasks_comment = isset($notifications['no_tasks_comment']) ? $notifications['no_tasks_comment'] : 0;
				$no_tickets = isset($notifications['no_tickets']) ? $notifications['no_tickets'] : 0;
				$no_tickets_comment = isset($notifications['no_tickets_comment']) ? $notifications['no_tickets_comment'] : 0;
				$no_bugs = isset($notifications['no_bugs']) ? $notifications['no_bugs'] : 0;
				$no_bugs_comment = isset($notifications['no_bugs_comment']) ? $notifications['no_bugs_comment'] : 0;
				?>
				<input type="checkbox" name="no_tasks" id="no_tasks" value="1" <?php if ( $no_tasks == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e('Disable all task notification emails.', 'projectopia-core'); ?>
				<br />
				<input type="checkbox" name="no_tasks_comment" id="no_tasks_comment" value="1" <?php if ( $no_tasks_comment == 1 ) { echo 'checked="checked"'; } ?> <?php if ( $no_tasks == 1 ) { echo 'disabled'; } ?> /> <?php esc_html_e('Only notify me if a task has a new comment added.', 'projectopia-core'); ?>
				<br />
				<?php if ( pto_has_addon_active_license( 'pto_st', 'tickets' ) ) { ?>
					<p><strong><?php esc_html_e('Support Tickets', 'projectopia-core'); ?></strong></p>
					<input type="checkbox" name="no_tickets" id="no_tickets" value="1" <?php if ( $no_tickets == 1 ) { echo 'checked="checked"'; } ?>  /> <?php esc_html_e('Disable all ticket notification emails.', 'projectopia-core'); ?>
					<br />
					<input type="checkbox" name="no_tickets_comment" id="no_tickets_comment" value="1" <?php if ( $no_tickets_comment == 1 ) { echo 'checked="checked"'; } ?> <?php if ( $no_tickets == 1 ) { echo 'disabled'; } ?> /> <?php esc_html_e('Only notify me if a ticket has a new comment added.', 'projectopia-core'); ?>
					<br />
				<?php 
				}
				include_once(ABSPATH.'wp-admin/includes/plugin.php');
				if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) { ?>
					<p><strong><?php esc_html_e('Bugs', 'projectopia-core'); ?></strong></p>
					<input type="checkbox" name="no_bugs" id="no_bugs" value="1" <?php if ( $no_bugs == 1 ) { echo 'checked="checked"'; } ?>  /> <?php esc_html_e('Disable all bug notification emails.', 'projectopia-core'); ?>
					<br />
					<input type="checkbox" name="no_bugs_comment" id="no_bugs_comment" value="1" <?php if ( $no_bugs_comment == 1 ) { echo 'checked="checked"'; } ?> <?php if ( $no_bugs == 1 ) { echo 'disabled'; } ?> /> <?php esc_html_e('Only notify me if a bug has a new comment added.', 'projectopia-core'); ?>
					<br />
				<?php } ?>
				<input style="width:100%" type="hidden" id="client_type" name="client_type" value="<?php echo esc_attr( $client_type ); ?>" />
				<input style="width:100%" type="hidden" id="client_object" name="client_object" value="<?php echo esc_attr( $assigned ); ?>" />
				<input style="width:100%" type="hidden" id="client_user_id" name="client_user_id" value="<?php echo esc_attr( $user->ID ); ?>" />
				<br />
				<input type="submit" id="client_settings_submit" class="cqpim_button font-white bg-violet rounded_2 op" value="<?php esc_html_e('Update Settings', 'projectopia-core'); ?>" />
				<div class="clear"></div>
				<br />
				<div id="settings_messages"></div>
			</form>
		</div>
	<?php } ?>	
</div>