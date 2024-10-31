<div class="dd-milestone 
				<?php 
				if ( ! empty( $milestone_toggles[ $user->ID ][ $milestone_id ] )
					&& $milestone_toggles[ $user->ID ][ $milestone_id ] == 'off' ) {
						echo 'ms-toggled'; } ?>" id="milestone-<?php echo esc_attr( $milestone_id ); ?>">
					<input type="hidden" class="element_to_add_weight" 
					name="element_to_add_weight[<?php echo esc_attr( $milestone_id ); ?>]" 
					id="element_to_add_weight[<?php echo esc_attr( $milestone_id ); ?>]" 
					data-msid="<?php echo esc_attr( $milestone_id ); ?>" 
					value="<?php echo esc_attr( $element_to_add['weight'] ); ?>" />
	<div class="dd-milestone-title">
				<span class="ms-title" <?php if ( ! empty($project_colours['ms_colour']) ) { ?> style="color:<?php echo esc_attr( $project_colours['ms_colour'] ); ?>"<?php } ?>><?php esc_html_e('Milestone:', 'projectopia-core'); ?></span>
				<span class="ms-title" id="ms_title_<?php echo esc_attr( $milestone_id ); ?>"><?php echo esc_html( $element_to_add['title'] ); ?></span>
				<div class="dd-milestone-actions">
					<?php
					if ( current_user_can('cqpim_edit_project_milestones') ) { ?>

					<div class="d-inline dropdown ideal-littleBit-action ideal-littleBit-action  dropdown-edit dropBottomRight">
						<button class="btn px-3" type="button"
							data-toggle="dropdown" aria-haspopup="true"
							aria-expanded="false">
							<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/verticale-helip.svg' ); ?>" alt=""
								class="img-fluid" />
						</button>

						<div class="dropdown-menu">

							<button data-ms="<?php echo esc_attr( $milestone_id ); ?>" 
									data-project="<?php echo esc_attr( $quote_id ); ?>" 
									value="<?php echo esc_attr( $milestone_id ); ?>"
									class="add_task dropdown-item d-flex align-items-center"
									type="button">
								<?php esc_html_e('Add Task', 'projectopia-core'); ?>
							</button>

							

							<button value="<?php echo esc_attr( $milestone_id ); ?>"
									class="edit-milestone dropdown-item d-flex align-items-center"
									type="button">
								<?php esc_html_e('Edit Milestone', 'projectopia-core'); ?>
							</button>

							<button class="delete_stage_conf dropdown-item d-flex align-items-center"
								type="button"
								data-id="<?php echo esc_attr( $milestone_id ); ?>" 
								value="<?php echo esc_attr( $milestone_id ); ?>">
								<?php esc_html_e('Delete Milestone', 'projectopia-core'); ?>
							</button>
						</div>
					</div>

					<button class="dd-reorder cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" 
						value="<?php echo esc_attr( $milestone_id ); ?>" 
						title="<?php esc_html_e('Reorder Milestone', 'projectopia-core'); ?>">
						<i class="fa fa-sort" aria-hidden="true"></i>
					</button>

					<?php } ?>
					<button id="toggle-<?php echo esc_attr( $milestone_id ); ?>" 
						class="toggle_tasks cqpim_button cqpim_small_button font-white bg-blue-sharp op rounded_2 cqpim_tooltip" 
						data-ms="<?php echo esc_attr( $milestone_id ); ?>" 
						data-project="<?php echo esc_attr( $quote_id ); ?>" 
						value="<?php if ( ! empty($milestone_toggles[ $user->ID ][ $milestone_id ]) && $milestone_toggles[ $user->ID ][ $milestone_id ] == 'off' ) { 
							echo 'show';  } else { echo 'hide';  } ?>"
						title="<?php esc_html_e('Toggle Tasks', 'projectopia-core'); ?>">
						<i class="fa <?php if ( ! empty($milestone_toggles[ $user->ID ][ $milestone_id ]) && $milestone_toggles[ $user->ID ][ $milestone_id ] == 'off' ) {
							echo 'fa-chevron-circle-down'; } else { echo 'fa-chevron-circle-up'; } ?>" aria-hidden="true">
						</i>
					</button>
				</div>

				<div class="dd-milestone-status mt-2">
					<?php echo wp_kses_post( $milestone_status_string ); ?>
				</div>

				<div class="clear"></div>
				<div class="dd-milestone-info mileStone-content-taskBar d-block flex-wrap d-md-flex justify-content-between align-items-center">
					<div class="mileStone-content-deadline d-block d-sm-flex">

						<?php
							if ( current_user_can( 'cqpim_view_project_financials' ) && ! empty( $element_to_add['cost'] ) ) { ?>
								<div class="mileStone-content-singleDeadline d-flex">
									<div class="mileStone-content-icon">
										<img src="<?php echo esc_attr( PTO_PLUGIN_URL . '/assets/admin/img/usacoin.svg' ); ?>" class="icon img-fluid mr-2" />
									</div>
									<div class="mileStone-content-info">
										<h5 class="mb-1"><?php esc_html_e('Budget', 'projectopia-core'); ?></h5>
										<p id="ms_cost_<?php echo esc_attr( $milestone_id ); ?>"  class="mb-0"><?php echo esc_html( pto_calculate_currency($quote_id, $element_to_add['cost']) ); ?></p>
									</div>
								</div>
						<?php } ?>

						<?php if ( ! empty($element_to_add['start']) ) { ?>
							<div class="mileStone-content-singleDeadline d-flex">
								<div class="mileStone-content-icon">
									<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/clander.svg' ); ?>" class="icon img-fluid mr-2" />
								</div>
								<div class="mileStone-content-info">
									<h5 class="mb-1"><?php esc_html_e('Start Date', 'projectopia-core'); ?></h5>
									<p id="ms_start_<?php echo esc_attr( $milestone_id ); ?>" class="mb-0"><?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element_to_add['start']) ); ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if ( ! empty($element_to_add['deadline']) ) { ?>
							<div class="mileStone-content-singleDeadline d-flex">
								<div class="mileStone-content-icon">
									<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/clander.svg' ); ?>" class="icon img-fluid mr-2" />
								</div>
								<div class="mileStone-content-info">
									<h5 class="mb-1"><?php esc_html_e('Deadline', 'projectopia-core'); ?></h5>
									<p class="mb-0" id="ms_deadline_<?php echo esc_attr( $milestone_id ); ?>"><?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element_to_add['deadline']) ); ?></p>
								</div>
							</div>
						<?php } ?>
					</div>
					
				</div>

				<div class="clear"></div>
			</div>
			<div class="dd-tasks" data-ms="<?php echo esc_attr( $milestone_id ); ?>">
				
			</div>
</div>