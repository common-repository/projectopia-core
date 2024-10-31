<?php
// Contact Details Metabox
function lead_pto_update_metabox_callback( $post ) {
	// Add an nonce field so we can check for it later.
 	wp_nonce_field( 'lead_update_metabox', 'lead_update_metabox_nonce' ); ?>

	<a class="save piaBtn btn btn-primary btn-block my-2" href="#"><?php esc_html_e( 'Update Lead', 'projectopia-core' ); ?></a>
	<?php if ( current_user_can( 'delete_cqpim_suppliers' ) ) { ?>
		<a class="delete piaBtn btn btn-primary btn-block redColor mt-0" href="<?php echo esc_url( get_delete_post_link($post->ID) ); ?>"><?php esc_html_e( 'Delete Lead', 'projectopia-core' ); ?></a>
	<?php }
}