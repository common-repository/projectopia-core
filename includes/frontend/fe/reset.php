<?php get_header(); ?>
<div class="cqpim-login">
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<?php echo esc_html( $post->post_title ); ?>
			</div>
		</div>
		<?php if ( empty($_GET['h']) ) { ?>
			<form id="cqpim-reset-pass">
				<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
				<input type="text" id="username" placeholder="<?php esc_attr_e('Email Address', 'projectopia-core'); ?>" />
				<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php esc_html_e('Reset Password', 'projectopia-core'); ?>" />
				<div class="clear"></div>
			</form>
			<?php $reset = get_option('cqpim_login_page'); 
			if ( ! empty($reset) ) { ?>
				<a href="<?php echo esc_url( get_the_permalink($reset) ); ?>" id="forgot" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php esc_html_e('Back to Login', 'projectopia-core'); ?></a>
			<?php } ?>
		<?php } else { ?>
			<form id="reset_pass_conf">
				<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
				<input type="hidden" id="hash" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['h'] ) ) ); ?>" />
				<input type="password" id="password" placeholder="<?php esc_attr_e('New Password', 'projectopia-core'); ?>" />
				<input type="password" id="password2" placeholder="<?php esc_attr_e('Repeat Password', 'projectopia-core'); ?>" />
				<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php esc_html_e('Reset Password', 'projectopia-core'); ?>" />
				<div class="clear"></div>
			</form>
			<?php $reset = get_option('cqpim_login_page'); 
			if ( ! empty($reset) ) { ?>
				<a href="<?php echo esc_url( get_the_permalink($reset) ); ?>" id="forgot" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php esc_html_e('Back to Login', 'projectopia-core'); ?></a>
			<?php } ?>					
		<?php } ?>
		<div class="clear"></div>
		<div id="login_messages" style="display:none"></div>			
	</div>
</div>					
<?php get_footer(); ?>