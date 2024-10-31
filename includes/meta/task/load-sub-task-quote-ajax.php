<?php 
$sub_task_pid = $task_pid;
$task_details = get_post_meta($sub_task_pid, 'task_details', true);
$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
$task_owner = get_post_meta($sub_task_pid, 'owner', true);
$task_owner_id = get_post_meta($sub_task_pid, 'owner', true);
$client_check = preg_replace('/[0-9]+/', '', $task_owner);
unset($client);
if ( $client_check == 'C' ) {
	$client = true;
}
if ( $task_owner ) {
	if ( ! empty($client) && $client == true ) {
		$id = preg_replace("/[^0-9,.]/", "", $task_owner);
		$client_object = get_user_by('id', $id);
		$task_owner = $client_object->display_name;
	} else {
		$team_details = get_post_meta($task_owner, 'team_details', true);
		$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
		if ( ! empty($team_name) ) {
			$task_owner = $team_name;
		}
	}
} else {
	$task_owner = '';
}
$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
$task_status = isset($task_details['status']) ? $task_details['status'] : '';
$task_time = isset($task_details['task_time']) ? $task_details['task_time'] : '';
$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : '';
$time_spent_total = 0;
$time_spent = get_post_meta($sub_task_pid, 'task_time_spent', true);
if ( ! empty($time_spent) ) {
	foreach ( $time_spent as $time ) {
		$timer = isset($time['time']) ? $time['time'] : 0;
		$time_spent_total = $time_spent_total + $timer;
		$project_total_time_spent = $project_total_time_spent + $timer;
	}
}
if ( ! is_numeric($task_deadline) ) {
	$str_deadline = str_replace('/','-', $task_deadline);
	$deadline_stamp = strtotime($str_deadline);
} else {
	$deadline_stamp = $task_deadline;
}
/*$now = time();

if ( $task_status != 'complete' ) {
	if ( ! empty($deadline_stamp) && $now > $deadline_stamp ) {
		$task_status_string = '<span class="badgeOverdue">' . esc_html__('OVERDUE', 'projectopia-core') . '</span>';
	} else {
		$task_status_string = isset($task_details['status']) ? $task_details['status'] : '';
		if ( ! $task_status_string || $task_status_string == 'pending' ) {
			$task_status_string = '<span class="badgeOverdue  clientApproval">' . esc_html__('Pending', 'projectopia-core') . '</span>';
		} elseif ( $task_status_string == 'on_hold' ) {
			$task_status_string = '<span class="badgeOverdue  clientApproval">' . esc_html__('On Hold', 'projectopia-core') . '</span>';
		} elseif ( $task_status_string == 'progress' ) {
			$task_status_string = '<span class="badgeOverdue normal">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
		}
	}
} else {
	$task_status_string = '<span class="badgeOverdue approved">' . esc_html__('Complete', 'projectopia-core') . '</span>';
}*/
$task_status_string = pto_get_task_status_html( $task_status, $deadline_stamp );
/**
 * Filter Task Status Display in Project page
 */
$task_status_string = apply_filters('pto_project_task_status_string', $task_status_string, $task_details['status'], $task_details);

