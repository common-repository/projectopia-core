<?php

// Back Compat
add_action( 'admin_init', 'pto_v4_compat' );
function pto_v4_compat() {
	$checked = get_option( 'v4_compat_complete' );
	if ( empty( $checked ) ) {
		$args = array(
			'post_type'      => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$projects = get_posts( $args );
		foreach ( $projects as $project ) {
			$contract_status = get_post_meta( $project->ID, 'contract_status', true );
			if ( empty( $contract_status ) ) {
				$contract = pto_get_contract_status( $project->ID );
				update_post_meta( $project->ID, 'contract_status', $contract );
			}           
		}
		update_option( 'v4_compat_complete', true );
	}
	$support_ticket_status = get_option( 'support_status' );
	$default_status = array(
		'key'   => array( 'open', 'resolved', 'hold', 'waiting' ),
		'value' => array( 'Open', 'Resolved', 'On Hold', 'Awaiting Response' ),
		'color' => array( '#F1C40F', '#8ec165', '#e7505a', '#796799' ),
	);
						
	if ( $support_ticket_status == '' ) {
		update_option( 'support_status', $default_status );
	}
}

add_action( 'admin_init', 'pto_v4_1_compat' );
function pto_v4_1_compat() {
	$checked = get_option( 'v4_1_compat_complete' );
	if ( empty( $checked ) ) {
		update_option( 'new_lead_email_subject', 'A new Lead has been submitted at %%COMPANY_NAME%%' );
		update_option( 'new_lead_email_content', 'Dear %%TEAM_NAME%%

A new lead has been submitted at %%COMPANY_NAME%%

You can view the lead by clicking this link - %%LEAD_URL%%

Best Regards

%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%' );

		update_option( 'cqpim_enable_faq', 1 );
		update_option( 'cqpim_faq_slug', 'pto_faq' );
		$role = get_role( 'cqpim_client' );
		if ( ! empty( $role ) ) {
			$role->add_cap( 'read_private_cqpim_faqs' );
		}
		update_option( 'v4_1_compat_complete', true );
	}
}

add_action( 'admin_init', 'pto_v4_3_compat' );
function pto_v4_3_compat() {
	$checked = get_option( 'v4_3_compat_complete' );
	if ( empty( $checked ) ) {
		$args = array(
			'post_type'      => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$projects = get_posts( $args );
		foreach ( $projects as $project ) {            
			$project_details = get_post_meta( $project->ID, 'project_details', true );            
			if ( ! empty( $project_details['closed'] ) ) {
				update_post_meta( $project->ID, 'closed', 1 );
			}       
		}
		update_option( 'pto_default_project_sort', 2 );
		update_option( 'pto_default_project_order', 'asc' );
		update_option( 'pto_default_drop_closed', 1 );
		update_option( 'assignment_response_subject', '%%CURRENT_USER%% has %%ACCEPT_CHOICE%% assignment of a task: %%TASK_NAME%%' );
		update_option( 'assignment_response_email', 'Dear %%NAME%%

%%CURRENT_USER%% has %%ACCEPT_CHOICE%% assignment of a task: %%TASK_NAME%%

NOTES: %%NOTES%%
Task URL: %%TASK_URL%%

Best Regards

%%CURRENT_USER%%
%%COMPANY_NAME%%
Tel: %%COMPANY_TELEPHONE%%
Email: %%COMPANY_SALES_EMAIL%%' );
		update_option( 'pto_task_acceptance', 0 );
		update_option( 'v4_3_compat_complete', true );
	}
}

add_action( 'admin_init', 'pto_v5_0_0_compat' );
function pto_v5_0_0_compat() {
	$checked = get_option( 'pto_v5_0_0_compat_complete' );
	if ( empty( $checked ) ) {
		$client_login = get_option( 'cqpim_login_page' );
		$client_dash = get_option( 'cqpim_client_page' );
		$client_reset = get_option( 'cqpim_reset_page' );
		$client_register = get_option( 'cqpim_register_page' );

		if ( $client_login ) {
			add_post_meta( $client_login, 'pto_template_type', 'cqpim_login_page' );
		}
		if ( $client_dash ) {
			add_post_meta( $client_reset, 'pto_template_type', 'cqpim_reset_page' );
		}
		if ( $client_reset ) {
			add_post_meta( $client_dash, 'pto_template_type', 'cqpim_client_page' );
		}
		if ( $client_register ) {
			add_post_meta( $client_register, 'pto_template_type', 'cqpim_register_page' );
		}

		update_option( 'pto_v5_0_0_compat_complete', true );
	}
}

add_action( 'admin_init', 'pto_v5_0_2_compat' );
function pto_v5_0_2_compat() {
	$checked = get_option( 'pto_v5_0_2_compat_complete' );
	if ( empty( $checked ) ) {
		$args = array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'meta_key'       => 'projectopia-core',
			'meta_value'     => true,
		);
		$attachments = get_posts( $args );
		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				delete_post_meta( $attachment->ID, 'projectopia-core' );
				update_post_meta( $attachment->ID, 'cqpim', true );
			}
		}
		update_option( 'pto_v5_0_2_compat_complete', true );
	}
}

add_action( 'admin_init', 'pto_v5_0_7_compat' );
function pto_v5_0_7_compat() {
	$checked = get_option( 'pto_v5_0_7_compat_complete' );
	if ( empty( $checked ) ) {
		$client_reset = get_option( 'cqpim_reset_page' );
		$client_register = get_option( 'cqpim_register_page' );
		if ( get_post( $client_register )->post_name == 'client-reset' ) {
			update_option( 'cqpim_register_page', $client_reset );
			update_option( 'cqpim_reset_page', $client_register );
			update_post_meta( $client_register, 'pto_template_type', 'cqpim_register_page' );
			update_post_meta( $client_reset, 'pto_template_type', 'cqpim_reset_page' );
		}
		update_option( 'pto_v5_0_7_compat_complete', true );
	}
}

add_action( 'admin_init', 'pto_v5_0_8_compat' );
function pto_v5_0_8_compat() {
	$checked = get_option( 'pto_v5_0_8_compat_complete' );
	if ( empty( $checked ) ) {
		foreach ( [ 'task', 'client', 'invoice', 'support' ] as $name ) {
			$option = get_option( 'cqpim_custom_fields_' . $name );
			if ( $option ) {
				update_option( 'cqpim_custom_fields_' . $name, wp_unslash( $option ) );
				update_option( 'cqpim_custom_fields_backup_' . $name, $option );
			}
		}
		update_option( 'pto_v5_0_8_compat_complete', true );
	}
}