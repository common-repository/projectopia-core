<?php
$user = wp_get_current_user();
$client_id = get_post_meta($post->ID, 'subscription_client', true);
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper ? $looper : 0;
$p_title = get_the_title(); 
if ( time() - $looper > 5 && in_array('cqpim_client', $user->roles) ) {
	$client_logs = get_post_meta($client_id, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		/* translators: %s: Subscription Title */
		'page' => sprintf(esc_html__('Subscription - %s', 'projectopia-core'), $p_title),
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
include('header.php');
?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php if ( $assigned == $client_id ) {
				$p_title = str_replace('Private:', '', $p_title);
				echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> <a href="' . esc_url( get_the_permalink($client_dash) ) . '?pto-page=subscriptions">' . esc_html__('Subscriptions', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html( $p_title );
				} else {
					esc_html_e('ACCESS DENIED', 'projectopia-core');
				}
			?>
		</div>
		<div id="cqpim-cdash-inside">
			<?php
			if ( $assigned == $client_id ) { 
				pto_return_subscription_fe($post->ID);
			} else { ?>
				<br />
				<div class="cqpim-dash-item-full grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Access Denied', 'projectopia-core'); ?></span>
							</div>
						</div>
						<p><?php esc_html_e('Cheatin\' uh? We can\'t let you see this item because it\'s not yours', 'projectopia-core'); ?></p>
					</div>
				</div>
			<?php } ?>	
		</div>
	</div>
</div>
<?php include('footer_inc.php'); ?>