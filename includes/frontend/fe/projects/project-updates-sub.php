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
					<span class="caption-subject font-green-sharp sbold"><?php echo esc_html( $p_title ); ?></span>
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
					<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('Project Status', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<table class="cqpim_table sum-status">
				<tbody>
					<?php if ( $contract_status == 1 || $deposit && $deposit != 'none' && get_option('disable_invoices') != 1 ) { ?>
						<tr>
							<th style="border-top:0;" colspan="2"><?php esc_html_e('Prerequisites', 'projectopia-core'); ?></th>
						</tr>
						<?php $p_status = ( ! empty( $sent ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
						<?php $checked = get_option('enable_project_contracts'); 
						if ( ! empty($checked) && empty($client_contract) ) { ?>
							<tr>
								<td><?php esc_html_e('Contract Sent', 'projectopia-core'); ?></td>
								<td><?php echo wp_kses_post( $p_status ); ?></td>
							</tr>
							<?php $p_status = ( ! empty( $confirmed ) ) ? '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('Complete', 'projectopia-core') . '</span>' : '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Pending', 'projectopia-core') . '</span>'; ?>
							<tr>
								<td><?php esc_html_e('Contract Signed', 'projectopia-core'); ?></td>
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
					<?php } ?>
					<tr>
						<th colspan="2"><?php esc_html_e('Milestones', 'projectopia-core'); ?></th>
					</tr>
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
						<td><?php echo wp_kses_post( $element['title'] ); ?></td>
						<td><?php echo wp_kses_post( $p_status ); ?></td>
					</tr>
					<?php } ?>
					<tr>
						<th colspan="2"><?php esc_html_e('Completion', 'projectopia-core'); ?></th>
					</tr>	
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
					<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('Project Updates', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<?php if ( $project_progress ) {
				$project_progress = array_reverse($project_progress);
				echo '<ul style="max-height:500px; overflow:auto" class="project_summary_progress">';
				foreach ( $project_progress as $progress ) {
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
									<div class="timeline-by font-blue-madison sbold"><?php echo wp_kses_post( $progress['by'] ); ?></div>
									<div class="clear"></div>
									<div class="timeline-update font-grey-cascade"><?php echo wp_kses_post( $progress['update'] ); ?></div>
									<div class="clear"></div>
									<div class="timeline-date font-grey-cascade"><?php echo esc_html( $progress['date'] ); ?></div>
								</div>
								<div class="clear"></div>
							</div>
						</li>						
					<?php
				}
				echo '</ul>';
			} ?>
		</div>
	</div>		
</div>