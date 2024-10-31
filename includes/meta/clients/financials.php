<?php
function pto_client_financials_metabox_callback( $post ) {
 	wp_nonce_field( 'client_financials_metabox', 'client_financials_metabox_nonce' );

	$client_details = get_post_meta($post->ID, 'client_details', true);
	$invoice_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
	$billing_email = isset($client_details['billing_email']) ? $client_details['billing_email'] : '';
	$tax_disabled = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
	$stax_disabled = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
	$client_tax_reg = isset($client_details['client_tax_reg']) ? $client_details['client_tax_reg'] : '';
	$client_stax_reg = isset($client_details['client_stax_reg']) ? $client_details['client_stax_reg'] : '';
	$client_tax_name = isset($client_details['client_tax_name']) ? $client_details['client_tax_name'] : '';
	$client_stax_name = isset($client_details['client_stax_name']) ? $client_details['client_stax_name'] : '';
	$client_invoice_prefix = isset($client_details['client_invoice_prefix']) ? $client_details['client_invoice_prefix'] : '';
	$currency_override = get_option('allow_client_currency_override');
	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space');
	$client_currency_symbol = get_post_meta($post->ID, 'currency_symbol', true);
	$client_currency_symbol = ! empty($client_currency_symbol) ? $client_currency_symbol : $currency;
	$client_currency_code = get_post_meta($post->ID, 'currency_code', true);
	$client_currency_code = ! empty($client_currency_code) ? $client_currency_code : $currency_code;
	$client_currency_space = get_post_meta($post->ID, 'currency_space', true);
	$client_currency_space = ( empty($client_currency_space) && 'private' !== $post->post_status ) ? $currency_space : $client_currency_space;
	$client_currency_position = get_post_meta($post->ID, 'currency_position', true);
	$client_currency_position = ! empty($client_currency_position) ? $client_currency_position : $currency_position;
	$system_invoice_terms = get_option('company_invoice_terms');
	$system_tax_name = get_option('sales_tax_name');
	$system_tax_rate = get_option('sales_tax_rate');
	$system_stax_name = get_option('secondary_sales_tax_name');
	$system_stax_rate = get_option('secondary_sales_tax_rate');

	pto_generate_fields( array(
		'type'      => 'email',
		'id'        => 'dummy_text',
		'attribute' => 'style="display: none;"',
	) );
		
	pto_generate_fields( array(
		'type'        => 'email',
		'id'          => 'billing_email_address',
		'value'       => $billing_email,
		'label'       => __( 'Billing Email Address', 'projectopia-core' ),
		'placeholder' => true,
		'attribute'   => 'autocomplete="off"',
		'tooltip'     => __( 'If you would like to override the email address that invoices are sent to for this client, enter one here.', 'projectopia-core' ),
		'row_start'   => true,
		'col'         => true,
	) );
		
	pto_generate_fields( array(
		'id'          => 'client_invoice_prefix',
		'value'       => $client_invoice_prefix,
		'label'       => __( 'Invoice Prefix', 'projectopia-core' ),
		'tooltip'     => __( 'If you want to override the system invoice prefix for this client, enter one here.', 'projectopia-core' ),
		'placeholder' => true,
		'row_end'     => true,
		'col'         => true,
	) );

	pto_generate_fields( array(
		'id'          => 'client_tax_name',
		'value'       => $client_tax_name,
		'label'       => __( 'Tax 1 Name', 'projectopia-core' ),
		'tooltip'     => __( 'If you want to show your client\'s tax name on invoices, you can enter them here.', 'projectopia-core' ),
		'placeholder' => true,
		'row_start'   => true,
		'col'         => true,
	) );

	pto_generate_fields( array(
		'id'          => 'client_tax_reg',
		'value'       => $client_tax_reg,
		'label'       => __( 'Tax 1 Registration Number', 'projectopia-core' ),
		'tooltip'     => __( 'If you want to show your client\'s tax registration number on invoices, you can enter them here.', 'projectopia-core' ),
		'placeholder' => true,
		'row_end'     => true,
		'col'         => true,
	) );

	pto_generate_fields( array(
		'id'          => 'client_stax_name',
		'value'       => $client_stax_name,
		'label'       => __( 'Tax 2 Name', 'projectopia-core' ),
		'tooltip'     => __( 'If you want to show your client\'s secondary tax name on invoices, you can enter them here.', 'projectopia-core' ),
		'placeholder' => true,
		'row_start'   => true,
		'col'         => true,
	) );

	pto_generate_fields( array(
		'id'          => 'client_stax_reg',
		'value'       => $client_stax_reg,
		'label'       => __( 'Tax 2 Registration Number', 'projectopia-core' ),
		'tooltip'     => __( 'If you want to show your client\'s secondary tax registration number on invoices, you can enter them here.', 'projectopia-core' ),
		'placeholder' => true,
		'row_end'     => true,
		'col'         => true,
	) );
	
	if ( ! empty( $system_tax_rate ) ) {
		pto_generate_fields( array(
			'type'    => 'checkbox',
			'id'      => 'tax_disabled',
			'checked' => 1 == $tax_disabled,
			/* translators: %1$s: System Tax Name, %2$s: System Tax Rate */
			'label'   => sprintf( __( 'This client should NOT be charged %1$s @ %2$s', 'projectopia-core' ), $system_tax_name, $system_tax_rate . '%' ),
		) );
	}

	if ( ! empty( $system_stax_rate ) ) {
		pto_generate_fields( array(
			'type'    => 'checkbox',
			'id'      => 'stax_disabled',
			'checked' => 1 == $stax_disabled,
			/* translators: %1$s: System Secondary Tax Name, %2$s: System Secondary Tax Rate */
			'label'   => sprintf( __( 'This client should NOT be charged %1$s @ %2$s', 'projectopia-core' ), $system_stax_name, $system_stax_rate . '%' ),
		) );
	}

	pto_generate_fields( array(
		'type'    => 'select',
		'id'      => 'invoice_terms',
		'value'   => $invoice_terms,
		'label'   => __( 'Invoice Terms', 'projectopia-core' ),
		'tooltip' => __( 'If you would like to override the email address that invoices are sent to for this client, enter one here.', 'projectopia-core' ),
		'default' => false,
		'options' => array(
			/* translators: %s: No. of days */
			''   => __( 'Use Company Terms', 'projectopia-core' ) . ' ' . sprintf( _n( '(%s day)', '(%s days)', $system_invoice_terms, 'projectopia-core' ), $system_invoice_terms ),
			'1'  => __( 'Due on Receipt', 'projectopia-core' ),
			'7'  => __( '7 days', 'projectopia-core' ),
			'14' => __( '14 days', 'projectopia-core' ),
			'28' => __( '28 days', 'projectopia-core' ),
			'30' => __( '30 days', 'projectopia-core' ),
			'60' => __( '60 days', 'projectopia-core' ),
			'90' => __( '90 days', 'projectopia-core' ),
		),
	) );
	
	if ( $currency_override == 1 ) { ?>
		<p class="sbold"><?php esc_html_e('Currency Override', 'projectopia-core'); ?><i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" style="margin-left: 8px;" title="<?php esc_html_e('If you would like to override the system currency settings for this client you can do so here. The settings here will take precedence over system settings and will be applied to any quote/estimate, project and invoice that is assigned to this client, unless those are overriden manually.', 'projectopia-core'); ?>"></i></p>
		<div class="cqpim-alert cqpim-alert-info alert-display">
			<strong><?php esc_html_e('System Currency Settings', 'projectopia-core'); ?></strong><br />
			<?php esc_html_e('Currency Symbol:', 'projectopia-core'); ?> <?php echo esc_html( $currency ); ?><br />
			<?php esc_html_e('Currency Code:', 'projectopia-core'); ?> <?php echo esc_html( $currency_code ); ?><br />
			<?php esc_html_e('Currency Position:', 'projectopia-core'); ?> <?php if ( $currency_position == 'l' ) { esc_html_e('Before Amount', 'projectopia-core'); } else { esc_html_e('After Amount', 'projectopia-core'); } ?><br />
			<?php esc_html_e('Currency Space:', 'projectopia-core'); ?> <?php if ( $currency_space == '1' ) { esc_html_e('Yes', 'projectopia-core'); } else { esc_html_e('No', 'projectopia-core'); } ?>
		</div>

		<?php

		pto_generate_fields( array(
			'id'          => 'currency_symbol',
			'value'       => $client_currency_symbol,
			'label'       => __( 'Client Currency Symbol', 'projectopia-core' ),
			'tooltip'     => __( 'Leave blank to use system currency symbol', 'projectopia-core' ),
			'placeholder' => true,
			'row_start'   => true,
			'col'         => true,
			'col_num'     => 4,
		) );

		pto_generate_fields( array(
			'type'    => 'select',
			'id'      => 'currency_code',
			'value'   => $client_currency_code,
			'label'   => __( 'Client Currency Code', 'projectopia-core' ),
			'tooltip' => __( 'Leave blank to use system currency code. Some of the available currencies are not supported by PayPal.', 'projectopia-core' ),
			'col'     => true,
			'col_num' => 4,
			'options' => pto_return_currency_select(),
		) );

		pto_generate_fields( array(
			'type'    => 'select',
			'id'      => 'currency_position',
			'value'   => $client_currency_position,
			'label'   => __( 'Client Currency Symbol Position', 'projectopia-core' ),
			'tooltip' => __( 'Leave blank to use system currency position', 'projectopia-core' ),
			'row_end' => true,
			'col'     => true,
			'col_num' => 4,
			'options' => array(
				'l' => __( 'Before Amount', 'projectopia-core' ),
				'r' => __( 'After Amount', 'projectopia-core' ),
			),
		) );
		
		pto_generate_fields( array(
			'type'    => 'checkbox',
			'id'      => 'currency_space',
			'value'   => $client_currency_space,
			'label'   => __( 'Add a space between the currency symbol and amount.', 'projectopia-core' ),
			'checked' => $client_currency_space == '1',
		) );
	}
}

