<?php
add_action( 'add_meta_boxes_cqpim_project', 'add_pto_project_cpt_metaboxes' );
function add_pto_project_cpt_metaboxes( $post ) {
	$project_contributors = get_post_meta( $post->ID, 'project_contributors', true );
	if ( ! $project_contributors ) {
		$project_contributors = array();
	}
	$user = wp_get_current_user();
	$assigned = pto_get_team_from_userid( $user );
	foreach ( $project_contributors as $contributor ) {
		if ( ! empty( $contributor['team_id'] ) && $assigned == $contributor['team_id'] ) {
			$access = true;
		}
	}
	if ( ! empty($access) || current_user_can('cqpim_view_all_projects') || current_user_can('cqpim_create_new_project') && pto_is_edit_page('new') || $post->post_author == $user->ID ) {
		add_meta_box( 
			'project_summary', 
			__('Project Brief', 'projectopia-core'),
			'pto_project_summary_metabox_callback', 
			'cqpim_project',
			'normal',
			'high'
		);
		if ( current_user_can('cqpim_view_project_client_page') ) {
			add_meta_box( 
				'general_project_notes', 
				__('General Project Information', 'projectopia-core'), 
				'pto_general_project_info_metabox_callback', 
				'cqpim_project',
				'normal',
				'high'
			);      
		}
		add_meta_box( 
			'project_contributors', 
			__('Team Members', 'projectopia-core'),
			'pto_project_contributors_metabox_callback', 
			'cqpim_project',
			'normal',
			'high'
		);
		if ( current_user_can('edit_cqpim_invoices') ) {
			if ( get_option('disable_invoices') != 1 ) {
				add_meta_box( 
					'project_invoices', 
					__('Project Invoices', 'projectopia-core'),
					'pto_project_invoices_metabox_callback', 
					'cqpim_project',
					'normal',
					'high'
				);
			}
		}
		add_meta_box( 
			'project_elements', 
			__('Milestones & Tasks', 'projectopia-core'), 
			'pto_project_elements_metabox_callback', 
			'cqpim_project',
			'normal'
		);
		if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
			if ( current_user_can('cqpim_view_bugs') || current_user_can('cqpim_view_all_bugs') ) {
				add_meta_box( 
					'project_bugs', 
					__('Project Bugs', 'projectopia-core'),
					'pto_project_bugs_metabox_callback', 
					'cqpim_project',
					'normal'
				);                  
			}
		}
		add_meta_box( 
			'project_details', 
			__('Project Details', 'projectopia-core'),
			'pto_project_details_metabox_callback', 
			'cqpim_project',
			'side',
			'high'
		);

		add_meta_box( 
			'project_colors', 
			__('Project Colors', 'projectopia-core'),
			'pto_project_color_metabox_callback', 
			'cqpim_project',
			'side',
			'high'
		);

		add_meta_box( 
			'project_status', 
			__('Project Status', 'projectopia-core'),
			'pto_project_status_metabox_callback', 
			'cqpim_project',
			'side',
			'high'
		);

		if ( current_user_can('cqpim_view_project_contract') && get_option('enable_project_contracts') ) {
			add_meta_box( 
				'project_contract', 
				__('Project Contract', 'projectopia-core'),
				'pto_project_contract_metabox_callback', 
				'cqpim_project',
				'side',
				'high'
			);
		}
		if ( current_user_can('cqpim_view_project_financials') ) {
			add_meta_box( 
				'project_financials', 
				__('Project Financials', 'projectopia-core'), 
				'pto_project_financials_metabox_callback', 
				'cqpim_project',
				'normal'
			);
			if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) {
				add_meta_box( 
					'project_expenses', 
					__('Project Expenses', 'projectopia-core'), 
					'pto_project_expenses_metabox_callback', 
					'cqpim_project',
					'normal'
				);                  
			}
			$setting = get_option('allow_project_currency_override');
			if ( $setting == 1 ) {
				add_meta_box( 
					'project_currency', 
					__('Project Currency Settings', 'projectopia-core'), 
					'pto_project_currency_metabox_callback', 
					'cqpim_project',
					'side',
					'high'
				);      
			}
		}
		add_meta_box( 
			'project_notes', 
			__('My Project Notes', 'projectopia-core'),
			'pto_project_notes_metabox_callback', 
			'cqpim_project',
			'normal'
		);
		if ( current_user_can('upload_files') ) {
			add_meta_box( 
				'project_files', 
				__('Project Files', 'projectopia-core'),
				'pto_project_files_metabox_callback', 
				'cqpim_project',
				'normal'
			);
		}
		add_meta_box( 
			'project_messages', 
			__('Project Messages', 'projectopia-core'), 
			'pto_project_messages_metabox_callback', 
			'cqpim_project',
			'normal'
		);
		add_meta_box( 
			'project_updates', 
			__('Project Updates', 'projectopia-core'),
			'pto_project_updates_metabox_callback', 
			'cqpim_project',
			'normal'
		);
		if ( ! current_user_can('cqpim_edit_project_dates') ) {
			remove_meta_box( 'cqpim_project_catdiv', 'cqpim_project', 'side' );
		}
	} else {
		add_meta_box( 
			'project_denied', 
			__('Access Denied', 'projectopia-core'),
			'pto_project_denied_metabox_callback', 
			'cqpim_project',
			'normal'
		); 
		remove_meta_box( 'submitdiv', 'cqpim_project', 'side' );
	}
}
require_once( 'access_denied.php' );
require_once( 'project_summary.php' );
require_once( 'general_info.php' );
require_once( 'project_contract.php' );
require_once( 'project_currency.php' );
require_once( 'team_members.php' );
require_once( 'milestones.php' );
require_once( 'project_details.php' );
require_once( 'project_invoices.php' );
require_once( 'project_financials.php' );
require_once( 'project_expenses.php' );
require_once( 'project_notes.php' );
require_once( 'project_files.php' );
require_once( 'project_messages.php' );
require_once( 'project_updates.php' );