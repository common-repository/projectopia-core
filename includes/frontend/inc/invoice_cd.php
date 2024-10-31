<?php 
pto_set_transient('last_invoice', $post->ID);

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
	$amount = ( isset( $payment['amount'] ) && ! empty( $payment['amount'] ) ) ? $payment['amount'] : 0;
	$received = $received + (float) $amount;
}
$invoice_id = get_post_meta($post->ID, 'invoice_id', true);
$client_contact = get_post_meta($post->ID, 'client_contact', true);
$owner = get_user_by('id', $client_contact);
$project_id = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
$invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
$invoice_date_stamp = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
$allow_partial = isset($invoice_details['allow_partial']) ? $invoice_details['allow_partial'] : '';
$timezone   = wp_timezone();
if ( is_numeric($invoice_date) ) { $invoice_date = wp_date( get_option('cqpim_date_format'), $invoice_date, $timezone ); } else { $invoice_date = $invoice_date; }
$deposit = isset($invoice_details['deposit']) ? $invoice_details['deposit'] : '';
$due = isset($invoice_details['due']) ? $invoice_details['due'] : '';
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
		'page' => sprintf(esc_html__('Invoice - %s', 'projectopia-core'), $p_title),
	);
	update_post_meta($client_id, 'client_logs', $client_logs);
	update_post_meta($post->ID, 'looper', time());
}
if ( ! empty($_GET['download_pdf']) ) {
	$invoice = pto_generate_pdf_invoice($post->ID, $invoice_id);
	$invoice_name = basename($invoice);
	header("Content-Type: application/octet-stream");
	header("Content-Transfer-Encoding: Binary");
	header("Content-disposition: attachment; filename=\"$invoice_name\""); 
	echo readfile( $invoice ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile, WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
include('header.php');
?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php if ( $assigned == $client_id ) {
			$ptitle = get_post();
			$ptitle = $ptitle->post_title;
			$p_title = get_the_title(); $p_title = str_replace('Protected:', '', $p_title); echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> <a href="' . esc_url( get_the_permalink($client_dash) ) . '?pto-page=invoices">' . esc_html__('Invoices', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html( $p_title );
			} else {
				esc_html_e('ACCESS DENIED', 'projectopia-core');
			}
			?>
		</div>
		<div id="cqpim-cdash-inside">
			<?php
			if ( $assigned == $client_id ) { ?>
			<div class="masonry-grid">
				<div class="grid-sizer"></div>
				<div class="cqpim-dash-item-triple grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Line Items', 'projectopia-core'); ?></span>
							</div>
						</div>
						<br />
						<table class="cqpim_table">
							<thead>
								<tr>
									<th><?php esc_html_e('Qty', 'projectopia-core'); ?></th>
									<th><?php esc_html_e('Description', 'projectopia-core'); ?></th>
									<th><?php esc_html_e('Rate', 'projectopia-core'); ?></th>
									<th><?php esc_html_e('Total', 'projectopia-core'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							if ( empty($line_items) ) {
								$line_items = array();
							}               
							foreach ( $line_items as $item ) { ?>
								<tr>
									<td><?php echo esc_html( $item['qty'] ); ?></td>
									<td><?php echo wp_kses_post( $item['desc'] ); ?></td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $item['price']) ); ?></td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $item['sub']) ); ?></td>
								</tr>
							<?php } ?>
								<tr>
									<td colspan="3"><?php if ( $vat_rate ) { ?><?php esc_html_e('Subtotal:', 'projectopia-core'); ?><?php } else { ?><?php esc_html_e('TOTAL:', 'projectopia-core'); ?><?php } ?></td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $sub) ); ?></td>
								</tr>
							<?php 
							$outstanding = $sub;
							if ( $vat_rate ) { 
								$outstanding = $total;
								$tax_name = get_option('sales_tax_name'); ?>
								<tr>
									<td colspan="3"><?php echo esc_html( $tax_name ); ?>:</td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $vat) ); ?></td>
								</tr>
								<?php if ( ! empty($svat_rate) ) { ?>
									<tr>
									<td colspan="3"><?php echo esc_html( $stax_name ); ?>:</td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $svat) ); ?></td>
									</tr>					
								<?php } ?>
								<tr>
									<td colspan="3"><?php esc_html_e('TOTAL:', 'projectopia-core'); ?></td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $total) ); ?></td>
								</tr>
							<?php } ?>
								<tr>
									<td colspan="3"><?php esc_html_e('Received:', 'projectopia-core'); ?></td>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $received) ); ?></td>
								</tr>
								<tr>
									<td colspan="3"><?php esc_html_e('Outstanding:', 'projectopia-core'); ?></td>
									<?php $outstanding = $outstanding - $received; ?>
									<td><?php echo esc_html( pto_calculate_currency($post->ID, $outstanding) ); ?></td>
								</tr>
							</tbody>
						</table>
						<br />
						<?php echo wp_kses_post( wpautop($invoice_footer) ); ?>
						<div class="clear"></div>							
					</div>
				</div>
				<div class="cqpim-dash-item-double grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Invoice Details', 'projectopia-core'); ?> </span>
							</div>
						</div>
						<?php $now = time();
						if ( empty($on_receipt) ) {
							if ( empty($invoice_details['paid']) ) {
								if ( $terms_over ) {
									if ( $now > $terms_over ) {
										echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('THIS INVOICE IS OVERDUE', 'projectopia-core') . '</div>';      
									}
								}
							}
						} 
						if ( ! empty($invoice_details['paid']) ) {
							echo '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Invoice Paid', 'projectopia-core') . '</div>';        
						}
						?>
						<span class="invoice_to">
							<?php if ( $company_number ) { ?>
								<p><span><?php esc_html_e('Company Reg Number:', 'projectopia-core'); ?></span> <span><?php echo esc_html( $company_number ); ?></span></p>
							<?php } ?>
								<p><span><?php esc_html_e('Invoice Number:', 'projectopia-core'); ?></span> <span>							
									<?php if ( ! empty($client_invoice_prefix) ) {
										echo esc_html( $client_invoice_prefix . $invoice_id ); 
									} elseif ( empty($client_invoice_prefix) && ! empty($system_invoice_prefix) ) {
										echo esc_html( $system_invoice_prefix . $invoice_id );
									} else {
										echo esc_html( $invoice_id );
									} ?>
								</span></p>
							<?php if ( $vat_rate ) { ?>
								<p><span><?php echo esc_html( get_option('sales_tax_name') ); ?> <?php esc_html_e('Number', 'projectopia-core'); ?>:</span> <span><?php echo esc_html( $tax_reg ); ?></span></p>		
							<?php } ?>						
							<?php if ( $svat_rate ) { ?>
							<p><span><?php echo esc_html( get_option('secondary_sales_tax_name') ); ?> <?php esc_html_e('Number', 'projectopia-core'); ?>:</span> <span><?php echo esc_html( $stax_reg ); ?></span></p>						
								<?php } ?>
							<p><span><?php esc_html_e('Invoice Date:', 'projectopia-core'); ?></span> <span><?php echo esc_html( $invoice_date ); ?></span></p>
							<p><span><?php esc_html_e('Due Date:', 'projectopia-core'); ?></span> <span>
								<?php if ( empty($on_receipt) ) { ?>							
									<?php if ( $due ) { echo esc_html( $due ); } else { ?>
										<?php $due_date = strtotime('+ ' . $invoice_terms . ' days', $invoice_date_stamp);
											echo esc_html( wp_date(get_option('cqpim_date_format'), $due_date) ); ?>
									<?php } ?>						
								<?php } else { ?>
									<?php esc_html_e('Due on Receipt', 'projectopia-core'); ?>
								<?php } ?>								
							</span></p>
							<p><span><?php esc_html_e('Project:', 'projectopia-core'); ?></span> <span><?php echo esc_html( $project_ref ); ?></span></p>
						</span>
						<span class="invoice_to">
							<p><strong><?php esc_html_e('INVOICE TO:', 'projectopia-core'); ?></strong></p>
							<?php if ( ! empty($owner) ) { echo esc_html( $owner->display_name ) . ' - '; } ?>
							<?php echo esc_html( $client_company ); ?> <br />
							<p><?php echo esc_textarea( $client_address ); ?> </p>
							<?php echo wp_kses_post(wpautop($client_postcode)); ?>
							<?php if ( ! empty($client_tax_name) ) { ?>
								<?php if ( ! empty($client_tax_name) ) { ?>
									<p><span><?php 
										/* translators: %s: Tax Name */
										printf(esc_html__('%s Reg Number: ', 'projectopia-core'), esc_html( $client_tax_name )); ?></span> <?php echo esc_html( $client_tax_reg ); ?></p>
								<?php } ?>
								<?php if ( ! empty($client_stax_name) ) { ?>
									<p><span><?php 
										/* translators: %s: Secondary Tax Name */
										printf(esc_html__('%s Reg Number: ', 'projectopia-core'), esc_html( $client_stax_name )); ?></span> <?php echo esc_html( $client_stax_reg ); ?></p>
								<?php } ?>
							<?php } ?>
						</span>
						<div class="clear"></div>
						<div>
							<?php
							$data = get_option('cqpim_custom_fields_invoice');
							if ( ! empty($data) ) {
								$form_data = json_decode($data);
								$fields = $form_data;
							}
							$values = get_post_meta( $post->ID, 'custom_fields', true );
							if ( ! empty($fields) ) {
								echo '<div style="border-top:1px solid #eef1f5; border-bottom:1px solid #eef1f5" id="cqpim-custom-fields">';
								foreach ( $fields as $field ) {
									$value = '';
									if ( isset( $field->name ) ) {
										$value = isset( $values[ $field->name ] ) ? $values[ $field->name ] : '';
									}
									$p_class_name = ! empty( $field->className ) ? $field->className : '';
									echo '<div class="cqpim_form_item">';
									if ( $field->type != 'header' ) {
										echo '<p><span>' . esc_html( $field->label ) . ': </span>';
									}
									if ( $field->type == 'header' ) {
										echo '<' . esc_attr( $field->subtype ) . ' class="cqpim-custom ' . esc_attr( $p_class_name ) . '">' . esc_html( $field->label ) . '</' . esc_attr( $field->subtype ) . '>';
									} elseif ( $field->type == 'text' ) {  
										echo '<span>' . wp_kses_post( make_clickable( $value ) ) . '</span></p>';
									} elseif ( $field->type == 'website' ) {
										echo '<span>' . esc_html( $value ) . '</span></p>';
									} elseif ( $field->type == 'number' ) {
										echo '<span>' . esc_html( $value ) . '</span></p>';
									} elseif ( $field->type == 'textarea' ) {
										echo '<span>' . wp_kses_post( make_clickable( $value ) ) . '</span></p>';
									} elseif ( $field->type == 'date' ) {
										echo '<span>' . esc_html( $value ) . '</span></p>';
									} elseif ( $field->type == 'email' ) {
										echo '<span>' . esc_html( $value ) . '</span></p>';
									} elseif ( $field->type == 'checkbox-group' ) {
										$options = $field->values;
										foreach ( $options as $option ) {
											if ( ! empty($option->selected) ) {
												echo '<span>' . esc_html( $option->label ) . '</span></p>';
											}
										}
									} elseif ( $field->type == 'radio-group' ) {
										$options = $field->values;
										foreach ( $options as $option ) {
											if ( ! empty($option->selected) ) {
												echo '<span>' . esc_html( $option->label ) . '</span></p>';
											}
										}
									} elseif ( $field->type == 'select' ) {
										$options = $field->values;
										foreach ( $options as $option ) {
											if ( ! empty($option->selected) ) {
												echo '<span>' . esc_html( $option->label ) . '</span></p>';
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
						<div>
							<?php if ( empty($invoice_details['paid']) ) { 
								$key = 'payment_amount_' . $post->ID;
								if ( ! empty($_GET['atp']) ) {
									$outstanding = sanitize_text_field(wp_unslash($_GET['atp']));
								}
								$vl = number_format( (float)$outstanding, 2, '.', '');
								pto_set_transient( $key, $vl );
								$amount_to_pay = pto_get_transient( $key );
								$stripe = get_option('client_invoice_stripe_key');
								$ideal = get_option('client_invoice_stripe_ideal');
								$paypal = get_option('client_invoice_paypal_address');
								if ( function_exists('pto_twocheck_return_sid') ) {
									$twocheck = pto_twocheck_return_sid();
								}
								$vat = get_option('sales_tax_rate');
								if ( empty($vat) ) {
									$total = $sub;
								}
								$partial = get_option('client_invoice_allow_partial');
								$user = wp_get_current_user();
								$return = get_option('cqpim_client_page');
								$return = get_the_permalink($return);
								if ( ! empty($stripe) && ! empty($user->ID) || ! empty($twocheck) && ! empty($user->ID) || ! empty($paypal) && ! empty($user->ID) ) {    
									$can_change = ( ! empty( $_GET['atp'] ) && ! empty( $partial ) ) || ! empty( $allow_partial );
									if ( $can_change ) {
										echo '<div id="payment-amount" style="margin-top: 15px;">';
											echo '<span style="font-weight:bold">' . esc_html__('Amount to Pay', 'projectopia-core') . ': </span>';
											echo '<input style="margin: 10px 0;" type="text" id="amount_to_pay" value="' . esc_attr( $amount_to_pay ) . '" />';
											echo ' <button class="cqpim_button cqpim_small_button rounded_2 bg-violet font-white" id="save_amount">' . esc_html__('Update', 'projectopia-core') . '</button><span id="amount_spinner" class="ajax_spinner" style="display:none"></span><br />';
										echo '</div>';
									}
									/* translators: %s: Payment Amount */
									echo '<button style="font-size:14px" class="cqpim_button cqpim_button_link font-white bg-violet block mt-10 rounded_2 block" id="cqpim_pay_now">' . sprintf(esc_html__('Pay %s Now', 'projectopia-core'), esc_html(pto_calculate_currency($post->ID, $amount_to_pay))) . '</button>';
									echo '</div>'; ?>
									<div id="cqpim_payment_methods_container" style="display:none">
										<div id="cqpim_payment_methods" style="padding:10px">
											<h3><?php esc_html_e('Payment Methods', 'projectopia-core'); ?></h3>
											<ul>
												<?php
												$currency_code = get_post_meta( $post->ID, 'currency_code', true );
												if ( ! empty( $paypal ) && in_array( $currency_code, pto_paypal_supported_currencies() ) ) {
													// Get paypal enable sandbox setting
													if ( get_option( 'cqpim_paypal_enable_sandbox' ) ) {
														$paypal_url = 'https://www.sandbox.paypal.com/us/cgi-bin/webscr';
													} else {
														$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
													} ?>
													<li>
														<form action="<?php echo esc_url( $paypal_url ); ?>" method="post" class="paypal">
															<input type="hidden" name="cmd" value="_xclick">
															<input type="hidden" name="business" value="<?php echo esc_textarea( get_option( 'client_invoice_paypal_address' ) ); ?>">
															<input type="hidden" name="item_name" value="<?php echo esc_html( get_option( 'company_name' ) ); ?> - <?php esc_html_e( 'Invoice', 'projectopia-core' ); ?> #<?php echo esc_html( $invoice_id ) ?>">
															<input type="hidden" id="paypal-amount" name="amount" value="<?php echo esc_html( $amount_to_pay ); ?>">
															<input type="hidden" name="quantity" value="1">
															<input type="hidden" name="currency_code" value="<?php echo esc_attr( $currency_code ); ?>">
															<input type="hidden" name="first_name" value="">
															<input type="hidden" name="no_shipping" value="1">
															<input type="hidden" name="rm" value="2">
															<input type="hidden" name="return" value="<?php echo esc_url( $return ); ?>">
															<input type="hidden" name="cancel_return" value="<?php echo esc_url( $return ); ?>">
															<input type="hidden" name="notify_url" value="<?php echo esc_url( $return ); ?>">
															<input style="max-width:100%" type="image" src="<?php echo esc_url( PTO_PLUGIN_URL . '/img/ec-button.png' ); ?>" name="submit" alt="<?php esc_html_e( 'Pay with Paypal', 'projectopia-core' ); ?>">
														</form>										
													</li>
												<?php } ?>
												<?php if ( ! empty( $stripe ) ) { ?>
													<li>
														<?php                                                                                                                    
														require_once( PTO_FE_PATH . '/stripe-sca/init.php' );
														\Stripe\Stripe::setApiKey( get_option( 'client_invoice_stripe_secret' ) );
														
														$c_user = wp_get_current_user();
														$client_id = pto_get_client_from_userid( $c_user );

														$client_details = get_post_meta( $client_id['assigned'], 'client_details', true );
														$client_company = isset( $client_details['client_company'] ) ? $client_details['client_company'] : '';
														$client_contact = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
														$client_address = isset( $client_details['client_address'] ) ? $client_details['client_address'] : '—';
														$client_postcode = isset( $client_details['client_postcode'] ) ? $client_details['client_postcode'] : '';
														$client_telephone = isset( $client_details['client_telephone'] ) ? $client_details['client_telephone'] : '';
														$client_email = isset( $client_details['client_email'] ) ? $client_details['client_email'] : $c_user->user_email;

														if ( ! empty( $client_id ) ) {
															if ( $client_id['type'] == 'admin' ) {
																$client_gw_ids = get_post_meta( $client_id['assigned'], 'client_gw_ids', true );
															} else {
																$client_contacts = get_post_meta( $client_id['assigned'], 'client_contacts', true );
																$client_gw_ids = isset( $client_contacts[ $user->ID ]['client_gw_ids'] ) ? $client_contacts[ $user->ID ]['client_gw_ids'] : '';
															}
														}
														$stripe_customer_id = isset( $client_gw_ids['stripe'] ) ? $client_gw_ids['stripe'] : 0;
														if ( empty( $stripe_customer_id ) ) {
															$customer = \Stripe\Customer::create( [
																'name' => $client_company,
																'address' => [
																	'line1' => $client_address,
																	'postal_code' => $client_postcode,
																],
																'phone' => $client_telephone,
																'email'  => $client_email,
																'description' => __( 'Projectopia Client', 'projectopia-core' ),
															] );

															$stripe_customer_id = $customer->id;
															if ( $client_id['type'] == 'admin' ) {
																$client_gw_ids = get_post_meta( $client_id['assigned'], 'client_gw_ids', true );
																$client_gw_ids = $client_gw_ids && is_array( $client_gw_ids ) ? $client_gw_ids : array();
																$client_gw_ids['stripe'] = $customer->id;
																update_post_meta( $client_id['assigned'], 'client_gw_ids', $client_gw_ids );
															} else {
																$client_contacts = get_post_meta( $client_id['assigned'], 'client_contacts', true );
																$client_contacts = $client_contacts && is_array( $client_contacts ) ? $client_contacts : array();
																$client_contacts[ $user->ID ]['client_gw_ids']['stripe'] = $customer->id;
																update_post_meta( $client_id['assigned'], 'client_contacts', $client_contacts );
															}
														} else {
															$customer = \Stripe\Customer::retrieve( $stripe_customer_id );
															$customer->name = $client_company;
															$customer->address['line1'] = $client_address;
															$customer->address['postal_code'] = trim( $client_postcode );
															$customer->phone = $client_telephone;
															$customer->email = $client_email;
															$customer->description = __( 'Projectopia Client', 'projectopia-core' );
															$customer->save();
														}

														$key = 'payment_amount_' . $post->ID;
														$intent = \Stripe\PaymentIntent::create( [
															'amount'      => $amount_to_pay * 100,
															'currency'    => $currency_code,
															'setup_future_usage' => 'off_session',
															'description' => __( 'Invoice', 'projectopia-core' ) . ' #' . $invoice_id,
															'customer' => $stripe_customer_id,
														] );

														wp_enqueue_script( 'pto_stripe_js' );                            
														?>
														<div id="card-element">
															<!-- Elements will create input elements here -->
														</div>

														<!-- We'll put the error messages in this element -->
														<div id="card-errors" role="alert"></div>

														<button class="cqpim_button cqpim_button_link font-white bg-violet block mt-10 rounded_2 block" id="pto-submit-payment" data-secret="<?php echo esc_attr( $intent->client_secret ); ?>">Pay With Stripe</button>

														<script>
															var stripe = Stripe('<?php echo esc_js( get_option('client_invoice_stripe_key') ); ?>'); // test publishable API key
															var elements = stripe.elements();
															var style = {
																base: {
																	color: '#32325d',
																	fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
																	fontSmoothing: 'antialiased',
																	fontSize: '16px',
																	'::placeholder': {
																		color: '#aab7c4'
																	}
																},
																invalid: {
																	color: '#fa755a',
																	iconColor: '#fa755a'
																}
															};
															var cardElement = elements.create('card',{style: style});
															cardElement.mount('#card-element');
															cardElement.addEventListener('change', function(event) {
																var displayError = document.getElementById('card-errors');
																if (event.error) {
																	displayError.textContent = event.error.message;
																} else {
																	displayError.textContent = '';
																}
															});
															var cardButton = document.getElementById('pto-submit-payment');
															var clientSecret = cardButton.dataset.secret;
															cardButton.addEventListener('click', function(ev){
																stripe.handleCardPayment( clientSecret, cardElement, {} ).then(function(result){
																	if ( result.error ) {
																		alert(result.error.message);
																	} else {
																		var form = document.getElementById('pto-stripe-form');
																		//Append payment success input
																		var hiddenInput = document.createElement('input');
																		hiddenInput.setAttribute('type', 'hidden');
																		hiddenInput.setAttribute('name', 'payment_status_stripe');
																		hiddenInput.setAttribute('value', 'completed');
																		form.appendChild(hiddenInput);
																		//Append payment array
																		var hiddenInputarr = document.createElement('input');
																		hiddenInputarr.setAttribute('type', 'hidden');
																		hiddenInputarr.setAttribute('name', 'stripe_payment_id');
																		hiddenInputarr.setAttribute('value', result.paymentIntent.id);
																		form.appendChild(hiddenInputarr);
																		form.submit();
																	}
																});
															});
														</script>
														<form id="pto-stripe-form" method="POST" action="<?php echo esc_url( $return ); ?>">
															<input type="hidden" value="stripe" name="payment_method" />
															<?php wp_nonce_field( 'stripe_hidden_nonce', 'stripe_hidden_nonce_field' ); ?>
														</form>
													</li>
												<?php } ?>
												<?php if ( ! empty( $ideal) && ! empty( $stripe ) ) { ?>
													<li>
														<br />
														<img style="max-width:60px" src="<?php echo esc_url( PTO_PLUGIN_URL ) . '/img/ideal_logo.png'; ?>" id="ideal_trigger" />													
													</li>
												<?php } ?>
												<?php if ( ! empty( $twocheck ) ) { ?>
													<li>
														<?php
														$key = 'payment_amount_' . $post->ID;
														$name = __( 'Invoice', 'projectopia-core' ) . ' #' . $invoice_id;
														$price = $amount_to_pay;
														do_action( 'pto_twocheck_button', $post->ID, $name, $price, $return, $client_email );
														?>
													</li>
												<?php } ?>
											</ul>
										</div>
									</div>
								<?php } 
							} ?>
							<div class="clear"></div>
						</div>
						<?php $escrow_transaction = get_post_meta($post->ID, 'escrow_transaction', true);
						if ( ! empty($escrow_transaction->parties[0]->next_step) ) {
							$next_step = $escrow_transaction->parties[0]->next_step;
						}
						if ( ! empty($next_step) ) { ?>
							<a class="cqpim_button cqpim_button_link font-white bg-violet block mt-10 rounded_2 block" href="<?php echo esc_attr( $next_step ); ?>"><?php esc_html_e('Accept Escrow Transaction', 'projectopia-core') ; ?></a>
						<?php } ?>
						<a class="cqpim_button cqpim_button_link font-white bg-violet block mt-10 rounded_2 block" href="<?php echo esc_url( get_the_permalink() ); ?>?pto-page=print" target="_blank"><?php esc_html_e('View Printable Invoice', 'projectopia-core') ; ?></a>
						<a class="cqpim_button cqpim_button_link font-white bg-violet block mt-10 rounded_2 block" href="<?php echo esc_url( get_the_permalink() ); ?>?download_pdf=1"><?php esc_html_e('Download PDF Invoice', 'projectopia-core') ; ?></a>
					</div>
				</div>		
			</div>
			<?php if ( ! empty($ideal) ) { 
				$user = wp_get_current_user(); 
				$key = 'payment_amount_' . $post->ID;
				$amount_to_pay = pto_get_transient( $key ); ?>
				<div id="cqpim_payment_ideal_container" style="display:none">
					<div id="cqpim_payment_ideal" style="padding:10px">			
						<div id="ideal_form">
							<?php 
							$return = get_option('cqpim_client_page');
							$return = get_the_permalink($return);                           
							?>
							<br />
							<form id="payment-form">
								<div class="form-row">
									<label for="name">
										<?php esc_html_e('Name', 'projectopia-core'); ?>
									</label>
									<input type="text" name="ideal_name" value="<?php echo esc_attr( $user->display_name ); ?>">
								</div>
								<br />
								<div class="form-row">
									<label for="ideal-bank-element">
										<?php esc_html_e('iDEAL Bank', 'projectopia-core'); ?>
									</label>
									<br />
									<div id="ideal-bank-element"></div>
								</div>
								<br />
								<input type="hidden" name="ideal_amount" value="<?php echo esc_html( $amount_to_pay * 100 ); ?>" />
								<input type="hidden" name="ideal_return" value="<?php echo esc_url( $return ); ?>" />
								<input type="hidden" name="ideal_descriptor" value="<?php esc_html_e('Invoice', 'projectopia-core'); ?> #<?php echo esc_html( $invoice_id ); ?>" />
								<div class="btn-container">
									<button class="cqpim_button bg-violet font-white rounded_2 op"><?php esc_html_e('Submit Payment', 'projectopia-core'); ?></button>
									<button class="cqpim_button bg-violet font-white rounded_2 op go-back"><?php esc_html_e('Back', 'projectopia-core'); ?></button>
								</div>
								<div id="ideal-error-message" role="alert"></div>
							</form>
						</div>				
					</div>
				</div>
			<?php } ?>
			<?php } else { ?>
				<br />
				<div class="cqpim-dash-item-full grid-item">
					<div class="cqpim_block">
						<div class="cqpim_block_title">
							<div class="caption">
								<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Access Denied', 'projectopia-core'); ?></span>
							</div>
						</div>
						<p><?php esc_html_e('Cheatin\' uh? We can\'t let you see this item because it\'s not yours', 'projectopia-core'); ?></p>
					</div>
				</div>
			<?php } ?>	
		</div>
	</div>
</div>
<?php
include('footer_inc.php'); ?>