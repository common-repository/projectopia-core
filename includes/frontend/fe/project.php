<?php
while ( have_posts() ) : the_post();
	$user = wp_get_current_user(); 
	$user_id = $user->ID;
	$logo = get_option('company_logo');
	$logo_url = isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : '';
	$p_title = get_the_title();
	$p_title = str_replace('Private: ', '', $p_title);
	$company_name = get_option('company_name');
	$company_address = get_option('company_address');
	$company_postcode = get_option('company_postcode');
	$contract_text = get_option('default_contract_text');
	$contract_text = pto_replacement_patterns($contract_text, $post->ID, 'project');
	$currency = get_option('currency_symbol');
	$vat = get_post_meta($post->ID, 'tax_applicable', true);
	if ( ! empty($vat) ) {
		$vat = get_post_meta($post->ID, 'tax_rate', true);
	}
	$tax_name = get_option('sales_tax_name');
	if ( ! empty($vat) ) {
		$vat_string = '';
	} else {
		$vat_string = '';
	}
	$project_details = get_post_meta($post->ID, 'project_details', true);
	$project_elements = get_post_meta($post->ID, 'project_elements', true);
	$p_type = isset($project_details['quote_type']) ? $project_details['quote_type'] : '';
	$upper_type = ucfirst($p_type);
	$quote_id = isset($project_details['quote_id']) ? $project_details['quote_id'] : '';
	$quote_details = get_post_meta($quote_id, 'quote_details', true);
	$project_summary = isset($project_details['project_summary']) ? $project_details['project_summary'] : '';
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$project_client_ids = get_post_meta($client_id, 'client_ids', true);
	if ( empty($project_client_ids) ) {
		$project_client_ids = array();
	}
	$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
	$client_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
	$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
	$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
	$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
	$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	if ( $client_terms ) {
		$invoice_terms = $client_terms;
	} else {
		$invoice_terms = get_option('company_invoice_terms');
	}
	if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contract' ) {
		include('header.php');       
		include( 'projects/project-contract.php' );
		include('footer.php');
	} 
	if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contract-print' ) {
		include( 'projects/project-contract-print.php' );
	}
	if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'summary' ) {
		include('header.php');  
		include( 'projects/project-summary.php' );
		include('footer.php');
	}
endwhile;