unset($hide);                           
if ( ! empty($quote_details['hide_complete']) && $task_status == 'complete' ) {
	$hide = true;
} ?>
?>
<div <?php if ( ! empty($hide) ) { ?>style="display:none"<?php } ?> class="dd-subtask" id="task-<?php echo esc_attr( $sub_task_pid ); ?>">

	<input class="task_weight" type="hidden" name="task_weight_<?php echo esc_attr( $sub_task_pid ); ?>" id="task_weight_<?php echo esc_attr( $sub_task_pid ); ?>" value="<?php echo esc_attr( $weight ); ?>" />
	<input class="task_id" type="hidden" name="task_id_<?php echo esc_attr( $sub_task_pid ); ?>" id="task_id_<?php echo esc_attr( $sub_task_pid ); ?>" value="<?php echo esc_attr( $sub_task_pid ); ?>" />
	<span class="table-task ms-title" <?php if ( ! empty($project_colours['task_colour']) ) { ?> style="color:<?php echo esc_attr( $project_colours['task_colour'] ); ?>"<?php } ?>>
		<?php esc_html_e('Subtask', 'projectopia-core'); ?></span> 
	<a href="<?php echo esc_url( get_edit_post_link($sub_task_pid) ); ?>">
	<span class="ms-title" id="task_title_<?php echo esc_attr( $sub_task_pid ); ?>"><?php echo esc_html( get_the_title( $sub_task_pid ) ); ?>
	</span>
	</a>

	<?php if ( current_user_can( 'publish_cqpim_tasks' ) ) { ?>
		<div class="dd-task-actions">
			<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
				<button class="btn px-3" type="button"
					data-toggle="dropdown" aria-haspopup="true"
					aria-expanded="false">
					<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
						class="img-fluid" />
				</button>
				<div class="dropdown-menu">
					<button value="<?php echo esc_attr( $sub_task_pid ); ?>"
							class="edit-task dropdown-item d-flex align-items-center"
							type="button">
						<?php esc_html_e('Edit Subtask', 'projectopia-core'); ?>
					</button>
					<button class="delete_subtask_trigger dropdown-item d-flex align-items-center"
							type="button"
							data-id="<?php echo esc_attr( $sub_task_pid ); ?>" 
							value="<?php echo esc_attr( $sub_task_pid ); ?>">
						<?php esc_html_e('Delete Subtask', 'projectopia-core'); ?>
					</button>
					
				</div>
			</div>
			<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" value="<?php echo esc_attr( $task_milestone_id ); ?>" title="<?php esc_html_e('Reorder Subtask', 'projectopia-core'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
		</div>
	<?php } ?>
	<div class="dd-task-status mt-2" id="task-<?php echo esc_attr( $sub_task_pid ); ?>-status">
		<?php echo wp_kses_post( $task_status_string ); ?>
	</div>
	
	<div class="dd-task-info">
		<?php
		if ( current_user_can('cqpim_edit_project_milestones') || $task_owner_id == $current_team ) { 
			$start_date = ! empty( $start ) ? wp_date( get_option('cqpim_date_format'), $start ) : ''; 
			$end_date   = ! empty($task_deadline) ? wp_date(get_option('cqpim_date_format'), $task_deadline) : '';
			?>
			<div class="mileStone-addSchedule">
				<div class="d-flex flex-wrap">
					<div class="addSchedule">
						<label><?php esc_html_e('Start Date', 'projectopia-core') ?></label>
						<input
							type="text"
							id="start_<?php echo esc_attr( $sub_task_pid ); ?>"
							data-id="<?php echo esc_attr( $sub_task_pid ); ?>"
							value="<?php echo esc_attr( $start_date ); ?>"
							class="datepicker input start_editable" />
					</div>	
					<div class="addSchedule">
						<label><?php esc_html_e('Deadline', 'projectopia-core') ?></label>
						<input type="text"
							id="end_<?php echo esc_attr( $sub_task_pid ); ?>" 
							data-id="<?php echo esc_attr( $sub_task_pid )?>"
							value="<?php echo esc_attr( $end_date ); ?>"
							class="datepicker input end_editable" />
					</div>
					<div class="addSchedule">
						<label><?php esc_html_e('Assigned To', 'projectopia-core') ?></label>
						<select data-id="<?php echo esc_attr( $sub_task_pid ); ?>"
							class="input customSelect admin_task_assignee assignee_editable">
							<option value=""><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
							<?php 
							$assignees = pto_get_available_assignees($sub_task_pid); 

							if ( $client_id != '' && $client_contact_name != '' ) { 
							?>
							<optgroup label="<?php esc_html_e('Client', 'projectopia-core'); ?>">
							<option value="<?php echo esc_attr( $client_id ); ?>" <?php selected($task_owner_id, $client_id); ?>><?php echo esc_html( $client_contact_name ); ?></option>
							<?php }
							?>
							</optgroup>
							<?php
							if ( ! empty($assignees) ) {
								foreach ( $assignees as $available ) { 
									$av_team_details = get_post_meta($available, 'team_details', true); 
									$av_team_name = isset($av_team_details['team_name']) ? $av_team_details['team_name'] : ''; ?>
									<option value="<?php echo esc_attr( $available ); ?>" <?php selected($task_owner_id, $available); ?>><?php echo esc_html( $av_team_name ); ?></option>
								<?php }
							} elseif ( $task_owner_id != $client_id && ! empty( $team_name ) ) { ?>	
								<option value="<?php echo esc_attr( $task_owner_id ); ?>" <?php selected($task_owner_id, $task_owner_id); ?>><?php echo esc_html( $team_name ); ?></option>
							<?php } ?>
						</select>
					</div>
					<?php if ( ! empty($task_est_time) ) { ?>
						<div class="addSchedule">
							<label> <?php esc_html_e('Estimate Time', 'projectopia-core') ?></label> 
							<span id="est_time_<?php echo esc_attr( $sub_task_pid ); ?>" class="input px-3 py-2"><?php echo esc_html( $task_est_time ); ?></span> 
						</div>
					<?php } ?>
					<?php if ( ! empty($time_spent_total) ) { ?>
						<div class="addSchedule">
							<label> <?php esc_html_e('Time Spent', 'projectopia-core') ?> </label>
							<span class="input px-3 py-2"><?php echo esc_html( $time_spent_total ); ?></span>
						</div>
					<?php } ?>									
				</div>
			</div>
		<?php } else { ?>
			<div class="mileStone-addSchedule">
				<div class="d-flex flex-wrap">
					<?php if ( ! empty($start) ) { ?>
					<div class="addSchedule">
						<label><?php esc_html_e('Start Date', 'projectopia-core') ?></label>
						<span> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?> </span>
					</div>
					<?php } ?>
					<?php if ( ! empty($task_deadline) ) { ?>
						<div class="addSchedule">
							<label><?php esc_html_e('Deadline', 'projectopia-core') ?></label>
							<span> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?> </span>
						</div>
					<?php } ?>
					<?php if ( ! empty( $task_owner ) ) { ?>
						<div class="addSchedule">
							<label><?php esc_html_e('Assigned To', 'projectopia-core') ?></label>
							<span> <?php echo esc_html( $task_owner ); ?> </span>
						</div>
					<?php } ?>
					<?php if ( ! empty($task_est_time) ) { ?>
						<div class="addSchedule">
							<label><?php esc_html_e('Est. Time:', 'projectopia-core') ?></label>
							<span> <?php echo esc_html( $task_est_time ); ?> </span>
						</div>
					<?php } ?>
					<?php if ( ! empty($time_spent_total) ) { ?>
						<div class="addSchedule">
							<label> <?php esc_html_e('Time Spent', 'projectopia-core') ?> </label>
							<span><?php echo esc_html( $time_spent_total ); ?></span>
						</div>
					<?php } ?>	
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="clear"></div>
</div>