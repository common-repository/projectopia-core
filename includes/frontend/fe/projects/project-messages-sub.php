<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$p_title = get_the_title();
$p_title = str_replace('Private:', '', $p_title);
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	/* translators: %1$s: Project ID, %2$s: Project Title */
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Messages Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('Project Messages', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<?php
			$project_messages = get_post_meta($post->ID, 'project_messages', true);
			if ( ! $project_messages ) {
				echo '<p style="padding:15px;">' . esc_html__('No messages to show...', 'projectopia-core') . '</p>';
			} else {
				echo '<br /><div style="max-height:500px; overflow:auto">';
				echo '<ul class="project_summary_progress" style="margin:0">	';
				$project_messages = array_reverse($project_messages);
				foreach ( $project_messages as $key => $message ) { 
					$user = get_user_by('id', $message['author']);
					$email = $user->user_email;
					$size = 80; 
					if ( $message['visibility'] != 'internal' ) {
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
									<?php echo wp_kses_post( $message['by'] ); ?>
								</div>
								<div class="clear"></div>
								<div class="timeline-update font-grey-cascade"><?php echo wp_kses_post( wpautop($message['message']) ); ?></div>
								<div class="clear"></div>
								<div class="timeline-date font-grey-cascade"><?php echo esc_html( wp_date(get_option('cqpim_date_format') . ' H:i', $message['date']) ); ?></div>
							</div>
							<div class="clear"></div>
						</div>
					</li>
					<?php }
					}
					echo '</ul>';
				echo '</div>';      
			} ?>
			<a href="#" id="add_message_trigger" class="cqpim_button right font-white bg-blue op mt-20 rounded_2"><?php esc_html_e('Send Message', 'projectopia-core'); ?></a>
			<div class="clear"></div>
			<div style="display:none">
				<div id="add_message">
					<div style="padding:12px; text-align:left">
						<h3><?php esc_html_e('Send Message', 'projectopia-core'); ?></h3>
						<input type="hidden" id="message_who" value="client" />
						<input type="hidden" id="add_message_visibility" name="add_message_visibility" value="all" />
						<input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr( $post->ID ); ?>" />
						<p><strong><?php esc_html_e('Message', 'projectopia-core'); ?></strong></p>
						<textarea style="width:95%; height:300px;min-width:400px" id="add_message_text" name="add_message_text"></textarea>
						<br />
						<div id="message_messages"></div>
						<button id="add_message_ajax" class="cqpim_button right font-green border-green op mt-20"><?php esc_html_e('Send Message', 'projectopia-core'); ?> <div id="ajax_spinner_message" class="ajax_loader" style="display:none"></div></button>
						<div class="clear"></div>
					</div>
				</div>
			</div>	
		</div>
		</div>
	</div>
</div>