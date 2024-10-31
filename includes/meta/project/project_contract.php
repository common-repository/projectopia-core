<?php
function pto_project_contract_metabox_callback( $post ) {
	wp_nonce_field( 'project_contract_metabox', 'project_contract_metabox_nonce' );
	$contract_status = get_post_meta($post->ID, 'contract_status', true); ?>

	<div class="form-group mb-0">
		<div class="input-group">
			<select name="contract_status" class="form-control input customSelect">
				<option value="0"><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
				<option value="1" <?php selected($contract_status, 1, true); ?>><?php esc_html_e('Enabled', 'projectopia-core'); ?></option>
				<option value="2" <?php selected($contract_status, 2, true); ?>><?php esc_html_e('Disabled', 'projectopia-core'); ?></option>
			</select>
		</div>
	</div>
<?php }

add_action( 'save_post_cqpim_project', 'save_pto_project_contract_metabox_data' );
function save_pto_project_contract_metabox_data( $post_id ) {
	if ( ! isset( $_POST['project_contract_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['project_contract_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'project_contract_metabox' ) ) {
	    return $post_id;
	}
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( isset( $_POST['contract_status'] ) ) {
		update_post_meta( $post_id, 'contract_status', sanitize_text_field( wp_unslash( $_POST['contract_status'] ) ) );
	}
}