<?php
$p_mode = isset( $_GET['mode'] ) ? sanitize_text_field(wp_unslash($_GET['mode'])) : 'print';
if ( $p_mode == 'print' ) { ?>
	<!DOCTYPE html>
	<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
	<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
	<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title><?php wp_title(); ?></title>   
		<?php wp_head(); ?>
		<?php echo '<style>' . esc_textarea( get_option('cqpim_dash_css') ) . '</style>'; ?>
		<style>
			#content {
				margin: 0px auto;
				width: 760px;
				padding: 20px;
			}
		</style>
	</head>
<?php } else { ?>
	<!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <style>
                body {
                    font-family: DejaVu Sans !important;
                    background-color: #fff !important;
					font-size: 13px;
                }
				h2 {
                    font-family: DejaVu Sans !important;
					font-size: 18px;
                }
            </style>
        </head>
<?php } ?>
<body <?php body_class(); ?> style="background:#fff">
	<div style="background: #fff;text-align: left" id="content" role="main">
		<?php
		pto_set_transient('last_invoice',$post->ID);
		$user = wp_get_current_user(); 
		$user_id = $user->ID;
		$inv_logo = get_option('cqpim_invoice_logo');
		$logo = get_option('company_logo');
		$logo_url = isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : '';
		$p_title = get_the_title();
		$p_title = str_replace('Protected: ', '', $p_title);
		$company_name = get_option('company_name');
		$company_address = get_option('company_address');
		$company_number = get_option('company_number');
		$company_postcode = get_option('company_postcode');
		$company_telephone = get_option('company_telephone');
		$company_accounts_email = get_option('company_accounts_email');
		$currency = get_option('currency_symbol');
		$vat_rate = get_post_meta($post->ID, 'tax_rate', true);
		$svat_rate = get_post_meta($post->ID, 'stax_rate', true);
		$tax_name = get_option('sales_tax_name');
		$tax_reg = get_option('sales_tax_reg');
		$stax_name = get_option('secondary_sales_tax_name');
		$stax_reg = get_option('secondary_sales_tax_reg');
		$invoice_terms = get_option('company_invoice_terms');
		if ( $vat_rate ) {
			$vat_string = '';
		} else {
			$vat_string = '';
		} 
		$invoice_details = get_post_meta($post->ID, 'invoice_details', true);
		$invoice_payments = get_post_meta($post->ID, 'invoice_payments', true);
		if ( empty($invoice_payments) ) {
			$invoice_payments = array();
		}
		$received = 0;
		foreach ( $invoice_payments as $payment ) {
			$amount = isset($payment['amount']) ? $payment['amount'] : 0;
			$received = $received + (float) $amount;
		}
		$invoice_id = get_post_meta($post->ID, 'invoice_id', true);
		$client_contact = get_post_meta($post->ID, 'client_contact', true);
		$owner = get_user_by('id', $client_contact);
		$project_id = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
		$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
		$invoice_date_stamp = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
		$allow_partial = isset($invoice_details['allow_partial']) ? $invoice_details['allow_partial'] : '';
		if ( is_numeric($invoice_date) ) { $invoice_date = wp_date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
		$deposit = isset($invoice_details['deposit']) ? $invoice_details['deposit'] : '';
		$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
		if ( is_numeric($due) ) { $due = wp_date(get_option('cqpim_date_format'), $due); } else { $due = $due; }
		if ( ! empty($project_id) ) {
			$project_details = get_post_meta($project_id, 'project_details', true);
			$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
		} else {
			$project_ref = __('N/A', 'projectopia-core');
		}
		$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_ids = get_post_meta($client_id, 'client_ids', true);
		$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
		$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
		$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
		$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
		$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
		$client_tax_name = isset($client_details['client_tax_name']) ? $client_details['client_tax_name'] : '';
		$client_stax_name = isset($client_details['client_stax_name']) ? $client_details['client_stax_name'] : '';
		$client_tax_reg = isset($client_details['client_tax_reg']) ? $client_details['client_tax_reg'] : '';
		$client_stax_reg = isset($client_details['client_stax_reg']) ? $client_details['client_stax_reg'] : '';
		$client_invoice_prefix = isset($client_details['client_invoice_prefix']) ? $client_details['client_invoice_prefix'] : '';
		$system_invoice_prefix = get_option('cqpim_invoice_prefix');
		$line_items = get_post_meta($post->ID, 'line_items', true);
		$p_totals = get_post_meta($post->ID, 'invoice_totals', true);
		$sub = isset($p_totals['sub']) ? $p_totals['sub'] : '';
		$vat = isset($p_totals['tax']) ? $p_totals['tax'] : '';
		$svat = isset($p_totals['stax']) ? $p_totals['stax'] : '';
		$total = isset($p_totals['total']) ? $p_totals['total'] : '';
		$invoice_footer = get_option('client_invoice_footer');
		$invoice_footer = pto_replacement_patterns($invoice_footer, $post->ID, 'invoice');
		$terms_over = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
		$on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
		$pass = isset($_GET['pwd']) ? sanitize_text_field(wp_unslash($_GET['pwd'])) : '';
		$looper = get_post_meta($post->ID, 'looper', true);
		$looper = $looper ? $looper : 0;
		if ( time() - $looper > 5 && in_array('cqpim_client', $user->roles) ) {
			$user = wp_get_current_user();
			$client_logs = get_post_meta($client_id, 'client_logs', true);
			if ( empty($client_logs) ) {
				$client_logs = array();
			}
			$now = time();
			$client_logs[ $now ] = array(
				'user' => $user->ID,
				/* translators: %s: Invoice Title */
				'page' => sprintf(esc_html__('Invoice - %1$s', 'projectopia-core'), $p_title),
			);
			update_post_meta($client_id, 'client_logs', $client_logs);
			update_post_meta($post->ID, 'looper', time());
		}
		if ( current_user_can( 'edit_cqpim_invoices' ) OR $client_user_id == $user_id OR $pass == md5($post->post_password) OR in_array($user->ID, $client_ids) ) { ?>
			<table style="width:100%; border: 1px solid #fff; border-collapse:collapse">
				<tr>
					<td style="margin:0; padding:0;">
						<?php if ( ! empty($inv_logo['cqpim_invoice_logo']) ) { ?>
							<img src="<?php echo isset($inv_logo['cqpim_invoice_logo']) ? esc_url( $inv_logo['cqpim_invoice_logo'] ) : ''; ?>" style="max-height: 60px;" />
						<?php } else { ?>
							<img src="<?php echo isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : ''; ?>" style="max-height: 60px;" />
						<?php } ?>
					</td>
					<td style="margin:0; padding:0;font-size:30px;text-align: right;">
						<?php esc_html_e('INVOICE', 'projectopia-core'); ?>
					</td>
				</tr>
			</table>
			<div class="clear"></div>
			<?php
			$now = time();
			if ( empty($on_receipt) ) {
				if ( empty($invoice_details['paid']) ) {
					if ( $terms_over ) {
						if ( $now > $terms_over ) {
							echo '<div style="background:#fff; text-align:center; padding:0;color:#000; font-weight:bold">' . esc_html__('THIS INVOICE IS OVERDUE', 'projectopia-core') . '</div><br />';      
						}
					}
				}
			}
			?>
			<?php if ( ! empty($invoice_details['paid']) && $invoice_details['paid'] == true ) { ?>	
				<div class="contract-confirmed">
					<div style="background:#fff; text-align:center; padding:0;color:#000; font-weight:bold"><?php esc_html_e('THIS INVOICE HAS BEEN PAID', 'projectopia-core'); ?></div>
				</div>
				<br /><br />
			<?php } ?>
			<div>
				<table style="width:100%; border: 1px solid #fff; border-collapse:collapse">
					<tr>
						<td style="padding-right: 10px;">
							<div style="background:#f9f9f9;padding:10px;text-align:left">
								<h3 style="margin:0"><?php esc_html_e('Invoice From:', 'projectopia-core'); ?></h3>
								<p>	<?php echo esc_html( $company_name ); ?> </p>
								<?php echo wp_kses_post( wpautop( $company_address ) ); ?>
								<?php echo wp_kses_post( wpautop( $company_postcode ) ); ?>
								<p>
									<?php esc_html_e('Tel:', 'projectopia-core'); ?> <?php echo esc_html( $company_telephone ); ?><br />
									<?php esc_html_e('Email:', 'projectopia-core'); ?> <a href="mailto:<?php echo esc_attr( $company_accounts_email ); ?>"><?php echo esc_html( $company_accounts_email ); ?></a><br />
									<?php if ( $company_number ) { ?>
										<?php esc_html_e('Company Reg Number:', 'projectopia-core'); ?> <?php echo esc_html( $company_number ); ?>
									<?php } ?>
								</p>
							</div>
						</td>
						<td style="vertical-align: initial;padding-left: 10px;">
							<div style="background:#f9f9f9;padding:10px;text-align:left">
								<h3 style="margin:0"><?php esc_html_e('Invoice To:', 'projectopia-core'); ?></h3>
								<?php if ( ! empty($owner) ) { echo '<p>'. esc_html__('FAO:', 'projectopia-core') . ' ' . esc_html( $owner->display_name ) . '</p>'; } ?>
								<p>	<?php echo esc_html( $client_company ); ?> </p>
								<?php echo wp_kses_post( wpautop( $client_address ) ); ?>
								<?php do_action( 'pto_invoice_after_client_address', $post->ID ); ?>
								<?php echo wp_kses_post( wpautop( $client_postcode ) ); ?>
								<p>
									<?php if ( ! empty($owner) ) { ?>
										<?php esc_html_e('Email:', 'projectopia-core'); ?> <a href="mailto:<?php echo esc_attr( $owner->user_email ); ?>"><?php echo esc_html( $owner->user_email ); ?></a>
									<?php } else { ?>
										<?php esc_html_e('Email:', 'projectopia-core'); ?> <a href="mailto:<?php echo esc_attr( $client_email ); ?>"><?php echo esc_html( $client_email ); ?></a>
									<?php } ?>
								</p>
								<?php if ( ! empty($client_tax_name) ) { ?>
									<?php if ( ! empty($client_tax_name) ) { ?>
										<?php 
											/* translators: %s: Tax Name */
											printf(esc_html__('%s Reg Number: ', 'projectopia-core'), esc_html( $client_tax_name )); ?> <?php echo esc_html( $client_tax_reg ); ?>
									<?php } ?>
									<?php if ( ! empty($client_stax_name) ) { ?>
										<br /><?php 
											/* translators: %s: Secondary Tax Name */
											printf(esc_html__('%s Reg Number: ', 'projectopia-core'), esc_html( $client_stax_name )); ?> <?php echo esc_html( $client_stax_reg ); ?>
									<?php } ?>
								<?php } ?>
							</div>
						</td>
					</tr>
				</table>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<br />
			<div style="background:#f9f9f9;padding:10px;text-align:left">
				<?php
				$data = get_option('cqpim_custom_fields_invoice');
				if ( ! empty($data) ) {
					$form_data = json_decode($data);
					$fields = $form_data;
				}
				$values = get_post_meta($post->ID, 'custom_fields', true);
				if ( ! empty($fields) ) {
					echo '<div id="cqpim-custom-fields">';
					foreach ( $fields as $field ) {
						$value = '';
						if ( isset( $field->name ) ) {
							$value = isset($values[ $field->name ]) ? $values[ $field->name ] : '';
						}
						$class = isset($field->className) ? $field->className : '';
						$n_id = strtolower($field->label);
						$n_id = str_replace(' ', '_', $n_id);
						$n_id = str_replace('-', '_', $n_id);
						$n_id = preg_replace('/[^\w-]/', '', $n_id);
						echo '<div class="cqpim_form_item">';
						if ( $field->type != 'header' ) {
							echo '<span style="font-size:14px; font-weight:bold">' . esc_html( $field->label ) . ': </span>';
						}
						if ( $field->type == 'header' ) {
							echo '<' . esc_attr( $field->subtype ) . ' class="cqpim-custom ' . esc_attr( $class ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
						} elseif ( $field->type == 'text' ) {  
							echo '<span style="font-size:12px">' . esc_html( $value ) . '</span><br />';
						} elseif ( $field->type == 'website' ) {
							echo '<span style="font-size:12px">' . esc_html( $value ) . '</span><br />';
						} elseif ( $field->type == 'number' ) {
							echo '<span style="font-size:12px">' . esc_html( $value ) . '</span><br />';
						} elseif ( $field->type == 'textarea' ) {
							echo '<span style="font-size:12px">' . esc_textarea( $value ) . '</span><br />';
						} elseif ( $field->type == 'date' ) {
							echo '<span style="font-size:12px">' . esc_html( $value ) . '</span><br />';
						} elseif ( $field->type == 'email' ) {
							echo '<span style="font-size:12px">' . esc_html( $value ) . '</span><br />';
						} elseif ( $field->type == 'checkbox-group' ) {
							$options = $field->values;
							foreach ( $options as $option ) {
								if ( ! empty($option->selected) ) {
									echo '<span style="font-size:12px">' . esc_html( $option->label ) . '</span><br />';
								}
							}
						} elseif ( $field->type == 'radio-group' ) {
							$options = $field->values;
							foreach ( $options as $option ) {
								if ( ! empty($option->selected) ) {
									echo '<span style="font-size:12px">' . esc_html( $option->label ) . '</span><br />';
								}
							}
						} elseif ( $field->type == 'select' ) {
							$options = $field->values;
							foreach ( $options as $option ) {
								if ( ! empty($option->selected) ) {
									echo '<span style="font-size:12px">' . esc_html( $option->label ) . '</span><br />';
								}
							}
						}
						echo '</div>';
					}
					echo '</div>';
				}
				?>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			<br />
			<table style="width:100%; border: 1px solid #fff; border-collapse:collapse" id="invoice_info">
				<thead>
					<tr style="background:#f9f9f9; border:1px solid #fff;text-align:center;">
						<?php if ( $vat_rate ) { ?>
						<th style="border:1px solid #fff; padding:8px"><?php echo esc_html( get_option('sales_tax_name') ); ?> <?php esc_html_e('Number', 'projectopia-core'); ?></th>
						<?php } ?>
						<?php if ( $svat_rate ) { ?>
						<th style="border:1px solid #fff; padding:8px"><?php echo esc_html( get_option('secondary_sales_tax_name') ); ?> <?php esc_html_e('Number', 'projectopia-core'); ?></th>
						<?php } ?>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Date', 'projectopia-core'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Invoice #', 'projectopia-core'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Due Date', 'projectopia-core'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Project', 'projectopia-core'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr style="border:1px solid #fff;text-align:center;">
						<?php if ( $vat_rate ) { ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( $tax_reg ); ?></td>
						<?php } ?>
						<?php if ( $svat_rate ) { ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( $stax_reg ); ?></td>
						<?php } ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( $invoice_date ); ?></td>						
						<td style="border:1px solid #fff; padding:8px">
							<?php if ( ! empty($client_invoice_prefix) ) {
								echo esc_html( $client_invoice_prefix . $invoice_id );
							} elseif ( empty($client_invoice_prefix) && ! empty($system_invoice_prefix) ) {
								echo esc_html( $system_invoice_prefix . $invoice_id );
							} else {
								echo esc_html( $invoice_id );
							} ?>					
						</td>						
							<td style="border:1px solid #fff; padding:8px">
								<?php if ( empty($on_receipt) ) { ?>							
									<?php if ( $due ) { echo esc_html( $due ); } else { ?>
										<?php echo esc_html( wp_date(get_option('cqpim_date_format'), $due) ); ?>
									<?php } ?>						
								<?php } else { ?>
									<?php esc_html_e('Due on Receipt', 'projectopia-core'); ?>
								<?php } ?>	
							</td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( $project_ref ); ?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<table style="width:100%; border: 1px solid #fff; border-collapse:collapse;text-align:center;" id="invoice_items">
				<thead>
					<tr style="background:#f9f9f9">
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Qty', 'projectopia-core'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Description', 'projectopia-core'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Rate', 'projectopia-core'); ?></th>
						<th style="border:1px solid #fff; padding:8px"><?php esc_html_e('Total', 'projectopia-core'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				if ( empty($line_items) ) {
					$line_items = array();
				}               
				foreach ( $line_items as $item ) { ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( $item['qty'] ); ?></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo wp_kses_post( $item['desc'] ); ?></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $item['price']) ); ?></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $item['sub']) ); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php if ( $vat_rate ) { ?><?php esc_html_e('Subtotal:', 'projectopia-core'); ?><?php } else { ?><?php esc_html_e('TOTAL:', 'projectopia-core'); ?><?php } ?></strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $sub) ); ?></td>
					</tr>
				<?php 
				$outstanding = $sub;
				if ( $vat_rate ) { 
					$outstanding = $total;
					$tax_name = get_option('sales_tax_name'); ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php echo esc_html( $tax_name ); ?>:</strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $vat) ); ?></td>
					</tr>
					<?php if ( ! empty($svat_rate) ) { ?>
						<tr>
							<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
							<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php echo esc_html( $stax_name ); ?>:</strong></td>
							<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $svat) ); ?></td>
						</tr>					
					<?php } ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php esc_html_e('TOTAL:', 'projectopia-core'); ?></strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $total) ); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php esc_html_e('Received:', 'projectopia-core'); ?></strong></td>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $received) ); ?></td>
					</tr>
					<tr>
						<td style="border:1px solid #fff; padding:8px" class="no_border" colspan="2"></td>
						<td class="no_border" style="text-align:right; border:1px solid #fff; padding:8px"><strong><?php esc_html_e('Outstanding:', 'projectopia-core'); ?></strong></td>
						<?php $outstanding = $outstanding - $received; ?>
						<td style="border:1px solid #fff; padding:8px"><?php echo esc_html( pto_calculate_currency($post->ID, $outstanding) ); ?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<div style="text-align:left" class="invoice_footer">
				<p><?php echo wp_kses_post( wpautop($invoice_footer) ); ?></p>
				<div class="clear"></div>
			</div>
		<?php } else { ?>
			<h1><?php esc_html_e('Access Denied', 'projectopia-core'); ?></h1>
		<?php } ?>
	</div>
<?php if ( $p_mode == 'print' ) {
	wp_footer();
} ?>
</body>
</html>