<?php
function pto_form_builder_metabox_callback( $post ) {
 	wp_nonce_field( 'form_builder_metabox', 'form_builder_metabox_nonce' );

	$type = get_post_meta( $post->ID, 'form_type', true ); ?>

	<h5 class="pt-1"><strong><?php esc_html_e( 'Form Type', 'projectopia-core' ); ?>: </strong><?php $type == 'client_dashboard' ? esc_html_e( 'Client Dashboard Form', 'projectopia-core' ) : esc_html_e( 'Anonymous Frontend Form', 'projectopia-core' ) ?> (<a href="#" id="edit_form_type"><?php esc_html_e( 'Edit', 'projectopia-core' ); ?></a>)</h5>
	<?php if ( $type == 'anonymous_frontend' ) { ?>
		<p><?php esc_html_e( 'The Anonymous Frontend form will include default fields as well as those added here. These are Name, Company Name, Address, Postcode, Telephone and Email. This is so that the plugin can create a client from the submission. Fields created here will be inserted into the Project Brief field in the quote.', 'projectopia-core' ); ?></p>		
	<?php } ?>
	<h5 class="pt-3"><strong><?php esc_html_e( 'Configure this form in the Setting page', 'projectopia-core' ); ?>: </strong> (<a href="<?php echo esc_url( admin_url() . 'admin.php?page=pto-settings#tabs-8' ); ?>" target="_blank"><?php esc_html_e( 'Settings', 'projectopia-core' ); ?></a>)</h5>
	<div id="form_basics_container" style="display: none;">
		<div id="form_basics">
			<div style="padding: 12px;">
				<h3><?php esc_html_e( 'Form Settings', 'projectopia-core' ); ?></h3>
				<?php if ( empty( $type ) ) { ?>
					<p><?php esc_html_e( 'These initial settings will ensure that your form is created correctly with the required minimum fields.', 'projectopia-core' ); ?></p>
					<div class="form-group">
					    <label for="form_title"><?php esc_html_e( 'Form Title:', 'projectopia-core' ); ?></label>
					    <div class="input-group">
							<input type="text" name="form_title" id="form_title" class="form-control input" />
						</div>
					</div>
				<?php } ?><hr>
				<p><?php esc_html_e( 'There are two types of forms, Anonymous Frontend and Client Dashboard. Please refer to the Forms tab in the CQPIM settings for usage.', 'projectopia-core' ); ?></p>
				<div class="form-group">
					<label for="pto-invoice-workflow"><?php esc_html_e( 'Form Type:', 'projectopia-core' ); ?></label>
					<div class="input-group">
						<select id="form_type" name="form_type" class="form-control input full-width">
							<option value=""><?php esc_html_e( 'Choose an option...', 'projectopia-core' ); ?></option>
							<option value="anonymous_frontend" <?php if ( $type == 'anonymous_frontend' ) { echo 'selected'; } ?>><?php esc_html_e( 'Anonymous Frontend Form', 'projectopia-core' ); ?></option>
							<option value="client_dashboard" <?php if ( $type == 'client_dashboard' ) { echo 'selected'; } ?>><?php esc_html_e( 'Client Dashboard Form', 'projectopia-core' ); ?></option>
						</select>
					</div>
				</div>
				<?php if ( ! empty( $type ) ) { ?>
					<p><strong><?php esc_html_e('NOTE:', 'projectopia-core'); ?></strong> <?php esc_html_e('Changing the type of form will revert the fields to a default required set.', 'projectopia-core'); ?></p>
				<?php } ?>
				<div id="basics-error"></div>
				<button class="cancel-creation mt-20 piaBtn redColor"><?php esc_html_e( 'Cancel', 'projectopia-core' ); ?></button>
				<button class="save-basics mt-20 piaBtn right"><?php esc_html_e( 'Save', 'projectopia-core' ); ?></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>			
	<?php	  
}

add_action( 'save_post_cqpim_forms', 'save_pto_form_builder_metabox_data' );
function save_pto_form_builder_metabox_data( $post_id ) {
	if ( ! isset( $_POST['form_builder_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['form_builder_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'form_builder_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$type = get_post_meta( $post_id, 'form_type', true );
	if ( isset( $_POST['form_type'] ) ) {
		if ( isset( $type ) && $type != $_POST['form_type'] ) {
			update_post_meta( $post_id, 'builder_data', '' );
		}
		update_post_meta( $post_id, 'form_type', sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) );
	}

	if ( isset( $_POST['form_title'] ) ) {
		$form_updated = array(
			'ID'         => $post_id,
			'post_title' => sanitize_text_field( wp_unslash( $_POST['form_title'] ) ),
			'post_name'  => $post_id,
		);

		if ( ! wp_is_post_revision( $post_id ) ) {
			remove_action( 'save_post_cqpim_forms', 'save_pto_form_builder_metabox_data' );
			wp_update_post( $form_updated );
			add_action( 'save_post_cqpim_forms', 'save_pto_form_builder_metabox_data' );
		}
	}
}