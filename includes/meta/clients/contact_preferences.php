<?php
function pto_contact_prefs_metabox_callback( $post ) {
 	wp_nonce_field( 'contact_prefs_metabox', 'contact_prefs_metabox_nonce' );

	$notifications = get_post_meta( $post->ID, 'client_notifications', true );
	$no_tasks = isset( $notifications['no_tasks'] ) ? $notifications['no_tasks'] : 0;
	$no_tasks_comment = isset( $notifications['no_tasks_comment'] ) ? $notifications['no_tasks_comment'] : 0;
	$no_tickets = isset( $notifications['no_tickets'] ) ? $notifications['no_tickets'] : 0;
	$no_tickets_comment = isset( $notifications['no_tickets_comment'] ) ? $notifications['no_tickets_comment'] : 0;
	$no_bugs = isset( $notifications['no_bugs'] ) ? $notifications['no_bugs'] : 0;
	$no_bugs_comment = isset( $notifications['no_bugs_comment'] ) ? $notifications['no_bugs_comment'] : 0;
	?>
	<p><?php esc_html_e('Email Preferences (Main Contact):', 'projectopia-core'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('By default, clients receive an email notification whenever a task or ticket is updated. You can use these settings to disable those notifications or to limit them to be sent only when a new comment has been added in the task or ticket. You can configure these settings for additional client contacts in the Client Contacts box.', 'projectopia-core'); ?>"></i></p>		
	<?php
	
	echo '<div class="mb-2"><strong>' . esc_html__( 'Tasks:', 'projectopia-core' ) . ' </strong></div>';

	pto_generate_fields( array(
		'type'    => 'checkbox',
		'id'      => 'no_tasks',
		'label'   => __( 'Do not send task update emails', 'projectopia-core' ),
		'checked' => 1 == $no_tasks,
	) );

	pto_generate_fields( array(
		'type'     => 'checkbox',
		'id'       => 'no_tasks_comment',
		'label'    => __( 'Notify new comments only', 'projectopia-core' ),
		'checked'  => 1 == $no_tasks_comment,
		'disabled' => 1 == $no_tasks,
	) );

	echo '<div class="mb-2"><strong>' . esc_html__( 'Tickets:', 'projectopia-core' ) . ' </strong></div>';

	pto_generate_fields( array(
		'type'    => 'checkbox',
		'id'      => 'no_tickets',
		'label'   => __( 'Do not send ticket update emails', 'projectopia-core' ),
		'checked' => 1 == $no_tickets,
	) );

	pto_generate_fields( array(
		'type'     => 'checkbox',
		'id'       => 'no_tickets_comment',
		'label'    => __( 'Notify new comments only', 'projectopia-core' ),
		'checked'  => 1 == $no_tickets_comment,
		'disabled' => 1 == $no_tickets,
	) );

	if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
		echo '<div class="mb-2"><strong>' . esc_html__( 'Bugs:', 'projectopia-core' ) . ' </strong></div>';

		pto_generate_fields( array(
			'type'    => 'checkbox',
			'id'      => 'no_bugs',
			'label'   => __( 'Do not send bug update emails', 'projectopia-core' ),
			'checked' => 1 == $no_bugs,
		) );

		pto_generate_fields( array(
			'type'     => 'checkbox',
			'id'       => 'no_bugs_comment',
			'label'    => __( 'Notify new comments only', 'projectopia-core' ),
			'checked'  => 1 == $no_bugs_comment,
			'disabled' => 1 == $no_bugs,
		) );
	}
}

add_action( 'save_post_cqpim_client', 'save_pto_contact_prefs_metabox_data' );
function save_pto_contact_prefs_metabox_data( $post_id ) {
	if ( ! isset( $_POST['contact_prefs_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['contact_prefs_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'contact_prefs_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$no_tasks = isset($_POST['no_tasks']) ? sanitize_text_field( wp_unslash( $_POST['no_tasks'] ) ) : 0;
	$no_tasks_comment = isset($_POST['no_tasks_comment']) ? sanitize_text_field( wp_unslash( $_POST['no_tasks_comment'] ) ) : 0;
	$no_tickets = isset($_POST['no_tickets']) ? sanitize_text_field( wp_unslash( $_POST['no_tickets'] ) ) : 0;
	$no_tickets_comment = isset($_POST['no_tickets_comment']) ? sanitize_text_field( wp_unslash( $_POST['no_tickets_comment'] ) ) : 0;
	$no_bugs = isset($_POST['no_bugs']) ? sanitize_text_field( wp_unslash( $_POST['no_bugs'] ) ) : 0;
	$no_bugs_comment = isset($_POST['no_bugs_comment']) ? sanitize_text_field( wp_unslash( $_POST['no_bugs_comment'] ) ) : 0;
	$client_notifications = array(
		'no_tasks'           => $no_tasks,
		'no_tasks_comment'   => $no_tasks_comment,
		'no_tickets'         => $no_tickets,
		'no_tickets_comment' => $no_tickets_comment,
		'no_bugs'            => $no_bugs,
		'no_bugs_comment'    => $no_bugs_comment,
	);
	update_post_meta( $post_id, 'client_notifications', $client_notifications );
}