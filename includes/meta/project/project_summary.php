<?php
/**
 * Function to show the project summary editor in meta box.
 * 
 * @param Object $post This is an object.
 * 
 * @return void
 */
function pto_project_summary_metabox_callback( $post ) {
	$meta = get_post_meta( $post->ID, 'project_details', true );
	wp_nonce_field( 'project_summary_metabox', 'project_summary_metabox_nonce' );

	$quote_details = get_post_meta($post->ID, 'project_details', true);
	$quote_summary = isset( $quote_details['project_summary'] ) ? $quote_details['project_summary'] : '';

	$content = '';
	if ( $quote_summary ) {
		$content = $quote_summary;
	}

	$editor_id = 'projectsummary';
	$settings  = array(
		'textarea_name' => 'project_summary',
		'textarea_rows' => 12,
		'media_buttons' => FALSE,
		'tinymce'       => true,
	);
	
	do_action( 'pto_before_project_brief_editor', $post );

	if ( current_user_can( 'cqpim_edit_project_brief' ) ) {
		wp_editor( $content, $editor_id, $settings );
	} else {
		echo wp_kses_post( wpautop( $content ) );
	}

	do_action( 'pto_after_project_brief_editor', $post );

	echo '<div class="clear"></div>';
}

add_action( 'save_post_cqpim_project', 'save_pto_project_summary_metabox_data' );
/**
 * Function to save the project summary on project update.
 * 
 * @param int $post_id  This is project id.
 * 
 * @return int|void
 */
function save_pto_project_summary_metabox_data( $post_id ) {
	if ( ! isset( $_POST['project_summary_metabox_nonce'] ) ) {
		return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['project_summary_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'project_summary_metabox' ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	} 

	if ( isset( $_POST['project_summary'] ) ) {
		$quote_details = get_post_meta( $post_id, 'project_details', true );
		$quote_details = $quote_details && is_array( $quote_details ) ? $quote_details : array();
		$quote_summary = isset( $_POST['project_summary'] ) ? wp_kses_post( wp_unslash( $_POST['project_summary'] ) ) : '';
		$quote_details['project_summary'] = $quote_summary;
		update_post_meta( $post_id, 'project_details', $quote_details );
	}
}
