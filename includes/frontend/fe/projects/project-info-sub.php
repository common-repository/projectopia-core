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
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Info Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('Project Information', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<?php
			$project_info = get_post_meta($post->ID, 'general_project_notes', true);
			if ( ! empty($project_info['general_project_notes']) ) {
				echo wp_kses_post( wpautop($project_info['general_project_notes']) );
			} else {
				esc_html_e('No general project information has been added.', 'projectopia-core');
			}           
			?>
		</div>
	</div>
</div>