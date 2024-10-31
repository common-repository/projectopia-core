<?php
register_deactivation_hook( PTO_FILE, 'pto_deactivation' );
function pto_deactivation() {
	if ( pto_check_addon_status( '2checkout' ) || pto_check_addon_status( 'expenses' ) || pto_check_addon_status( 'reporting' ) || pto_check_addon_status( 'bugs' ) || pto_check_addon_status( 'subscriptions' ) || pto_check_addon_status( 'woocommerce' ) ) {
		$message = __( 'You cannot disable Projectopia because some add-ons are active. Please disable those first.', 'projectopia-core' );
		wp_die( esc_html( $message ), 'Projectopia Add-Ons Are Active' );     
	}
	wp_clear_scheduled_hook( 'pto_check_recurring_invoices' );
	wp_clear_scheduled_hook( 'pto_check_email_pipe' );
	$user = get_user_by( 'login', 'ptouploader' );
	wp_delete_user( $user->ID );
	remove_role( 'ptouploader' );
	$role = get_role( 'administrator' );
	if ( ! empty( $role ) ) {
		$role->remove_cap( 'cqpim_view_dashboard' );
	}
}

projectopia_fs()->add_action( 'after_uninstall', 'pto_uninstall_plugin' );
function pto_uninstall_plugin() {
	$client_login = get_option( 'cqpim_login_page' );
	$client_dash = get_option( 'cqpim_client_page' );
	$client_reset = get_option( 'cqpim_reset_page' );
	$client_register = get_option( 'cqpim_register_page' );
	wp_delete_post( $client_login, true );
	wp_delete_post( $client_reset, true );
	wp_delete_post( $client_dash, true );
	wp_delete_post( $client_register, true );
}

