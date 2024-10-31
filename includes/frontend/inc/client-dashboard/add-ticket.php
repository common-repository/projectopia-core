<br />
<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-life-ring font-light-violet" aria-hidden="true"></i>
			<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Add Support Ticket', 'projectopia-core'); ?></span>
		</div>
	</div>
	<?php 
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		'page' => __('Client Dashboard Add Support Ticket Page', 'projectopia-core'),
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	$string = pto_random_string(10);
	?>
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
	<div id="cqpim_backend_quote">
		<br />
		<input type="hidden" name="action" value="new_ticket" />
		<h4><?php esc_html_e('Ticket Title:', 'projectopia-core'); ?></h4>
		<input type="text" id="ticket_title" required />
		<h4><?php esc_html_e('Ticket Priority:', 'projectopia-core'); ?></h4>
		<?php
			$support_ticket_priorities = get_option( 'support_ticket_priorities');
			if ( empty( $support_ticket_priorities ) ) {
				$support_ticket_priorities = array(
					'low'       => '#5c9bd1',
					'normal'    => '#8ec165',
					'high'      => '#f1c40f',
					'immediate' => '#f10f0f',
				);
				update_option( 'support_ticket_priorities', $support_ticket_priorities );
			}
		?>
		<select id="ticket_priority_new" name="ticket_priority_new">
			<?php
				foreach ( $support_ticket_priorities as $key => $priority_color ) {
					$item = $key;
					if ( $key === 'low' ) {
						$item = __( 'Low', 'projectopia-core' );
					}

					if ( $key === 'normal' ) {
						$item = __( 'Normal', 'projectopia-core' );
					}

					if ( $key === 'high' ) {
						$item = __( 'High', 'projectopia-core' );
					}

					if ( $key === 'immediate' ) {
						$item = __( 'Immediate', 'projectopia-core' );
					}
					printf(
						'<option value="%s">%s</option>',
						esc_attr( $key ),
						esc_html( $item )
					);
				}
			?>
		</select>

		<?php
			pto_set_transient('upload_ids','');
			pto_set_transient('ticket_changes','');
		?>
		<h4><?php esc_html_e('Upload Files', 'projectopia-core'); ?></h4>
		<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
		<div id="upload_attachments"></div>
		<div class="clear"></div>
		<input type="hidden" name="image_id" id="upload_attachment_ids">
		<div class="clear"></div>
		<?php
		$data = get_option('cqpim_custom_fields_support');
		pto_get_custom_fields( $data, $post, false, true );
		?>
		<div class="clear"></div>
		<h4><?php esc_html_e('Details', 'projectopia-core'); ?></h4>				
		<textarea id="ticket_update_new" name="ticket_update_new" required ></textarea>
		<div class="clear"></div>
		<br /><br />
		<a id="support-submit" href="#" class="cqpim_button font-white bg-violet rounded_2 op" style="margin-right:0"><?php esc_html_e('Create Ticket', 'projectopia-core'); ?></a>
		<div class="clear"></div>
	<br />
</div>
</div>