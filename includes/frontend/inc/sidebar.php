<?php
$user = wp_get_current_user();  
$assignment = pto_get_client_from_userid($user);
$assigned = isset($assignment['assigned']) ? $assignment['assigned'] : '';
$client_type = isset($assignment['type']) ? $assignment['type'] : '';
$client_contract = get_post_meta($assigned, 'client_contract', true);
$tickets = get_option('disable_tickets');
?>
<ul class="cqpim-dash-main-menu nomobile">
	<div class="mobile_items">
		<?php if ( empty($avatar) ) { echo '<div class="cqpim_avatar">' . get_avatar( $user->ID, 50, '', false, array( 'force_display' => true ) ) . '</div>'; }; ?>
		<span class="cqpim_username rounded_2"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo esc_html( $user->display_name ); ?></span>
		<span class="cqpim_role rounded_2"><i class="fa fa-users" aria-hidden="true"></i> <?php echo isset($client_details['client_company']) ? esc_html( $client_details['client_company'] ) : ''; ?> <?php if ( $client_type == 'admin' ) { echo esc_html__('(Main Contact)', 'projectopia-core'); } ?></span>
		<div class="clear"></div>
		<ul id="cd-head-actions-mobile">				
			<?php if ( ! empty($messaging) ) { ?>
				<li class="desktop_items">
					<span class="cqpim_icon">
						<a <?php if ( ! empty($unread_qty) ) { echo 'class="cqpim_active"'; } ?> href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=messages'; ?>"><i class="fa fa-envelope-open cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Messages', 'projectopia-core'); ?>"></i></a>
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
						<a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=settings'; ?>"><i class="fa fa-sliders cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Edit my Profile', 'projectopia-core'); ?>"></i></a>
					</span>
				</li>
			<?php } ?>
			<li class="desktop_items">
				<span class="cqpim_icon">
					<a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=client-files'; ?>"><i class="fa fa-file cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Client Files', 'projectopia-core'); ?>"></i></a>
				</span>
			</li>
			<?php $client_settings = get_option('allow_client_users');
			if ( $client_settings == 1 ) { ?>
				<li class="contacts desktop_items">
					<span class="cqpim_icon">
						<a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=contacts'; ?>"><i class="fa fa-users cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Contacts', 'projectopia-core'); ?>"></i></a>
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
					<a href="<?php echo esc_url(wp_logout_url($login_url)); ?>"><i class="fa fa-sign-out cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Log Out', 'projectopia-core'); ?>"></i></a>
				</span>
			</li>
		</ul>		
	</div>
	<?php $client_dash = get_option('cqpim_client_page'); ?>
	<li style="padding:10px"><?php esc_html_e('MENU', 'projectopia-core'); ?></li>
	<li class="link<?php if ( empty($_GET['pto-page']) && ! is_singular('cqpim_tasks') && ! is_singular('cqpim_support') && ! is_singular('cqpim_bug') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)); ?>"><i class="fa fa-tachometer" aria-hidden="true"></i><?php esc_html_e('Dashboard', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php do_action( 'pto_cd_after_dashboard_menu', $client_dash ); ?>
	<?php if ( get_option('cqpim_messages_allow_client') == 1 ) { ?>
	<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'messages' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php esc_html_e('Messages', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<?php if ( get_option('enable_quotes') == 1 ) { ?>
	<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quotes' || is_singular('cqpim_quote') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=quotes'; ?>"><i class="fa fa-file-text" aria-hidden="true"></i><?php esc_html_e('Quotes / Estimates', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'projects' || is_singular('cqpim_project') || is_singular('cqpim_tasks') || is_singular('cqpim_bug') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=projects'; ?>"><i class="fa fa-th" aria-hidden="true"></i><?php esc_html_e('Projects', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php if ( empty($tickets) && pto_has_addon_active_license( 'pto_st', 'tickets' ) ) { ?>
		<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'support' || is_singular('cqpim_support') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=support'; ?>"><i class="fa fa-life-ring" aria-hidden="true"></i><?php esc_html_e('Support Tickets', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<?php if ( get_option('cqpim_enable_faq_dash') ) { ?>
		<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'faq' || is_singular('cqpim_faq') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=faq'; ?>"><i class="fa fa-question-circle" aria-hidden="true"></i><?php esc_html_e('FAQ', 'projectopia-core'); ?></a><span class="selected"></span></li>	
	<?php } ?>
	<?php if ( get_option('disable_invoices') != 1 ) { ?>
	<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'invoices' || is_singular('cqpim_invoice') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php esc_html_e('Invoices', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php } ?>
	<?php if ( ! empty($quote_form) && get_option('enable_quotes') == 1 ) { ?>
		<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quote_form' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($client_dash)) . '?pto-page=quote_form'; ?>"><i class="fa fa-angle-double-right" aria-hidden="true"></i><?php esc_html_e('Request a Quote', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php } ?>		
	<?php do_action( 'pto_sidebar_content_area', $client_dash ); ?>	
	<?php if ( $post->post_type == 'cqpim_project' || ! empty($ppid) ) { 
	if ( ! empty($ppid) ) { ?>
		<li style="padding:10px"><?php esc_html_e('PROJECT MENU', 'projectopia-core'); ?></li>
		<?php 
		do_action( 'pto_project_menu_start', $ppid );
		$project_details = get_post_meta($ppid, 'show_project_info', true);
		if ( ! empty($project_details) ) { ?>
			<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'info' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=info'; ?>"><i class="fa fa-info-circle" aria-hidden="true"></i><?php esc_html_e('Project Information', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<?php $checked = get_post_meta($ppid, 'contract_status', true);
		if ( ! empty($checked) && $checked == 1 ) { ?>
			<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contract' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=contract'; ?>"><i class="fa fa-file-text" aria-hidden="true"></i><?php esc_html_e('View Contract', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'updates' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=updates'; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><?php esc_html_e('Updates & Progress', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'milestones' || is_singular('cqpim_tasks') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=milestones'; ?>"><i class="fa fa-tasks" aria-hidden="true"></i><?php esc_html_e('Milestones & Tasks', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php 
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) && get_post_meta($ppid, 'bugs_activated', true) ) { ?>
			<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'bugs' || is_singular('cqpim_bug') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=bugs'; ?>"><i class="fa fa-bug" aria-hidden="true"></i><?php esc_html_e('Bugs', 'projectopia-core'); ?></a><span class="selected"></span></li>			
		<?php } ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'messages' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php esc_html_e('Messages', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'files' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=files'; ?>"><i class="fa fa-file" aria-hidden="true"></i><?php esc_html_e('Files', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php if ( get_option('disable_invoices') != 1 ) { ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'invoices' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php esc_html_e('Costs & Invoices', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } else { ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'invoices' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($ppid)) . '?pto-page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php esc_html_e('Costs', 'projectopia-core'); ?></a><span class="selected"></span></li>	
		<?php } ?>
	<?php } else { ?>
		<li style="padding:10px"><?php esc_html_e('PROJECT MENU', 'projectopia-core'); ?></li>
		<?php
		do_action( 'pto_project_menu_start', $post->ID );
		$project_details = get_post_meta($post->ID, 'show_project_info', true);
		if ( ! empty($project_details) ) { ?>
			<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'info' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=info'; ?>"><i class="fa fa-info-circle" aria-hidden="true"></i><?php esc_html_e('Project Information', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<?php $checked = get_post_meta($post->ID, 'contract_status', true);
		if ( ! empty($checked) && $checked == 1 ) { ?>
			<li class="link<?php if ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contract' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=contract'; ?>"><i class="fa fa-file-text" aria-hidden="true"></i><?php esc_html_e('View Contract', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'updates' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=updates'; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><?php esc_html_e('Updates & Progress', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'milestones' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=milestones'; ?>"><i class="fa fa-tasks" aria-hidden="true"></i><?php esc_html_e('Milestones & Tasks', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php 
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) && get_post_meta($post->ID, 'bugs_activated', true) ) { ?>
			<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'bugs' || is_singular('cqpim_bug') ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=bugs'; ?>"><i class="fa fa-bug" aria-hidden="true"></i><?php esc_html_e('Bugs', 'projectopia-core'); ?></a><span class="selected"></span></li>			
		<?php } ?>			
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'messages' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php esc_html_e('Messages', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'files' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=files'; ?>"><i class="fa fa-file" aria-hidden="true"></i><?php esc_html_e('Files', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php if ( get_option('disable_invoices') != 1 ) { ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'invoices' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php esc_html_e('Costs & Invoices', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } else { ?>
		<li class="link<?php if ( ! empty($_GET['sub']) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'invoices' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=summary&sub=invoices'; ?>"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?php esc_html_e('Costs', 'projectopia-core'); ?></a><span class="selected"></span></li>
		<?php } ?>
	<?php } ?>	
	<?php } ?>

	<?php 
		if ( $post->post_type == 'cqpim_quote' ) {
			?>
			<li style="padding:10px" class="link <?php if ( isset( $_GET['pto-page'] ) && ! isset( $_GET['sub'] ) && ( 'quote' === sanitize_text_field(wp_unslash($_GET['pto-page'])) ) ) { echo 'active'; } ?>"> <?php esc_html_e('QUOTE MENU', 'projectopia-core'); ?></li>
			<li class="link<?php if ( ! empty( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'messages' ) { echo ' active'; } ?>"><a href="<?php echo esc_url(get_the_permalink($post->ID)) . '?pto-page=quote&sub=messages'; ?>"><i class="fa fa-envelope" aria-hidden="true"></i><?php esc_html_e('Messages', 'projectopia-core'); ?></a><span class="selected"></span></li>
	<?php } ?>

	<?php if ( is_active_sidebar('cqpim_client_sidebar') ) { ?>
			<?php dynamic_sidebar('cqpim_client_sidebar'); ?>
	<?php } ?>

	<?php
	/**
	 * Fires when sidebar render.
	 *
	 * @param string $client_dash
	 */
	do_action( 'pto_client_dashboard_sidebar', $client_dash );
	?>

</ul>
