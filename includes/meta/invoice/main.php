<?php
add_action( 'add_meta_boxes_cqpim_invoice', 'add_pto_invoice_cpt_metaboxes' );
function add_pto_invoice_cpt_metaboxes( $post ) {
	add_meta_box( 
		'invoice_payments', 
		__('Payments / Deductions', 'projectopia-core'),
		'pto_invoice_payments_metabox_callback', 
		'cqpim_invoice',
		'normal'
	);
	if ( get_option('pto_escrow') == 1 && current_user_can('cqpim_view_escrow') ) {
		add_meta_box( 
			'invoice_pto_escrow', 
			__('Escrow Transaction', 'projectopia-core'),
			'pto_invoice_escrow_metabox_callback', 
			'cqpim_invoice',
			'normal'
		);
	}
	add_meta_box( 
		'invoice_client_project', 
		__('Invoice Details', 'projectopia-core'),
		'pto_invoice_client_project_metabox_callback', 
		'cqpim_invoice',
		'side',
		'high'
	);
	$setting = get_option('allow_invoice_currency_override');
	if ( $setting == 1 ) {
		add_meta_box( 
			'invoice_currency', 
			__('Invoice Currency Settings', 'projectopia-core'), 
			'pto_invoice_currency_metabox_callback', 
			'cqpim_invoice',
			'side',
			'high'
		);      
	}
	add_meta_box( 
		'invoice_line_items', 
		__('Line Items', 'projectopia-core'),
		'pto_invoice_items_metabox_callback', 
		'cqpim_invoice',
		'normal',
		'high'
	);
	$data = get_option('cqpim_custom_fields_invoice');
	if ( ! empty($data) ) {
		$form_data = json_decode($data);
		$fields = $form_data;
	}
	if ( ! empty($fields) ) {
		add_meta_box( 
			'invoice_fields', 
			__('Custom Fields', 'projectopia-core'),
			'pto_invoice_fields_metabox_callback', 
			'cqpim_invoice', 
			'normal'
		);              
	}
	if ( ! current_user_can('publish_cqpim_invoices') ) {
		remove_meta_box( 'submitdiv', 'cqpim_invoice', 'side' );
	}
}
require_once( 'payments.php' );
require_once( 'escrow.php' );
require_once( 'fields.php' );
require_once( 'invoice_currency.php' );
require_once( 'client_details.php' );
require_once( 'line_items.php' );