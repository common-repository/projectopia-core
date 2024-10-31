<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard', 'projectopia-core'),
);
update_post_meta($assigned, 'client_logs', $client_logs);
$stickets = get_option('disable_tickets');
$team_member = get_post_meta($assigned, 'team_member', true);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item">
		<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-exclamation-triangle font-light-violet" aria-hidden="true"></i>
				<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Alerts', 'projectopia-core'); ?></span>
			</div>
		</div>
		<?php 
			$alerts = array();
			// Get Custom
			$custom_alerts = get_post_meta($assigned, 'custom_alerts', true);
			if ( ! empty($custom_alerts) ) {
				foreach ( $custom_alerts as $key => $custom_alert ) {
					if ( empty($custom_alert['cleared']) ) {
						$alerts[] = array(
							'type'     => 'message',
							'custom'   => true,
							'alert_id' => $key,
							'seen'     => $custom_alert['seen'],
							'level'    => $custom_alert['level'],
							'data'     => $custom_alert['message'],
						);
					}
				}
			}
			// Show the Alerts
			if ( ! empty($alerts) ) {
				echo '<ul class="cqpim_alerts">';
				foreach ( $alerts as $alert ) {
					if ( ! empty($alert['custom']) ) {
						if ( empty($alert['seen']) ) {
							$custom_alerts = get_post_meta($assigned, 'custom_alerts', true);
							$custom_alerts[ $alert['alert_id'] ]['seen'] = time();
							update_post_meta($assigned, 'custom_alerts', $custom_alerts);
						}
						echo '<li class="' . esc_attr( $alert['type'] ) . '"><div class="cqpim-alert cqpim-alert-' . esc_attr( $alert['level'] ) . ' alert-display">' . esc_html( $alert['data'] ) . '<a class="cqpim_alert_clear" href="#" data-client="' . esc_attr( $assigned ) . '" data-alert="' . esc_attr( $alert['alert_id'] ) . '"><i class="fa fa-times"></i></a></div></li>';
					} else {
						echo '<li class="' . esc_attr( $alert['type'] ) . '"><div class="cqpim-alert cqpim-alert-' . esc_attr( $alert['level'] ) . ' alert-display">' . esc_html( $alert['data'] ) . '</div></li>';
					}
				}
				echo '</ul>';
			} else {
				echo '<p>' . esc_html__('You have no active alerts.', 'projectopia-core') . '</p>';
			}
		?>
	</div>
	</div>
	<?php 
	/**
	 * Hooks to add content/HTML before client's dashboard blocks.
	 */
	do_action('pto_client_dashboard_before_content');
	?>
	<?php  if ( get_option('disable_invoices') != 1 ) { ?>
		<div class="cqpim-dash-item-triple grid-item">
			<div class="cqpim_block">
				<div class="cqpim_block_title">
					<div class="caption">
						<i class="fa fa-credit-card-alt font-light-violet" aria-hidden="true"></i>
						<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Outstanding Invoices', 'projectopia-core'); ?></span>
					</div>
				</div>
				<?php
					$args = array(
						'post_type'      => 'cqpim_invoice',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'orderby'        => 'date',
						'order'          => 'DESC',
					);
					$invoices = get_posts($args);
					$currency = get_option('currency_symbol');
					echo '<br /><table class="cqpim_table dash">';
					echo '<thead>';
					echo '<tr><th>' . esc_html__('Invoice ID', 'projectopia-core') . '</th><th>' . esc_html__('Owner', 'projectopia-core') . '</th><th>' . esc_html__('Due Date', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr>';
					echo '</thead>';
					echo '<tbody>';
					$i = 0;
					foreach ( $invoices as $invoice ) {
						$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
						$client_contact = get_post_meta($invoice->ID, 'client_contact', true);
						$owner = get_user_by('id', $client_contact);
						$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
						$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
						$client_details = get_post_meta($client_id, 'client_details', true);
						$client_ids = get_post_meta($client_id, 'client_ids', true);
						if ( empty($client_ids) ) {
							$client_ids = array();
						}                                   
						$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
						$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
						$tax_rate = get_option('sales_tax_rate');
						if ( ! empty($tax_rate) ) {
							$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
						} else {
							$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
						}                   
						$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
						$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
						$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
						$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
						$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
						$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
						if ( empty( $on_receipt ) && ! empty( $due ) ) {
							$due_readable = gmdate('d/m/Y', $due);
						} else {
							$due_readable = __('Due on Receipt', 'projectopia-core');
						}
						$current_date = time();
						$p_link = get_the_permalink($invoice->ID);
						$password = md5($invoice->post_password);
						$url = $p_link;
						$display_name = isset( $owner->display_name ) ? esc_html( $owner->display_name ) : '';
						if ( ! $paid ) {
							if ( $current_date > $due ) {
								if ( empty($on_receipt) ) {
									$p_status = '<span class="cqpim_button cqpim_small_button border-red font-red sbold nolink op"><strong>' . esc_html__('Overdue', 'projectopia-core') . '</strong></span>';
								} else {
									$p_status = '<span class="cqpim_button cqpim_small_button border-red font-red nolink op"><strong>' . esc_html__('On Receipt', 'projectopia-core') . '</strong></span>';
								}
							} else {
								if ( ! $sent ) {
									$p_status = '<span class="cqpim_button cqpim_small_button border-red font-red nolink op">' . esc_html__('New', 'projectopia-core') . '</span>';
								} else {
									$p_status = '<span class="cqpim_button cqpim_small_button border-amber font-amber nolink op">' . esc_html__('Outstanding', 'projectopia-core') . '</span>';
								}
							}
						} else {
							$p_status = '<span class="cqpim_button cqpim_small_button border-green font-green nolink op">' . esc_html__('Paid', 'projectopia-core') . '</span>';
						}       
						if ( $client_user_id == $user->ID && empty($paid) && ! empty($sent) || in_array($user->ID, $client_ids) && empty($paid) && ! empty($sent) ) {
							echo '<tr>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('ID', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( $url ) . '" >' . esc_html( $invoice_id ) . '</a></td>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Owner', 'projectopia-core') . '</strong>: </span> ' . esc_html( $display_name ) . '</td>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Due', 'projectopia-core') . '</strong>: </span> ' . esc_html( $due_readable ) . '</td>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Status', 'projectopia-core') . '</strong>: </span> ' . wp_kses_post( $p_status ) . '</td>';
							echo '</tr>';
							$i++;
						}
					}
					if ( $i == 0 ) {
						echo '<tr>';
						echo '<td colspan="4">' . esc_html__('You do not have any outstanding invoices', 'projectopia-core') . '</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
				?>
			</div>
		</div>
	<?php } ?>
	<div class="cqpim-dash-item-triple grid-item">
		<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-th font-light-violet" aria-hidden="true"></i>
				<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Open Projects', 'projectopia-core'); ?></span>
			</div>
		</div>
		<br />
		<table class="cqpim_table dash">
			<thead>
				<tr>
					<th><?php esc_html_e('Owner', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('Title', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('Progress', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('Links', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('Status', 'projectopia-core'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$args = array(
					'post_type'      => 'cqpim_project',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$projects = get_posts($args);
				$i = 0;
				foreach ( $projects as $project ) { 
					$url = get_the_permalink($project->ID); 
					$summary = $url . '?pto-page=summary&sub=updates';
					$contract = $url . '?pto-page=contract';
					$project_details = get_post_meta($project->ID, 'project_details', true); 
					$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
					$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
					$owner = get_user_by('id', $client_contact);
					$client_details = get_post_meta($client_id, 'client_details', true);
					$client_ids = get_post_meta($client_id, 'client_ids', true);                                
					$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
					$sent = isset($project_details['sent']) ? $project_details['sent'] : ''; 
					$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : ''; 
					$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : ''; 
					$closed = isset($project_details['closed']) ? $project_details['closed'] : ''; 
					$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
					$str_finish_date = str_replace('/','-', $finish_date);
					$unix_finish_date = strtotime($str_finish_date);
					$current_date = time();
					$days_to_due = round(abs($current_date - $unix_finish_date) / 86400);
					$project_elements = get_post_meta($project->ID, 'project_elements', true); 
					$contract_status = get_post_meta($project->ID, 'contract_status', true); 
					if ( empty($project_elements) ) {
						$project_elements = array();
					}
					$task_count = 0;
					$task_total_count = 0;
					$task_complete_count = 0;
					foreach ( $project_elements as $element ) {

						if ( empty( $element ) || empty( $element['id'] ) ) {
							continue;
						}

						$args = array(
							'post_type'      => 'cqpim_tasks',
							'posts_per_page' => -1,
							'meta_key'       => 'milestone_id',
							'meta_value'     => $element['id'],
							'orderby'        => 'date',
							'order'          => 'ASC',
						);
						$tasks = get_posts($args);  
						foreach ( $tasks as $task ) {
							$task_total_count++;
							$task_details = get_post_meta($task->ID, 'task_details', true);
							if ( ! empty($task_details['status']) && $task_details['status'] != 'complete' ) {
								$task_count++;
							}
							if ( ! empty($task_details['status']) && $task_details['status'] == 'complete' ) {
								$task_complete_count++;
							}
						}
					}
					if ( $task_total_count != 0 ) {
						$pc_per_task = 100 / $task_total_count;
						$pc_complete = $pc_per_task * $task_complete_count;
					} else {
						$pc_complete = 0;
					}
					if ( ! $closed ) {
						if ( ! $signoff ) {
							if ( $contract_status == 1 ) {
								if ( ! $confirmed ) {
									if ( ! $sent ) {
										$p_status = '<span class="cqpim_button cqpim_small_button nolink op border-red font-red">' . esc_html__('New', 'projectopia-core') . '</span>';
									} else {
										$p_status = '<span class="cqpim_button cqpim_small_button nolink op border-amber font-amber">' . esc_html__('Awaiting Contracts', 'projectopia-core') . '</span>';
									}
								} else {
									$p_status = '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
								}
							} else {
								$p_status = '<span class="cqpim_button cqpim_small_button nolink op border-green font-green">' . esc_html__('In Progress', 'projectopia-core') . '</span>';
							}
						} else {
							$p_status = '<span class="cqpim_button cqpim_small_button nolink op border-blue font-blue">' . esc_html__('Signed Off', 'projectopia-core') . '</span>';
						}
					} else {
						$p_status = '<span class="cqpim_button cqpim_small_button nolink op border-grey-cascade font-grey-cascade">' . esc_html__('Closed', 'projectopia-core') . '</span>';
					}
					if ( empty($client_ids) ) { $client_ids = array(); }
					if ( $client_user_id == $user->ID && empty($closed) || in_array($user->ID, $client_ids) && empty($closed) ) {
						if ( $contract_status == 2 || $contract_status == 1 && ! empty($sent) || $contract_status == 1 && ! empty($confirmed) ) {
							?>						
							<tr>
								<td><span class="nodesktop"><strong><?php esc_html_e('Owner', 'projectopia-core'); ?></strong>: </span> <?php echo isset($owner->display_name) ? esc_html( $owner->display_name ) : ''; ?></td>
								<td><span class="nodesktop"><strong><?php esc_html_e('Title', 'projectopia-core'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo esc_url( $summary ); ?>"><?php echo esc_html( $project->post_title ); ?></a></td>
								<td><span class="nodesktop"><strong><?php esc_html_e('Progress', 'projectopia-core'); ?></strong>: </span> <?php echo number_format( (float)$pc_complete, 2, '.', ''); ?>%</td>
								<?php if ( $contract_status == 1 ) { ?>
									<td><span class="nodesktop"><strong><?php esc_html_e('Contract', 'projectopia-core'); ?></strong>: </span> <a class="cqpim-link" href="<?php echo esc_url( $contract ); ?>"><?php esc_html_e('View Contract', 'projectopia-core'); ?></a></td>
								<?php } else { ?>
									<td></td>
								<?php } ?>
								<td><span class="nodesktop"><strong><?php esc_html_e('Status', 'projectopia-core'); ?></strong>: </span> <?php echo wp_kses_post( $p_status ); ?></td>
							</tr>
							<?php 
							$i++;
						}
					}
				} 
				if ( $i == 0 ) {
					echo '<tr><td colspan="5">' . esc_html__('You do not have any open projects', 'projectopia-core') . '</td></tr>';
				}
				?>
			</tbody>
		</table>
		</div>
	</div>
	<?php if ( get_option('enable_quotes') == 1 ) { ?>
	<div class="cqpim-dash-item-double grid-item">
		<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-file-text font-light-violet" aria-hidden="true"></i>
				<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Open Quotes / Estimates', 'projectopia-core'); ?></span>
			</div>
		</div>
		<br />
		<table class="cqpim_table dash">
			<thead>
				<tr>
					<th><?php esc_html_e('Title', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('Status', 'projectopia-core'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$args = array(
					'post_type'      => 'cqpim_quote',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$quotes = get_posts($args);
				$i = 0;
				foreach ( $quotes as $quote ) { 
					$url = get_the_permalink($quote->ID); 
					$quote_details = get_post_meta($quote->ID, 'quote_details', true); 
					$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
					$client_details = get_post_meta($client_id, 'client_details', true);
					$client_ids = get_post_meta($client_id, 'client_ids', true);
					if ( empty($client_ids) ) {
						$client_ids = array();
					}                               
					$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
					$sent = isset($quote_details['sent']) ? $quote_details['sent'] : ''; 
					$confirmed = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : ''; 
					if ( ! $confirmed ) {
						if ( ! $sent ) {
							$p_status = '<span class="cqpim_button cqpim_small_button nolink op font-red border-red">' . esc_html__('Not Sent', 'projectopia-core') . '</span>';
						} else {
							$p_status = '<span class="cqpim_button cqpim_small_button nolink op font-amber border-amber">' . esc_html__('New', 'projectopia-core') . '</span>';
						}
					} else {
						$p_status = '<span class="cqpim_button cqpim_small_button nolink op font-green border-green">' . esc_html__('Accepted', 'projectopia-core') . '</span>';
					}

					$quote_link = '<a class="cqpim-link" href="' . esc_url( $url ) . '?pto-page=quote">' . $quote->post_title . '</a>';
					$quote_link = apply_filters( 'pto_open_quotes_title', $quote_link, $quote );

					$p_status = apply_filters( 'pto_open_quotes_status', $p_status, $quote );

					$sent = apply_filters( 'pto_open_quotes_sent_flag', $sent );
					if ( ( $client_user_id == $user->ID || in_array($user->ID, $client_ids) ) && empty($confirmed) && ! empty($sent) ) {
					?>						
						<tr>	
							<td><span class="nodesktop"><strong><?php esc_html_e('Title', 'projectopia-core'); ?></strong>: </span> <?php echo wp_kses_post( $quote_link ); ?></td>
							<td><span class="nodesktop"><strong><?php esc_html_e('Status', 'projectopia-core'); ?></strong>: </span> <?php echo wp_kses_post( $p_status ); ?></td>
						</tr>
					<?php 
						$i++;
					}
				} 
				if ( $i == 0 ) {
					echo '<tr><td colspan="2">' . esc_html__('You do not have any open quotes', 'projectopia-core') . '</td></tr>';
				}
				?>
			</tbody>
		</table>					
		</div>
	</div>
	<?php } ?>
	<?php if ( empty($stickets) && pto_has_addon_active_license( 'pto_st', 'tickets' ) ) { ?>
		<div class="cqpim-dash-item-triple grid-item">
			<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<i class="fa fa-life-ring font-light-violet" aria-hidden="true"></i>
					<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Open Support Tickets', 'projectopia-core'); ?></span>
				</div>
				<div class="actions">
					<a class="cqpim_button cqpim_small_button border-green-sharp font-light-violet rounded_2 sbold" href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=add-support-ticket'; ?>"><?php esc_html_e('Add Support Ticket', 'projectopia-core'); ?></a>
				</div>
			</div>
			<br />
				<?php 
				$user = wp_get_current_user();
				$args = array(
					'post_type'      => 'cqpim_support',
					'posts_per_page' => -1,
					'post_status'    => 'private',
					'author__in'     => $client_ids_untouched,
				); 
				$tickets = get_posts($args);
				$total_tickets = count($tickets);
				if ( $tickets ) {
					$i = 0;
					echo '<table class="cqpim_table files dash">';
					echo '<thead><tr><th>' . esc_html__('Ticket Title', 'projectopia-core') . '</th><th>' . esc_html__('Owner', 'projectopia-core') . '</th><th>' . esc_html__('Priority', 'projectopia-core') . '</th><th>' . esc_html__('Last Updated', 'projectopia-core') . '</th></tr></thead>';
					echo '<tbody>';
					foreach ( $tickets as $ticket ) {
						$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
						$owner = get_user_by('id', $ticket->post_author);
						$display_name = isset( $owner->display_name ) ? esc_html( $owner->display_name ) : '';
						$support_ticket_status = get_option('support_status');
						$key_arr = $support_ticket_status['key'];
						$value_arr = $support_ticket_status['value'];
						$color_arr = $support_ticket_status['color'];
			
						$pos = array_search($ticket_status, $key_arr);
						$val = $value_arr[ $pos ];
						$col = $color_arr[ $pos ];

						$tstatus = '<span class="cqpim_button cqpim_small_button op" style="border: 1px solid '.esc_attr($col).'; color:'.esc_attr($col).'">' . $val . '</span>';


						$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
						$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
						if ( is_numeric($ticket_updated) ) { $ticket_updated = wp_date(get_option('cqpim_date_format') . ' H:i', $ticket_updated); } else { $ticket_updated = $ticket_updated; }

						$priority = '';
						if ( ! empty( $ticket_priority ) ) {
							$support_ticket_priorities = get_option( 'support_ticket_priorities');
							if ( ! empty( $support_ticket_priorities[ $ticket_priority ] ) ) {
								$color_code = $support_ticket_priorities[ $ticket_priority ];
								$priority = '<span style="text-transform:capitalize;border:solid 1px '. esc_attr( $color_code ) .' !important;color:'. esc_attr( $color_code ) .' !important" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . esc_html( $ticket_priority ) . '</span>';
							} else {
								$priority = '<span style="text-transform:capitalize;" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . esc_html( $ticket_priority ) . '</span>';
							}
						}

						if ( $ticket_status != 'resolved' ) {
							echo '<tr>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Title', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( get_the_permalink($ticket->ID) ) . '">' . esc_html( $ticket->post_title ) . '</a></td>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Owner', 'projectopia-core') . '</strong>: </span> ' . esc_html( $display_name ) . '</td>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Priority', 'projectopia-core') . '</strong>: </span> ' . wp_kses_post( $priority ) . '</td>';
							echo '<td><span class="nodesktop"><strong>' . esc_html__('Updated', 'projectopia-core') . '</strong>: </span> ' . esc_html( $ticket_updated ) . '</td>';
							echo '</tr>';
							$i++;
						}
					}
					if ( empty( $i ) ) {
						echo '<tr>';
						echo '<td colspan="4">' . esc_html__('You do not have any open support tickets', 'projectopia-core') . '</td>';
						echo '</tr>'; 
					}
					echo '</tbody>';
					echo '</table>';
				} else {
					echo '<table class="cqpim_table files dash">';
					echo '<thead><tr><th>' . esc_html__('Ticket Title', 'projectopia-core') . '</th><th>' . esc_html__('Owner', 'projectopia-core') . '</th><th>' . esc_html__('Priority', 'projectopia-core') . '</th><th>' . esc_html__('Last Updated', 'projectopia-core') . '</th></tr></thead>';
					echo '<tbody>'; 
					echo '<tr>';
					echo '<td colspan="4">' . esc_html__('You do not have any open support tickets', 'projectopia-core') . '</td>';
					echo '</tr>';   
					echo '</tbody>';
					echo '</table>';                    
				}
				?>
				<div class="clear"></div>
			</div>
		</div>
	<?php } ?>
	<div class="cqpim-dash-item-double grid-item">
		<div class="cqpim_block" styled="max-height:500px; overflow:auto">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-tasks font-light-violet" aria-hidden="true"></i>
				<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Project Updates', 'projectopia-core'); ?></span>
			</div>
		</div>
		<br />
		<?php 
			$args = array(
				'post_type'      => 'cqpim_project',
				'post_status'    => 'private',
				'posts_per_page' => -1,
			);
			$projects = get_posts($args);
			$updates = array();
			$i = 0;
			foreach ( $projects as $project ) {
				$project_details = get_post_meta($project->ID, 'project_details', true);
				$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
				$client_details = get_post_meta($client_id, 'client_details', true);
				$client_ids = get_post_meta($client_id, 'client_ids', true);                            
				$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
				if ( empty($client_ids) ) { 
					$client_ids = array();
				}
				if ( $client_user_id == $user->ID || in_array( $user->ID, $client_ids ) ) {
					$project_progress = get_post_meta($project->ID, 'project_progress', true);
					$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

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

						$i++;
					}

					pto_project_updates_element( $project_updates );
				}
					/*$i = 0;
					foreach ( $project_updates as $update ) {
						$date_stamp = time();
						if ( isset($update['date']) && ! empty($update['date']) ) {
							if ( ! is_numeric($update['date']) ) {
								$str_deadline = str_replace('/','-', $update['date']);
								$date_stamp = strtotime($str_deadline);
							} else {
								$date_stamp = $update['date'];
							}   
						}				
						$updates[ $date_stamp . $i ] = array(
							'pid'    => $project->ID,
							'by'     => @$update['by'],
							'date'   => @$update['date'],
							'update' => @$update['update'],
						);
						$i++;
					}*/
				//}
			}
			/*ksort($updates);
			$updates = array_reverse($updates);
			echo '<ul class="project_summary_progress">';
			foreach ( $updates as $pupdate ) {
				$project_details = get_post_meta($pupdate['pid'], 'project_details', true);
				$url = get_the_permalink($pupdate['pid']);
				$project_obj = get_post($pupdate['pid']);
				$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
				if ( is_numeric($pupdate['date']) ) { $pupdate['date'] = wp_date(get_option('cqpim_date_format') . ' H:i', $pupdate['date']); } else { $pupdate['date'] = $pupdate['date']; }
				echo '<li style="margin-bottom:0">'; ?>
					<div class="timeline-entry">
						<?php if ( empty($avatar) ) {
							echo '<div class="update-who">';
							echo get_avatar( pto_get_user_id_by_display_name($pupdate['by']), 60, '', false, array( 'force_display' => true ) );
							echo '</div>';
						} ?>
						<div class="update-data" <?php echo (empty($avatar) ? '' : 'style="width:100%; float:none"');?>>
							<div class="timeline-body-arrow"> </div>
							<div class="timeline-by font-blue-madison sbold"><?php echo $pupdate['by']; ?></div>
							<div class="clear"></div>
							<div class="timeline-update font-grey-cascade"><a class="cqpim-link font-grey-cascade" href="<?php echo esc_url( $url ); ?>?pto-page=summary&sub=updates"><?php echo $project_obj->post_title; ?></a> - <?php echo $pupdate['update']; ?></div>
							<div class="clear"></div>
							<div class="timeline-date font-grey-cascade"><?php echo $pupdate['date']; ?></div>
						</div>
						<div class="clear"></div>
					</div>
				<?php echo '</li>';
			}
			echo '</ul>';*/
			if ( empty( $i ) ) {
				echo '<div>' . esc_html__('There are no project updates to show.', 'projectopia-core') . '</div>';
			}
		?>
		</div>
	</div>
	<?php 
	/**
	 * Hooks to add content/HTML after client's dashboard blocks.
	 */
	do_action('pto_client_dashboard_after_content');
	?>
</div>