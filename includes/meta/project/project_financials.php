<?php
function pto_project_financials_metabox_callback( $post ) {
 	wp_nonce_field( 'project_financials_metabox', 'project_financials_metabox_nonce' ); 

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
		$pid = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
		if ( ! empty($pid) && $pid == $post->ID ) {
			foreach ( $invoice_payments as $payment ) {
				$amount = isset($payment['amount']) ? $payment['amount'] : 0;
				$total_paid = $total_paid + (float) $amount;
			}
		}
	}
	$quote_elements = get_post_meta($post->ID, 'project_elements', true);
	$quote_extras = get_post_meta($post->ID, 'project_extras', true);
	$quote_details = get_post_meta($post->ID, 'project_details', true);
	$type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
	if ( $quote_elements ) {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100"><thead><tr>';
		echo '<th>' . esc_html__('Milestone', 'projectopia-core') . '</th>';
		if ( $type == 'estimate' ) {
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
			if ( empty($acost) ) {
				$acost = 0;
			}
			if ( ! empty($cost) ) {
				$subtotal = $subtotal + $cost;
			}
			if ( ! empty($acost) ) {
				$asubtotal = $asubtotal + $acost;
			}
			echo '<tr><td>' . esc_html( $element['title'] ) . '</td>';
			if ( 1 ) {
				echo '<td>' . esc_html( pto_calculate_currency( $post->ID, $cost ) ) . '</td>';
				if ( ! empty($acost) || $acost === 0 ) {
					if ( $acost > $cost ) {
						$class = 'font-white bg-red sbold cqpim_tooltip';
						$title = esc_html__('Over Budget', 'projectopia-core');
					}
					if ( $acost < $cost ) {
						$class = 'font-white bg-green-sharp cqpim_tooltip';
						$title = esc_html__('Under Budget', 'projectopia-core');
					}
					if ( $acost == $cost ) {
						$class = 'font-white bg-green-sharp cqpim_tooltip';
						$title = esc_html__('On Budget', 'projectopia-core');
					}
					echo '<td class="' . esc_attr( $class ) . '" title="' . esc_attr( $title ) . '">' . esc_html( pto_calculate_currency( $post->ID, $acost ) ) . '</td>';
				} else {
					echo '<td><span class="font-red">' . esc_html__('PENDING', 'projectopia-core') . '</span></td>';
				}
			} else {
				echo '<td>' . esc_html( pto_calculate_currency( $post->ID, $cost ) ) . '</td></tr>';
			}
		}

		$span = '';
		if ( $asubtotal > $subtotal ) {
			$class = 'font-white bg-red cqpim_tooltip';
			$title = esc_html__('Over Budget', 'projectopia-core');
		}
		if ( $asubtotal < $subtotal ) {
			$class = 'font-white bg-green-sharp cqpim_tooltip';
			$title = esc_html__('Under Budget', 'projectopia-core');
		}
		if ( $asubtotal == $subtotal ) {
			$class = 'font-white bg-green-sharp cqpim_tooltip';
			$title = esc_html__('On Budget', 'projectopia-core');
		}           
		$vat = get_post_meta($post->ID, 'tax_applicable', true);            
		if ( ! empty($vat) ) {
			$vat_rate = get_option('sales_tax_rate');
			$stax_rate = get_option('secondary_sales_tax_rate');
			if ( ! empty($vat_rate) ) {
				$total_vat = $subtotal / 100 * $vat_rate;
			}
			if ( ! empty($stax_rate) ) {
			$total_stax = $subtotal / 100 * $stax_rate;
			}
			$stax_applicable = get_post_meta($post->ID, 'stax_applicable', true);
			if ( ! empty($stax_applicable) ) {
				$total = $subtotal + $total_vat + $total_stax;
			} else {
				$total = $subtotal + $total_vat;
			}
			$tax_name = get_option('sales_tax_name');
			$stax_name = get_option('secondary_sales_tax_name');
			$outstanding = $total - $total_paid;
			if ( ! empty($vat_rate) ) {
			$atotal_vat = $asubtotal / 100 * $vat_rate;
			}
			if ( ! empty($stax_rate) ) {
			$atotal_stax = $asubtotal / 100 * $stax_rate;
			}
			if ( ! empty($stax_applicable) ) {
				$atotal = $asubtotal + $atotal_vat + $atotal_stax;
			} else {
				$atotal = $asubtotal + $atotal_vat;
			}
			$aoutstanding = $atotal - $total_paid;
			echo '<tr><td ' . esc_attr( $span ) . ' align="right" class="quote-align-right">' . esc_html__('Subtotal:', 'projectopia-core') . '</td>';
			echo '<td class="subtotal">' . esc_html( $currency .  $subtotal ) . '</td>';
			echo '<td class="subtotal ' . esc_attr( $class ) . '" title="' . esc_attr( $title ) . '">' . esc_html( pto_calculate_currency( $post->ID, $asubtotal ) ) . '</td>';           
			echo '</tr>';
			echo '<tr><td ' . esc_attr( $span ) . ' align="right" class="quote-align-right">' . esc_html( $tax_name ) . ': </td>';
			echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $total_vat ) ) . '</td>';
				echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $atotal_vat ) ) . '</td>';            
			echo '</tr>';
			if ( ! empty($stax_applicable) ) {
				echo '<tr><td ' . esc_attr( $span ) . ' align="right" class="quote-align-right">' . esc_html( $stax_name ) . ': </td>';
				echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $total_stax ) ) . '</td>';
					echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $atotal_stax ) ) . '</td>';           
				echo '</tr>';
			}
			echo '<tr><td ' . esc_attr( $span ) . ' align="right" class="quote-align-right">' . esc_html__('TOTAL:', 'projectopia-core') . '</td>';
			echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $total ) ) . '</td>';
				echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $atotal ) ) . '</td>';            
				$aatotal = $atotal;
			echo '</tr>';
				echo '<tr><td colspan="2" align="right" class="quote-align-right">' . esc_html__('Received:', 'projectopia-core') . '</td>';
				echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $total_paid ) ) . '</td>';            
			echo '</tr>';
				echo '<tr><td colspan="2" align="right" class="quote-align-right">' . esc_html__('Outstanding:', 'projectopia-core') . '</td>';
				echo '<td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $aoutstanding ) ) . '</td>';          
			echo '</tr>';
		} else {
			$atotal = $asubtotal;
			$aoutstanding = $atotal - $total_paid;
			$span = 'colspan="2"';
			echo '<tr><td align="right" class="quote-align-right">' . esc_html__('TOTAL:', 'projectopia-core') . '</td><td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $subtotal ) ) . '</td><td class="subtotal ' . esc_attr( $class ) . '">' . esc_html( pto_calculate_currency( $post->ID, $asubtotal ) ) . '</td></tr>';
			echo '<tr><td ' . esc_attr( $span ) . ' align="right" class="quote-align-right">' . esc_html__('Received:', 'projectopia-core') . '</td><td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $total_paid ) ) . '</td></tr>';                
			echo '<tr><td ' . esc_attr( $span ) . ' align="right" class="quote-align-right">' . esc_html__('Outstanding:', 'projectopia-core') . '</td><td class="subtotal">' . esc_html( pto_calculate_currency( $post->ID, $aoutstanding ) ) . '</td></tr>';
		}
		if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) {
			$args = array(
				'post_type'      => 'cqpim_expense',
				'posts_per_page' => -1,
				'post_status'    => 'private',
				'meta_key'       => 'project_id',
				'meta_value'     => $post->ID,
			);
			$expenses = get_posts($args);
			if ( ! empty($expenses) ) {
				echo '<tr><th colspan="3">' . esc_html__('Project Expenses', 'projectopia-core') . '</th></tr>';
				echo '<tr><th colspan="2">' . esc_html__('Expense', 'projectopia-core') . '</th><th>' . esc_html__('Cost', 'projectopia-core') . '</th></tr>';
				$expense_total = 0;
				foreach ( $expenses as $expense ) {
					unset($auth);
					$invoice_totals = get_post_meta($expense->ID, 'invoice_totals', true); 
					$invoice_total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
					$expense_total = $expense_total + $invoice_total;
					$auth = get_post_meta($expense->ID, 'auth_active', true);
					$auth_limit = get_option('cqpim_expense_auth_limit');
					$authorised = get_post_meta($expense->ID, 'authorised', true);  
					if ( empty($auth) || ! empty($auth) && ! empty($authorised) && $authorised == 1 || ! empty($auth) && empty($authorised) && ! empty($auth_limit) && $auth_limit > $invoice_total ) {                            
						echo '<tr><td colspan="2">' . esc_html( $expense->post_title ) . '</td><td>' . esc_html( pto_calculate_currency( $expense->ID, $invoice_total ) ) . '</td></tr>';
					}
				}
				echo '<tr><td colspan="2">' . esc_html__('Total:', 'projectopia-core') . '</td><td>' . esc_html( pto_calculate_currency( $post->ID, $expense_total ) ) . '</td></tr>';
				if ( ! empty($vat) ) {
					if ( ! empty($stax_applicable) ) {
						$total = $aatotal - $atotal_vat - $atotal_stax;
					} else {
						$total = $aatotal - $atotal_vat;
					}
				} else {
					$total = $asubtotal;
				}
				$profit = $total - $expense_total;
				echo '<tr><td colspan="2"><strong>' . esc_html__('Profit:', 'projectopia-core') . '</strong></td><td><strong>' . esc_html( pto_calculate_currency( $post->ID, $profit ) ) . '</strong></td></tr>';
			}               
		}
		echo '</tbody></table></div>';
	} else {
		echo '<p>' . esc_html__('You have not added any milestones. Please add at least one milestone to enable this section', 'projectopia-core') . '</p>';
	} 
}