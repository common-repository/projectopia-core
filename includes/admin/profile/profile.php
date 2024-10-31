<?php
add_action( 'admin_menu' , 'register_pto_profile_page', 29 ); 
function register_pto_profile_page() {
	$mypage = add_submenu_page( 
				'pto-dashboard',
				__('My Profile', 'projectopia-core'),      
				'<span class="pto-sm-hidden">' . esc_html__('My Profile', 'projectopia-core') . '</span>',          
				'cqpim_team_edit_profile',          
				'pto-manage-profile',       
				'pto_team_profile'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_team_profile() { 
	$user = wp_get_current_user(); 
	$assigned = pto_get_team_from_userid( $user );
	$team_details = get_post_meta( $assigned, 'team_details', true );
	$team_avatar = get_post_meta( $assigned, 'team_avatar', true );
	?>
	<div class="wrap" id="cqpim-permissions">
		<div id="main-container">
			<input type="hidden" id="team_id" value="<?php echo esc_attr( $assigned ); ?>" />
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-pencil-square-o font-black" aria-hidden="true"></i>
						<span class="caption-subject font-black sbold"><?php esc_html_e('My Profile', 'projectopia-core'); ?> </span>
					</div>
				</div>
				<div class="tabContentInfo">
                    <div class="nav-tabs-panel">
                        <div class="tabMenuWrapper">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="editDetails-tab" data-toggle="tab" href="#editDetails" role="tab" aria-controls="editDetails" aria-selected="true"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php esc_html_e('Details', 'projectopia-core'); ?></a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="editPhoto-tab" data-toggle="tab" href="#editPhoto" role="tab" aria-controls="editPhoto" aria-selected="true"><i class="fa fa-camera" aria-hidden="true"></i> <?php esc_html_e('My Photo', 'projectopia-core'); ?></a>
                                </li>
								<li class="nav-item" role="presentation">
                                    <a class="nav-link" id="changePassword-tab" data-toggle="tab" href="#changePassword" role="tab" aria-controls="changePassword" aria-selected="true"><i class="fa fa-lock" aria-hidden="true"></i> <?php esc_html_e('Change Password', 'projectopia-core'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tabContentWrapper">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active show" id="editDetails" role="tabpanel" aria-labelledby="editDetails-tab">
                                <div class="tab-pane-body">
									<div class="row">
									    <div class="col-6">
									        <div class="form-group">
												<label for="team_name"><?php esc_html_e('Full Name', 'projectopia-core'); ?></label>
												<div class="input-group">
													<input type="text" name="team_name" id="team_name" class="form-control input" value="<?php echo isset( $team_details['team_name'] ) ? esc_attr( $team_details['team_name'] ) : ''; ?>" required="required" />
												</div>
									        </div>
										</div>
										<div class="col-6">
										    <div class="form-group">
										    	<label for="team_email"><?php esc_html_e('Email Address', 'projectopia-core'); ?></label>
										    	<div class="input-group">
													<input type="text" name="team_email" id="team_email" class="form-control input" value="<?php echo isset( $team_details['team_email'] ) ? esc_attr( $team_details['team_email'] ) : ''; ?>" required="required" />
												</div>
											</div>
										</div>
									</div>
									<div class="row">
									    <div class="col-6">
									        <div class="form-group">
												<label for="team_telephone"><?php esc_html_e('Telephone', 'projectopia-core'); ?></label>
												<div class="input-group">
													<input type="text" name="team_telephone" id="team_telephone" class="form-control input" value="<?php echo isset( $team_details['team_telephone'] ) ? esc_attr( $team_details['team_telephone'] ) : ''; ?>" required="required" />
												</div>
									        </div>
										</div>
										<div class="col-6">
										    <div class="form-group">
										    	<label for="team_job"><?php esc_html_e('Job Title', 'projectopia-core'); ?></label>
										    	<div class="input-group">
													<input type="text" name="team_job" id="team_job" class="form-control input" value="<?php echo isset( $team_details['team_job'] ) ? esc_attr( $team_details['team_job'] ) : ''; ?>" required="required" />
												</div>
											</div>
										</div>
									</div>
									<button class="pto_update_details piaBtn btn btn-primary mb-2" data-type="personal"><?php esc_html_e('Update Contact Details', 'projectopia-core'); ?></button>
								</div>
                            </div>
                            <div class="tab-pane fade" id="editPhoto" role="tabpanel" aria-labelledby="editPhoto-tab">
                                <div class="tab-pane-body">
									<div class="cqpim_upload_wrapper">
										<div class="form-group">
    										<label for="attachments"><?php esc_html_e('Upload new Photo', 'projectopia-core'); ?></label>
											<input type="file" class="form-control-file cqpim-file-upload-avatar" name="async-upload" id="attachments" />
										</div>
										<div id="upload_attachments"></div>
										<input type="hidden" name="image_id" id="upload_attachment_ids">
									</div>
									<div id="pto_avatar_preview_cont" style="display: none;margin-bottom: 15px;margin-top: 20px;">
										<div class="image-caption"><?php esc_html_e('New Photo Preview', 'projectopia-core'); ?></div>
										<div id="pto_avatar_preview"></div>
									</div>
									<?php if ( ! empty( $team_avatar ) ) { ?>
										<div id="pto_avatar_current_cont" style="margin-bottom: 15px;margin-top: 20px;">
											<div class="image-caption"><?php esc_html_e( 'Current Photo', 'projectopia-core' ); ?></div>
											<div id="pto_avatar_current"><?php echo wp_get_attachment_image( $team_avatar, 'thumbnail', false, '' ); ?></div>
										</div>
									<?php } ?>
									<button class="pto_update_details piaBtn btn btn-primary mb-2" data-type="photo"><?php esc_html_e('Update Photo', 'projectopia-core'); ?></button>
									<?php if ( ! empty( $team_avatar ) ) { ?>
										<button class="pto_remove_current_photo piaBtn btn btn-primary mb-2 redColor" data-type="photo"><?php esc_html_e('Remove Photo', 'projectopia-core'); ?></button>
									<?php } ?>
								</div>
                            </div>
							<div class="tab-pane fade" id="changePassword" role="tabpanel" aria-labelledby="changePassword-tab">
                                <div class="tab-pane-body">
									<div class="row">
									    <div class="col-6">
									        <div class="form-group">
												<label for="password"><?php esc_html_e('New Password', 'projectopia-core'); ?></label>
												<div class="input-group">
													<input type="password" name="password" id="password" class="form-control input" value="" required="required" />
												</div>
									        </div>
										</div>
										<div class="col-6">
										    <div class="form-group">
										    	<label for="password2"><?php esc_html_e('Repeat Password', 'projectopia-core'); ?></label>
										    	<div class="input-group">
													<input type="password" name="password2" id="password2" class="form-control input" value="" required="required" />
												</div>
											</div>
										</div>
									</div>
									<button class="pto_update_details piaBtn btn btn-primary mb-2" data-type="password"><?php esc_html_e('Update Password', 'projectopia-core'); ?></button>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
<?php }