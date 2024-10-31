<?php
$user = wp_get_current_user();
if ( in_array('cqpim_client', $user->roles) ) {
	$login_page = get_option('cqpim_client_page');
	$url = get_the_permalink($login_page);
	wp_safe_redirect($url, 302);
	exit(); 
} ?>

<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php wp_title(); ?></title>   
	<?php wp_head(); ?>
	<?php echo '<style>' . esc_textarea( get_option('cqpim_dash_css') ) . '</style>'; ?>
</head>
<?php $bg = get_option('cqpim_dash_bg'); ?>
<?php if ( empty($bg) ) { ?>
	<body class="pto-client-login" style="background:<?php echo esc_attr( get_option('client_login_bg_color', '#3B3F51') ); ?>; height:100vh">
<?php } else { ?>
	<body class="pto-client-login" style="background:url(<?php echo esc_url($bg['cqpim_dash_bg']); ?>) center top no-repeat; background-size:cover; height:100vh">
<?php } ?>
<div id="overlay" style="display:none">
	<div id="spinner">
		<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/img/loading_spinner.gif' ); ?>" />
	</div>
</div>
<div id="content" role="main">	
	<br /><br />
	<?php
	$logo = get_option('cqpim_dash_logo'); 
	if ( $logo ) { ?>
		<div style="text-align:center; max-width:400px; margin:0 auto">
			<img style="max-width:100%; margin:20px 0 0" src="<?php echo esc_url($logo['cqpim_dash_logo']); ?>" />
		</div>
	<?php } ?>
	<br /><br />
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
				<input type="submit" class="op cqpim_button right bg-violet font-white rounded_2" value="<?php esc_html_e('Log In', 'projectopia-core'); ?>" />
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
</div>
<?php wp_footer(); ?>
</body>
</html>	