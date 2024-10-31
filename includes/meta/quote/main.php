<?php
add_action( 'add_meta_boxes_cqpim_quote', 'add_pto_quote_cpt_metaboxes' );
function add_pto_quote_cpt_metaboxes( $post ) {
	add_meta_box( 
		'quote_status', 
		__('Quote / Estimate Status', 'projectopia-core'),
		'pto_quote_status_metabox_callback', 
		'cqpim_quote',
		'side'
	);
	add_meta_box( 
		'quote_client_details', 
		__('Quote / Estimate Details', 'projectopia-core'), 
		'pto_quote_client_metabox_callback', 
		'cqpim_quote',
		'side',
		'high'
	);
	$setting = get_option('allow_quote_currency_override');
	if ( $setting == 1 ) {
		add_meta_box( 
			'quote_currency', 
			__('Quote / Estimate Currency Settings', 'projectopia-core'), 
			'pto_quote_currency_metabox_callback', 
			'cqpim_quote',
			'side'
		);      
	}
	add_meta_box( 
		'quote_details', 
		__('Quote / Estimate Header & Footer', 'projectopia-core'),
		'pto_quote_details_metabox_callback', 
		'cqpim_quote',
		'normal',
		'low'
	);
	add_meta_box( 
		'quote_elements', 
		__('Milestones & Tasks', 'projectopia-core'),
		'pto_quote_elements_metabox_callback', 
		'cqpim_quote',
		'normal'
	);
	add_meta_box( 
		'quote_summary', 
		__('Project Brief', 'projectopia-core'),
		'pto_quote_summary_metabox_callback', 
		'cqpim_quote',
		'normal',
		'high'
	);
	add_meta_box( 
		'quote_files', 
		__('Quote / Estimate Files', 'projectopia-core'), 
		'pto_quote_files_metabox_callback', 
		'cqpim_quote',
		'normal',
		'high'
	);
	// Add quote messaging metabox.
	add_meta_box(
		'quote_messages', 
		__('Quote Messages', 'projectopia-core'), 
		'pto_quote_messages_metabox_callback', 
		'cqpim_quote',
		'normal'
	);
	if ( ! current_user_can('publish_cqpim_quotes') ) {
		remove_meta_box( 'submitdiv', 'cqpim_quote', 'side' );
	}
}
require_once('quote_details.php');
require_once('quote_currency.php');
require_once('quote_summary.php');
require_once('files.php');
require_once('quote_header.php');
require_once('quote_status.php');
require_once('quote_milestones.php');
require_once('quote_messages.php');