<?php
while ( have_posts() ) : the_post();
$user = wp_get_current_user(); 
$user_id = $user->ID;
$quote_id = get_the_ID();
$logo = get_option('company_logo');
$logo_url = isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : '';
$quote_details = get_post_meta($quote_id, 'quote_details', true);
$quote_elements = get_post_meta($quote_id, 'quote_elements', true);         
$quote_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
$start_date = isset($quote_details['start_date']) ? $quote_details['start_date'] : '';
$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
$client_details = get_post_meta($client_id, 'client_details', true);
$client_contacts = get_post_meta($client_id, 'client_contacts', true);
$quote_client_ids = get_post_meta($client_id, 'client_ids', true);
$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
$client_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
$finish_date = isset($quote_details['finish_date']) ? $quote_details['finish_date'] : '';
$quote_header = isset($quote_details['quote_header']) ? $quote_details['quote_header'] : '';
$quote_footer = isset($quote_details['quote_footer']) ? $quote_details['quote_footer'] : '';
if ( empty($client_contacts) ) {
	$client_contacts = array();
}
if ( $client_contact == $client_user_id ) {
	$quote_header = str_replace('%%CLIENT_NAME%%', $client_details['client_contact'], $quote_header);
} else {
	$quote_header = str_replace('%%CLIENT_NAME%%', isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : $client_details['client_contact'], $quote_header);
}
$quote_header = pto_replacement_patterns($quote_header, $quote_id, 'quote');    
$quote_footer = pto_replacement_patterns($quote_footer, $quote_id, 'quote');            
$deposit = isset($quote_details['deposit_amount']) ? $quote_details['deposit_amount'] : '';
$p_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';
$upper_type = ucfirst($p_type);
$currency = get_option('currency_symbol');
$vat = get_post_meta($post->ID, 'tax_applicable', true);
if ( ! empty($vat) ) {
	$vat = get_post_meta($post->ID, 'tax_rate', true);
}
if ( $client_terms ) {
	$invoice_terms = $client_terms;
} else {
	$invoice_terms = get_option('company_invoice_terms');
}
$tax_name = get_option('sales_tax_name');
if ( $vat ) {
	$vat_string = '+' . $tax_name;
} else {
	$vat_string = '';
}
if ( in_array('cqpim_client', $user->roles) ) {
	$user = wp_get_current_user();
	$client_logs = get_post_meta($quote_details['client_id'], 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$p_title = get_the_title();
$p_title = str_replace('Private:', '', $p_title);
$client_logs[ $now ] = array(
	'user' => $user->ID,
	/* translators: %1$s: Quote ID, %2$s: Quote Title */
	'page' => sprintf(esc_html__('Quote %1$s - %2$s', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($quote_details['client_id'], 'client_logs', $client_logs);
}
if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quote' ) {
	include('header.php');
	include( 'quotes/quote-page.php' );
	include('footer.php');
} 
if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'print' ) {
include( 'quotes/quote-print.php' );
}
endwhile;