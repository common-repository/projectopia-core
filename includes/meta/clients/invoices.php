<?php
function pto_client_invoices_metabox_callback( $post ) {
 	wp_nonce_field( 
	'client_invoices_metabox', 
	'client_invoices_metabox_nonce' ); 
	$args = array(
		'post_type'      => 'cqpim_invoice',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_key'       => 'invoice_client',
		'meta_value'     => $post->ID,
	);
	$invoices = get_posts($args);
	if ( $invoices ) {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_invoices_table" data-sort="[[ 0, \'desc\' ]]" data-rows="5">';
		echo '<thead>';
		echo '<tr><th>' . esc_html__('Invoice ID', 'projectopia-core') . '</th><th>' . esc_html__('Project Ref', 'projectopia-core') . '</th><th>' . esc_html__('Invoice Date', 'projectopia-core') . '</th><th>' . esc_html__('Due Date', 'projectopia-core') . '</th><th>' . esc_html__('Amount', 'projectopia-core') . '</th><th>' . esc_html__('Outstanding', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr>';
		echo '</thead>';
		foreach ( $invoices as $invoice ) {
				$invoice_link = get_edit_post_link($invoice->ID);
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
				$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
				if ( is_numeric($invoice_date) ) { $invoice_date = wp_date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
				$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
				$subtotal = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
				$tax_rate = get_option('sales_tax_rate');
				$tax_applicable = get_post_meta($invoice->ID, 'tax_applicable', true);
				if ( $tax_applicable == 1 ) {
					$total = $total;
				} else {
					$total = $subtotal;
				}
				$project_id = isset( $invoice_details['project_id'] ) ? $invoice_details['project_id'] : '';
				$project_details = get_post_meta( $project_id, 'project_details', true );
				$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
				$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
				if ( empty($on_receipt) ) {
					$due_string = wp_date(get_option('cqpim_date_format'), $due);
				} else {
					$due_string = __('Due on Receipt', 'projectopia-core');
				}
				$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
				$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
				$now = time();
				if ( ! $paid ) {
					if ( $due ) {
						if ( $now > $due ) {
							if ( empty($on_receipt) ) {
								$status = '<span class="status overdue">' . esc_html__('Overdue', 'projectopia-core') . '</span>';
							} else {
								$status = '<span class="status overdue">' . esc_html__('Due on Receipt', 'projectopia-core') . '</span>';
							}       
						} else {
							if ( $sent ) {
								$status = '<span class="status clientApproval">' . esc_html__('Sent', 'projectopia-core') . '</span>';                         
							} else {
								$status = '<span class="status notSent">' . esc_html__('Not Sent', 'projectopia-core') . '</span>';                            
							}
						}
					}
				} else {
					$class = 'paid';
					$status = '<span class="status approved">' . esc_html__('Paid', 'projectopia-core') . '</span>';
				}
				$outstanding = (float)$total - (float)$total_paid;
				echo '<tr>';
				echo '<td><a href="' . esc_url( $invoice_link ) . '">' . esc_html( $invoice_id ) . '</a></td>';
				if ( $project_id ) {
					echo '<td><a href="' . esc_url( get_edit_post_link( $project_id ) ) . '">' . esc_html( get_the_title( $project_id ) ) . '</a></td>';
				} else {
					echo '<td>' . esc_html__('N/A', 'projectopia-core') . '</td>';
				}
				echo '<td>' . esc_html( $invoice_date ) . '</td>';
				echo '<td>' . wp_kses_post( $due_string ) . '</td>';
				echo '<td>' . esc_html( pto_calculate_currency($invoice->ID, $total) ) . '</td>';
				echo '<td>' . esc_html( pto_calculate_currency($invoice->ID, $outstanding) ) . '</td>';
				echo '<td>' . wp_kses_post( $status ) . '</td>';
				echo '</tr>';               
		}
		echo '</table></div>';
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('This client does not have any invoices assigned...', 'projectopia-core') . '</div>';
	}
}