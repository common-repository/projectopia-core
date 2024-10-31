<?php get_header(); ?>	 
<div class="cqpim-login">
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<?php echo esc_html( $post->post_title ); ?>
			</div>
		</div>
		<form id="cqpim-login">
			<?php wp_nonce_field('ajax-register-nonce', 'signonsecurity'); ?> 
			<input type="text" id="username" placeholder="<?php esc_attr_e('Email Address', 'projectopia-core'); ?>" />
			<input type="password" id="password" placeholder="<?php esc_attr_e('Password', 'projectopia-core'); ?>" />
			<?php if ( ! empty( get_option('pto_frontend_form_google_recaptcha') ) ) { ?>
				<div class="g-recaptcha" style="margin-bottom:10px;" data-sitekey="<?php echo esc_attr( get_option('google_recaptcha_site_key') );?>"></div>
			<?php } ?>
			<input type="submit" class="op cqpim_button right bg-blue font-white rounded_2" value="<?php esc_html_e('Log In', 'projectopia-core'); ?>" />
			<div class="clear"></div>
		</form>
		<?php $reset = get_option('cqpim_reset_page'); 
		if ( ! empty($reset) ) { ?>
			<a href="<?php echo esc_url( get_the_permalink($reset) ); ?>" id="forgot" class="op"><?php esc_html_e('Forgotten Password?', 'projectopia-core'); ?></a>
		<?php } ?>
		<?php $register = get_option('cqpim_login_reg');
		$register_page = get_option('cqpim_register_page'); 
		if ( ! empty($register) && ! empty($register_page) ) { ?>
			<a href="<?php echo esc_url( get_the_permalink($register_page) ); ?>" id="register" class="op cqpim_button cqpim_button bg-grey-cascade font-white rounded_2"><?php esc_html_e('Create Account', 'projectopia-core'); ?></a>
		<?php } ?>
		<div class="clear"></div>
		<div id="login_messages" style="display:none"></div>
	</div>
</div>
<?php get_footer(); ?>