	<br />
	<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-users font-light-violet" aria-hidden="true"></i>
			<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Contacts', 'projectopia-core'); ?></span>
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
		'page' => __('Client Dashboard Contacts Page', 'projectopia-core'),
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	$client_settings = get_option('allow_client_users');
	if ( $client_settings == 1 ) { ?>
		<p><?php esc_html_e('If you would like to give multiple users at your organisation access to your client dashboard, you can do so here.', 'projectopia-core'); ?></p>
		<?php 
		$client_contacts = get_post_meta($assigned, 'client_contacts', true);
		if ( empty($client_contacts) ) {
			echo '<p>' . esc_html__('You have not added any additional contacts', 'projectopia-core') . '</p>';
		} else {
			echo '<br />';
			foreach ( $client_contacts as $key => $contact ) {
				$user = get_user_by('id', $contact['user_id']);
				echo '<div class="team_member">';
				$value = get_option('cqpim_disable_avatars');
				if ( empty($value) ) {
					echo '<div class="cqpim_gravatar">';
						echo get_avatar( $user->ID, 80, '', false, array( 'force_display' => true ) ); 
					echo '</div>';
				} 
				echo '<div class="team_details">';
				echo '<span class="team_name block">' . esc_html( $contact['name'] ) . '</span>';
				echo '<i class="fa fa-envelope" aria-hidden="true"></i> ' . esc_html( $contact['email'] ) . '<br />';
				echo '<i class="fa fa-phone" aria-hidden="true"></i> ' . esc_html( $contact['telephone'] ) . '<br />';
				echo '</div>';
				echo '<br />';
				echo '<div class="team_delete"><button class="edit-milestone cqpim_button cqpim_small_button border-amber font-amber" value="'. esc_attr( $key ) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button><button class="delete_team cqpim_button cqpim_small_button border-red font-red" value="' . esc_attr( $key ) . '"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
				echo '<div class="clear"></div>';
				echo '</div>';              
			}
			echo '<div class="clear"></div>';
			foreach ( $client_contacts as $key => $contact ) { ?>
				<div id="contact_edit_container_<?php echo esc_attr( $key ); ?>" style="display:none">
					<div id="contact_edit_<?php echo esc_attr( $key ); ?>" class="contact_edit">
						<div style="padding:12px">
							<h3><?php esc_html_e('Edit Contact', 'projectopia-core'); ?> - <?php echo esc_html( $contact['name'] ); ?></h3>
							<label for="contact_name_<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Contact Name', 'projectopia-core'); ?></label>
							<input type="text" id="contact_name_<?php echo esc_attr( $key ); ?>" name="contact_name_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $contact['name'] ); ?>" />
							<br />
							<label for="contact_email_<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Contact Email', 'projectopia-core'); ?></label>
							<input type="text" id="contact_email_<?php echo esc_attr( $key ); ?>" name="contact_email_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $contact['email'] ); ?>" />
							<br />
							<label for="contact_telephone_<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Contact Telephone', 'projectopia-core'); ?></label>
							<input type="text" id="contact_telephone_<?php echo esc_attr( $key ); ?>" name="contact_telephone_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $contact['telephone'] ); ?>" />
							<br /><br />
							<h3><?php esc_html_e('Reset Password', 'projectopia-core'); ?></h3>
							<input class="pass" type="password" id="new_password_<?php echo esc_attr( $key ); ?>" name="new_password_<?php echo esc_attr( $key ); ?>" placeholder="<?php esc_attr_e('Enter new password', 'projectopia-core'); ?>" />
							<br />
							<input class="pass" type="password" id="confirm_password_<?php echo esc_attr( $key ); ?>" name="confirm_password_<?php echo esc_attr( $key ); ?>" placeholder="<?php esc_attr_e('Confirm new password', 'projectopia-core'); ?>" />
							<br /><br .>
							<input type="checkbox" id="send_new_password_<?php echo esc_attr( $key ); ?>" name="send_new_password_<?php echo esc_attr( $key ); ?>" value="1" /> <?php esc_html_e('Send the contact\'s new password by email', 'projectopia-core'); ?>
							<br />
							<input class="pass" type="hidden" id="pass_type_<?php echo esc_attr( $key ); ?>" name="pass_type_<?php echo esc_attr( $key ); ?>" value="contact" />
							<div id="client_team_messages_<?php echo esc_attr( $key ); ?>"></div>							
							<button class="cancel-colorbox cqpim_button font-red border-red op mt-20"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
							<button id="contact_edit_submit_<?php echo esc_attr( $key ); ?>" class="cqpim_button mt-20 font-green border-green right op contact_edit_submit" value="<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Edit Contact', 'projectopia-core'); ?><span id="ajax_spinner_contact_<?php echo esc_attr( $key ); ?>" class="ajax_loader" style="display:none"></span></button>
						</div>
					</div>
				</div>
			<?php
			}
		} 
	} if ( $client_settings == 1 ) { ?>
		<br /><br />
		<a id="add_client_team" class="cqpim_button font-white bg-violet rounded_2 op"><?php esc_html_e('Add Contact', 'projectopia-core'); ?></a>
	<?php } ?>
	<div class="clear"></div>
	<div id="add_client_team_ajax_container" style="display:none">
		<div id="add_client_team_ajax">
			<div style="padding:12px">
				<h3><?php esc_html_e('Add Contact', 'projectopia-core'); ?></h3>
				<p><?php esc_html_e('Adding a contact will create a new login and give the user access to the client dashboard.', 'projectopia-core'); ?></p>
				<label for="contact_name"><?php esc_html_e('Contact Name', 'projectopia-core'); ?></label>
				<input type="text" id="contact_name" name="contact_name" />
				<br />
				<label for="contact_email"><?php esc_html_e('Contact Email', 'projectopia-core'); ?></label>
				<input type="text" id="contact_email" name="contact_email" />
				<br />
				<label for="contact_telephone"><?php esc_html_e('Contact Telephone', 'projectopia-core'); ?></label>
				<input type="text" id="contact_telephone" name="contact_telephone" />
				<br /><br />
				<input type="checkbox" id="send_contact_details" name="send_contact_details" value="1" /> <?php esc_html_e('Send the contact login details by email', 'projectopia-core'); ?>
				<br />
				<input type="hidden" id="post_ID" name="post_id" value="<?php echo esc_attr( $assigned ); ?>" />
				<div id="client_team_messages"></div>
				<button class="cancel-colorbox cqpim_button font-red border-red op mt-20"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
				<button id="add_client_team_submit" class="cqpim_button mt-20 font-green border-green right op"><?php esc_html_e('Add Client Contact', 'projectopia-core'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display:none"></span></button>
			</div>
		</div>
	</div>
	</div>