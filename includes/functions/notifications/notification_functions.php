<?php
function pto_check_unread_team_notifications( $team_id ) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	$notifications = $notifications && is_array($notifications) ? $notifications : array();
	$unread = array();
	foreach ( $notifications as $notification ) {
		if ( empty($notification['read']) ) {
			$unread[] = $notification;
		}
	}
	$notifications = count($unread);
	return $notifications;
}

function pto_check_unread_client_notifications( $team_id ) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	$notifications = $notifications && is_array($notifications) ? $notifications : array();
	$unread = array();
	foreach ( $notifications as $notification ) {
		if ( empty($notification['read']) ) {
			$unread[] = $notification;
		}
	}
	$notifications = count($unread);
	return $notifications;
}

function pto_get_team_notifications( $team_id, $unread = false ) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	if ( ! empty($unread && $unread == true) ) {
		$unread = array();
		foreach ( $notifications as $notification ) {
			if ( empty($notification['read']) ) {
				$unread[] = $notification;
			}
		}
		return $unread;
	} else {
		return $notifications;
	}
}

function pto_get_client_notifications( $team_id, $unread = false ) {
	$notifications = get_post_meta($team_id, 'notifications', true);
	if ( ! empty($unread && $unread == true) ) {
		$unread = array();
		foreach ( $notifications as $notification ) {
			if ( empty($notification['read']) ) {
				$unread[] = $notification;
			}
		}
		return $unread;
	} else {
		return $notifications;
	}
}

