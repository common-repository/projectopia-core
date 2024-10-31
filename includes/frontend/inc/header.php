<?php 
include_once(ABSPATH.'wp-admin/includes/plugin.php');
$user = wp_get_current_user();
if ( ! in_array('cqpim_client', $user->roles) ) {
	$login_page = get_option('cqpim_login_page');
	$url = get_the_permalink($login_page);
	wp_safe_redirect($url, 302);
	exit();
} else {
	$user = wp_get_current_user();  
	$login_page_id = get_option('cqpim_login_page');
	$client_dash = get_option('cqpim_client_page');
	$login_url = get_the_permalink($login_page_id);
	$user_id = $user->ID;
	$dash_type = get_option('client_dashboard_type');
	$theme = wp_get_theme();
	$quote_form = get_option('cqpim_backend_form');
	$assigned = pto_get_client_from_userid($user);
	$client_type = '';
	if ( ! empty( $assigned['type'] ) ) {
		$client_type = $assigned['type'];
	}

	if ( ! empty( $assigned['assigned'] ) ) {
		$assigned = $assigned['assigned'];
	}
	$client_contract = get_post_meta($assigned, 'client_contract', true);
	$client_details = get_post_meta($assigned, 'client_details', true);
	$client_ids = get_post_meta($assigned, 'client_ids', true);
	$client_ids_untouched = $client_ids;
	$avatar = get_option('cqpim_disable_avatars');
	if ( empty($client_ids_untouched) ) {
		$client_ids_untouched = array();
	}
	$login_url = get_option('cqpim_logout_url');
	if ( empty($login_url) ) {
		$login_url = get_the_permalink($login_page_id);
	}
}
?>
<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html  class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php wp_title(); ?></title>   
	<?php wp_head(); ?>
	<?php echo '<style>' . esc_textarea( get_option('cqpim_dash_css') ) . '</style>'; ?>
</head>
<?php
$avatar = get_option('cqpim_disable_avatars');
$user = wp_get_current_user();
$client = pto_get_client_from_userid($user);
$messaging = get_option('cqpim_messages_allow_client');
$unread = pto_new_messages($user->ID);
$unread_stat = isset($unread['read_val']) ? $unread['read_val'] : '';
$unread_qty = isset($unread['new_messages']) ? $unread['new_messages'] : '';
if ( ! empty( $client['assigned']) ) {
	$notification_count = pto_check_unread_client_notifications($client['assigned']);
	$notifications = pto_get_team_notifications($client['assigned']);
}
if ( ! empty($notifications) && is_array($notifications) ) {
	$notifications = array_reverse($notifications);
}
?>
<body style="min-height:700px;" <?php body_class(); ?>>	
<div id="overlay" style="display:none">
	<div id="spinner">
		<img src="<?php echo esc_url( PTO_PLUGIN_URL ) . '/img/loading_spinner.gif'; ?>" />
	</div>
