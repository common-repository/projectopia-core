<?php
function pto_project_invoices_metabox_callback( $post ) {
 	wp_nonce_field( 'project_invoices_metabox', 'project_invoices_metabox_nonce' );
	 
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
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable">';
		echo '<thead>';
		echo '<tr><th>' . esc_html__('Invoice ID', 'projectopia-core') . '</th><th>' . esc_html__('Invoice Date', 'projectopia-core') . '</th><th>' . esc_html__('Due Date', 'projectopia-core') . '</th><th>' . esc_html__('Amount', 'projectopia-core') . '</th><th>' . esc_html__('Outstanding', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		$i = 0;
		foreach ( $invoices as $invoice ) {
			$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
			$invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
			$total_paid = 0;
			if ( empty($invoice_payments) ) {
				$invoice_payments = array();
			}
			foreach ( $invoice_payments as $payment ) {
				$amount = isset($payment['amount']) ? $payment['amount'] : 0;
				$total_paid = $total_paid + (float) $amount;
			}               
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
			if ( is_numeric($due) ) { $due_readable = wp_date(get_option('cqpim_date_format'), $due); } else { $due_readable = $due; }
			$current_date = time();
			$link = get_edit_post_link($invoice->ID);

			if ( ! $paid ) {
				if ( $current_date > $due ) {
					$status = '<span class="badgeOverdue"><strong>' . esc_html__('OVERDUE', 'projectopia-core') . '</strong></span>';
				} else {
					if ( ! $sent ) {
						$status = '<span class="badgeOverdue clientApproval">' . esc_html__('Not Sent', 'projectopia-core') . '</span>';
					} else {
						$status = '<span class="badgeOverdue normal">' . esc_html__('Sent', 'projectopia-core') . '</span>';
					}
				}
			} else {
				$status = '<span class="badgeOverdue approved">' . esc_html__('Paid', 'projectopia-core') . '</span>';
			}

			$outstanding = (float)$total - (float)$total_paid;
			echo '<tr>';
			echo '<td><a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $invoice_id ) . '</a></td>';
			echo '<td>' . esc_html( $invoice_date ) . '</td>';
			echo '<td>' . esc_html( $due_readable ) . '</td>';
			echo '<td>' . esc_html( pto_calculate_currency( $invoice->ID, $total ) ) . '</td>';
			echo '<td>' . esc_html( pto_calculate_currency( $invoice->ID, $outstanding ) ) . '</td>';
			echo '<td>' . wp_kses_post( $status ) . '</td>';
			echo '</tr>';
			$i++;           
		}
		echo '</tbody>';
		echo '</table></div>';
	} else {
		echo '<p>' . esc_html__('There are no invoices for this project', 'projectopia-core') . '</p>';
	}
}