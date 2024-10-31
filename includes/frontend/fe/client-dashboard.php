<?php include('header.php');
$payment_status = isset($_POST['payment_status']) ? strtolower(sanitize_text_field(wp_unslash($_POST['payment_status']))) : '';
$tx = isset($_GET['tx']) ? strtolower(sanitize_text_field(wp_unslash($_GET['tx']))) : '';
$st = isset($_GET['st']) ? strtolower(sanitize_text_field(wp_unslash($_GET['st']))) : '';
if ( $payment_status == 'completed' || ! empty($tx) && ! empty($st) && $st == 'completed' ) {
	$last = pto_get_transient('last_invoice');
	$key = 'payment_amount_' . $last;
	pto_mark_invoice_paid($last, 'PayPal',pto_get_transient($key));
	$payment = true;
}
$twocheck = isset($_GET['credit_card_processed']) ? sanitize_text_field(wp_unslash($_GET['credit_card_processed'])) : '';
if ( $twocheck == 'Y' ) {
	$last = pto_get_transient('last_invoice');
	$key = 'payment_amount_' . $last;
	pto_mark_invoice_paid($last, '2Checkout', pto_get_transient($key));
	$payment = true;
}
$stripe_payment_status = isset($_POST['payment_status_stripe']) ? strtolower(sanitize_text_field(wp_unslash($_POST['payment_status_stripe']))) : '';
$payment_method = isset($_POST['payment_method']) ? sanitize_text_field(wp_unslash($_POST['payment_method'])) : '';
$stripe_payment_id = isset($_POST['stripe_payment_id']) ? sanitize_text_field(wp_unslash($_POST['stripe_payment_id'])) : '';
if ( isset( $_POST['stripe_hidden_nonce_field'] ) && wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['stripe_hidden_nonce_field'])), 'stripe_hidden_nonce') && $stripe_payment_status == 'completed' && $payment_method == 'stripe' && $stripe_payment_id != '' && strpos($stripe_payment_id, 'pi_') !== false ) {        
	$last = pto_get_transient('last_invoice');
	$spost_id = $last;  
	$key = 'payment_amount_' . $last;
    pto_mark_invoice_paid($last, 'Stripe', pto_get_transient($key));            
    $payment = true;    
}
$stripe_source = isset($_GET['source']) ? sanitize_text_field(wp_unslash($_GET['source'])) : '';
if ( ! empty($stripe_source) ) {        
	$last = pto_get_transient('last_invoice');
	$spost_id = $last;
	$spost_obj = get_post($spost_id);
	require_once(PTO_FE_PATH . '/stripe-sca/init.php');
	\Stripe\Stripe::setApiKey(get_option('client_invoice_stripe_secret'));
	$source = $stripe_source;
	$key = 'payment_amount_' . $last;   
	$amt = pto_get_transient($key);
	try {
		$charge = \Stripe\Charge::create(array(
			"amount"      => $amt * 100,
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
		pto_mark_invoice_paid($last, 'iDEAL', $amt);            
		$payment = true;
	}
}
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard', 'projectopia-core'),
);
update_post_meta($assigned, 'client_logs', $client_logs); 
$stickets = get_option('disable_tickets');

if ( empty($_GET['pto-page']) ) {
	include('client-dashboard/alerts.php');
	if ( get_option('enable_quotes') == 1 ) {
		include('client-dashboard/quotes.php');
	}
	include('client-dashboard/projects.php');
	if ( empty($stickets) ) {
		include('client-dashboard/tickets.php');
	}
	if ( get_option('disable_invoices') != 1 ) {
		include('client-dashboard/invoices.php');
	}
}
if ( ! empty($messaging) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'messages' ) {
	include('client-dashboard/messages.php');   
}
if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'subscriptions' ) {
	include('client-dashboard/subs-page.php');
} if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'subscription-plans' ) {
	include('client-dashboard/subs-plan-page.php');
}
if ( ! empty($quote_form) && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'quote_form' ) {
	include('client-dashboard/form.php');   
}
if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'faq' ) {
	include('client-dashboard/faq-page.php');
}
if ( isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'add-support-ticket' && empty($stickets) ) { 
	include('client-dashboard/add-ticket.php'); 
}
if ( get_option('allow_client_settings') && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'settings' ) { 
	include('client-dashboard/settings.php');   
}
if ( get_option('allow_client_settings') && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'client-files' ) { 
	include('client-dashboard/client-files.php');   
}
if ( get_option('allow_client_users') && isset( $_GET['pto-page'] ) && sanitize_text_field(wp_unslash($_GET['pto-page'])) == 'contacts' ) { 
	include('client-dashboard/contacts.php');   
}
include('footer.php'); 
