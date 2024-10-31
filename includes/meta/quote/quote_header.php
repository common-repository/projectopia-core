<?php
function pto_quote_details_metabox_callback( $post ) {
 	wp_nonce_field( 'quote_details_metabox', 'quote_details_metabox_nonce' );

	$quote_details = get_post_meta($post->ID, 'quote_details', true);
	$quote_header = isset($quote_details['quote_header']) ? $quote_details['quote_header'] : '';
	$quote_footer = isset($quote_details['quote_footer']) ? $quote_details['quote_footer'] : '';
	$type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	if ( empty($quote_header) ) {
		$quote_header = get_option( 'quote_header' );
	}
	if ( empty($quote_footer) ) {
		$quote_footer = get_option( 'quote_footer' );
		$current_user_tag = '%%CURRENT_USER%%';
		$current_user = wp_get_current_user();
		if ( ! ($current_user instanceof WP_User) )
			return;
		$current_user = $current_user->display_name;
		$quote_footer = str_replace($current_user_tag, $current_user, $quote_footer);           
	} ?>
	<div class="clear"></div>
	<h5 class="mt-2"><?php esc_html_e('Edit Header', 'projectopia-core'); ?></h5>
	<?php
	$editor_id = 'quote_header';
	$settings  = array(
		'textarea_name' => 'quote_header',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce'       => true,
	);
	wp_editor( $quote_header, $editor_id, $settings ); ?>				
	<div class="clear"></div>
	<h5 class="mt-3"><?php esc_html_e('Edit Footer', 'projectopia-core'); ?></h5>
	<?php
	$editor_id = 'quote_footer';
	$settings  = array(
		'textarea_name' => 'quote_footer',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce'       => true,
	);
	wp_editor( $quote_footer, $editor_id, $settings );
}

add_action( 'save_post_cqpim_quote', 'save_pto_quote_details_metabox_data' );
function save_pto_quote_details_metabox_data( $post_id ) {
	if ( ! isset( $_POST['quote_details_metabox_nonce'] ) ) {
	    return $post_id;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['quote_details_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'quote_details_metabox' ) ) {
	    return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$quote_details = get_post_meta( $post_id, 'quote_details', true );
	if ( isset( $_POST['quote_header'] ) ) {
		$quote_details['quote_header'] = wp_kses_post( wp_unslash( $_POST['quote_header'] ) );
	}
	if ( isset( $_POST['quote_footer'] ) ) {
		$quote_details['quote_footer'] = wp_kses_post( wp_unslash( $_POST['quote_footer'] ) );
	}

	update_post_meta( $post_id, 'quote_details', $quote_details );
}