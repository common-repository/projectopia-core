<?php
$dash_page = get_option('cqpim_client_page');
$dash_url = get_the_permalink($dash_page);
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
$currency = get_option('currency_symbol');
$vat = get_option('sales_tax_rate');
$invoice_terms = get_option('company_invoice_terms');
$tax_name = get_option('sales_tax_name');
if ( $vat ) {
	$vat_string = '+' . $tax_name;
} else {
	$vat_string = '';
}
$project_details = get_post_meta($post->ID, 'project_details', true);
$project_elements = get_post_meta($post->ID, 'project_elements', true);
$project_progress = get_post_meta($post->ID, 'project_progress', true);
$p_type = isset($project_details['quote_type']) ? $project_details['quote_type'] : '';
$upper_type = ucfirst($p_type);
$quote_id = isset($project_details['quote_id']) ? $project_details['quote_id'] : '';
$quote_details = get_post_meta($quote_id, 'quote_details', true);
$project_summary = isset($quote_details['quote_summary']) ? $quote_details['quote_summary'] : '';
$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
$client_details = get_post_meta($client_id, 'client_details', true);
$project_client_ids = get_post_meta($client_id, 'client_ids', true);
if ( empty($project_client_ids) ) {
	$project_client_ids = array();
}
$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
$pm_name = isset($project_details['pm_name']) ? $project_details['pm_name'] : '';
$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
$deposit_invoice_id = isset($project_details['deposit_invoice_id']) ? $project_details['deposit_invoice_id'] : '';
$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
$completion_invoice_id = isset($project_details['completion_invoice_id']) ? $project_details['completion_invoice_id'] : '';
$deposit_invoice_details = get_post_meta($deposit_invoice_id, 'invoice_details', true);
$completion_invoice_details = get_post_meta($completion_invoice_id, 'invoice_details', true);
$deposit_sent = isset($deposit_invoice_details['sent']) ? $deposit_invoice_details['sent'] : '';
$deposit_paid = isset($deposit_invoice_details['paid']) ? $deposit_invoice_details['paid'] : '';
$completion_sent = isset($completion_invoice_details['sent']) ? $completion_invoice_details['sent'] : '';
$completion_paid = isset($completion_invoice_details['paid']) ? $completion_invoice_details['paid'] : '';
$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
if ( $signoff ) {
	$p_status = __('Signed Off / Completed', 'projectopia-core');
} else {
	if ( $confirmed ) {
		$p_status = __('Contract Signed', 'projectopia-core');
	} else {
		if ( $sent ) {
			$p_status = __('Contract Sent', 'projectopia-core');
		} else {
			$p_status = __('Contract Not Sent', 'projectopia-core');
		}
	}
}
if ( ! is_numeric($finish_date) ) {
	$str_finish_date = str_replace('/','-', $finish_date);
	$unix_finish_date = strtotime($str_finish_date);
} else {
	$unix_finish_date = $finish_date;
}
$current_date = time();
if ( $finish_date < $current_date ) {
	$days_to_due = 0;
} else {
	$days_to_due = round(abs($current_date - $unix_finish_date) / 86400);
}       
$task_count = 0;
$task_total_count = 0;
$task_complete_count = 0;
if ( empty($project_elements) ) {
	$project_elements = array();
}
foreach ( $project_elements as $element ) {
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key'       => 'milestone_id',
		'meta_value'     => $element['id'],
		'orderby'        => 'date',
		'order'          => 'ASC',
	);
	$tasks = get_posts($args);  
	foreach ( $tasks as $task ) {
		$task_total_count++;
		$task_details = get_post_meta($task->ID, 'task_details', true);
		if ( $task_details['status'] != 'complete' ) {
			$task_count++;
		}
		if ( $task_details['status'] == 'complete' ) {
			$task_complete_count++;
		}
	}
}
if ( $task_total_count != 0 ) {
	$pc_per_task = 100 / $task_total_count;
	$pc_complete = $pc_per_task * $task_complete_count;
} else {
	$pc_complete = 0;
}
if ( $client_user_id == $user_id OR in_array($user->ID, $project_client_ids) ) {
	if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'info' ) {
		include( 'project-info-sub.php' );
	}
	if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'updates' ) {
		include( 'project-updates-sub.php' );
	}
	$active = get_post_meta($post->ID, 'bugs_activated', true);
	include_once(ABSPATH.'wp-admin/includes/plugin.php');
	if ( pto_has_addon_active_license( 'pto_bugs', 'bugs' ) && $active == true ) {
		if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'bugs' ) {
			include( 'project-bugs-sub.php' );
		}
		if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'addbug' ) {
			include( 'project-addbug-sub.php' );
		}
	}
	if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'messages' ) {
		include( 'project-messages-sub.php' );
	}
	if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'files' ) {
		include( 'project-files-sub.php' );
	}
	if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'milestones' ) {
		include( 'project-milestones-sub.php' );
	}
	if ( isset( $_GET['sub'] ) && sanitize_text_field(wp_unslash($_GET['sub'])) == 'invoices' ) {
		include( 'project-invoices-sub.php' );
	}
	do_action( 'pto_project_summary_content', $post->ID, $assigned );
} else { ?>
	<br />
	<div class="cqpim-dash-item-full grid-item">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Access Denied', 'projectopia-core'); ?></span>
				</div>
			</div>
			<p><?php esc_html_e('Cheatin\' uh? We can\'t let you see this item because it\'s not yours', 'projectopia-core'); ?></p>
		</div>
	</div>
<?php } ?>		
