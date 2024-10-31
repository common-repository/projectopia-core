<?php
if ( ! function_exists('pto_quotes_cpt') ) {
	function pto_quotes_cpt() {
		if ( current_user_can('cqpim_create_new_quote') && current_user_can('publish_cqpim_quotes') ) {
			$quote_caps = array();
		} else {
			$quote_caps = array( 'create_posts' => false );
		}
		$labels = array(
			'name'               => _x( 'Quotes / Estimates', 'Post Type General Name', 'projectopia-core' ),
			'singular_name'      => _x( 'Quote / Estimate', 'Post Type Singular Name', 'projectopia-core' ),
			'menu_name'          => __( 'Quotes / Estimates', 'projectopia-core' ),
			'parent_item_colon'  => __( 'Parent Quote / Estimate:', 'projectopia-core' ),
			'all_items'          => __( 'Quotes / Estimates', 'projectopia-core' ),
			'view_item'          => __( 'View Quote / Estimate', 'projectopia-core' ),
			'add_new_item'       => __( 'Add New Quote / Estimate', 'projectopia-core' ),
			'add_new'            => __( 'New Quote / Estimate', 'projectopia-core' ),
			'edit_item'          => __( 'Edit Quote / Estimate', 'projectopia-core' ),
			'update_item'        => __( 'Update Quote / Estimate', 'projectopia-core' ),
			'search_items'       => __( 'Search Quotes / Estimates', 'projectopia-core' ),
			'not_found'          => __( 'No Quotes / Estimates found', 'projectopia-core' ),
			'not_found_in_trash' => __( 'No Quotes / Estimates found in trash', 'projectopia-core' ),
		);
		$args = array(
			'label'               => __( 'quote', 'projectopia-core' ),
			'description'         => __( 'Quotes', 'projectopia-core' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'capabilities'        => $quote_caps,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => 'cqpim-dashboard', 
			'show_in_admin_bar'   => true,
			'menu_position'       => 1,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => array( 'cqpim_quote', 'cqpim_quotes' ),
			'map_meta_cap'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => get_option('cqpim_quote_slug') ),
		);
		register_post_type( 'cqpim_quote', $args );
	}
	add_action( 'init', 'pto_quotes_cpt', 10 );
}
if ( ! function_exists( 'pto_quote_cpt_custom_columns' ) ) {
	function pto_quote_cpt_custom_columns( $columns ) {
		$new_columns = array(
			'title'          => __('Title', 'projectopia-core'),
			'client_details' => __('Client Details', 'projectopia-core'),
			'q_status'       => __('Status', 'projectopia-core'),
		);
		unset($columns['date']);
	    return array_merge( $columns, $new_columns );
	}
	add_filter('manage_cqpim_quote_posts_columns' , 'pto_quote_cpt_custom_columns', 10, 1 );
}
add_action( 'manage_cqpim_quote_posts_custom_column', 'content_pto_quote_cpt_columns', 10, 2 );
function content_pto_quote_cpt_columns( $column, $post_id ) {
	global $post;
	$quote_details = get_post_meta( $post_id, 'quote_details', true );
	$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
	$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$converted = get_post_meta($post_id, 'converted_project', true);
	if ( ! empty($client_contact) ) {
		if ( ! empty($client_details['user_id']) && $client_details['user_id'] == $client_contact ) {
			$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
			$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
		} else {
			$client_contact_name = isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_contacts[ $client_contact ]['telephone']) ? $client_contacts[ $client_contact ]['telephone'] : '';
			$client_email = isset($client_contacts[ $client_contact ]['email']) ? $client_contacts[ $client_contact ]['email'] : '';        
		}
	} else {
		$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
		$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';      
	}
	$quote_sent = isset($quote_details['sent']) ? $quote_details['sent'] : '';
	$quote_accepted = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : '';
	$quote_type = isset($quote_details['quote_type']) ? $quote_details['quote_type'] : '';      
	switch ( $column ) {
		case 'client_details':
			echo '<strong>' . esc_html_e('Company Name:', 'projectopia-core') . '</strong> ' . esc_html( $client_company_name ) . '<br />';
			echo '<strong>' . esc_html_e('Contact Name:', 'projectopia-core') . '</strong> ' . esc_html( $client_contact_name ) . '<br />';
			echo '<strong>' . esc_html_e('Email:', 'projectopia-core') . '</strong> ' . esc_html( $client_email ) . '<br />';
			echo '<strong>' . esc_html_e('Telephone:', 'projectopia-core') . '</strong> ' . esc_html( $client_telephone ) . '<br />';
		    break;
		case 'q_status':
			if ( ! $quote_accepted ) {
				if ( empty($quote_sent) ) {
					echo '<div class="cqpim-alert cqpim-alert-danger">';
					$quote_type == 'estimate' ? esc_html_e('This estimate has not yet been sent to the client.', 'projectopia-core') : esc_html_e('This quote has not yet been sent to the client.', 'projectopia-core');
					echo '</div>';
				}
				if ( $quote_sent ) {
					$quote_sent = $quote_details['sent_details'];
					$to = isset($quote_sent['to']) ? $quote_sent['to'] : '';
					$by = isset($quote_sent['by']) ? $quote_sent['by'] : '';
					$at = isset($quote_sent['date']) ? $quote_sent['date'] : '';
					if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
					echo '<div class="cqpim-alert cqpim-alert-warning">';
					/* translators: %1$s: Quote Type, %2$s: Sent to Name, %3$s: Timestamp, %4$s: Sent By author */
					$quote_type == 'estimate' ? printf(esc_html__('This estimate was sent to %2$s on %3$s by %4$s', 'projectopia-core'), esc_html( $quote_type ), esc_html( $to ), esc_html( $at ), wp_kses_post( $by )) : printf(esc_html__('This quote was sent to %2$s on %3$s by %4$s', 'projectopia-core'), esc_html( $quote_type ), esc_html( $to ), esc_html( $at ), wp_kses_post( $by ));
					echo '</div>';
				}
			} else {
				$quote_accepted = $quote_details['confirmed_details'];
				$ip = isset($quote_accepted['ip']) ? $quote_accepted['ip'] : '';
				$by = isset($quote_accepted['by']) ? $quote_accepted['by'] : '';
				$at = isset($quote_accepted['date']) ? $quote_accepted['date'] : '';
				if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
				echo '<div class="cqpim-alert cqpim-alert-success">';
				/* translators: %1$s: Sent to Name, %2$s: Timestamp, %3$s: IP */
				$quote_type == 'estimate' ? printf(esc_html__('This estimate was accepted by %1$s on %2$s from IP address %3$s', 'projectopia-core'), esc_html( $quote_type ), wp_kses_post( $by ), esc_html( $at ), esc_html( $ip )) : printf(esc_html__('This quote was accepted by %1$s on %2$s from IP address %3$s', 'projectopia-core'), wp_kses_post( $by ), esc_html( $at ), esc_html( $ip ));
				echo '</div>';              
			}

			if ( $converted ) {
				echo '<div class="cqpim-alert cqpim-alert-success mt-2">';
				/* translators: %1$s: Sent to Name, %2$s: Timestamp, %3$s: IP */
				printf( esc_html__('This was converted to the Project %s', 'projectopia-core'), '<a href="' . get_edit_post_link( $converted ) . '" target="_blank">#' . $converted . '</a>' );
				echo '</div>';
			}
		    break;
		default:
			break;
	}
} 