<?php
function pto_team_details_metabox_callback( $post ) {
 	wp_nonce_field( 'team_details_metabox', 'team_details_metabox_nonce' );

	$team_details = get_post_meta($post->ID, 'team_details', true);
	$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
	$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
	$team_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
	$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
	$team_perms = isset($team_details['team_perms']) ? $team_details['team_perms'] : '';
	if ( is_array($team_perms) ) { $team_perms = $team_perms[0]; }
	$team_user = isset($team_details['user_id']) ? $team_details['user_id'] : '';
	$team_user_object = get_user_by('id', $team_user);
	$user_taken = isset($team_details['user_taken']) ? $team_details['user_taken'] : '';
	$email_taken = isset($team_details['email_exists']) ? $team_details['email_exists'] : '';
	?>
	<div class="form-group">
		<label for="team_name"><?php esc_html_e('Name', 'projectopia-core'); ?></label>
		<div class="input-group">
			<input type="text" id="team_name" name="team_name" class="form-control input" value="<?php echo esc_attr( $team_name ); ?>" required />
		</div>
	</div>
	<div class="form-group">
		<label for="team_email"><?php esc_html_e('Email', 'projectopia-core'); ?></label>
		<div class="input-group">
			<input type="email"  id="team_email" name="team_email" class="form-control input" value="<?php echo esc_attr( $team_email ); ?>" required />
		</div>
	</div>
	<div class="form-group">
		<label for="team_telephone"><?php esc_html_e('Telephone', 'projectopia-core'); ?></label>
		<div class="input-group">
			<input type="text"  id="team_telephone" name="team_telephone" class="form-control input" value="<?php echo esc_attr( $team_telephone ); ?>" />
		</div>
	</div>
	<div class="form-group">
		<label for="team_job"><?php esc_html_e('Job Title', 'projectopia-core'); ?></label>
		<div class="input-group">
			<input type="text"  id="team_job" name="team_job" class="form-control input" value="<?php echo esc_attr( $team_job ); ?>" />
		</div>
	</div>
	<?php if ( current_user_can('cqpim_grant_admin_role') ) { ?>
		<p><?php esc_html_e('Permissions Level', 'projectopia-core'); ?></p>
		<?php if ( $team_perms == 'administrator' || ! empty($team_user_object->roles) && in_array('administrator', $team_user_object->roles) ) { ?>
			<p><?php esc_html_e('This Team Member is a WordPress Administrator, you cannot change their role here.', 'projectopia-core'); ?></p>
		<?php } elseif ( ! current_user_can('cqpim_grant_admin_role') ) { ?>
			<p><?php esc_html_e('You do not have permission to edit roles.', 'projectopia-core'); ?></p>
		<?php } else { ?>
			<div class="form-group">
				<div class="input-group">
					<select id="team_perms" name="team_perms" class="form-control input">
						<?php $plugin_roles = get_option('cqpim_roles');
						foreach ( $plugin_roles as $plugin_role ) {
							if ( $plugin_role == 'cqpim_admin' ) { ?>
								<option value="<?php echo esc_attr( $plugin_role ); ?>" <?php if ( $team_perms == $plugin_role ) { echo 'selected="selected"'; } ?>>PTO Admin</option>				
							<?php } else {
								$plugin_role_machine = 'cqpim_' . $plugin_role;
								$role_name = str_replace('_', ' ', $plugin_role_machine);
								$role_name = str_replace('cqpim', 'PTO', $role_name);
								$role_name = ucwords($role_name); ?>			
								<option value="<?php echo esc_attr( $plugin_role_machine ); ?>" <?php if ( $team_perms == $plugin_role_machine ) { echo 'selected="selected"'; } ?>><?php echo esc_html( $role_name ); ?></option>
						<?php }                     
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>
	<?php } else { 
		$roles = isset( $team_user_object->roles ) ? $team_user_object->roles : array();
		if ( in_array( 'administrator', $roles ) ) {
			echo '<input type="hidden" name="team_perms" value="administrator" />';
		}
	} ?>
	<?php if ( $email_taken ) {
		$team_details = get_post_meta( $post->ID, 'team_details', true ); 
		unset( $team_details['user_taken'] );
		update_post_meta($post->ID, 'team_details', $team_details); ?>
		<div class="cqpim-alert cqpim-alert-danger alert-display"><?php esc_html_e('EMAIL UPDATE FAILED: There is already a user with that email address, please try a different one.', 'projectopia-core'); ?></div>
	<?php } ?>
	<?php if ( current_user_can( 'publish_cqpim_teams' ) ) { ?>
		<a class="piaBtn btn btn-primary btn-block mt-0 save" href="#"><?php esc_html_e( 'Update Team Member', 'projectopia-core' ); ?></a>
	<?php } ?>
	<?php if ( $team_user && current_user_can( 'cqpim_reset_team_passwords' ) && $team_perms != 'administrator' ) { ?>
		<a class="piaBtn btn btn-primary btn-block mt-2 reset-password" href="#"><?php esc_html_e('Reset User\'s Password', 'projectopia-core'); ?></a>
		<div id="password_reset_container" style="display:none">
			<div id="password_reset">
				<div style="padding:12px">
					<h3><?php esc_html_e('Reset Password', 'projectopia-core'); ?></h3>
					<p style="font-size: 13px;"><?php esc_html_e('If you would like to reset the user\'s password, please enter and confirm the new password below. This will be encrypted and saved to the database, you can however choose to send an email with the new password before the encryption takes place.', 'projectopia-core'); ?></p>
					<div class="form-group">
						<div class="input-group">
							<input type="password" id="new_password" class="form-control input pass" name="new_password" placeholder="<?php esc_attr_e('Enter new password', 'projectopia-core'); ?>" />
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<input type="password" id="confirm_password" class="form-control input pass" name="confirm_password" placeholder="<?php esc_attr_e('Confirm new password', 'projectopia-core'); ?>" />
						</div>
					</div>
					<div class="pto-inline-item-wrapper">
						<input type="checkbox" id="send_new_password" name="send_new_password" value="1" /> <?php esc_html_e('Send the user\'s new password by email', 'projectopia-core'); ?>
					</div>
					<input class="pass" type="hidden" id="pass_type" name="pass_type" value="team" />
					<div id="password_messages"></div>
					<button class="cancel-colorbox piaBtn btn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
					<button id="reset_pass_ajax" class="btn piaBtn right" value="<?php echo esc_attr( $team_user ); ?>"><?php esc_html_e('Reset Password', 'projectopia-core'); ?></button><div class="ajax_spinner" style="display: none;"></div>
				</div>
			</div>
		</div>
	<?php }
}

add_action( 'save_post_cqpim_teams', 'save_pto_team_details_metabox_data' );
function save_pto_team_details_metabox_data( $post_id ) {
	if ( ! isset( $_POST['team_details_metabox_nonce'] ) ) {
	    return $post_id;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['team_details_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'team_details_metabox' ) ) {
	    return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$team_details = get_post_meta( $post_id, 'team_details', true );
	if ( ! $team_details ) {
		$team_details = array();
	}

	if ( isset( $_POST['team_name'] ) ) {
		$team_details['team_name'] = sanitize_text_field( wp_unslash( $_POST['team_name'] ) );
	}

	if ( isset( $_POST['team_email'] ) ) {
		$team_details['team_email'] = sanitize_email( wp_unslash( $_POST['team_email'] ) );
	}

	if ( isset( $_POST['team_telephone'] ) ) {
		$team_details['team_telephone'] = sanitize_text_field( wp_unslash( $_POST['team_telephone'] ) );
	}
	if ( isset( $_POST['team_job'] ) ) {
		$team_details['team_job'] = sanitize_text_field( wp_unslash( $_POST['team_job'] ) );
	}

	if ( isset( $_POST['team_perms'] ) ) {
		$team_details['team_perms'] = sanitize_text_field( wp_unslash( $_POST['team_perms'] ) );
	}

	if ( empty( $_POST['team_perms'] ) ) {
		$team_details['team_perms'] = 'cqpim_user';
	}

	if ( isset( $team_details['user_id'] ) ) {
		$wp_user_id = $team_details['user_id'];
		$user = get_user_by( 'id',  $wp_user_id );
	} else {
		$user = get_user_by( 'email', $team_details['team_email'] );
	}
	
	if ( ! $user ) {
		if ( $team_details['team_email'] ) {
			$login = $team_details['team_email'];
			$passw = pto_random_string(10);
			$email = $team_details['team_email'];
			if ( ! username_exists( $login ) && ! email_exists( $email ) ) {
				unset( $team_details['user_taken'] );
				$user_id = wp_create_user( $login, $passw, $email );
				$user = new WP_User( $user_id );
				$user->set_role( $team_details['team_perms'] );
				$team_details['user_id'] = $user_id;
				update_post_meta( $post_id, 'team_details', $team_details );
				send_pto_team_email( $post_id, $passw );
				$user_data = array(
					'ID'           => $user_id,
					'display_name' => $team_details['team_name'],
					'first_name'   => $team_details['team_name'],
				);
				wp_update_user($user_data);
			} else {
				$team_details['user_taken'] = true;
				update_post_meta($post_id, 'team_details', $team_details);
			}
		}
	} else {
		if ( ! empty( $team_details['team_email'] ) ) {
			$login = $team_details['team_email'];
			$email = $team_details['team_email'];
			if ( ! email_exists( $email ) ) {
				unset( $team_details['user_taken'] );
				$user_data = array(
					'ID'           => $user->ID,
					'display_name' => $team_details['team_name'],
					'first_name'   => $team_details['team_name'],
					'user_email'   => $team_details['team_email'],
				);

				wp_update_user( $user_data );
				update_post_meta( $post_id, 'team_details', $team_details );
			} else {
				$team_details['user_taken'] = true;
                $team_details['user_id'] = $user->ID;
				update_post_meta( $post_id, 'team_details', $team_details );
			}
		} else {
			$user_data = array(
				'ID'           => $user->ID,
				'display_name' => $team_details['team_name'],
				'first_name'   => $team_details['team_name'],
			);

			wp_update_user( $user_data );
			update_post_meta( $post_id, 'team_details', $team_details );
		}

		pto_update_user_role_meta( $team_details['team_perms'], $user );
	}

	$title = get_the_title( $post_id );
	$name_token = '%%NAME%%';
	$team_details = get_post_meta( $post_id, 'team_details', true );
	$team_name = $team_details['team_name'];
	$title = str_replace( $name_token, $team_name, $title );
	$client_updated = array(
		'ID'         => $post_id,
		'post_title' => $title,
		'post_name'  => $post_id,
	);

	if ( ! wp_is_post_revision( $post_id ) ) {
		remove_action( 'save_post_cqpim_teams', 'save_pto_team_details_metabox_data' );
		wp_update_post( $client_updated );
		add_action( 'save_post_cqpim_teams', 'save_pto_team_details_metabox_data' );
	}
	
	$team_details = get_post_meta( $post_id, 'team_details', true );
	$title = $team_details['team_name'];
	$slug = $title;
	$slug = strtolower( $slug );
	$slug = preg_replace( "/[^a-z0-9_\s-]/", "", $slug );
	$slug = preg_replace( "/[\s-]+/", " ", $slug );
	$slug = preg_replace( "/[\s_]/", "-", $slug );
	$client_updated = array(
		'ID'         => $post_id,
		'post_title' => $title,
		'post_name'  => $slug,
	);

	if ( ! wp_is_post_revision( $post_id ) ) {
		remove_action( 'save_post_cqpim_teams', 'save_pto_team_details_metabox_data' );
		wp_update_post( $client_updated );
		add_action( 'save_post_cqpim_teams', 'save_pto_team_details_metabox_data' );
	}
}

function pto_update_user_role_meta( $role, $user ) {
	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/user.php' );
	}

	$system_roles = get_editable_roles();
	foreach ( $system_roles as $key => $system_role ) {
		if ( strpos( $key, 'cqpim_' ) !== false ) {
			$user->remove_role( $key );
		}
	}

	if ( ! in_array( 'administrator', $user->roles ) ) {
		$user->set_role( $role );
	}
}