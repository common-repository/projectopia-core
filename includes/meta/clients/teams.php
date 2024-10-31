<?php
function pto_client_team_metabox_callback( $post ) {
 	wp_nonce_field( 'client_team_metabox', 'client_team_metabox_nonce' );

	$client_contacts = get_post_meta( $post->ID, 'client_contacts', true );
	$client_contacts = $client_contacts && is_array( $client_contacts ) ? $client_contacts : array();
	$client_ids = get_post_meta( $post->ID, 'client_ids', true );

	?>

	<div class="team-members mt-3">
		<?php
			if ( ! empty( $client_contacts ) ) {
				echo '<div class="row">';
				foreach ( $client_contacts as $key => $contact ) {
					//Set avatar.
					$profile_avatar = '';
					if ( empty( get_option( 'cqpim_disable_avatars') ) ) {
						$profile_avatar = get_avatar(
							$contact['user_id'],
							40,
							'',
							false,
							[
								'force_display' => true,
								'class'         => 'img-fluid',
							]
						);

						if ( empty( $profile_avatar ) ) {
							$profile_avatar = sprintf(
								'<img src="%s" alt="%s" class="img-fluid" />',
								PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
								esc_html( $contact['name'] )
							);
						}
					}

					$no_tasks = isset( $contact['notifications']['no_tasks'] ) ? $contact['notifications']['no_tasks'] : '';
					$no_tasks_comment = isset( $contact['notifications']['no_tasks_comment'] ) ? $contact['notifications']['no_tasks_comment'] : '';
					$no_tickets = isset( $contact['notifications']['no_tickets'] ) ? $contact['notifications']['no_tickets'] : '';
					$no_tickets_comment = isset( $contact['notifications'] ['no_tickets_comment']) ? $contact['notifications']['no_tickets_comment'] : '';
					$no_bugs = isset( $contact['notifications']['no_bugs'] ) ? $contact['notifications']['no_bugs'] : '';
					$no_bugs_comment = isset( $contact['notifications']['no_bugs_comment'] ) ? $contact['notifications']['no_bugs_comment'] : '';

					?>

					<div class="member-grid text-center mx-3 mb-2">
						<div class="cqpim_gravatar"><?php echo wp_kses_post( $profile_avatar ); ?></div>

						<div class="team_details">
							<h5 class="member-name py-2 team_name"> <?php echo wp_kses_post( $contact['name'] ); ?> </h5>

							<?php if ( ! empty( $contact['telephone'] ) ) { ?>
								<p class="pb-1">
									<a href="tel:<?php echo esc_html( $contact['telephone'] ); ?>">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/phone-fill.svg' ); ?>" class="icon img-fluid mr-2" />
										<?php echo esc_html( $contact['telephone'] ); ?>
									</a>
								</p>
							<?php } ?>

							<p class="pb-1">
								<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>">
									<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/mail-fill.svg' ); ?>" class="icon img-fluid mr-2" />
									<?php echo esc_html( $contact['email'] ); ?>
								</a>
							</p>

							<?php
							if ( current_user_can( 'publish_cqpim_clients' ) ) {
								echo '<div class="team_delete"><button class="edit-milestone cqpim_button cqpim_small_button border-amber font-amber" value="' . esc_attr( $key ) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>&nbsp;&nbsp;&nbsp;<button class="delete_team cqpim_button cqpim_small_button border-red font-red" value="' . esc_attr( $key ) . '"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
							}
							?>
						</div>
					</div>

					<div id="contact_edit_container_<?php echo esc_attr( $key ); ?>" style="display: none;">
						<div id="contact_edit_<?php echo esc_attr( $key ); ?>" class="contact_edit">
							<div style="padding: 12px;">
								<h3><?php esc_html_e('Edit Contact', 'projectopia-core'); ?> - <?php echo esc_html( $contact['name'] ); ?></h3>
								<?php

								pto_generate_fields( array(
									'id'    => 'contact_name_' . $key,
									'value' => $contact['name'],
									'label' => __( 'Contact Name', 'projectopia-core' ),
								) );

								pto_generate_fields( array(
									'type'  => 'email',
									'id'    => 'contact_email_' . $key,
									'value' => $contact['email'],
									'label' => __( 'Contact Email', 'projectopia-core' ),
								) );

								pto_generate_fields( array(
									'id'    => 'contact_telephone_' . $key,
									'value' => $contact['telephone'],
									'label' => __( 'Contact Telephone', 'projectopia-core' ),
								) );

								pto_generate_fields( array(
									'type'        => 'password',
									'id'          => 'new_password_' . $key,
									'label'       => __( 'Enter new password', 'projectopia-core' ),
									'class'       => 'pass',
									'placeholder' => true,
									'row_start'   => true,
									'col'         => true,
								) );

								pto_generate_fields( array(
									'type'        => 'password',
									'id'          => 'confirm_password_' . $key,
									'label'       => __( 'Confirm new password', 'projectopia-core' ),
									'class'       => 'pass',
									'placeholder' => true,
									'row_end'     => true,
									'col'         => true,
								) );

								echo '<div class="mb-2"><strong>' . esc_html__( 'Tasks:', 'projectopia-core' ) . ' </strong></div>';

								pto_generate_fields( array(
									'type'    => 'checkbox',
									'id'      => 'no_tasks_' . $key,
									'label'   => __( 'Do not send task update emails', 'projectopia-core' ),
									'checked' => 1 == $no_tasks,
								) );

								pto_generate_fields( array(
									'type'     => 'checkbox',
									'id'       => 'no_tasks_comment_' . $key,
									'label'    => __( 'Notify new comments only', 'projectopia-core' ),
									'checked'  => 1 == $no_tasks_comment,
									'disabled' => 1 == $no_tasks,
								) );

								echo '<div class="mb-2"><strong>' . esc_html__( 'Tickets:', 'projectopia-core' ) . ' </strong></div>';

								pto_generate_fields( array(
									'type'    => 'checkbox',
									'id'      => 'no_tickets_' . $key,
									'label'   => __( 'Do not send ticket update emails', 'projectopia-core' ),
									'checked' => 1 == $no_tickets,
								) );

								pto_generate_fields( array(
									'type'     => 'checkbox',
									'id'       => 'no_tickets_comment_' . $key,
									'label'    => __( 'Notify new comments only', 'projectopia-core' ),
									'checked'  => 1 == $no_tickets_comment,
									'disabled' => 1 == $no_tickets,
								) );

								if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
									echo '<div class="mb-2"><strong>' . esc_html__( 'Bugs:', 'projectopia-core' ) . ' </strong></div>';

									pto_generate_fields( array(
										'type'    => 'checkbox',
										'id'      => 'no_bugs_' . $key,
										'label'   => __( 'Do not send bug update emails', 'projectopia-core' ),
										'checked' => 1 == $no_bugs,
									) );

									pto_generate_fields( array(
										'type'     => 'checkbox',
										'id'       => 'no_bugs_comment_' . $key,
										'label'    => __( 'Notify new comments only', 'projectopia-core' ),
										'checked'  => 1 == $no_bugs_comment,
										'disabled' => 1 == $no_bugs,
									) );
								}

								pto_generate_fields( array(
									'type'  => 'hidden',
									'id'    => 'pass_type_' . $key,
									'value' => 'contact',
									'class' => 'pass',
								) );

								?>

								<div id="client_team_messages_<?php echo esc_attr( $key ); ?>"></div>							
								<button class="cancel-colorbox piaBtn redColor mt-10"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
								<button id="contact_edit_submit_<?php echo esc_attr( $key ); ?>" class="piaBtn contact_edit_submit mt-10 right" value="<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Edit Contact', 'projectopia-core'); ?></button>
							</div>
						</div>
					</div>
				<?php } ?>
				</div><?php
			} else {
				echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('This client does not have any additional contacts. If you add a new contact then they will be given access to the client dashboard. You will also be able to assign quotes/projects to individual contacts.', 'projectopia-core') . '</div>';
			}
		?>
		<div class="row px-3 justify-content-end">
			<?php if ( current_user_can( 'publish_cqpim_clients' ) ) { ?>
				<button id="add_client_team" class="mt-10 piaBtn right" value=""><?php esc_html_e( 'Add Client Contact', 'projectopia-core' ); ?></button>
			<?php } ?>
		</div>
	</div>

	<div class="clear"></div>
	<div id="add_client_team_ajax_container" style="display: none;">
		<div id="add_client_team_ajax">
			<div style="padding: 12px;">
				<h3><?php esc_html_e('Add Client Contact', 'projectopia-core'); ?></h3>
				<p><?php esc_html_e('Adding a client contact will create a new login and give the user access to the client dashboard for this client. You will also be able to assign quotes and projects to this contact.', 'projectopia-core'); ?></p>
				<?php

				pto_generate_fields( array(
					'id'    => 'contact_name',
					'label' => __( 'Contact Name', 'projectopia-core' ),
				) );

				pto_generate_fields( array(
					'type'  => 'email',
					'id'    => 'contact_email',
					'label' => __( 'Contact Email', 'projectopia-core' ),
				) );

				pto_generate_fields( array(
					'id'    => 'contact_telephone',
					'label' => __( 'Contact Telephone', 'projectopia-core' ),
				) );

				pto_generate_fields( array(
					'type'  => 'checkbox',
					'id'    => 'send_contact_details',
					'label' => __( 'Send the contact login details by email', 'projectopia-core' ),
				) );
				
				?>
				<div id="client_team_messages"></div>
				<button class="cancel-colorbox piaBtn redColor mt-10"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>			
				<button id="add_client_team_submit" class="piaBtn mt-10 right"><?php esc_html_e('Add Client Contact', 'projectopia-core'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display: none;"></span></button>
			</div>
		</div>
	</div>
	<?php
}