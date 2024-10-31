<?php
add_action( 'admin_menu' , 'register_pto_acceptance_page', 29 ); 
function register_pto_acceptance_page() {
	$mypage = add_submenu_page( 
				'pto-acceptance',
				__('Task Acceptance', 'projectopia-core'),             
				'<span class="pto-sm-hidden">' . esc_html__('Task Acceptance', 'projectopia-core') . '</span>',             
				'edit_cqpim_projects',          
				'pto-acceptance',       
				'pto_acceptance'
	);
	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}
function pto_acceptance() { 
	$user = wp_get_current_user(); 
	$roles = $user->roles;
	$assigned = pto_get_team_from_userid();
	$task_id = isset($_GET['task']) ? sanitize_text_field(wp_unslash($_GET['task'])) : '';
	$accept_string = isset($_GET['accept_string']) ? sanitize_text_field(wp_unslash($_GET['accept_string'])) : '';
	$task_obj = get_post($task_id);
	$project_id = get_post_meta($task_obj->ID, 'project_id', true);
	$project_obj = get_post($project_id);
	$task_accept_string = get_post_meta($task_obj->ID, 'accept_rand', true);
	$task_owner = get_post_meta($task_obj->ID, 'owner', true);
	$last_updated = get_post_meta($task_obj->ID, 'last_updated', true);
	$last_updated_user = get_user_by('id', $last_updated);
	?>
	<div class="masonry-grid">
		<div class="cqpim-dash-item-full grid-item tasks-box">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-pencil-square-o font-light-violet " aria-hidden="true"></i>
						<span class="caption-subject font-light-violet  sbold"> <?php esc_html_e('Accept Task', 'projectopia-core'); ?></span>
					</div>
				</div>
				<?php if ( $assigned != $task_owner || $accept_string != $task_accept_string ) { ?>
					<h3><?php esc_html_e('Task Acceptance Link has Expired', 'projectopia-core'); ?></h3>
				<?php } else { ?>
					<h2><?php
						/* translators: %s: Task Assignee */
						printf(esc_html__('%s has assigned a task to you!', 'projectopia-core'), esc_html($last_updated_user->display_name)); ?></h2>
					<h3><strong><?php esc_html_e('Project', 'projectopia-core'); ?>: </strong> <?php echo esc_html($project_obj->post_title); ?></h3>
					<h3><strong><?php esc_html_e('Task', 'projectopia-core'); ?>: </strong> <?php echo esc_html($task_obj->post_title); ?></h3>
					<h3><?php esc_html_e('Do you accept this task?', 'projectopia-core'); ?></h3>
					<br />
					<form id="submit_acceptance">
						<div>
							<select name="accept_answer" id="accept_answer" required>
								<option value="0"><?php esc_html_e('Choose...', 'projectopia-core'); ?></option>
								<option value="1"><?php esc_html_e('Yes', 'projectopia-core'); ?></option>
								<option value="2"><?php esc_html_e('No', 'projectopia-core'); ?></option>
							</select>
						</div>
						<div id="accept_no" style="display:none">
							<p><?php esc_html_e('You have chosen to not accept this task, please explain why below.', 'projectopia-core'); ?></p>
							<input type="text" name="accept_answer_no" id="accept_answer_no" />
						</div>
						<br />
						<input type="hidden" id="task_id" value="<?php echo esc_attr( $task_id ); ?>" />
						<input type="submit" class="cqpim_button bg-blue font-white rounded_4" value="<?php esc_html_e('Submit', 'projectopia-core'); ?>" />
					</form>
					<div id="submit_messages"></div>
				<?php } ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>	
<?php }