<?php
function pto_quote_files_metabox_callback( $post ) {
 	wp_nonce_field( 'quote_files_metabox', 'quote_files_metabox_nonce' );

	pto_files_meta_data( $post, 'front_quotefiles' );
}

add_action( 'save_post_cqpim_quote', 'save_pto_quote_files_metabox_data' );
function save_pto_quote_files_metabox_data( $post_id ) {
	if ( ! isset( $_POST['quote_files_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['quote_files_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'quote_files_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$attachments = isset( $_POST['image_id'] ) ? sanitize_text_field( wp_unslash( $_POST['image_id'] ) ) : '';
	if ( ! empty( $attachments ) ) {
		$attachments = explode( ',', $attachments );
		foreach ( $attachments as $attachment ) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, [ 'post_parent' => $post_id ], [ 'ID' => $attachment ] );
			update_post_meta( $attachment, 'cqpim', true );
		}
	}

	if ( isset( $_POST['delete_file'] ) ) {
		$att_to_delete = array_map( 'sanitize_text_field', wp_unslash( $_POST['delete_file'] ) );
		foreach ( $att_to_delete as $key => $attID ) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, [ 'post_parent' => '' ], [ 'ID' => $attID ] );
		}
	}   
}