<?php include('header.php'); 
$payment_status = isset($_POST['payment_status']) ? strtolower(sanitize_text_field(wp_unslash($_POST['payment_status']))) : '';
$tx = isset($_GET['tx']) ? strtolower(sanitize_text_field(wp_unslash($_GET['tx']))) : '';
$st = isset($_GET['st']) ? strtolower(sanitize_text_field(wp_unslash($_GET['st']))) : '';
if ( $payment_status == 'completed' || ! empty($tx) && ! empty($st) && $st == 'completed' ) {
	$last = pto_get_transient('last_invoice');
	$key = 'payment_amount_' . $last;
	pto_mark_invoice_paid($last, 'PayPal', pto_get_transient($key));
	
	$payment = true;
}
$twocheck = isset($_GET['credit_card_processed']) ? sanitize_text_field(wp_unslash($_GET['credit_card_processed'])) : '';
if ( $twocheck == 'Y' ) {
	$last = pto_get_transient('last_invoice');
	$key = 'payment_amount_' . $last;
	if ( isset( $_GET['total'] ) ) {
		//pto_mark_invoice_paid($last, '2Checkout', pto_get_transient($key));
		pto_mark_invoice_paid($last, '2Checkout', sanitize_text_field(wp_unslash($_GET['total']))); //patch
		$payment = true;
	}
}
$stripe_payment_status = isset($_POST['payment_status_stripe']) ? strtolower(sanitize_text_field(wp_unslash($_POST['payment_status_stripe']))) : '';
$payment_method = isset($_POST['payment_method']) ? sanitize_text_field(wp_unslash($_POST['payment_method'])) : '';
$stripe_payment_id = isset($_POST['stripe_payment_id']) ? sanitize_text_field(wp_unslash($_POST['stripe_payment_id'])) : '';
if ( isset( $_POST['stripe_hidden_nonce_field'] ) && wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['stripe_hidden_nonce_field']) ), 'stripe_hidden_nonce') && $stripe_payment_status == 'completed' && $payment_method == 'stripe' && $stripe_payment_id != '' && strpos($stripe_payment_id, 'pi_') !== false ) {        
	$last = pto_get_transient('last_invoice');
	$key = 'payment_amount_' . $last;
	$spost_id = $last;  
    pto_mark_invoice_paid($last, 'Stripe', pto_get_transient($key));            
        $payment = true;    
}
$stripe_source = isset($_GET['source']) ? sanitize_text_field(wp_unslash($_GET['source'])) : '';
if ( ! empty($stripe_source) ) {        
	$last = pto_get_transient('last_invoice');
	$key = 'payment_amount_' . $last;
	$spost_id = $last;
	$spost_obj = get_post($spost_id);
	require_once(PTO_FE_PATH . '/stripe-sca/init.php');
	\Stripe\Stripe::setApiKey(get_option('client_invoice_stripe_secret'));
	$source = $stripe_source;
	try {
		$charge = \Stripe\Charge::create(array(
			"amount"      => pto_get_transient($key) * 100,
			"currency"    => "eur",
			"source"      => $source,
			"description" => __('Invoice', 'projectopia-core') . ' - ' . $spost_obj->post_title,
		)
		);
	} catch ( \Stripe\Error\InvalidRequest $e ) {
		$stripe_response = json_decode($e->httpBody);
		$stripe_error = $stripe_response->error;
	}   
	if ( ! empty($stripe_error->code) ) {
		$payment_error = true;
		$error_message = $stripe_error->message;
	} else {
		pto_mark_invoice_paid($last, 'iDEAL', pto_get_transient($key));         
		$payment = true;
	}
} ?>
<div id="cqpim-dash-sidebar-back"></div>
<div class="cqpim-dash-content">
	<div id="cqpim-dash-sidebar">
		<?php include('sidebar.php'); ?>
	</div>
	<div id="cqpim-dash-content">
		<div id="cqpim_admin_title">
			<?php
				if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quotes' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Quotes / Estimates', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'projects' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Projects', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'faq' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('FAQ', 'projectopia-core');
				} elseif ( pto_has_addon_active_license( 'pto_st', 'tickets' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'support' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Support Tickets', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'invoices' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Invoices', 'projectopia-core');
				} elseif ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'subscriptions' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html( pto_return_subs_text('subs') );
				} elseif ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'subscription-plans' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html( pto_return_subs_text('plans') );
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quote_form' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Request a Quote', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'settings' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Settings', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'messages' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Messages', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contacts' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Contacts', 'projectopia-core');
				} elseif ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'client-files' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Client Files', 'projectopia-core');
				}  elseif ( pto_has_addon_active_license( 'pto_st', 'tickets') && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'add-support-ticket' ) {
					echo '<a href="' . esc_url( get_the_permalink($client_dash) ) . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <i class="fa fa-circle"></i> ' . esc_html__('Add Support Ticket', 'projectopia-core');
				} elseif ( empty($_GET['pto-page']) ) {
					esc_html_e('Dashboard', 'projectopia-core');
				}
				do_action( 'pto_cd_breadcrumbs', $client_dash );
			?>										
		</div>
		<div id="cqpim-cdash-inside">
			<?php if ( ! empty($payment) && $payment == true ) { ?>
				<div class="cqpim-alert cqpim-alert-success alert-display">
				  	<strong><?php esc_html_e('Payment Successful.', 'projectopia-core'); ?></strong> <?php esc_html_e('Your payment has been accepted, thank you.', 'projectopia-core'); ?>
				</div>			
			<?php } ?>
			<?php if ( ! empty($payment_error) && $payment_error == true ) { ?>
				<div class="cqpim-alert cqpim-alert-danger alert-display">
				  	<strong><?php esc_html_e('Payment Declined.', 'projectopia-core'); ?></strong> <?php echo esc_html( $error_message ); ?>
				</div>			
			<?php } ?>
			<?php
			if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quotes' && get_option('enable_quotes') == 1 ) {
				include('client-dashboard/quotes-page.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'projects' ) {
				include('client-dashboard/projects-page.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'faq' ) {
				include('client-dashboard/faq-page.php');
			} if ( pto_has_addon_active_license( 'pto_st', 'tickets' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'support' ) {
				include('client-dashboard/ticket-page.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'invoices' ) {
				include('client-dashboard/invoice-page.php');
			} if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'subscriptions' ) {
				include('client-dashboard/subs-page.php');
			} if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'subscription-plans' ) {
				include('client-dashboard/subs-plan-page.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quote_form' && ! empty($quote_form) ) {
				include('client-dashboard/quote-form.php');
			} if ( pto_has_addon_active_license( 'pto_st', 'tickets' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'add-support-ticket' ) { 
				include('client-dashboard/add-ticket.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'settings' ) {
				include('client-dashboard/client-settings.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'client-files' ) {
				include('client-dashboard/client-files.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contacts' ) {
				include('client-dashboard/client-contacts.php');
			} if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'messages' ) {
				include('client-dashboard/messages.php');
			} if ( empty($_GET['pto-page']) ) {
				include('client-dashboard/dashboard.php');
			}

			$page_slug = ! empty( $_GET['pto-page'] ) ? sanitize_text_field(wp_unslash($_GET['pto-page'])) : '';
			$page_id = ! empty( $_GET['id'] ) ? sanitize_text_field(wp_unslash($_GET['id'])) : 0;

			/**
			 * Fires when client dashboard prepare the page.
			 * @param int    $page_id   Page Id.
			 * @param string $page_slug Page slug.
			 */
			do_action( 'pto_client_dashboard_page', $page_slug, $page_id );

			?>
		</div>			
	</div>
	<div class="clear"></div>
</div>
<?php 
include('footer_inc.php'); ?>
