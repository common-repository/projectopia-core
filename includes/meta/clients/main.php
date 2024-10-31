<?php
add_action( 'add_meta_boxes_cqpim_client', 'add_pto_contact_cpt_metaboxes' );
function add_pto_contact_cpt_metaboxes( $post ) {
	add_meta_box( 
		'contact_details', 
		__('Client Details', 'projectopia-core'),
		'pto_contact_details_metabox_callback', 
		'cqpim_client', 
		'side',
		'high'
	);
	add_meta_box( 
		'contact_prefs', 
		__('Contact Preferences', 'projectopia-core'),
		'pto_contact_prefs_metabox_callback', 
		'cqpim_client', 
		'side',
		'high'
	);
	$contracts = get_option('enable_project_contracts');
	if ( ! empty($contracts) ) {
		add_meta_box( 
			'client_contracts', 
			__('Contract Settings', 'projectopia-core'),
			'pto_contract_settings_metabox_callback', 
			'cqpim_client', 
			'side'
		);
	}
	add_meta_box( 
		'client_notes', 
		__('Client Notes', 'projectopia-core'),
		'pto_client_notes_metabox_callback', 
		'cqpim_client', 
		'normal'
	);
	$data = get_option( 'cqpim_custom_fields_client' );
	if ( ! empty( $data ) ) {
		$form_data = json_decode( $data );
		$fields = $form_data;
	}
	if ( ! empty( $fields ) ) {
		add_meta_box( 
			'client_fields', 
			__('Custom Fields', 'projectopia-core'),
			'pto_client_fields_metabox_callback', 
			'cqpim_client', 
			'normal'
		);              
	}
	add_meta_box( 
		'client_alerts', 
		__('Client Alerts', 'projectopia-core'),
		'pto_client_alerts_metabox_callback', 
		'cqpim_client', 
		'normal'
	);
	add_meta_box( 
		'client_files', 
		__('Client Files', 'projectopia-core'),
		'pto_client_files_metabox_callback', 
		'cqpim_client', 
		'normal'
	);
	add_meta_box( 
		'client_team', 
		__('Client Contacts', 'projectopia-core'),
		'pto_client_team_metabox_callback', 
		'cqpim_client', 
		'normal'
	);
	if ( current_user_can('edit_cqpim_invoices') ) {
		if ( get_option('disable_invoices') != 1 ) {
			add_meta_box( 
				'client_financials', 
				__('Client Financials', 'projectopia-core'), 
				'pto_client_financials_metabox_callback', 
				'cqpim_client', 
				'normal'
			);
			add_meta_box( 
				'client_rec_invoices', 
				__('Recurring Invoices', 'projectopia-core'), 
				'pto_client_recinvoices_metabox_callback', 
				'cqpim_client', 
				'normal'
			);
			add_meta_box( 
				'client_invoices', 
				__('Client Invoices', 'projectopia-core'), 
				'pto_client_invoices_metabox_callback', 
				'cqpim_client', 
				'normal'
			);
		}
	}
	add_meta_box( 
		'client_projects', 
		__('Client Projects', 'projectopia-core'),
		'pto_client_projects_metabox_callback', 
		'cqpim_client', 
		'normal'
	);
	if ( current_user_can('edit_cqpim_supports') ) {
		add_meta_box( 
			'client_tickets', 
			__('Client Tickets', 'projectopia-core'), 
			'pto_client_tickets_metabox_callback', 
			'cqpim_client', 
			'normal'
		);
	}
	add_meta_box( 
		'client_logs', 
		__('Client Logs', 'projectopia-core'),
		'pto_client_logs_metabox_callback', 
		'cqpim_client', 
		'normal',
		'low'
	);
	if ( ! current_user_can('publish_cqpim_clients') ) {
		remove_meta_box( 'submitdiv', 'cqpim_client', 'side' );
	}
}
require_once( 'contact_details.php' );
require_once( 'contact_preferences.php' );
require_once( 'projects.php' );
require_once( 'financials.php' );
require_once( 'rec_invoices.php' );
require_once( 'invoices.php' );
require_once( 'files.php' );
require_once( 'notes.php' );
require_once( 'fields.php' );
require_once( 'alerts.php' );
require_once( 'teams.php' );
require_once( 'tickets.php' );
require_once( 'logs.php' );
require_once( 'contracts.php' );