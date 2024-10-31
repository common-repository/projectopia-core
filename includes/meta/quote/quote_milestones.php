<?php
function pto_quote_elements_metabox_callback( $post ) {
 	wp_nonce_field( 'quote_elements_metabox', 'quote_elements_metabox_nonce' );

	$quote_elements = get_post_meta( $post->ID, 'quote_elements', true );
	$quote_details = get_post_meta( $post->ID, 'quote_details', true );
	$type = isset( $quote_details['quote_type'] ) ? $quote_details['quote_type'] : '';
	if ( ! empty( $type ) ) {
		if ( empty( $quote_elements ) ) {
			echo '<div id="dd-container" class="milestone-content">';
			echo '<p id="no_ms_nag">' . esc_html__( 'You have not added any milestones to this quote / estimate, please do so below', 'projectopia-core' ) . '</p>';
			echo '</div>';
		} else {
			$currency = get_option('currency_symbol');
			if ( $type == 'estimate' ) { 
				$cost_title = __('Estimated Cost', 'projectopia-core');
			} else {
				$cost_title = __('Cost', 'projectopia-core');
			}
			$ordered = array();
			foreach ( $quote_elements as $element ) {
				//If milestone is orphan then avoid it.
				if ( ! empty( $element['id'] ) && ! empty( $element['weight'] ) ) {
					$weight = $element['weight'];
					$ordered[ $weight ] = $element;
				}
			}
			ksort( $ordered );
			?>
			<div id="dd-container" class="milestone-content">
				<?php foreach ( $ordered as $key => $element ) { ?>
					<div class="dd-milestone" id="milestone-<?php echo esc_attr( $element['id'] ); ?>">
						<input type="hidden" class="element_weight" name="element_weight[<?php echo esc_attr( $element['id'] ); ?>]" id="element_weight[<?php echo esc_attr( $element['id'] ); ?>]" data-msid="<?php echo esc_attr( $element['id'] ); ?>" value="<?php echo esc_attr( $element['weight'] ); ?>" />
						<div class="dd-milestone-title">
							<span class="ms-title" style="color: #337ab7;"><?php esc_html_e('Milestone:', 'projectopia-core'); ?></span>
							<span class="ms-title" id="ms_title_<?php echo esc_attr( $element['id'] ); ?>"><?php echo esc_html( $element['title'] ); ?></span>
							<?php if ( current_user_can('publish_cqpim_quotes') ) {
								if ( empty( $quote_details['confirmed'] ) ) { ?>
									<div class="dd-milestone-actions">
										<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
											<button class="btn px-3" type="button"
												data-toggle="dropdown" aria-haspopup="true"
												aria-expanded="false">
												<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
													class="img-fluid" />
											</button>
											<div class="dropdown-menu">
												<button data-ms="<?php echo esc_attr( $element['id'] ); ?>" 
														data-project="<?php echo esc_attr( $post->ID ); ?>" 
														value="<?php echo esc_attr( $element['id'] ); ?>"
														class="add_task dropdown-item d-flex align-items-center"
														type="button">
													<?php esc_html_e('Add Task', 'projectopia-core'); ?>
												</button>
												<button value="<?php echo esc_attr( $element['id'] ); ?>"
														class="edit-milestone dropdown-item d-flex align-items-center"
														type="button">
													<?php esc_html_e('Edit Milestone', 'projectopia-core'); ?>
												</button>
												<button class="delete_stage_conf dropdown-item d-flex align-items-center"
													type="button"
													data-id="<?php echo esc_attr( $element['id'] ); ?>" 
													value="<?php echo esc_attr( $element['id'] ); ?>">
													<?php esc_html_e('Delete Milestone', 'projectopia-core'); ?>
												</button>
											</div>
										</div>
										<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" 
											value="<?php echo esc_attr( $element['id'] ); ?>" 
											title="<?php esc_html_e('Reorder Milestone', 'projectopia-core'); ?>">
											<i class="fa fa-sort" aria-hidden="true"></i>
										</button>
									</div>
								<?php } else { ?>
									<div class="dd-milestone-status mt-2">
										<span class="badgeOverdue clientApproval"><?php esc_html_e( 'Locked', 'projectopia-core' ); ?></span>
									</div>
								<?php }
							} ?>
							<div class="clear"></div>
							<div class="dd-milestone-info mileStone-content-taskBar d-block flex-wrap d-md-flex justify-content-between align-items-center">
								<div class="mileStone-content-deadline d-block d-sm-flex">
									<?php if ( ! empty( $element['cost'] ) ) { ?>
										<div class="mileStone-content-singleDeadline d-flex">
											<div class="mileStone-content-icon">
												<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/usacoin.svg' ); ?>" class="icon img-fluid mr-2" />
											</div>
											<div class="mileStone-content-info">
												<h5 class="mb-1"><?php esc_html_e('Budget', 'projectopia-core'); ?></h5>
												<p id="ms_cost_<?php echo esc_attr( $element['id'] ); ?>" class="mb-0"><?php echo esc_html( pto_calculate_currency($post->ID, $element['cost']) ); ?></p>
											</div>
										</div>
									<?php } ?>
									<?php if ( ! empty($element['start']) ) { ?>
										<div class="mileStone-content-singleDeadline d-flex">
											<div class="mileStone-content-icon">
												<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/clander.svg' ); ?>" class="icon img-fluid mr-2" />
											</div>
											<div class="mileStone-content-info">
												<h5 class="mb-1"><?php esc_html_e('Start Date', 'projectopia-core'); ?></h5>
												<p id="ms_start_<?php echo esc_attr( $element['id'] ); ?>" class="mb-0"><?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element['start']) ); ?></p>
											</div>
										</div>
									<?php } ?>
									<?php if ( ! empty($element['deadline']) ) { ?>
										<div class="mileStone-content-singleDeadline d-flex">
											<div class="mileStone-content-icon">
												<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/clander.svg' ); ?>" class="icon img-fluid mr-2" />
											</div>
											<div class="mileStone-content-info">
												<h5 class="mb-1"><?php esc_html_e('Deadline', 'projectopia-core'); ?></h5>
												<p class="mb-0" id="ms_deadline_<?php echo esc_attr( $element['id'] ); ?>"><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ), $element['deadline'] ) ); ?></p>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
							<div class="clear"></div>
						</div>
						<div class="dd-tasks" data-ms="<?php echo esc_attr( $element['id'] ); ?>">
							<?php 
							$args = array(
								'post_type'      => 'cqpim_tasks',
								'posts_per_page' => -1,
								'meta_key'       => 'milestone_id',
								'meta_value'     => $element['id'],
								'orderby'        => 'date',
								'order'          => 'ASC',
							);
							$tasks = get_posts($args);
							$ordered = array();
							foreach ( $tasks as $task ) {
								$task_details = get_post_meta($task->ID, 'task_details', true);
								$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
								if ( empty($task->post_parent) ) {
									$ordered[ $weight ] = $task;
								}
							}
							ksort($ordered);
							foreach ( $ordered as $task ) {
								$task_details = get_post_meta($task->ID, 'task_details', true);
								$task_deadline = isset($task_details['deadline']) && ! empty($task_details['deadline']) ? $task_details['deadline'] : $element['deadline'];
								$start = isset($task_details['task_start']) && ! empty($task_details['task_start']) ? $task_details['task_start'] : $element['start'];
								$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
								$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
								$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
								?>	
								<div class="dd-task" id="task-<?php echo esc_attr( $task->ID ); ?>">
									<input class="task_weight" type="hidden" name="task_weight_<?php echo esc_attr( $task->ID ); ?>" id="task_weight_<?php echo esc_attr( $task->ID ); ?>" value="<?php echo esc_attr( $weight ); ?>" />
									<input class="task_id" type="hidden" name="task_id_<?php echo esc_attr( $task->ID ); ?>" id="task_id_<?php echo esc_attr( $task->ID ); ?>" value="<?php echo esc_attr( $task->ID ); ?>" />
									<input class="task_msid" type="hidden" name="task_msid_<?php echo esc_attr( $task->ID ); ?>" id="task_msid_<?php echo esc_attr( $task->ID ); ?>" value="<?php echo esc_attr( $element['id'] ); ?>" />
									<span class="ms-title" style="color: #36c6d3;"><?php esc_html_e('Task', 'projectopia-core'); ?></span>
									<a href="<?php echo esc_url( get_edit_post_link( $task->ID ) ); ?>">
										<span class="ms-title" id="task_title_<?php echo esc_attr( $task->ID ); ?>">
											<?php echo esc_html( $task->post_title ); ?>
										</span>
									</a>
									<?php if ( current_user_can( 'publish_cqpim_quotes' ) ) {
										if ( empty( $quote_details['confirmed'] ) ) { ?>
											<div class="dd-task-actions">
												<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
													<button class="btn px-3" type="button"
														data-toggle="dropdown" aria-haspopup="true"
														aria-expanded="false">
														<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
															class="img-fluid" />
													</button>
													<div class="dropdown-menu">
														<button value="<?php echo esc_attr( $task->ID ); ?>"
																class="edit-task dropdown-item d-flex align-items-center"
																type="button">
															<?php esc_html_e('Edit Task', 'projectopia-core'); ?>
														</button>
														<button class="delete_task_trigger dropdown-item d-flex align-items-center"
																type="button"
																data-id="<?php echo esc_attr( $task->ID ); ?>" 
																value="<?php echo esc_attr( $task->ID ); ?>">
															<?php esc_html_e('Delete Task', 'projectopia-core'); ?>
														</button>
														<button data-ms="<?php echo esc_attr( $element['id'] ); ?>"
																data-project="<?php echo esc_attr( $post->ID ); ?>" 
																value="<?php echo esc_attr( $task->ID ); ?>"
																class="add_subtask dropdown-item d-flex align-items-center"
																type="button">
															<?php esc_html_e('Add Subtask', 'projectopia-core'); ?>
														</button>
													</div>
												</div>
												<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" value="<?php echo esc_attr( $element['id'] ); ?>" title="<?php esc_html_e('Reorder Task', 'projectopia-core'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
											</div>
										<?php } else { ?>
											<div class="dd-milestone-status mt-2">
												<span class="badgeOverdue clientApproval"><?php esc_html_e( 'Locked', 'projectopia-core' ); ?></span>
											</div>
										<?php }
									} ?>
									<div class="dd-task-info">
										<div class="mileStone-addSchedule">
											<div class="d-flex flex-wrap">
												<?php if ( ! empty($start) ) { ?>
													<div class="addSchedule">
														<label><?php esc_html_e('Start Date', 'projectopia-core') ?></label>
														<span id="task_start_<?php echo esc_attr( $task->ID ); ?>"> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?> </span>
													</div>
												<?php } ?>
												<?php if ( ! empty($task_deadline) ) { ?>
													<div class="addSchedule">
														<label><?php esc_html_e('Deadline', 'projectopia-core') ?></label>
														<span id="task_deadline_<?php echo esc_attr( $task->ID ); ?>"> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?> </span>
													</div>
												<?php } ?>
												<?php if ( ! empty($task_est_time) ) { ?>
													<div class="addSchedule">
														<label><?php esc_html_e('Est. Time:', 'projectopia-core') ?></label>
														<span id="task_time_<?php echo esc_attr( $task->ID ); ?>"> <?php echo esc_html( $task_est_time ); ?> </span>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>
									<div class="clear"></div>
									<div class="dd-subtasks">
										<?php 
										$args = array(
											'post_type'   => 'cqpim_tasks',
											'posts_per_page' => -1,
											'meta_key'    => 'milestone_id',
											'meta_value'  => $element['id'],
											'post_parent' => $task->ID,
											'orderby'     => 'date',
											'order'       => 'ASC',
										);
										$subtasks = get_posts($args);
										$ordered = array();
										foreach ( $subtasks as $subtask ) {
											$task_details = get_post_meta($subtask->ID, 'task_details', true);
											$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
											$ordered[ $weight ] = $subtask;
										}
										ksort($ordered);
										if ( ! empty($ordered) ) {
											foreach ( $ordered as $subtask ) {
												$task_details = get_post_meta($subtask->ID, 'task_details', true);
												$task_deadline = isset($task_details['deadline']) && ! empty($task_details['deadline']) ? $task_details['deadline'] : $element['deadline'];
												$start = isset($task_details['task_start']) && ! empty($task_details['task_start']) ? $task_details['task_start'] : $element['start'];
												$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
												$sweight = isset($task_details['weight']) ? $task_details['weight'] : 0;
												$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : '';
												?>	
												<div class="dd-subtask" id="task-<?php echo esc_attr( $subtask->ID ); ?>">
													<input class="task_weight" type="hidden" name="task_weight_<?php echo esc_attr( $subtask->ID ); ?>" id="task_weight_<?php echo esc_attr( $subtask->ID ); ?>" value="<?php echo esc_attr( $sweight ); ?>" />
													<input class="task_id" type="hidden" name="task_id_<?php echo esc_attr( $subtask->ID ); ?>" id="task_id_<?php echo esc_attr( $subtask->ID ); ?>" value="<?php echo esc_attr( $subtask->ID ); ?>" />
													<span class="ms-title" style="color: #36c6d3;"><?php esc_html_e('Subtask', 'projectopia-core'); ?></span>
													<a href="<?php echo esc_url( get_edit_post_link( $subtask->ID ) ); ?>">
														<span class="ms-title" id="task_title_<?php echo esc_attr( $subtask->ID ); ?>">
															<?php echo esc_html( $subtask->post_title ); ?>
														</span>
													</a>
													<?php if ( current_user_can('publish_cqpim_quotes') ) { ?>
														<div class="dd-task-actions">
															<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
																<button class="btn px-3" type="button"
																	data-toggle="dropdown" aria-haspopup="true"
																	aria-expanded="false">
																	<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
																		class="img-fluid" />
																</button>
																<div class="dropdown-menu">
																	<button value="<?php echo esc_attr( $subtask->ID ); ?>"
																		class="edit-task dropdown-item d-flex align-items-center"
																		type="button">
																	<?php esc_html_e('Edit Subtask', 'projectopia-core'); ?>
																	</button>
																	<button class="delete_subtask_trigger dropdown-item d-flex align-items-center"
																		type="button"
																		data-id="<?php echo esc_attr( $subtask->ID ); ?>" 
																		value="<?php echo esc_attr( $subtask->ID ); ?>">
																		<?php esc_html_e('Delete Subtask', 'projectopia-core'); ?>
																	</button>
																</div>
															</div>
															<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" value="<?php echo esc_attr( $element['id'] ); ?>" title="<?php esc_html_e('Reorder Subtask', 'projectopia-core'); ?>"><i class="fa fa-sort" aria-hidden="true"></i></button>
														</div>
													<?php } ?>
													<div class="dd-task-info">
														<div class="mileStone-addSchedule">
															<div class="d-flex flex-wrap">
																<?php if ( ! empty($start) ) { ?>
																	<div class="addSchedule">
																		<label><?php esc_html_e('Start Date', 'projectopia-core') ?></label>
																		<span id="task_start_<?php echo esc_attr( $subtask->ID ); ?>"> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?> </span>
																	</div>
																<?php } ?>
																<?php if ( ! empty($task_deadline) ) { ?>
																	<div class="addSchedule">
																		<label><?php esc_html_e('Deadline', 'projectopia-core') ?></label>
																		<span id="task_deadline_<?php echo esc_attr( $subtask->ID ); ?>"> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?> </span>
																	</div>
																<?php } ?>
																<?php if ( ! empty($task_est_time) ) { ?>
																	<div class="addSchedule">
																		<label><?php esc_html_e('Est. Time:', 'projectopia-core') ?></label>
																		<span id="task_time_<?php echo esc_attr( $subtask->ID ); ?>"> <?php echo esc_html( $task_est_time ); ?> </span>
																	</div>
																<?php } ?>
															</div>
														</div>
													</div>
													<div class="clear"></div>
												</div>
											<?php } ?>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php }
	} else {
		esc_html_e('Please update this post before adding milestones', 'projectopia-core');
	}
	if ( current_user_can('publish_cqpim_quotes') ) { ?>	
		<!-- Action Buttons -->
		<?php if ( empty($quote_details['confirmed_details']['date']) ) { ?>
			<?php if ( current_user_can('cqpim_apply_project_templates') ) { ?>
				<a href="#apply-template" id="apply-template" class="mt-20 piaBtn btn btn-primary ml-2 caribbeanGreen right colorbox"><?php esc_html_e('Apply Milestone Template', 'projectopia-core'); ?></a>
			<?php } ?>
			<a href="#add-milestone-div" id="add-milestone" class="mt-20 piaBtn right colorbox"><?php esc_html_e('Add Milestone', 'projectopia-core'); ?></a>
			<a href="#clear-all" id="clear-all" class="mt-20 piaBtn btn btn-primary redColor mx-2 right colorbox"><?php esc_html_e('Clear All Milestones / Tasks', 'projectopia-core'); ?></a>
			<div class="clear"></div>
		<?php } ?>
		<!-- Apply milestone template -->
		<div id="apply-template-div-container" style="display:none">
			<div id="apply-template-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Apply Milestone / Task Template', 'projectopia-core'); ?></h3>
					<div class="form-group">
						<label>
							<?php
							$elements = get_post_meta($post->ID, 'quote_elements', true);
							if ( empty($elements) ) {
								echo '<p>' . esc_html_e('Choose a template to apply Milestones and Tasks from.', 'projectopia-core') . '</p>';
							} else {
								echo '<p>' . esc_html_e('You already have Milestones and Tasks on this quote, applying a template will overwrite them with the contents of the template.', 'projectopia-core') . '</p>';                       
							} ?>
						</label>

						<?php
						$args = array(
							'post_type'      => 'cqpim_templates',
							'posts_per_page' => -1,
							'post_status'    => 'private',
						);
						$templates = get_posts($args);
						?>

						<div class="input-group">
							<select id="template_choice" class="form-control input customSelect">
								<option value=""><?php esc_html_e('Choose a template...', 'projectopia-core'); ?></option>
								<?php foreach ( $templates as $template ) { ?>
									<option value="<?php echo esc_attr( $template->ID ); ?>"><?php echo esc_html( $template->post_title ); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div id="template_team_warning" style="display:none"></div>
					<div id="apply-template-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button id="apply-template-action" 
							class="metabox-add-button btn piaBtn right" 
								data-type="quote" 
								data-hid="<?php if ( ! empty( $new_id ) ) { echo esc_attr( $new_id ); } ?>" 
								data-hwe="<?php if ( ! empty( $new_id ) ) { echo esc_attr( $new_weight ); } ?>" 
								value="<?php echo esc_attr( $post->ID ); ?>">
							<?php esc_html_e('Apply Template', 'projectopia-core'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- Clear All Colorbox -->
		<div id="clear-all-div-container" style="display:none">
			<div id="clear-all-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Clear All', 'projectopia-core'); ?></h3>
					<p class="form-group">
						<?php esc_html_e('Are you sure you want to clear all Milestones and Tasks? This cannot be undone.', 'projectopia-core'); ?>
					</p>
					<div id="clear-all-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button id="clear-all-action" class="btn piaBtn right" data-type="quote" value="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('Clear All', 'projectopia-core'); ?></button> <div id="clear_spinner" class="ajax_spinner" style="display:none"></div>				
					</div>
				</div>
			</div>
		</div>
		<!-- Add Milestone Colorbox -->
		<div id="add-milestone-div-container" style="display:none">
			<div id="add-milestone-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Add Milestone', 'projectopia-core'); ?></h3>
					<div id="add_milestone_form">
						<div class="form-group"> 
							<div class="input-group">
								<input class="form-control input" 
									type="text" name="quote_element_title" 
									id="quote_element_title" 
									placeholder="<?php esc_attr_e('Milestone title, eg. \'Design Phase\'', 'projectopia-core'); ?>"/>
							</div>
						</div>
						<div class="form-group"> 
							<div class="input-group">
								<input class="form-control input datepicker"
									type="text" 
									name="quote_element_start" id="quote_element_start" 
									placeholder="<?php esc_attr_e('Start', 'projectopia-core'); ?>"/>
							</div>
						</div>
						<div class="form-group"> 
							<div class="input-group">
								<input class="form-control input datepicker" 
									type="text" name="quote_element_finish" 
									id="quote_element_finish" 
									placeholder="<?php esc_attr_e('Deadline', 'projectopia-core'); ?>"/>
							</div>
						</div>
						<div class="form-group"> 
							<div class="input-group">
							<?php if ( $type == 'estimate' ) { ?>
								<input class="form-control input" type="text" 
									name="quote_element_cost" id="quote_element_cost" 
									placeholder="<?php esc_attr_e('Estimated Cost', 'projectopia-core'); ?>"/>
							<?php } else { ?>
								<input class="form-control input" type="text" 
									name="quote_element_cost" id="quote_element_cost" 
									placeholder="<?php esc_attr_e('Cost', 'projectopia-core'); ?>"/>
							<?php } ?>
							</div>
						</div>
						<div class="mt-3 d-flex align-items-center justify-content-between">
							<button class="cancel-colorbox piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
							<input type="submit" id="add_quote_element" class="metabox-add-button btn piaBtn" value="<?php esc_html_e('Add Milestone', 'projectopia-core'); ?>">
						</div>
					</div>
				</div>
			</div>	
		</div>
		<!-- Edit Milestone Colorbox -->
		<div id="edit-milestone-container" style="display:none">
			<div id="edit-milestone" class="edit_milestone">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Edit Milestone', 'projectopia-core'); ?></h3>
					<input type="hidden" class="element_id" id="edit_milestone_id" value="" />
					<div class="form-group">
						<label for="edit_milestone_title"> <?php esc_html_e('Title:', 'projectopia-core'); ?> </label>
						<div class="input-group"> 
							<input type="text" id="edit_milestone_title" value="" class="form-control input"/>
						</div>
					</div>
					<div class="form-group">
						<label for="edit_milestone_start"> <?php esc_html_e('Start Date:', 'projectopia-core'); ?> </label>
						<div class="input-group"> 
							<input type="text" id="edit_milestone_start" value="" class="form-control input datepicker"/>
						</div>
					</div>
					<div class="form-group">
						<label for="edit_milestone_end"> <?php esc_html_e('Deadline:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<input type="text" id="edit_milestone_end" value="" class="datepicker form-control input"/>
						</div>
					</div>
					<div class="form-group">
						<?php if ( $type == 'estimate' ) { ?>
							<label for="edit_milestone_cost"> <?php esc_html_e('Estimated Cost:', 'projectopia-core'); ?> </label>
						<?php } else { ?>
							<label for="edit_milestone_cost"> <?php esc_html_e('Cost:', 'projectopia-core'); ?> </label>
						<?php } ?>
						<div class="input-group">
							<input type="text" id="edit_milestone_cost" value="" class="form-control input"/>
						</div>
					</div>
					<div class="form-group">
						<label for="edit_milestone_fcost"> <?php esc_html_e('Finished Cost:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<input type="text" id="edit_milestone_fcost" value="" class="form-control input"/>
						</div>
					</div>
					<div class="form-group">
						<label for="edit_milestone_status"> <?php esc_html_e('Status:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<select class="status form-control customSelect input" name="edit_milestone_status" id="edit_milestone_status">
								<option value="pending"><?php esc_html_e('Pending', 'projectopia-core'); ?></option>
								<option value="on_hold"><?php esc_html_e('On Hold', 'projectopia-core'); ?></option>
								<option value="complete"><?php esc_html_e('Complete', 'projectopia-core'); ?></option>
							</select>
						</div>
					</div>
					<div id="edit-milestone-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button class="save-milestone metabox-add-button btn piaBtn right"><?php esc_html_e('Save', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<!-- Delete Milestone Colorbox -->
		<div id="delete-milestone-div-container" style="display:none">
			<div id="delete-milestone" class="delete-milestone-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Are you sure?', 'projectopia-core'); ?></h3>
					<p><?php esc_html_e('Deleting this milestone will also delete related tasks. Are you sure you want to do this?', 'projectopia-core'); ?></p>						
					<div id="delete-milestone-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button id="delete-milestone-button" class="delete_stage metabox-add-button btn piaBtn right"><?php esc_html_e('Delete', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<!-- Add Task Colorbox -->
		<div id="add-task-div-container" style="display:none">
			<div id="add-task-div" class="add-task-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Add Task', 'projectopia-core'); ?></h3>
					<input type="hidden" id="add_task_milestone_id" value="" />
					<input type="hidden" id="add_task_project_id" value="<?php echo esc_attr( $post->ID ); ?>" />
					<div class="form-group"> 
						<div class="input-group">
							<input class="form-control input"  type="text" id="add_task_title" 
								placeholder="<?php esc_attr_e('Task title', 'projectopia-core'); ?>"/>
						</div>
					</div>
					<div class="form-group"> 
						<div class="input-group">
							<textarea style="min-height: 150px;" class="form-control input" rows="5" id="add_task_description" 
								placeholder="<?php esc_attr_e('Task description', 'projectopia-core'); ?>"></textarea>
						</div>
					</div>
					<div class="form-group"> 
						<div class="input-group">
							<input class="form-control input datepicker" type="text" id="add_task_start" 
								placeholder="<?php esc_attr_e('Start Date', 'projectopia-core'); ?>"/>
						</div>
					</div>
					<div class="form-group"> 
						<div class="input-group">
							<input class="form-control input datepicker"
								type="text" id="add_task_finish" 
								placeholder="<?php esc_attr_e('Deadline', 'projectopia-core'); ?>"/><br /><br />
						</div>
					</div>
					<div class="form-group"> 
						<div class="input-group">
							<input class="form-control input" type="text" id="add_task_time" 
								placeholder="<?php esc_attr_e('Estimated Time (in decimal format, eg. 4.5 hours)', 'projectopia-core'); ?>"/>																		
						</div>
					</div>
					<div id="add-task-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn btn btn-primary redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button class="save-task metabox-add-button piaBtn right colorbox right"><?php esc_html_e('Add Task', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>	
		</div>
		<!-- Delete Task Colorbox -->
		<div id="delete-task-div-container" style="display:none">
			<div id="delete-task" class="delete-task-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Are you sure?', 'projectopia-core'); ?></h3>
					<p><?php esc_html_e('Deleting this task will also delete the attached subtasks. This action cannot be undone.', 'projectopia-core'); ?></p>	
					<div id="delete-task-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel_delete_task	 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button id="delete-task-button" class="delete_stage btn piaBtn right " value=""><?php esc_html_e('Delete', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<!-- Add SubTask Colorbox -->
		<div id="add-subtask-div-container" style="display:none">
			<div id="add-subtask-div" class="add-subtask-div">
				<div class="p-2">
					<h3 class="model_title"><?php esc_html_e('Add Subtask', 'projectopia-core'); ?></h3>
					<input type="hidden" id="add_subtask_milestone_id" value="" />
					<input type="hidden" id="add_subtask_parent_id" value="" />
					<input type="hidden" id="add_subtask_project_id" value="<?php echo esc_attr( $post->ID ); ?>" />
					<div class="form-group">
						<div class="input-group">
							<input class="form-control input" type="text"
								id="add_subtask_title" value="" 
								placeholder="<?php esc_attr_e('Task title', 'projectopia-core'); ?>"/>							
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
						<textarea class="form-control input" style="height:100px" id="add_subtask_description" 
							placeholder="<?php esc_attr_e('Task description', 'projectopia-core'); ?>"></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<input class="datepicker form-control input" type="text"
								id="add_subtask_start" value="" 
								placeholder="<?php esc_attr_e('Start Date', 'projectopia-core'); ?>"/>							
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<input class="datepicker form-control input" type="text"
								id="add_subtask_finish" value="" 
								placeholder="<?php esc_attr_e('Deadline', 'projectopia-core'); ?>"/>							
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<input class="form-control input" type="text"
								id="add_subtask_time" value="" 
								placeholder="<?php esc_attr_e('Estimated Time (in decimal format, eg. 4.5 hours)', 'projectopia-core'); ?>"/>			
						</div>
					</div>
					<div id="add-subtask-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button class="save-subtask metabox-add-button btn piaBtn right"><?php esc_html_e('Add Subtask', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>	
		</div>
		<!-- Delete SubTask Colorbox -->
		<div id="delete-subtask-div-container" style="display:none">
			<div id="delete-subtask" class="delete-subtask-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Are you sure?', 'projectopia-core'); ?></h3>
					<p><?php esc_html_e('Are you sure you want to delete this subtask? This action cannot be undone.', 'projectopia-core'); ?></p>
					<div id="delete-subtask-messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel_delete_task piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button id="delete-subtask-button" class="delete_stage btn piaBtn right " value=""><?php esc_html_e('Delete', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<!-- Edit Task Colorbox -->
		<div id="edit-task-div-container" style="display:none">
			<div id="edit-task" class="edit-task-div">
				<div style="padding:12px">
					<h3 class="model_title"><?php esc_html_e('Edit Task', 'projectopia-core'); ?></h3>
					<input type="hidden" name="edit_task_id" id="edit_task_id" value="" />
					<div class="form-group">
						<label for="edit_task_title"> <?php esc_html_e('Title:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<input class="form-control input" type="text" name="edit_task_title" 
								id="edit_task_title" value="" 
								placeholder="<?php esc_attr_e('Task title', 'projectopia-core'); ?>"/>							
						</div>
					</div>
					<div class="form-group">
						<label for="edit_task_description"> <?php esc_html_e('Description:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<textarea class="form-control input" style="height:100px" name="edit_task_description" 
								id="edit_task_description" 
								placeholder="<?php esc_attr_e('Task description', 'projectopia-core'); ?>"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="edit_task_start"> <?php esc_html_e('Start:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<input class="form-control input  datepicker" type="text" 
								name="edit_task_start" id="edit_task_start" value="" 
								placeholder="<?php esc_attr_e('Start Date', 'projectopia-core'); ?>"/>														
						</div>
					</div>
					<div class="form-group">
						<label for="edit_task_start"> <?php esc_html_e('Deadline:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<input class="form-control input datepicker" type="text" 
								name="edit_task_finish" id="edit_task_finish" value="" 
								placeholder="<?php esc_attr_e('Deadline', 'projectopia-core'); ?>"/>	
						</div>
					</div>
					<div class="form-group">
						<label for="edit_task_start"> <?php esc_html_e('Est Time:', 'projectopia-core'); ?> </label>
						<div class="input-group">
							<input type="text" class="form-control input" name="edit_task_time" 
								id="edit_task_time" value="" 
								placeholder="<?php esc_attr_e('Estimated Time (in decimal format, eg. 4.5 hours)', 'projectopia-core'); ?>"/>	
						</div>
					</div>
					<div id="edit_task_messages"></div>
					<div class="mt-3 d-flex align-items-center justify-content-between">
						<button class="cancel-colorbox piaBtn redColor0"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
						<button class="update-task metabox-add-button btn piaBtn right" value=""><?php esc_html_e('Save', 'projectopia-core'); ?></button>
					</div>
				</div>
			</div>	
		</div>		
	<?php }
}