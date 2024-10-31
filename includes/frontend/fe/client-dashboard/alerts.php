<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-exclamation-triangle font-green-sharp" aria-hidden="true"></i> <?php esc_html_e('Alerts', 'projectopia-core'); ?>
		</div>
	</div>
	<div class="cqpim-dash-item-inside">
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
		if ( get_option('disable_invoices') != 1 ) {
		// Check Invoices
		$args = array(
			'post_type'      => 'cqpim_invoice',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		);
		$invoices = get_posts($args);
		$outstanding_invoices = 0;
		$overdue_invoices = 0;
		foreach ( $invoices as $invoice ) {
			$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
			$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_ids = get_post_meta($client_id, 'client_ids', true);    
			if ( empty($client_ids) ) {
				$client_ids = array();
			}
			$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
			$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
			$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
			$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
			$now = time();
			$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';                          
			if ( $client_user_id == $user->ID && empty($paid) && ! empty($sent) || in_array($user->ID, $client_ids) && empty($paid) && ! empty($sent) ) {
				$outstanding_invoices++;
				if ( $due < $now ) {
					$overdue_invoices++;
				}
			}                           
		}
		if ( $overdue_invoices != 0 ) {
			$alerts[] = array(
				'type'  => 'oinvoice',
				'level' => 'danger',
				/* translators: %s: No of overdue invoices */
				'data'  => sprintf(_n('You have %s overdue invoice', 'You have %s overdue invoices', $overdue_invoices, 'projectopia-core'), $overdue_invoices),
			);
		}
		if ( $outstanding_invoices != 0 ) {
			if ( $outstanding_invoices == 1 ) {
				$alerts[] = array(
					'type'  => 'invoice',
					'level' => 'warning',
					'data'  => __('You have 1 outstanding invoice', 'projectopia-core'),
				);                      
			} else {
				$alerts[] = array(
					'type'  => 'invoice',
					'level' => 'warning',
					/* translators: %s: Number of outstanding invoices */
					'data'  => sprintf(esc_html__('You have %s outstanding invoices', 'projectopia-core'), $outstanding_invoices),
				);
			}
		}
		}
		if ( get_option('enable_quotes') == 1 ) {
		// Check Quotes
		$args = array(
			'post_type'      => 'cqpim_quote',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$quotes = get_posts($args);
		$open_quotes = 0;
		foreach ( $quotes as $quote ) {
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
			if ( $client_user_id == $user->ID && empty($confirmed) && ! empty($sent) || in_array($user->ID, $client_ids) && empty($confirmed) && ! empty($sent) ) {
				$open_quotes++;
			}
		}
		if ( $open_quotes != 0 ) {
			$alerts[] = array(
				'type'  => 'quote',
				'level' => 'info',
				/* translators: %s: No of alerts */
				'data'  => sprintf(_n('You have %s new quote/estimate that requires your attention', 'You have %s new quotes/estimates that require your attention', $open_quotes, 'projectopia-core'), $open_quotes),
			);
		}
		}
		// Check Projects
		$args = array(
			'post_type'      => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status'    => 'private',
		);
		$projects = get_posts($args);
		$unsigned_contracts = 0;
		foreach ( $projects as $project ) {
			$project_details = get_post_meta($project->ID, 'project_details', true); 
			$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
			$client_contract = get_post_meta($client_id, 'client_contract', true);
			$contracts_enabled = get_option('enable_project_contracts');
			$client_details = get_post_meta($client_id, 'client_details', true);
			$client_ids = get_post_meta($client_id, 'client_ids', true);
			$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
			$sent = isset($project_details['sent']) ? $project_details['sent'] : ''; 
			$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
			if ( empty($client_ids) ) { $client_ids = array(); } 
			if ( $client_user_id == $user->ID && empty($confirmed) && ! empty($sent) || in_array($user->ID, $client_ids) && empty($confirmed) && ! empty($sent) ) {
				if ( empty($client_contract) && ! empty($contracts_enabled) ) {
					$unsigned_contracts++;
				}
			}
		}
		if ( $unsigned_contracts != 0 ) {
			$alerts[] = array(
				'type'  => 'contract',
				'level' => 'info',
				/* translators: %s: No of contracts */
				'data'  => sprintf(_n('You have %s contract that requires your attention', 'You have %s contracts that require your attention', $unsigned_contracts, 'projectopia-core'), $unsigned_contracts),
			);
		}
		// Check Tickets
		$user = wp_get_current_user();
		$args = array(
			'post_type'      => 'cqpim_support',
			'posts_per_page' => -1,
			'post_status'    => 'private',
			'author__in'     => $client_ids_untouched,
		); 
		$tickets = get_posts($args);
		$unread_tickets = 0;
		foreach ( $tickets as $ticket ) {
			$ticket_updated = get_post_meta($ticket->ID, 'unread', true);
			$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
			if ( $ticket_updated == 1 && $ticket_status != 'resolved' ) {
				$unread_tickets++;
			}
		}
		if ( $unread_tickets != 0 ) {
			$alerts[] = array(
				'type'  => 'support',
				'level' => 'warning',
				/* translators: %s: No of messages */
				'data'  => sprintf(_n('You have %s open support ticket that requires your attention', 'You have %s open support tickets that require your attention', $unread_tickets, 'projectopia-core'), $unread_tickets),
			);
		}
		$messages = pto_new_messages($user->ID);
		$messages = isset($messages['read_val']) ? $messages['read_val'] : '';
		if ( ! empty($messages) ) {
			$alerts[] = array(
				'type'  => 'message',
				'level' => 'info',
				'data'  => __('You have new messages', 'projectopia-core') . ' - <a style="color:#222" href="?pto-page=messages">' . esc_html__('View', 'projectopia-core') . '</a>',
			);                  
		}
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) ) {
			$args = array(
				'post_type'      => 'cqpim_bug',
				'posts_per_page' => -1,
				'post_status'    => 'private',
			);
			$bugs = get_posts($args);
			foreach ( $bugs as $bug ) {
				$updated = get_post_meta($bug->ID, 'updated', true);
				$project = get_post_meta($bug->ID, 'bug_project', true);
				$project_details = get_post_meta($project, 'project_details', true);
				$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
				$client_ids = get_post_meta($client_id, 'client_ids', true);
				if ( ! empty($updated) && ! empty($client_ids) && in_array($user->ID, $client_ids) ) {
					$alerts[] = array(
						'type'  => 'message',
						'level' => 'info',
						/* translators: %s: Bug Name */
						'data'  => sprintf(esc_html__('You have an updated bug: %s', 'projectopia-core'), $bug->post_title) . ' <a href="' . get_the_permalink($bug->ID) . '"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></i></a>',
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