add_action( "wp_ajax_pto_remove_all_data", "pto_remove_all_data" );
function pto_remove_all_data() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	// Deactivate Plugin
	deactivate_plugins( plugin_basename( PTO_FILE ) );
	// Remove Projects
	$args = array(
		'post_type'      => 'cqpim_project',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Quotes
	$args = array(
		'post_type'      => 'cqpim_quote',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Clients
	$args = array(
		'post_type'      => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Teams
	$args = array(
		'post_type'      => 'cqpim_teams',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Tickets
	$args = array(
		'post_type'      => 'cqpim_support',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Tasks
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Forms
	$args = array(
		'post_type'      => 'cqpim_forms',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove MS Templates
	$args = array(
		'post_type'      => 'cqpim_templates',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Terms
	$args = array(
		'post_type'      => 'cqpim_terms',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Conversations
	$args = array(
		'post_type'      => 'cqpim_conversations',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Messages
	$args = array(
		'post_type'      => 'cqpim_messages',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Invoices
	$args = array(
		'post_type'      => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Suppliers
	$args = array(
		'post_type'      => 'cqpim_supplier',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Expenses
	$args = array(
		'post_type'      => 'cqpim_expense',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Bugs
	$args = array(
		'post_type'      => 'cqpim_bug',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Leads
	$args = array(
		'post_type'      => 'cqpim_lead',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	$posts = get_posts( $args );
	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
	// Remove Roles
	$roles = get_option( 'cqpim_roles' ); 
	if ( ! is_array( $roles) ) {
		$roles = array( get_option( 'cqpim_roles' ) );
	}
	foreach ( $roles as $role ) {
		$cqpim_role = 'cqpim_' . $role;
		remove_role( $cqpim_role);
	}
	remove_role( 'cqpim_admin' );
	remove_role( 'cqpim_client' );
	remove_role( 'cqpimuploader' );
	remove_role( 'ptouploader' );
	delete_option( 'cqpim_roles' );
	delete_option( 'cqpim_login_page' );
	delete_option( 'cqpim_reset_page' );
	delete_option( 'cqpim_client_page' );
	delete_option( 'cqpim_permissions' );
	delete_option( 'vthreeone_completed', '0' );
	delete_option( 'vthree_completed', '0' );
	delete_option( 'twopoint9_completed', '0' );
	delete_option( 'twopoint8_completed', '0' );
	delete_option( 'twopoint7five_completed', '0' );
	delete_option( 'twopoint7_completed', '0' );
	delete_option( 'twopoint6_completed', '0' );
	// Remove Settings
	delete_option( 'cqpim_plugin_name' );
	delete_option( 'cqpim_show_docs_link' );
	delete_option( 'cqpim_date_format' );
	delete_option( 'cqpim_allowed_extensions' );
	delete_option( 'cqpim_timezone' );
	delete_option( 'cqpim_disable_avatars' );
	delete_option( 'cqpim_invoice_slug' );
	delete_option( 'cqpim_quote_slug' );
	delete_option( 'cqpim_project_slug' );
	delete_option( 'cqpim_support_slug' );
	delete_option( 'cqpim_task_slug' );
	delete_option( 'enable_quotes' );
	delete_option( 'enable_quote_terms' );
	delete_option( 'enable_project_creation' );
	delete_option( 'enable_project_contracts' );
	delete_option( 'disable_invoices' );
	delete_option( 'invoice_workflow' );
	delete_option( 'auto_send_invoices' );
	// Company Settings
	delete_option( 'team_type' );
	delete_option( 'company_name' );
	delete_option( 'company_address' );
	delete_option( 'company_postcode' );
	delete_option( 'company_telephone' );
	delete_option( 'company_sales_email' );
	delete_option( 'company_accounts_email' );
	delete_option( 'company_support_email' );
	delete_option( 'company_logo' );
	delete_option( 'company_bank_name' );
	delete_option( 'currency_symbol' );
	delete_option( 'currency_symbol_position' );
	delete_option( 'currency_symbol_space' );
	delete_option( 'allow_client_currency_override' );
	delete_option( 'allow_project_currency_override' );
	delete_option( 'allow_quote_currency_override' );
	delete_option( 'allow_invoice_currency_override' );
	delete_option( 'currency_code' );
	delete_option( 'company_bank_ac' );
	delete_option( 'company_bank_sc' );
	delete_option( 'company_bank_iban' );
	delete_option( 'company_invoice_terms' );
	delete_option( 'sales_tax_rate' );
	delete_option( 'sales_tax_name' );
	delete_option( 'sales_tax_reg' );
	delete_option( 'secondary_sales_tax_rate' );
	delete_option( 'secondary_sales_tax_name' );
	delete_option( 'secondary_sales_tax_reg' );
	delete_option( 'company_number' );
	// Admin dashboard settings.
	delete_option( 'cqpim_admin_dash_css' );
	delete_option( 'cqpim_save_dashboard_metabox_filters' );
	// Client Settings
	delete_option( 'client_dashboard_type' );
	delete_option( 'auto_welcome' );
	delete_option( 'auto_welcome_subject' );
	delete_option( 'auto_welcome_content' );
	delete_option( 'client_password_reset_subject' );
	delete_option( 'client_password_reset_content' );
	delete_option( 'password_reset_subject' );
	delete_option( 'password_reset_content' );
	delete_option( 'added_contact_subject' );
	delete_option( 'added_contact_content' );
	delete_option( 'allow_client_settings' );
	delete_option( 'allow_client_users' );
	delete_option( 'cqpim_dash_logo' );
	delete_option( 'cqpim_dash_bg' );
	// Quote Settings
	delete_option( 'enable_frontend_anon_quotes' );
	delete_option( 'enable_client_quotes' );
	delete_option( 'quote_header' );
	delete_option( 'quote_footer' );
	delete_option( 'quote_acceptance_text' );
	delete_option( 'quote_email_subject' );
	delete_option( 'quote_email_pdf_attach' );
	delete_option( 'quote_default_email' );
	delete_option( 'client_quote_message_subject' );
	delete_option( 'client_quote_message_email' );
	delete_option( 'company_quote_message_subject' );
	delete_option( 'company_quote_message_email' );
	// Project Settings
	delete_option( 'default_contract_text' );
	delete_option( 'contract_acceptance_text' );
	delete_option( 'client_contract_subject' );
	delete_option( 'client_contract_email' );
	delete_option( 'client_update_subject' );
	delete_option( 'client_update_email' );  
	delete_option( 'client_message_subject' );
	delete_option( 'client_message_email' );
	delete_option( 'company_message_subject' );
	delete_option( 'company_message_email' );
	delete_option( 'auto_contract' );
	delete_option( 'auto_invoice' );
	delete_option( 'auto_update' );
	delete_option( 'auto_completion' );
	// Invoice Settings
	delete_option( 'client_invoice_email_attach' );
	delete_option( 'client_invoice_after_send_remind_days' );
	delete_option( 'client_invoice_before_terms_remind_days' );
	delete_option( 'client_invoice_after_terms_remind_days' );
	delete_option( 'client_invoice_high_priority' );
	delete_option( 'client_invoice_paypal_address' );
	delete_option( 'client_invoice_stripe_key' );
	delete_option( 'client_invoice_stripe_secret' );
	delete_option( 'client_invoice_subject' );
	delete_option( 'client_invoice_email' );
	delete_option( 'client_deposit_invoice_subject' );
	delete_option( 'client_deposit_invoice_email' );
	delete_option( 'client_invoice_reminder_subject' );
	delete_option( 'client_invoice_reminder_email' );
	delete_option( 'client_invoice_overdue_subject' );
	delete_option( 'client_invoice_overdue_email' );
	delete_option( 'client_invoice_footer' );
	delete_option( 'client_deposit_invoice_email' );
	delete_option( 'client_invoice_allow_partial' );
	delete_option( 'client_invoice_twocheck_sid' );
	delete_option( 'client_invoice_receipt_subject' );
	delete_option( 'client_invoice_receipt_email' );
	// Teams
	delete_option( 'team_account_subject' );
	delete_option( 'team_account_email' );   
	delete_option( 'team_reset_subject' );
	delete_option( 'team_reset_email' );
	delete_option( 'team_project_subject' );
	delete_option( 'team_project_email' );   
	delete_option( 'team_assignment_subject' );
	delete_option( 'team_assignment_email' );    
	// Support
	delete_option( 'client_create_ticket_subject' );
	delete_option( 'client_create_ticket_email' );
	delete_option( 'client_update_ticket_subject' );
	delete_option( 'client_update_ticket_email' );
	delete_option( 'company_update_ticket_subject' );
	delete_option( 'company_update_ticket_email' );
	// Quote Forms
	delete_option( 'cqpim_frontend_form' );
	delete_option( 'cqpim_backend_form' );
	delete_option( 'form_reg_auto_welcome' );
	delete_option( 'form_auto_welcome' );
	delete_option( 'pto_frontend_form_google_recaptcha' );
	delete_option( 'new_quote_subject' );
	delete_option( 'new_quote_email' );
	delete_option( 'cqpim_dash_css' );
	delete_option( 'cqpim_logout_url' );
	delete_option( 'google_recaptcha_site_key' );
	delete_option( 'google_recaptcha_secret_key' );
	// Piping Settings 
	delete_option( 'cqpim_mail_server' );
	delete_option( 'cqpim_piping_address' );
	delete_option( 'cqpim_mailbox_name' );
	delete_option( 'cqpim_mailbox_pass' );
	delete_option( 'cqpim_string_prefix' );
	delete_option( 'cqpim_create_support_on_email' );
	delete_option( 'cqpim_create_support_on_unknown_email' );
	delete_option( 'cqpim_send_piping_reject' );
	delete_option( 'cqpim_piping_delete' );
	delete_option( 'cqpim_bounce_subject' );
	delete_option( 'cqpim_bounce_content' );
	delete_option( 'cqpim_new_message_subject' );
	delete_option( 'cqpim_new_message_content' );
	delete_option( 'cqpim_messages_allow_client' );
	delete_option( 'cqpim_html_email_styles' );
	delete_option( 'cqpim_html_email' );
	delete_option( 'cqpim_settings_imported' );
	delete_option( 'cqpim_cc_address' );
	delete_option( 'pto_v4_1_compat' );
	delete_option( 'v4_1_compat_complete' );
	delete_option( 'pto_v5_0_0_compat_complete' );
	delete_option( 'cqpim_enable_faq' );
	delete_option( 'cqpim_enable_faq_dash_accordion' );
	delete_option( 'cqpim_enable_faq_dash_cats' );
	delete_option( 'cqpim_enable_faq_dash' );
	//Delete initial setup option.
	delete_option( 'pto_run_setup_wizard' );
	//Delete support priorities options.
	delete_option( 'support_ticket_priorities' ); 
	wp_send_json( array( 
		'error'    => false,
		'message'  => __( 'The plugin has been reset and deactivated, redirecting now', 'projectopia-core' ),
		'redirect' => admin_url() . 'plugins.php',
	) ); 
}
