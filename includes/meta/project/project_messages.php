<?php
/**
 * function to show the messages meta box in project edit screen.
 * 
 * @param Object $post This is post object.
 * @return void 
 */
function pto_project_messages_metabox_callback( $post ) {
	wp_nonce_field( 'project_messages_metabox', 'project_messages_metabox_nonce' );

	$project_messages = get_post_meta( $post->ID, 'project_messages', true );
	if ( ! $project_messages ) {
		echo '<p>' . esc_html_e( 'No messages to show...', 'projectopia-core' ) . '</p>';
	} else {
		$project_messages = array_reverse( $project_messages ); ?>
		<div class="projectActivities ProjectUpdatesWrapper" style="max-height:500px; overflow-y:auto">
			<ul class="project_summary_progress" style="margin:0">		
				<?php foreach ( $project_messages as $key => $message ) { 
					$user = get_user_by( 'id', $message['author'] );
					if ( empty( $user ) || ! is_object( $user ) ) {
						continue;
					}
					$email = $user->user_email;
					$size = 80;     
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
								<div class="timeline-by">
									<?php echo esc_html( $message['by'] ); ?>
									<?php if ( current_user_can( 'cqpim_edit_project_dates' ) ) { ?>
										<button class="delete_message cqpim_button cqpim_small_button bg-red border-red right op cqpim_tooltip font-white" data-id="<?php echo esc_attr( $key ); ?>"><i class="fa fa-trash"></i></button>
									<?php } ?>
								</div>
								<div class="clear"></div>
								<div class="timeline-update"><?php echo wp_kses_post( wpautop( $message['message'] )); ?></div>
								<div class="clear"></div>
								<div class="timeline-date"><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $message['date'] ) ); ?></div>
							</div>
							<div class="clear"></div>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>	
	<?php } ?>
	<button id="add_message_trigger" class="mt-20 piaBtn right"><?php esc_html_e('Send Message', 'projectopia-core'); ?></button>
	<div class="clear"></div>
	<div id="add_message_container" style="display:none">
		<div id="add_message">
			<div style="padding:12px">
				<h3 class="model_title"><?php esc_html_e('Send Message', 'projectopia-core'); ?></h3>
				<input type="hidden" id="message_who" value="admin" />

				<div class="form-group">
					<label> <?php esc_html_e('Message Visibility', 'projectopia-core'); ?> </label>
					<div class="input-group">
						<select class="form-control input customSelect" id="add_message_visibility" name="add_message_visibility">
							<option value="all"><?php esc_html_e('Visible to All', 'projectopia-core'); ?></option>
							<option value="internal"><?php esc_html_e('Internal Message (Client cannot see this)', 'projectopia-core'); ?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label> <?php esc_html_e('Message Notifications', 'projectopia-core'); ?> </label>
					<div class="input-group">
						<p>
							<input type="checkbox" id="send_to_team" name="send_to_team" /> 
								<?php esc_html_e('Send a notification to the Project Team', 'projectopia-core'); ?>
						</p>
						<p>
							<input type="checkbox" id="send_to_client" name="send_to_client" /> 
								<?php esc_html_e('Send a notification to the client', 'projectopia-core'); ?>
						</p>

					</div>
				</div>

				<div class="form-group">
					<label> <?php esc_html_e('Message', 'projectopia-core'); ?> </label>
					<div class="input-group">
						<textarea class="form-control input" style="width:100%; height:200px;min-width:400px" id="add_message_text" name="add_message_text"></textarea>
					</div>
				</div>

				<div id="message_messages"></div>
				<button id="add_message_ajax" class="mt-20 piaBtn right"><?php esc_html_e('Send Message', 'projectopia-core'); ?></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php
}