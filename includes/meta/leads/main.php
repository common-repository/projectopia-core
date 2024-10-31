<?php
add_action( 'add_meta_boxes_cqpim_lead', 'add_pto_lead_cpt_metaboxes' );
function add_pto_lead_cpt_metaboxes( $post ) {
	add_meta_box( 
		'lead_summary', 
		__('Lead Details', 'projectopia-core'),
		'pto_lead_details_metabox_callback', 
		'cqpim_lead',
		'normal',
		'high'
	);
	$type = get_post_meta($post->ID, 'form_type', true);
	if ( ! empty($type) && $type == 'cqpim' || empty($type) || ! empty($type) && $type == 'manual' ) {
		add_meta_box( 
			'lead_files', 
			__('Lead Files', 'projectopia-core'),
			'pto_lead_files_metabox_callback', 
			'cqpim_lead',
			'normal',
			'high'
		);
	}
	add_meta_box( 
		'lead_notes', 
		__('Lead Notes', 'projectopia-core'),
		'pto_lead_notes_metabox_callback', 
		'cqpim_lead',
		'normal',
		''
	);
	add_meta_box( 
		'lead_submitted', 
		__('Submitted On', 'projectopia-core'),
		'pto_lead_submitted_metabox_callback', 
		'cqpim_lead',
		'side',
		'high'
	);
	add_meta_box( 
		'lead_update', 
		__('Update Lead', 'projectopia-core'),
		'lead_pto_update_metabox_callback', 
		'cqpim_lead',
		'side',
		'high'
	);
	if ( ! current_user_can('publish_cqpim_leads') ) {
		remove_meta_box( 'submitdiv', 'cqpim_lead', 'side' );
	}
}
require_once( 'lead_details.php' );
require_once( 'files.php' );
require_once( 'lead_notes.php' );
require_once( 'lead_submitted.php' );
require_once( 'lead_update.php' );