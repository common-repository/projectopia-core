<?php
/**
 * Initial Settings Wizard.
 */

/**
* Install PTO and setup initial wizard.
*/
add_action( 'admin_init', 'pto_install', 99999 );
function pto_install() {
	if ( ! projectopia_fs()->is_activation_mode() ) {
		$wizard_status = get_option( 'pto_run_setup_wizard' );
		if ( 1 == $wizard_status ) {
			global $pagenow;
			if ( 'plugins.php' == $pagenow ) {
				$url = admin_url() . 'admin.php?page=pto-settings&sub-page=pto-welcome';
				wp_safe_redirect( $url );
				exit();
			}
		} elseif ( 2 == $wizard_status ) { 
			if ( ! empty( $_GET['page'] ) && 'pto-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) && ! empty( $_GET['sub-page'] ) ) {
				$url = admin_url() . 'admin.php?page=pto-dashboard';
				wp_safe_redirect( esc_url( $url ) );
				exit();
			}
		}
	}
}

add_action( 'admin_notices', 'initial_register_pto_settings_page', 5 ); 
function initial_register_pto_settings_page() {
	if ( ! empty( $_GET['page'] ) && 'pto-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) && ! empty( $_GET['sub-page'] ) ) {
		$wizard_status = get_option( 'pto_run_setup_wizard' );
		$subpage = sanitize_text_field( wp_unslash( $_GET['sub-page'] ) );
		if ( 1 == $wizard_status ) {
			if ( 'pto-initial-settings' === $subpage ) {
				pto_open_initial_settings_wizard();
			}

			if ( 'pto-welcome' === $subpage ) {
				pto_open_initial_settings_popup();
			}
		}
	}
}

