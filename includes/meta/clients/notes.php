<?php
function pto_client_notes_metabox_callback( $post ) {
 	wp_nonce_field( 'client_notes_metabox', 'client_notes_metabox_nonce' );

	$client_details = get_post_meta($post->ID, 'client_details', true);
	$client_notes = isset($client_details['client_notes']) ? $client_details['client_notes'] : '';
	$editor_id = 'clientnotes';
	$settings  = array(
		'textarea_name' => 'clientnotes',
		'textarea_rows' => 10,
		'media_buttons' => false,
		'wpautop'       => false,
	);
	wp_editor( $client_notes, $editor_id, $settings );
}

add_action( 'save_post_cqpim_client', 'save_pto_client_notes_metabox_data' );
function save_pto_client_notes_metabox_data( $post_id ) {
	if ( ! isset( $_POST['client_notes_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['client_notes_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'client_notes_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$client_details = get_post_meta( $post_id, 'client_details', true );
	if ( isset( $_POST['clientnotes'] ) ) {
		$client_details['client_notes'] = wp_kses_post( wp_unslash( $_POST['clientnotes'] ) );
	}   
	update_post_meta( $post_id, 'client_details', $client_details );
}