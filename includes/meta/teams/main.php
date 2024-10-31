<?php
add_action( 'add_meta_boxes_cqpim_teams', 'add_pto_teams_cpt_metaboxes' );
function add_pto_teams_cpt_metaboxes( $post ) {
	add_meta_box( 
		'team_details', 
		__('Team Member Details', 'projectopia-core'),
		'pto_team_details_metabox_callback', 
		'cqpim_teams', 
		'side',
		'high'
	);
	add_meta_box( 
		'team_tasks', 
		__('Open Tasks', 'projectopia-core'),
		'pto_team_tasks_metabox_callback', 
		'cqpim_teams', 
		'normal'
	);
	add_meta_box( 
		'team_projects', 
		__('Projects', 'projectopia-core'),
		'pto_team_projects_metabox_callback', 
		'cqpim_teams', 
		'normal'
	);
	add_meta_box( 
		'team_calendar', 
		__('Calendar', 'projectopia-core'),
		'pto_team_calendar_metabox_callback', 
		'cqpim_teams', 
		'normal',
		'high'
	);
	if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) && current_user_can('cqpim_view_expenses_admin') ) {
		add_meta_box( 
			'team_expenses', 
			__('Expenses', 'projectopia-core'),
			'pto_team_expenses_metabox_callback', 
			'cqpim_teams', 
			'normal'
		);          
	}
	if ( ! current_user_can('publish_cqpim_teams') ) {
		remove_meta_box( 'submitdiv', 'cqpim_teams', 'side' );
	}
}
require_once('team_details.php');
require_once('team_calendar.php');
require_once('team_tasks.php');
require_once('team_projects.php');
require_once('team_expenses.php');