<?php
function pto_contact_details_metabox_callback( $post ) {
 	wp_nonce_field( 'contact_details_metabox', 'contact_details_metabox_nonce' );

	$client_details = get_post_meta($post->ID, 'client_details', true);
	$notifications = get_post_meta($post->ID, 'client_notifications', true);
	$client_ref = isset($client_details['client_ref']) ? $client_details['client_ref'] : '';
	$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_contact = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
	$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
	$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$client_user = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$user_taken = isset($client_details['user_taken']) ? $client_details['user_taken'] : '';
	$email_taken = isset($client_details['email_exists']) ? $client_details['email_exists'] : '';
	$team_member = get_post_meta($post->ID, 'team_member', true);
	$avatar = get_option( 'cqpim_disable_avatars' );    
	?>
	<div style="text-align: center;">
		<?php if ( empty( $avatar ) ) { ?>
			<div class="cqpim_avatar">
				<?php echo get_avatar( $client_user, 80, '', false, array( 'force_display' => true ) ); ?>
			</div>
		<?php } ?>
	</div>
	<?php 

	do_action( 'pto_client_fields_start', $post->ID, $client_details );

	pto_generate_fields( array(
		'label'    => __( 'Client Number:', 'projectopia-core' ),
		'id'       => 'client_ref',
		'value'    => ( $client_ref ) ? $client_ref : $post->ID,
		'required' => true,
	) ); 
	
	pto_generate_fields( array(
		'label'    => __( 'Company Name:', 'projectopia-core' ),
		'id'       => 'client_company',
		'value'    => $client_company,
		'required' => true,
	) );
	
	pto_generate_fields( array(
		'label'    => __( 'Main Contact Name:', 'projectopia-core' ),
		'id'       => 'client_contact',
		'value'    => $client_contact,
		'required' => true,
	) );

	pto_generate_fields( array(
		'type'  => 'textarea',
		'label' => __( 'Client Address:', 'projectopia-core' ),
		'id'    => 'client_address',
		'value' => $client_address,
	) );
	
	do_action( 'pto_client_fields_after_address', $post->ID, $client_details );

	pto_generate_fields( array(
		'label'    => __( 'Client Postcode:', 'projectopia-core' ),
		'id'       => 'client_postcode',
		'value'    => $client_postcode,
		'required' => true,
	) );

	pto_generate_fields( array(
		'label'    => __( 'Client Telephone:', 'projectopia-core' ),
		'id'       => 'client_telephone',
		'value'    => $client_telephone,
		'required' => true,
	) );

	pto_generate_fields( array(
		'type'     => 'email',
		'label'    => __( 'Client Email:', 'projectopia-core' ),
		'id'       => 'client_email',
		'value'    => $client_email,
		'required' => true,
	) );

	do_action( 'pto_client_fields_end', $post->ID, $client_details );

	if ( $user_taken || $email_taken ) {
		$client_details = get_post_meta( $post->ID, 'client_details', true ); 
		unset( $client_details['user_taken'] );
		update_post_meta( $post->ID, 'client_details', $client_details ); ?>
		<div class="cqpim-alert cqpim-alert-danger sbold alert-display"><?php esc_html_e( 'EMAIL UPDATE FAILED: There is already a user with that email address, please try a different one.', 'projectopia-core' ); ?></div>
	<?php } 
	
	if ( current_user_can( 'publish_cqpim_clients' ) ) {
		$pending = get_post_meta($post->ID, 'pending', true); 
		if ( ! empty( $pending ) ) {
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__( 'This client is pending approval. Update the client to activate the account and send login details.', 'projectopia-core' ) . '</div>';
		}?>
		<?php $screen = get_current_screen(); ?>
		<a class="piaBtn btn btn-primary btn-block mt-0 save" href="#"><?php $screen->action == 'add' ? esc_html_e( 'Add Client', 'projectopia-core') : esc_html_e( 'Update Client', 'projectopia-core' ); ?></a>
	<?php }
	
	if ( $client_user && current_user_can( 'cqpim_reset_client_passwords' ) ) { ?>
		<a class="piaBtn btn btn-primary btn-block btn-orange reset-password" href="#"><?php esc_html_e( 'Reset Client\'s Password', 'projectopia-core' ); ?></a>
		<div id="password_reset_container" style="display: none;">
			<div id="password_reset">
				<div style="padding: 12px;">
					<h3><?php esc_html_e( 'Reset Password', 'projectopia-core' ); ?></h3>
					<p><?php esc_html_e( 'If you would like to reset the client\'s password, please enter and confirm the new password below.', 'projectopia-core' ); ?> <br />
					<?php esc_html_e( 'This will be encrypted and saved to the database, you can however choose to send an email with the new password before the encryption takes place.', 'projectopia-core' ); ?></p>
					<?php 

					pto_generate_fields( array(
						'type'        => 'password',
						'id'          => 'new_password',
						'class'       => 'pass',
						'placeholder' => __( 'Enter new password', 'projectopia-core' ),
						'attribute'   => 'autocomplete="off"',
					) );
					
					pto_generate_fields( array(
						'type'        => 'password',
						'id'          => 'confirm_password',
						'class'       => 'pass',
						'placeholder' => __( 'Confirm new password', 'projectopia-core' ),
						'attribute'   => 'autocomplete="off"',
					) );

					pto_generate_fields( array(
						'type'  => 'hidden',
						'id'    => 'pass_type',
						'class' => 'pass',
						'value' => 'client',
					) );
					
					pto_generate_fields( array(
						'type'    => 'checkbox',
						'id'      => 'send_new_password',
						'label'   => __( 'Send the client\'s new password by email', 'projectopia-core' ),
						'checked' => 'client',
					) );

					?>
					<div id="password_messages"></div>
					<button class="cancel-colorbox piaBtn redColor mt-1"><?php esc_html_e( 'Cancel', 'projectopia-core' ); ?></button>
					<button id="reset_pass_ajax" class="piaBtn mt-1 right" value="<?php echo esc_attr( $client_user ); ?>"><?php esc_html_e( 'Reset Password', 'projectopia-core' ); ?></button>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php	
}

add_action( 'save_post_cqpim_client', 'save_pto_contact_details_metabox_data' );
function save_pto_contact_details_metabox_data( $post_id ) {
	if ( ! isset( $_POST['contact_details_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['contact_details_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'contact_details_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	} 

	// Check the post type is cqpim_client or not.
	$cqpim_client_cpt = get_post_type( $post_id );
	if ( empty( $cqpim_client_cpt ) || 'cqpim_client' !== $cqpim_client_cpt ) {
		return $post_id;
	} 
	$looper = get_post_meta($post_id, 'looper', true);
	$looper = $looper ? $looper : 0;
	if ( time() - $looper > 5 ) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_details = $client_details && is_array($client_details) ? $client_details : array();
		$client_details['client_ref'] = isset($_POST['client_ref']) ? sanitize_text_field( wp_unslash( $_POST['client_ref'] ) ) : '';
		$client_details['client_company'] = isset($_POST['client_company']) ? sanitize_text_field( wp_unslash( $_POST['client_company'] ) ) : '';
		$client_details['client_contact'] = isset($_POST['client_contact']) ? sanitize_text_field( wp_unslash( $_POST['client_contact'] ) ) : '';
		$client_details['client_address'] = isset($_POST['client_address']) ? sanitize_textarea_field( wp_unslash( $_POST['client_address'] ) ) : '';
		$client_details['client_postcode'] = isset($_POST['client_postcode']) ? sanitize_text_field( wp_unslash( $_POST['client_postcode'] ) ) : '';
		$client_details['client_telephone'] = isset($_POST['client_telephone']) ? sanitize_text_field( wp_unslash( $_POST['client_telephone'] ) ) : '';
		$client_details['client_email'] = isset($_POST['client_email']) ? sanitize_email( wp_unslash( $_POST['client_email'] ) ) : '';
		$wp_user_id = isset($client_details['user_id']) ? sanitize_text_field( wp_unslash( $client_details['user_id'] ) ) : '';
		$user = get_user_by( 'id', $wp_user_id );
		if ( empty($user->ID) ) {
			if ( $client_details['client_email'] ) {
				$login = $client_details['client_email'];
				$passw = pto_random_string(10);
				$email = $client_details['client_email'];
				if ( ! username_exists( $login ) && ! email_exists( $email ) ) {
					unset($client_details['user_taken']);
					update_post_meta($post_id, 'pending', false);
					// Remove this user_register action before register new user.
					remove_action( 'user_register', 'pto_create_user_as_client_from_user_page', 10, 1);
					$user_id = wp_create_user( $login, $passw, $email );
					$user = new WP_User( $user_id );
					$user->set_role( 'cqpim_client' );
					$client_details['user_id'] = $user_id;
					$client_ids = array();
					$client_ids[] = $user_id;
					update_post_meta($post_id, 'client_ids', $client_ids);
					update_post_meta($post_id, 'client_details', $client_details);
					$auto_welcome = get_option('auto_welcome');
					if ( $auto_welcome == 1 ) {
						send_pto_welcome_email($post_id, $passw);
					}
					$user_data = array(
						'ID'           => $user_id,
						'display_name' => $client_details['client_contact'],
						'first_name'   => $client_details['client_contact'],
					);
					wp_update_user($user_data);
				} else {
					wp_die('You cannot use that email address because there is already a user in the system with that address. You should convert the existing user to a PTO Client in the WP Users page');
				}
			}
		} else {
			$client_details_old = get_post_meta($post_id, 'client_details', true);
			if ( $client_details['client_email'] != $client_details_old['client_email'] ) {
				$login = $client_details['client_email'];
				$email = $client_details['client_email'];   
				if ( ! email_exists( $email ) ) {
					unset($client_details['user_taken']);   
					$user_data = array(
						'ID'           => $user->ID,
						'display_name' => $client_details['client_contact'],
						'first_name'   => $client_details['client_contact'],
						'user_email'   => $client_details['client_email'],
					);
					wp_update_user($user_data); 
					update_post_meta($post_id, 'client_details', $client_details);
				} else {
					$client_details['client_email'] = $client_details_old['client_email'];
					$client_details['user_taken'] = true;
					update_post_meta($post_id, 'client_details', $client_details);
				}
			} else {
				$user_data = array(
					'ID'           => $user->ID,
					'display_name' => $client_details['client_contact'],
					'first_name'   => $client_details['client_contact'],
				);
				wp_update_user($user_data); 
				update_post_meta($post_id, 'client_details', $client_details);              
			}
		}

		do_action( 'pto_save_client_data', $post_id, $client_details );

		$title = get_the_title($post_id);
		$company_token = '%%CLIENT_COMPANY%%';
		$client_token = '%%CLIENT_NUMBER%%';
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_company = isset( $client_details['client_company'] ) ? $client_details['client_company'] : '';
		$client_ref = $client_details['client_ref'];
		$title = str_replace($company_token, $client_company, $title);
		$title = str_replace($client_token, $client_ref, $title);       
		$client_updated = array(
			'ID'         => $post_id,
			'post_title' => $title,
			'post_name'  => $post_id,
		);  
		if ( ! wp_is_post_revision( $post_id ) ) {
			remove_action('save_post_cqpim_client', 'save_pto_contact_details_metabox_data');
			wp_update_post( $client_updated );
			add_action('save_post_cqpim_client', 'save_pto_contact_details_metabox_data');
		}
		update_post_meta($post_id, 'looper', time());       
	}
}