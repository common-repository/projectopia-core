
<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-life-ring font-green-sharp" aria-hidden="true"></i> <?php esc_html_e('Support Tickets', 'projectopia-core'); ?>
		</div>
		<div class="actions">
			<a href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=add-support-ticket'; ?>" class="cqpim_button cqpim_small_button font-green-sharp border-green-sharp op"><?php esc_html_e('Add Support Ticket', 'projectopia-core'); ?></a>
		</div>
	</div>
	<?php 
	$show_open_warning = get_option('pto_support_opening_warning');
	if ( ! empty($show_open_warning) ) {
		$open = pto_return_open();
		if ( $open == 1 ) {
			$message = get_option('pto_support_closed_message');
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">' . esc_textarea( $message ) . '</div>';
		} elseif ( $open == 2 ) {
			$message = get_option('pto_support_open_message');
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_textarea( $message ) . '</div>';
		}
	}
	?>
	<div class="cqpim-dash-item-inside">
		<div id="ticket_container" class="content">
			<?php 
			$user = wp_get_current_user();
			
			$support_ticket_status = get_option('support_status');
			
			$key_arr = $support_ticket_status['key'];
			$value_arr = $support_ticket_status['value'];
			$color_arr = $support_ticket_status['color'];
			$ticket_status = pto_get_transient('ticket_status');
			$p_status = ! empty($ticket_status) ? $ticket_status : $key_arr;
			$args = array(
				'post_type'      => 'cqpim_support',
				'posts_per_page' => -1,
				'post_status'    => 'private',
				'author__in'     => $client_ids_untouched,
				'meta_key'       => 'ticket_status',
				'meta_value'     => $p_status,
			); 
			$tickets = get_posts($args);
			$total_tickets = count($tickets);
			if ( $tickets ) {
				echo '<table class="datatable_style dataTable-CST" id="front_tickets_table">';
				echo '<thead><tr><th>' . esc_html__('ID', 'projectopia-core') . '</th><th>' . esc_html__('Ticket Title', 'projectopia-core') . '</th><th>' . esc_html__('Ticket Owner', 'projectopia-core') . '</th><th>' . esc_html__('Priority', 'projectopia-core') . '</th><th>' . esc_html__('Created', 'projectopia-core') . '</th><th>' . esc_html__('Last Updated', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr></thead>';
				echo '<tbody>';
				foreach ( $tickets as $ticket ) {
					$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
					$owner = get_user_by('id', $ticket->post_author);
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
							$priority = '<span style="text-transform:capitalize;border:solid 1px '. esc_attr( $color_code ) .' !important;color:'. esc_attr( $color_code ) .' !important" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . wp_kses_post( $ticket_priority ) . '</span>';
						} else {
							$priority = '<span style="text-transform:capitalize;" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' .  wp_kses_post( $ticket_priority ) . '</span>';
						}
					}
					echo '<tr>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Ticket ID', 'projectopia-core') . '</strong>: </span> ' . esc_html( $ticket->ID ) . '</td>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Title', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( get_the_permalink($ticket->ID) ) . '">' . esc_html( $ticket->post_title ) . '</a></td>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Owner', 'projectopia-core') . '</strong>: </span> ' . esc_html( $owner->display_name ) . '</td>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Priority', 'projectopia-core') . '</strong>: </span> ' . wp_kses_post( $priority ) . '</td>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Created', 'projectopia-core') . '</strong>: </span> ' . get_the_date(get_option('cqpim_date_format') . ' H:i', $ticket->ID) . '</td>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Updated', 'projectopia-core') . '</strong>: </span> ' . esc_html( $ticket_updated ) . '</td>';
					echo '<td><span class="nodesktop"><strong>' . esc_html__('Status', 'projectopia-core') . '</strong>: </span> ' . wp_kses_post( $tstatus ) . '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			} else {
				echo '<table class="datatable_style dataTable-CST" id="front_tickets_table">';
				echo '<thead><tr><th>' . esc_html__('ID', 'projectopia-core') . '</th><th>' . esc_html__('Ticket Title', 'projectopia-core') . '</th><th>' . esc_html__('Ticket Owner', 'projectopia-core') . '</th><th>' . esc_html__('Priority', 'projectopia-core') . '</th><th>' . esc_html__('Created', 'projectopia-core') . '</th><th>' . esc_html__('Last Updated', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr></thead>';
				echo '<tbody>';
				echo '<tr>';
				if ( is_array($p_status) ) {
					echo '<td>' . esc_html__('No open support tickets found', 'projectopia-core') . '</td><td></td><td></td><td></td><td></td><td></td><td></td>';
				} elseif ( $p_status == 'resolved' ) {
					echo '<td>' . esc_html__('No resolved support tickets found', 'projectopia-core') . '</td><td></td><td></td><td></td><td></td><td></td><td></td>';
				}               
				echo '</tr>';
				echo '</tbody>';
				echo '</table>';
			}
			if ( $p_status == 'resolved' ) { ?>
				<br />
				<button id="switch_to_resolved" class="cqpim_button cqpim_small_button font-green-sharp border-green-sharp op"><?php esc_html_e('View Open Tickets', 'projectopia-core'); ?></button>
			<?php } else { ?>
				<br />
				<button id="switch_to_resolved" class="cqpim_button cqpim_small_button font-green-sharp border-green-sharp op"><?php esc_html_e('View Resolved Tickets', 'projectopia-core'); ?></button>
			<?php } ?>		
			<div class="clear"></div>
		</div>
	</div>
</div>	