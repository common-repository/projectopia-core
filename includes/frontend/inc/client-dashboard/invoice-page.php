<br />
<div class="cqpim_block">
<div class="cqpim_block_title">
	<div class="caption">
		<i class="fa fa-credit-card-alt font-light-violet" aria-hidden="true"></i>
		<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Invoices', 'projectopia-core'); ?></span>
	</div>
</div>
<br />
<?php 
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Invoices Page', 'projectopia-core'),
);
update_post_meta($assigned, 'client_logs', $client_logs);
$args = array(
	'post_type'      => 'cqpim_invoice',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);
$invoices = get_posts($args);
$i = 0;
if ( $invoices ) {
	$currency = get_option('currency_symbol');
	echo '<table class="datatable_style dataTable-CI" id="front_invoicepage_table">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>' . esc_html__('Invoice ID', 'projectopia-core') . '</th><th>' . esc_html__('Owner', 'projectopia-core') . '</th><th>' . esc_html__('Invoice Date', 'projectopia-core') . '</th><th>' . esc_html__('Due Date', 'projectopia-core') . '</th><th>' . esc_html__('Amount', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th>';
	do_action( 'pto_cd_invoices_column_head_after' );
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach ( $invoices as $invoice ) {
		$invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
		$client_contact = get_post_meta($invoice->ID, 'client_contact', true);
		$owner = get_user_by('id', $client_contact);
		$invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
		$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_ids = get_post_meta($client_id, 'client_ids', true);
		$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
		$invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
		$tax_rate = get_option('sales_tax_rate');
		if ( ! empty($tax_rate) ) {
			$total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
		} else {
			$total = isset($invoice_totals['sub']) ? $invoice_totals['sub'] : '';
		}                   
		$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
		if ( is_numeric($invoice_date) ) { $invoice_date = wp_date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
		$sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
		$paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
		$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
		$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
		$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
		if ( empty($on_receipt) ) {
			$due_readable = wp_date(get_option('cqpim_date_format'), $due);
		} else {
			$due_readable = __('Due on Receipt', 'projectopia-core');
		}
		$current_date = time();
		$password = md5($invoice->post_password);
		$url = get_the_permalink($invoice->ID);
		if ( ! $paid ) {
			if ( $current_date > $due ) {
				if ( empty($on_receipt) ) {
					$p_status = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink sbold"><strong>' . esc_html__('OVERDUE', 'projectopia-core') . '</strong></span>';
				} else {
					$p_status = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink"><strong>' . esc_html__('Due on Receipt', 'projectopia-core') . '</strong></span>';
				}
			} else {
				if ( ! $sent ) {
					$p_status = '<span class="cqpim_button cqpim_small_button font-red border-red op nolink">' . esc_html__('New', 'projectopia-core') . '</span>';
				} else {
					$p_status = '<span class="cqpim_button cqpim_small_button font-amber border-amber op nolink">' . esc_html__('Outstanding', 'projectopia-core') . '</span>';
				}
			}
		} else {
			$p_status = '<span class="cqpim_button cqpim_small_button font-green border-green op nolink">' . esc_html__('PAID', 'projectopia-core') . '</span>';
		}   
		if ( ! is_array($client_ids) ) {
			$client_ids = array();
		}
		if ( $client_user_id == $user->ID && ! empty($sent) || in_array($user->ID, $client_ids) && ! empty($sent) || $client_user_id == $user->ID && ! empty($paid) ) { 
			echo '<tr>';
			echo '<td><span class="nodesktop"><strong>' . esc_html__('ID', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( $url ) . '" >' . esc_html( $invoice_id ) . '</a></td>';
			echo '<td><span class="nodesktop"><strong>' . esc_html__('Owner', 'projectopia-core') . '</strong>: </span> ' . esc_html( $owner->display_name ) . '</td>';
			echo '<td><span class="nodesktop"><strong>' . esc_html__('Date', 'projectopia-core') . '</strong>: </span> ' . esc_html( $invoice_date ) . '</td>';
			echo '<td><span class="nodesktop"><strong>' . esc_html__('Due', 'projectopia-core') . '</strong>: </span> ' . esc_html( $due_readable ) . '</td>';
			echo '<td><span class="nodesktop"><strong>' . esc_html__('Amount', 'projectopia-core') . '</strong>: </span> ' . esc_html( pto_calculate_currency($invoice->ID, $total) ) . '</td>';
			echo '<td><span class="nodesktop"><strong>' . esc_html__('Status', 'projectopia-core') . '</strong>: </span> ' . wp_kses_post( $p_status ) . '</td>';
			do_action( 'pto_cd_invoices_column_body_after', $invoice );
			echo '</tr>';
			$i++;
		}
	}
	echo '</tbody>';
	echo '</table>';
}
?>
</div>