</div>
<div id="cqpim_dashboard_container">
	<div class="cqpim-client-dash" role="main">	
		<div id="cqpim_admin_head">
			<div id="cqpim_admin_head_inside">
				<ul>
					<li class="nodesktop cd-menu">
						<a class="menu-open" title="<?php esc_html_e('Menu', 'projectopia-core'); ?>" href="#"><img src="<?php echo esc_url( PTO_PLUGIN_URL ) . '/img/menu.png'; ?>" alt="Menu" /></a>
						<a style="display:none" class="menu-close" title="<?php esc_html_e('Close Menu', 'projectopia-core'); ?>" href="#"><img src="<?php echo esc_url( PTO_PLUGIN_URL ) . '/img/close.png'; ?>" alt="Menu" /></a>
					</li>
					<?php
					$logo = get_option('cqpim_dash_logo'); 
					if ( $logo ) { ?>
						<li style="width:190px; padding-right:15px; text-align:center; overflow:hidden; max-height:54px">
							<img style="max-width:100%" src="<?php echo esc_url($logo['cqpim_dash_logo']); ?>" />			
						</li>
					<?php } ?>
					<?php if ( empty($avatar) ) { echo '<li class="cqpim_avatar desktop_items">' . get_avatar( $user->ID, 50, '', false, array( 'force_display' => true ) ) . '</li>'; } else { echo '<li style="height:50px; width:1px;margin-left:-1px">&nbsp;</li>'; } ?>
					<li class="desktop_items"><span class="cqpim_username rounded_2"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo esc_html( $user->display_name ); ?></span></li>
					<li class="desktop_items"><span class="cqpim_role rounded_2"><i class="fa fa-users" aria-hidden="true"></i> <?php echo isset($client_details['client_company']) ? esc_html( $client_details['client_company'] ) : ''; ?> <?php if ( $client_type == 'admin' ) { echo esc_html__('(Main Contact)', 'projectopia-core'); } ?></span></li>
				</ul>
				<ul id="cd-head-actions">
					<?php do_action( 'pto_before_cd_header_icons', $client_dash ); ?>
					<li>
						<span class="cqpim_icon">
							<a class="cqpim_notifications <?php if ( ! empty($notification_count) ) { ?>cqpim_active<?php } ?>" href="#"><i class="fa fa-bell" aria-hidden="true" title="<?php esc_html_e('Notifications', 'projectopia-core'); ?>"></i></a>
							<?php if ( ! empty($notification_count) ) { ?>								
								<span id="nf_counter" class="cqpim_counter"><?php echo esc_html( $notification_count ); ?></span>								
							<?php } ?>	
						</span>
						<div id="cqpim_notifications" style="display:none">
							<div id="notification_up">
								<i class="fa fa-caret-up"></i>
							</div>
							<div class="inner rounded_4">
								<h3 class="font-white"><?php esc_html_e('Notifications', 'projectopia-core'); ?></h3>
								<div id="notification_list" class="rounded_4">
									<?php if ( ! empty($notifications) ) { ?>
										<ul id="notifications_ul">
											<?php foreach ( $notifications as $key => $notification ) { ?>
												<li <?php if ( empty($notification['read']) ) { ?>class="unread"<?php } ?>>
													<?php if ( empty($avatar) ) { ?>
														<div class="notification_avatar">
															<?php echo get_avatar( $notification['from'], 25, '', false, array( 'force_display' => true ) ) ?>
														</div>
													<?php } ?>
													<div class="notification_message" <?php if ( empty($avatar) ) { ?>style="width:calc(100% - 50px)"<?php } ?>>
														<a href="#" class="notification_item" data-item="<?php echo esc_attr( $notification['item'] ); ?>" data-key="<?php echo esc_attr( $key ); ?>" data-type="<?php echo esc_attr( $notification['type'] ); ?>"><?php echo esc_html( $notification['message'] ); ?></a><br />
														<span class="notification_time"><?php echo esc_html( wp_date(get_option('cqpim_date_format') . ' H:i', $notification['time']) ); ?></span>
														<div class="notification_remove"><a class="nf_remove_button" href="#" data-key="<?php echo esc_attr( $key ); ?>" title="<?php esc_html_e('Clear Notification', 'projectopia-core'); ?>"><i class="fa fa-times-circle"></i></a></div>
													</div>
													<div class="clear"></div>
												</li>
											<?php } ?>
										</ul>
									<?php } else { ?>
										<p style="padding:0 10px"><?php esc_html_e('You do not have any notifications', 'projectopia-core'); ?></p>
									<?php } ?>
								</div>
								<div id="notification_actions">
									<a id="mark_all_read_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php esc_html_e('Mark All as Read', 'projectopia-core'); ?></a>
									<a id="clear_all_read_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php esc_html_e('Clear All Read', 'projectopia-core'); ?></a>
									<a id="clear_all_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php esc_html_e('Clear All', 'projectopia-core'); ?></a>
								</div>
							</div>
						</div>					
					</li>				
					<?php if ( ! empty($messaging) ) { ?>
						<li class="desktop_items">
							<span class="cqpim_icon">
								<a <?php if ( ! empty($unread_qty) ) { echo 'class="cqpim_active"'; } ?> href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=messages'; ?>"><i class="fa fa-envelope-open cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Messages', 'projectopia-core'); ?>"></i></a>
								<?php if ( ! empty($unread_qty) ) { ?>
									<span class="cqpim_counter"><?php echo esc_html( $unread_qty ); ?></span>
								<?php } ?>
							</span>
						</li>
					<?php } ?>
					<?php
					$client_settings = get_option('allow_client_settings');
					if ( $client_settings == 1 ) { ?>
						<li class="desktop_items">
							<span class="cqpim_icon">
								<a href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=settings'; ?>"><i class="fa fa-sliders cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Edit my Profile', 'projectopia-core'); ?>"></i></a>
							</span>
						</li>
					<?php } ?>
					<li class="desktop_items">
						<span class="cqpim_icon">
							<a href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=client-files'; ?>"><i class="fa fa-file cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Client Files', 'projectopia-core'); ?>"></i></a>
						</span>
					</li>
					<?php $client_settings = get_option('allow_client_users');
					if ( $client_settings == 1 ) { ?>
						<li class="contacts desktop_items">
							<span class="cqpim_icon">
								<a href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=contacts'; ?>"><i class="fa fa-users cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Contacts', 'projectopia-core'); ?>"></i></a>
							</span>
						</li>
					<?php } ?>
					<li class="desktop_items">
						<span class="cqpim_icon">
							<?php
								$login_page_id = get_option('cqpim_login_page');
								$login_url = get_option('cqpim_logout_url');
								if ( empty($login_url) ) {
									$login_url = get_the_permalink($login_page_id);
								}
							?>
							<a href="<?php echo esc_url( wp_logout_url($login_url) ); ?>"><i class="fa fa-sign-out cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Log Out', 'projectopia-core'); ?>"></i></a>
						</span>
					</li>
					<?php do_action( 'pto_after_cd_header_icons', $client_dash ); ?>
				</ul>
				<div class="clear"></div>
			</div>
		</div>		