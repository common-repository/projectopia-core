<?php
add_action( 'add_meta_boxes_cqpim_forms', 'add_pto_forms_cpt_metaboxes' );
function add_pto_forms_cpt_metaboxes( $post ) {
	add_meta_box( 
		'form_builder', 
		__('Form Details', 'projectopia-core'),
		'pto_form_builder_metabox_callback', 
		'cqpim_forms',
		'normal',
		'high'
	);
	add_meta_box( 
		'form_builder_builder', 
		__('Form Builder', 'projectopia-core'),
		'pto_form_builder_builder_metabox_callback', 
		'cqpim_forms',
		'normal'
	);
	if ( ! current_user_can('publish_cqpim_forms') ) {
		remove_meta_box( 'submitdiv', 'cqpim_forms', 'side' );
	}
}
require_once( 'form_details.php' );
require_once( 'form_builder.php' );