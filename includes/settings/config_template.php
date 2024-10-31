<?php
function pto_settings_values() {
$settings = array(
	'system'         => array(
		'cqpim_plugin_name'                    => 'Projectopia',
		'cqpim_use_plugin_icon'                => 0,
		'cqpim_show_docs_link'                 => 1,
		'cqpim_date_format'                    => 'd/m/Y',
		'cqpim_allowed_extensions'             => 'png,jpg,gif,zip,doc,docx,xls,xlsx,pdf',
		'cqpim_disable_avatars'                => 0,
		'cqpim_invoice_slug'                   => 'pto_invoice',
		'cqpim_quote_slug'                     => 'pto_quote',
		'cqpim_project_slug'                   => 'pto_project',
		'cqpim_support_slug'                   => 'pto_support',
		'cqpim_task_slug'                      => 'pto_task',
		'cqpim_faq_slug'                       => 'pto_faq',
		'enable_quotes'                        => 1,
		'enable_quote_terms'                   => 0,
		'enable_project_creation'              => 1,
		'enable_project_contracts'             => 1,
		'auto_contract'                        => 1,
		'disable_invoices'                     => 0,
		'invoice_workflow'                     => 0,
		'auto_send_invoices'                   => 1,
		'cqpim_save_dashboard_metabox_filters' => 0,
	),
	'company'        => array(
		'company_name'                    => 'Some Company',
		'company_address'                 => '123 Fake Street' . PHP_EOL . 'Somewhereville' . PHP_EOL . 'Somecity',
		'company_postcode'                => 'AB12 3CD',
		'company_telephone'               => '123456789',
		'company_sales_email'             => get_option('admin_email'),
		'company_accounts_email'          => get_option('admin_email'),
		'company_support_email'           => get_option('admin_email'),
		'currency_symbol'                 => '£',
		'currency_symbol_position'        => 'l',
		'currency_symbol_space'           => 0,
		'allow_client_currency_override'  => 0,
		'allow_quote_currency_override'   => 0,
		'allow_project_currency_override' => 0,
		'allow_invoice_currency_override' => 0,
		'currency_code'                   => 'GBP',
		'company_invoice_terms'           => 14,
	),
	'business_hours' => array(
		'pto_opening'                  => array(     
			'mon' => array(
				'active' => '1',
				'open'   => '9:00am',
				'close'  => '5:00pm',
			),
			'tue' => array(
				'active' => '1',
				'open'   => '9:00am',
				'close'  => '5:00pm',
			),
			'wed' => array(
				'active' => '1',
				'open'   => '9:00am',
				'close'  => '5:00pm',
			),
			'thu' => array(
				'active' => '1',
				'open'   => '9:00am',
				'close'  => '5:00pm',
			),
			'fri' => array(
				'active' => '1',
				'open'   => '9:00am',
				'close'  => '5:00pm',
			),
			'sat' => array(
				'active' => '0',
				'open'   => '',
				'close'  => '',
			),
			'sun' => array(
				'active' => '0',
				'open'   => '',
				'close'  => '',
			),
		),
		'pto_support_open_message'     => 'Our Support Department is currently OPEN. Please raise a ticket and we\'ll get back to you shortly.',
		'pto_support_closed_message'   => 'Our Support Department is currently CLOSED. We are open from 9am to 5pm, Monday to Friday, GMT. Please raise a ticket and we will respond during our opening hours.',
		'pto_support_opening_warning'  => 0,
		'pto_shortcode_open_message'   => 'Our Support Department is currently OPEN. Please raise a ticket and we\'ll get back to you shortly.',
		'pto_shortcode_closed_message' => 'Our Support Department is currently CLOSED. We are open from 9am to 5pm, Monday to Friday, GMT. Please raise a ticket and we will respond during our opening hours.',
	),
	'clients'        => array(
		'auto_welcome'           => 1,
		'auto_welcome_subject'   => 'Your %%COMPANY_NAME%% Dashboard Login Details',
		'auto_welcome_content'   => 'Dear %%CLIENT_NAME%%
Thank you for your interest in working with %%COMPANY_NAME%%.
Here are the login details to your dashboard, where you can view quotes/estimates and keep up to date on any past, current and future projects.
Login URL: %%LOGIN%%
Email Address: %%CLIENT_EMAIL%%
Password: %%CLIENT_PASSWORD%%
Please keep these details safe as you will need to use them to view any quotes/estimates that we send to you.
If you have any questions, please don\'t hesitate to get in touch.
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'password_reset_subject' => 'Your new Password from %%COMPANY_NAME%%',
		'password_reset_content' => 'Dear %%CLIENT_NAME%%
%%CURRENT_USER%% has just reset the password to your %%COMPANY_NAME%% dashboard.
Here are the new login details to your dashboard, where you can view quotes/estimates and keep up to date on any past, current and future projects.
Login URL: %%LOGIN%%
Email Address: %%CLIENT_EMAIL%%
Password: %%NEW_PASSWORD%%
Please keep these details safe as you will need to use them to view any quotes/estimates that we send to you.
If you have any questions, please don\'t hesitate to get in touch.
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'added_contact_subject'  => 'Your %%COMPANY_NAME%% Dashboard Login Details',
		'added_contact_content'  => 'Dear %%CONTACT_NAME%%
%%CURRENT_USER%% has just added you as a contact for %%CLIENT_COMPANY%%.
Here are the login details to your dashboard, where you can view quotes/estimates and keep up to date on any past, current and future projects.
Login URL: %%LOGIN%%
Email Address: %%CONTACT_EMAIL%%
Password: %%CONTACT_PASSWORD%%
Please keep these details safe as you will need to use them to view any quotes/estimates that we send to you.
If you have any questions, please don\'t hesitate to get in touch.
Best Regards
%%CURRENT_USER%%',
	),
	'dashboard'      => array(
		'client_dashboard_type'         => 'inc',
		'allow_client_settings'         => 1,
		'allow_client_users'            => 1,
		'cqpim_login_reg'               => 1,
		'client_password_reset_subject' => 'Your new Password from %%COMPANY_NAME%%',
		'client_password_reset_content' => 'Dear %%CLIENT_NAME%%
A request has been made to reset the password on your %%COMPANY_NAME%% Dashboard.
Please click the link below to complete the password reset process.
%%PASSWORD_RESET_LINK%%
If you did not make this request, please disregard this email.
If you have any questions, please don\'t hesitate to get in touch.
Best Regards
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'quotes'         => array(
		'quote_header'                  => 'Dear %%CLIENT_NAME%%
Thank you for your interest in working with %%COMPANY_NAME%%.
Following our recent conversation, I have prepared the following detailed %%TYPE%% which I hope is of interest to you.',
		'quote_footer'                  => 'I hope this %%TYPE%% meets your business goals.
Please don\'t hesitate to call me if you have any questions.
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'quote_acceptance_text'         => 'If you would like to proceed with this %%TYPE%%, please accept by filling in the form below. You will then be sent a contract to sign and, if necessary, a deposit invoice. Please ensure that you are completely happy with this %%TYPE%% before proceeding.
Your name and IP address as well as the current date & time will be recorded when accepting this %%TYPE%%.',
		'quote_email_pdf_attach'        => 0,
		'quote_email_subject'           => 'FAO: %%CLIENT_NAME%% - %%COMPANY_NAME%% %%TYPE%%: %%QUOTE_REF%%',
		'quote_default_email'           => 'Dear %%CLIENT_NAME%%
Thank you for your interest in working with %%COMPANY_NAME%%.
Following our recent conversation, I have prepared the following detailed %%TYPE%% which I hope is of interest to you. You can view the %%TYPE%% by clicking on the following link.
%%LOGIN%%
%%QUOTE_CLIENT_URL%%
I hope this %%TYPE%% meets your business goals. Please don\'t hesitate to call me if you have any questions.
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_quote_message_subject'  => '%%CURRENT_USER%% has just sent a message related to Quote %%QUOTE_REF%% %%PIPING_ID%%',
		'client_quote_message_email'    => 'Dear %%CLIENT_NAME%%
%%CURRENT_USER%% has just sent a new message related to Quote %%QUOTE_REF%%.
%%MESSAGE%%
%%LOGIN%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'company_quote_message_subject' => '%%CURRENT_USER%% has just sent a message related to Quote %%QUOTE_REF%% %%PIPING_ID%%',
		'company_quote_message_email'   => 'Dear %%TEAM_NAME%%
%%CURRENT_USER%% has just sent a new message related to Quote %%QUOTE_REF%%.
%%MESSAGE%%
%%LOGIN%%
Best Regards
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'projects'       => array(
		'default_contract_text'    => get_option('default_contract_text'),
		'contract_acceptance_text' => 'If you would like to proceed with this project, please sign this contract by filling in the form below.
Your name and IP address as well as the current date & time will be recorded when signing this contract, and you agree to be bound by the terms listed above.',
		'client_contract_subject'  => 'Contract Documentation for %%COMPANY_NAME%% Project %%PROJECT_REF%%',
		'client_contract_email'    => 'Dear %%CLIENT_NAME%%
Thank you for your interest in working with %%COMPANY_NAME%%.
Please click below to view and sign your contract.
%%LOGIN%%
If you cannot remember your password to our client area, you can reset it by clicking this link -
%%CLIENT_PASSWORD_LINK%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_message_subject'   => '%%CURRENT_USER%% has just sent a message in Project %%PROJECT_REF%% %%PIPING_ID%%',
		'client_message_email'     => 'Dear %%CLIENT_NAME%%
%%CURRENT_USER%% has just sent a new message in Project %%PROJECT_REF%%.
%%MESSAGE%%
%%LOGIN%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'company_message_subject'  => '%%CURRENT_USER%% has just sent a message in Project %%PROJECT_REF%% %%PIPING_ID%%',
		'company_message_email'    => 'Dear %%TEAM_NAME%%
%%CURRENT_USER%% has just sent a new message in Project %%PROJECT_REF%%.
%%MESSAGE%%
%%LOGIN%%
Best Regards
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'invoices'       => array(
		'cqpim_invoice_template'          => 1,
		'client_invoice_email_attach'     => 1,
		'client_invoice_footer'           => 'Thank you for your business, we appreciate it very much.
Please pay via BACS to -
Account name: %%ACCOUNT_NAME%%
Sort code: %%SORT_CODE%%
Account number: %%ACCOUNT_NUMBER%%
IBAN: %%IBAN%%',
		'client_invoice_subject'          => 'Invoice %%INVOICE_ID%% from %%COMPANY_NAME%%',
		'client_invoice_email'            => 'Dear %%CLIENT_NAME%%
Thank you for working with %%COMPANY_NAME%%.
Please click below to view invoice %%INVOICE_ID%%.
%%LOGIN%%
If you cannot remember your password to our client area, you can reset it by clicking this link -
%%CLIENT_PASSWORD_LINK%%
For your convenience, you can also view the Invoice without logging in by clicking here -
%%INVOICE_LINK%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_deposit_invoice_subject'  => 'Deposit Invoice %%INVOICE_ID%% from %%COMPANY_NAME%%',
		'client_deposit_invoice_email'    => 'Dear %%CLIENT_NAME%%
Thank you for working with %%COMPANY_NAME%%.
A new invoice (%%INVOICE_ID%%) is now available in your client area.
Please note that this is a deposit invoice which must be received before the date shown on the invoice. Failure to make payment by this date may result in the project being delayed.
%%LOGIN%%
If you cannot remember your password to our client area, you can reset it by clicking this link -
%%CLIENT_PASSWORD_LINK%%
For your convenience, you can also view the Invoice without logging in by clicking here -
%%INVOICE_LINK%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_invoice_reminder_subject' => 'REMINDER: Invoice %%INVOICE_ID%% from %%COMPANY_NAME%%',
		'client_invoice_reminder_email'   => 'Dear %%CLIENT_NAME%%
Thank you for working with %%COMPANY_NAME%%.
This is a reminder regarding invoice %%INVOICE_ID%%.
%%LOGIN%%
If you cannot remember your password to our client area, you can reset it by clicking this link -
%%CLIENT_PASSWORD_LINK%%
For your convenience, you can also view the Invoice without logging in by clicking here -
%%INVOICE_LINK%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_invoice_overdue_subject'  => 'OVERDUE: Invoice %%INVOICE_ID%% from %%COMPANY_NAME%%',
		'client_invoice_overdue_email'    => 'Dear %%CLIENT_NAME%%
Thank you for working with %%COMPANY_NAME%%.
Please click below to view invoice %%INVOICE_ID%%,  which is now overdue.
%%LOGIN%%
If you cannot remember your password to our client area, you can reset it by clicking this link -
%%CLIENT_PASSWORD_LINK%%
For your convenience, you can also view the Invoice without logging in by clicking here -
%%INVOICE_LINK%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_invoice_receipt_subject'  => 'Notification of payment on Invoice %%INVOICE_ID%% from %%COMPANY_NAME%%',
		'client_invoice_receipt_email'    => 'Dear %%CLIENT_NAME%%
Thank you for your payment of %%AMOUNT%% for invoice %%INVOICE_ID%%. This has been processed and your account has been updated.
%%LOGIN%%
If you cannot remember your password to our client area, you can reset it by clicking this link -
%%CLIENT_PASSWORD_LINK%%
For your convenience, you can also view the Invoice without logging in by clicking here -
%%INVOICE_LINK%%
Best Regards
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'teams'          => array(
		'team_account_subject' => 'Your Login Details from %%COMPANY_NAME%%',
		'team_account_email'   => 'Dear %%TEAM_NAME%%
%%CURRENT_USER%% has just added you as a team member!
Here are the login details to your dashboard.
Login URL: %%LOGIN%%
Email Address: %%TEAM_EMAIL%%
Password: %%TEAM_PASSWORD%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'team_reset_subject'   => 'Your new Password from %%COMPANY_NAME%%',
		'team_reset_email'     => 'Dear %%TEAM_NAME%%
%%CURRENT_USER%% has just reset the password to your %%COMPANY_NAME%% dashboard.
Here are the new login details to your dashboard.
Login URL: %%LOGIN%%
Email Address: %%TEAM_EMAIL%%
Password: %%NEW_PASSWORD%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'team_project_subject' => '%%CURRENT_USER%% has added you to Project %%PROJECT_REF%%',
		'team_project_email'   => 'Dear %%TEAM_NAME%%
%%CURRENT_USER%% has just added you as a team member on Project %%PROJECT_REF%%.
You will now be able to see this project in your dashboard.
Login URL: %%LOGIN%%
Email Address: %%TEAM_EMAIL%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'tasks'          => array(
		'team_assignment_subject' => '%%TASK_PROJECT%%: Task Updated - %%TASK_TITLE%% %%PIPING_ID%%',
		'team_assignment_email'   => 'Dear %%NAME%%
%%CURRENT_USER%% has just updated a task: (%%TASK_TITLE%%)
Task Status: %%TASK_STATUS%%
Task Priority: %%TASK_PRIORITY%%
Start Date: %%TASK_START%%
Deadline: %%TASK_DEADLINE%%
Estimated Time (Hours): %%TASK_EST%%
Percentage Complete: %%TASK_PC%%
Project: %%TASK_PROJECT%%
Milestone: %%TASK_MILESTONE%%
Assignee: %%TASK_OWNER%%
%%TASK_UPDATE%%
Click here to see the updates -
%%TASK_URL%%
Best Regards
%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'tickets'        => array(
		'client_create_ticket_subject' => '%%CLIENT_NAME%% has just raised a new Support Ticket %%PIPING_ID%%',
		'client_create_ticket_email'   => 'Dear %%COMPANY_NAME%%
%%CLIENT_NAME%% has just raised a new Support Ticket.
Title: %%TICKET_TITLE%%
ID: %%TICKET_ID%%
Priority: %%TICKET_PRIORITY%%
Status: %%TICKET_STATUS%%
%%TICKET_UPDATE%%
Please log in to your dashboard to read and reply to their ticket.
Best Regards
%%COMPANY_NAME%% Support
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
		'client_update_ticket_subject' => '%%UPDATER_NAME%% has just updated Support Ticket %%TICKET_ID%% %%PIPING_ID%% ',
		'client_update_ticket_email'   => 'Dear %%NAME%%
%%UPDATER_NAME%% has just updated a Support Ticket.
Title: %%TICKET_TITLE%%
ID: %%TICKET_ID%%
Priority: %%TICKET_PRIORITY%%
Status: %%TICKET_STATUS%%
%%TICKET_UPDATE%%
Please log in to your dashboard to read and reply to their update.
Best Regards
%%COMPANY_NAME%% Support
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'forms'          => array(
		'form_reg_auto_welcome' => 1,
		'new_quote_subject'     => '%%NAME%% just requested a new Quote',
		'new_quote_email'       => 'Dear %%COMPANY_NAME%%
%%NAME%% just requested a new quote.
You can view the new quote here - %%QUOTE_URL%%',
	),
	'piping'         => array(
		'cqpim_string_prefix'           => 'ID',
		'cqpim_create_support_on_email' => 1,
		'cqpim_send_piping_reject'      => 1,
		'cqpim_piping_delete'           => 1,
		'cqpim_bounce_subject'          => 'Email Address Not Recognised',
		'cqpim_bounce_content'          => 'Dear %%SENDER_NAME%%
Unfortunately your email could not be processed at this time, either because your email address is not registered in our system or because the email address does not match the %%TYPE%% that you are replying to.
Best Regards
%%COMPANY_NAME%% Support
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'messaging'      => array(
		'cqpim_enable_messaging'      => 1,
		'cqpim_messages_allow_client' => 1,
		'cqpim_new_message_subject'   => '%%CONVERSATION_ID%% %%SENDER_NAME%% has sent you a new message',
		'cqpim_new_message_content'   => 'Dear %%RECIPIENT_NAME%%
%%SENDER_NAME%% has just sent a new message in conversation "%%CONVERSATION_SUBJECT%%"
%%MESSAGE%%
Please log in to your dashboard to view and action the message.
Best Regards
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%',
	),
	'import'         => array(
		'cqpim_settings_imported' => true,
	),
);
	/**
	 * Create filters to update the config template for new setting options.
	 * @since 4.3.5
	 * 
	 * @param array $settings List of default templates and value.
	 * 
	 * @return array $settings
	 */
	$settings = apply_filters('pto_add_new_setting_template', $settings );

	return $settings;
}

