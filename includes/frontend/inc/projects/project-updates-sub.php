<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$p_title = get_the_title();
$p_title = str_replace('Private:', '', $p_title);
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	/* translators: %1$s: Project ID, %2$s: Project Title */
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Update / Status Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($assigned, 'client_logs', $client_logs);
$contract_status = get_post_meta($post->ID, 'contract_status', true); 
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-double grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"><?php echo esc_html( $p_title ); ?></span>
				</div>	
			</div>
			<ul class="project_stats">
				<li><strong><span class="project_stat_head"><?php esc_html_e('Open Tasks: ' , 'projectopia-core') ?></span></strong><br /><span class="project_stat"><?php echo esc_html( $task_count ); ?></span></li>
				<li><strong><span class="project_stat_head"><?php esc_html_e('Complete: ' , 'projectopia-core') ?></span></strong><br /><span class="project_stat"><?php echo number_format( (float)$pc_complete, 2, '.', ''); ?>%</span></li>
				<li><strong><span class="project_stat_head"><?php esc_html_e('Days to Launch!', 'projectopia-core'); ?></span></strong><br /><span class="project_stat"><?php echo esc_html( $days_to_due ); ?></span></li>
			</ul>
		</div>
	</div>
	<div class="cqpim-dash-item-triple grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Project Status', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<?php if ( $contract_status == 1 || $deposit && $deposit != 'none' && get_option('disable_invoices') != 1 ) { ?>
				<table class="cqpim_table sum-status">
					<thead>
						<tr>
							<th style="border-top:0;" colspan="2"><?php esc_html_e('Prerequisites', 'projectopia-core'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php $p_status = ( ! empty( $sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
						<?php $checked = get_option('enable_project_contracts'); 
						if ( ! empty($checked) && empty($client_contract) ) { ?>
							<tr>
								<td><?php esc_html_e('Contract Sent', 'projectopia-core'); ?></td>
								<td><?php echo wp_kses_post( $p_status ); ?></td>
							</tr>
							<?php $p_status = ( ! empty( $confirmed ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
							<tr>
								<td><a href="<?php echo esc_url( get_the_permalink($post->ID) ) . '?pto-page=contract'; ?>"><?php esc_html_e('Contract Signed', 'projectopia-core'); ?></a></td>
								<td><?php echo wp_kses_post( $p_status ); ?></td>
							</tr>
						<?php } ?>
						<?php if ( $deposit && $deposit != 'none' && get_option('disable_invoices') != 1 ) { ?>
						<?php $p_status = ( ! empty( $deposit_invoice_id ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
						<tr>
							<td><?php esc_html_e('Deposit Invoice Created', 'projectopia-core'); ?></td>
							<td><?php echo wp_kses_post( $p_status ); ?></td>
						</tr>
						<?php $p_status = ( ! empty( $deposit_sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
						<tr>
							<td><?php esc_html_e('Deposit Invoice Sent', 'projectopia-core'); ?></td>
							<td><?php echo wp_kses_post( $p_status ); ?></td>
						</tr>	
						<?php $p_status = ( ! empty( $deposit_paid ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
						<tr>
							<td><?php esc_html_e('Deposit Paid', 'projectopia-core'); ?></td>
							<td><?php echo wp_kses_post( $p_status ); ?></td>
						</tr>						
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
			<table class="cqpim_table sum-status">
				<thead>
					<tr>
						<th colspan="2"><?php esc_html_e('Milestones', 'projectopia-core'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$ordered = array();
					$i = 0;
					$mi = 0;
					foreach ( $project_elements as $key => $element ) {
						$weight = isset($element['weight']) ? $element['weight'] : $mi;
						$ordered[ $weight ] = $element;
						$mi++;
					}
					ksort($ordered);                        
					foreach ( $ordered as $element ) { 
					$p_status = isset($element['status']) ? $element['status'] : ''; ?>
					<?php $p_status = (  $p_status == 'complete' ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
					<tr>
						<td><?php echo esc_html( $element['title'] ); ?></td>
						<td><?php echo wp_kses_post( $p_status ); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<table class="cqpim_table sum-status">
				<thead>
					<tr>
						<th colspan="2"><?php esc_html_e('Completion', 'projectopia-core'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php $p_status = ( ! empty( $signoff ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
					<tr>
						<td><?php esc_html_e('Signed Off / Launched', 'projectopia-core'); ?></td>
						<td><?php echo wp_kses_post( $p_status ); ?></td>
					</tr>
					<?php if ( get_option('disable_invoices') != 1 && get_option('invoice_workflow') != 1 ) { ?>
					<?php $p_status = ( ! empty( $completion_invoice_id ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
					<tr>
						<td><?php esc_html_e('Completion Invoice Created', 'projectopia-core'); ?></td>
						<td><?php echo wp_kses_post( $p_status ); ?></td>
					</tr>
					<?php $p_status = ( ! empty( $completion_sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
					<tr>
						<td><?php esc_html_e('Completion Invoice Sent', 'projectopia-core'); ?></td>
						<td><?php echo wp_kses_post( $p_status ); ?></td>
					</tr>	
					<?php $p_status = ( ! empty( $completion_paid ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
					<tr>
						<td><?php esc_html_e('Completion Invoice Paid', 'projectopia-core'); ?></td>
						<td><?php echo wp_kses_post( $p_status ); ?></td>
					</tr>	
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="cqpim-dash-item-double grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Project Updates', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<?php if ( $project_progress ) {
				$project_progress = array_reverse($project_progress);
				echo '<ul stydle="max-height:500px; overflow:auto" class="project_summary_progress">';
				//Group the project updates as per day.
				$project_updates = [];

				//error_log( print_r( $project_progress, true ) );

				foreach ( (array) $project_progress as $project_update ) {

					//If project update date is empty then continue.
					if ( empty( $project_update['date'] ) ) {
						continue;
					}

					$update_timestamp = $project_update['date'];
					$update_date = $update_time = '';

					// Check if date is unix timestemps.
					if ( ! is_numeric( $update_timestamp ) ) {
						$update_timestamp = strtotime( $update_timestamp );
					}
					
					$date_stamp = gmdate( 'Y-m-d', $update_timestamp );
					$date_key = strtotime( $date_stamp . ' 00:00:00' );
						
					$update_date = gmdate( 'M d Y', $update_timestamp );
					$update_time = wp_date( 'h:i A', $update_timestamp );

					//Calculate date and time line for updates.
					$today = new DateTime( 'today' );
					$modified_date = new DateTime( gmdate( 'Y-m-d', $update_timestamp ) );
					$today->setTime( 0, 0, 0 );
					$modified_date->setTime( 0, 0, 0 );

					//Make date label for updates group.
					if ( $today->diff( $modified_date )->days === 0 ) {
						$update_date = __( 'Today', 'projectopia-core' );
					} elseif ( $today->diff( $modified_date )->days === -1 ) {
						$update_date = __( 'Yesterday', 'projectopia-core' );
					}

					//Set avatar.
					if ( empty( $avatar ) ) {
						$profile_avatar = get_avatar(
							pto_get_user_id_by_display_name( $project_update['by'] ),
							40,
							'',
							false,
							[
								'force_display' => true,
								'class'         => 'img-fluid',
							]
						);

						if ( empty( $profile_avatar ) ) {
							$profile_avatar = sprintf(
								'<img src="%s" alt="%s" class="img-fluid" />',
								PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
								esc_html( $project_update['by'] )
							);
						}
					}

					$project_updates[ $date_key ]['date'] = $update_date;

					//Group updates day wise.
					$project_updates[ $date_key ][] = [
						'member_name'    => $project_update['by'],
						'avatar'         => $profile_avatar,
						'time'           => $update_time,
						'update_message' => $project_update['update'],
						'timestamp'      => $update_timestamp,
					];
				}

				pto_project_updates_element( $project_updates );

				/*foreach ( $project_progress as $progress ) {
					if ( is_numeric($progress['date']) ) { $progress['date'] = wp_date(get_option('cqpim_date_format') . ' H:i', $progress['date']); } else { $progress['date'] = $progress['date']; }     
					?>
						<li style="margin-bottom:0">
							<div class="timeline-entry">
								<?php if ( empty($avatar) ) {
									echo '<div class="update-who">';
									echo get_avatar( pto_get_user_id_by_display_name($progress['by']), 60, '', false, array( 'force_display' => true ) );
									echo '</div>';
								} ?>
								<?php if ( empty($avatar) ) { ?>
									<div class="update-data">
								<?php } else { ?>
									<div style="width:100%; float:none" class="update-data">
								<?php } ?>
									<div class="timeline-body-arrow"> </div>
									<div class="timeline-by font-blue-madison sbold"><?php echo $progress['by']; ?></div>
									<div class="clear"></div>
									<div class="timeline-update font-grey-cascade"><?php echo $progress['update']; ?></div>
									<div class="clear"></div>
									<div class="timeline-date font-grey-cascade"><?php echo $progress['date']; ?></div>
								</div>
								<div class="clear"></div>
							</div>
						</li>						
					<?php
				}*/
				echo '</ul>';
			} ?>
		</div>
	</div>		
</div>