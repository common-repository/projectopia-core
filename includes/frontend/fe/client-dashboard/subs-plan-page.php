<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Quotes Page', 'projectopia-core'),
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<br />
<div class="cqpim_block">
	<?php do_action( 'pto_subs_plans_page' ); ?>
</div>