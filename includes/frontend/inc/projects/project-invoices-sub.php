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
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Invoices Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Ongoing Costs', 'projectopia-core'); ?></span>
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
			$total_paid = 0;
			foreach ( $invoices as $invoice ) {
				$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
				$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
				if ( empty($invoice_payments) ) {
					$invoice_payments = array();
				}
				if ( ! empty($invoice_details['project_id']) && $invoice_details['project_id'] == $post->ID ) {
					foreach ( $invoice_payments as $payment ) {
						$amount = isset($payment['amount']) ? $payment['amount'] : 0;
						$total_paid = $total_paid + (float) $amount;
					}
				}
			}
			$quote_elements = get_post_meta($post->ID, 'project_elements', true);
			$quote_extras = get_post_meta($post->ID, 'project_extras', true);
			$quote_details = get_post_meta($post->ID, 'project_details', true);
			$p_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
			if ( $quote_elements ) {
				echo '<table class="cqpim_table"><thead><tr>';
				echo '<th>' . esc_html__('Milestone', 'projectopia-core') . '</th>';
				if ( $p_type == 'estimate' ) {
					echo '<th>' . esc_html__('Estimated Cost', 'projectopia-core') . '</th>';
					echo '<th>' . esc_html__('Final Cost', 'projectopia-core') . '</th>';
				} else {
					echo '<th>' . esc_html__('Cost', 'projectopia-core') . '</th>';
					echo '<th>' . esc_html__('Final Cost', 'projectopia-core') . '</th>';
				}
				echo '</tr></thead>';
				echo '<tbody>';
				$subtotal = 0;
				$asubtotal = 0;
				$currency = get_option('currency_symbol');
				$ordered = array();
				$i = 0;
				$mi = 0;
				foreach ( $quote_elements as $key => $element ) {
					$weight = isset($element['weight']) ? $element['weight'] : $mi;
					$ordered[ $weight ] = $element;
					$mi++;
				}
				ksort($ordered);
				foreach ( $ordered as $element ) {
					$acost = isset($element['acost']) ? $element['acost'] : '';
					$cost = preg_replace("/[^\\d.]+/","", $element['cost']);
					$acost = preg_replace("/[^\\d.]+/","", $acost);
					if ( ! empty($cost) ) {
					$subtotal = $subtotal + $cost;
					}
					if ( ! empty($acost) ) {
					$asubtotal = $asubtotal + $acost;
					}
					echo '<tr><td>' . esc_html( $element['title'] ) . '</td>';
					if ( 1 ) {
						echo '<td>' . esc_html( pto_calculate_currency($post->ID, $cost) ) . '</td>';
						if ( $acost ) {
							if ( $acost > $cost ) {
								$class = 'over';
							}
							if ( $acost < $cost ) {
								$class = 'under';
							}
						if ( $acost == $cost ) {
							$class = 'under';
						}
							echo '<td class="' . esc_attr( $class ) . '">' . esc_html( pto_calculate_currency($post->ID, $acost) ) . '</td>';
						} else {
							echo '<td><span style="color:#d9534f">' . esc_html__('PENDING', 'projectopia-core') . '</span></td>';
						}
					} else {
						echo '<td>' . esc_html( $currency ) . '' . esc_html( $cost ) . '</td></tr>';
					}
				}
				$span = '';
				if ( $asubtotal > $subtotal ) {
					$class = 'over';
				}
				if ( $asubtotal < $subtotal ) {
					$class = 'under';
				}
				if ( $asubtotal == $subtotal ) {
					$class = 'under';
				}           
				$vat = get_post_meta($post->ID, 'tax_applicable', true);            
				if ( ! empty($vat) ) {
					$vat_rate = get_option('sales_tax_rate');
					$stax_rate = ! empty(get_option('secondary_sales_tax_rate')) ? get_option('secondary_sales_tax_rate') : 0;
					// Estimated
					$total_vat = $subtotal / 100 * $vat_rate;
					$total_stax = $subtotal / 100 * $stax_rate;
					$stax_applicable = get_post_meta($post->ID, 'stax_applicable', true);
					if ( ! empty($stax_applicable) ) {
						$total = $subtotal + $total_vat + $total_stax;
					} else {
						$total = $subtotal + $total_vat;
					}
					$tax_name = get_option('sales_tax_name');
					$stax_name = get_option('secondary_sales_tax_name');
					$outstanding = $total - $total_paid;
					// Actual
					$atotal_vat = $asubtotal / 100 * $vat_rate;
					$atotal_stax = $asubtotal / 100 * $stax_rate;
					if ( ! empty($stax_applicable) ) {
						$atotal = $asubtotal + $atotal_vat + $atotal_stax;
					} else {
						$atotal = $asubtotal + $atotal_vat;
					}
					$aoutstanding = $atotal - $total_paid;
					echo '<tr><td align="right" class="quote-align-right">' . esc_html__('Subtotal:', 'projectopia-core') . '</td>';
					echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $subtotal) ) . '</td>';
					echo '<td class="subtotal ' . esc_attr( $class ) . '">' . esc_html( pto_calculate_currency($post->ID, $asubtotal) ) . '</td>';          
					echo '</tr>';
					echo '<tr><td align="right" class="quote-align-right">' . esc_html( $tax_name ) . ': </td>';
					echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_vat) ) . '</td>';
						echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $atotal_vat) ) . '</td>';            
					echo '</tr>';
					if ( ! empty($stax_applicable) ) {
						echo '<tr><td align="right" class="quote-align-right">' . esc_html( $stax_name ) . ': </td>';
						echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_stax) ) . '</td>';
							echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $atotal_stax) ) . '</td>';           
						echo '</tr>';
					}
					echo '<tr><td align="right" class="quote-align-right">' . esc_html__('TOTAL:', 'projectopia-core') . '</td>';
					echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total) ) . '</td>';
						echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $atotal) ) . '</td>';            
					echo '</tr>';
						echo '<tr><td colspan="2" align="right" class="quote-align-right">' . esc_html__('Received:', 'projectopia-core') . '</td>';
						echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_paid) ) . '</td>';            
					echo '</tr>';
						echo '<tr><td colspan="2" align="right" class="quote-align-right">' . esc_html__('Outstanding:', 'projectopia-core') . '</td>';
						echo '<td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $aoutstanding) ) . '</td>';          
					echo '</tr>';
				} else {
					$atotal = $asubtotal;
					$aoutstanding = $atotal - $total_paid;
					echo '<tr><td align="right" class="quote-align-right">' . esc_html__('TOTAL:', 'projectopia-core') . '</td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $subtotal) ) . '</td><td class="subtotal ' . esc_attr( $class ) . '">' . esc_html( pto_calculate_currency($post->ID, $asubtotal) ) . '</td></tr>';
					echo '<tr><td colspan="2" align="right" class="quote-align-right">' . esc_html__('Received:', 'projectopia-core') . '</td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_paid) ) . '</td></tr>';                
					echo '<tr><td colspan="2" align="right" class="quote-align-right">' . esc_html__('Outstanding:', 'projectopia-core') . '</td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $aoutstanding) ) . '</td></tr>';
				}
				echo '</tbody></table>';
			} else {
				echo '<p style="padding:30px">';
				esc_html_e( 'You have not added any milestones. Please add at least one milestone to enable this section', 'projectopia-core');
				echo '</p>';
			}
			?>
		</div>
	</div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Project Invoices', 'projectopia-core'); ?></span>
				</div>	
			</div>
		<?php
		$args = array(
			'post_type'      => 'cqpim_invoice',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_key'       => 'invoice_project',
			'meta_value'     => $post->ID,
		);
		$invoices = get_posts($args);
		if ( $invoices ) {
			$currency = get_option('currency_symbol');
			echo '<table class="cqpim_table">';
			echo '<thead>';
			echo '<tr><th>' . esc_html__('Invoice ID', 'projectopia-core') . '</th><th>' . esc_html__('Invoice Date', 'projectopia-core') . '</th><th>' . esc_html__('Due Date', 'projectopia-core') . '</th><th>' . esc_html__('Amount', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr>';
			echo '</thead>';
			echo '<tbody>';
			$i = 0;
			foreach ( $invoices as $invoice ) {
				$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
				$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
				$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
				$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
				$tax_rate = get_option('sales_tax_rate');
				$tax_applicable = get_post_meta($invoice->ID, 'tax_applicable', true);
				if ( $tax_applicable == 1 ) {
					$total = $total;
				} else {
					$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
				}
				$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
				if ( is_numeric($invoice_date) ) { $invoice_date = wp_date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
				$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
				$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
				$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
				$due_readable = wp_date(get_option('cqpim_date_format'), $due);
				$current_date = time();
				$url = get_the_permalink($invoice->ID);
				if ( ! $paid ) {
					if ( $current_date > $due ) {
						$p_status = '<span class="cqpim_button cqpim_small_button font-red border-red sbold nolink op"><strong>' . esc_html__('OVERDUE', 'projectopia-core') . '</strong></span>';
					} else {
						if ( ! $sent ) {
							$p_status = '<span class="cqpim_button cqpim_small_button font-red border-red nolink op">' . esc_html__('NOT SENT', 'projectopia-core') . '</span>';
						} else {
							$p_status = '<span class="cqpim_button cqpim_small_button font-amber border-amber nolink op">' . esc_html__('SENT', 'projectopia-core') . '</span>';
						}
					}
				} else {
					$p_status = '<span class="cqpim_button cqpim_small_button font-green border-green nolink op">' . esc_html__('PAID', 'projectopia-core') . '</span>';
				}               
				echo '<tr>';
				echo '<td><span class="nodesktop"><strong>' . esc_html__('ID', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $invoice_id ) . '</a></td>';
				echo '<td><span class="nodesktop"><strong>' . esc_html__('Date', 'projectopia-core') . '</strong>: </span> ' . esc_html( $invoice_date ) . '</td>';
				echo '<td><span class="nodesktop"><strong>' . esc_html__('Due', 'projectopia-core') . '</strong>: </span> ' . esc_html( $due_readable ) . '</td>';
				echo '<td><span class="nodesktop"><strong>' . esc_html__('Amount', 'projectopia-core') . '</strong>: </span> ' . esc_html( pto_calculate_currency($invoice->ID, $total) ) . '</td>';
				echo '<td>' . wp_kses_post( $p_status ) . '</td>';
				echo '</tr>';
				$i++;           
			}
			echo '</tbody>';
			echo '</table>';
		} else {
			echo '<p>' . esc_html__('There are no invoices for this project', 'projectopia-core') . '</p>';
		}
		?>
		</div>
	</div>
</div>