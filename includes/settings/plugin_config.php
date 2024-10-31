<?php
// Add CQPIM settings page
add_action( 'admin_menu' , 'register_pto_settings_page', 29 ); 
function register_pto_settings_page() {
	$mypage = add_submenu_page( 'pto-dashboard', esc_html__( 'Settings', 'projectopia-core' ), '<span class="pto-sm-hidden">' . esc_html__( 'Settings', 'projectopia-core' ) . '</span>', 'edit_cqpim_settings', 'pto-settings', 'pto_settings' );
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
	// register settings
	add_action( 'admin_init', 'register_pto_settings' );
}
// Validate uploaded logo
function pto_validate_image( $plugin_options ) {
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ( $key == 'company_logo' && $image['size'] ) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array( 'test_form' => false );      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[ $key ] = $file['url'];  
			} else {             
				$options = get_option('company_logo');       
				$plugin_options[ $key ] = $options[ $logo ];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('company_logo');     
			if ( ! empty($options[ $key ]) ) {
				$plugin_options[ $key ] = $options[ $key ];
			}   
		}   
		$i++; 
	} 
	return $plugin_options;
}
function pto_validate_logo( $plugin_options ) { 
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ( $key == 'cqpim_dash_logo' && $image['size'] ) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array( 'test_form' => false );      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[ $key ] = $file['url'];  
			} else {             
				$options = get_option('cqpim_dash_logo');       
				$plugin_options[ $key ] = $options[ $logo ];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('cqpim_dash_logo');     
			if ( ! empty($options[ $key ]) ) {
				$plugin_options[ $key ] = $options[ $key ];
			}  
		}   
		$i++; 
	} 
	return $plugin_options;
}
function pto_validate_bg( $plugin_options ) {
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ( $key == 'cqpim_dash_bg' && $image['size'] ) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array( 'test_form' => false );      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[ $key ] = $file['url'];  
			} else {             
				$options = get_option('cqpim_dash_bg');       
				$plugin_options[ $key ] = $options[ $logo ];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('cqpim_dash_bg');     
			if ( ! empty($options[ $key ]) ) {
				$plugin_options[ $key ] = $options[ $key ];
			}   
		}   
		$i++; 
	} 
	return $plugin_options;
}
function pto_validate_invlogo( $plugin_options ) {
	$i = 0; 
	foreach ( $_FILES as $key => $image ) {    
		if ( $key == 'cqpim_invoice_logo' && $image['size'] ) { 
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) { 
				$override = array( 'test_form' => false );      
				$file = wp_handle_upload( $image, $override );
				$plugin_options[ $key ] = $file['url'];
			} else {             
				$options = get_option('cqpim_invoice_logo');       
				$plugin_options[ $key ] = $options[ $logo ];          
				wp_die('No image was uploaded.');     
			}  
		} else {     
			$options = get_option('cqpim_invoice_logo');  
			if ( ! empty($options[ $key ]) ) {
				$plugin_options[ $key ] = $options[ $key ];
			}
		}   
		$i++; 
	} 
	return $plugin_options;
}
function register_pto_settings() {
	register_setting( 'cqpim_settings', 'cqpim_plugin_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'cqpim_use_default_icon' );
	register_setting( 'cqpim_settings', 'cqpim_show_docs_link' );
	register_setting( 'cqpim_settings', 'cqpim_online_widget', '' );
	register_setting( 'cqpim_settings', 'cqpim_date_format', '' );
	register_setting( 'cqpim_settings', 'cqpim_allowed_extensions', '' );
	register_setting( 'cqpim_settings', 'cqpim_timezone', '' );
	register_setting( 'cqpim_settings', 'cqpim_disable_avatars', '' );
	register_setting( 'cqpim_settings', 'cqpim_invoice_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_quote_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_project_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_support_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_task_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_bug_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_subs_slug', '' );
	register_setting( 'cqpim_settings', 'cqpim_faq_slug', '' );
	register_setting( 'cqpim_settings', 'enable_quotes', '' );
	register_setting( 'cqpim_settings', 'disable_tickets', '' );
	register_setting( 'cqpim_settings', 'cqpim_enable_messaging', '' );
	register_setting( 'cqpim_settings', 'enable_quote_terms', '' );
	register_setting( 'cqpim_settings', 'enable_project_creation', '' );
	register_setting( 'cqpim_settings', 'enable_project_contracts', '' );
	register_setting( 'cqpim_settings', 'disable_invoices', '' );
	register_setting( 'cqpim_settings', 'invoice_workflow', '' );
	register_setting( 'cqpim_settings', 'auto_send_invoices', '' );
	// Company Settings
	register_setting( 'cqpim_settings', 'team_type' );
	register_setting( 'cqpim_settings', 'company_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_address', '' );
	register_setting( 'cqpim_settings', 'company_postcode', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_telephone', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_sales_email', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_accounts_email', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_support_email', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'cqpim_cc_address', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_logo', 'pto_validate_image' );
	register_setting( 'cqpim_settings', 'company_bank_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'currency_symbol', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'currency_symbol_position');
	register_setting( 'cqpim_settings', 'currency_symbol_space');
	register_setting( 'cqpim_settings', 'currency_decimal');
	register_setting( 'cqpim_settings', 'currency_comma');
	register_setting( 'cqpim_settings', 'allow_client_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_project_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_quote_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_invoice_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'allow_supplier_currency_override', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'currency_code' );
	register_setting( 'cqpim_settings', 'company_bank_ac', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_bank_sc', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_bank_iban', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_invoice_terms', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'sales_tax_rate', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'sales_tax_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'sales_tax_reg', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'secondary_sales_tax_rate', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'secondary_sales_tax_name', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'secondary_sales_tax_reg', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'company_number', 'sanitize_text_field' );
	// Admin dashboard.
	register_setting( 'cqpim_settings', 'cqpim_admin_dash_css' );
	register_setting( 'cqpim_settings', 'cqpim_save_dashboard_metabox_filters' );
	// Business Hours
	register_setting( 'cqpim_settings', 'pto_opening');
	register_setting( 'cqpim_settings', 'pto_support_open_message');
	register_setting( 'cqpim_settings', 'pto_support_closed_message');
	register_setting( 'cqpim_settings', 'pto_support_opening_warning');
	register_setting( 'cqpim_settings', 'pto_shortcode_open_message');
	register_setting( 'cqpim_settings', 'pto_shortcode_closed_message');
	// Lead Settings
	register_setting( 'cqpim_settings', 'new_lead_email_subject');
	register_setting( 'cqpim_settings', 'new_lead_email_content');
	// Client Settings
	register_setting( 'cqpim_settings', 'client_dashboard_type' );
	register_setting( 'cqpim_settings', 'client_dashboard_gfont' );
	register_setting( 'cqpim_settings', 'client_dashboard_primary_color' );
	register_setting( 'cqpim_settings', 'client_dashboard_secondary_color' );
	register_setting( 'cqpim_settings', 'client_dashboard_link_color' );
	register_setting( 'cqpim_settings', 'client_dashboard_link_hover_color' );
	register_setting( 'cqpim_settings', 'client_dashboard_button_color' );
	register_setting( 'cqpim_settings', 'client_dashboard_text_color' );
	register_setting( 'cqpim_settings', 'client_login_bg_color' );
	register_setting( 'cqpim_settings', 'cqpim_login_reg' );
	register_setting( 'cqpim_settings', 'cqpim_login_reg_company' );
	register_setting( 'cqpim_settings', 'pto_dcreg_approve' );
	register_setting( 'cqpim_settings', 'auto_welcome' );
	register_setting( 'cqpim_settings', 'auto_welcome_subject' );
	register_setting( 'cqpim_settings', 'auto_welcome_content' );
	register_setting( 'cqpim_settings', 'client_password_reset_subject' );
	register_setting( 'cqpim_settings', 'client_password_reset_content' );
	register_setting( 'cqpim_settings', 'password_reset_subject' );
	register_setting( 'cqpim_settings', 'password_reset_content' );
	register_setting( 'cqpim_settings', 'added_contact_subject' );
	register_setting( 'cqpim_settings', 'added_contact_content' );
	register_setting( 'cqpim_settings', 'allow_client_settings' );
	register_setting( 'cqpim_settings', 'allow_client_users' );
	register_setting( 'cqpim_settings', 'cqpim_dash_logo', 'pto_validate_logo' );
	register_setting( 'cqpim_settings', 'cqpim_dash_bg', 'pto_validate_bg' );
	// Quote Settings
	register_setting( 'cqpim_settings', 'enable_frontend_anon_quotes' );
	register_setting( 'cqpim_settings', 'enable_client_quotes' );
	register_setting( 'cqpim_settings', 'quote_header' );
	register_setting( 'cqpim_settings', 'quote_footer' );
	register_setting( 'cqpim_settings', 'quote_acceptance_text' );
	register_setting( 'cqpim_settings', 'quote_email_subject', 'sanitize_text_field' );
	register_setting( 'cqpim_settings', 'quote_email_pdf_attach' );
	register_setting( 'cqpim_settings', 'quote_default_email' );
	register_setting( 'cqpim_settings', 'client_quote_message_subject' );
	register_setting( 'cqpim_settings', 'client_quote_message_email' );
	register_setting( 'cqpim_settings', 'company_quote_message_subject' );
	register_setting( 'cqpim_settings', 'company_quote_message_email' );
	// Project Settings
	register_setting( 'cqpim_settings', 'pto_default_project_sort' );
	register_setting( 'cqpim_settings', 'pto_default_project_order' );
	register_setting( 'cqpim_settings', 'pto_default_drop_closed' );
	register_setting( 'cqpim_settings', 'default_contract_text' );
	register_setting( 'cqpim_settings', 'default_contract_text' );
	register_setting( 'cqpim_settings', 'contract_acceptance_text' );
	register_setting( 'cqpim_settings', 'client_contract_subject' );
	register_setting( 'cqpim_settings', 'client_contract_email' );
	register_setting( 'cqpim_settings', 'client_update_subject' );
	register_setting( 'cqpim_settings', 'client_update_email' );
	register_setting( 'cqpim_settings', 'client_message_subject' );
	register_setting( 'cqpim_settings', 'client_message_email' );
	register_setting( 'cqpim_settings', 'company_message_subject' );
	register_setting( 'cqpim_settings', 'company_message_email' );
	register_setting( 'cqpim_settings', 'auto_contract' );
	register_setting( 'cqpim_settings', 'auto_invoice' );
	register_setting( 'cqpim_settings', 'auto_update' );
	register_setting( 'cqpim_settings', 'auto_completion' );
	// Invoice Settings
	register_setting( 'cqpim_settings', 'cqpim_invoice_template' );
	register_setting( 'cqpim_settings', 'cqpim_invoice_prefix' );
	register_setting( 'cqpim_settings', 'cqpim_invoice_logo', 'pto_validate_invlogo' );
	register_setting( 'cqpim_settings', 'cqpim_clean_main_colour');
	register_setting( 'cqpim_settings', 'cqpim_cool_main_colour');
	register_setting( 'cqpim_settings', 'client_invoice_email_attach' );
	register_setting( 'cqpim_settings', 'client_invoice_after_send_remind_days' );
	register_setting( 'cqpim_settings', 'client_invoice_before_terms_remind_days' );
	register_setting( 'cqpim_settings', 'client_invoice_after_terms_remind_days' );
	register_setting( 'cqpim_settings', 'client_invoice_high_priority' );
	register_setting( 'cqpim_settings', 'client_invoice_paypal_address' );
	register_setting( 'cqpim_settings', 'client_invoice_stripe_key' );
	register_setting( 'cqpim_settings', 'client_invoice_stripe_secret' );
	register_setting( 'cqpim_settings', 'client_invoice_stripe_ideal' );
	register_setting( 'cqpim_settings', 'client_invoice_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_email' );
	register_setting( 'cqpim_settings', 'client_deposit_invoice_subject' );
	register_setting( 'cqpim_settings', 'client_deposit_invoice_email' );
	register_setting( 'cqpim_settings', 'client_invoice_reminder_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_reminder_email' );
	register_setting( 'cqpim_settings', 'client_invoice_overdue_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_overdue_email' );
	register_setting( 'cqpim_settings', 'client_invoice_footer' );
	register_setting( 'cqpim_settings', 'client_deposit_invoice_email' );
	register_setting( 'cqpim_settings', 'client_invoice_allow_partial');
	register_setting( 'cqpim_settings', 'client_invoice_twocheck_sid');
	register_setting( 'cqpim_settings', 'client_invoice_receipt_subject' );
	register_setting( 'cqpim_settings', 'client_invoice_receipt_email' );
	register_setting( 'cqpim_settings', 'pto_escrow_email_address' );
	register_setting( 'cqpim_settings', 'pto_escrow_api_key' );
	register_setting( 'cqpim_settings', 'pto_escrow_inspection' );
	// Teams
	register_setting( 'cqpim_settings', 'team_account_subject' );
	register_setting( 'cqpim_settings', 'team_account_email' ); 
	register_setting( 'cqpim_settings', 'team_reset_subject' );
	register_setting( 'cqpim_settings', 'team_reset_email' );
	register_setting( 'cqpim_settings', 'team_project_subject' );
	register_setting( 'cqpim_settings', 'team_project_email' ); 
	register_setting( 'cqpim_settings', 'team_assignment_subject' );
	register_setting( 'cqpim_settings', 'team_assignment_email' );
	register_setting( 'cqpim_settings', 'assignment_response_subject' );
	register_setting( 'cqpim_settings', 'assignment_response_email' );
	register_setting( 'cqpim_settings', 'pto_task_acceptance' );    
	register_setting( 'cqpim_settings', 'pto_task_status' );    
	// Support
	register_setting( 'cqpim_settings', 'cqpim_disable_ticket_priority' );
	register_setting( 'cqpim_settings', 'client_create_ticket_subject' );
	register_setting( 'cqpim_settings', 'support_status' );
	// Email
	register_setting( 'cqpim_settings', 'client_create_ticket_email' );
	register_setting( 'cqpim_settings', 'client_update_ticket_subject' );
	register_setting( 'cqpim_settings', 'client_update_ticket_email' );
	register_setting( 'cqpim_settings', 'company_update_ticket_subject' );
	register_setting( 'cqpim_settings', 'company_update_ticket_email' );
	// FAQ 
	register_setting( 'cqpim_settings', 'cqpim_enable_faq' );
	register_setting( 'cqpim_settings', 'cqpim_enable_faq_dash_accordion' );
	register_setting( 'cqpim_settings', 'cqpim_enable_faq_dash_cats' );
	register_setting( 'cqpim_settings', 'cqpim_enable_faq_dash' );
	// Quote Forms
	register_setting( 'cqpim_settings', 'cqpim_frontend_form' );
	register_setting( 'cqpim_settings', 'cqpim_backend_form' );
	register_setting( 'cqpim_settings', 'form_reg_auto_welcome' );
	register_setting( 'cqpim_settings', 'form_auto_welcome' );
	register_setting( 'cqpim_settings', 'pto_frontend_form_google_recaptcha' );
	register_setting( 'cqpim_settings', 'new_quote_subject' );
	register_setting( 'cqpim_settings', 'new_quote_email' );
	register_setting( 'cqpim_settings', 'cqpim_dash_css' );
	register_setting( 'cqpim_settings', 'cqpim_logout_url' );
	register_setting( 'cqpim_settings', 'gdpr_pp_page' );
	register_setting( 'cqpim_settings', 'gdpr_pp_page_check' );
	register_setting( 'cqpim_settings', 'gdpr_tc_page' );                   
	register_setting( 'cqpim_settings', 'gdpr_tc_page_check' );
	register_setting( 'cqpim_settings', 'gdpr_consent_text' );                      
	register_setting( 'cqpim_settings', 'gdpr_consent' );
	register_setting( 'cqpim_settings', 'pto_cquo_approve' );
	register_setting( 'cqpim_settings', 'pto_creg_approve' );
	register_setting( 'cqpim_settings', 'google_recaptcha_site_key' );
	register_setting( 'cqpim_settings', 'google_recaptcha_secret_key' );
	// Suppliers and Expenses
	register_setting( 'cqpim_settings', 'cqpim_activate_expense_auth' );
	register_setting( 'cqpim_settings', 'cqpim_expense_auth_limit' );
	register_setting( 'cqpim_settings', 'cqpim_expense_auth_members' );
	register_setting( 'cqpim_settings', 'cqpim_auth_email_subject' );
	register_setting( 'cqpim_settings', 'cqpim_auth_email_content' );
	register_setting( 'cqpim_settings', 'cqpim_authorised_email_subject' );
	register_setting( 'cqpim_settings', 'cqpim_authorised_email_content' );
	// Bug Tracker
	register_setting( 'cqpim_settings', 'cqpim_bugs_auto' );
	register_setting( 'cqpim_settings', 'cqpim_new_bug_subject' );
	register_setting( 'cqpim_settings', 'cqpim_new_bug_content' );
	register_setting( 'cqpim_settings', 'cqpim_update_bug_subject' );
	register_setting( 'cqpim_settings', 'cqpim_update_bug_content' );
	// WooCommerce
	register_setting( 'cqpim_settings', 'cqpim_wc_new_project_subject' );
	register_setting( 'cqpim_settings', 'cqpim_wc_new_project_content' );
	// Piping Settings 
	register_setting( 'cqpim_settings', 'cqpim_mail_server' );
	register_setting( 'cqpim_settings', 'cqpim_piping_address' );
	register_setting( 'cqpim_settings', 'cqpim_mailbox_name' );
	register_setting( 'cqpim_settings', 'cqpim_mailbox_pass' );
	register_setting( 'cqpim_settings', 'cqpim_string_prefix' );
	register_setting( 'cqpim_settings', 'cqpim_create_support_on_email' );
	register_setting( 'cqpim_settings', 'cqpim_send_piping_reject' );
	register_setting( 'cqpim_settings', 'cqpim_piping_delete' );
	register_setting( 'cqpim_settings', 'cqpim_bounce_subject' );
	register_setting( 'cqpim_settings', 'cqpim_bounce_content' );
	// Create Support on Unknown Client Email.
	register_setting( 'cqpim_settings', 'cqpim_create_support_on_unknown_email' );

	// Messaging  Settings
	register_setting( 'cqpim_settings', 'cqpim_new_message_subject');
	register_setting( 'cqpim_settings', 'cqpim_new_message_content');
	register_setting( 'cqpim_settings', 'cqpim_messages_allow_client');
	// HTML Email
	register_setting( 'cqpim_settings', 'cqpim_html_email_styles');
	register_setting( 'cqpim_settings', 'cqpim_html_email');
	// Subscriptins
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_subject');
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_content');
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_accept_subject');
	register_setting( 'cqpim_settings', 'cqpim_new_subscription_accept_content');
	register_setting( 'cqpim_settings', 'cqpim_subscription_cancelled_subject');
	register_setting( 'cqpim_settings', 'cqpim_subscription_cancelled_content');
	register_setting( 'cqpim_settings', 'cqpim_subscription_failed_subject');
	register_setting( 'cqpim_settings', 'cqpim_subscription_failed_content');
	register_setting( 'cqpim_settings', 'cqpim_subscription_reminder_subject');
	register_setting( 'cqpim_settings', 'cqpim_subscription_reminder_content');
	register_setting( 'cqpim_settings', 'cqpim_paypal_api_signature');
	register_setting( 'cqpim_settings', 'cqpim_paypal_api_password');
	register_setting( 'cqpim_settings', 'cqpim_paypal_api_username');
	register_setting( 'cqpim_settings', 'cqpim_paypal_enable_sandbox');
	register_setting( 'cqpim_settings', 'cqpim_2checkout_enable_sandbox');
	register_setting( 'cqpim_settings', 'cqpim_twocheck_pub_key');
	register_setting( 'cqpim_settings', 'cqpim_twocheck_priv_key');
	register_setting( 'cqpim_settings', 'cqpim_twocheck_account');
}
// Allow CQPIM admins access to these settings
function pto_settings_page_capability( $capability ) {
	return 'edit_cqpim_settings';
}
add_filter( 'option_page_capability_cqpim_settings', 'pto_settings_page_capability' );
function pto_settings() { ?>
	<div class="wrap" id="cqpim-settings"><div id="icon-tools" class="icon32"></div>
		<h1><?php esc_html_e('Settings', 'projectopia-core'); ?></h1>
		<?php $user = wp_get_current_user(); ?>
		<form method="post" action="options.php" enctype="multipart/form-data">
			<div id="main-container" class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-cog font-black" aria-hidden="true"></i>
						<span class="caption-subject font-black sbold"><?php esc_html_e('Plugin Settings', 'projectopia-core'); ?> </span>
					</div>
				</div>
				<?php 
				$option_group = 'cqpim_settings';
				settings_fields( $option_group ); ?>
				<div id="tabs" style="display: none;">
					<ul>
						<li><a href="#tabs-11"><i class="fa fa-cogs" aria-hidden="true"></i><?php esc_html_e('Plugin Settings', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-1"><i class="fa fa-building-o" aria-hidden="true"></i><?php esc_html_e('Your Company', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-25"><i class="fa fa-dashboard" aria-hidden="true"></i><?php esc_html_e('Admin Dashboard', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-19"><i class="fa fa-clock-o" aria-hidden="true"></i><?php esc_html_e('Business Hours', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-23"><i class="fa fa-user-o" aria-hidden="true"></i><?php esc_html_e('Leads', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-2"><i class="fa fa-envelope-o" aria-hidden="true"></i><?php esc_html_e('Client Email', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-9"><i class="fa fa-dashcube" aria-hidden="true"></i></span><?php esc_html_e('Client Dashboard', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-3"><i class="fa fa-money" aria-hidden="true"></i><?php esc_html_e('Quotes / Estimates', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-4"><i class="fa fa-sticky-note-o" aria-hidden="true"></i><?php esc_html_e('Projects', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-18"><i class="fa fa-bug" aria-hidden="true"></i><?php esc_html_e('Bug Tracker', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-5"><i class="fa fa-bolt" aria-hidden="true"></i><?php esc_html_e('Invoices', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-20"><i class="fa fa-usd" aria-hidden="true"></i><?php esc_html_e('Subscriptions', 'projectopia-core'); ?></a></li>
						<?php if ( 0 ) { ?>
						<li><a href="#tabs-22"><?php esc_html_e('WooCommerce', 'projectopia-core'); ?></a></li>
						<?php } ?>
						<li><a href="#tabs-21"><i class="fa fa-credit-card" aria-hidden="true"></i><?php esc_html_e('Payment Gateways', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-6"><i class="fa fa-users" aria-hidden="true"></i><?php esc_html_e('Team Members', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-10"><i class="fa fa-tasks" aria-hidden="true"></i><?php esc_html_e('Tasks', 'projectopia-core'); ?></a></li>								
						<li><a href="#tabs-7"><i class="fa fa-ticket" aria-hidden="true"></i><?php esc_html_e('Support Tickets', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-24"><i class="fa fa-question-circle-o" aria-hidden="true"></i><?php esc_html_e('FAQ', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-8"><i class="fa fa-file-text-o" aria-hidden="true"></i><?php esc_html_e('Forms', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-16"><i class="fa fa-user" aria-hidden="true"></i><?php esc_html_e('Suppliers / Expenses', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-17"><i class="fa fa-flag" aria-hidden="true"></i><?php esc_html_e('Reporting', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-12"><i class="fa fa-reply-all" aria-hidden="true"></i><?php esc_html_e('Email Piping', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-14"><i class="fa fa-commenting-o" aria-hidden="true"></i><?php esc_html_e('Messaging System', 'projectopia-core'); ?></a></li>
						<li><a href="#tabs-15"><i class="fa fa-file-code-o" aria-hidden="true"></i><?php esc_html_e('HTML Email Template', 'projectopia-core'); ?></a></li>
						<?php
							/**
							 * Create action to add new tab for setting options.
							 * @since 5.0.4
							 */
							do_action( 'pto_add_new_setting_tab_title' );
						?>
						<?php 
						$user = wp_get_current_user();
						if ( in_array( 'administrator', $user->roles ) ) { ?>
							<li><a href="#tabs-13"><i class="fa fa-repeat" aria-hidden="true"></i><?php esc_html_e('Plugin Reset', 'projectopia-core'); ?></a></li>
						<?php } ?>
					</ul>
					<div id="tabs-11">
						<h3><?php esc_html_e('Plugin Name', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('If you\'d like to rename the plugin in the admin menu, you can do so here.', 'projectopia-core'); ?></p>
						<div class="form-group">
					        <label for="pto-name"><?php esc_html_e('Plugin Name:', 'projectopia-core'); ?></label>
					        <div class="input-group">
							    <?php $value = esc_attr( get_option('cqpim_plugin_name') ); ?>
					        	<input id="pto-name" class="form-control input" type="text" name="cqpim_plugin_name" value="<?php echo $value ? esc_attr( $value ) : 'Projectopia'; ?>" />	
					        </div>
				        </div>
						<h3><?php esc_html_e('Plugin Icon', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
						    <?php $disable = esc_attr( get_option('cqpim_use_default_icon') ); ?>
							<input type="checkbox" id="cqpim-default-icon" name="cqpim_use_default_icon" value="1" <?php if ( ! empty($disable) ) { ?> checked="checked"<?php } ?> /> <?php esc_html_e('Use WordPress Default Admin Menu Icon (Cog)', 'projectopia-core'); ?>
						</div>	
						<!--h3><?php //esc_html_e('Documentation Link', 'projectopia-core'); ?></!--h3>
						<div-- class="pto-inline-item-wrapper">
						    <?php //$disable = esc_attr( get_option('cqpim_show_docs_link') ); ?>
							<input type="checkbox" name="cqpim_show_docs_link" value="1" <?php //if ( ! empty($disable) ) { ?> checked="checked"<?php //} ?> /> <?php //esc_html_e('Show Documentation Link in Admin Menu?', 'projectopia-core'); ?>
						</div-->
						<h3><?php esc_html_e('Who\'s Online Widget', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
						    <?php $disable = esc_attr( get_option('cqpim_online_widget') ); ?>
							<input type="checkbox" name="cqpim_online_widget" value="1" <?php if ( ! empty($disable) ) { ?> checked="checked"<?php } ?>/> <?php esc_html_e('Show Who\'s Online Widget on the Admin Dashboard?', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('Date', 'projectopia-core'); ?></h3>
						<div class="form-group">
							<label for="pto-date-format"><?php esc_html_e('Date Format', 'projectopia-core'); ?></label>
							<?php $value = esc_attr( get_option('cqpim_date_format') ); ?>
							<div class="input-group">
								<select id="pto-date-format" name="cqpim_date_format" class="form-control input">
								    <option value=""><?php esc_html_e('Choose a date format', 'projectopia-core'); ?></option>
									<option value="Y-m-d" <?php selected( $value, "Y-m-d" ); ?>>Y-m-d (<?php echo esc_html( date_i18n( 'Y-m-d' ) ); ?>)</option>
									<option value="m/d/Y" <?php selected( $value, "m/d/Y" ); ?>>m/d/Y (<?php echo esc_html( date_i18n( 'm/d/Y' ) ); ?>)</option>
									<option value="d/m/Y" <?php selected( $value, "d/m/Y" ); ?>>d/m/Y (<?php echo esc_html( date_i18n( 'd/m/Y' ) ); ?>)</option>
									<option value="d.m.Y" <?php selected( $value, "d.m.Y" ); ?>>d.m.Y (<?php echo esc_html( date_i18n( 'd.m.Y' ) ); ?>)</option>
								</select>			
							</div>
						</div>
						<h3><?php esc_html_e('Manage Categories', 'projectopia-core'); ?></h3>
						<div class="pto-settings-item-wrapper">
						    <a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_project_cat"><?php esc_html_e('Manage Project Categories', 'projectopia-core'); ?></a> | 
						    <a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_client_cat"><?php esc_html_e('Manage Client Categories', 'projectopia-core'); ?></a> | 
						    <a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_lead_cat"><?php esc_html_e('Manage Lead Categories', 'projectopia-core'); ?></a> | 
						    <a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_faq_cat"><?php esc_html_e('Manage FAQ Categories', 'projectopia-core'); ?></a>
						    <?php if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) { ?>
								| <a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_supplier_cat"><?php esc_html_e('Manage Supplier Categories', 'projectopia-core'); ?></a> | 
						    	<a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_expense_cat"><?php esc_html_e('Manage Expense Categories', 'projectopia-core'); ?></a>
						    <?php } ?>
						    <?php if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) ) { ?>
								| <a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_plan_cat"><?php esc_html_e('Manage Subscription Plan Categories', 'projectopia-core'); ?></a> | 
						    	<a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_subscription_cat"><?php esc_html_e('Manage Subscription Categories', 'projectopia-core'); ?></a>						
						    <?php } ?>
						</div>
						<h3><?php esc_html_e('Avatars', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('Projectopia uses the WordPress avatar to show thumbnails of users. By default this uses Gravatar.org, but you can also use plugins to upload custom avatars.', 'projectopia-core'); ?></p>
						<div class="pto-inline-item-wrapper">
							<?php $disable = get_option('cqpim_disable_avatars'); ?>
							<input type="checkbox" name="cqpim_disable_avatars" value="1" <?php if ( ! empty($disable) ) { ?> checked="checked"<?php } ?>/> <?php esc_html_e('Disable Avatars', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('URL Rewrites', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('By default, invoices, quotes and projects will have Projectopia based URL\'s. This is done for compatibility so that you don\'t end up with duplicate slugs in WordPress. You can change these here. Make sure that the URL slug that you choose does not exist on your site already, and also make sure that you flush your permalinks when these are updated, otherwise you may experience 404 errors.', 'projectopia-core'); ?></p>
						<div class="row">
					        <div class="col-4">
						        <div class="form-group">
					                <label for="pto-invoice-slug"><?php esc_html_e('Invoices:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('cqpim_invoice_slug'); ?>
						        	    <input id="pto-invoice-slug" class="form-control input" type="text" name="cqpim_invoice_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        </div>
							</div>
							<div class="col-4">
						        <div class="form-group">
					                <label for="pto-quote-slug"><?php esc_html_e('Quotes:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('cqpim_quote_slug'); ?>
						        	    <input id="pto-quote-slug" class="form-control input" type="text" name="cqpim_quote_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        </div>
							</div>
							<div class="col-4">
						        <div class="form-group">
					                <label for="pto-project-slug"><?php esc_html_e('Projects:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('cqpim_project_slug'); ?>
						        	    <input id="pto-project-slug" class="form-control input" type="text" name="cqpim_project_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        </div>
							</div>
						</div>
						<div class="row">
					        <div class="col-4">
						        <div class="form-group">
					                <label for="pto-support-slug"><?php esc_html_e('Support Tickets:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('cqpim_support_slug'); ?>
						        	    <input id="pto-support-slug" class="form-control input" type="text" name="cqpim_support_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        </div>
							</div>
							<div class="col-4">
						        <div class="form-group">
					                <label for="pto-task-slug"><?php esc_html_e('Tasks:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('cqpim_task_slug'); ?>
						        	    <input id="pto-task-slug" class="form-control input" type="text" name="cqpim_task_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        </div>
							</div>
							<div class="col-4">
						        <div class="form-group">
					                <label for="pto-faq-slug"><?php esc_html_e('FAQs:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('cqpim_faq_slug'); ?>
						        	    <input id="pto-faq-slug" class="form-control input" type="text" name="cqpim_faq_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        </div>
							</div>
						</div>
						<div class="row">
						    <?php if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) { ?>
						        <div class="col-4">
						            <div class="form-group">
					                    <label for="pto-bug-slug"><?php esc_html_e('Bugs:', 'projectopia-core'); ?></label>
					                    <div class="input-group">
						            	    <?php $value = get_option('cqpim_bug_slug'); ?>
						            	    <input id="pto-bug-slug" class="form-control input" type="text" name="cqpim_bug_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						            	</div>
						            </div>
							    </div>
							<?php } ?>
							<?php if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) ) { ?>
							    <div class="col-4">
						            <div class="form-group">
					                    <label for="pto-subs-slug"><?php esc_html_e('Subscriptions:', 'projectopia-core'); ?></label>
					                    <div class="input-group">
						            	    <?php $value = get_option('cqpim_subs_slug'); ?>
						            	    <input id="pto-subs-slug" class="form-control input" type="text" name="cqpim_subs_slug" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						            	</div>
						            </div>
							    </div>
							<?php } ?>
						</div>						
						<h3><?php esc_html_e('Workflow', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="projectWorkflow-tab" data-toggle="tab" href="#projectWorkflow" role="tab" aria-controls="projectWorkflow" aria-selected="true"><?php esc_html_e('Project Workflow', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="invoiceWorkflow-tab" data-toggle="tab" href="#invoiceWorkflow" role="tab" aria-controls="invoiceWorkflow" aria-selected="false"><?php esc_html_e('Invoice Workflow', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="projectWorkflow" role="tabpanel" aria-labelledby="projectWorkflow-tab">
                                        <div class="tab-pane-body">
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('enable_quotes'); ?>
											    <input type="checkbox" name="enable_quotes" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Enable the quotes system.', 'projectopia-core'); ?>
											</div>
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('enable_quote_terms'); ?>
											    <input type="checkbox" name="enable_quote_terms" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Add Terms & Conditions section to quotes.', 'projectopia-core'); ?>
											</div>
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('enable_project_creation'); ?>
											    <input type="checkbox" name="enable_project_creation" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Create a project automatically when a quote is accepted.', 'projectopia-core'); ?>
											</div>
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('enable_project_contracts'); ?>
											    <input type="checkbox" name="enable_project_contracts" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Enable the contracts feature in projects.', 'projectopia-core'); ?>
											</div>
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('auto_contract'); ?>
											    <input type="checkbox" name="auto_contract" id="auto_contract" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Send the project contract automatically when a quote is accepted and the project is created.', 'projectopia-core'); ?>
											</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="invoiceWorkflow" role="tabpanel" aria-labelledby="invoiceWorkflow-tab">
                                        <div class="tab-pane-body">
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('disable_invoices'); ?>
											    <input type="checkbox" name="disable_invoices" id="disable_invoices" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Disable the Invoice section.', 'projectopia-core'); ?>
											</div>
											<div class="form-group">
												<label for="pto-invoice-workflow"><?php esc_html_e('Invoice Type:', 'projectopia-core'); ?></label>
												<?php $value = get_option('invoice_workflow'); ?>
												<div class="input-group">
													<select id="pto-invoice-workflow" name="invoice_workflow" class="form-control input">
														<option value="0" <?php selected( $value, "0" ); ?>><?php esc_html_e('Create Deposit Invoice - Type 1', 'projectopia-core'); ?></option>
														<option value="1" <?php selected( $value, "1" ); ?>><?php esc_html_e('Create Deposit Invoice - Type 2', 'projectopia-core'); ?></option>
													</select>			
												</div>
											</div>
											<p><?php printf( '<strong>%1$s</strong> %2$s', esc_html__('Type 1:', 'projectopia-core'), esc_html__('Create deposit invoice (if deposit amount selected) automatically when contract is signed (contract mode only) or when project is created from quote (no contract mode). Create a completion invoice (project total minus deposit) when project is marked as signed off.', 'projectopia-core') ); ?><br>
											<?php printf( '<strong>%1$s</strong> %2$s', esc_html__('Type 2:', 'projectopia-core'), esc_html__('Create deposit invoice (if deposit amount selected) automatically when contract is signed (contract mode only) or when project is created from quote (no contract mode). Create a new invoice when milestones are marked as complete for the total milestone fee minus the deposit percentage.', 'projectopia-core') ); ?></p>
											<div class="pto-inline-item-wrapper">
											    <?php $checked = get_option('auto_send_invoices'); ?>
											    <input type="checkbox" name="auto_send_invoices" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Send invoices to the client automatically when they are created.', 'projectopia-core'); ?>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>						
					</div>
					<div id="tabs-1">
						<h3><?php esc_html_e('Company Details', 'projectopia-core'); ?></h3>
						<div class="form-group">
						    <label for="company_name"><?php esc_html_e('Company Name', 'projectopia-core'); ?></label>
							<div class="input-group">
								<input type="text" class="form-control input" id="company_name" name="company_name" value="<?php echo esc_attr( get_option('company_name') ); ?>" />
							</div>
						</div>
						<div class="form-group">
						    <label for="company_address"><?php esc_html_e('Company Address', 'projectopia-core'); ?></label>
							<div class="input-group">
								<textarea id="company_address" class="form-control input pto-textarea" name="company_address"><?php echo esc_attr( get_option('company_address') ); ?></textarea>
							</div>
						</div>
						<div class="row">
					        <div class="col-4">
						        <div class="form-group">
									<label for="company_postcode"><?php esc_html_e('Company Postcode', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="company_postcode" class="form-control input" name="company_postcode" value="<?php echo esc_attr( get_option('company_postcode') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="company_telephone"><?php esc_html_e('Company Telephone', 'projectopia-core'); ?></label>
							    	<div class="input-group">
							    		<input type="text" id="company_telephone" class="form-control input" name="company_telephone" value="<?php echo esc_attr( get_option('company_telephone') ); ?>" />
							    	</div>
								</div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="company_number"><?php esc_html_e('Company Number', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="text" id="company_number" class="form-control input" name="company_number" value="<?php echo esc_attr( get_option('company_number') ); ?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
					        <div class="col-6">
						        <div class="form-group">
									<label for="company_sales_email"><?php esc_html_e('Sales Email', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="company_sales_email" class="form-control input" name="company_sales_email" value="<?php echo esc_attr( get_option('company_sales_email') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-6">
							    <div class="form-group">
							    	<label for="company_accounts_email"><?php esc_html_e('Accounts Email', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="text" id="company_accounts_email" class="form-control input" name="company_accounts_email" value="<?php echo esc_attr( get_option('company_accounts_email') ); ?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
					        <div class="col-6">
						        <div class="form-group">
									<label for="company_support_email"><?php esc_html_e('Support Email (For Support Tickets)', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="company_support_email" class="form-control input" name="company_support_email" value="<?php echo esc_attr( get_option('company_support_email') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-6">
							    <div class="form-group">
							    	<label for="cqpim_cc_address"><?php esc_html_e('Outgoing Email BCC Address (copies all outgoing emails)', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="text" id="cqpim_cc_address" class="form-control input" name="cqpim_cc_address" value="<?php echo esc_attr( get_option('cqpim_cc_address') ); ?>" />
									</div>
								</div>
							</div>
						</div>
						<h3><?php esc_html_e('Company Logo', 'projectopia-core'); ?></h3>
						<?php $logo = get_option('company_logo'); 
						if ( $logo ) { ?>
							<img style="max-width:300px;background:#ececec" src="<?php echo esc_url( $logo['company_logo'] ); ?>" /><br />
							<button class="remove_logo cancel-colorbox cancel-creation piaBtn redColor mt-4" data-type="company_logo"><?php esc_html_e('Remove', 'projectopia-core'); ?></button><br /><br />
						<?php } ?>
						<input type="file" id="pto-company-logo" name="company_logo" />
						<div class="clear"></div>
						<h3><?php esc_html_e('Financial Details', 'projectopia-core'); ?></h3>
						<div class="row">
					        <div class="col-4">
						        <div class="form-group">
									<label for="currency_symbol"><?php esc_html_e('Currency Symbol', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="currency_symbol" class="form-control input" name="currency_symbol" value="<?php echo esc_attr( get_option('currency_symbol') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="currency_symbol_position"><?php esc_html_e('Currency Symbol Position', 'projectopia-core'); ?></label>
									<?php $value = get_option('currency_symbol_position'); ?>
							    	<div class="input-group">
						            	<select name="currency_symbol_position" id="currency_symbol_position" class="form-control input">
										    <option value="l" <?php if ( $value == 'l' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Before Amount', 'projectopia-core'); ?></option>
									        <option value="r" <?php if ( $value == 'r' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('After Amount', 'projectopia-core'); ?></option>
						            	</select>			
						            </div>
								</div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="currency_code"><?php esc_html_e('Currency Code (Used for Payment Gateways)', 'projectopia-core'); ?><i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="" style="margin-left: 8px;" data-original-title="<?php esc_attr_e('Some of the available currencies are not supported by PayPal.', 'projectopia-core'); ?>"></i></label>
							    	<div class="input-group">
									    <?php $accode = get_option('currency_code'); ?>
									    <select name="currency_code" id="currency_code" class="form-control input">
									    	<option value="0"><?php esc_html_e('Choose a currency', 'projectopia-core'); ?></option>
									    	<?php $codes = pto_return_currency_select();
									    	foreach ( $codes as $key => $code ) {
									    		echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $accode, false ) . '>' . esc_html( $code ) . '</option>';
									    	} ?>
									    </select>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
					        <div class="col-6">
						        <div class="form-group">
									<label for="company_bank_name"><?php esc_html_e('Account Name', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="company_bank_name" class="form-control input" name="company_bank_name" value="<?php echo esc_attr( get_option('company_bank_name') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-6">
							    <div class="form-group">
							    	<label for="company_bank_ac"><?php esc_html_e('Account Number', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="text" id="company_bank_ac" class="form-control input" name="company_bank_ac" value="<?php echo esc_attr( get_option('company_bank_ac') ); ?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
					        <div class="col-4">
						        <div class="form-group">
									<label for="company_bank_sc"><?php esc_html_e('Sort Code', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="company_bank_sc" class="form-control input" name="company_bank_sc" value="<?php echo esc_attr( get_option('company_bank_sc') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="company_bank_iban"><?php esc_html_e('IBAN', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="text" id="company_bank_iban" class="form-control input" name="company_bank_iban" value="<?php echo esc_attr( get_option('company_bank_iban') ); ?>" />
									</div>
								</div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="company_invoice_terms"><?php esc_html_e('Invoice Terms', 'projectopia-core'); ?></label>
							    	<div class="input-group">
								        <?php $terms = get_option('company_invoice_terms'); ?>
								        <select id="company_invoice_terms" name="company_invoice_terms" class="form-control input">
								        	<option value="1" <?php if ( $terms == 1 ) { echo 'selected'; } ?>><?php $text = esc_html__('Due on Receipt', 'projectopia-core'); esc_html_e('Due on Receipt', 'projectopia-core'); ?></option>
								        	<option value="7" <?php if ( $terms == 7 ) { echo 'selected'; } ?>>7 <?php $text = esc_html__('days', 'projectopia-core'); esc_html_e('days', 'projectopia-core'); ?></option>
								        	<option value="14" <?php if ( $terms == 14 ) { echo 'selected'; } ?>>14 <?php $text = esc_html__('days', 'projectopia-core'); esc_html_e('days', 'projectopia-core'); ?></option>
								        	<option value="28" <?php if ( $terms == 28 ) { echo 'selected'; } ?>>28 <?php $text = esc_html__('days', 'projectopia-core'); esc_html_e('days', 'projectopia-core'); ?></option>
								        	<option value="30" <?php if ( $terms == 30 ) { echo 'selected'; } ?>>30 <?php $text = esc_html__('days', 'projectopia-core'); esc_html_e('days', 'projectopia-core'); ?></option>
								        	<option value="60" <?php if ( $terms == 60 ) { echo 'selected'; } ?>>60 <?php $text = esc_html__('days', 'projectopia-core'); esc_html_e('days', 'projectopia-core'); ?></option>
								        	<option value="90" <?php if ( $terms == 90 ) { echo 'selected'; } ?>>90 <?php $text = esc_html__('days', 'projectopia-core'); esc_html_e('days', 'projectopia-core'); ?></option>
								        </select>
									</div>
								</div>
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('currency_symbol_space'); ?>
							<input type="checkbox" id="currency_symbol_space" name="currency_symbol_space" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Add Space Between Amount and Currency Symbol', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('currency_decimal'); ?>
							<input type="checkbox" id="currency_decimal" name="currency_decimal" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Remove Decimals in Currency', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('currency_comma'); ?>
							<input type="checkbox" id="currency_comma" name="currency_comma" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Add comma separators (eg. 1,000 or 100,000)', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('allow_client_currency_override'); ?>
							<input type="checkbox" id="allow_client_currency_override" name="allow_client_currency_override" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Allow Currency to be Set Per Client', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('allow_quote_currency_override'); ?>
							<input type="checkbox" id="allow_quote_currency_override" name="allow_quote_currency_override" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Allow Currency to be Set Per Quote', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('allow_project_currency_override'); ?>
							<input type="checkbox" id="allow_project_currency_override" name="allow_project_currency_override" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Allow Currency to be Set Per Project', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
						    <?php $value = get_option('allow_invoice_currency_override'); ?>
							<input type="checkbox" id="allow_invoice_currency_override" name="allow_invoice_currency_override" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Allow Currency to be Set Per Invoice', 'projectopia-core'); ?>
						</div>
						<?php if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) { ?>
							<div class="pto-inline-item-wrapper">
							    <?php $value = get_option('allow_supplier_currency_override'); ?>
								<input type="checkbox" id="allow_supplier_currency_override" name="allow_supplier_currency_override" value="1" <?php if ( $value == '1' ) { echo 'checked'; } ?> /> <?php esc_html_e('Allow Currency to be Set Per Supplier', 'projectopia-core'); ?>
							</div>
						<?php } ?>
						<h3><?php esc_html_e('Tax Settings', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="primaryTax-tab" data-toggle="tab" href="#primaryTax" role="tab" aria-controls="primaryTax" aria-selected="true"><?php esc_html_e('Sales Tax', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="secondaryTax-tab" data-toggle="tab" href="#secondaryTax" role="tab" aria-controls="secondaryTax" aria-selected="false"><?php esc_html_e('Secondary Sales Tax', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="primaryTax" role="tabpanel" aria-labelledby="primaryTax-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('These settings apply to sales tax, such as VAT. If you do not charge sales tax, then leave these fields blank.', 'projectopia-core'); ?></p>
											<div class="row">
					    					    <div class="col-4">
											        <div class="form-group">
														<label for="sales_tax_rate"><?php esc_html_e('Tax Percentage (eg. 20)', 'projectopia-core'); ?></label>
														<div class="input-group">
															<input type="text" name="sales_tax_rate" id="sales_tax_rate" class="form-control input" value="<?php echo esc_attr( get_option('sales_tax_rate') ); ?>" />
														</div>
											        </div>
												</div>
												<div class="col-4">
												    <div class="form-group">
												    	<label for="sales_tax_name"><?php esc_html_e('Tax Name (eg. VAT)', 'projectopia-core'); ?></label>
												    	<div class="input-group">
															<input type="text" name="sales_tax_name" id="sales_tax_name" class="form-control input" value="<?php echo esc_attr( get_option('sales_tax_name') ); ?>" />
														</div>
													</div>
												</div>
												<div class="col-4">
												    <div class="form-group">
												    	<label for="sales_tax_reg"><?php esc_html_e('Tax Reg Number', 'projectopia-core'); ?></label>
												    	<div class="input-group">
															<input type="text" name="sales_tax_reg" id="sales_tax_reg" class="form-control input" value="<?php echo esc_attr( get_option('sales_tax_reg') ); ?>" />
														</div>
													</div>
												</div>
											</div>
										</div>
                                    </div>
                                    <div class="tab-pane fade" id="secondaryTax" role="tabpanel" aria-labelledby="secondaryTax-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('These settings apply to a secondary sales tax. If you do not charge a secondary sales tax, then leave these fields blank.', 'projectopia-core'); ?></p>
											<div class="row">
					    					    <div class="col-4">
											        <div class="form-group">
														<label for="secondary_sales_tax_rate"><?php esc_html_e('Secondary Tax Percentage (eg. 20)', 'projectopia-core'); ?></label>
														<div class="input-group">
															<input type="text" name="secondary_sales_tax_rate" id="secondary_sales_tax_rate" class="form-control input" value="<?php echo esc_attr( get_option('secondary_sales_tax_rate') ); ?>" />
														</div>
											        </div>
												</div>
												<div class="col-4">
												    <div class="form-group">
												    	<label for="secondary_sales_tax_name"><?php esc_html_e('Secondary Tax Name (eg. VAT)', 'projectopia-core'); ?></label>
												    	<div class="input-group">
															<input type="text" name="secondary_sales_tax_name" id="secondary_sales_tax_name" class="form-control input" value="<?php echo esc_attr( get_option('secondary_sales_tax_name') ); ?>" />
														</div>
													</div>
												</div>
												<div class="col-4">
												    <div class="form-group">
												    	<label for="secondary_sales_tax_reg"><?php esc_html_e('Secondary Tax Reg Number', 'projectopia-core'); ?></label>
												    	<div class="input-group">
															<input type="text" name="secondary_sales_tax_reg" id="secondary_sales_tax_reg" class="form-control input" value="<?php echo esc_attr( get_option('secondary_sales_tax_reg') ); ?>" />
														</div>
													</div>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>					
					</div>
					<div id="tabs-19">
						<h3><?php esc_html_e('Business Hours', 'projectopia-core'); ?></h3>
						<?php $business = get_option('pto_opening'); ?>
						<table class="pto_business_hours" style="width: 100%;">
							<thead>
								<tr>
									<th><?php esc_html_e('Day', 'projectopia-core'); ?></th>
									<th><?php esc_html_e('Opening Time', 'projectopia-core'); ?></th>
									<th><?php esc_html_e('Closing Time', 'projectopia-core'); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[mon][active]" value="1" <?php checked(1, isset($business['mon']['active']) ? $business['mon']['active'] : 0); ?>/> <?php esc_html_e('Monday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input class="form-control input timepicker" type="text" name="pto_opening[mon][open]" value="<?php echo isset($business['mon']['open']) ? esc_attr( $business['mon']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input class="form-control input timepicker" type="text" name="pto_opening[mon][close]" value="<?php echo isset($business['mon']['close']) ? esc_attr( $business['mon']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[tue][active]" value="1" <?php checked(1, isset($business['tue']['active']) ? $business['tue']['active'] : 0); ?>/> <?php esc_html_e('Tuesday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[tue][open]" value="<?php echo isset($business['tue']['open']) ? esc_attr( $business['tue']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[tue][close]" value="<?php echo isset($business['tue']['close']) ? esc_attr( $business['tue']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[wed][active]" value="1" <?php checked(1, isset($business['wed']['active']) ? $business['wed']['active'] : 0); ?>/> <?php esc_html_e('Wednesday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[wed][open]" value="<?php echo isset($business['wed']['open']) ? esc_attr( $business['wed']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[wed][close]" value="<?php echo isset($business['wed']['close']) ? esc_attr( $business['wed']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[thu][active]" value="1" <?php checked(1, isset($business['thu']['active']) ? $business['thu']['active'] : 0); ?>/> <?php esc_html_e('Thursday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[thu][open]" value="<?php echo isset($business['thu']['open']) ? esc_attr( $business['thu']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[thu][close]" value="<?php echo isset($business['thu']['close']) ? esc_attr( $business['thu']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[fri][active]" value="1" <?php checked(1, isset($business['fri']['active']) ? $business['fri']['active'] : 0); ?>/> <?php esc_html_e('Friday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[fri][open]" value="<?php echo isset($business['fri']['open']) ? esc_attr( $business['fri']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[fri][close]" value="<?php echo isset($business['fri']['close']) ? esc_attr( $business['fri']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[sat][active]" value="1" <?php checked(1, isset($business['sat']['active']) ? $business['sat']['active'] : 0); ?>/> <?php esc_html_e('Saturday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[sat][open]" value="<?php echo isset($business['sat']['open']) ? esc_attr( $business['sat']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[sat][close]" value="<?php echo isset($business['sat']['close']) ? esc_attr( $business['sat']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="pto-cb-wrapper">
											<input type="checkbox" name="pto_opening[sun][active]" value="1" <?php checked(1, isset($business['sun']['active']) ? $business['sun']['active'] : 0); ?>/> <?php esc_html_e('Sunday', 'projectopia-core'); ?>
										</div>
									</td>									
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[sun][open]" value="<?php echo isset($business['sun']['open']) ? esc_attr( $business['sun']['open'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<div class="input-group">
												<input type="text" class="form-control input timepicker" name="pto_opening[sun][close]" value="<?php echo isset($business['sun']['close']) ? esc_attr( $business['sun']['close'] ) : ''; ?>" autocomplete="off" />
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<?php 
						if ( pto_return_open() == 1 ) {
							echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__( 'You are currently closed.', 'projectopia-core' ) . '</div>';
						}
						if ( pto_return_open() == 2 ) {
							echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__( 'You are currently open.', 'projectopia-core' ) . '</div>';
						}                           
						if ( pto_return_open() == 3 ) {
							echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__( 'Unable to calculate opening status for today, ensure you have completed both opening and closing time.', 'projectopia-core' ) . '</div>';
						}
						?>
						<div class="clear"></div>
						<h3><?php esc_html_e('Opening Times Warnings', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="supportTicket-tab" data-toggle="tab" href="#supportTicket" role="tab" aria-controls="supportTicket" aria-selected="true"><?php esc_html_e('Support Ticket Warning', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="shortcode-tab" data-toggle="tab" href="#shortcode" role="tab" aria-controls="shortcode" aria-selected="false"><?php esc_html_e('Shortcode Warning', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="supportTicket" role="tabpanel" aria-labelledby="supportTicket-tab">
                                        <div class="tab-pane-body">
											<div class="pto-inline-item-wrapper">
									            <?php $option = get_option('pto_support_opening_warning'); ?>
									            <input type="checkbox" name="pto_support_opening_warning" value="1" <?php checked(1, $option); ?>/> <?php esc_html_e('Show an open / closed warning on support ticket pages of the client dashboard.', 'projectopia-core'); ?>
									        </div>
									        <div class="pto-settings-label">
									        	<?php esc_html_e('Open Message', 'projectopia-core'); ?>
									        </div>
									        <div class="form-group">
									        	<div class="input-group">
									                <textarea class="cqpim-alert cqpim-alert-info form-control input pto-textarea" name="pto_support_open_message"><?php echo esc_html( get_option('pto_support_open_message') ); ?></textarea>
									        	</div>
									        </div>
									        <div class="pto-settings-label">
									        	<?php esc_html_e('Closed Message', 'projectopia-core'); ?>
									        </div>
									        <div class="form-group">
									        	<div class="input-group">
									        		<textarea class="cqpim-alert cqpim-alert-warning form-control input pto-textarea" name="pto_support_closed_message"><?php echo esc_html( get_option('pto_support_closed_message') ); ?></textarea>					
									        	</div>
									        </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="shortcode" role="tabpanel" aria-labelledby="shortcode-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('You can display the [pto_opening_hours] shortcode anywhere on your site, and it will display the alerts entered below depending on whether you are open or closed.', 'projectopia-core'); ?></p>
                                            <div class="pto-settings-label">
												<?php esc_html_e('Open Message', 'projectopia-core'); ?>
											</div>
											<div class="form-group">
												<div class="input-group">
													<textarea class="cqpim-alert cqpim-alert-info form-control input pto-textarea" name="pto_shortcode_open_message"><?php echo esc_html( get_option('pto_shortcode_open_message') ); ?></textarea>
												</div>
											</div>
											<div class="pto-settings-label">
												<?php esc_html_e('Closed Message', 'projectopia-core'); ?>
					                        </div>
											<div class="form-group">
												<div class="input-group">
													<textarea class="cqpim-alert cqpim-alert-warning form-control input pto-textarea" name="pto_shortcode_closed_message"><?php echo esc_html( get_option('pto_shortcode_closed_message') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary save-business" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>						
					</div>
					<div id="tabs-25">
						<h3><?php esc_html_e('Admin Dashboard', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('Save Dashboard Meta box Filters', 'projectopia-core'); ?></p>
						<div class="pto-inline-item-wrapper">
							<?php $is_check = get_option('cqpim_save_dashboard_metabox_filters'); ?>
							<input type="checkbox" name="cqpim_save_dashboard_metabox_filters" value="1" <?php if ( ! empty($is_check) ) { ?> checked="checked"<?php } ?>/> <?php esc_html_e('Preserve filters for Active Projects meta box', 'projectopia-core'); ?>
					    </div>
						<div class="clear"></div>
						<h3><?php esc_html_e('Admin Dashboard Custom CSS', 'projectopia-core'); ?></h3>
						<div class="form-group">
							<div class="input-group">
								<textarea style="height:500px;" class="form-control input pto-textarea pto-te" name="cqpim_admin_dash_css" id="cqpim_admin_dash_css"><?php echo esc_textarea( get_option('cqpim_admin_dash_css') ); ?></textarea>
					        </div>
					    </div>
						<div class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</div>
					</div>
					<div id="tabs-9">
						<h3><?php esc_html_e('Client Dashboard', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('Projectopia has a full width theme included that is used on the Client Dashboard. If you prefer, you can have the Client Dashboard load inside your active WordPress theme instead.', 'projectopia-core'); ?></p>
						<?php 
							$type = get_option('client_dashboard_type');
							$gfont = get_option('client_dashboard_gfont');
							$theme = wp_get_theme();
							$fonts = get_transient( 'pto_saved_gfonts' );
							if ( ! $fonts || ! is_array( $fonts ) ) {
								$response = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAzi_qLI94l0cSZHZL8MPXYTNTPNSN4vmw' );
								if ( is_array( $response ) && ! is_wp_error( $response ) ) {
									$fonts = [
										'default' => esc_html__( 'Default', 'projectopia-core' ),
									];
									$body = json_decode( $response['body'], true );
									foreach ( $body['items'] as $item ) {
										$fonts[ str_replace( [ ' ', '_' ], '+', $item['family'] ) ] = $item['family'];
									}
									set_transient( 'pto_saved_gfonts', $fonts, 86400 );
								}
							}
						?>
						<div class="row">
					        <div class="col-6">
								<div class="form-group">
									<label for="client_dashboard_type"><?php esc_html_e('Select Theme:', 'projectopia-core'); ?></label>
									<div class="input-group">
										<select id="client_dashboard_type" name="client_dashboard_type" class="form-control input full-width">
											<option value="inc" <?php echo ($type == 'inc') ? 'selected="selected"' : '';?>><?php esc_html_e('Projectopia Client Dashboard Theme', 'projectopia-core'); ?></option>
											<option value="active" <?php echo (empty($type) || $type == 'active') ? 'selected="selected"' : '';?>><?php esc_html_e('Current Active WP Theme', 'projectopia-core'); ?> (<?php echo esc_html( $theme->name ); ?>)</option>
											<?php do_action('client_dashboard_type', $type); ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-6 hide-cd-theme">
						        <div class="form-group">
									<label for="client_dashboard_gfont"><?php esc_html_e('Select Font Family:', 'projectopia-core'); ?></label>
									<div class="input-group">
										<select id="client_dashboard_gfont" name="client_dashboard_gfont" class="form-control input full-width">
											<?php
											foreach ( $fonts as $key => $value ) {
												echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $gfont, false ) . '>' . esc_attr( $value ) . '</option>';
											}
											?>
										</select>
									</div>
						        </div>
							</div>
						</div>
						<div class="row hide-cd-theme">
							<div class="col-2">
						        <div class="form-group">
					                <label for="pto-cd-primary-color"><?php esc_html_e('Primary Color:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('client_dashboard_primary_color', '#002b78'); ?>
						        	    <input id="pto-cd-primary-color" class="form-control input" type="color" name="client_dashboard_primary_color" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
									<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="#002b78"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
						        </div>
							</div>
							<div class="col-2">
						        <div class="form-group">
					                <label for="pto-cd-secondary-color"><?php esc_html_e('Secondary Color:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('client_dashboard_secondary_color', '#36c6d3'); ?>
						        	    <input id="pto-cd-secondary-color" class="form-control input" type="color" name="client_dashboard_secondary_color" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        	<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="#36c6d3"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
						        </div>
							</div>
							<div class="col-2">
						        <div class="form-group">
					                <label for="pto-cd-link-color"><?php esc_html_e('Link Color:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('client_dashboard_link_color', pto_adjust_color_brightness( '#001529', -0.1 )); ?>
						        	    <input id="pto-cd-link-color" class="form-control input" type="color" name="client_dashboard_link_color" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        	<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="<?php echo esc_attr( pto_adjust_color_brightness( '#001529', -0.1 ) ); ?>"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
						        </div>
							</div>
							<div class="col-2">
						        <div class="form-group">
					                <label for="pto-cd-link-hover-color"><?php esc_html_e('Link Hover Color:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('client_dashboard_link_hover_color', pto_adjust_color_brightness( '#001529', -0.3 )); ?>
						        	    <input id="pto-cd-link-hover-color" class="form-control input" type="color" name="client_dashboard_link_hover_color" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        	<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="<?php echo esc_attr( pto_adjust_color_brightness( '#001529', -0.3 ) ); ?>"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
						        </div>
							</div>
							<div class="col-2">
						        <div class="form-group">
					                <label for="pto-cd-prfimary-color"><?php esc_html_e('Button Color:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('client_dashboard_button_color', '#337ab7'); ?>
						        	    <input id="pto-cd-primafry-color" class="form-control input" type="color" name="client_dashboard_button_color" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        	<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="#337ab7"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
						        </div>
							</div>
							<div class="col-2">
						        <div class="form-group">
					                <label for="pto-cd-text-color"><?php esc_html_e('Text Color:', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <?php $value = get_option('client_dashboard_text_color', '#001529'); ?>
						        	    <input id="pto-cd-text-color" class="form-control input" type="color" name="client_dashboard_text_color" value="<?php echo esc_attr( $value ); ?>" style="border-radius: .375rem;" />
						        	</div>
						        	<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="#000000"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
						        </div>
							</div>
							
						</div>
						<h4><?php esc_html_e('Client Dashboard Logout URL', 'projectopia-core'); ?></h4>
						<p class="pto-subheading"><?php esc_html_e('This must be on the same domain as the plugin, otherwise this setting will not work.', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<?php $logout = get_option('cqpim_logout_url'); ?>
						        <input type="URL" name="cqpim_logout_url" id="cqpim_logout_url" class="form-control input" value="<?php echo esc_url( $logout ); ?>" />
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $client_settings = get_option('allow_client_settings'); ?>
							<input type="checkbox" name="allow_client_settings" id="allow_client_settings" value="1" <?php checked($client_settings, 1, true); ?>/> <?php esc_html_e('Allow Clients to update their details from their dashboard', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $client_settings = get_option('allow_client_users'); ?>
							<input type="checkbox" name="allow_client_users" id="allow_client_users" value="1" <?php checked($client_settings, 1, true); ?>/> <?php esc_html_e('Allow Clients to grant access to their dashboard & create users for other team members/colleagues.', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('Client Registration', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
							<?php $client_settings = get_option('cqpim_login_reg'); ?>
							<input type="checkbox" name="cqpim_login_reg" id="cqpim_login_reg" value="1" <?php checked($client_settings, 1, true); ?>/> <?php esc_html_e('Allow Clients to Register from the Login Screen', 'projectopia-core'); ?>
					    </div>
						<div class="pto-inline-item-wrapper">
							<?php $client_settings = get_option('cqpim_login_reg_company'); ?>
						    <input type="checkbox" name="cqpim_login_reg_company" id="cqpim_login_reg_company" value="1" <?php checked($client_settings, 1, true); ?>/> <?php esc_html_e('Require a Company Name to be entered on the registration form', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<input type="checkbox" name="pto_dcreg_approve" value="1" <?php checked(get_option('pto_dcreg_approve'), 1); ?> /> <?php esc_html_e('Do not send login details until client is approved by admin', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('Dashboard Password Reset', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This email is sent when a client requests a password reset.', 'projectopia-core'); ?></p>
						<h4><?php esc_html_e('Password Reset Email Subject', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" class="form-control input" id="client_password_reset_subject" name="client_password_reset_subject" value="<?php echo esc_attr( get_option('client_password_reset_subject') ); ?>" />
							</div>
						</div>
						<h4><?php esc_html_e('Password Reset Email Content', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<textarea class="form-control input pto-textarea pto-h-400" id="client_password_reset_content" name="client_password_reset_content"><?php echo esc_html( get_option('client_password_reset_content') ); ?></textarea>
							</div>
						</div>
						<h3><?php esc_html_e('Dashboard Logo', 'projectopia-core'); ?></h3>
							<?php 
							$logo = get_option('cqpim_dash_logo'); 
							if ( $logo ) { ?>
								<img style="max-width: 300px;background: #ececec;" src="<?php echo esc_url( $logo['cqpim_dash_logo'] ); ?>" />
								<br />
								<button class="remove_logo mt-20 piaBtn redColor" data-type="cqpim_dash_logo"><?php esc_html_e('Remove', 'projectopia-core'); ?></button>
								<br /><br />
							<?php } ?>
							<input type="file" name="cqpim_dash_logo" />
						<br /><br />
						<div class="clear"></div>	
						<h3><?php esc_html_e('Login/Register Page Background Image', 'projectopia-core'); ?></h3>
						<?php $logo = get_option('cqpim_dash_bg'); 
						if ( $logo ) { ?>
							<img style="max-width: 300px;background: #ececec;" src="<?php echo esc_url( $logo['cqpim_dash_bg'] ); ?>" />
							<br />
							<button class="remove_logo mt-20 piaBtn redColor" data-type="cqpim_dash_bg"><?php esc_html_e('Remove', 'projectopia-core'); ?></button>
							<br /><br />
						<?php } ?>
						<input type="file" name="cqpim_dash_bg" /><br /><br />
						<div class="clear"></div>	
						<h3><?php esc_html_e('Login/Register Page Background Color', 'projectopia-core'); ?></h3>
						<div class="row">
							<div class="col-2">
								<div class="form-group">
									<div class="input-group">
										<input id="pto-cd-primary-color" class="form-control input" type="color" name="client_login_bg_color" value="<?php echo esc_attr( get_option('client_login_bg_color', '#3B3F51') ); ?>" style="border-radius: .375rem;">
									</div>
									<div class="text-center mt-1" style="font-size: 12px;"><a href="#" class="set-default-color" data-color="#3B3F51"><?php esc_html_e('Set Default', 'projectopia-core'); ?></a></div>
								</div>
							</div>
						</div>
						<h3><?php esc_html_e('Built-In Client Dashboard Custom CSS', 'projectopia-core'); ?></h3>
						<div class="form-group">
							<div class="input-group">
								<textarea style="height: 500px;" class="form-control input pto-textarea pto-te" name="cqpim_dash_css" id="cqpim_dash_css"><?php echo esc_html( get_option('cqpim_dash_css') ); ?></textarea>
							</div>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>						
					</div>
					<div id="tabs-23">						
						<h3><?php esc_html_e('New Lead Email Notification', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This email is sent to all users who have access to the Leads system when a new lead is submitted via a form', 'projectopia-core'); ?></p>
						<h4><?php esc_html_e('New Lead Email Subject', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" id="new_lead_email_subject" class="form-control input" name="new_lead_email_subject" value="<?php echo esc_attr( get_option('new_lead_email_subject') ); ?>" />
							</div>
						</div>
						<h4><?php esc_html_e('New Lead Email Content', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<textarea id="new_lead_email_content" class="form-control input pto-textarea pto-h-300" name="new_lead_email_content"><?php echo esc_html( get_option('new_lead_email_content') ); ?></textarea>
							</div>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>
					</div>
					<div id="tabs-2">						
						<h3><?php esc_html_e('Client Email Settings', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="welcomeEmail-tab" data-toggle="tab" href="#welcomeEmail" role="tab" aria-controls="welcomeEmail" aria-selected="true"><?php esc_html_e('Welcome Email', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="passwordResetEmail-tab" data-toggle="tab" href="#passwordResetEmail" role="tab" aria-controls="passwordResetEmail" aria-selected="false"><?php esc_html_e('Password Reset', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="newContactEmail-tab" data-toggle="tab" href="#newContactEmail" role="tab" aria-controls="newContactEmail" aria-selected="false"><?php esc_html_e('New Contract Email', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="welcomeEmail" role="tabpanel" aria-labelledby="welcomeEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('When a Client is created, a user account is also created to allow the new client to log in to their dashboard. On this page you can choose whether or not to send an automated welcome email when the client\'s account has been added.', 'projectopia-core'); ?></p>
											<div class="pto-inline-item-wrapper">
												<?php $auto_welcome = get_option('auto_welcome'); ?>
											    <input type="checkbox" name="auto_welcome" id="auto_welcome" value="1" <?php checked($auto_welcome, 1, true); ?>/> <?php esc_html_e('Send the client a welcome email with login details to their dashboard (Recommended).', 'projectopia-core'); ?>
											</div>
											<h4><?php esc_html_e('Welcome Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" id="auto_welcome_subject" class="form-control input" name="auto_welcome_subject" value="<?php echo esc_attr( get_option('auto_welcome_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Welcome Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea id="auto_welcome_content" class="form-control input pto-textarea pto-h-400" name="auto_welcome_content"><?php echo esc_html( get_option('auto_welcome_content') ); ?></textarea>
												</div>
											</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="passwordResetEmail" role="tabpanel" aria-labelledby="passwordResetEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is optionally sent to the client when an admin resets their password.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Password Reset Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" id="password_reset_subject" class="form-control input" name="password_reset_subject" value="<?php echo esc_attr( get_option('password_reset_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Password Reset Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-400" id="password_reset_content" name="password_reset_content"><?php echo esc_html( get_option('password_reset_content') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="newContactEmail" role="tabpanel" aria-labelledby="newContactEmail-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('New Contact Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="added_contact_subject" name="added_contact_subject" value="<?php echo esc_attr( get_option('added_contact_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('New Contact Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea  class="form-control input pto-textarea pto-h-400" id="added_contact_content" name="added_contact_content"><?php echo esc_html( get_option('added_contact_content') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>
					</div>
					<div id="tabs-3">
						<h3><?php esc_html_e('Templates', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="headerFooter-tab" data-toggle="tab" href="#headerFooter" role="tab" aria-controls="headerFooter" aria-selected="true"><?php esc_html_e('Header & Footer', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="quoteAccept-tab" data-toggle="tab" href="#quoteAccept" role="tab" aria-controls="quoteAccept" aria-selected="false"><?php esc_html_e('Default Quote Acceptance Text', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="quoteEmail-tab" data-toggle="tab" href="#quoteEmail" role="tab" aria-controls="quoteEmail" aria-selected="false"><?php esc_html_e('Quote Email', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="clientMsgEmail-tab" data-toggle="tab" href="#clientMsgEmail" role="tab" aria-controls="clientMsgEmail" aria-selected="false"><?php esc_html_e('Client Message Email', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="companyMsgEmail-tab" data-toggle="tab" href="#companyMsgEmail" role="tab" aria-controls="companyMsgEmail" aria-selected="false"><?php esc_html_e('Company Message Email', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="headerFooter" role="tabpanel" aria-labelledby="headerFooter-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('Default Quote Header Text', 'projectopia-core'); ?></h4>
											<p class="pto-subheading"><?php esc_html_e('The contents of this field will appear at the top of quotes/estimates. It can also be overridden on an individual basis when creating quotes/estimates.', 'projectopia-core'); ?></p>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="quote_header" name="quote_header"><?php echo esc_html( get_option('quote_header') ); ?></textarea>
												</div>
											</div>
											<h4><?php esc_html_e('Default Quote Footer Text', 'projectopia-core'); ?></h4>
											<p class="pto-subheading"><?php esc_html_e('The contents of this field will appear at the bottom of quotes/estimates, just before the quote/estimate acceptance text. It can also be overridden on an individual basis when creating quotes/estimates.', 'projectopia-core'); ?></p>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="quote_footer" name="quote_footer"><?php echo esc_html( get_option('quote_footer') ); ?></textarea>
												</div>
											</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="quoteAccept" role="tabpanel" aria-labelledby="quoteAccept-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('The contents of this field will appear alongside the form that clients will use to accept quotes/estimates. It should include instructions on how to proceed.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Quote Acceptance Text', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="quote_acceptance_text" name="quote_acceptance_text"><?php echo esc_html( get_option('quote_acceptance_text') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="quoteEmail" role="tabpanel" aria-labelledby="quoteEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('When a quote is sent to a client by email, these fields will be used for the subject and content of the email.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Quote Default Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" name="quote_email_subject" id="quote_email_subject" value="<?php echo esc_attr( get_option('quote_email_subject') ); ?>"/>
												</div>
											</div>
											<h4><?php esc_html_e('Quote Default Email Content', 'projectopia-core'); ?></h4>
											<?php $content = get_option( 'quote_default_email' ); ?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="quote_default_email" name="quote_default_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
											<div class="pto-inline-item-wrapper">
												<?php $value = get_option('quote_email_pdf_attach'); ?>
											    <input type="checkbox" name="quote_email_pdf_attach" value="1" <?php checked($value, 1); ?> /> <?php esc_html_e('Attach Quote PDF in email', 'projectopia-core'); ?>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="clientMsgEmail" role="tabpanel" aria-labelledby="clientMsgEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent to your client when you send a new quote message.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Client Message Email Subject', 'projectopia-core'); ?></h4>
											<?php
												$client_quote_message_subject = get_option('client_quote_message_subject');
												if ( empty( $client_quote_message_subject ) ) {
													$client_quote_message_subject = pto_settings_values()['quotes']['client_quote_message_subject'];
												}
											?>
											<div class="form-group">
												<div class="input-group">
													<input type="text" id="client_quote_message_subject" class="form-control input" name="client_quote_message_subject" value="<?php echo esc_attr( $client_quote_message_subject ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Message Email Content', 'projectopia-core'); ?></h4>
											<?php
												$client_quote_message_email = get_option('client_quote_message_email');
												if ( empty( $client_quote_message_email ) ) {
													$client_quote_message_email = pto_settings_values()['quotes']['client_quote_message_email'];
												}
											?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="client_quote_message_email" name="client_quote_message_email"><?php echo esc_html( $client_quote_message_email ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="companyMsgEmail" role="tabpanel" aria-labelledby="companyMsgEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent to you when a client has sent a new quote message.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Company Message Email Subject', 'projectopia-core'); ?></h4>
											<?php
												$company_quote_message_subject = get_option('company_quote_message_subject');
												if ( empty( $company_quote_message_subject ) ) {
													$company_quote_message_subject = pto_settings_values()['quotes']['company_quote_message_subject'];
												}
											?>
											<div class="form-group">
												<div class="input-group">
													<input type="text" id="company_quote_message_subject" class="form-control input" name="company_quote_message_subject" value="<?php echo esc_attr( $company_quote_message_subject ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Company Message Email Content', 'projectopia-core'); ?></h4>
											<?php
												$company_quote_message_email = get_option('company_quote_message_email');
												if ( empty( $company_quote_message_email ) ) {
													$company_quote_message_email = pto_settings_values()['quotes']['company_quote_message_email'];
												}
											?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="company_quote_message_email" name="company_quote_message_email"><?php echo esc_html( $company_quote_message_email ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>
					</div>
					<div id="tabs-4">
						<h3><?php esc_html_e('Project Admin List Order', 'projectopia-core'); ?></h3>	
						<div class="row">
					        <div class="col-6">
						        <div class="form-group">
					                <label for="pto-project-sort"><?php esc_html_e('Sort By', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        		<?php $setting = get_option('pto_default_project_sort'); ?>
										<select id="pto-project-sort" name="pto_default_project_sort" class="form-control input full-width">
											<option value="0"><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
											<option value="1" <?php selected(1, $setting); ?>><?php esc_html_e('Alphabetically', 'projectopia-core'); ?></option>
											<option value="2" <?php selected(2, $setting); ?>><?php esc_html_e('By Modified Date', 'projectopia-core'); ?></option>
										</select>
									</div>
						        </div>
							</div>
							<div class="col-6">
						        <div class="form-group">
					                <label for="pto-project-sort-order"><?php esc_html_e('Sort Order', 'projectopia-core'); ?></label>
					                <div class="input-group">
										<?php $setting = get_option('pto_default_project_order'); ?>
										<select id="pto-project-sort-order" name="pto_default_project_order" class="form-control input full-width">
											<option value="0"><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
											<option value="asc" <?php selected('asc', $setting); ?>><?php esc_html_e('ASC', 'projectopia-core'); ?></option>
											<option value="desc" <?php selected('desc', $setting); ?>><?php esc_html_e('DESC', 'projectopia-core'); ?></option>
										</select>
									</div>
						        </div>
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $value = get_option('pto_default_drop_closed'); ?>
						    <input type="checkbox" name="pto_default_drop_closed" value="1" <?php checked($value, 1); ?> /> <?php esc_html_e('Drop closed projects to the bottom of the list', 'projectopia-core'); ?>
						</div>						
						<h3><?php esc_html_e('Terms & Conditions', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('These Terms & Conditions will appear on the contract that is sent to your clients. You can add templates by clicking Terms Templates in the Projectopia menu.', 'projectopia-core'); ?></p>
						<h4><?php esc_html_e('Default Contract Terms & Conditions', 'projectopia-core'); ?></h4>
						<?php
						$content = get_option( 'default_contract_text' ); 
						$args = array(
							'post_type'      => 'cqpim_terms',
							'posts_per_page' => -1,
							'post_status'    => 'private',
						);
						$terms = get_posts( $args );
						if ( ! empty( $terms ) ) {
							echo '<div class="form-group">';
								echo '<div class="input-group">';
									echo '<select name="default_contract_text" class="form-control input full-width">';
										foreach ( $terms as $term ) {
											echo '<option value="' . esc_attr( $term->ID ) . '" ' . selected( $term->ID, $content, false ) . '>' . esc_html( $term->post_title ) . '</option>';
										}
									echo '</select>';
								echo '</div>';
							echo '</div>';
						} else {
                            echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__( 'Please create a Term Template at first.', 'projectopia-core' ) . '</div>';
						} ?>
						<h3><?php esc_html_e('Contract Acceptance Text', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This will appear on the contract alongside the form that clients will use to e-sign. It should include instructions on how to proceed.', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<?php $content = get_option( 'contract_acceptance_text' ); ?>
								<textarea class="form-control input pto-textarea pto-h-200" id="contract_acceptance_text" name="contract_acceptance_text"><?php echo esc_html( $content ); ?></textarea>
							</div>
						</div>
						<h3><?php esc_html_e('Email Templates', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="projectClientContact-tab" data-toggle="tab" href="#projectClientContact" role="tab" aria-controls="projectClientContact" aria-selected="true"><?php esc_html_e('Client Contract Email', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="projectClientEmail-tab" data-toggle="tab" href="#projectClientEmail" role="tab" aria-controls="projectClientEmail" aria-selected="false"><?php esc_html_e('Client Message Email', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="projectCompanyEmail-tab" data-toggle="tab" href="#projectCompanyEmail" role="tab" aria-controls="projectCompanyEmail" aria-selected="false"><?php esc_html_e('Company Message Email', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="projectClientContact" role="tabpanel" aria-labelledby="projectClientContact-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent out when a project has been created and should contain information on what a client needs to do in order to sign their contract.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Client Contract Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_contract_subject" name="client_contract_subject" value="<?php echo esc_attr( get_option('client_contract_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Contract Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<?php $content = get_option( 'client_contract_email' ); ?>
													<textarea class="form-control input pto-textarea pto-h-300" id="client_contract_email" name="client_contract_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                    <div class="tab-pane fade" id="projectClientEmail" role="tabpanel" aria-labelledby="projectClientEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent to your client when you send a new project message.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Client Message Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_message_subject" name="client_message_subject" value="<?php echo esc_attr( get_option('client_message_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Message Email Content', 'projectopia-core'); ?></h4>
											<?php $content = get_option( 'client_message_email' ); ?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="client_message_email" name="client_message_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="projectCompanyEmail" role="tabpanel" aria-labelledby="projectCompanyEmail-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent to you when a client has sent a new project message.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Company Message Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="company_message_subject" name="company_message_subject" value="<?php echo esc_attr( get_option('company_message_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Company Message Email Content', 'projectopia-core'); ?></h4>
											<?php $content = get_option( 'company_message_email' ); ?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="company_message_email" name="company_message_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php do_action( 'pto/after_projects_tab' ); ?>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>					
					</div>
					<div id="tabs-18">
						<?php if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) { ?>
							<h3><?php esc_html_e('Bug Tracker', 'projectopia-core'); ?></h3>
							<div class="pto-inline-item-wrapper">
							    <?php $value = get_option('cqpim_bugs_auto'); ?>
								<input type="checkbox" name="cqpim_bugs_auto" value="1" <?php checked($value, 1); ?> /> <?php esc_html_e('Automatically activate bug tracker in new projects', 'projectopia-core'); ?>
							</div>
							<div class="tabContentInfo">
                                <div class="nav-tabs-panel">
                                    <div class="tabMenuWrapper">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
							    			<li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="newBugEmail-tab" data-toggle="tab" href="#newBugEmail" role="tab" aria-controls="newBugEmail" aria-selected="true"><?php esc_html_e('New Bug Email', 'projectopia-core'); ?></a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="updateBugEmail-tab" data-toggle="tab" href="#updateBugEmail" role="tab" aria-controls="updateBugEmail" aria-selected="false"><?php esc_html_e('Updated Bug Email', 'projectopia-core'); ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="tabContentWrapper">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade active show" id="newBugEmail" role="tabpanel" aria-labelledby="newBugEmail-tab">
                                            <div class="tab-pane-body">
							    				<p><?php esc_html_e('This email is sent to the PM, assignee and client when a bug is raised against a project.', 'projectopia-core'); ?></p>
							    				<h4><?php esc_html_e('Client Contract Email Subject', 'projectopia-core'); ?></h4>
							    				<div class="form-group">
							    					<div class="input-group">
							    						<input type="text" class="form-control input" id="cqpim_new_bug_subject" name="cqpim_new_bug_subject" value="<?php echo esc_attr( get_option('cqpim_new_bug_subject') ); ?>" />
							    					</div>
							    				</div>
							    				<h4><?php esc_html_e('Client Contract Email Content', 'projectopia-core'); ?></h4>
							    				<div class="form-group">
							    					<div class="input-group">
							    						<?php $content = get_option( 'cqpim_new_bug_content' ); ?>
							    						<textarea class="form-control input pto-textarea pto-h-300" id="cqpim_new_bug_content" name="cqpim_new_bug_content"><?php echo esc_html( $content ); ?></textarea>
							    					</div>
							    				</div>
							    			</div>
                                        </div>
                                        <div class="tab-pane fade" id="updateBugEmail" role="tabpanel" aria-labelledby="updateBugEmail-tab">
                                            <div class="tab-pane-body">
							    				<p><?php esc_html_e('This email is sent to the PM, assignee and client when a bug is updated by a team member or a client.', 'projectopia-core'); ?></p>
							    				<h4><?php esc_html_e('Client Message Email Subject', 'projectopia-core'); ?></h4>
							    				<div class="form-group">
							    					<div class="input-group">
							    						<input type="text" class="form-control input" id="cqpim_update_bug_subject" name="cqpim_update_bug_subject" value="<?php echo esc_attr( get_option('cqpim_update_bug_subject') ); ?>" />
							    					</div>
							    				</div>
							    				<h4><?php esc_html_e('Client Message Email Content', 'projectopia-core'); ?></h4>
							    				<?php $content = get_option( 'cqpim_update_bug_content' ); ?>
							    				<div class="form-group">
							    					<div class="input-group">
							    						<textarea class="form-control input pto-textarea pto-h-300" id="cqpim_update_bug_content" name="cqpim_update_bug_content"><?php echo esc_html( $content ); ?></textarea>
							    					</div>
							    				</div>
							    			</div>
                                        </div>
                                    </div>
                                </div>
                        	</div>
							<p class="submit">
								<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
							</p>	
						<?php } else { ?>
							<h3><?php esc_html_e('Bug Tracker Add-On Not Found', 'projectopia-core'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-bug-tracker-add-on/" target="_blank">https://projectopia.io/projectopia-bug-tracker-add-on/</a>'; ?>
							<p><?php 
							/* translators: %s: Addon Link */
							printf(esc_html__('To use the Bug Tracker part of the plugin, you need to purchase the Bug Tracker Add-On. Please visit %s for more information.', 'projectopia-core'), wp_kses_post( $link ) ); ?></p>
						<?php } ?>						
					</div>
					<div id="tabs-5">
						<h3><?php esc_html_e('Invoice Prefix', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('If you\'d like to add a prefix to your invoice numbering, enter one here. This will appear before the invoice number on all invoices, but can be overriden per client if needed', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
							    <input type="text" class="form-control input" name="cqpim_invoice_prefix" value="<?php echo esc_attr( get_option('cqpim_invoice_prefix') ); ?>" />
							</div>
						</div>
						<h3><?php esc_html_e('Invoice Template', 'projectopia-core'); ?></h3>
						<?php $checked = get_option( 'cqpim_invoice_template' ); 
						if ( empty( $checked ) ) {
							update_option( 'cqpim_invoice_template', 1 );
						} ?>
						<div class="row">
					        <div class="col-6">
						        <div class="form-group">
									<label for="cqpim_invoice_template"><?php esc_html_e('Select Template', 'projectopia-core'); ?></label>
					                <div class="input-group">
										<select id="cqpim_invoice_template" name="cqpim_invoice_template" class="form-control input full-width">
											<option value="1" <?php selected( 1, $checked ); ?>><?php esc_html_e('Default', 'projectopia-core'); ?></option>
											<option value="2" <?php selected( 2, $checked ); ?>><?php esc_html_e('Clean', 'projectopia-core'); ?></option>
											<option value="3" <?php selected( 3, $checked ); ?>><?php esc_html_e('Space', 'projectopia-core'); ?></option>
										</select>
									</div>
						        </div>
							</div>
							<div class="col-6 cqpim-clean-main-colour" style="display: none;">
						        <div class="form-group">
									<label for="cqpim_clean_main_colour"><?php esc_html_e('Primay Colour', 'projectopia-core'); ?></label>
					                <div class="input-group">
										<input type="color" id="cqpim_clean_main_colour" class="form-control input" name="cqpim_clean_main_colour" value="<?php echo esc_attr( get_option('cqpim_clean_main_colour') ); ?>" />
									</div>
						        </div>
							</div>
							<div class="col-6 cqpim-cool-main-colour" style="display: none;">
						        <div class="form-group">
									<label for="cqpim_cool_main_colour"><?php esc_html_e('Primary Colour', 'projectopia-core'); ?></label>
					                <div class="input-group">
										<input type="color" id="cqpim_cool_main_colour" class="form-control input" name="cqpim_cool_main_colour" value="<?php echo esc_attr( get_option('cqpim_cool_main_colour') ); ?>" />
									</div>
						        </div>
							</div>
						</div>
						<h3><?php esc_html_e('Invoice Logo', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('By default, the invoice will use the global company logo. You can override the invoice logo here if you wish.', 'projectopia-core'); ?></p>
						<?php 
						$logo = get_option('cqpim_invoice_logo'); 
						if ( $logo ) { ?>
							<img style="max-width: 300px;background: #ececec;" src="<?php echo esc_url( $logo['cqpim_invoice_logo'] ); ?>" />
							<br />
							<button class="remove_logo cancel-colorbox cancel-creation piaBtn redColor mt-3" data-type="cqpim_invoice_logo"><?php esc_html_e('Remove', 'projectopia-core'); ?></button>
							<br /><br />
						<?php } ?>	
						<input type="file" name="cqpim_invoice_logo" />
						<div class="clear"></div>
						<h3><?php esc_html_e('PDF Invoice Email Attachments', 'projectopia-core'); ?></h3>
						<p><strong><?php esc_html_e('IMPORTANT:', 'projectopia-core'); ?></strong> <?php esc_html_e('PDF Invoice attachments require the PHP cURL Extension. If you experience blank PDFs then check that you have this installed and your host is not blocking the requests.', 'projectopia-core'); ?></p>
						<div class="pto-inline-item-wrapper">
							<?php $client_invoice_email_attach = get_option('client_invoice_email_attach'); ?>
							<input type="checkbox" name="client_invoice_email_attach" id="client_invoice_email_attach" value="1" <?php checked($client_invoice_email_attach, 1, true); ?>/> <?php $text = esc_html__('Attach a PDF Invoice to Client Emails	', 'projectopia-core'); esc_html_e('Attach a PDF Invoice to Client Emails	', 'projectopia-core'); ?>					
						</div>
						<h3><?php esc_html_e('Partial Invoice Payments', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
							<?php $client_invoice_allow_partial = get_option('client_invoice_allow_partial'); ?>
							<input type="checkbox" name="client_invoice_allow_partial" id="client_invoice_allow_partial" value="1" <?php checked($client_invoice_allow_partial, 1, true); ?>/> <?php esc_html_e('Allow partial invoice payments (This is a global setting and can be overridden on a per invoice basis. Deposit invoices do not allow partial payments.)', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('Invoice Reminder Settings', 'projectopia-core'); ?></h3>
						<p><strong><?php esc_html_e('IMPORTANT:', 'projectopia-core'); ?> </strong> <?php esc_html_e('The following settings require the use of WP Cron, which some hosts block access to. Please check that you have access to wp cron, otherwise these settings may not work properly.', 'projectopia-core'); ?></p>
						<div class="row">
					        <div class="col-4">
						        <div class="form-group">
									<label for="client_invoice_after_send_remind_days"><?php esc_html_e('Reminder email after invoice sent', 'projectopia-core'); ?></label>
									<div class="input-group">
										<?php $terms = get_option( 'client_invoice_after_send_remind_days' ); ?>
										<select id="client_invoice_after_send_remind_days" name="client_invoice_after_send_remind_days" class="form-control input">
											<option value="" <?php if ( ! $terms ) { echo 'selected'; } ?>><?php esc_html_e( 'Choose...', 'projectopia-core' ) ?></option>
											<?php for ( $i = 1; $i <= 20; $i++ ) { ?>
												<option value="<?php echo esc_attr( $i ); ?>" <?php if ( $terms == $i ) { echo 'selected'; } ?>><?php echo esc_html( $i ); ?> <?php ( $i != 1 ) ? esc_html_e('days', 'projectopia-core') : esc_html_e('day', 'projectopia-core'); ?></option>
											<?php } ?>
										</select>
									</div>
						        </div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="client_invoice_before_terms_remind_days"><?php esc_html_e('Reminder email before due date', 'projectopia-core'); ?></label>
									<div class="input-group">
										<?php $terms = get_option( 'client_invoice_before_terms_remind_days' ); ?>
										<select id="client_invoice_before_terms_remind_days" name="client_invoice_before_terms_remind_days" class="form-control input">
											<option value="" <?php if ( ! $terms ) { echo 'selected'; } ?>><?php esc_html_e( 'Choose...', 'projectopia-core' ) ?></option>
											<?php for ( $i = 1; $i <= 20; $i++ ) { ?>
												<option value="<?php echo esc_attr( $i ); ?>" <?php if ( $terms == $i ) { echo 'selected'; } ?>><?php echo esc_html( $i ); ?> <?php ( $i != 1 ) ? esc_html_e('days', 'projectopia-core') : esc_html_e('day', 'projectopia-core'); ?></option>
											<?php } ?>
										</select>		
						            </div>
								</div>
							</div>
							<div class="col-4">
							    <div class="form-group">
							    	<label for="client_invoice_after_terms_remind_days"><?php esc_html_e('Overdue email after due date', 'projectopia-core'); ?></label>
							    	<div class="input-group">
									    <?php $terms = get_option( 'client_invoice_after_terms_remind_days' ); ?>
										<select id="client_invoice_after_terms_remind_days" name="client_invoice_after_terms_remind_days" class="form-control input">
											<option value="" <?php if ( ! $terms ) { echo 'selected'; } ?>><?php esc_html_e( 'Choose...', 'projectopia-core' ) ?></option>
											<?php for ( $i = 1; $i <= 20; $i++ ) { ?>
												<option value="<?php echo esc_attr( $i ); ?>" <?php if ( $terms == $i ) { echo 'selected'; } ?>><?php echo esc_html( $i ); ?> <?php ( $i != 1 ) ? esc_html_e('days', 'projectopia-core') : esc_html_e('day', 'projectopia-core'); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $client_invoice_high_priority = get_option('client_invoice_high_priority'); ?>
							<input type="checkbox" name="client_invoice_high_priority" id="client_invoice_high_priority" value="1" <?php checked($client_invoice_high_priority, 1, true); ?>/> <?php esc_html_e('Mark invoice reminder/overdue emails as high priority?', 'projectopia-core'); ?>
						</div>
							<h3><?php esc_html_e('Invoice Footer', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This text will appear at the bottom of invoices, so should contain instructions for payment etc.', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<?php $content = get_option( 'client_invoice_footer' ); ?>
							    <textarea class="form-control input pto-textarea pto-h-300" id="client_invoice_footer" name="client_invoice_footer"><?php echo esc_html( $content ); ?></textarea>
							</div>
						</div>
						<h3><?php esc_html_e('Client Email Templates', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="clientInvoiceEmail-tab" data-toggle="tab" href="#clientInvoiceEmail" role="tab" aria-controls="clientInvoiceEmail" aria-selected="true"><?php esc_html_e('Invoice Email', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="clientDepositInvoiceEmail-tab" data-toggle="tab" href="#clientDepositInvoiceEmail" role="tab" aria-controls="clientDepositInvoiceEmail" aria-selected="false"><?php esc_html_e('Deposit Invoice Email', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="clientInvoiceReminderEmail-tab" data-toggle="tab" href="#clientInvoiceReminderEmail" role="tab" aria-controls="clientInvoiceReminderEmail" aria-selected="false"><?php esc_html_e('Invoice Reminder Email', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="clientInvoiceOverdueEmail-tab" data-toggle="tab" href="#clientInvoiceOverdueEmail" role="tab" aria-controls="clientInvoiceOverdueEmail" aria-selected="false"><?php esc_html_e('Invoice Overdue Email', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="clientPaymentNotificationEmail-tab" data-toggle="tab" href="#clientPaymentNotificationEmail" role="tab" aria-controls="clientPaymentNotificationEmail" aria-selected="false"><?php esc_html_e('Payment Notication Email', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="clientInvoiceEmail" role="tabpanel" aria-labelledby="clientInvoiceEmail-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('Client Invoice Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_invoice_subject" name="client_invoice_subject" value="<?php echo esc_attr( get_option('client_invoice_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Invoice Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea  class="form-control input pto-textarea pto-h-400" id="client_invoice_email" name="client_invoice_email"><?php echo esc_html( get_option('client_invoice_email') ); ?></textarea>
												</div>
											</div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="clientDepositInvoiceEmail" role="tabpanel" aria-labelledby="clientDepositInvoiceEmail-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('Client Deposit Invoice Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_deposit_invoice_subject" name="client_deposit_invoice_subject" value="<?php echo esc_attr( get_option('client_deposit_invoice_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Deposit Invoice Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea  class="form-control input pto-textarea pto-h-400" id="client_deposit_invoice_email" name="client_deposit_invoice_email"><?php echo esc_html( get_option('client_deposit_invoice_email') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="clientInvoiceReminderEmail" role="tabpanel" aria-labelledby="clientInvoiceReminderEmail-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('Client Invoice Reminder Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_invoice_reminder_subject" name="client_invoice_reminder_subject" value="<?php echo esc_attr( get_option('client_invoice_reminder_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Invoice Reminder Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea  class="form-control input pto-textarea pto-h-400" id="client_invoice_reminder_email" name="client_invoice_reminder_email"><?php echo esc_html( get_option('client_invoice_reminder_email') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="clientInvoiceOverdueEmail" role="tabpanel" aria-labelledby="clientInvoiceOverdueEmail-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('Client Invoice Reminder Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_invoice_overdue_subject" name="client_invoice_overdue_subject" value="<?php echo esc_attr( get_option('client_invoice_overdue_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Invoice Reminder Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea  class="form-control input pto-textarea pto-h-400" id="client_invoice_overdue_email" name="client_invoice_overdue_email"><?php echo esc_html( get_option('client_invoice_overdue_email') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="clientPaymentNotificationEmail" role="tabpanel" aria-labelledby="clientPaymentNotificationEmail-tab">
                                        <div class="tab-pane-body">
											<h4><?php esc_html_e('Client Invoice Reminder Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="client_invoice_receipt_subject" name="client_invoice_receipt_subject" value="<?php echo esc_attr( get_option('client_invoice_receipt_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Client Invoice Reminder Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<textarea  class="form-control input pto-textarea pto-h-400" id="client_invoice_receipt_email" name="client_invoice_receipt_email"><?php echo esc_html( get_option('client_invoice_receipt_email') ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>
					</div>
					<div id="tabs-20">
						<?php if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) ) { ?>
							<h3><?php esc_html_e('Subscription Emalis', 'projectopia-core'); ?></h3>
							<div class="tabContentInfo">
                            	<div class="nav-tabs-panel">
                            	    <div class="tabMenuWrapper">
                            	        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            	            <li class="nav-item" role="presentation">
                            	                <a class="nav-link active" id="newSubscriptionEmail-tab" data-toggle="tab" href="#newSubscriptionEmail" role="tab" aria-controls="newSubscriptionEmail" aria-selected="true"><?php esc_html_e('New Subscription', 'projectopia-core'); ?></a>
                            	            </li>
                            	            <li class="nav-item" role="presentation">
                            	                <a class="nav-link" id="newSubscriptionAcceptedEmail-tab" data-toggle="tab" href="#newSubscriptionAcceptedEmail" role="tab" aria-controls="newSubscriptionAcceptedEmail" aria-selected="false"><?php esc_html_e('New Subscription Accepted', 'projectopia-core'); ?></a>
                            	            </li>
											<li class="nav-item" role="presentation">
                            	                <a class="nav-link" id="newSubscriptionCancelledEmail-tab" data-toggle="tab" href="#newSubscriptionCancelledEmail" role="tab" aria-controls="newSubscriptionCancelledEmail" aria-selected="false"><?php esc_html_e('Subscription Cancelled', 'projectopia-core'); ?></a>
                            	            </li>
											<li class="nav-item" role="presentation">
                            	                <a class="nav-link" id="newSubscriptionFailedEmail-tab" data-toggle="tab" href="#newSubscriptionFailedEmail" role="tab" aria-controls="newSubscriptionFailedEmail" aria-selected="false"><?php esc_html_e('Subscription Failed', 'projectopia-core'); ?></a>
                            	            </li>
											<li class="nav-item" role="presentation">
                            	                <a class="nav-link" id="newSubscriptionReminderEmail-tab" data-toggle="tab" href="#newSubscriptionReminderEmail" role="tab" aria-controls="newSubscriptionReminderEmail" aria-selected="false"><?php esc_html_e('Subscription Reminder', 'projectopia-core'); ?></a>
                            	            </li>
                            	        </ul>
                            	    </div>
                            	</div>
                            	<div class="tabContentWrapper">
                            	    <div class="tab-content" id="myTabContent">
                            	        <div class="tab-pane fade active show" id="newSubscriptionEmail" role="tabpanel" aria-labelledby="newSubscriptionEmail-tab">
                            	            <div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to the client when a new subscription has been created for them to accept.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_new_subscription_subject" name="cqpim_new_subscription_subject" value="<?php echo esc_attr( get_option('cqpim_new_subscription_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<textarea class="form-control input pto-textarea pto-h-400" id="cqpim_new_subscription_content" name="cqpim_new_subscription_content"><?php echo esc_html( get_option('cqpim_new_subscription_content') ); ?></textarea>
													</div>
												</div>
                            	            </div>
                            	        </div>
                            	        <div class="tab-pane fade" id="newSubscriptionAcceptedEmail" role="tabpanel" aria-labelledby="newSubscriptionAcceptedEmail-tab">
                            	            <div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to the selected Team Members when a new subscription has been accepted and activated by the client.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_new_subscription_accept_subject" name="cqpim_new_subscription_accept_subject" value="<?php echo esc_attr( get_option('cqpim_new_subscription_accept_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<textarea  class="form-control input pto-textarea pto-h-400" id="cqpim_new_subscription_accept_content" name="cqpim_new_subscription_accept_content"><?php echo esc_html( get_option('cqpim_new_subscription_accept_content') ); ?></textarea>
													</div>
												</div>
											</div>
                            	        </div>
										<div class="tab-pane fade" id="newSubscriptionCancelledEmail" role="tabpanel" aria-labelledby="newSubscriptionCancelledEmail-tab">
                            	            <div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to the selected Team Members and the Client when a subscription has been cancelled.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_subscription_cancelled_subject" name="cqpim_subscription_cancelled_subjectt" value="<?php echo esc_attr( get_option('cqpim_subscription_cancelled_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<textarea  class="form-control input pto-textarea pto-h-400" id="cqpim_subscription_cancelled_content" name="cqpim_subscription_cancelled_content"><?php echo esc_html( get_option('cqpim_subscription_cancelled_content') ); ?></textarea>
													</div>
												</div>
											</div>
                            	        </div>
										<div class="tab-pane fade" id="newSubscriptionFailedEmail" role="tabpanel" aria-labelledby="newSubscriptionFailedEmail-tab">
                            	            <div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to the selected Team Members and the Client when a subscription payment has failed.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_subscription_failed_subject" name="cqpim_subscription_failed_subject" value="<?php echo esc_attr( get_option('cqpim_subscription_failed_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<textarea  class="form-control input pto-textarea pto-h-400" id="cqpim_subscription_failed_content" name="cqpim_subscription_failed_content"><?php echo esc_html( get_option('cqpim_subscription_failed_content') ); ?></textarea>
													</div>
												</div>
											</div>
                            	        </div>
										<div class="tab-pane fade" id="newSubscriptionReminderEmail" role="tabpanel" aria-labelledby="newSubscriptionReminderEmail-tab">
                            	            <div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to the client in advance of a payment being taken, if this has been configured in the subscription.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_subscription_reminder_subject" name="cqpim_subscription_reminder_subject" value="<?php echo esc_attr( get_option('cqpim_subscription_reminder_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<textarea  class="form-control input pto-textarea pto-h-400" id="cqpim_subscription_reminder_content" name="cqpim_subscription_reminder_content"><?php echo esc_html( get_option('cqpim_subscription_reminder_content') ); ?></textarea>
													</div>
												</div>
											</div>
                            	        </div>
                            	    </div>
                            	</div>
                        	</div>
							<p class="submit">
								<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
							</p>
						<?php } else { ?>
							<h3><?php esc_html_e('Subscriptions Add-On Not Found', 'projectopia-core'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-subscriptions-add-on/" target="_blank">https://projectopia.io/projectopia-subscriptions-add-on/</a>'; ?>
							<p><?php 
							/* translators: %s: Addon Link */
							printf(esc_html__('To use the Subscriptions part of the plugin, you need to purchase the Projectopia Subscriptions Add-On. Please visit %s for more information.', 'projectopia-core'), wp_kses_post( $link ) ); ?></p>
						<?php } ?>						
					</div>
					<?php if ( 0 ) { ?>
					<div id="tabs-22">
						<?php if ( pto_has_addon_active_license( 'pto_woo', 'woocommerce' ) ) { ?>
							<h3><?php esc_html_e('WooCommerce', 'projectopia-core'); ?></h3>
							<p><strong><?php esc_html_e('New Project Notification Email', 'projectopia-core'); ?></strong></p>
							<p><?php esc_html_e('This email is sent to the client when a new order has been processed and a project created for them.', 'projectopia-core'); ?></p>
							<p><strong><?php esc_html_e('Subject', 'projectopia-core'); ?></strong></p>
							<input type="text" id="cqpim_wc_new_project_subject" name="cqpim_wc_new_project_subject" value="<?php echo esc_attr( get_option('cqpim_wc_new_project_subject') ); ?>" />
							<p><strong><?php esc_html_e('Content', 'projectopia-core'); ?></strong></p>
							<?php $content   = get_option( 'cqpim_wc_new_project_content' ); ?>
							<textarea style="width:100%; height:200px" id="cqpim_wc_new_project_content" name="cqpim_wc_new_project_content"><?php echo esc_html( $content ); ?></textarea>
							<p class="submit">
								<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
							</p>
						<?php } else { ?>
							<h3><?php esc_html_e('WooCommerce Add-On Not Found', 'projectopia-core'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-woocommerce-add-on/" target="_blank">https://projectopia.io/projectopia-woocommerce-add-on/</a>'; ?>
							<p><?php 
							/* translators: %s: Addon Link */
							printf(esc_html__('To use the WooCommerce part of the plugin, you need to purchase the Projectopia WooCommerce Add-On. Please visit %s for more information.', 'projectopia-core'), wp_kses_post( $link ) ); ?></p>
						<?php } ?>						
					</div>
					<?php } ?>
					<div id="tabs-21">
						<h3><?php esc_html_e('Payment Gateways', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="paypalPG-tab" data-toggle="tab" href="#paypalPG" role="tab" aria-controls="paypalPG" aria-selected="true"><?php esc_html_e('Paypal', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="stripePG-tab" data-toggle="tab" href="#stripePG" role="tab" aria-controls="stripePG" aria-selected="false"><?php esc_html_e('Stripe', 'projectopia-core'); ?></a>
                                        </li>
										<?php if ( pto_has_addon_active_license( 'pto_2co', '2checkout' ) ) {
											do_action( 'pto/settings_tab' );
										} ?>
									</ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="paypalPG" role="tabpanel" aria-labelledby="paypalPG-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('To allow clients to pay invoices via Paypal, enter your Paypal details below.', 'projectopia-core'); ?></p>
											<div class="form-group">
												<label for="client_invoice_paypal_address"><?php esc_html_e('Paypal Email Address', 'projectopia-core'); ?></label>
												<div class="input-group">
													<input type="text" name="client_invoice_paypal_address" id="client_invoice_paypal_address" class="form-control input" value="<?php echo esc_attr( get_option('client_invoice_paypal_address') ); ?>" />	
												</div>
											</div>
											<?php if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) ) { ?>
												<div class="row">
					    						    <div class="col-6">
												        <div class="form-group">
															<label for="cqpim_paypal_api_username"><?php esc_html_e('Paypal API Username', 'projectopia-core'); ?></label>
															<div class="input-group">
																<input type="text" name="cqpim_paypal_api_username" id="cqpim_paypal_api_username" class="form-control input" value="<?php echo esc_attr( get_option('cqpim_paypal_api_username') ); ?>" />
															</div>
												        </div>
													</div>
													<div class="col-6">
													    <div class="form-group">
													    	<label for="cqpim_paypal_api_password"><?php esc_html_e('Paypal API Password', 'projectopia-core'); ?></label>
													    	<div class="input-group">
																<input type="text" name="cqpim_paypal_api_password" id="cqpim_paypal_api_password" class="form-control input" value="<?php echo esc_attr( get_option('cqpim_paypal_api_password') ); ?>" />						
															</div>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="cqpim_paypal_api_signature"><?php esc_html_e('Paypal API Signature', 'projectopia-core'); ?></label>
													<div class="input-group">
														<input type="text" name="cqpim_paypal_api_signature" id="cqpim_paypal_api_signature" class="form-control input" value="<?php echo esc_attr( get_option('cqpim_paypal_api_signature') ); ?>" />						
													</div>
												</div>
											<?php } ?>
											<div class="pto-inline-item-wrapper">
							       				<input type="checkbox" name="cqpim_paypal_enable_sandbox" id="cqpim_paypal_enable_sandbox" value="1" <?php checked(1, get_option('cqpim_paypal_enable_sandbox')); ?> /> <?php esc_html_e('Check this option if you want to enable PayPal sandbox for testing', 'projectopia-core'); ?>
											</div>
										</div>
                                    </div>
                                    <div class="tab-pane fade" id="stripePG" role="tabpanel" aria-labelledby="stripePG-tab">
                                        <div class="tab-pane-body">
											<?php if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) ) { ?>
												<p><?php esc_html_e('To allow clients to pay invoices and set up subscriptions via Stripe, enter your Stripe Publishable Key below.', 'projectopia-core'); ?></p>
											<?php } else { ?>
												<p><?php esc_html_e('To allow clients to pay invoices via Stripe, enter your Stripe Publishable Key below.', 'projectopia-core'); ?></p>
											<?php } ?>
											<div class="row">
					    					    <div class="col-6">
											        <div class="form-group">
														<label for="client_invoice_stripe_key"><?php esc_html_e('Stripe Public Key', 'projectopia-core'); ?></label>
														<div class="input-group">
															<input type="text" name="client_invoice_stripe_key" id="client_invoice_stripe_key" class="form-control input" value="<?php echo esc_attr( get_option('client_invoice_stripe_key') ); ?>" />
														</div>
											        </div>
												</div>
												<div class="col-6">
												    <div class="form-group">
												    	<label for="client_invoice_stripe_secret"><?php esc_html_e('Stripe Secret Key', 'projectopia-core'); ?></label>
												    	<div class="input-group">
															<input type="text" name="client_invoice_stripe_secret" id="client_invoice_stripe_secret" class="form-control input" value="<?php echo esc_attr( get_option('client_invoice_stripe_secret') ); ?>" />						
														</div>
													</div>
												</div>
											</div>
											<div class="pto-inline-item-wrapper">
												<input type="checkbox" name="client_invoice_stripe_ideal" id="client_invoice_stripe_ideal" value="1" <?php checked(1, get_option('client_invoice_stripe_ideal')); ?> /> <?php esc_html_e('If you have iDEAL activated on your Stripe account, check this box to enable it as a payment gateway', 'projectopia-core'); ?>
											</div>
										</div>
                                    </div>      
									<?php if ( pto_has_addon_active_license( 'pto_2co', '2checkout' ) ) {
										do_action( 'pto/settings_tab_content' );
									} ?>
                                </div>
                            </div>
                        </div>
						<?php /*if(get_option('pto_escrow') == 1) { ?>
							<p><strong><?php esc_html_e('Escrow', 'projectopia-core'); ?></strong></p>
							<p><?php esc_html_e('Escrow Email Address', 'projectopia-core'); ?></p>
							<input type="text" name="pto_escrow_email_address" id="pto_escrow_email_address" value="<?php echo esc_attr( get_option('pto_escrow_email_address'); ?>" />
							<p><?php esc_html_e('Escrow API Key', 'projectopia-core'); ?></p>
							<input type="text" name="pto_escrow_api_key" id="pto_escrow_api_key" value="<?php echo esc_attr( get_option('pto_escrow_api_key'); ?>" />
							<p><?php esc_html_e('Escrow Inspection Period', 'projectopia-core'); ?></p>
							<?php 
							$range = range(1,30); 
							$inspd = get_option('pto_escrow_inspection');
							?>
							<select name="pto_escrow_inspection">
								<option value="0"><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
								<?php foreach($range as $insp) { ?>
									<option value="<?php echo $insp * 86400; ?>" <?php selected($insp * 86400, $inspd); ?>><?php echo $insp; ?></option>
								<?php } ?>
							</select>
							<br /><br />
						<?php }*/ ?>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>					
					</div>
					<div id="tabs-6">
						<h3><?php esc_html_e('Email Templates', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="teamNewAccount-tab" data-toggle="tab" href="#teamNewAccount" role="tab" aria-controls="teamNewAccount" aria-selected="true"><?php esc_html_e('New Account', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="teamPasswordReset-tab" data-toggle="tab" href="#teamPasswordReset" role="tab" aria-controls="teamPasswordReset" aria-selected="false"><?php esc_html_e('Password Reset', 'projectopia-core'); ?></a>
                                        </li>
										<li class="nav-item" role="presentation">
                                            <a class="nav-link" id="teamNewProject-tab" data-toggle="tab" href="#teamNewProject" role="tab" aria-controls="teamNewProject" aria-selected="false"><?php esc_html_e('New Project', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="teamNewAccount" role="tabpanel" aria-labelledby="teamNewAccount-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent out when an admin creates a new team member.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('New Account Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="team_account_subject" name="team_account_subject" value="<?php echo esc_attr( get_option('team_account_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('New Account Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<?php $content = get_option( 'team_account_email' ); ?>
													<textarea class="form-control input pto-textarea pto-h-300" id="team_account_email" name="team_account_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                    <div class="tab-pane fade" id="teamPasswordReset" role="tabpanel" aria-labelledby="teamPasswordReset-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is optionally sent to the team member when an admin resets their password.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Password Reset Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="team_reset_subject" name="team_reset_subject" value="<?php echo esc_attr( get_option('team_reset_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Password Reset Email Content', 'projectopia-core'); ?></h4>
											<?php $content = get_option( 'team_reset_email' ); ?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="team_reset_email" name="team_reset_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
									<div class="tab-pane fade" id="teamNewProject" role="tabpanel" aria-labelledby="teamNewProject-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent to a team member when an admin adds them to a project.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('New Project Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="team_project_subject" name="team_project_subject" value="<?php echo esc_attr( get_option('team_project_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('New Project Email Content', 'projectopia-core'); ?></h4>
											<?php $content = get_option( 'team_project_email' ); ?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="team_project_email" name="team_project_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>
					</div>
					<div id="tabs-10">
						<h3><?php esc_html_e('Task Assignee Acceptance', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('Activating this will require team members to accept or reject tasks that are assigned to them. They will be sent an approval link via email and via the in app notifications system. If they accept the task then the task will be marked as In Progress, but if they reject the task it will be reassigned to the previous assignee and a reason must be provided. To use this feature, you should place the %%ACCEPT_TASK_LINK%% tag in the "Task Update Email Content" field below.', 'projectopia-core'); ?></p>
						<div class="pto-inline-item-wrapper">
							<?php $value = get_option('pto_task_acceptance'); ?>
							<input type="checkbox" name="pto_task_acceptance" value="1" <?php checked(1, $value); ?> /> <?php esc_html_e('Activate Task Assignee Acceptance', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('Task Emails', 'projectopia-core'); ?></h3>
						<div class="tabContentInfo">
                            <div class="nav-tabs-panel">
                                <div class="tabMenuWrapper">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="taskAssignment-tab" data-toggle="tab" href="#taskAssignment" role="tab" aria-controls="taskAssignment" aria-selected="true"><?php esc_html_e('Task Assignment Email', 'projectopia-core'); ?></a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="taskUpdate-tab" data-toggle="tab" href="#taskUpdate" role="tab" aria-controls="taskUpdate" aria-selected="false"><?php esc_html_e('Task Update Email', 'projectopia-core'); ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabContentWrapper">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="taskAssignment" role="tabpanel" aria-labelledby="taskAssignment-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent out when an admin creates a new team member.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Task Assignment Acceptance Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="assignment_response_subject" name="assignment_response_subject" value="<?php echo esc_attr( get_option('assignment_response_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Task Assignment Acceptance Email Content', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<?php $content = get_option( 'assignment_response_email' ); ?>
													<textarea class="form-control input pto-textarea pto-h-300" id="assignment_response_email" name="assignment_response_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                    <div class="tab-pane fade" id="taskUpdate" role="tabpanel" aria-labelledby="taskUpdate-tab">
                                        <div class="tab-pane-body">
											<p><?php esc_html_e('This email is sent to the client and all watchers when a task is updated.', 'projectopia-core'); ?></p>
											<h4><?php esc_html_e('Task Update Email Subject', 'projectopia-core'); ?></h4>
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control input" id="team_assignment_subject" name="team_assignment_subject" value="<?php echo esc_attr( get_option('team_assignment_subject') ); ?>" />
												</div>
											</div>
											<h4><?php esc_html_e('Task Update Email Content', 'projectopia-core'); ?></h4>
											<?php $content = get_option( 'team_assignment_email' ); ?>
											<div class="form-group">
												<div class="input-group">
													<textarea class="form-control input pto-textarea pto-h-300" id="team_assignment_email" name="team_assignment_email"><?php echo esc_html( $content ); ?></textarea>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<h3><?php esc_html_e('Task Status', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('You can edit default task statuses or add new task status. You will not be able to make changes to the system statuses as we need it for the system to handle previous tasks.', 'projectopia-core'); ?></p>
						<div class="task-status">
							<div class="row">
					    	    <div class="col-4">
							        <span class="task-status-header"><?php esc_html_e( 'Value', 'projectopia-core' ); ?></span>
								</div>
								<div class="col-2">
									<span class="task-status-header"><?php esc_html_e( 'Color', 'projectopia-core' ); ?></span>
								</div>
								<div class="col-6">
									<span class="task-status-header"><?php esc_html_e( 'Description', 'projectopia-core' ); ?></span>
								</div>
							</div>
							<?php	  
							$pto_task_status = get_option( 'pto_task_status' );
							if ( empty( $pto_task_status ) ) {
								$pto_task_status = pto_get_default_task_statuses();
							}

							for ( $i = 0; $i < count( $pto_task_status['key'] ); $i++ ) { 
								if ( empty( $pto_task_status['key'][ $i ] ) ) {
									continue;
								} 
								
								$disabled = '';
								$class = 'ind-status';
								$btn = true;
								if ( in_array( $pto_task_status['key'][ $i ], array( 'pending', 'progress', 'on_hold', 'complete' ) ) ) {
									$disabled = 'readonly';
									$btn = false;
									$class = '';
								}
								?>

								<div class="row <?php echo esc_attr( $class ); ?>">
					    		    <div class="col-4">
								        <div class="form-group">
					    		            <div class="input-group">
								        	    <input class="form-control input" type="text" name="pto_task_status[key][]" value="<?php echo esc_attr( $pto_task_status['key'][ $i ] ); ?>" <?php echo esc_attr( $disabled ); ?>/>
								        	</div>
								        </div>
									</div>
									<div class="col-2">
								        <div class="form-group">
					    		            <div class="input-group">
												<input class="form-control input" type="color" name="pto_task_status[color][]" value="<?php echo esc_attr( $pto_task_status['color'][ $i ] ); ?>" />
								        	</div>
								        </div>
									</div>
									<div class="col-6">
								        <div class="form-group">
					    		            <div class="input-group">
								        	    <input class="form-control input" type="text" name="pto_task_status[value][]" value="<?php echo esc_attr( $pto_task_status['value'][ $i ] ); ?>" style="border-radius: .375rem;" />
												<?php if ( $i == 0 ) {
													echo '<input type="button" class="btn-plus btn-plus-task" title="' . esc_html__( 'Add New Status', 'projectopia-core' ) . '" data-remove-title="' . esc_html__( 'Remove Status', 'projectopia-core' ) . '">';
												} else {
													if ( $btn ) {
														echo '<input type="button" class="btn-minus" title="' . esc_html__( 'Remove Status', 'projectopia-core' ) . '">';
													}
												} ?>
								        	</div>
								        </div>
									</div>
								</div>
  							<?php } ?>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>
					</div>
					<div id="tabs-7">
						<?php if ( pto_has_addon_active_license( 'pto_st', 'tickets' ) ) { ?>
							<h3><?php esc_html_e('Disable Support Tickets', 'projectopia-core'); ?></h3>
							<div class="pto-inline-item-wrapper">
								<?php $checked = get_option('disable_tickets'); ?>
								<input type="checkbox" name="disable_tickets" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Disable the support ticket system.', 'projectopia-core'); ?>
							</div>
							<h3><?php esc_html_e('Email Templates', 'projectopia-core'); ?></h3>
							<div class="tabContentInfo">
								<div class="nav-tabs-panel">
									<div class="tabMenuWrapper">
										<ul class="nav nav-tabs" id="myTab" role="tablist">
											<li class="nav-item" role="presentation">
												<a class="nav-link active" id="supportNewTicket-tab" data-toggle="tab" href="#supportNewTicket" role="tab" aria-controls="supportNewTicket" aria-selected="true"><?php esc_html_e('New Ticket Email', 'projectopia-core'); ?></a>
											</li>
											<li class="nav-item" role="presentation">
												<a class="nav-link" id="supportUpdateTicket-tab" data-toggle="tab" href="#supportUpdateTicket" role="tab" aria-controls="supportUpdateTicket" aria-selected="false"><?php esc_html_e('Updated Ticket Email Email', 'projectopia-core'); ?></a>
											</li>
										</ul>
									</div>
								</div>
								<div class="tabContentWrapper">
									<div class="tab-content" id="myTabContent">
										<div class="tab-pane fade active show" id="supportNewTicket" role="tabpanel" aria-labelledby="supportNewTicket-tab">
											<div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to your Support email address when a client raises a new ticket.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('New Ticket Email Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="client_create_ticket_subject" name="client_create_ticket_subject" value="<?php echo esc_attr( get_option('client_create_ticket_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('New Ticket Email Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<?php $content = get_option( 'client_create_ticket_email' ); ?>
														<textarea class="form-control input pto-textarea pto-h-300" id="client_create_ticket_email" name="client_create_ticket_email"><?php echo esc_html( $content ); ?></textarea>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="supportUpdateTicket" role="tabpanel" aria-labelledby="supportUpdateTicket-tab">
											<div class="tab-pane-body">
												<p><?php esc_html_e('This email is sent to the owner, client and watchers when a ticket is updated.', 'projectopia-core'); ?></p>
												<h4><?php esc_html_e('Task Update Email Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="client_update_ticket_subject" name="client_update_ticket_subject" value="<?php echo esc_attr( get_option('client_update_ticket_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Task Update Email Content', 'projectopia-core'); ?></h4>
												<?php $content = get_option( 'client_update_ticket_email' ); ?>
												<div class="form-group">
													<div class="input-group">
														<textarea class="form-control input pto-textarea pto-h-300" id="client_update_ticket_email" name="client_update_ticket_email"><?php echo esc_html( $content ); ?></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3><?php esc_html_e('Support Ticket Status', 'projectopia-core'); ?></h3>
							<p><?php esc_html_e('You can edit default support statuses or add new support statuses. You will not be able to make changes to resolved status as we need it for the system to close the ticket.', 'projectopia-core'); ?></p>
							<div class="support-ticket-status">
								<div class="row">
									<div class="col-4">
										<span class="support-ticket-header"><?php esc_html_e( 'Value', 'projectopia-core' ); ?></span>
									</div>
									<div class="col-2">
										<span class="support-ticket-header"><?php esc_html_e( 'Color', 'projectopia-core' ); ?></span>
									</div>
									<div class="col-6">
										<span class="support-ticket-header"><?php esc_html_e( 'Description', 'projectopia-core' ); ?></span>
									</div>
								</div>
								<?php	  
								$support_ticket_status = get_option( 'support_status' );
								/*$status_keys = $support_ticket_status['key'];
								$resolved_index = -1;
								$resolved_index = array_search( 'resolved', $status_keys );
								$resolved_status_clr = ( $resolved_index != -1 ) ? $support_ticket_status['color'][ $resolved_index ] : '#8ec165';*/

								for ( $i = 0; $i < count( $support_ticket_status['key'] ); $i++ ) { 
									if ( empty( $support_ticket_status['key'][ $i ] ) ) {
										continue;
									} 
									
									$disabled = '';
									$class = 'ind-status';
									$btn = true;
									if ( in_array( $support_ticket_status['key'][ $i ], array( 'open', 'resolved', 'hold', 'waiting' ) ) ) {
										$disabled = 'readonly';
										$btn = false;
										$class = '';
									}
									?>

									<div class="row <?php echo esc_attr( $class ); ?>">
										<div class="col-4">
											<div class="form-group">
												<div class="input-group">
													<input class="form-control input" type="text" name="support_status[key][]" value="<?php echo esc_attr( $support_ticket_status['key'][ $i ] ); ?>" <?php echo esc_attr( $disabled ); ?> />
												</div>
											</div>
										</div>
										<div class="col-2">
											<div class="form-group">
												<div class="input-group">
													<input class="form-control input" type="color" name="support_status[color][]" value="<?php echo esc_attr( $support_ticket_status['color'][ $i ] ); ?>" />
												</div>
											</div>
										</div>
										<div class="col-6">
											<div class="form-group">
												<div class="input-group">
													<input class="form-control input" type="text" name="support_status[value][]" value="<?php echo esc_attr( $support_ticket_status['value'][ $i ] ); ?>" style="border-radius: .375rem;"/>
													<?php if ( $i == 0 ) {
														echo '<input type="button" class="btn-plus btn-plus-task" title="' . esc_html__( 'Add New Status', 'projectopia-core' ) . '" data-remove-title="' . esc_html__( 'Remove Status', 'projectopia-core' ) . '">';
													} else {
														if ( $btn ) {
															echo '<input type="button" class="btn-minus" title="' . esc_html__( 'Remove Status', 'projectopia-core' ) . '">';
														}
													} ?>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
							<p class="submit">
								<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
							</p>
						<?php } else { ?>
							<h3><?php esc_html_e('Support Tickets Add-On Not Found', 'projectopia-core'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-support-tickets-add-on/" target="_blank">https://projectopia.io/projectopia-support-tickets-add-on/</a>'; ?>
							<p><?php 
							/* translators: %s: Addon Link */
							printf(esc_html__('To use the Support Tickets part of the plugin, you need to purchase the Support Tickets Add-On. Please visit %s for more information.', 'projectopia-core'), wp_kses_post( $link ) ); ?></p>
						<?php } ?>
					</div>
					<div id="tabs-24">
						<h3><?php esc_html_e('FAQ Settings', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
							<?php $checked = get_option('cqpim_enable_faq'); ?>
							<input type="checkbox" name="cqpim_enable_faq" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Enable the FAQ system', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('FAQ Categories', 'projectopia-core'); ?></h3>
						<div class="pto-settings-item-wrapper">
							<a href="<?php echo esc_url( admin_url() ); ?>edit-tags.php?taxonomy=cqpim_faq_cat"><?php esc_html_e('Manage FAQ Categories', 'projectopia-core'); ?></a>
						</div>
						<h3><?php esc_html_e('Client Dashboard', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
							<?php $checked = get_option('cqpim_enable_faq_dash'); ?>
							<input type="checkbox" name="cqpim_enable_faq_dash" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Show the FAQ tab in the Client Dashboard Menu', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $checked = get_option('cqpim_enable_faq_dash_cats'); ?>
							<input type="checkbox" name="cqpim_enable_faq_dash_cats" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Group FAQ by category', 'projectopia-core'); ?>
						</div>	
						<h3><?php esc_html_e('FAQ Shortcode', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('You can display your FAQ anywhere on your site with the following shortcodes', 'projectopia-core'); ?>:</p>
						<p>
							<?php esc_html_e('Display a plain FAQ List', 'projectopia-core'); ?>: <code>[pto_faq]</code><br />
							<?php esc_html_e('Display the FAQ, grouped by category', 'projectopia-core'); ?>: <code>[pto_faq category="1"]</code>
						</p>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>			
					</div>
					<div id="tabs-8">
						<h3><?php esc_html_e('Google reCaptcha Settings', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('Register your domain name with Google reCaptcha service and add the keys to the fields below.', 'projectopia-core'); ?>
						   	<a target="_blank" href="https://www.google.com/recaptcha/admin#list"><?php esc_html_e('Get the API Keys.', 'projectopia-core'); ?></a>
						</p>
						<div class="row">
					        <div class="col-6">
						        <div class="form-group">
									<label for="google_recaptcha_site_key"><?php esc_html_e('Site Key', 'projectopia-core'); ?></label>
					                <div class="input-group">
						        	    <input class="form-control input" type="text" id="google_recaptcha_site_key" name="google_recaptcha_site_key" value="<?php echo esc_attr( get_option('google_recaptcha_site_key') ); ?>" />
						        	</div>
						        </div>
							</div>
							<div class="col-6">
						        <div class="form-group">
									<label for="google_recaptcha_secret_key"><?php esc_html_e('Secret Key', 'projectopia-core'); ?></label>
					                <div class="input-group">
										<input class="form-control input" type="text" id="google_recaptcha_secret_key" name="google_recaptcha_secret_key" value="<?php echo esc_attr( get_option('google_recaptcha_secret_key') ); ?>" />
						        	</div>
						        </div>
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
							<input type="checkbox" name="pto_frontend_form_google_recaptcha" id="pto_frontend_form_google_recaptcha" value="1" <?php checked(get_option('pto_frontend_form_google_recaptcha'), 1, true); ?> /> <?php esc_html_e('Enable google recaptcha for frontend forms.', 'projectopia-core'); ?>
						</div>
						<?php
							$value = get_option('gdpr_consent_text');
							if ( empty( $value ) ) {
								update_option( 'gdpr_consent_text', 'I give consent for my personal data to be stored for the purpose of carrying out business activities.' );
							}
							$args = array(
								'sort_order'   => 'asc',
								'sort_column'  => 'post_title',
								'hierarchical' => 1,
								'exclude'      => '',
								'include'      => '',
								'meta_key'     => '',
								'meta_value'   => '',
								'authors'      => '',
								'child_of'     => 0,
								'parent'       => -1,
								'exclude_tree' => '',
								'number'       => '',
								'offset'       => 0,
								'post_type'    => 'page',
								'post_status'  => 'publish',
							);                          
							$pages = get_pages( $args );
						?>
						<h3><?php esc_html_e('Frontend Quote Form', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This form can be displayed anywhere in your theme with the [cqpim_frontend_form] shortcode. Completion of the form will send an email to the sales email address, create a new client and a new quote and will copy any additional form fields into the Project Brief within the quote.', 'projectopia-core'); ?></p>
						<?php 
						$value = get_option( 'cqpim_frontend_form' );
						$args = array(
							'post_type'      => 'cqpim_forms',
							'posts_per_page' => -1,
							'meta_key'       => 'form_type',
							'meta_value'     => 'anonymous_frontend',
							'post_status'    => 'private',
						);
						$forms = get_posts( $args );
						if ( ! empty( $forms ) ) {
							echo '<div class="form-group">';
								echo '<div class="input-group">';
									echo '<select id="cqpim_frontend_form" name="cqpim_frontend_form" class="form-control input full-width">';
									    echo '<option value="">' . esc_html__( 'Choose a form...', 'projectopia-core' ) . '</option>';
										foreach ( $forms as $form ) {
											echo '<option value="' . esc_attr( $form->ID ) . '" ' . selected( $form->ID, $value, false ) . '>' . esc_html( $form->post_title ) . '</option>';
										}
									echo '</select>';
								echo '</div>';
							echo '</div>';
						} else {
                            echo '<div class="cqpim-alert cqpim-alert-danger alert-display mb-3">' . esc_html__( 'Please create a Frontend Quote Form at first.', 'projectopia-core' ) . '</div>';
						} ?>
						<div class="pto-inline-item-wrapper">
							<?php $auto_welcome = get_option('form_auto_welcome'); ?>
							<input type="checkbox" name="form_auto_welcome" id="form_auto_welcome" value="1" <?php checked($auto_welcome, 1, true); ?>/> <?php esc_html_e('Send the client a welcome email with login details to their dashboard (Recommended).', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<input type="checkbox" name="pto_cquo_approve" value="1" <?php checked(get_option('pto_cquo_approve'), 1); ?> /> <?php esc_html_e('Do not send login details until client is approved by admin', 'projectopia-core'); ?>
						</div>				
						<h3><?php esc_html_e('Client Registration Form (No quote required)', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('If you would like to add a form for clients to register without creating a quote, you can use the [cqpim_registration_form] shortcode anywhere on your site. ', 'projectopia-core'); ?></p>
						<div class="pto-inline-item-wrapper">
							<?php $auto_welcome = get_option('form_reg_auto_welcome'); ?>
							<input type="checkbox" name="form_reg_auto_welcome" id="form_reg_auto_welcome" value="1" <?php checked($auto_welcome, 1, true); ?>/> <?php $text = esc_html__('Send the client a welcome email with login details to their dashboard (Recommended).', 'projectopia-core'); esc_html_e('Send the client a welcome email with login details to their dashboard (Recommended).', 'projectopia-core'); ?>
						</div>
							<div class="pto-inline-item-wrapper">
							<input type="checkbox" name="pto_creg_approve" value="1" <?php checked(get_option('pto_creg_approve'), 1); ?> /> <?php esc_html_e('Do not send login details until client is approved by admin', 'projectopia-core'); ?>									
						</div>
						<h3><?php esc_html_e('Dashboard Quote Form', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This form will be displayed in the client Dashboard. Completion of the form will send an email to the sales email address, create a new quote and will copy the form fields into the Project Brief within the quote. Leave this field blank to disable the client Dashboard form', 'projectopia-core'); ?></p>
						<?php 
						$value = get_option( 'cqpim_backend_form' );
						$args = array(
							'post_type'      => 'cqpim_forms',
							'posts_per_page' => -1,
							'meta_key'       => 'form_type',
							'meta_value'     => 'client_dashboard',
							'post_status'    => 'private',
						);
						$forms = get_posts( $args );
						if ( ! empty( $forms ) ) {
							echo '<div class="form-group">';
								echo '<div class="input-group">';
									echo '<select id="cqpim_backend_form" name="cqpim_backend_form" class="form-control input full-width">';
										foreach ( $forms as $form ) {
											echo '<option value="' . esc_attr( $form->ID ) . '" ' . selected( $form->ID, $value, false ) . '>' . esc_html( $form->post_title ) . '</option>';
										}
									echo '</select>';
								echo '</div>';
							echo '</div>';
						} else {
                            echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__( 'Please create a Dashboard Quote Form at first.', 'projectopia-core' ) . '</div>';
						} ?>
						<h3><?php esc_html_e('Form Confirmation Fields', 'projectopia-core'); ?></h3>
						<?php $tc_id = get_option( 'gdpr_tc_page' ); ?>
						<div class="form-group">
							<label for="gdpr_tc_page"><?php esc_html_e( 'Terms & Conditions Page', 'projectopia-core' ); ?></label>
							<div class="input-group">
								<select id="gdpr_tc_page" name="gdpr_tc_page" class="form-control input full-width">
									<option value=""><?php esc_html_e( 'Choose...', 'projectopia-core' ); ?></option>
						        	<?php foreach ( $pages as $page ) {
										echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $tc_id, $page->ID ) . '>' . esc_html( $page->post_title ) . '</option>';
									} ?>
						        </select>
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
							<input type="checkbox" name="gdpr_tc_page_check" value="1" <?php checked(get_option('gdpr_tc_page_check'), 1); ?> /> <?php esc_html_e('Add a checkbox to confirm that the client has read the Terms & Conditions Page', 'projectopia-core'); ?><br />
						</div>
						<?php $pp_id = get_option( 'gdpr_pp_page' ); ?>
						<div class="form-group">
							<label for="gdpr_pp_page"><?php esc_html_e( 'Privacy Policy Page', 'projectopia-core' ); ?></label>
							<div class="input-group">
								<select id="gdpr_pp_page" name="gdpr_pp_page" class="form-control input full-width">
									<option value=""><?php esc_html_e( 'Choose...', 'projectopia-core'); ?></option>
									<?php foreach ( $pages as $page ) {
										echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $pp_id, $page->ID ) . '>' . esc_html( $page->post_title ) . '</option>';
									} ?>
								</select>
							</div>
						</div>
						<div class="pto-inline-item-wrapper">
							<input type="checkbox" name="gdpr_pp_page_check" value="1" <?php checked(get_option('gdpr_pp_page_check'), 1); ?> /> <?php esc_html_e('Add a checkbox to confirm that the client has read the Privacy Policy Page', 'projectopia-core'); ?><br />
						</div>				
						<h3><?php esc_html_e('Form Emails', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This email will be sent to your sales email address when a quote has been requested.', 'projectopia-core'); ?></p>
						<h4><?php esc_html_e('New Quote Email Subject', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" id="new_quote_subject" class="form-control input" name="new_quote_subject" value="<?php echo esc_attr( get_option('new_quote_subject') ); ?>" />
							</div>
						</div>
						<h4><?php esc_html_e('New Quote Email Content', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<textarea class="form-control input pto-textarea pto-h-300" id="new_quote_email" name="new_quote_email"><?php echo esc_html( get_option('new_quote_email') ); ?></textarea>
							</div>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>						
					</div>
					<div id="tabs-16">
						<?php if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) { ?>
							<h3><?php esc_html_e('Suppliers / Expenses', 'projectopia-core'); ?></h3>
							<div class="pto-inline-item-wrapper">
								<?php $auth = get_option('cqpim_activate_expense_auth'); ?>
								<input type="checkbox" name="cqpim_activate_expense_auth" value="1" <?php checked($auth, 1); ?>/> <?php esc_html_e('Expenses should be authorised by an Admin', 'projectopia-core'); ?>
							</div>
							<h4><?php esc_html_e('Expenses Authorisation Limit', 'projectopia-core'); ?></h4>
							<p clas="pto-subheading"><?php esc_html_e('If you\'d like to skip authorisation for smaller value expenses, enter the limit here. Any expenses with a value less than entered here will not require authorisation. Leave this blank if you would prefer not to set a limit.', 'projectopia-core'); ?></p>
							<div class="form-group">
								<div class="input-group">
									<input type="number" class="form-control input" name="cqpim_expense_auth_limit" value="<?php echo esc_attr( get_option('cqpim_expense_auth_limit') ); ?>" />
								</div>
							</div>
							<h4><?php esc_html_e('Permissions', 'projectopia-core'); ?></h4>
							<p><?php esc_html_e('To control who can authorise expenses, and who can skip authorisation, please visit the plugin Roles & Permissions page.', 'projectopia-core'); ?></p>
							<h3><?php esc_html_e('Authorisation Email Templates', 'projectopia-core'); ?></h3>
							<div class="tabContentInfo">
                        	    <div class="nav-tabs-panel">
                        	        <div class="tabMenuWrapper">
                        	            <ul class="nav nav-tabs" id="myTab" role="tablist">
											<li class="nav-item" role="presentation">
                        	                    <a class="nav-link active" id="exprensesAuthorisation-tab" data-toggle="tab" href="#exprensesAuthorisation" role="tab" aria-controls="exprensesAuthorisation" aria-selected="true"><?php esc_html_e('Authorisation Email', 'projectopia-core'); ?></a>
                        	                </li>
                        	                <li class="nav-item" role="presentation">
                        	                    <a class="nav-link" id="exprensesAuthorised-tab" data-toggle="tab" href="#exprensesAuthorised" role="tab" aria-controls="exprensesAuthorised" aria-selected="false"><?php esc_html_e('Authorised Email', 'projectopia-core'); ?></a>
                        	                </li>
                        	            </ul>
                        	        </div>
                        	    </div>
                        	    <div class="tabContentWrapper">
                        	        <div class="tab-content" id="myTabContent">
                        	            <div class="tab-pane fade active show" id="exprensesAuthorisation" role="tabpanel" aria-labelledby="exprensesAuthorisation-tab">
                        	                <div class="tab-pane-body">
												<h4><?php esc_html_e('Authorisation Email Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_auth_email_subject" name="cqpim_auth_email_subject" value="<?php echo esc_attr( get_option('cqpim_auth_email_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Authorisation Email Content', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<?php $content = get_option( 'cqpim_auth_email_content' ); ?>
														<textarea class="form-control input pto-textarea pto-h-300" id="cqpim_auth_email_content" name="cqpim_auth_email_content"><?php echo esc_html( $content ); ?></textarea>
													</div>
												</div>
											</div>
                        	            </div>
                        	            <div class="tab-pane fade" id="exprensesAuthorised" role="tabpanel" aria-labelledby="exprensesAuthorised-tab">
                        	                <div class="tab-pane-body">
												<h4><?php esc_html_e('Authorised Email Subject', 'projectopia-core'); ?></h4>
												<div class="form-group">
													<div class="input-group">
														<input type="text" class="form-control input" id="cqpim_authorised_email_subject" name="cqpim_authorised_email_subject" value="<?php echo esc_attr( get_option('cqpim_authorised_email_subject') ); ?>" />
													</div>
												</div>
												<h4><?php esc_html_e('Authorised Email Content', 'projectopia-core'); ?></h4>
												<?php $content = get_option( 'cqpim_authorised_email_content' ); ?>
												<div class="form-group">
													<div class="input-group">
														<textarea class="form-control input pto-textarea pto-h-300" id="cqpim_authorised_email_content" name="cqpim_authorised_email_content"><?php echo esc_html( $content ); ?></textarea>
													</div>
												</div>
											</div>
                        	            </div>
                        	        </div>
                        	    </div>
                        	</div>
							<p class="submit">
								<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
							</p>						
						<?php } else { ?>
							<h3><?php esc_html_e('Suppliers / Expenses Add-On Not Found', 'projectopia-core'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-suppliers-expenses-add-on/" target="_blank">https://projectopia.io/projectopia-suppliers-expenses-add-on/</a>'; ?>
							<p><?php 
							/* translators: %s: Addon Link */
							printf(esc_html__('To use the Suppliers / Expenses part of the plugin, you need to purchase the  Suppliers / Expenses Add-On. Please visit %s for more information.', 'projectopia-core'), wp_kses_post( $link ) ); ?></p>
						<?php } ?>						
					</div>
					<div id="tabs-17">
						<?php if ( pto_has_addon_active_license( 'pto_re', 'reporting' ) ) { ?>
							<h3><?php esc_html_e('Reporting', 'projectopia-core'); ?></h3>
							<p><?php esc_html_e('Reporting Add-On Enabled. No settings required.', 'projectopia-core'); ?></p>
						<?php } else { ?>
							<h3><?php esc_html_e('Reporting Add-On Not Found', 'projectopia-core'); ?></h3>
							<?php $link = '<a href="https://projectopia.io/projectopia-reporting-add-on/" target="_blank">https://projectopia.io/projectopia-reporting-add-on/</a>'; ?>
							<p><?php 
							/* translators: %s: Addon Link */
							printf(esc_html__('To use the Reporting part of the plugin, you need to purchase the Projectopia Reporting Add-On. Please visit %s for more information.', 'projectopia-core'), wp_kses_post( $link ) ); ?></p>
						<?php } ?>						
					</div>
					<div id="tabs-12">
						<h3><?php esc_html_e('Email Piping', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('Email Piping works by scanning your mailbox and parsing new emails into the relevant Task/Support Ticket/Project.', 'projectopia-core'); ?></p>
						<p><?php esc_html_e('We highly recommend creating a new mailbox for this process, as any incoming email (that doesn\'t relate to an existing item) with an address that is in the system as a client will register a new Support Ticket (If configured).', 'projectopia-core'); ?></p>
						<p><?php esc_html_e('It is also critical to place the %%PIPING_ID%% tag in the subject line of all emails related to Support Tickets, Tasks and Project Messages.', 'projectopia-core'); ?></p>
						<p><?php esc_html_e('You should also check that the emails contain the latest update message. You can check for the correct tag by clicking the "View Sample Content" button next to each email.', 'projectopia-core'); ?></p>
						<h3><?php esc_html_e('Mail Settings', 'projectopia-core'); ?></h3>
						<?php $value = get_option('cqpim_mail_server'); ?>
						<h4><?php 
						/* translators: %s: Ducumentation Link */
						printf( esc_html__( 'Mail Server Address (including port and path if necessary, please refer to our %s for details )', 'projectopia-core' ), '<a href="https://projectopia.io/docs41/email-piping/">Documentation</a>' ); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="cqpim_mail_server" id="cqpim_mail_server" class="form-control input" value="<?php echo esc_attr( $value ); ?>" />
							</div>
						</div>
						<?php $value = get_option('cqpim_piping_address'); ?>
						<h4><?php esc_html_e('Email Address (If Piping is active, this address will be the reply address of all ticket/task emails. Ensure it matches the mailbox below)', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="cqpim_piping_address" id="cqpim_piping_address" class="form-control input" value="<?php echo esc_attr( $value ); ?>" />
							</div>
						</div>
						<div class="cqpim-alert cqpim-alert-warning alert-display"><?php esc_html_e('Do NOT use the same email address that is used as the "Support Email" address on the company details tab. This will cause a loop as the addresses will keep emailing each other. Use a dedicated mailbox solely for email piping.', 'projectopia-core'); ?></div>
						<div class="row">
							<div class="col-6">
							    <div class="form-group">
							    	<label for="cqpim_mailbox_name"><?php esc_html_e('Email Username (often the same as the email address)', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="text" name="cqpim_mailbox_name" id="cqpim_mailbox_name" class="form-control input" value="<?php echo esc_attr( get_option('cqpim_mailbox_name') ); ?>" />
									</div>
								</div>
							</div>
							<div class="col-6">
							    <div class="form-group">
							    	<label for="cqpim_mailbox_pass"><?php esc_html_e('Email Password', 'projectopia-core'); ?></label>
							    	<div class="input-group">
										<input type="password" name="cqpim_mailbox_pass" id="cqpim_mailbox_pass" class="form-control input" value="<?php echo esc_attr( get_option('cqpim_mailbox_pass') ); ?>" />
									</div>
								</div>
							</div>
						</div>
						<button id="test_piping" class="btn piaBtn mb-4">
							<?php esc_html_e('Test Settings', 'projectopia-core'); ?></button> <div id="test_apinner" class="ajax_spinner" style="display: none;">
						</div>
						<h3><?php esc_html_e('ID Prefix', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('The ID Prefix is used in the Piping ID tag and helps the system to identify where updates should go. For example, if you enter "ID" in this field, the result of the %%PIPING_ID%% tag would be "[ID:1234]".','projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<?php $value = get_option('cqpim_string_prefix'); ?>
								<input type="text" name="cqpim_string_prefix" class="form-control input" value="<?php echo esc_attr( $value ); ?>" />
							</div>
						</div>
						<h3><?php esc_html_e('Other Settings', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
							<?php $value = get_option('cqpim_create_support_on_email'); ?>
							<input type="checkbox" name="cqpim_create_support_on_email" <?php if ( $value == 1 ) { echo 'checked="checked"'; } ?> value="1" /> <?php esc_html_e('Create a new support ticket if an email arrives from an address registered to a client in the system', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $value = get_option('cqpim_create_support_on_unknown_email'); ?>
							<input type="checkbox" name="cqpim_create_support_on_unknown_email" <?php if ( $value == 1 ) { echo 'checked="checked"'; } ?> value="1" /> <?php esc_html_e('Create a new support ticket even if an email arrives from an address not registered to a client in the system', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $value = get_option('cqpim_send_piping_reject'); ?>
							<input type="checkbox" name="cqpim_send_piping_reject" <?php if ( $value == 1 ) { echo 'checked="checked"'; } ?> value="1" /> <?php esc_html_e('Send a reject email (below) if an email is received that doesn\'t match a client in the system.', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $value = get_option('cqpim_piping_delete'); ?>
							<input type="checkbox" name="cqpim_piping_delete" <?php if ( $value == 1 ) { echo 'checked="checked"'; } ?> value="1" /> <?php esc_html_e('Delete the email from the Piping inbox once it has been processed.', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('Reject Email', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('This email will be sent to the sender if the from email address is not registered in Projectopia.', 'projectopia-core'); ?></p>
						<h4><strong><?php esc_html_e('Reject Email Subject', 'projectopia-core'); ?></strong></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" id="cqpim_bounce_subject" class="form-control input" name="cqpim_bounce_subject" value="<?php echo esc_attr( get_option('cqpim_bounce_subject') ); ?>" />
							</div>
						</div>
						<h4><strong><?php esc_html_e('Reject Email Content', 'projectopia-core'); ?></strong></h4>
						<div class="form-group">
							<div class="input-group">
								<textarea class="form-control input pto-textarea pto-h-300" id="cqpim_bounce_content" name="cqpim_bounce_content"><?php echo esc_html( get_option('cqpim_bounce_content') ); ?></textarea>
							</div>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>							
					</div>
					<?php 
					$user = wp_get_current_user();
					if ( in_array( 'administrator', $user->roles ) ) { ?>
						<div id="tabs-13">
							<h3><?php esc_html_e('Plugin Reset', 'projectopia-core'); ?></h3>
							<p><?php esc_html_e('If you would like to reset the plugin, click the reset button. This will deactivate the plugin and remove ALL data, including settings, roles, permissions and all posts (projects, clients etc). This cannot be undone.', 'projectopia-core'); ?></p>
							<p><strong><?php esc_html_e('IMPORTANT: Any users who have a plugin role will need to have their role reassigned and will not be able to access the site until this is done.', 'projectopia-core'); ?></strong></p>
							<button id="reset-cqpim" class="cancel-creation piaBtn redColor red-btn"><?php esc_html_e('Reset plugin and remove ALL data', 'projectopia-core'); ?></button>
							<br /><br />
							<div id="reset_cqpim_container" style="display: none;">
								<div id="reset_cqpim">
									<div style="padding: 12px;">
										<h3><?php esc_html_e('Are you sure?', 'projectopia-core'); ?></h3>
										<p class="mb-3"><?php esc_html_e('Are you sure you want to deactivate the plugin and remove ALL associated data and settings?', 'projectopia-core'); ?></p>
										<a class="cancel-colorbox cancel-creation piaBtn redColor trsp red-btn" style="color: #fff;"><?php esc_html_e('Cancel', 'projectopia-core'); ?></a>
										<button class="reset-cqpim-conf btn piaBtn right trsp"><?php esc_html_e('Confirm', 'projectopia-core'); ?></button>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					<div id="tabs-14">
						<h3><?php esc_html_e('Enable Messaging System', 'projectopia-core'); ?></h3>
						<div class="pto-inline-item-wrapper">
							<?php $checked = get_option('cqpim_enable_messaging'); ?>
							<input type="checkbox" name="cqpim_enable_messaging" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Enable Messaging System for Team Members', 'projectopia-core'); ?>
						</div>
						<div class="pto-inline-item-wrapper">
							<?php $checked = get_option('cqpim_messages_allow_client'); ?>
							<input type="checkbox" name="cqpim_messages_allow_client" value="1" <?php checked($checked, 1, true); ?> /> <?php esc_html_e('Enable Messaging System for Clients', 'projectopia-core'); ?>
						</div>
						<h3><?php esc_html_e('New Message Notication', 'projectopia-core'); ?></h3>
						<h4><?php esc_html_e('New Message Email Subject', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<input type="text" id="cqpim_new_message_subject" class="form-control input" name="cqpim_new_message_subject" value="<?php echo esc_attr( get_option('cqpim_new_message_subject') ); ?>" />
							</div>
						</div>
						<h4><?php esc_html_e('New Message Email Content', 'projectopia-core'); ?></h4>
						<div class="form-group">
							<div class="input-group">
								<textarea class="form-control input pto-textarea pto-h-300" id="cqpim_new_message_content" name="cqpim_new_message_content"><?php echo esc_html( get_option('cqpim_new_message_content') ); ?></textarea>
							</div>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>						
					</div>
					<div id="tabs-15">
						<h3><?php esc_html_e('HTML Email Template', 'projectopia-core'); ?></h3>
						<p><?php esc_html_e('If you would like to customise the outgoing emails from Projectopia with an HTML email template, you can build one here.', 'projectopia-core'); ?></p>
						<p><?php esc_html_e('You can use the %%EMAIL_CONTENT%% tag to render the content of the email in your template', 'projectopia-core'); ?></p>
						<p><?php esc_html_e('You can use the %%LOGO%% tag to render the Company Logo', 'projectopia-core'); ?></p>
						<h3><?php esc_html_e('HTML Email Styles', 'projectopia-core'); ?></h3>
						<div class="form-group">
							<div class="input-group">
								<textarea class="form-control input pto-textarea pto-h-500" name="cqpim_html_email_styles"><?php echo esc_html( get_option('cqpim_html_email_styles') ); ?></textarea>
							</div>
						</div>
						<h3><?php esc_html_e('HTML Email Markup', 'projectopia-core'); ?></h3>
						<div class="form-group">
							<div class="input-group">
								<textarea class="form-control input pto-textarea pto-h-500" name="cqpim_html_email"><?php echo esc_html( get_option('cqpim_html_email') ); ?></textarea>
							</div>
						</div>
						<p class="submit">
							<input type="submit" class="piaBtn btn btn-primary" value="<?php esc_attr_e('Save Changes', 'projectopia-core'); ?>" />
						</p>						
					</div>
					<?php
						/**
						 * Create action to add new tab contents for setting options.
						 * @since 5.0.4
						 */
						do_action( 'pto_add_new_setting_tab_content' );
                    ?>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div><!-- eof #main-container -->
			<div class="clear"></div>
		</form>
		<div class="clear"></div>
	</div><!-- eof.wrap -->
<?php } 