function pto_add_team_notification( $team_id, $from, $item, $type, $ctype = '' ) {
	$from = get_user_by('id', $from);
	$item_link = get_edit_post_link($item);
	$item_obj = get_post($item);
	// Team Notifications
	if ( ! empty($type) && $type == 'task' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has updated a task: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'task_assignee' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has assigned a task to you: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'new_ticket' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has raised a new support ticket: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'support_assignee' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has assigned a support ticket to you: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'support_update' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has updated a support ticket: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'new_lead' ) {
		/* translators: %s: Post Title */
		$message = sprintf(esc_html__('A new Lead has been submitted: %s', 'projectopia-core'), $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'bug_assigned' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has assigned a bug to you: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'bug_updated' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has updated a bug: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'project_message' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has sent a message in project: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'quote_accepted' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has accepted a quote: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'contract_accepted' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has confirmed a contract: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'team_project' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has added you to a project: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'subscription_cancelled' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has cancelled a subscription: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'subscription_activated' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has activated a subscription: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'expense_auth' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has requested an expense authorisation: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'expense_approve' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has approved an expense request: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'expense_declined' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has declined an expense request: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'new_quote' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has requested a new quote: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'creg_auth' ) {
		/* translators: %s: Post Title */
		$message = sprintf(esc_html__('A new client has registered, this client needs approval: %s', 'projectopia-core'), $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'creg_noauth' ) {
		/* translators: %s: Post Title */
		$message = sprintf(esc_html__('A new client has registered: %s', 'projectopia-core'), $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'subscription_failed' ) {
		/* translators: %s: Post Title */
		$message = sprintf(esc_html__('A subscription has failed to renew: %s', 'projectopia-core'), $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'subscription_renewed' ) {
		/* translators: %s: Post Title */
		$message = sprintf(esc_html__('A subscription has renewed successfully: %s', 'projectopia-core'), $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'bug_new' ) {
		$project_id = get_post_meta($item_obj->ID, 'bug_project', true);
		$project_obj = get_post($project_id);
		/* translators: %1$s: Sent via Name, %2$s: Post Title, %3$s: Project Title */
		$message = sprintf(esc_html__('%1$s has reported a new bug in project %3$s: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title, $project_obj->post_title);
	}
	// Client Notifications
	if ( ! empty($type) && $type == 'quote_message' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has sent a message in quote: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'quote_sent' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has sent you a new quote: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}   
	if ( ! empty($type) && $type == 'invoice_sent' && isset($from->display_name) ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has sent you a new invoice: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'contract_sent' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has sent you a new contract: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	if ( ! empty($type) && $type == 'subscription_sent' ) {
		/* translators: %1$s: Sent via Name, %2$s: Post Title */
		$message = sprintf(esc_html__('%1$s has sent you a new subscription: %2$s', 'projectopia-core'), $from->display_name, $item_obj->post_title);
	}
	$notifications = get_post_meta($team_id, 'notifications', true);
	$notifications = $notifications && is_array($notifications) ? $notifications : array();
	$notifications[] = array(
		'time'    => time(),
		'item'    => $item_obj->ID,
		'from'    => isset($from->ID) ? $from->ID : '',
		'read'    => 0,
		'message' => $message,
		'type'    => $ctype,
	);
	update_post_meta($team_id, 'notifications', $notifications);

	/**
	 * Create new action on member notification.
	 * @since 4.3.5
	 *
	 * @param int    $team_id Member ID.
	 * @param array  $message Notification message.
	 * @param string $type    Notification type.
	 */
	do_action( 'pto_add_team_notification', $team_id, $message, $type );
}

add_action( "wp_ajax_pto_notifications_remove_nf", "pto_notifications_remove_nf" );
function pto_notifications_remove_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$team = pto_get_team_from_userid($user);
	$notifications = get_post_meta($team, 'notifications', true);
	$notifications = array_reverse($notifications);
	unset($notifications[ $key ]);
	$notifications = array_reverse($notifications);
	update_post_meta($team, 'notifications', $notifications);
	wp_send_json_success( [ 'message' => 'success' ] );

	exit;
}

add_action( "wp_ajax_pto_notifications_client_remove_nf", "pto_notifications_client_remove_nf" );
function pto_notifications_client_remove_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$team = pto_get_client_from_userid($user);
	$notifications = get_post_meta($team['assigned'], 'notifications', true);
	$notifications = array_reverse($notifications);
	unset($notifications[ $key ]);
	$notifications = array_reverse($notifications);
	update_post_meta($team['assigned'], 'notifications', $notifications);
	pto_send_json( array( 
		'error' => false,
	) );
}

add_action( "wp_ajax_pto_notifications_item", "pto_notifications_item" );
function pto_notifications_item() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	if ( empty($item) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('The item ID is missing. Unable to redirect.', 'projectopia-core'),
		) );                 
	} else {
		$team = pto_get_team_from_userid($user);
		$notifications = get_post_meta($team, 'notifications', true);
		$notifications = array_reverse($notifications);
		$notifications[ $key ]['read'] = 1;
		$notifications = array_reverse($notifications);
		update_post_meta($team, 'notifications', $notifications);
		pto_send_json( array( 
			'error'    => false,
			'redirect' => get_edit_post_link($item),
		) );     
	}
	exit;
}

add_action( "wp_ajax_pto_notifications_client_item", "pto_notifications_client_item" );
function pto_notifications_client_item() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$item = isset($_POST['item']) ? sanitize_text_field( wp_unslash( $_POST['item'] ) ) : '';
	$type = isset($_POST['type']) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$key = isset($_POST['key']) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	if ( empty($item) ) {
		pto_send_json( array( 
			'error'   => true,
			'message' => __('The item ID is missing. Unable to redirect.', 'projectopia-core'),
		) );                 
	} else {
		$team = pto_get_client_from_userid($user);
		$team = $team['assigned'];
		$notifications = get_post_meta($team, 'notifications', true);
		$notifications = array_reverse($notifications);
		$notifications[ $key ]['read'] = 1;
		$notifications = array_reverse($notifications);
		update_post_meta($team, 'notifications', $notifications);
		$link = get_the_permalink($item);
		if ( ! empty($type) && $type == 'quote' ) {
			$link = $link . '?pto-page=quote';
		}
		if ( ! empty($type) && $type == 'contract' ) {
			$link = $link . '?pto-page=contract';
		}

		if ( ! empty( $type ) && $type == 'quote_message' ) {
			$link = $link . '?pto-page=quote&sub=messages';
		}

		if ( ! empty( $type ) && $type == 'project_message' ) {
			$link = $link . '?pto-page=summary&sub=messages';
		}
		pto_send_json( array( 
			'error'    => false,
			'redirect' => $link,
		) );     
	}
	exit;
}

add_action( "wp_ajax_pto_clear_all_read_nf", "pto_clear_all_read_nf" );
function pto_clear_all_read_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	$notifications = get_post_meta($team, 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach ( $notifications as $key => $notification ) {
		if ( ! empty($notification['read']) ) {
			unset($notifications[ $key ]);
		}
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team, 'notifications', $notifications);
	wp_send_json_success( [ 'message' => 'success' ] );
	exit;   
}

add_action( "wp_ajax_pto_clear_all_read_client_nf", "pto_clear_all_read_client_nf" );
function pto_clear_all_read_client_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	$notifications = get_post_meta($team['assigned'], 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach ( $notifications as $key => $notification ) {
		if ( ! empty($notification['read']) ) {
			unset($notifications[ $key ]);
		}
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team['assigned'], 'notifications', $notifications);
	pto_send_json( array( 
		'error' => false,
	) ); 
}

add_action( "wp_ajax_pto_mark_all_read_nf", "pto_mark_all_read_nf" );
function pto_mark_all_read_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	$notifications = get_post_meta($team, 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach ( $notifications as $key => $notification ) {
		$notifications[ $key ]['read'] = 1;
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team, 'notifications', $notifications);
	wp_send_json_success( [ 'message' => 'success' ] );
	exit;
}

add_action( "wp_ajax_pto_mark_all_read_client_nf", "pto_mark_all_read_client_nf" );
function pto_mark_all_read_client_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	$notifications = get_post_meta($team['assigned'], 'notifications', true);
	$notifications = array_reverse($notifications);
	foreach ( $notifications as $key => $notification ) {
		$notifications[ $key ]['read'] = 1;
	}
	$notifications = array_reverse($notifications);
	update_post_meta($team['assigned'], 'notifications', $notifications);
	pto_send_json( array( 
		'error' => false,
	) );
}

add_action( "wp_ajax_pto_clear_all_nf", "pto_clear_all_nf" );
function pto_clear_all_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_team_from_userid($user);
	delete_post_meta($team, 'notifications');
	pto_send_json( array( 
		'html' => '<p style="padding:0 10px">' . esc_html__('You do not have any notifications', 'projectopia-core') . '</p>',
	) );     
}

add_action( "wp_ajax_pto_clear_all_client_nf", "pto_clear_all_client_nf" );
function pto_clear_all_client_nf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$user = wp_get_current_user();
	$team = pto_get_client_from_userid($user);
	delete_post_meta($team['assigned'], 'notifications');
	pto_send_json( array( 
		'html' => '<p style="padding:0 10px">' . esc_html__('You do not have any notifications', 'projectopia-core') . '</p>',
	) );     
}