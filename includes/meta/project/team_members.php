<?php
/**
 * function to show the team member in meta box in project edit screen.
 * 
 * @param Object $post This is post object.
 * @return void 
 */
function pto_project_contributors_metabox_callback( $post ) {
	wp_nonce_field( 'project_contributors_metabox', 'project_contributors_metabox_nonce' );
	
	$current_user = wp_get_current_user();
	$project_contributors = get_post_meta( $post->ID, 'project_contributors', true );
	?>
	<div class="team-members mt-3">
		<div class="row">
		<?php
			if ( ! empty( $project_contributors ) ) {
				foreach ( $project_contributors as $key => $contributor ) {
					$contributor['pm'] = isset( $contributor['pm'] ) ? $contributor['pm'] : '';
					$team_details      = get_post_meta( $contributor['team_id'], 'team_details', true );
					$user_id           = isset( $team_details['user_id'] ) ? $team_details['user_id'] : '';
					$team_name         = isset( $team_details['team_name'] ) ? $team_details['team_name'] : '';
					$team_job          = isset( $team_details['team_job'] ) ? $team_details['team_job'] : '';
					$team_email        = isset( $team_details['team_email'] ) ? $team_details['team_email'] : '';
					$team_telephone    = isset( $team_details['team_telephone'] ) ? $team_details['team_telephone'] : '';

					//Set avatar.
					$profile_avatar = '';
					if ( empty( get_option( 'cqpim_disable_avatars') ) ) {
 						$profile_avatar = get_avatar( $user_id, 40, '', false, [
							'force_display' => true,
							'class'         => 'img-fluid',
						] );

						if ( empty( $profile_avatar ) ) {
							$profile_avatar = sprintf(
								'<img src="%s" alt="%s" class="img-fluid" />',
								PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
								esc_html( $team_name  )
							);
						}                   
					}
					?>
					<div class="member-grid text-center mx-3">
						<?php 
						//If user is Project Manager then add label
						if ( ! empty( $contributor['pm'] ) ) { ?>
							<div class="ppm cqpim_ribbon_left cqpim_button cqpim_small_button nolink bg-blue-sharp font-white op">
								<strong><?php esc_html_e( 'PM', 'projectopia-core'); ?></strong>
							</div>
						<?php } ?>
						<div class="cqpim_gravatar"><?php echo wp_kses_post( $profile_avatar ); ?></div>
						<div class="team_details">
							<h5 class="member-name py-2 team_name"> <?php echo esc_html( $team_name ); ?> </h5>
							<?php if ( ! empty(  $team_telephone ) ) { ?>
								<p class="pb-1">
									<a href="tel:<?php echo esc_html( $team_telephone ); ?>">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/phone-fill.svg' ); ?>" class="icon img-fluid mr-2" />
										<?php echo esc_html( $team_telephone ); ?>
									</a>
								</p>
							<?php } ?>
							<p class="pb-1">
								<a href="mailto:<?php echo esc_attr( $team_email ); ?>">
									<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/mail-fill.svg' ); ?>" class="icon img-fluid mr-2" />
									<?php echo esc_html( $team_email ); ?>
								</a>
							</p>
							<?php
							if ( current_user_can( 'cqpim_edit_project_members' ) || $current_user->ID == $user_id ) {
								printf(
									'<p class="pb-1"><input type="checkbox" class="disable_email" 
										id="dn-%1$s"  data-key="%2$s" data-team="%1$s" %s value="1"',
										esc_attr( $contributor['team_id'] ),
										esc_attr( $key ),
										checked( isset( $contributor['demail'] ) ? esc_attr( $contributor['demail'] ) : 0, 1, false )
								);
								printf(
									'<span>
										%s
										<i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="%s"></i>
									</span> </p>',
									esc_html__( 'Disable Emails', 'projectopia-core'),
									esc_html__( 'Check this box to disable task email notifications for this project.
										You will still receive notifications in the dashboard.', 'projectopia-core' )
								);
							}
							if ( current_user_can('cqpim_edit_project_members') ) {
								echo '<p class="pb-1"><input type="checkbox" class="project_manager" id="pm-' . esc_attr( $contributor['team_id'] ) . '" data-team="' . esc_attr( $contributor['team_id'] ) . '" data-key="' . esc_attr( $key ) . '" ' . checked($contributor['pm'], 1, false) . ' value="1" />';
								echo  esc_html__(' Project Manager', 'projectopia-core');
								echo "</p>";
							}
							?>
						</div>
						<div class="dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
							<button class="btn px-3" type="button"
								data-toggle="dropdown" aria-haspopup="true"
								aria-expanded="false">
								<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
									class="img-fluid" />
							</button>
							<div class="dropdown-menu">
								<?php
								if ( current_user_can( 'edit_cqpim_teams' ) ) {
									$team_url = get_edit_post_link($contributor['team_id']); ?>
									<a href="<?php echo esc_url( $team_url ); ?>" class="dropdown-item" type="button">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/edit.svg' ); ?>" alt=""
											class="img-fluid" />
											<?php esc_html_e('Edit Information', 'projectopia-core'); ?>
									</a>
								<?php
								}

								//Member delete permission.
								if ( current_user_can( 'cqpim_edit_project_members' ) ) { ?>
									<button class="dropdown-item delete_team" type="button" data-team="<?php echo esc_attr( $contributor['team_id'] ); ?>" value="<?php echo esc_attr( $key ); ?>">
										<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/delete.svg' ); ?>" alt=""
											class="img-fluid" />
										<?php esc_html_e('Remove Member', 'projectopia-core'); ?>
									</button>
								<?php } ?>
							</div>
						</div>
					</div>
				<?php }
			}

			// If no member is assign yet.
			if ( empty( $project_contributors ) ) {
				echo '<p class="p px-3">' . esc_html__('There are no team members assigned to this project', 'projectopia-core') . '</p>';
			}
		?>
		</div>
		<div class="row px-3 justify-content-end">
			<?php
			//Add team member button if user has permission.
			if ( current_user_can( 'cqpim_edit_project_members' ) ) {
				echo '<button id="add_team_member" class="mt-20 piaBtn right" value="">' . esc_html__('Add Team Member', 'projectopia-core') . '</button>';
			}
			?>
		</div>
	</div>
	<div id="add_team_member_div_container" style="display:none">
		<div id="add_team_member_div">
			<div style="padding:12px">
				<h3 class="model_title"><?php esc_html_e('Add Team Member', 'projectopia-core'); ?></h3>
				<div class="form-group">
					<label><?php esc_html_e('Adding a team member to this project will give them access
				to the project tasks and allow them to edit, assign and complete tasks.', 'projectopia-core'); ?></label>
					<label for=""> <?php esc_html_e('Select team members:', 'projectopia-core'); ?> </label>
					<div class="input-group" style="height: 250px;">
						<?php
							$project_contributors = get_post_meta($post->ID, 'project_contributors', true);
							$project_contributors = $project_contributors && is_array($project_contributors) ? $project_contributors : array();
							$args = array(
								'post_type'      => 'cqpim_teams',
								'posts_per_page' => -1,
								'post_status'    => 'private',
							);
							$team_members = get_posts($args);
							echo '<select class="form-control input customSelect" id="team_members" name="members[]" multiple="multiple">';
							if ( $team_members ) {
								foreach ( $team_members as $team_member ) {
									$team_details = get_post_meta($team_member->ID, 'team_details', true);
									$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
									$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
									if ( in_array($team_member->ID, $project_contributors) ) {
										$added = 'style="display:none"';
									} else {
										$added = '';
									}
									echo '<option value="' . esc_attr( $team_member->ID ) . '" ' . esc_attr( $added ) . '>' . esc_html( $team_name . ' - ' . $team_job ) . '</option>';
								}
							}
							echo '</select>';
						?>
					</div>
				</div>
				<div id="add_team_messages"></div>
				<div class="mt-3 d-flex align-items-center justify-content-between">
					<button id="add_team_member_ajax" class="piaBtn right " value=""><?php esc_html_e('Add Team Member', 'projectopia-core'); ?></button>
					<button class="cancel-colorbox btn btn-danger"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>					
				</div>
			</div>
		</div>
	</div>
<?php } ?>