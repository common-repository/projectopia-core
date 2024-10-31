<?php
function pto_return_localisation() {
	global $wp_locale;
	$localisation = array(
		'ajaxurl'             => admin_url('admin-ajax.php'),
		'stripe_key'          => get_option('client_invoice_stripe_key'),
		'PTO_PLUGIN_URL'      => PTO_PLUGIN_URL,
		'calendar'            => array(
			'closeText'       => __( 'Done', 'projectopia-core' ),
			'currentText'     => __( 'Today', 'projectopia-core' ),
			'monthNames'      => array_values( $wp_locale->month ),
			'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
			'monthStatus'     => __( 'Show a different month', 'projectopia-core' ),
			'dayNames'        => array_values( $wp_locale->weekday ),
			'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'     => array_values( $wp_locale->weekday_initial ),
			'dateFormat'      => pto_date_format_php_to_js( get_option( 'cqpim_date_format' ) ),
			'firstDay'        => get_option( 'start_of_week' ),
		),
		'datatables'          => array(
			'sEmptyTable'     => __('No data available in table', 'projectopia-core'),
			'sInfo'           => __('Showing _START_ to _END_ of _TOTAL_', 'projectopia-core'),
			'sInfoEmpty'      => __('Showing 0 to 0 of 0', 'projectopia-core'),
			'sInfoFiltered'   => __('(filtered from _MAX_ total entries)', 'projectopia-core'),
			'sInfoPostFix'    => '',
			'sInfoThousands'  => ',',
			'sLengthMenu'     => __('Show _MENU_', 'projectopia-core'),
			'sLoadingRecords' => __('Loading...', 'projectopia-core'),
			'sProcessing'     => __('Processing...', 'projectopia-core'),
			'sSearch'         => __('Search:', 'projectopia-core'),
			'sZeroRecords'    => __('No matching records found', 'projectopia-core'),
			'sFirst'          => __('First', 'projectopia-core'),
			'sLast'           => __('Last', 'projectopia-core'),
			'sNext'           => __('Next', 'projectopia-core'),
			'sPrevious'       => __('Previous', 'projectopia-core'),
			'sSortAscending'  => __(': activate to sort column ascending', 'projectopia-core'),
			'sSortDescending' => __(': activate to sort column descending', 'projectopia-core'),
		),
		'cf_alerts'           => array(
			'done' => __('Custom fields updated successfully', 'projectopia-core'),
			'fail' => __('There was a problem updating the fields. Perhaps you didn\'t add any?', 'projectopia-core'),
		),
		'quote_vars'          => array(
			'assign_error'  => __('You are not assigned to this project', 'projectopia-core'),
			'project_dates' => __('Please choose the Type, Client, Ref and Dates for this Quote / Estimate', 'projectopia-core'),
		),
		'project_vars'        => array(
			'assign_error'  => __('You are not assigned to this project', 'projectopia-core'),
			'project_dates' => __('Please add a Ref and Dates for this Project', 'projectopia-core'),
			'ms_complete'   => __('You cannot mark a milestone as complete until you have completed the finished cost field.', 'projectopia-core'),
		),
		'teams'               => array(
			'link_error' => __('Something went wrong, pleae check your WordPress account to make sure the Display Name field has been completed. Then try again.', 'projectopia-core'),
		),
		'uploads'             => array(
			'upload_url'        => admin_url('async-upload.php'),
			'ajax_url'          => admin_url('admin-ajax.php'),
			'nonce'             => wp_create_nonce('media-form'),
			'strings'           => array(
				'uploading' => __('Uploading...', 'projectopia-core'),
				'success'   => __('Successfully uploaded', 'projectopia-core'),
				'change'    => __('Remove', 'projectopia-core'),
				'error'     => __('Failed to upload file. It may not be on our list of allowed extensions. Please try again.', 'projectopia-core'),
			),
			'client_up_fail'    => __('The file(s) could not be uploaded or no files were selected for upload, please try again', 'projectopia-core'),
			'client_up_success' => __('The file(s) were successfully uploaded.', 'projectopia-core'),
		),
		'messaging'           => array(
			'dialogs' => array(
				'deleteconv' => __('Delete Conversation', 'projectopia-core'),
				'leaveconv'  => __('Leave Conversation', 'projectopia-core'),
				'removeconv' => __('Remove User', 'projectopia-core'),
				'addconv'    => __('Add User', 'projectopia-core'),
				'cancel'     => __('Cancel', 'projectopia-core'),
			),
		),
		'quotes'              => array(
			'assign_error'  => __('You are not assigned to this project', 'projectopia-core'),
			'project_dates' => __('Please choose the Type, Client, Ref and Dates for this Quote / Estimate', 'projectopia-core'),
		),
		'projects'            => array(
			'assign_error'   => __('You are not assigned to this project', 'projectopia-core'),
			'project_dates'  => __('Please add a Ref and Dates for this Project', 'projectopia-core'),
			'ms_complete'    => __('You cannot mark a milestone as complete until you have completed the finished cost field.', 'projectopia-core'),
			'pm_update_text' => __('Project Manager updated successfully!', 'projectopia-core'),
		),
		'confirm_delete'      => __( 'It will permanently delete selected entries. It can\'t be undone. Are you sure?', 'projectopia-core' ),
		'confirm_delete_user' => __( 'It will permanently delete selected clients and associated WordPress users. It can\'t be undone. Are you sure?', 'projectopia-core' ),
		'tasks'               => array(
			'skip_btn'   => __( 'Skip', 'projectopia-core' ),
			'submit_btn' => __( 'Submit', 'projectopia-core' ),
			'add_notes'  => __( 'Add Notes', 'projectopia-core' ),
			'notes'      => __( 'Notes', 'projectopia-core' ),
		),
		'global_nonce'        => wp_create_nonce( PTO_GLOBAL_NONCE ),
	);
	return $localisation;
}
add_action( 'wp_loaded', 'pto_register_front_and_back_scripts' );
function pto_register_front_and_back_scripts(){
	wp_register_style( 
		'pto_fontawesome', 
		'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_colorbox', 
		PTO_ASSETS_URL  . 'js/jquery.colorbox-min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_equalheights', 
		PTO_ASSETS_URL  . 'js/jquery.matchHeight-min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), 
		PTO_VERSION, 
		'all' 
	);  
	wp_register_script( 
		'pto_ppjs', 
		'https://www.paypalobjects.com/api/checkout.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_multi_upload', 
		PTO_PLUGIN_URL  . '/includes/scripts/upload/multi_upload.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_upload_avatar',
		PTO_PLUGIN_URL  . '/includes/scripts/upload/avatar_upload.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_colorbox_styles', 
		PTO_PLUGIN_URL  . '/includes/css/colorbox.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_autocomplete_styles', 
		PTO_PLUGIN_URL  . '/includes/css/autocomplete.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_datatables',
		PTO_ASSETS_URL  . 'js/jquery.dataTables.min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);

	wp_register_style( 
		'pto_datatables_styles', 
		PTO_ASSETS_URL  . 'css/jquery.dataTables.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);

	wp_register_script( 
		'pto_tokeninput',
		PTO_ASSETS_URL  . 'js/jquery.tokeninput.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_masonry',  
		PTO_ASSETS_URL  . 'js/masonry.pkgd.min.js',
		array(),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_jquery_ui_styles', 
		PTO_ASSETS_URL  . 'css/jquery-ui.css',
		array(), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_timepicker',
		PTO_ASSETS_URL  . 'js/jquery.timepicker.min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);  
	wp_register_style( 
		'pto_timepicker_styles', 
		PTO_ASSETS_URL  . 'css/jquery.timepicker.min.css',
		array(), 
		PTO_VERSION,
		'all' 
	);  
	wp_register_script( 
		'pto_timer',
		PTO_ASSETS_URL  . 'js/timer.jquery.min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_stripe_js',
		'https://js.stripe.com/v3/',
		array(),
		get_bloginfo( 'version' ),
		false
	);
	wp_register_script( 
		'pto_stripe_ideal', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/stripe_ideal.js',
		array( 'jquery', 'pto_colorbox', 'pto_stripe_js' ),
		PTO_VERSION, 
		true 
	);
}
add_action( 'wp_enqueue_scripts', 'pto_register_all_non_admin_scripts' );
function pto_register_all_non_admin_scripts() {
	$font = pto_get_client_dashboard_gfont();
	wp_register_script( 
		'pto_form_upload', 
		PTO_PLUGIN_URL  . '/includes/scripts/upload/form_upload.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_client_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-client-styles.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_dashboard_font', 
		'https://fonts.googleapis.com/css2?family=' . $font . ':ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap',
		array(), 
		PTO_VERSION,
		'all'
	);
	wp_register_style( 
		'pto_client_fe_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-client-styles-fe.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_client_inc_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-client-styles-inc.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_dash_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/dashboard_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_bugs_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/bugs_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_client_messaging', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/messaging_client_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-dialog' ), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_quote_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/quote_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_register_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/register_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_project_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/project_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_tasks_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/tasks_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_tickets_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/tickets_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_fe_subs_custom', 
		PTO_PLUGIN_URL  . '/includes/scripts/frontend/subs_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION, 
		'all' 
	);
	//Add select2 styles CDN.
	wp_register_style( 
		'pto_select2_styles', 
		PTO_ASSETS_URL  . 'css/select2.min.css',
		array(), 
		PTO_VERSION,
		'all' 
	);
	//Add select2 scripts CDN.
	wp_register_script( 
		'pto_select2_scripts',
		PTO_ASSETS_URL  . 'js/select2.min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);

	wp_register_style( 
		'pto_fe_lead_form', 
		PTO_PLUGIN_URL  . '/includes/css/pto-fe-lead-form.css',
		array(), 
		PTO_VERSION,
		'all' 
	);

}
add_action( 'admin_enqueue_scripts', 'pto_register_all_admin_scripts' );
function pto_register_all_admin_scripts(){
	wp_register_style( 
		'pto_admin_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-admin-styles.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script( 
		'pto_options',
		PTO_PLUGIN_URL  . '/includes/scripts/options/plugin_options.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_client_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/client/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_general',
		PTO_PLUGIN_URL  . '/includes/scripts/options/admin_general.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_dash',
		PTO_PLUGIN_URL  . '/includes/scripts/admin/dash_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_messaging',
		PTO_PLUGIN_URL  . '/includes/scripts/admin/messaging_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-dialog' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_permissions',
		PTO_PLUGIN_URL  . '/includes/scripts/admin/permissions_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_admin_tickets',
		PTO_PLUGIN_URL  . '/includes/scripts/tickets/tickets_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_quotes_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/quote/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_quotes_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/quote/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_forms_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/forms/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_leads_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/leads/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_leadforms_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/leadforms/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_fields_general',
		PTO_PLUGIN_URL  . '/includes/scripts/options/fields_general.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_invoice_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/invoice/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_invoice_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/invoice/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_project_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/project/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_project_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/project/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_tasks_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/tasks/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_bugs_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/bugs/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_tasks_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/tasks/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_templates_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/templates/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_suppliers_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/suppliers/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_suppliers_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/suppliers/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_expenses_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/expenses/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_expenses_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/expenses/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_subscriptions_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/subscriptions/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_subscriptions_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/subscriptions/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_plans_custom',
		PTO_PLUGIN_URL  . '/includes/scripts/plans/admin_custom.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_plans_ajax',
		PTO_PLUGIN_URL  . '/includes/scripts/plans/admin_ajax.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_repeater', 
		PTO_ASSETS_URL  . 'js/jquery.repeater.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_charts', 
		PTO_ASSETS_URL  . 'js/Chart.bundle.min.js',
		array(), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_fullcal',
		PTO_PLUGIN_URL  . '/assets/fullcalendar/packages/core/main.min.js',
		array( 'jquery' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_fullcal_daygrid',
		PTO_PLUGIN_URL  . '/assets/fullcalendar/packages/daygrid/main.min.js',
		array( 'jquery' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_style( 
		'pto_fullcal_styles', 
		PTO_PLUGIN_URL  . '/assets/fullcalendar/packages/core/main.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_style( 
		'pto_fullcal_daygrid_styles', 
		PTO_PLUGIN_URL  . '/assets/fullcalendar/packages/daygrid/main.min.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);
	wp_register_script(
		'pto_fullcal_locale',
		PTO_PLUGIN_URL  . '/assets/fullcalendar/packages/core/locales/' . strtolower( str_replace( '_', '-', get_locale() ) ) . '.js',
		array( 'jquery' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_formbuilder',
		PTO_PLUGIN_URL . '/assets/formbuilder/assets/js/form-builder.min.js',
		array( 'jquery' ),
		PTO_VERSION,
		TRUE
	);
	wp_register_script( 
		'pto_formbuilder_render',
		PTO_PLUGIN_URL . '/assets/formbuilder/assets/js/form-render.min.js',
		array( 'jquery' ),
		PTO_VERSION,
		TRUE
	);
	// wp_register_style( 
	// 	'pto_formbuilder_styles', 
	// 	PTO_PLUGIN_URL . '/assets/formbuilder/assets/css/form-builder.min.css',
	// 	array(), 
	// 	PTO_VERSION,
	// 	'all' 
	// );

	//Add select2 styles CDN.
	wp_register_style( 
		'pto_select2_styles', 
		PTO_ASSETS_URL  . 'css/select2.min.css',
		array(), 
		PTO_VERSION,
		'all' 
	);
	//Add select2 scripts CDN.
	wp_register_script( 
		'pto_select2_scripts',
		PTO_ASSETS_URL  . 'js/select2.min.js',
		array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
		PTO_VERSION,
		TRUE
	);
}
add_action( 'wp_enqueue_scripts', 'pto_enqueue_scripts_where_required', 99 );
function pto_enqueue_scripts_where_required() {
	$login_page = get_option('cqpim_login_page');
	$dash_page = get_option('cqpim_client_page');
	$reset_page = get_option('cqpim_reset_page');
	$register_page = get_option('cqpim_register_page');
	if ( is_page($login_page) || is_page($dash_page) || is_page($reset_page) || is_page($register_page) ) {
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_form_upload');
		wp_localize_script('pto_form_upload', 'localisation', pto_return_localisation());
	}
	if ( is_page($dash_page) ) {
		wp_enqueue_script('pto_fe_register_custom');
		wp_localize_script('pto_fe_register_custom', 'localisation', pto_return_localisation());
		wp_enqueue_script('pto_upload_avatar');
		wp_localize_script('pto_upload_avatar', 'localisation', pto_return_localisation()); 
	}
	if ( is_singular('cqpim_quote') ) {
		pto_enqueue_all_frontend(); 
		wp_enqueue_script('pto_fe_quote_custom');
		wp_localize_script('pto_fe_quote_custom', 'localisation', pto_return_localisation());   
	}
	if ( is_singular('cqpim_project') ) {
		pto_enqueue_all_frontend(); 
		wp_enqueue_script('pto_fe_project_custom');
		// Enqueue select2 cdn.
		wp_enqueue_script('pto_select2_scripts');
		wp_enqueue_style('pto_select2_styles');
		wp_localize_script('pto_fe_project_custom', 'localisation', pto_return_localisation()); 
		if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
			wp_enqueue_script('pto_fe_bugs_custom');
			wp_localize_script('pto_fe_bugs_custom', 'localisation', pto_return_localisation());
		}
	}
	if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
		if ( is_singular('cqpim_bug') ) {
			pto_enqueue_all_frontend(); 
			wp_enqueue_script('pto_fe_bugs_custom');
			wp_localize_script('pto_fe_bugs_custom', 'localisation', pto_return_localisation());
		}
	}
	if ( is_singular('cqpim_tasks') ) {
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_fe_tasks_custom');
		wp_localize_script('pto_fe_tasks_custom', 'localisation', pto_return_localisation());   
	}
	if ( is_singular('cqpim_support') ) {
		pto_enqueue_all_frontend();
		wp_enqueue_script('pto_fe_tickets_custom');
		wp_localize_script('pto_fe_tickets_custom', 'localisation', pto_return_localisation()); 
	}
	if ( is_singular('cqpim_invoice') ) {
		pto_enqueue_all_frontend();
	}
	if ( is_singular('cqpim_faq') ) {
		pto_enqueue_all_frontend();
	}
	if ( is_singular('cqpim_subscription') ) {
		pto_enqueue_all_frontend(); 
		wp_enqueue_script('pto_fe_subs_custom');
		wp_localize_script('pto_fe_subs_custom', 'localisation', pto_return_localisation());
	}
}
function pto_enqueue_all_frontend() {
	$theme = get_option('client_dashboard_type');
	if ( $theme == 'inc' ) {
		$user = wp_get_current_user();
		$login_page = get_option('cqpim_login_page');
		$dash_page = get_option('cqpim_client_page');
		$reset_page = get_option('cqpim_reset_page');
		$register_page = get_option('cqpim_register_page');
		if ( is_singular('cqpim_project') || is_singular('cqpim_quote') || is_singular('cqpim_invoice') || is_singular('cqpim_subscription') || is_singular('cqpim_bug') || is_singular('cqpim_support') || is_singular('cqpim_tasks') || is_singular('cqpim_faq') && in_array('cqpim_client', $user->roles) || is_page($login_page) || is_page($dash_page) || is_page($reset_page) || is_page($register_page) ) {
			global $wp_styles;
			foreach ( $wp_styles->queue as $handle ) {
				wp_dequeue_style($handle);
			}
			global $wp_scripts;
			foreach ( $wp_scripts->queue as $handle ) {
				wp_dequeue_script($handle);
			}
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery');
			wp_enqueue_style('pto_client_inc_styles');
			wp_enqueue_style('pto_dashboard_font');
		}
	} else {
		wp_enqueue_style('pto_client_fe_styles');
	}
	do_action('pto_enqueue_all_frontend', $theme);

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_style('pto_jquery_ui_styles');
	wp_enqueue_script('pto_colorbox');
	wp_enqueue_style('pto_colorbox_styles');
	wp_enqueue_script('pto_masonry');
	wp_enqueue_script('pto_datatables');
	wp_enqueue_style('pto_datatables_styles');
	wp_enqueue_style('pto_fontawesome');
	wp_enqueue_script('pto_tokeninput');
	wp_enqueue_script('pto_fe_dash_custom');
	wp_enqueue_script('pto_stripe_js');
	$stripe = get_option('client_invoice_stripe_ideal');
	if ( ! empty( $stripe ) ) {
		wp_enqueue_script( 'pto_stripe_ideal' );
	}
	wp_localize_script('pto_fe_dash_custom', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_multi_upload');
	wp_localize_script('pto_multi_upload', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_client_messaging');
	wp_localize_script('pto_client_messaging', 'localisation', pto_return_localisation());
	wp_enqueue_style('pto_autocomplete_styles');
	pto_enqueue_google_recaptch_script();
}
add_action( 'admin_enqueue_scripts', 'pto_enqueue_admin_js', 25 );
function pto_enqueue_admin_js() {
	global $post_type;
	switch ( $post_type ) { 
		case 'cqpim_tasks':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_tasks_custom');
			wp_localize_script('pto_tasks_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_tasks_ajax');
			wp_localize_script('pto_tasks_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_forms':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_formbuilder');
			wp_enqueue_script('pto_formbuilder_render');
			//wp_enqueue_style('pto_formbuilder_styles');
			wp_enqueue_script('pto_forms_ajax');
			wp_localize_script('pto_forms_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_client':
			pto_enqueue_all_admin();
			break;
		case 'cqpim_lead':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_leads_ajax');
			wp_localize_script('pto_leads_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_leadform':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_formbuilder');
			wp_enqueue_script('pto_formbuilder_render');
			//wp_enqueue_style('pto_formbuilder_styles');
			wp_enqueue_script('pto_leadforms_ajax');
			wp_localize_script('pto_leadforms_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_teams':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_charts');
			wp_enqueue_script('pto_fullcal');
			wp_enqueue_script('pto_fullcal_daygrid');
			wp_enqueue_script('pto_fullcal_locale');
			wp_enqueue_style('pto_fullcal_styles');
			break;
		case 'cqpim_terms':
			pto_enqueue_all_admin();
			break;
		case 'cqpim_faq':
			pto_enqueue_all_admin();
			break;
		case 'cqpim_bug':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_bugs_custom');
			wp_localize_script('pto_bugs_custom', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_quote':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_quotes_custom');
			wp_localize_script('pto_quotes_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_quotes_ajax');
			wp_localize_script('pto_quotes_ajax', 'localisation', pto_return_localisation());
			break;  
		case 'cqpim_templates':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_templates_ajax');
			wp_localize_script('pto_templates_ajax', 'localisation', pto_return_localisation());
			break;  
		case 'cqpim_project':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_project_custom');
			// Enqueue select2 cdn.
			wp_enqueue_script('pto_select2_scripts');
			wp_enqueue_style('pto_select2_styles');
			wp_localize_script('pto_project_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_project_ajax');
			wp_localize_script('pto_project_ajax', 'localisation', pto_return_localisation());
			if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
				wp_enqueue_script('pto_bugs_custom');
				wp_localize_script('pto_bugs_custom', 'localisation', pto_return_localisation());
			}
			break;  
		case 'cqpim_invoice':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_invoice_custom');
			wp_localize_script('pto_invoice_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_invoice_ajax');
			wp_localize_script('pto_invoice_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_support':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_admin_tickets');
			wp_localize_script('pto_admin_tickets', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_supplier':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_charts');
			wp_enqueue_script('pto_suppliers_custom');
			wp_localize_script('pto_suppliers_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_suppliers_ajax');
			wp_localize_script('pto_suppliers_ajax', 'localisation', pto_return_localisation());
			break;  
		case 'cqpim_expense':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_expenses_custom');
			wp_localize_script('pto_expenses_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_expenses_ajax');
			wp_localize_script('pto_expenses_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_subscription':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_subscriptions_custom');
			wp_localize_script('pto_subscriptions_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_subscriptions_ajax');
			wp_localize_script('pto_subscriptions_ajax', 'localisation', pto_return_localisation());
			break;
		case 'cqpim_plan':
			pto_enqueue_all_admin();
			wp_enqueue_script('pto_plans_custom');
			wp_localize_script('pto_plans_custom', 'localisation', pto_return_localisation());
			wp_enqueue_script('pto_plans_ajax');
			wp_localize_script('pto_plans_ajax', 'localisation', pto_return_localisation());
			break;              
		default:
			break;
	}
}
function pto_enqueue_all_admin() {
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'pto_colorbox' );
	wp_enqueue_style( 'wp-color-picker');
	wp_enqueue_script( 'wp-color-picker');
	wp_enqueue_script('pto_admin_general');
	wp_localize_script('pto_admin_general', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_client_custom');
	wp_localize_script('pto_client_custom', 'localisation', pto_return_localisation());
	wp_enqueue_script('pto_multi_upload');
	wp_localize_script('pto_multi_upload', 'localisation', pto_return_localisation());
	//wp_enqueue_script('pto_datatables');
	wp_enqueue_script('pto_repeater');
	//wp_enqueue_style('pto_datatables_styles');
	wp_enqueue_style('pto_fontawesome');
	wp_enqueue_style('pto_admin_styles');
	wp_enqueue_style('pto_jquery_ui_styles');
	wp_enqueue_style('pto_colorbox_styles');    
	wp_enqueue_script('pto_timer');
	wp_enqueue_script('pto_equalheights');
}

add_action( 'admin_enqueue_scripts', function() {
	if ( ! check_is_pto_plugin() ) { 
		return;
	}

	wp_enqueue_style( 'bootstrap-css', PTO_ASSETS_URL  . 'css/bootstrap.min.css', [], '4.6.0', 'all' );
	wp_enqueue_script( 'bootstrap-scripts', PTO_ASSETS_URL  . 'js/bootstrap.bundle.min.js', [], '4.6.0', false );
	wp_enqueue_style( 'fonts-imports', 'https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap', null , PTO_VERSION , 'all' );
	wp_enqueue_style( 'build_file', PTO_PLUGIN_URL .'/assets/admin/build/pto-main.css', null , PTO_VERSION , 'all' );

	wp_enqueue_style( 'percircle-css', PTO_ASSETS_URL  . 'css/percircle.css', [], '1.0.25', 'all' );
	wp_enqueue_script( 'percircle', PTO_ASSETS_URL  . 'js/percircle.js', [], '1.0.25', false );

	wp_register_script(
		'build_file_scripts',
		PTO_PLUGIN_URL .'/assets/admin/build/js/pto-main.js',
		[ 'jquery' ],
		PTO_VERSION,
		true
	);

	wp_localize_script(
		'build_file_scripts',
		'pto_object',
		array(
			'ajaxURL'        => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'     => wp_create_nonce('pto_nonce'),
			'PTO_PLUGIN_URL' => PTO_PLUGIN_URL,
		)
	);

	wp_enqueue_style( 'b-datatable-css', PTO_ASSETS_URL  . 'css/dataTables.bootstrap4.min.css', [], '1.11.3', 'all' );
	wp_enqueue_script( 'datatables-js', PTO_ASSETS_URL  . 'js/jquery.dataTables.min.js', [ 'jquery' ], '1.11.3', true );
	wp_enqueue_script( 'bootstrap-data-table-scripts', PTO_ASSETS_URL  . 'js/dataTables.bootstrap4.min.js', [ 'datatables-js' ], '1.11.3', true );
	wp_enqueue_script( 'build_file_scripts' );
}, 11 );

function pto_enqueue_plugin_option_scripts() {
	if ( check_is_pto_plugin() ) { 
		add_action( 'admin_enqueue_scripts', 'pto_enqueue_plugin_option_scripts_now' );
	}
}
function pto_enqueue_plugin_option_scripts_now() {
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'wp-color-picker' );
	$scripts = array(
		//'pto_jquery_ui',
		'pto_colorbox',
		'pto_masonry',
		//'pto_datatables',
		'pto_tokeninput',
		'pto_repeater',
		'pto_charts',
		'pto_fullcal_moment',
		'pto_fullcal',
		'pto_fullcal_daygrid',
		'pto_fullcal_locale',
		'pto_formbuilder',
		'pto_formbuilder_render',
		'pto_options',
		'pto_admin_general',
		'pto_admin_dash',
		'pto_admin_messaging',
		'pto_admin_permissions',
		'pto_fields_general',
		'pto_multi_upload',
		'pto_expenses_custom',
		'pto_expenses_ajax',
		'pto_timepicker',
		'pto_upload_avatar',
		'pto_timer',
	);
	pto_custom_enqueue_scripts( $scripts );
	$styles = array(
		'pto_fontawesome',
		'pto_admin_styles',
		'pto_jquery_ui_styles',
		'pto_colorbox_styles',
		//'pto_datatables_styles',
		//'pto_formbuilder_styles',
		'pto_fullcal_styles',
		'pto_fullcal_daygrid_styles',
		'pto_autocomplete_styles',
		'pto_timepicker_styles',
		'wp-color-picker',
	);
	pto_custom_enqueue_styles( $styles );
}

function pto_enqueue_plugin_form_scripts() {
	add_action( 'admin_enqueue_scripts', 'pto_enqueue_plugin_form_scripts_now' );       
}

function pto_enqueue_plugin_form_scripts_now() {
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	$scripts = array(
		//'pto_jquery_ui',
		'pto_formbuilder',
		'pto_formbuilder_render',
		'pto_fields_general',
	);
	pto_custom_enqueue_scripts( $scripts );
	$styles = array(
		'pto_fontawesome',
		'pto_admin_styles',
		'pto_jquery_ui_styles',
		//'pto_formbuilder_styles',
	);
	pto_custom_enqueue_styles( $styles );
}

function pto_custom_enqueue_scripts( $scripts ) {
	if ( ! is_array( $scripts ) ) { return; }
	foreach ( $scripts as $script ) {
		wp_enqueue_script( $script );
		wp_localize_script( $script, 'localisation', pto_return_localisation() );
	}
}
function pto_custom_enqueue_styles( $styles ) {
	if ( ! is_array( $styles ) ) { return; }
	foreach ( $styles as $style ) {
		wp_enqueue_style( $style );
	}
}

/**
 * Function to add admin custom CSS.
 */
function pto_enqueue_admin_custom_css() {
	if ( ! empty( get_option( 'cqpim_admin_dash_css' ) ) ) {
		printf( '<style type="text/css"> %s </style>',
			get_option( 'cqpim_admin_dash_css' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
}
add_action( 'admin_head', 'pto_enqueue_admin_custom_css' );

/**
 * Function to enqueue the steps jquery for multi page intial settings.
 */
function pto_enqueue_steps_jquery() {

	wp_register_style( 
		'pto_intial_steps_styles', 
		PTO_PLUGIN_URL  . '/includes/css/pto-admin-initial-settings.css',
		array(), 
		PTO_VERSION, 
		'all' 
	);

	wp_register_script( 
		'pto_jquery_steps', 
		PTO_ASSETS_URL  . 'js/jquery.steps.min.js',
		array(), 
		PTO_VERSION, 
		'all'
	);
	wp_register_script( 
		'pto_jquery_validate', 
		PTO_ASSETS_URL  . 'js/jquery.validate.min.js',
		array(), 
		PTO_VERSION, 
		'all'
	);

	// Enqueue if intial settings steps are not done yet.
	if ( 1 == get_option( 'pto_run_setup_wizard' ) ) {
		wp_enqueue_style('pto_intial_steps_styles');
		wp_enqueue_script('pto_jquery_steps');
		wp_enqueue_script('pto_jquery_validate');
	}
}
add_action('admin_enqueue_scripts', 'pto_enqueue_steps_jquery');

/**
 * Function to enqueue the google recaptcha api.
 */
function pto_enqueue_google_recaptch_script() {
	wp_enqueue_script( 'jquery' );
	if ( empty( get_option('pto_frontend_form_google_recaptcha') ) ) {
		return;
	}
	wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), get_bloginfo( 'version' ), false );
}
add_action( 'wp_enqueue_scripts', 'pto_enqueue_google_recaptch_script', 1 );