add_action( 'save_post_cqpim_client', 'save_pto_client_financials_metabox_data' );
function save_pto_client_financials_metabox_data( $post_id ) {
	if ( ! isset( $_POST['client_financials_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['client_financials_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'client_financials_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$currency_symbol = isset($_POST['currency_symbol']) ? sanitize_text_field( wp_unslash( $_POST['currency_symbol'] ) ) : '';
	$currency_code = isset($_POST['currency_code']) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : '';
	$currency_space = isset($_POST['currency_space']) ? sanitize_text_field( wp_unslash( $_POST['currency_space'] ) ) : '';
	$currency_position = isset($_POST['currency_position']) ? sanitize_text_field( wp_unslash( $_POST['currency_position'] ) ) : '';
	update_post_meta($post_id, 'currency_symbol', $currency_symbol);
	update_post_meta($post_id, 'currency_code', $currency_code);
	update_post_meta($post_id, 'currency_space', $currency_space);
	update_post_meta($post_id, 'currency_position', $currency_position);
	if ( isset($_POST['invoice_terms']) ) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$invoice_terms = sanitize_text_field( wp_unslash( $_POST['invoice_terms'] ) );
		$client_details['invoice_terms'] = $invoice_terms;
		update_post_meta($post_id, 'client_details', $client_details);
	}   
	if ( isset($_POST['client_invoice_prefix']) ) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$invoice_terms = sanitize_text_field( wp_unslash( $_POST['client_invoice_prefix'] ) );
		$client_details['client_invoice_prefix'] = $invoice_terms;
		update_post_meta($post_id, 'client_details', $client_details);
	}
	if ( isset($_POST['billing_email_address']) ) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$billing_email = sanitize_email( wp_unslash( $_POST['billing_email_address'] ) );
		$client_details['billing_email'] = $billing_email;
		update_post_meta($post_id, 'client_details', $client_details);
	}
	$client_details = get_post_meta($post_id, 'client_details', true);
	$client_details['client_tax_name'] = isset($_POST['client_tax_name']) ? sanitize_text_field( wp_unslash( $_POST['client_tax_name'] ) ) : '';
	$client_details['client_stax_name'] = isset($_POST['client_stax_name']) ? sanitize_text_field( wp_unslash( $_POST['client_stax_name'] ) ) : '';
	$client_details['client_tax_reg'] = isset($_POST['client_tax_reg']) ? sanitize_text_field( wp_unslash( $_POST['client_tax_reg'] ) ) : '';
	$client_details['client_stax_reg'] = isset($_POST['client_stax_reg']) ? sanitize_text_field( wp_unslash( $_POST['client_stax_reg'] ) ) : '';
	update_post_meta($post_id, 'client_details', $client_details);  
	if ( isset($_POST['tax_disabled']) ) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$tax_disabled = sanitize_text_field( wp_unslash( $_POST['tax_disabled'] ) );
		$client_details['tax_disabled'] = $tax_disabled;
		update_post_meta($post_id, 'client_details', $client_details);  
	} else {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_details['tax_disabled'] = 0;
		update_post_meta($post_id, 'client_details', $client_details);
	}
	if ( isset($_POST['stax_disabled']) ) {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$stax_disabled = sanitize_text_field( wp_unslash( $_POST['stax_disabled'] ) );
		$client_details['stax_disabled'] = $stax_disabled;
		update_post_meta($post_id, 'client_details', $client_details);  
	} else {
		$client_details = get_post_meta($post_id, 'client_details', true);
		$client_details['stax_disabled'] = 0;
		update_post_meta($post_id, 'client_details', $client_details);
	}
}