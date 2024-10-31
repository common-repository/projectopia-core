<?php
function pto_get_user_role( $user = null ) {
	$user = $user ? new WP_User( $user ) : wp_get_current_user();
	$roles = $user->roles;
	$role = isset($roles[0]) ? $roles[0] : __('No Role', 'projectopia-core');
	$role = str_replace('cqpim_', '', $role);
	$role = str_replace('_', ' ', $role);
	return ucwords($role);
}
function pto_convert_date( $date, $format = 0 ) {
	if ( empty($format) ) {
		$format = get_option('cqpim_date_format');
	}
	if ( ! empty($date) ) {
		$date = (array) DateTime::createFromFormat($format, $date);
		$date = strtotime($date['date']);
	}   
	return $date;
}
function pto_date_format_php_to_js( $sFormat ) {
	switch ( $sFormat ) {
		case 'F j, Y':
			return( 'MM dd, yy' );
			break;
		case 'Y/m/d':
			return( 'yy/mm/dd' );
			break;
		case 'm/d/Y':
			return( 'mm/dd/yy' );
			break;
		case 'd/m/Y':
			return( 'dd/mm/yy' );
			break;
		case 'Y-m-d':
			return( 'yy-mm-dd' );
			break;
		case 'd.m.Y':
			return( 'dd.mm.yy' );
			break;
	}
}
function pto_check_addon_status( $addon ) {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}   
	if ( $addon == 'reporting' ) {
		if ( is_plugin_active('pto-reporting/pto-reporting.php') || defined('PTO_REPORTING_DIRNAME') && is_plugin_active(PTO_REPORTING_DIRNAME . '/pto-reporting.php') ) {
			return true;
		}
	}
	if ( $addon == '2checkout' ) {
		if ( is_plugin_active('pto-2checkout/pto-2checkout.php') || defined('PTO_2CHECKOUT_DIRNAME') && is_plugin_active(PTO_2CHECKOUT_DIRNAME . '/pto-2checkout.php') ) {
			return true;
		}
	}
	if ( $addon == 'bugs' ) {
		if ( is_plugin_active('pto-bugs/pto-bugs.php') || defined('PTO_BUGS_DIRNAME') && is_plugin_active(PTO_BUGS_DIRNAME . '/pto-bugs.php') ) {
			return true;
		}
	}
	if ( $addon == 'expenses' ) {
		if ( is_plugin_active('pto-expenses/pto-expenses.php') || defined('PTO_EXPENSES_DIRNAME') && is_plugin_active(PTO_EXPENSES_DIRNAME . '/pto-expenses.php') ) {
			return true;
		}
	}
	if ( $addon == 'subscriptions' ) {
		if ( is_plugin_active('pto-subscriptions/pto-subscriptions.php') || defined('PTO_SUBSCRIPTIONS_DIRNAME') && is_plugin_active(PTO_SUBSCRIPTIONS_DIRNAME . '/pto-subscriptions.php') ) {
			return true;
		}
	}
	if ( $addon == 'woocommerce' ) {
		if ( is_plugin_active('pto-woocommerce/pto-woocommerce.php') || defined('PTO_WOOCOMMERCE_DIRNAME') && is_plugin_active(PTO_WOOCOMMERCE_DIRNAME . '/pto-woocommerce.php') ) {
			return true;
		}
	}
	if ( $addon == 'kanban' ) {
		if ( is_plugin_active('pto-kanban/pto-kanban.php') || defined('PTO_BOARD_DIRNAME') && is_plugin_active(PTO_BOARD_DIRNAME . '/pto-kanban.php') ) {
			return true;
		}
	}
	if ( $addon == 'roles' ) {
		if ( is_plugin_active('pto-roles/pto-roles.php') || defined('PTO_ROLES_PERMISSIONS_DIRNAME') && is_plugin_active(PTO_ROLES_PERMISSIONS_DIRNAME . '/pto-roles.php') ) {
			return true;
		}
	}
	if ( $addon == 'timeentries' ) {
		if ( is_plugin_active('pto-time-entries/pto-time-entries.php') || defined('PTO_TIME_ENTRIES_DIRNAME') && is_plugin_active(PTO_TIME_ENTRIES_DIRNAME . '/pto-time-entries.php') ) {
			return true;
		}
	}
	if ( $addon == 'customfields' ) {
		if ( is_plugin_active('pto-custom-fields/pto-custom-fields.php') || defined('PTO_CUSTOM_FIELDS_DIRNAME') && is_plugin_active(PTO_CUSTOM_FIELDS_DIRNAME . '/pto-custom-fields.php') ) {
			return true;
		}
	}
	if ( $addon == 'tickets' ) {
		if ( is_plugin_active('pto-tickets/pto-tickets.php') || defined('PTO_SUPPORT_TICKETS_DIRNAME') && is_plugin_active(PTO_SUPPORT_TICKETS_DIRNAME . '/pto-tickets.php') ) {
			return true;
		}
	}
	return false;
}
function pto_schedule_minute( $schedules ) {
	$schedules['every_minute'] = array(
		'interval' => 300,
		'display'  => __( 'Every Minute', 'projectopia-core' ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'pto_schedule_minute' ); // phpcs:ignore WordPress.WP.CronInterval.CronSchedulesInterval
function pto_random_string( $length = 10 ) {
	$key = '';
	$keys = array_merge( range(0,9), range('a', 'z') );
	for ( $i = 0; $i < $length; $i++ ) {
		$key .= $keys[ array_rand($keys) ];
	}
	return $key;
}
if ( function_exists( 'add_image_size' ) ) { 
	add_image_size( 'company-logo', 400, 100, true );
	add_image_size( 'cqpim-login-background', 2000, 1200, true );
}
add_filter( 'bulk_actions-edit-cqpim_teams', '__return_empty_array', 100 );
add_filter( 'post_row_actions', 'pto_remove_row_actions', 39, 2 );
function pto_remove_row_actions( $actions, $post ) {
	if ( strpos( $post->post_type, 'cqpim_' ) !== false ) {
		unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );

		$actions['trash'] = '<a href="#" data-redirect="' . esc_url( get_delete_post_link( get_the_ID(), '', true ) ) . '" class="submitdelete pto-delete-post">' . esc_html__( 'Delete', 'projectopia-core' ) . '</a>';
	}
	return $actions;
}
function pto_get_client_ip() {
	$ipaddress = '';
	if ( ! empty($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
	elseif ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
	elseif ( ! empty($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) );
	elseif ( ! empty($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) );
	elseif ( ! empty($_SERVER['HTTP_FORWARDED']))
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) );
	elseif ( ! empty($_SERVER['REMOTE_ADDR']))
		$ipaddress = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}
function no_index_pto_types() {
	if ( is_singular('cqpim_quote') || is_singular('cqpim_project') || is_singular('cqpim_invoice') || is_singular('cqpim_support') ) {
		echo '<meta name="robots" content="noindex, nofollow">';
	}
}
add_action('wp_head', 'no_index_pto_types');
add_action('post_edit_form_tag', 'pto_enctype_form_tag');
function pto_enctype_form_tag(){  
	printf(' enctype="multipart/form-data" encoding="multipart/form-data" ');
}
function pto_get_image_id_by_url( $url ) {
	global $wpdb;
	$img_id = 0;
		preg_match( '|' . get_site_url() . '|i', $url, $matches );
		if ( isset( $matches ) and 0 < count( $matches ) ) {
			$url = preg_replace( '/([^?]+).*/', '\1', $url ); 
			$guid = preg_replace( '/(.+)-\d+x\d+\.(\w+)/', '\1.\2', $url );
			$img_id = $wpdb->get_var( $wpdb->prepare( "SELECT `ID` FROM $wpdb->posts WHERE `guid` = %s", $guid ) );
		if ( $img_id ) {
			$img_id = intval( $img_id );
		}
	}
	return $img_id;
}
function pto_upload_dir( $dirs ) {
	$dirs['subdir'] = '/cqpim-uploads';
	$dirs['path'] = $dirs['basedir'] . '/pto-uploads';
	$dirs['url'] = $dirs['baseurl'] . '/pto-uploads';
	return $dirs;
}
function pto_upload_user_file( $file = array(), $project_id = 0 ) {

	//Check project id is empty.
	if ( empty( $project_id ) ) {
		return false;
	}

	require_once( ABSPATH . 'wp-admin/includes/admin.php' );
	add_filter( 'upload_dir', 'pto_upload_dir' );   
	$file_return = wp_handle_upload( $file, array( 'test_form' => false ) );
	if ( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
		return false;
	} else {
		$filename = $file_return['file'];
		$attachment = array(
			'post_mime_type' => $file_return['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_parent'    => $project_id,
			'post_status'    => 'inherit',
			'guid'           => $file_return['url'],
		);
		$attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );
		if ( 0 < intval( $attachment_id ) ) {
			return $attachment_id;
		}
	}
	remove_filter( 'upload_dir', 'pto_upload_dir' );    
	return false;
}
function pto_is_edit_page( $new_edit = null ) {
	global $pagenow;
	if ( ! is_admin()) return false;
	if ($new_edit == "edit")
		return in_array( $pagenow, array( 'post.php' ) );
	elseif ($new_edit == "new")
		return in_array( $pagenow, array( 'post-new.php' ) );
	else
		return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}
function pto_get_currency_symbol( $post_id ) {
	$currency = get_option('currency_symbol');
	$post_currency = get_post_meta($post_id, 'currency_symbol', true);
	if ( ! $post_currency ) {
		$post_currency = $currency;
	}
	return $post_currency;
}
function pto_calculate_currency( $post_id, $amount = '' ) {
	$currency = get_option('currency_symbol');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space'); 
	$post_currency = get_post_meta($post_id, 'currency_symbol', true);
	$post_currency_space = get_post_meta($post_id, 'currency_space', true);     
	$post_currency_position = get_post_meta($post_id, 'currency_position', true);
	if ( ! $post_currency ) {
		$post_currency = $currency;
	}
	if ( ! $post_currency_space ) {
		$post_currency_space = true;
	}
	if ( ! $post_currency_position ) {
		$post_currency_position = 'l';
	}
	if ( $amount == "0" || empty($amount) ) {
		if ( ! empty($post_currency_space) ) {
			$space = ' ';
		} else {
			$space = '';
		}
		if ( $post_currency_position == 'l' ) {
			return $post_currency . $space . "0";
		} else {
			return "0" . $space . $post_currency;
		}
	} else {
		$amount = str_replace(',', '', $amount);
		$decimal = get_option('currency_decimal');
		$comma = get_option('currency_comma');
		if ( ! empty($decimal) ) {
			if ( ! empty($comma) ) {
				$amount = number_format( (float)$amount, 0, '.', ',');
			} else {
				$amount = number_format( (float)$amount, 0, '.', '');
			}
		} else {
			if ( ! empty($comma) ) {
				$amount = number_format( (float)$amount, 2, '.', ',');
			} else {
				$amount = number_format( (float)$amount, 2, '.', '');
			}
		}
		if ( ! empty($post_currency_space) ) {
			$space = ' ';
		} else {
			$space = '';
		}
		if ( $post_currency_position == 'l' ) {
			return $post_currency . $space . $amount;
		} else {
			return $amount . $space . $post_currency;
		}
	}
}
function pto_format_currency( $amount = '' ) {
	$currency = get_option('currency_symbol');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space');
	
	if ( $amount == "0" || empty($amount) ) {
		if ( ! empty($currency_space) ) {
			$space = ' ';
		} else {
			$space = '';
		}
		if ( $currency_position == 'l' ) {
			return $currency . $space . "0";
		} else {
			return "0" . $space . $currency;
		}
	} else {
		$amount = str_replace(',', '', $amount);
		$decimal = get_option('currency_decimal');
		$comma = get_option('currency_comma');
		if ( ! empty($decimal) ) {
			if ( ! empty($comma) ) {
				$amount = number_format( (float)$amount, 0, '.', ',');
			} else {
				$amount = number_format( (float)$amount, 0, '.', '');
			}
		} else {
			if ( ! empty($comma) ) {
				$amount = number_format( (float)$amount, 2, '.', ',');
			} else {
				$amount = number_format( (float)$amount, 2, '.', '');
			}
		}
		if ( ! empty($currency_space) ) {
			$space = ' ';
		} else {
			$space = '';
		}
		if ( $currency_position == 'l' ) {
			return $currency . $space . $amount;
		} else {
			return $amount . $space . $currency;
		}
	}
}
function pto_send_emails( $to, $subject, $message, $headers = null, $attachments = array(), $type = 'other' ) {
	$headers = array();
	$subject = str_replace('\\', '', $subject);
	$message = str_replace('\\', '', $message);
	$sender_name = get_option('company_name');
	if ( ! empty($type) && $type == 'support' ) {
		$sender_email = get_option('company_support_email');
	}
	if ( ! empty($type) && $type == 'accounts' ) {
		$sender_email = get_option('company_accounts_email');
	}
	if ( ! empty($type) && $type == 'sales' ) {
		$sender_email = get_option('company_sales_email');
	}

	// If $type is other then sender of email will be current user's email.
	if ( ! empty($type) && 'other' === $type ) {
		$current_user = wp_get_current_user();
		$sender_email = $current_user->user_email;
	}

	$sender_name = $sender_name;
	$headers[] .= 'MIME-Version: 1.0';
	$headers[] .= 'Content-Type: text/html; charset=UTF-8';
	$headers[] = 'From: ' . $sender_name . ' <' . $sender_email . '>';
	if ( isset($_SERVER['SERVER_ADDR']) ) {
		$headers[] = 'X-Originating-IP: ' . sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) );
	}
	$value = get_option('cqpim_piping_address');
	$cc = get_option('cqpim_cc_address');
	if ( ! empty($cc) ) {
		$headers[] = 'BCC: ' . $cc;         
	}
	if ( ! empty($value) && $type == 'sales' || ! empty($value) && $type == 'support' ) {
		$headers[] = 'Reply-to: ' . $value;
	}
	$message = nl2br($message);
	$html = get_option('cqpim_html_email');
	if ( ! empty($html) ) {
		$logo = get_option('company_logo');
		$logo = isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : '';
		$styles = get_option('cqpim_html_email_styles');
		$html_message = '<html><head><style>' . $styles . '</style></head>';
		$html_message .= '<body>' . $html . '</body>';
		$html_message .= '</html>';
		$html_message = str_replace('%%EMAIL_CONTENT%%', $message, $html_message);
		$html_message = str_replace('%%LOGO%%', '<img src="' . $logo . '" />', $html_message);
		$message = $html_message;
	}
	return wp_mail($to, $subject, $message, $headers, $attachments);
}
add_action( "wp_ajax_nopriv_pto_delete_file", "pto_delete_file");
add_action( "wp_ajax_pto_delete_file", "pto_delete_file");      
function pto_delete_file() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$att_to_delete = isset( $_POST['ID'] ) ? sanitize_text_field( wp_unslash( $_POST['ID'] ) ) : '';
	wp_delete_attachment( $att_to_delete, true );
	exit();
}
function pto_replacement_patterns( $content, $post_id, $type = NULL ) {
	$user = wp_get_current_user();
	$content = str_replace('%%CURRENT_USER%%', $user->display_name, $content);
	$content = str_replace('%%COMPANY_SALES_EMAIL%%', get_option('company_sales_email'), $content);
	$content = str_replace('%%COMPANY_SUPPORT_EMAIL%%', get_option('company_support_email'), $content);
	$content = str_replace('%%COMPANY_ACCOUNTS_EMAIL%%', get_option('company_accounts_email'), $content);
	$content = str_replace('%%COMPANY_NAME%%', get_option('company_name'), $content);
	$content = str_replace('%%COMPANY_TELEPHONE%%', get_option('company_telephone'), $content);
	$content = str_replace('%%CQPIM_LOGIN%%', get_the_permalink(get_option('cqpim_login_page')), $content);
	$content = str_replace('%%LOGIN%%', get_the_permalink(get_option('cqpim_login_page')), $content);
	$company_name_tag = '%%COMPANY_NAME%%';
	$company_number_tag = '%%COMPANY_NUMBER%%';
	$company_address_tag = '%%COMPANY_ADDRESS%%';
	$company_postcode_tag = '%%COMPANY_POSTCODE%%';
	$company_telephone_tag = '%%COMPANY_TELEPHONE%%';
	$company_sales_email_tag = '%%COMPANY_SALES_EMAIL%%';
	$company_accounts_email_tag = '%%COMPANY_ACCOUNTS_EMAIL%%';
	$account_name_tag = '%%ACCOUNT_NAME%%';
	$sort_code_tag = '%%SORT_CODE%%';
	$account_number_tag = '%%ACCOUNT_NUMBER%%';
	$iban_tag = '%%IBAN%%';
	$company_name = get_option('company_name');
	$company_number = get_option('company_number');
	$company_address = get_option('company_address');
	$company_postcode = get_option('company_postcode');
	$company_telephone = get_option('company_telephone');
	$company_sales_email = get_option('company_sales_email');
	$company_accounts_email = get_option('company_accounts_email');
	$account_name = get_option('company_bank_name');
	$sort_code = get_option('company_bank_sc');
	$account_number = get_option('company_bank_ac');
	$iban = get_option('company_bank_iban');    
	$content = str_replace($company_name_tag, $company_name, $content);
	$content = str_replace($company_number_tag, $company_number, $content);
	$content = str_replace($company_address_tag, $company_address, $content);
	$content = str_replace($company_postcode_tag, $company_postcode, $content);
	$content = str_replace($company_telephone_tag, $company_telephone, $content);
	$content = str_replace($company_sales_email_tag, $company_sales_email, $content);
	$content = str_replace($company_accounts_email_tag, $company_accounts_email, $content);
	$content = str_replace($account_name_tag, $account_name, $content);
	$content = str_replace($sort_code_tag, $sort_code, $content);
	$content = str_replace($account_number_tag, $account_number, $content);
	$content = str_replace($iban_tag, $iban, $content);
	$client_name_tag = '%%CLIENT_NAME%%';
	$client_company_tag = '%%CLIENT_COMPANY%%';
	$client_address_tag = '%%CLIENT_ADDRESS%%';
	$client_postcode_tag = '%%CLIENT_POSTCODE%%';
	$client_telephone_tag = '%%CLIENT_TELEPHONE%%';
	$client_email_tag = '%%CLIENT_EMAIL%%';
	$client_ref_tag = '%%CLIENT_REF%%'; 
	if ( $type == 'quote' ) {
		$quote_details = get_post_meta($post_id, 'quote_details', true);
		$client_id = $quote_details['client_id'];
	} elseif ( $type == 'project' ) {
		$project_details = get_post_meta($post_id, 'project_details', true);
		$client_id = $project_details['client_id'];     
	} elseif ( $type == 'invoice' ) {
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$client_id = $invoice_details['client_id'];     
	} elseif ( $type == 'subscription' ) {
		$client_id = get_post_meta($post_id, 'subscription_client', true);
	} else {
		$client_id = $post_id;
	}
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_ref = isset($client_details['client_ref']) ? $client_details['client_ref'] : '';
	$client_company = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
	$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
	$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$content = str_replace($client_name_tag, $client_name, $content);
	$content = str_replace($client_company_tag, $client_company, $content);
	$content = str_replace($client_address_tag, $client_address, $content);
	$content = str_replace($client_postcode_tag, $client_postcode, $content);
	$content = str_replace($client_telephone_tag, $client_telephone, $content);
	$content = str_replace($client_email_tag, $client_email, $content);
	$content = str_replace($client_ref_tag, $client_ref, $content);
	if ( $type == 'task' ) {
		$task_title_tag = '%%TASK_TITLE%%';
		$task_status_tag = '%%TASK_STATUS%%';
		$task_priority_tag = '%%TASK_PRIORITY%%';
		$task_start_tag = '%%TASK_START%%';
		$task_end_tag = '%%TASK_DEADLINE%%';
		$task_est_tag = '%%TASK_EST%%';
		$task_pc_tag = '%%TASK_PC%%';
		$task_project_tag = '%%TASK_PROJECT%%';
		$task_milestone_tag = '%%TASK_MILESTONE%%';
		$task_owner_tag = '%%TASK_OWNER%%';
		$task_url_tag = '%%TASK_URL%%';
		$task_object = get_post($post_id);
		$task_title = $task_object->post_title;
		$task_details = get_post_meta($post_id, 'task_details', true);
		$task_status = isset($task_details['status']) ? $task_details['status'] : __('N/A', 'projectopia-core');
		/*if ( $task_status == 'pending' ) { $task_status = __('Pending', 'projectopia-core'); } 
		if ( $task_status == 'progress' ) { $task_status = __('In Progress', 'projectopia-core'); } 
		if ( $task_status == 'complete' ) { $task_status = __('Complete', 'projectopia-core'); } 
		if ( $task_status == 'on_hold' ) { $task_status = __('On Hold', 'projectopia-core'); }*/
		$task_status = ucwords( pto_get_task_status_value_by_key( $task_status ) );
		/**
		 * Filter Task Status Display
		 */
		$task_status = apply_filters('pto_task_status_string', $task_status, $task_details['status'], $task_details);
		$task_status = ucwords($task_status);
		$task_priority = isset($task_details['task_priority']) ? $task_details['task_priority'] : __('N/A', 'projectopia-core');
		if ( $task_priority == 'low' ) { $task_priority = __('Low', 'projectopia-core'); } 
		if ( $task_priority == 'normal' ) { $task_priority = __('Normal', 'projectopia-core'); } 
		if ( $task_priority == 'high' ) { $task_priority = __('High', 'projectopia-core'); } 
		if ( $task_priority == 'immediate' ) { $task_priority = __('Immediate', 'projectopia-core'); } 
		$task_priority = ucwords($task_priority);
		$task_start = isset($task_details['task_start']) ? $task_details['task_start'] : __('N/A', 'projectopia-core');
		if ( ! empty($task_start) ) {
			$task_start = wp_date(get_option('cqpim_date_format'), $task_start);
		} else {
			$task_start = __('N/A', 'projectopia-core');
		}
		$deadline = isset($task_details['deadline']) ? $task_details['deadline'] : __('N/A', 'projectopia-core');
		if ( ! empty($deadline) ) {
			$deadline = wp_date(get_option('cqpim_date_format'), $deadline);
		} else {
			$deadline = __('N/A', 'projectopia-core');
		}
		$task_est = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : __('N/A', 'projectopia-core');
		$task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] . '%' : __('N/A', 'projectopia-core');
		$task_owner = get_post_meta($post_id, 'owner', true);
		$client_check = preg_replace('/[0-9]+/', '', $task_owner);
		$client = false;
		if ( $client_check == 'C' ) {
			$client = true;
		}
		if ( $task_owner ) {
			if ( $client == true ) {
				$id = preg_replace("/[^0-9,.]/", "", $task_owner);
				$client_object = get_user_by('id', $id);
				$task_owner = $client_object->display_name;
			} else {
				$team_details = get_post_meta($task_owner, 'team_details', true);
				$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
				if ( ! empty($team_name) ) {
					$task_owner = $team_name;
				}
			}
		} else {
			$task_owner = '';
		}
		$task_owner = isset($task_owner) ? $task_owner : __('N/A', 'projectopia-core');
		$project_id = get_post_meta($post_id, 'project_id', true);
		$project_details = get_post_meta($project_id, 'project_details', true);
		$project_object = get_post($project_id);
		$project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
		$project_ref = $project_object->post_title;
		$project_elements = get_post_meta($project_id, 'project_elements', true);
		$milestone_id = get_post_meta($post_id, 'milestone_id', true);
		$milestone_name = isset($project_elements[ $milestone_id ]['title']) ? $project_elements[ $milestone_id ]['title'] : __('N/A', 'projectopia-core');
		$user = wp_get_current_user();
		$roles = $user->roles;
		if ( in_array('cqpim_client', $roles) ) {
			$task_url = site_url() . '/wp-admin/post.php?post=' . $task_object->ID . '&action=edit';
		} else {
			$task_url = get_the_permalink($task_object->ID);
		}
		$content = str_replace($task_title_tag, $task_title, $content);
		$content = str_replace($task_status_tag, $task_status, $content);
		$content = str_replace($task_priority_tag, $task_priority, $content);
		$content = str_replace($task_start_tag, $task_start, $content);
		$content = str_replace($task_end_tag, $deadline, $content);
		$content = str_replace($task_est_tag, $task_est, $content);
		$content = str_replace($task_pc_tag, $task_pc, $content);
		$content = str_replace($task_owner_tag, $task_owner, $content);
		$content = str_replace($task_project_tag, $project_ref, $content);
		$content = str_replace($task_milestone_tag, $milestone_name, $content);
		$content = str_replace($task_url_tag, $task_url, $content);
	}
	if ( $type == 'ticket' ) {
		$ticket_id_tag = '%%TICKET_ID%%';
		$ticket_title_tag = '%%TICKET_TITLE%%';
		$ticket_status_tag = '%%TICKET_STATUS%%';
		$ticket_priority_tag = '%%TICKET_PRIORITY%%';
		$ticket_owner_tag = '%%TICKET_OWNER%%';
		$ticket_update_tag = '%%TICKET_UPDATE%%';
		$ticket_id = $post_id;
		$ticket_title = get_the_title($post_id);
		$ticket_title = str_replace('Private:', '', $ticket_title);
		$ticket_owner = get_post_meta($post_id, 'ticket_owner', true);
		$owner_details = get_post_meta($ticket_owner, 'team_details', true);
		$ticket_owner = isset($owner_details['team_name']) ? $owner_details['team_name'] : '';
		$ticket_status = get_post_meta($post_id, 'ticket_status', true);
		$ticket_status = ucfirst($ticket_status);
		$ticket_priority = get_post_meta($post_id, 'ticket_priority', true);
		$ticket_priority = ucfirst($ticket_priority);
		$content = str_replace($ticket_owner_tag, $ticket_owner, $content);
		$content = str_replace($ticket_title_tag, $ticket_title, $content);
		$content = str_replace($ticket_id_tag, $ticket_id, $content);
		$content = str_replace($ticket_status_tag, $ticket_status, $content);
		$content = str_replace($ticket_priority_tag, $ticket_priority, $content);
		$ticket_updates = get_post_meta($post_id, 'ticket_updates', true);
		if ( empty($ticket_updates) ) {
			$ticket_updates = array();
		}
		$ticket_updates = array_reverse($ticket_updates);
		$ticket_update = isset($ticket_updates[0]['details']) ? $ticket_updates[0]['details'] : '';
		$content = str_replace($ticket_update_tag, $ticket_update, $content);
	}
	if ( $type == 'team' ) {
		$team_name_tag = '%%TEAM_NAME%%';
		$team_email_tag = '%%TEAM_EMAIL%%';
		$team_telephone_tag = '%%TEAM_TELEPHONE%%';
		$team_job_tag = '%%TEAM_JOB%%';
		$team_details = get_post_meta($post_id, 'team_details', true);
		$team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
		$team_email = isset($team_details['team_email']) ? $team_details['team_email'] : '';
		$team_telephone = isset($team_details['team_telephone']) ? $team_details['team_telephone'] : '';
		$team_job = isset($team_details['team_job']) ? $team_details['team_job'] : '';
		$content = str_replace($team_name_tag, $team_name, $content);
		$content = str_replace($team_email_tag, $team_email, $content);
		$content = str_replace($team_telephone_tag, $team_telephone, $content);
		$content = str_replace($team_job_tag, $team_job, $content);         
	}
	if ( $type == 'quote' ) {      
		$quote_ref_tag = '%%QUOTE_REF%%';
		$quote_link_tag = '%%QUOTE_LINK%%';
		$quote_type_tag = '%%TYPE%%';
		$client_login_tag = '%%CQPIM_LOGIN%%';
		$quote_details = get_post_meta($post_id, 'quote_details', true);
		$quote_type = $quote_details['quote_type'];
		if ( $quote_type == 'estimate' ) {
			$quote_type = __('estimate', 'projectopia-core');
		} else {
			$quote_type = __('quote', 'projectopia-core');
		}
		$quote_link = get_the_permalink($post_id);
		$quote_object = get_post($post_id);
		$quote_ref = $quote_object->post_title;
		$quote_password = md5( $quote_object->post_password );
		$quote_link = $quote_link . '?pwd=' . $quote_password;
		$quote_link2 = get_the_permalink($post_id);
		$content = str_replace($quote_ref_tag, $quote_ref, $content);
		$content = str_replace($quote_link_tag, $quote_link, $content);
		$content = str_replace($quote_type_tag, $quote_type, $content);
	}
	if ( $type == 'project' ) {        
		$contract_link_tag = '%%CONTRACT_LINK%%';
		$summary_link_tag = '%%SUMMARY_LINK%%';
		$project_ref_tag = '%%PROJECT_REF%%';
		$project_type_tag = '%%TYPE%%';
		$project_details = get_post_meta($post_id, 'project_details', true);
		$type = isset($project_details['quote_type']) ? $project_details['quote_type'] : '';
		$project_link = get_the_permalink($post_id);
		$project_object = get_post($post_id);
		$project_ref = $project_object->post_title;
		$project_password = md5( $project_object->post_password );
		$contract_link = $project_link . '?pwd=' . $project_password . '&page=contract';
		$summary_link = $project_link . '?pwd=' . $project_password . '&page=summary';
		$content = str_replace($contract_link_tag, $contract_link, $content);
		$content = str_replace($summary_link_tag, $summary_link, $content);
		$content = str_replace($project_ref_tag, $project_ref, $content);
		$content = str_replace($project_type_tag, $type, $content);
	}
	if ( $type == 'invoice' ) {        
		$invoice_id_tag = '%%INVOICE_ID%%';
		$invoice_link_tag = '%%INVOICE_LINK%%';
		$invoice_due_tag = '%%DUE_DATE%%';
		$invoice_object = get_post($post_id);
		$invoice_id = get_post_meta($post_id, 'invoice_id', true);
		$invoice_link = get_the_permalink($post_id);
		$invoice_password = md5( $invoice_object->post_password );
		$invoice_link = $invoice_link . '?pto-page=print&pwd=' . $invoice_password;
		$content = str_replace($invoice_id_tag, $invoice_id, $content);
		$content = str_replace($invoice_link_tag, $invoice_link, $content);
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
		$due = wp_date(get_option('cqpim_date_format'), $due);
		$content = str_replace($invoice_due_tag, $due, $content);
	}
	if ( $type == 'subscription' ) {
		$sub_object = get_post($post_id);
		$subscription_id = get_post_meta($post_id, 'subscription_id', true);
		$sub_id_tag = '%%SUBSCRIPTION_ID%%';
		$sub_plan_tag = '%%SUBSCRIPTION_PLAN_DETAILS%%';
		$content = str_replace($sub_id_tag, $subscription_id, $content);
		$sub_retry_tag = '%%PAYMENT_RETRY_INFO%%';
		$subscription_retry = get_post_meta($post_id, 'subscription_retry', true);
		
		$sub_item_type = get_post_meta($post_id, 'sub_item_type', true);
		$table_content = '';
		$subscription_frequency = get_post_meta($post_id, 'subscription_frequency', true);
		if ( $subscription_frequency == 'day' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Daily', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'week' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Weekly', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'biweek' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Every Two Weeks', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'month' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Monthly', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'bimonth' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Every Two Months', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'sixmonth' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Every 6 Months', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'year' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Yearly', 'projectopia-core') . '</p>';
		} elseif ( $subscription_frequency == 'biyear' ) {
			$table_content .= '<p><strong>' . esc_html__('Frequency of Billing', 'projectopia-core') . '</strong> : ' . __('Every Two Years', 'projectopia-core') . '</p>';
		}
		if ( ! empty($sub_item_type) && $sub_item_type == 'plan' ) {
			$sub_plan = get_post_meta($post_id, 'sub_plan', true);
			$line_items = get_post_meta($sub_plan, 'line_items', true);
			$totals = get_post_meta($sub_plan, 'invoice_totals', true);
			$sub = isset($totals['sub']) ? $totals['sub'] : '';
			$vat = isset($totals['tax']) ? $totals['tax'] : '';
			$svat = isset($totals['stax']) ? $totals['stax'] : '';
			$total = isset($totals['total']) ? $totals['total'] : '';   
			$vat_rate = get_post_meta($post_id, 'tax_rate', true);
			$svat_rate = get_post_meta($post_id, 'stax_rate', true);
			$tax_name = get_option('sales_tax_name');
			$tax_reg = get_option('sales_tax_reg');
			$stax_name = get_option('secondary_sales_tax_name');
			$stax_reg = get_option('secondary_sales_tax_reg');  
			$table_content .= '<table class="cqpim_table" style="width:100%">';
				$table_content .= '<thead style="background:#ececec">';
					$table_content .= '<tr>';
						$table_content .= '<th>' . esc_html__('Qty', 'projectopia-core') . '</th>';
						$table_content .= '<th>' . esc_html__('Description', 'projectopia-core') . '</th>';
						$table_content .= '<th>' . esc_html__('Rate', 'projectopia-core') . '</th>';
						$table_content .= '<th>' . esc_html__('Total', 'projectopia-core') . '</th>';
					$table_content .= '</tr>';
				$table_content .= '</thead>';
				$table_content .= '<tbody>';
				if ( empty($line_items) ) {
					$line_items = array();
				}               
				foreach ( $line_items as $item ) {
					$table_content .= '<tr>';
						$table_content .= '<td style="text-align:center">' . $item['qty'] . '</td>';
						$table_content .= '<td style="text-align:center">' . $item['desc'] . '</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $item['price']) . '</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $item['sub']) . '</td>';
					$table_content .= '</tr>';
				}
				$table_content .= '<tr>';
					$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">';
					if ( $vat_rate ) { 
						$table_content .= __('Subtotal:', 'projectopia-core');
					} else { 
						$table_content .= __('TOTAL:', 'projectopia-core');
					}
					$table_content .= '</td>';
					$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $sub) . '</td>';
				$table_content .= '</tr>';
				$outstanding = $sub;
				if ( $vat_rate ) { 
					$outstanding = $total;
					$tax_name = get_option('sales_tax_name');
					$table_content .= '<tr>';
						$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">' . esc_html( $tax_name ) . ':</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $vat) . '</td>';
					$table_content .= '</tr>';
					if ( ! empty($svat_rate) ) {
						$table_content .= '<tr>';
						$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">' . esc_html( $stax_name ) . ':</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $svat) . '</td>';
						$table_content .= '</tr>';              
					}
					$table_content .= '<tr>';
						$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">' . esc_html__('TOTAL:', 'projectopia-core') . '</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $total) . '</td>';
					$table_content .= '</tr>';
				}
				$table_content .= '</tbody>';
			$table_content .= '</table>';   
		}
		if ( ! empty($sub_item_type) && $sub_item_type == 'line' ) {
			$line_items = get_post_meta($post_id, 'line_items', true); 
			$tax_name = get_option('sales_tax_name');
			$stax_name = get_option('secondary_sales_tax_name');
			$tax_applicable = get_post_meta($post_id, 'tax_applicable', true);
			$stax_applicable = get_post_meta($post_id, 'stax_applicable', true); 
			$tax_app = get_post_meta($post_id, 'tax_set', true);
			$vat_rate = get_post_meta($post_id, 'tax_rate', true);
			$svat_rate = get_post_meta($post_id, 'stax_rate', true);
			$totals = get_post_meta($post_id, 'invoice_totals', true);
			$sub = isset($totals['sub']) ? $totals['sub'] : '';
			$vat = isset($totals['tax']) ? $totals['tax'] : '';
			$svat = isset($totals['stax']) ? $totals['stax'] : '';
			$total = isset($totals['total']) ? $totals['total'] : '';
			$table_content .= '<table class="cqpim_table" style="width:100%">';
				$table_content .= '<thead style="background:#ececec">';
					$table_content .= '<tr>';
						$table_content .= '<th>' . esc_html__('Qty', 'projectopia-core') . '</th>';
						$table_content .= '<th>' . esc_html__('Description', 'projectopia-core') . '</th>';
						$table_content .= '<th>' . esc_html__('Rate', 'projectopia-core') . '</th>';
						$table_content .= '<th>' . esc_html__('Total', 'projectopia-core') . '</th>';
					$table_content .= '</tr>';
				$table_content .= '</thead>';
				$table_content .= '<tbody>';
				if ( empty($line_items) ) {
					$line_items = array();
				}               
				foreach ( $line_items as $item ) {
					$table_content .= '<tr>';
						$table_content .= '<td style="text-align:center">' . $item['qty'] . '</td>';
						$table_content .= '<td style="text-align:center">' . $item['desc'] . '</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $item['price']) . '</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $item['sub']) . '</td>';
					$table_content .= '</tr>';
				}
				$table_content .= '<tr>';
					$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">';
					if ( $vat_rate ) { 
						$table_content .= __('Subtotal:', 'projectopia-core');
					} else { 
						$table_content .= __('TOTAL:', 'projectopia-core');
					}
					$table_content .= '</td>';
					$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $sub) . '</td>';
				$table_content .= '</tr>';
				$outstanding = $sub;
				if ( $vat_rate ) { 
					$outstanding = $total;
					$tax_name = get_option('sales_tax_name');
					$table_content .= '<tr>';
						$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">' . esc_html( $tax_name ) . ':</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $vat) . '</td>';
					$table_content .= '</tr>';
					if ( ! empty($svat_rate) ) {
						$table_content .= '<tr>';
						$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">' . esc_html( $stax_name ) . ':</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $svat) . '</td>';
						$table_content .= '</tr>';              
					}
					$table_content .= '<tr>';
						$table_content .= '<td style="text-align:right; font-weight:bold" colspan="3">' . esc_html__('TOTAL:', 'projectopia-core') . '</td>';
						$table_content .= '<td style="text-align:center">' . pto_calculate_currency($post_id, $total) . '</td>';
					$table_content .= '</tr>';
				}
				$table_content .= '</tbody>';
			$table_content .= '</table>';               
		}
		$content = str_replace($sub_plan_tag, $table_content, $content);
	}
	// CQPIM Login
	$client_login_tag = '%%CQPIM_LOGIN%%';
	$client_login = get_option('cqpim_login_page');
	$client_login = get_the_permalink($client_login);
	$content = str_replace($client_login_tag, $client_login, $content);
	// client Passwords
	$client_password_tag = '%%CLIENT_PASSWORD_LINK%%';
	$reset_page = get_option( 'cqpim_reset_page' );
	$client_password = get_the_permalink($reset_page);
	$content = str_replace($client_password_tag, $client_password, $content);
	return $content;
}
function pto_return_open() {
	$business_hours = get_option('pto_opening');
	$array = current_datetime();
	$now = $array->getTimestamp() + $array->getOffset();
	$day_of_week = gmdate('D', $now);
	$day_of_week = strtolower($day_of_week);
	$today_array = $business_hours[ $day_of_week ];
	if ( empty($today_array['active']) ) {
		return 1;
	} else {
		if ( ! empty($today_array['open']) && ! empty($today_array['close']) ) {
			$open = strtotime($today_array['open']);
			$close = strtotime($today_array['close']);
			if ( $now > $open && $now < $close ) {
				return 2;
			} else {
				return 1;
			}
		} else {
			return 3;
		}
	}
}
add_filter( 'post_updated_messages', 'pto_post_published' );
function pto_post_published( $messages ) {
	global $post;
	if ( ! empty($post) ) {
		if ( $post->post_type == 'cqpim_templates' || 
		$post->post_type == 'cqpim_quote' || 
		$post->post_type == 'cqpim_terms' || 
		$post->post_type == 'cqpim_forms' || 
		$post->post_type == 'cqpim_project' || 
		$post->post_type == 'cqpim_client' || 
		$post->post_type == 'cqpim_teams' || 
		$post->post_type == 'cqpim_support' || 
		$post->post_type == 'cqpim_supplier' || 
		$post->post_type == 'cqpim_expense' || 
		$post->post_type == 'cqpim_bug' ||
		$post->post_type == 'cqpim_plan' ||
		$post->post_type == 'cqpim_subscription' ||
		$post->post_type == 'cqpim_leadform' ||
		$post->post_type == 'cqpim_lead'
		) {
			unset($messages['post'][1]);
			unset($messages['post'][6]);
			return $messages;
		}
	}
}

