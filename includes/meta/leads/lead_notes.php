<?php
function pto_lead_notes_metabox_callback( $post ) {
 	wp_nonce_field( 'lead_notes_metabox', 'lead_notes_metabox_nonce' );
	 
	$lead_summary = get_post_meta( $post->ID, 'lead_notes', true );
	$content = '';
	if ( $lead_summary ) {
		$content = $lead_summary;
	}
	$editor_id = 'leadnotes';
	$settings  = array(
		'textarea_name' => 'lead_notes',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce'       => true,
	);
	wp_editor( $content, $editor_id, $settings );
}

add_action( 'save_post_cqpim_lead', 'save_pto_lead_notes_metabox_data' );
function save_pto_lead_notes_metabox_data( $post_id ) {
	if ( ! isset( $_POST['lead_notes_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['lead_notes_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'lead_notes_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( isset( $_POST['lead_notes'] ) ) {
		update_post_meta( $post_id, 'lead_notes', wp_kses_post( wp_unslash( $_POST['lead_notes'] ) ) );
	}
}