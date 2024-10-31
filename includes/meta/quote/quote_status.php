<?php
function pto_quote_status_metabox_callback( $post ) {
 	wp_nonce_field( 'quote_status_metabox', 'quote_status_metabox_nonce' );

	$quote_details = get_post_meta($post->ID, 'quote_details', true);
	$quote_elements = get_post_meta($post->ID, 'quote_elements', true);
	$quote_client = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$quote_ref = isset($quote_details['quote_ref']) ? $quote_details['quote_ref'] : '';
	$quote_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
	$start_date = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
	$finish_date = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
	$quote_header = isset($quote_details['quote_header']) ? $quote_details['quote_footer'] : '';
	$quote_footer = isset($quote_details['quote_footer']) ? $quote_details['quote_footer'] : '';
	$quote_deposit = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
	?>
	<table class="quote_status">
		<tr>
			<td class="title underline"><?php esc_html_e('Quote / Estimate Details', 'projectopia-core'); ?></td>
			<td></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $quote_client ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Client', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $quote_ref ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Quote / Estimate Ref', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $quote_summary ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Project Brief', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $start_date ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Start Date', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $finish_date ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Deadline', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $quote_header ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Quote / Estimate Header', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $quote_footer ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Quote / Estimate Footer', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<td class="title">&nbsp;</td>
			<td></td>
		</tr>
		<tr>
			<td class="title underline"><?php esc_html_e('Milestones', 'projectopia-core'); ?></td>
			<td></td>
		</tr>
		<?php if ( ! $quote_elements ) { ?>
			<tr>
				<td class="title"><?php esc_html_e('No Milestones Added', 'projectopia-core'); ?></td>
				<td class="red"></td>
			</tr>
		<?php } else {
			$ordered = array();
			$i = 0;
			$mi = 0;
			foreach ( $quote_elements as $key => $element ) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[ $weight ] = $element;
				$mi++;
			}
			ksort($ordered);
			foreach ( $ordered as $element ) { ?>
				<tr>
					<?php $classes = ( ! empty( $element['title'] ) && ! empty( $element['cost'] ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php echo esc_html( $element['title'] ); ?></td>
					<td class="<?php echo esc_attr( $classes ); ?>"></td>
				</tr>				
			<?php $i++; }                   
		}
		?>
		<tr>
			<td class="title">&nbsp;</td>
			<td></td>
		</tr>
		<tr>
			<td class="title underline"><?php esc_html_e('Deposit', 'projectopia-core'); ?></td>
			<td></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $quote_deposit ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Initial Deposit', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
	</table>
	<?php
	$url = get_the_permalink($post->ID);
	$quote_sent = isset($quote_details['sent']) ? $quote_details['sent'] : '';
	$quote_accepted = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : '';
	$quote_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	if ( ! $quote_accepted ) {
		if ( empty($quote_sent) ) {
			echo '<div class="cqpim-alert cqpim-alert-danger alert-display">';
			$quote_type == 'estimate' ? esc_html_e('This estimate has not yet been sent to the client.', 'projectopia-core') : esc_html_e('This quote has not yet been sent to the client.', 'projectopia-core');
			echo '</div>';
			if ( current_user_can('cqpim_send_quotes') ) { 
				echo '<button id="send_quote" class="piaBtn btn btn-primary btn-block" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Send Quote / Estimate', 'projectopia-core') . '</button>';
				echo '<a class="cqpim_button_link piaBtn btn btn-primary btn-block mt-2" href="' . esc_url( $url ) . '?pto-page=print" target="_blank">' . esc_html__('Preview Quote / Estimate', 'projectopia-core') . '</a>';
			}
		}
		if ( $quote_sent ) {
			$quote_sent = $quote_details['sent_details'];
			$to = isset($quote_sent['to']) ? $quote_sent['to'] : '';
			$by = isset($quote_sent['by']) ? $quote_sent['by'] : '';
			$at = isset($quote_sent['date']) ? $quote_sent['date'] : '';
			if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $start_date; }
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
			/* translators: %1$s: Accepted by, %2$s: Timestamp, %3$s: IP */
			$quote_type == 'estimate' ? printf( esc_html__( 'This estimate was sent to %1$s on %2$s by %3$s', 'projectopia-core' ), esc_attr( $to ), esc_attr( $at ), wp_kses_post( $by ) ) : printf( esc_html__( 'This quote was sent to %1$s on %2$s by %3$s', 'projectopia-core' ), esc_attr( $to ), esc_attr( $at ), wp_kses_post( $by ) );
			echo '</div>';
			if ( current_user_can('cqpim_send_quotes') ) { 
				echo '<button id="send_quote" class="piaBtn btn btn-primary btn-block" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Resend Quote / Estimate', 'projectopia-core') . '</button>';
				echo '<a class="cqpim_button_link piaBtn btn btn-primary btn-block mt-2" href="' . esc_url( $url ) . '?pto-page=print" target="_blank">' . esc_html__('Preview Quote / Estimate', 'projectopia-core') . '</a>';
			}
		}
	} else {
		$quote_accepted = $quote_details['confirmed_details'];
		$ip = isset($quote_accepted['ip']) ? $quote_accepted['ip'] : '';
		$by = isset($quote_accepted['by']) ? $quote_accepted['by'] : '';
		$at = isset($quote_accepted['date']) ? $quote_accepted['date'] : '';
		if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
		echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
		/* translators: %1$s: Accepted by, %2$s: Timestamp, %3$s: IP */
		$quote_type == 'estimate' ? printf( esc_html__('This estimate was accepted by %1$s on %2$s from IP address %3$s', 'projectopia-core' ), wp_kses_post( $by ), esc_attr( $at ), esc_attr( $ip ) ) : printf( esc_html__( 'This quote was accepted by %1$s on %2$s from IP address %3$s', 'projectopia-core' ), wp_kses_post( $by ), esc_attr( $at ), esc_attr( $ip ) );
		echo '</div>';  
		if ( current_user_can('cqpim_send_quotes') ) { 
			echo '<button id="send_quote" class="piaBtn btn btn-primary btn-block" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Resend Quote / Estimate', 'projectopia-core') . '</button>';
			echo '<a class="cqpim_button_link piaBtn btn btn-primary btn-block mt-2" href="' . esc_url( $url ) . '?pto-page=print" target="_blank">' . esc_html__('Preview Quote / Estimate', 'projectopia-core') . '</a>';
		}
	}
	if ( current_user_can('publish_cqpim_quotes') ) {
		echo '<button class="save piaBtn btn btn-primary btn-block mt-2" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Update Quote / Estimate', 'projectopia-core') . '</button>';
		echo '<button class="convert_to_project piaBtn btn btn-primary btn-block mt-2" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Convert Quote to Project', 'projectopia-core') . '</button>';
		echo '<div id="messages"></div>';
	}
	do_action('pto_after_quote_metabox_callback',$post);
	 ?>
	<div id="quote_convert_container" style="display:none">
		<div id="quote_convert">
			<div style="padding:12px">
				<h3><?php esc_html_e('Convert to Project', 'projectopia-core'); ?></h3>
				<p><?php esc_html_e('Quotes / Estimates are converted to projects automatically when they are accepted by the client. Are you sure you want to manually convert this quote / estimate?', 'projectopia-core'); ?></p>
				<div id="convert-error"></div>
				<button class="cancel-colorbox mt-10 mt-10 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
				<button class="convert_confirm mt-10 mt-10 piaBtn right" value="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('Convert to Project', 'projectopia-core'); ?></button>
			</div>
		</div>
	</div>
	<?php
}