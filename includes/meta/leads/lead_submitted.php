<?php
// Contact Details Metabox
function pto_lead_submitted_metabox_callback( $post ) {
 	wp_nonce_field( 'lead_submitted_metabox', 'lead_submitted_metabox_nonce' );

	$submitted = get_post_meta( $post->ID, 'lead_date', true );
	if ( ! empty( $submitted ) ) {
		echo '<h4 style="text-align: center;margin-top: 18px;">' . esc_html( wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $submitted ) ) . '</h4>';
	} else {
		echo '<h4 style="text-align: center;margin-top: 18px;">' . esc_html__( 'Not Published', 'projectopia-core' ) . '</h4>';
	}
}

add_action( 'save_post_cqpim_lead', 'save_pto_lead_submitted_metabox_data' );
function save_pto_lead_submitted_metabox_data( $post_id ) {
	if ( ! isset( $_POST['lead_submitted_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['lead_submitted_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'lead_submitted_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$submitted = get_post_meta( $post_id, 'lead_date', true );
	if ( empty( $submitted ) ) {
		update_post_meta( $post_id, 'lead_date', time() );
	}
}