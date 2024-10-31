<?php
function pto_client_fields_metabox_callback( $post ) {
 	wp_nonce_field( 'client_fields_metabox', 'client_fields_metabox_nonce' );

	$data = get_option( 'cqpim_custom_fields_client' );
	
	pto_get_custom_fields( $data, $post, true );
}

add_action( 'save_post_cqpim_client', 'save_pto_client_fields_metabox_data' );
function save_pto_client_fields_metabox_data( $post_id ) {
	if ( ! isset( $_POST['client_fields_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['client_fields_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'client_fields_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( ! empty( $_POST['custom-field'] ) ) {
		update_post_meta( $post_id, 'custom_fields', pto_sanitize_rec_array( wp_unslash( $_POST['custom-field'] ) ) );  //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
	
	if ( ! empty( $_POST['field_frontend'] ) ) {
		update_post_meta( $post_id, 'field_frontend', array_map( 'sanitize_textarea_field', wp_unslash( $_POST['field_frontend'] ) ) );
	}
}