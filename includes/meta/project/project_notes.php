<?php
/**
 * Function to show the project notes editor in meta box.
 * 
 * @param Object $post This is an object.
 * 
 * @return void
 */
function pto_project_notes_metabox_callback( $post ) {
	wp_nonce_field( 'project_notes_metabox', 'project_notes_metabox_nonce' ); ?>

	<p style="font-size: 1rem;"><?php esc_html_e('This is your personal project notes section. Nobody else can see this, including clients and other team members.', 'projectopia-core'); ?></p>

	<?php
	$project_details = get_post_meta($post->ID, 'project_details', true);
	$user = wp_get_current_user();
	$content = isset($project_details['project_notes'][ $user->ID ]) ? $project_details['project_notes'][ $user->ID ] : '';
	$editor_id = 'projectnotes';

	$settings  = array(
		'textarea_name' => 'project_notes',
		'textarea_rows' => 15,
		'media_buttons' => FALSE,
	);

	wp_editor( $content, $editor_id, $settings );       
}

add_action( 'save_post_cqpim_project', 'save_pto_project_notes_metabox_data' );
/**
 * Function to save the project notes on project update.
 * 
 * @param int $post_id  This is project id.
 * 
 * @return int|void
 */
function save_pto_project_notes_metabox_data( $post_id ) {
	if ( ! isset( $_POST['project_notes_metabox_nonce'] ) ) {
		return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['project_notes_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'project_notes_metabox' ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( isset( $_POST['project_notes'] ) ) {
		$user = wp_get_current_user();
		$project_notes = wp_kses_post( wp_unslash( $_POST['project_notes'] ) );
		$project_details = get_post_meta( $post_id, 'project_details', true );
		$project_details['project_notes'][ $user->ID ] = $project_notes;
		update_post_meta( $post_id, 'project_details', $project_details );
	}
}
