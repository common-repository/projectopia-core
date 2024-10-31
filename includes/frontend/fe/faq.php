<?php 
include('header.php');
$looper = get_post_meta($post->ID, 'looper', true);
$looper = $looper ? $looper : 0;
if ( time() - $looper > 5 ) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$p_title = $post->post_title;
	$p_title = str_replace('Private:', '', $p_title);
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		/* translators: %s: FAQ Title */
		'page' => sprintf(esc_html__('FAQ - %1$s', 'projectopia-core'), $p_title),
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-green-sharp sbold"><?php echo esc_html( $post->post_title ); ?> </span>
				</div>
			</div>
			<div class="pto-client-faq">
				<?php
				$terms = get_post_meta($post->ID, 'terms', true);
				echo do_shortcode(wpautop($terms));
				?>
			</div>
		</div>
	</div>	
</div>
<?php include('footer.php'); ?>