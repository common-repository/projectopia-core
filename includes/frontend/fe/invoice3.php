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
			#wpadminbar {display:none}
			body {background: #fff; margin:0; padding:0}
			.white p {color:#fff !important}
			span.invoice_to {font-size:14px; color:#000}
			span.invoice_to p {font-size:14px; color:#000; margin:0; padding:0}
			div.invoice_footer p {font-size:12px;}
			#content {
				margin: 0px auto;
				width: 760px;
				padding: 20px;
			}
			table td, table th {
				border: none !important;
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
<?php }
pto_set_transient('last_invoice',$post->ID);
$main_colour = get_option('cqpim_cool_main_colour');
if ( empty($main_colour) ) {
	$main_colour = '#333';
}
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
$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
$allow_partial = isset($invoice_details['allow_partial']) ? $invoice_details['allow_partial'] : '';
if ( is_numeric($invoice_date) ) { $invoice_date = wp_date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; }
$deposit = isset($invoice_details['deposit']) ? $invoice_details['deposit'] : '';
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
?>
<body style="background:#fff" <?php body_class(); ?>>
	<div style="background: #fff;text-align: left" id="content" role="main">
		<?php if ( current_user_can( 'edit_cqpim_invoices' ) OR $client_user_id == $user_id OR $pass == md5($post->post_password) OR in_array($user->ID, $client_ids) ) { ?>	
			<table style="width:100%; border-collapse:collapse">
				<tr>
					<td style="padding:15px 15px 15px 0;">
						<?php if ( ! empty($inv_logo['cqpim_invoice_logo']) ) { ?>
							<img src="<?php echo isset($inv_logo['cqpim_invoice_logo']) ? esc_url( $inv_logo['cqpim_invoice_logo'] ) : ''; ?>" style="max-height: 130px;" />
						<?php } else { ?>
							<img src="<?php echo isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : ''; ?>" style="max-height: 130px;" />
						<?php } ?>
					</td>
					<td style="padding:15px;">
						<div style="border-bottom:5px solid <?php echo esc_attr( $main_colour ); ?>;">
							<span style="font-size:12px"><?php esc_html_e('Invoice Date:', 'projectopia-core'); ?></span><br />
							<span style="font-size:18px; font-weight:bold"><?php echo esc_html( $invoice_date ); ?></span>
						</div>
					</td>
					<td style="padding:15px;">
						<div style="border-bottom:5px solid <?php echo esc_attr( $main_colour ); ?>;">
							<span style="font-size:12px"><?php esc_html_e('Invoice #:', 'projectopia-core'); ?></span><br />
							<span style="font-size:18px; font-weight:bold">
								<?php if ( ! empty($client_invoice_prefix) ) {
									echo esc_html( $client_invoice_prefix . $invoice_id );
								} elseif ( empty($client_invoice_prefix) && ! empty($system_invoice_prefix) ) {
									echo esc_html( $system_invoice_prefix . $invoice_id );
								} else {
									echo esc_html( $invoice_id );
								} ?>						
							</span>
						</div>
					</td>
					<td style="padding:15px 0 15px 15px;">
						<div style="border-bottom:5px solid <?php echo esc_attr( $main_colour ); ?>;">
							<span style="font-size:12px"><?php esc_html_e('Total:', 'projectopia-core'); ?></span><br />
							<span style="font-size:18px; font-weight:bold"><?php echo esc_html( pto_calculate_currency($post->ID, $total) ); ?></span>
						</div>
					</td>
				</tr>
			</table>
			<div style="text-align:center; padding-bottom:20px; font-size:28px; font-weight:100">
				<?php esc_html_e('INVOICE', 'projectopia-core'); ?>
			</div>
			<table style="width:100%; border-collapse:collapse">
				<tr>
					<td style="padding:15px 35px 0 0;">
						<div style="background:<?php echo esc_attr( $main_colour ); ?>; padding:30px;">
							<span style="color:#fff;" class="invoice_to">
								<span style="color:#fff; font-size:14px; font-weight:bold"><?php esc_html_e('INVOICE TO:', 'projectopia-core'); ?></span><br />
								<?php if ( ! empty($owner) ) { echo esc_html( $owner->display_name ) . ' - '; } ?>
								<?php echo esc_html( $client_company ); ?> <br />
								<?php echo esc_textarea( $client_address ); ?> <br />
								<?php echo esc_html( $client_postcode ); ?><br />
								<?php if ( ! empty($client_tax_name) ) { ?>
									<?php if ( ! empty($client_tax_name) ) { ?>
									<span style="color:#fff; font-size:12px; font-weight:bold"><?php 
										/* translators: %s: Tax Name */
										printf(esc_html__('%s Reg Number: ', 'projectopia-core'), esc_html( $client_tax_name )); ?></span> <?php echo esc_html( $client_tax_reg ); ?><br />
									<?php } ?>
									<?php if ( ! empty($client_stax_name) ) { ?>
										<span style="color:#fff; font-size:12px; font-weight:bold"><?php 
											/* translators: %s: Secondary Tax Name */
											printf(esc_html__('%s Reg Number: ', 'projectopia-core'), esc_html( $client_stax_name )); ?></span> <?php echo esc_html( $client_stax_reg ); ?><br />
									<?php } ?>
								<?php } ?>
							</span>
						</div>
					</td>
					<td style="padding:15px 0 0 15px;">
						<div style="padding:30px 30px 30px 0;">
							<span class="invoice_to">
								<?php if ( $company_number ) { ?>
									<span style="color:<?php echo esc_attr( $main_colour ); ?>; font-size:14px; font-weight:bold"><?php esc_html_e('Company Reg Number:', 'projectopia-core'); ?></span> <span style="font-size:12px"><?php echo esc_html( $company_number ); ?></span><br />
								<?php } ?>
								<?php if ( $vat_rate ) { ?>
									<span style="color:<?php echo esc_attr( $main_colour ); ?>; font-size:14px; font-weight:bold"><?php echo esc_html( get_option('sales_tax_name') ); ?> <?php esc_html_e('Number', 'projectopia-core'); ?>:</span> <span style="font-size:12px"><?php echo esc_html( $tax_reg ); ?></span><br />					
								<?php } ?>						
								<?php if ( $svat_rate ) { ?>
								<span style="color:<?php echo esc_attr( $main_colour ); ?>; font-size:14px; font-weight:bold"><?php echo esc_html( get_option('secondary_sales_tax_name') ); ?> <?php esc_html_e('Number', 'projectopia-core'); ?>:</span> <span style="font-size:12px"><?php echo esc_html( $stax_reg ); ?></span><br />						
									<?php } ?>
								<span style="color:<?php echo esc_attr( $main_colour ); ?>; font-size:14px; font-weight:bold"><?php esc_html_e('Due Date:', 'projectopia-core'); ?></span>						
								<span style="font-size:12px">						
									<?php if ( empty($on_receipt) ) { ?>							
										<?php if ( $due ) { echo esc_html( $due ); } else { ?>
											<?php echo esc_html( wp_date(get_option('cqpim_date_format'), $due) ); ?>
										<?php } ?>						
									<?php } else { ?>
										<?php esc_html_e('Due on Receipt', 'projectopia-core'); ?>
									<?php } ?>						
								</span><br />					
								<span style="color:<?php echo esc_attr( $main_colour ); ?>; font-size:14px; font-weight:bold"><?php esc_html_e('Project:', 'projectopia-core'); ?></span> <span style="font-size:12px"><?php echo esc_html( $project_ref ); ?></span><br />
							</span>
						</div>
					</td>
				</tr>
			</table>
			<?php
			$data = get_option('cqpim_custom_fields_invoice');
			if ( ! empty($data) ) {
				$form_data = json_decode($data);
				$fields = $form_data;
			}
			$values = get_post_meta($post->ID, 'custom_fields', true);
			if ( ! empty($fields) ) {
				echo '<div><div id="cqpim-custom-fields">';
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
						echo '<span style="color:' . esc_attr( $main_colour ) . '; font-size:14px; font-weight:bold">' . esc_html( $field->label ) . ': </span>';
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
				echo '</div></div>';
			}
			?>
			<table style="width:100%; border: 0; border-collapse:collapse; margin-top:15px;text-align:center;">
				<thead>
					<tr style="text-align:center;">
						<th style="background:<?php echo esc_attr( $main_colour ); ?>; border:0; color:#fff; padding:16px; font-size:12px; text-align:center"><?php esc_html_e('Qty', 'projectopia-core'); ?></th>
						<th style="background:<?php echo esc_attr( $main_colour ); ?>; border:0; color:#fff; padding:16px; font-size:12px"><?php esc_html_e('Description', 'projectopia-core'); ?></th>
						<th style="width:120px; background:<?php echo esc_attr( $main_colour ); ?>; border:0; color:#fff; padding:16px; font-size:12px"><?php esc_html_e('Rate', 'projectopia-core'); ?></th>
						<th style="width:120px; background:<?php echo esc_attr( $main_colour ); ?>; border:0; color:#fff; padding:16px; font-size:12px"><?php esc_html_e('Total', 'projectopia-core'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				if ( empty($line_items) ) {
					$line_items = array();
				}               
				foreach ( $line_items as $item ) { ?>
					<tr>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px; text-align:center"><?php echo esc_html( $item['qty'] ); ?></td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo wp_kses_post( $item['desc'] ); ?></td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $item['price']) ); ?></td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $item['sub']) ); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td style="border:0; text-align:left;" class="no_border" colspan="2" rowspan="6"><div style="font-size:13px;"><?php echo wp_kses_post( wpautop($invoice_footer) ); ?></div></td>
						<td style="background:#fff; border:0; color:<?php echo esc_attr( $main_colour ); ?>; padding:10px; font-size:13px; text-align:right; font-weight:bold"><?php if ( $vat_rate ) { ?><?php esc_html_e('Subtotal:', 'projectopia-core'); ?><?php } else { ?><?php esc_html_e('TOTAL:', 'projectopia-core'); ?><?php } ?></td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $sub) ); ?></td>
					</tr>
				<?php 
				$outstanding = $sub;
				if ( $vat_rate ) { 
					$outstanding = $total;
					$tax_name = get_option('sales_tax_name'); ?>
					<tr>
						<td style="background:#fff; border:0; color:<?php echo esc_attr( $main_colour ); ?>; padding:10px; font-size:13px;; text-align:right; font-weight:bold"><?php echo esc_html( $tax_name ); ?>:</td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $vat) ); ?></td>
					</tr>
					<?php if ( ! empty($svat_rate) ) { ?>
						<tr>
						<td style="background:#fff; border:0; color:<?php echo esc_attr( $main_colour ); ?>; padding:10px; font-size:13px;; text-align:right; font-weight:bold"><?php echo esc_html( $stax_name ); ?>:</td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $svat) ); ?></td>
						</tr>					
					<?php } ?>
					<tr>
						<td style="background:#fff; border:0; color:<?php echo esc_attr( $main_colour ); ?>; padding:10px; font-size:13px;; text-align:right; font-weight:bold"><?php esc_html_e('TOTAL:', 'projectopia-core'); ?></td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $total) ); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<td style="background:#fff; border:0; color:<?php echo esc_attr( $main_colour ); ?>; padding:10px; font-size:13px;; text-align:right; font-weight:bold"><?php esc_html_e('Received:', 'projectopia-core'); ?></td>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $received) ); ?></td>
					</tr>
					<tr>
						<td style="background:#fff; border:0; color:<?php echo esc_attr( $main_colour ); ?>; padding:10px; font-size:13px;; text-align:right; font-weight:bold"><?php esc_html_e('Outstanding:', 'projectopia-core'); ?></td>
						<?php $outstanding = $outstanding - $received; ?>
						<td style="background:#fff; border:0; border-bottom:1px solid #ececec; color:#000; padding:10px; font-size:13px;"><?php echo esc_html( pto_calculate_currency($post->ID, $outstanding) ); ?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<table style="width:100%; border-collapse:collapse">
				<tr>
					<td style="padding:15px 15px 15px 0;">
						<span style="color:<?php echo esc_attr( $main_colour ); ?>"><?php echo wp_kses_post( nl2br( $company_address ) ); ?><br /><?php echo esc_html( $company_postcode ); ?></span>
					</td>
					<td style="padding:15px 0 15px 15px;">
						<span style="color:<?php echo esc_attr( $main_colour ); ?>"><?php esc_html_e('Tel: ', 'projectopia-core'); ?> <?php echo esc_html( $company_telephone ); ?></span><br />
						<span style="color:<?php echo esc_attr( $main_colour ); ?>"><?php esc_html_e('Email: ', 'projectopia-core'); ?></span> <a style="color:<?php echo esc_attr( $main_colour ); ?>" href="mailto:<?php echo esc_attr( $company_accounts_email ); ?>"><?php echo esc_html( $company_accounts_email ); ?></a>
					</td>
				</tr>
			</table>
		<?php } else { ?>
			<h1><?php esc_html_e('Access Denied', 'projectopia-core'); ?></h1>
		<?php } ?>
	</div>
<?php if ( $p_mode == 'print' ) {
	wp_footer();
} ?>
</body>
</html>