<?php
function pto_leadform_builder_builder_metabox_callback( $post ) {
 	wp_nonce_field( 'leadform_builder_builder_metabox', 'leadform_builder_builder_metabox_nonce' );

	$data = get_post_meta( $post->ID, 'builder_data', true ); 
	$builder = ! empty( $data ) ? $data : '';
	?>
	<script>
		jQuery(document).ready(function() {
			var options = {
				editOnAdd: false,
				fieldRemoveWarn: true,
				disableFields: ['autocomplete', 'button', 'hidden', 'checkbox', 'paragraph'],
				formData : "<?php echo wp_kses_post( str_replace('"', '\"', $builder) ); ?>",
				dataType: 'json',
			};
			d = jQuery('#form_builder_container').formBuilder(options);
			var $fbEditor = jQuery(document.getElementById('form_builder_container'));
			var formBuilder2 = $fbEditor.data('formBuilder');
			jQuery("#form_builder_container").on('click','.save-template',function(e) {
				e.preventDefault();
				dt = d.actions.getData('json');
				jQuery('#builder_data').val(dt);
				jQuery('#publish').click();
			});
		});
	</script>
	<div id="form_builder_container">
	</div>
	<textarea style="display:none" name="builder_data" id="builder_data"><?php if ( ! empty($builder) ) { echo wp_kses_post( $builder ); } else { echo ''; } ?></textarea>
	<?php
}

add_action( 'save_post_cqpim_leadform', 'save_pto_leadform_builder_builder_metabox_data' );
function save_pto_leadform_builder_builder_metabox_data( $post_id ) {
	if ( ! isset( $_POST['leadform_builder_builder_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['leadform_builder_builder_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'leadform_builder_builder_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$builder_data = get_post_meta( $post_id, 'builder_data', true );
	if ( empty( $builder_data ) ) {
		$builder_data = array();
	}

	if ( ! empty( $_POST['builder_data'] ) ) {
		$builder_data = wp_kses_post( wp_unslash( $_POST['builder_data'] ) );
	}

	update_post_meta( $post_id, 'builder_data', wp_slash( $builder_data ) );
}