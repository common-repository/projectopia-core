<?php
function pto_leadform_builder_metabox_callback( $post ) {
 	wp_nonce_field( 'leadform_builder_metabox', 'leadform_builder_metabox_nonce' ); 

	$form_type = get_post_meta( $post->ID, 'form_type', true ); 
	$gravity_form = get_post_meta( $post->ID, 'gravity_form', true ); ?>

	<div class="form-group">
		<label for="builder_type"><?php esc_html_e( 'Form Type', 'projectopia-core' ); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('You can use either the Projectopia Form Builder or link a Gravity Form, if you have Gravity Forms installed.','projectopia-core'); ?>"></i></label>
		<div class="input-group">
			<select name="builder_type" id="builder_type" class="form-control input" required="required">
				<option value=""><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
				<option value="cqpim" <?php selected('cqpim', $form_type); ?>><?php esc_html_e('Use Projectopia Form Builder', 'projectopia-core'); ?></option>
				<?php if ( is_plugin_active('gravityforms/gravityforms.php') ) { ?>
					<option value="gf" <?php selected('gf', $form_type); ?>><?php esc_html_e('Use a Gravity Form', 'projectopia-core'); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<?php if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
		$gfapi = new GFAPI(); 
		$forms = $gfapi->get_forms(); ?>
		<div id="gravity_form_cont"<?php if ( empty($form_type) || ( ! empty( $form_type ) && $form_type == 'projectopia-core' ) ) { ?> style="display: none;"<?php } ?>>
			<div class="form-group">
				<label for="gravity_form"><?php esc_html_e( 'Choose a Gravity Form', 'projectopia-core' ); ?></label>
				<div class="input-group">
					<select name="gravity_form" id="gravity_form" class="form-control input">
						<option value="0"><?php esc_html_e( 'Choose form...', 'projectopia-core' ); ?></option>
						<?php if ( ! empty( $forms ) ) { ?>
							<?php foreach ( $forms as $key => $form ) { ?>
								<option value="<?php echo esc_attr( $form['id'] ); ?>" <?php selected( $form['id'], $gravity_form ); ?>><?php echo esc_html( $form['title'] ); ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if ( ! empty( $post->ID ) ) { ?>
		<div class="form-group">
		    <label for="pto_shortcode"><?php esc_html_e( 'Form Shortcode', 'projectopia-core' ); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('Use this shortcode to embed this form anywhere in your site.','projectopia-core'); ?>"></i></label>
		    <div class="input-group">
			    <input id="pto_shortcode" class="form-control input" type="text" name="cqpim_invoice_slug" value='[projectopia_lead_form id="<?php echo esc_attr( $post->ID ); ?>"]' disabled="disabled" style="cursor: text;" />
			</div>
		</div>
	<?php } ?>
	<button class="save right piaBtn btn btn-primary"><?php esc_html_e('Save Form Details', 'projectopia-core'); ?></button>
	<div class="clear"></div>
	<?php
}

add_action( 'save_post_cqpim_leadform', 'save_pto_leadform_builder_metabox_data' );
function save_pto_leadform_builder_metabox_data( $post_id ) {
	if ( ! isset( $_POST['leadform_builder_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['leadform_builder_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'leadform_builder_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( isset( $_POST['builder_type'] ) ) {
		update_post_meta( $post_id, 'form_type', sanitize_text_field( wp_unslash( $_POST['builder_type'] ) ) );
	}

	if ( isset( $_POST['gravity_form'] ) ) {
		update_post_meta( $post_id, 'gravity_form', sanitize_text_field( wp_unslash( $_POST['gravity_form'] ) ) );
	}
}