function pto_set_transient( $key, $value, $duration = 3600 ) {
	if ( is_user_logged_in() ) {
		$user = get_current_user_id();
		$option = 'pto_' . $user . '_' . $key;
		set_transient( $option, $value, $duration );
	} else {
		$option = 'pto_' . $key;
		set_transient( $option, $value, $duration );
	}
}

function pto_get_transient( $key, $default = '' ) {
	if ( is_user_logged_in() ) {
		$user = get_current_user_id();
		$option = 'pto_' . $user . '_' . $key;
		$value = get_transient( $option );
	} else {
		$option = 'pto_' . $key;
		$value = get_transient( $option );
	}
	if ( ! empty( $value ) ) {
		return $value;
	}
	return $default;
}

add_action('clear_auth_cookie','pto_clear_transients');
function pto_clear_transients(){
	if ( is_user_logged_in() ) {
		global $wpdb;
		$user = get_current_user_id();
		$option = 'pto_'.$user;
		// $query = "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%$option%'";
		// $wpdb->query($query);
		delete_option($option);
	}

	// If admin dashboard metabox filter setting is disable the remove save filters results.
	if ( empty ( get_option('cqpim_save_dashboard_metabox_filters') ) ) {
		$user_id = get_current_user_id();
		delete_option( 'pto_dashboard_project_order_'.$user_id );
		delete_option( 'pto_dashboard_project_posts_'.$user_id );
		delete_option( 'pto_dashboard_project_category_'.$user_id );
	}
}

// Return empty value if Custom fields is addon not available to make it backwards compatible
add_filter( 'option_cqpim_custom_fields_support', 'pto_filter_custom_fields_data' );
add_filter( 'option_cqpim_custom_fields_client', 'pto_filter_custom_fields_data' );
add_filter( 'option_cqpim_custom_fields_invoice', 'pto_filter_custom_fields_data' );
add_filter( 'option_cqpim_custom_fields_task', 'pto_filter_custom_fields_data' );
function pto_filter_custom_fields_data( $value ) {
	if ( ! pto_has_addon_active_license( 'pto_cf', 'customfields' ) ) {
		return false;
	}

	return $value;
}