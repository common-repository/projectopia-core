<?php
add_action( 'add_meta_boxes_cqpim_leadform', 'add_pto_leadforms_cpt_metaboxes' );
function add_pto_leadforms_cpt_metaboxes( $post ) {
	add_meta_box( 
		'leadform_builder', 
		__('Form Details', 'projectopia-core'),
		'pto_leadform_builder_metabox_callback', 
		'cqpim_leadform',
		'normal',
		'high'
	);
	if ( ! empty($post->ID) && $post->post_type == 'cqpim_leadform' ) {
		$form_type = get_post_meta($post->ID, 'form_type', true);   
		if ( ! empty($form_type) && $form_type == 'cqpim' ) {   
			add_meta_box( 
				'leadform_builder_builder', 
				__('Form Builder', 'projectopia-core'),
				'pto_leadform_builder_builder_metabox_callback', 
				'cqpim_leadform',
				'normal',
				'high'
			);
		}
	}
	if ( ! current_user_can('publish_cqpim_leadforms') ) {
		remove_meta_box( 'submitdiv', 'cqpim_leadform', 'side' );
	}
}
require_once( 'form_details.php' );
require_once( 'form_builder.php' );