function pto_open_initial_settings_wizard() {
	update_option( 'pto_run_setup_wizard', 2 );
    ?>
    <div class="pto-modal-wrapper">
		<form method="post" id="initial-settings-form" class="initial-settings-popup">
			<figure class="pto-logo-container mt-3">
				<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/img/pto-logo.png' ) ?>" alt="projectopia" class="pto-logo"/>
			</figure>
			<div id="wizard">
				<!-- SECTION 1 -->
			   	<h4></h4>
			   	<section>
					<div class="form-group">
						<div class="input-group">
							<label for="company_name"><?php esc_html_e('Company Name', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input" name="company_name" value="<?php echo esc_attr( get_option( 'company_name' ) ); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="company_address"><?php esc_html_e('Company Address', 'projectopia-core'); ?></label>
							<textarea name="company_address" class="form-control input pto-textarea" style="min-height: 80px"><?php echo esc_attr( get_option( 'company_address' ) ); ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="company_postcode"><?php esc_html_e('Company Postcode', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input" name="company_postcode" value="<?php echo esc_attr( get_option( 'company_postcode' ) ); ?>" />
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="company_telephone"><?php esc_html_e('Company Telephone', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input"  name="company_telephone" value="<?php echo esc_attr( get_option( 'company_telephone' ) ); ?>" />
						</div>
					</div>
			   	</section>
			   	<!-- SECTION 2 -->
			   	<h4></h4>
			   	<section>
			   		<div class="form-group">
						<div class="input-group">
							<label for="company_sales_email"><?php esc_html_e('Sales Email', 'projectopia-core'); ?> <span class="required-star">*</span></label>
							<input type="email" class="form-control input required" name="company_sales_email" value="<?php echo esc_attr( get_option( 'company_sales_email' ) ); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="company_accounts_email"><?php esc_html_e('Accounts Email', 'projectopia-core'); ?> <span class="required-star">*</span></label>
							<input type="email" class="form-control input required" name="company_accounts_email" value="<?php echo esc_attr( get_option( 'company_accounts_email' ) ); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="company_support_email"><?php esc_html_e('Support Email (For Support Tickets)', 'projectopia-core'); ?> <span class="required-star">*</span> </label>
							<input type="email" class="form-control input required" name="company_support_email" value="<?php echo esc_attr( get_option( 'company_support_email' ) ); ?>"/>
						</div>
					</div>
			   	</section>
			   	<!-- SECTION 3 -->
			   	<h4></h4>
			   	<section>
			   		<div class="form-group">
						<div class="input-group">
							<label for="currency_symbol"><?php esc_html_e('Currency Symbol', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input" name="currency_symbol" value="<?php echo esc_attr( get_option( 'currency_symbol' ) ); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="currency_symbol_position"><?php esc_html_e('Currency Symbol Position', 'projectopia-core'); ?></label>
							<?php
								$symbol_position = get_option( 'currency_symbol_position' );
								$selectedl = '';
								$selectedr = '';
								if ( 'l' == $symbol_position ) {
									$selectedl = 'selected="selected"';
								} elseif ( 'r' == $symbol_position ) {
									$selectedr = 'selected="selected"';
								}
							?>
							<select name="currency_symbol_position" class="form-control input">
								<option value="l" <?php echo esc_attr( $selectedl ); ?>> <?php esc_html_e('Before Amount', 'projectopia-core'); ?></option>
								<option value="r" <?php echo esc_attr( $selectedr ); ?>> <?php esc_html_e('After Amount', 'projectopia-core'); ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="currency_code"><?php esc_html_e('Currency Code (Used for Payment Gateways)', 'projectopia-core'); ?><i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="" style="margin-left: 8px;" data-original-title="<?php esc_attr_e('Some of the available currencies are not supported by PayPal.', 'projectopia-core'); ?>"></i></label>
							<select name="currency_code" class="form-control input">
								<option value="0"><?php esc_html_e('Choose a currency', 'projectopia-core'); ?></option>
								<?php
								$stored_code = get_option( 'currency_code' );
								if ( function_exists( 'pto_return_currency_select' ) ) {
									$codes = pto_return_currency_select();
								}
								if ( ! empty( $codes ) ) {
									foreach ( $codes as $key => $code ) {
										echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $stored_code, false ) . '>' . esc_html( $code ) . '</option>';
									}
								}
								?>
							</select>
						</div>
					</div>
			   	</section>
			   	<!-- SECTION 4 -->
			   	<h4></h4>
			   	<section>
					<p class="sub-header"><?php esc_html_e('These settings apply to sales tax, such as VAT. If you do not charge sales tax, then leave these fields blank.', 'projectopia-core'); ?></p>
					<div class="form-group">
						<div class="input-group">
							<label for="sales_tax_rate"><?php esc_html_e('Tax Percentage (eg. 20)', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input" name="sales_tax_rate" value="<?php echo esc_attr( get_option( 'sales_tax_rate' ) ); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="sales_tax_name"><?php esc_html_e('Tax Name (eg. VAT)', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input" name="sales_tax_name" value="<?php echo esc_attr( get_option( 'sales_tax_name' ) ); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<label for="sales_tax_reg"><?php esc_html_e('Tax Reg Number', 'projectopia-core'); ?></label>
							<input type="text" class="form-control input" name="sales_tax_reg" value="<?php echo esc_attr( get_option( 'sales_tax_reg' ) ); ?>"/>
						</div>
					</div>
			   	</section>
			   	<!-- SECTION 5 -->
			   	<h4></h4>
			   	<section>
					<div class="switch-field">
						<input type="radio" id="pto-stripe" name="pto-payment" value="yes" checked/>
						<label for="pto-stripe">Stripe</label>
						<input type="radio" id="pto-paypal" name="pto-payment" value="no" />
						<label for="pto-paypal">Paypal</label>
					</div>
					<div class="pto-payment-gateway-stripe">
						<p class="sub-header"><?php esc_html_e('To allow clients to pay invoices and set up subscriptions via Stripe, enter your Stripe Publishable Key below.', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<label for="client_invoice_stripe_key">Stripe Public Key</label>
								<input type="text" class="form-control input" name="client_invoice_stripe_key" value="<?php echo esc_attr( get_option( 'client_invoice_stripe_key' ) ); ?>"/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<label for="client_invoice_stripe_secret">Stripe Secret Key</label>
								<input type="text" class="form-control input" name="client_invoice_stripe_secret" value="<?php echo esc_attr( get_option( 'client_invoice_stripe_secret' ) ); ?>"/>
							</div>
						</div>
						<div>
							<label for="company_support_email">
								<input type="checkbox" value="1" name="client_invoice_stripe_ideal" <?php checked(1, get_option('client_invoice_stripe_ideal')); ?> />
								<?php esc_html_e('If you have iDEAL activated on your Stripe account, check this box to enable it as a payment gateway.', 'projectopia-core'); ?>
								<span class="checkmark"></span>
							</label>
						</div>
					</div>
					<div class="pto-payment-gateway-paypal">
						<p class="sub-header"><?php esc_html_e('To allow clients to pay invoices via Paypal, enter your Paypal email address below.', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<label for="client_invoice_paypal_address">Paypal Email Address </label>
								<input type="text" class="form-control input" name="client_invoice_paypal_address" value="<?php echo esc_attr( get_option( 'client_invoice_paypal_address' ) ); ?>"/>
							</div>
						</div>
						<p class="sub-header"><?php esc_html_e('To allow clients to set up subscriptions via Paypal, enter your Paypal API credentials below.', 'projectopia-core'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<label for="cqpim_paypal_api_username">Paypal API Username</label>
								<input type="text" class="form-control input"  name="cqpim_paypal_api_username" value="<?php echo esc_attr( get_option( 'cqpim_paypal_api_username' ) ); ?>"/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<label for="cqpim_paypal_api_password">Paypal API Password</label>
								<input type="text" class="form-control input" name="cqpim_paypal_api_password" value="<?php echo esc_attr( get_option( 'cqpim_paypal_api_password' ) ); ?>"/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<label for="cqpim_paypal_api_signature">Paypal API Signature</label>
								<input type="text" class="form-control input" name="cqpim_paypal_api_signature" value="<?php echo esc_attr( get_option( 'cqpim_paypal_api_signature' ) ); ?>"/>
							</div>
						</div>
					</div>
			   	</section>
			   	<p class="sub-header d-none">A link to our documentation <a  href="https://projectopia.io/docs41/" target="_blank">Click here</a></p>
			   	<p class="sub-header" style="text-align:center;"><a href="<?php echo esc_url( admin_url() ); ?>">Return to WordPress Dashboard</a></p>
			</div>
		</form>
	</div>
	<?php
}

add_action( "wp_ajax_pto_save_initial_settings_options", "pto_save_initial_settings_options");
/**
 * Function to update the initial setting options in installation process.
 */
function pto_save_initial_settings_options() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	// Company details.
	$company_name = filter_input( INPUT_POST, "company_name", FILTER_SANITIZE_STRING );
	if ( ! empty( $company_name ) ) {
		update_option( 'company_name', $company_name );
	}

	$company_address = filter_input( INPUT_POST, "company_address", FILTER_SANITIZE_STRING );
	if ( ! empty( $company_address ) ) {
		update_option( 'company_address', $company_address );
	}

	$company_postcode = filter_input( INPUT_POST, "company_postcode", FILTER_SANITIZE_STRING );
	if ( ! empty( $company_postcode ) ) {
		update_option( 'company_postcode', $company_postcode );
	}

	$company_telephone = filter_input( INPUT_POST, "company_telephone", FILTER_SANITIZE_NUMBER_INT );
	if ( ! empty( $company_telephone ) ) {
		update_option( 'company_telephone', $company_telephone );
	}

	// Company emails.
	$company_sales_email = filter_input( INPUT_POST, "company_sales_email", FILTER_SANITIZE_EMAIL );
	if ( ! empty( $company_sales_email ) ) {
		update_option( 'company_sales_email', $company_sales_email );
	}

	$company_accounts_email = filter_input( INPUT_POST, "company_accounts_email", FILTER_SANITIZE_EMAIL );
	if ( ! empty( $company_accounts_email ) ) {
		update_option( 'company_accounts_email', $company_accounts_email );
	}

	$company_support_email  = filter_input( INPUT_POST, "company_support_email", FILTER_SANITIZE_EMAIL );
	if ( ! empty( $company_support_email ) ) {
		update_option( 'company_support_email', $company_support_email );
	}

	// Finance details
	$currency_symbol = filter_input( INPUT_POST, "currency_symbol", FILTER_SANITIZE_STRING );
	if ( ! empty( $currency_symbol ) ) {
		update_option( 'currency_symbol', $currency_symbol );
	}

	$currency_symbol_position = filter_input( INPUT_POST, "currency_symbol_position", FILTER_SANITIZE_STRING );
	if ( ! empty( $currency_symbol_position ) ) {
		update_option( 'currency_symbol_position', $currency_symbol_position );
	}

	$currency_code = filter_input( INPUT_POST, "currency_code", FILTER_SANITIZE_STRING );
	if ( ! empty( $currency_code ) ) {
		update_option( 'currency_code', $currency_code );
	}

	//Sales tax details
	$sales_tax_rate = filter_input( INPUT_POST, "sales_tax_rate", FILTER_SANITIZE_NUMBER_INT );
	if ( ! empty( $sales_tax_rate ) ) {
		update_option( 'sales_tax_rate', $sales_tax_rate );
	}

	$sales_tax_name = filter_input( INPUT_POST, "sales_tax_name", FILTER_SANITIZE_STRING );
	if ( ! empty( $sales_tax_name ) ) {
		update_option( 'sales_tax_name', $sales_tax_name );
	}

	$sales_tax_reg  = filter_input( INPUT_POST, "sales_tax_reg", FILTER_SANITIZE_STRING );
	if ( ! empty( $sales_tax_reg ) ) {
		update_option( 'sales_tax_reg', $sales_tax_reg );
	}

	//Payment gateway details
	// Stripe details
	$client_invoice_stripe_key = filter_input( INPUT_POST, "client_invoice_stripe_key", FILTER_SANITIZE_STRING );
	if ( ! empty( $client_invoice_stripe_key ) ) {
		update_option( 'client_invoice_stripe_key', $client_invoice_stripe_key );
	}

	$client_invoice_stripe_secret = filter_input( INPUT_POST, "client_invoice_stripe_secret", FILTER_SANITIZE_STRING );
	if ( ! empty( $client_invoice_stripe_secret ) ) {
		update_option( 'client_invoice_stripe_secret', $client_invoice_stripe_secret );
	}

	$client_invoice_stripe_ideal = filter_input( INPUT_POST, "client_invoice_stripe_ideal", FILTER_SANITIZE_NUMBER_INT );
	if ( isset( $client_invoice_stripe_ideal ) ) {
		update_option( 'client_invoice_stripe_ideal', $client_invoice_stripe_ideal );
	}

	//Paypal details.
	$client_invoice_paypal_address = filter_input( INPUT_POST, "client_invoice_paypal_address", FILTER_SANITIZE_EMAIL );
	if ( ! empty( $client_invoice_paypal_address ) ) {
		update_option( 'client_invoice_paypal_address', $client_invoice_paypal_address );
	}

	$cqpim_paypal_api_username = filter_input( INPUT_POST, "cqpim_paypal_api_username", FILTER_SANITIZE_STRING );
	if ( ! empty( $cqpim_paypal_api_username ) ) {
		update_option( 'cqpim_paypal_api_username', $cqpim_paypal_api_username );
	}

	$cqpim_paypal_api_password = filter_input( INPUT_POST, "cqpim_paypal_api_password", FILTER_SANITIZE_STRING );
	if ( ! empty( $cqpim_paypal_api_password ) ) {
		update_option( 'cqpim_paypal_api_password', $cqpim_paypal_api_password );
	}

	$cqpim_paypal_api_signature = filter_input( INPUT_POST, "cqpim_paypal_api_signature", FILTER_SANITIZE_STRING );
	if ( ! empty( $cqpim_paypal_api_signature ) ) {
		update_option( 'cqpim_paypal_api_signature', $cqpim_paypal_api_signature );
	}

	$cqpim_paypal_enable_sandbox = filter_input( INPUT_POST, "cqpim_paypal_enable_sandbox", FILTER_SANITIZE_STRING );
	if ( ! empty( $cqpim_paypal_enable_sandbox ) ) {
		update_option( 'cqpim_paypal_enable_sandbox', $cqpim_paypal_enable_sandbox );
	}

	pto_send_json( array(
		'error'    => false,
		'redirect' => admin_url() . 'admin.php?page=pto-dashboard',
 		'message'  => 'Settings updated successfully',
	) ); 
}

/**
 * Function to show welcome popup.
 */
function pto_open_initial_settings_popup() {
?>
	<div class="pto-modal-wrapper">
		<div class="initial-settings-popup initial-notification">
			<div id="pre-wizard">
				<p>Thank you for installing Projectopia. Do you want to run the wizard to configure the basic plugin settings ?</p>
				<a class="cqpim_button cqpim_small_button font-white bg-green op" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=pto-settings&sub-page=pto-initial-settings">Yes</a>
				<a id="close-intial-popup" class="cqpim_button cqpim_small_button bg-red font-white op" href="#">No</a>
			</div>
		</div>
	</div>		
<?php
}

add_action( "wp_ajax_pto_initial_setup_popup_cancel", "pto_initial_setup_popup_cancel");
function pto_initial_setup_popup_cancel() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	update_option( 'pto_run_setup_wizard', 2 );
	$url = admin_url() . 'admin.php?page=pto-dashboard';
	pto_send_json( array( 
		'error'    => false,
		'redirect' => $url,
	) ); 
}
