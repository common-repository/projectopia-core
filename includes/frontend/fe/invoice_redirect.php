<?php 

if ( empty($_GET['pto-page']) ) {
	include('invoice_cd.php');
} elseif ( ! empty($_GET['pto-page']) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'print' ) {
	$invoice_template = get_option('cqpim_invoice_template');
	if ( $invoice_template == 1 ) {
		include('invoice.php');
	}
	if ( $invoice_template == 2 ) {
		include('invoice2.php');
	}
	if ( $invoice_template == 3 ) {
		include('invoice3.php');
	}
}