<?php
register_activation_hook( PTO_FILE, 'pto_activation' );
function pto_activation() {
	if ( is_plugin_active( 'cqpim/cqpim.php' ) ) {
		$message = __( 'You must deactivate and remove the old version of CQPIM Core before installing this one. Make sure you make a backup of any language translation files so that you can reinstate them', 'projectopia-core' );
		wp_die( esc_html( $message ), 'Projectopia Core Plugin Version Problem' );
	}

	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/pto-uploads';
	if ( ! is_dir( $upload_dir ) ) {
	   	mkdir( $upload_dir, 0775 );
	}

	wp_schedule_event( time(), 'hourly', 'pto_check_recurring_invoices' );
	wp_schedule_event( time(), 'every_minute', 'pto_check_email_pipe' );

	$role = get_role( 'cqpim_client' );
	if ( empty( $role ) ) {
		$result = add_role( 'cqpim_client', __( 'Projectopia Client', 'projectopia-core' ),
			array(
				'read'                        => true,
				'read_cqpim_quote'            => true,
				'read_cqpim_project'          => true,
				'read_cqpim_support'          => true,
				'read_cqpim_faq'              => true,
				'read_private_cqpim_quotes'   => true,
				'read_private_cqpim_projects' => true,
				'read_private_cqpim_supports' => true,
				'read_private_cqpim_faqs'     => true,
				'upload_files'                => true,
			)
		);
	}

	$role = get_role( 'cqpim_admin' );
	if ( empty( $role ) ) {      
		$result = add_role( 'cqpim_admin', __( 'Projectopia Admin', 'projectopia-core' ),
			array(
				'read'         => true,
				'upload_files' => true,
			)
		);
		$cqpim_roles = array( 'cqpim_admin' );
		update_option( 'cqpim_roles', $cqpim_roles );
	}

	$role = get_role( 'ptouploader' );
	if ( empty( $role ) ) {      
		$result = add_role( 'ptouploader', __( 'Projectopia Uploader', 'projectopia-core' ),
			array(
				'read'         => true,
				'upload_files' => true,
			)
		);
	}

	$page_info = [
		[
			'title'     => 'Client Login',
			'post_name' => 'client-login',
			'option'    => 'cqpim_login_page',
		],
		[
			'title'     => 'Client Dashboard',
			'post_name' => 'client-dashboard',
			'option'    => 'cqpim_client_page',
		],
		[
			'title'     => 'Client Register',
			'post_name' => 'client-register',
			'option'    => 'cqpim_register_page',
		],
		[
			'title'     => 'Password Reset',
			'post_name' => 'client-reset',
			'option'    => 'cqpim_reset_page',
		],
	];

	foreach ( $page_info as $single_page ) {
		$page_check = get_page_by_title( $single_page['title'] );
		if ( ! $page_check ) {
			$args = [
				'meta_key'       => 'pto_template_type',
				'meta_value'     => $single_page['option'],
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => 1,
			];
			$posts = get_posts( $args );
			if ( ! $posts ) {
				$new_page = array(
					'post_type'      => 'page',
					'post_title'     => $single_page['title'],
					'post_content'   => '',
					'post_excerpt'   => '',
					'post_status'    => 'publish',
					'post_author'    => 1,
					'post_name'      => $single_page['post_name'],
					'comment_status' => 'closed',
				);
				$new_page_id = wp_insert_post( $new_page );
				update_option( $single_page['option'], $new_page_id );
				add_post_meta( $new_page_id, 'pto_template_type', $single_page['option'] );
			} else {
				update_option( $single_page['option'], $posts[0]->ID ); 
				update_post_meta( $posts[0]->ID, 'pto_template_type', $single_page['option'] );
			}
		} else {
			update_option( $single_page['option'], $page_check->ID ); 
			update_post_meta( $page_check->ID, 'pto_template_type', $single_page['option'] );
		}
	}

	update_option( 'client_dashboard_type', 'inc' );

	/**
	 * Flag for the intial setup wizard and it has three states.
	 * Empty as not create, 1 as running wizard and 2 as wizard setup done.
	 */
	if ( empty( get_option( 'pto_run_setup_wizard' ) ) ) {
		update_option( 'pto_run_setup_wizard', 1 );
	}

	$role = get_role( 'administrator' );
	if ( ! empty( $role ) ) {
		$role->add_cap( 'cqpim_view_dashboard' );
	}

	require_once( PTO_PATH . '/includes/cpt/cpt.php' );
	flush_rewrite_rules();
}

add_action( 'admin_init', 'pto_anon_upload' );
function pto_anon_upload() {
	if ( ! username_exists( 'ptouploader' ) ) {
		$userdata = array(
			'user_login' => 'ptouploader',
			'user_pass'  => pto_random_string(),
			'role'       => 'ptouploader',
		);
		$user_id = wp_insert_user( $userdata ); 
		$user = get_user_by('id', $user_id);
	}
}

add_action( 'wp', 'pto_signon_uploader', 10 );
function pto_signon_uploader() {    
	$form_page = get_option( 'cqpim_form_page' );
	if ( empty( $form_page ) ) {
		update_option( 'cqpim_form_page', 1000000, true );
	}

	global $post;
	if ( ! empty( $post->ID ) && $post->ID == $form_page || ! empty( $_SERVER['HTTP_REFERER'] ) && $_SERVER['HTTP_REFERER'] == get_the_permalink( $form_page ) && isset( $_SERVER['PHP_SELF'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ), '/async-upload.php' ) === false ) {
		$pass = pto_random_string();
		if ( ! is_user_logged_in() ) {
			$username = 'ptouploader';
			$user = get_user_by( 'login', $username );
			wp_set_password( $pass, $user->ID );
			$creds = array();
			$creds['user_login'] = $username;
			$creds['user_password'] = $pass;
			$creds['remember'] = false;
			$signon = wp_signon( $creds, is_ssl() );
			wp_safe_redirect( get_the_permalink( $post->ID ) );
		}   
	} else {
		$user = wp_get_current_user();
		if ( ! empty( $user->roles ) && in_array( 'ptouploader', $user->roles ) ) {
			wp_logout();
			wp_safe_redirect( get_the_permalink( $post->ID ) );
		}
	}
}