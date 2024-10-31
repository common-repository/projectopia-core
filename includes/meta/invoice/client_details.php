<?php
function pto_invoice_client_project_metabox_callback( $post ) {
 	wp_nonce_field( 'invoice_client_project_metabox', 'invoice_client_project_metabox_nonce' );

	$client_contact = get_post_meta( $post->ID, 'client_contact', true );
	$invoice_details = get_post_meta( $post->ID, 'invoice_details', true );
	$invoice_id = get_post_meta( $post->ID, 'invoice_id', true );
	$due = isset( $invoice_details['terms_over'] ) ? $invoice_details['terms_over'] : '';
	$on_receipt = isset( $invoice_details['on_receipt'] ) ? $invoice_details['on_receipt'] : '';
	$invoice_date = isset( $invoice_details['invoice_date'] ) ? $invoice_details['invoice_date'] : '';
	$allow_partial = isset( $invoice_details['allow_partial'] ) ? $invoice_details['allow_partial'] : '';
	$deposit = isset( $invoice_details['deposit'] ) ? $invoice_details['deposit'] : '';
	$client_id = isset( $invoice_details['client_id'] ) ? $invoice_details['client_id'] : '';

	// Getting invoice layout style.
	$invoice_layout_rtl = 0;
	if ( ! empty( $invoice_details['invoice_layout_rtl'] ) ) {
		$invoice_layout_rtl = $invoice_details['invoice_layout_rtl'];
	}

	if ( ! $invoice_date ) {
        // Get local time base on user current timezone
        $local_time = gmdate( 'Y-m-d H:i:s', time() + get_option( 'gmt_offset' ) * 60 * 60 );
        $timezone   = wp_timezone();
        $datetime   = date_create( $local_time, $timezone );
        $invoice_date = wp_date( get_option('cqpim_date_format'), $datetime->getTimestamp(), $timezone );
	}

	if ( empty( $invoice_id ) ) {
		$invoice_id = pto_get_invoice_id();
	}

	pto_generate_fields( array(
		'id'    => 'invoice_number',
		'label' => __( 'Invoice Number:', 'projectopia-core' ),
		'value' => $invoice_id,
	) );

	pto_generate_fields( array(
		'id'    => 'invoice_date',
		'label' => __( 'Invoice Date:', 'projectopia-core' ),
		'value' => is_numeric( $invoice_date ) ? wp_date( get_option( 'cqpim_date_format' ), $invoice_date ) : $invoice_date,
		'class' => 'datepicker',
	) );

	$paid = isset( $invoice_details['paid'] ) ? $invoice_details['paid'] : '';
	$now = time();
	if ( empty( $on_receipt ) ) {
		if ( ! $paid ) {
			if ( $due ) {
				if ( $now > $due ) {
					echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__( 'THIS INVOICE IS OVERDUE', 'projectopia-core' ) . '</div>';        
				}
			}
		}
	}

	$args = array(
		'post_type'      => 'cqpim_client',
		'posts_per_page' => -1,
		'post_status'    => 'private',
	);
	
	$clients = get_posts( $args );
	$options = [];
	foreach ( $clients as $client ) {
		$client_details = get_post_meta( $client->ID, 'client_details', true );
		$client_contact_name = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
		$client_company_name = isset( $client_details['client_company'] ) ? $client_details['client_company'] : $client_contact_name;
		$options[ $client->ID ] = $client_company_name;
	}

	pto_generate_fields( array(
		'type'     => 'select',
		'id'       => 'invoice_client',
		'label'    => __( 'Client:', 'projectopia-core' ),
		'value'    => $client_id,
		'options'  => $options,
		'default'  => __( 'Select a Client...', 'projectopia-core' ),
		'required' => true,
	) );

	$options = [];
	if ( ! empty( $client_id ) ) {
		$client_details = get_post_meta( $client_id, 'client_details', true );
		$client_contacts = get_post_meta( $client_id, 'client_contacts', true );
		
		if ( $client_details ) {
			$options = [
				$client_details['user_id'] => $client_details['client_contact'] . ' ' . __( '(Main Contact)', 'projectopia-core' ),
			];
			if ( ! empty( $client_contacts ) ) {
				foreach ( $client_contacts as $contact ) {
					$options[ $contact['user_id'] ] = $contact['name'];
				}
			}
		}
	}

	$project_id = isset( $invoice_details['project_id'] ) ? absint( $invoice_details['project_id'] ) : '';

	pto_generate_fields( array(
		'type'    => 'select',
		'id'      => 'client_contact',
		'label'   => __( 'Client Contact:', 'projectopia-core' ),
		'value'   => $client_contact,
		'options' => $options,
		'default' => __( 'Select a Contact...', 'projectopia-core' ),
	) );

	if ( ! empty( $invoice_details['ticket'] ) ) {
		pto_generate_fields( array(
			'type'  => 'hidden',
			'id'    => 'invoice_project',
			'value' => $project_id,
		) );
	} else {
		$args = array(
			'post_type'      => 'cqpim_project',
			'posts_per_page' => -1,
			'post_status'    => 'private',                 
		);
		$projects = get_posts( $args );

		$options = [];
		if ( $invoice_details ) {
			$invoice_client_id = $invoice_details['client_id'];
			foreach ( $projects as $project ) {
				$project_details = get_post_meta( $project->ID, 'project_details', true );
				$project_client_id = $project_details['client_id'];
				
				if ( $invoice_client_id == $project_client_id ) {
					$options[ $project->ID ] = $project_details['quote_ref'] . ' - ' . $project->post_title;
				}
			}
		}

		pto_generate_fields( array(
			'type'    => 'select',
			'id'      => 'invoice_project',
			'label'   => __( 'Project:', 'projectopia-core' ),
			'value'   => $project_id,
			'options' => $options,
			'default' => __( 'Choose a Project...', 'projectopia-core' ),
		) );
	}

	pto_generate_fields( array(
		'type'    => 'checkbox',
		'id'      => 'invoice_layout_rtl',
		'label'   => __( 'Make invoice layout as RTL', 'projectopia-core' ),
		'checked' => 1 === $invoice_layout_rtl,
	) );

	if ( $deposit != 1 ) {
		pto_generate_fields( array(
			'type'    => 'checkbox',
			'id'      => 'allow_partial',
			'label'   => __( 'Allow partial payments on this invoice', 'projectopia-core' ),
			'checked' => 1 == $allow_partial,
		) );
	}

	$invoice_paid = isset($invoice_details['paid_details']) ? $invoice_details['paid_details'] : '';
	$invoice_sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
	$client = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
	$project = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
	$line_items = get_post_meta($post->ID, 'line_items', true);
	?>
	<p class="underline"><?php esc_html_e('Invoice Status', 'projectopia-core'); ?></p>
	<table class="quote_status">
		<tr>
			<td class="title"><?php esc_html_e('Invoice Date', 'projectopia-core'); ?></td>
			<?php if ( is_numeric($invoice_date) ) { $invoice_date = wp_date(get_option('cqpim_date_format'), $invoice_date); } else { $invoice_date = $invoice_date; } ;?>
			<td><?php echo esc_html( $invoice_date ); ?></td>
		</tr>
		<tr>
			<td class="title"><?php esc_html_e('Invoice Due', 'projectopia-core'); ?></td>
			<?php if ( empty($on_receipt) ) { ?>
				<?php if ( $due ) { ?>
				<td><?php echo esc_html( wp_date(get_option('cqpim_date_format'), $due) ); ?></td>
				<?php } else { ?>
				<td></td>
				<?php } ?>
			<?php } else { ?>
				<td><?php esc_html_e('On Receipt', 'projectopia-core'); ?></td>
			<?php } ?>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $client ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Client', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $project ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Project', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $line_items ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Line Items', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $invoice_sent ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Invoice Sent', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
		<tr>
			<?php $classes = ( ! empty( $invoice_paid ) ) ? 'green' : 'red'; ?>
			<td class="title"><?php esc_html_e('Invoice Paid', 'projectopia-core'); ?></td>
			<td class="<?php echo esc_attr( $classes ); ?>"></td>
		</tr>
	</table>
	<?php
	
	$invoice_sent = isset( $invoice_details['sent'] ) ? $invoice_details['sent'] : '';
	$invoice_paid = isset( $invoice_details['paid'] ) ? $invoice_details['paid'] : '';
	
	if ( current_user_can( 'publish_cqpim_invoices' ) ) {
		echo '<a class="piaBtn btn btn-primary btn-block mt-2 save" href="#">' . esc_html__( 'Update Invoice', 'projectopia-core' ) . '</a>';
		echo '<a id="edit_due" class="piaBtn btn btn-primary btn-block" href="#">' . esc_html__( 'Edit Due Date', 'projectopia-core' ) . '</a>'; ?>	
		<div id="invoice_due_container" style="display: none;">
			<div id="invoice_due">
				<div style="padding: 12px;">
					<h3><?php esc_html_e( 'Edit Invoice Due Date', 'projectopia-core' ); ?></h3>
					<?php

					pto_generate_fields( array(
						'id'    => 'due_date',
						'label' => __( 'Due Date', 'projectopia-core' ),
						'value' => is_numeric( $due ) ? wp_date( get_option( 'cqpim_date_format' ), $due ) : $due,
						'class' => 'datepicker',
					) );

					echo '<button class="piaBtn btn btn-primary btn-block" id="edit_date_conf" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Confirm Due Date', 'projectopia-core' ) . '</button>';
					
					?>								
				</div>
			</div>
		</div>		
		<?php
	}
	
	if ( $post->post_name ) {
		$url = get_the_permalink( $post->ID );
		$pdf_url = add_query_arg( 'download_pdf', '1', get_edit_post_link( $post->ID ) );
		
		echo '<a class="piaBtn btn btn-primary btn-block mt-2" href="' . esc_url( $url ) . '?pto-page=print" target="_blank">' . esc_html__( 'View Printable Invoice', 'projectopia-core' ) . '</a>';
		echo '<a class="piaBtn btn btn-primary btn-block" href="' . esc_url( $pdf_url ) . '">' . esc_html__( 'Download PDF Invoice', 'projectopia-core' ) . '</a>';
	}   
	
	if ( ! $invoice_paid ) {
		if ( empty( $invoice_sent ) ) {
			echo '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__( 'The invoice has not yet been sent to the client.', 'projectopia-core' ) . '</div>';
			if ( current_user_can( 'cqpim_mark_invoice_paid' ) ) {
				echo '<button class="piaBtn btn btn-primary btn-block" id="mark_paid_trigger" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Add Payment', 'projectopia-core' ) . '</button>'; ?>
				<div id="invoice_payment_container" style="display: none;">
					<div id="invoice_payment">
						<div style="padding: 12px;">
							<h3><?php esc_html_e( 'Add Payment', 'projectopia-core' ); ?></h3>
							<?php

							pto_generate_fields( array(
								'id'      => 'payment_amount',
								'label'   => __( 'Payment Amount', 'projectopia-core' ),
								'prepend' => get_option( 'currency_symbol' ),
							) );

							pto_generate_fields( array(
								'id'    => 'payment_date',
								'label' => __( 'Payment Date', 'projectopia-core' ),
								'value' => wp_date( get_option( 'cqpim_date_format' ), time() ),
								'class' => 'datepicker',
							) );

							pto_generate_fields( array(
								'type'  => 'textarea',
								'id'    => 'payment_notes',
								'label' => __( 'Payment Notes', 'projectopia-core' ),
							) );

							do_action( 'pto_invoice_payemnt_fields_end', $post->ID );

							echo '<button class="piaBtn btn btn-primary btn-block mt-0" id="mark_paid" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Add Payment', 'projectopia-core' ) . '</button>';

							?>
						</div>
					</div>
				</div>
				<?php
			}

			if ( current_user_can( 'cqpim_send_invoices' ) ) {
				echo '<button class="piaBtn btn btn-primary btn-block mt-2" id="send_invoice" data-type="send" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Send Invoice', 'projectopia-core' ) . '</button>';
			}
		}

		if ( $invoice_sent ) {
			$invoice_sent = $invoice_details['sent_details'];
			$to = isset( $invoice_sent['to'] ) ? $invoice_sent['to'] : '';
			$by = isset( $invoice_sent['by'] ) ? $invoice_sent['by'] : '';
			$at = isset( $invoice_sent['date'] ) ? $invoice_sent['date'] : '';
			$timezone = wp_timezone();
			if ( is_numeric( $at ) ) {
				$at = wp_date( get_option( 'cqpim_date_format' ). ' H:i', $at, $timezone );
			}
			echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
			/* translators: %1$s: Sent to Name, %2$s: Timestamp, %3$s: Sent By author */
			printf( esc_html__( 'The invoice was sent to %1$s on %2$s by %3$s', 'projectopia-core' ), esc_html( $to ), esc_html( $at ), wp_kses_post( $by ) );
			echo '</div>';
			if ( current_user_can( 'cqpim_send_invoices' ) ) {
				echo '<button class="piaBtn btn btn-primary btn-block" id="send_invoice" data-type="resend" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Resend Invoice', 'projectopia-core') . '</button>';
				if ( ! $paid ) {
					if ( $due ) {
						if ( $now > $due ) {
							echo '<button class="send_reminder piaBtn btn btn-primary mt-2 btn-block" data-type="overdue" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Send Overdue Email', 'projectopia-core') . '</button>';     
						} else {
							echo '<button class="send_reminder piaBtn btn btn-primary btn-block mt-2" data-type="reminder" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Send Reminder Email', 'projectopia-core') . '</button>';                           
						}
					} 
				}
			}
			if ( current_user_can( 'cqpim_mark_invoice_paid' ) ) {
				echo '<button class="piaBtn btn btn-primary btn-block mt-2" id="mark_paid_trigger" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Add Payment', 'projectopia-core' ) . '</button>'; ?>
				<div id="invoice_payment_container" style="display: none;">
					<div id="invoice_payment">
						<div style="padding: 12px;">
							<h3><?php esc_html_e( 'Add Payment', 'projectopia-core' ); ?></h3>
							<?php

							pto_generate_fields( array(
								'id'      => 'payment_amount',
								'label'   => __( 'Payment Amount', 'projectopia-core' ),
								'prepend' => get_option( 'currency_symbol' ),
							) );

							pto_generate_fields( array(
								'id'    => 'payment_date',
								'label' => __( 'Payment Date', 'projectopia-core' ),
								'value' => wp_date( get_option( 'cqpim_date_format' ), time() ),
								'class' => 'datepicker',
							) );

							pto_generate_fields( array(
								'type'  => 'textarea',
								'id'    => 'payment_notes',
								'label' => __( 'Payment Notes', 'projectopia-core' ),
							) );

							do_action( 'pto_invoice_payemnt_fields_end', $post->ID );

							echo '<button class="piaBtn btn btn-primary btn-block mt-0" id="mark_paid" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Add Payment', 'projectopia-core' ) . '</button>';

							?>
						</div>
					</div>
				</div>
				<?php
			}
		}
	} else {
		$invoice_paid = $invoice_details['paid_details'];
		$by = isset( $invoice_paid['by'] ) ? $invoice_paid['by'] : '';
		$at = isset( $invoice_paid['date'] ) ? $invoice_paid['date'] : '';
		if ( is_numeric( $at ) ) {
			$at = wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $at );
		}
		echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
		/* translators: %1$s: Mak Paid By author, %2$s: Timestamp */
		printf( esc_html__( 'The invoice was marked as paid by %1$s at %2$s', 'projectopia-core' ), wp_kses_post( apply_filters( 'pto_invoice_payment_method_title', $by ) ), esc_html( $at ) );
		echo '</div>';
	}

	if ( current_user_can( 'publish_cqpim_invoices' ) ) {
		echo '<div id="messages"></div>';
	}
}

add_action( 'save_post_cqpim_invoice', 'save_pto_invoice_client_project_metabox_data' );
function save_pto_invoice_client_project_metabox_data( $post_id ) {
	if ( ! isset( $_POST['invoice_client_project_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['invoice_client_project_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'invoice_client_project_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( isset( $_POST['invoice_number'] ) ) {
		$invoice_id = sanitize_text_field( wp_unslash( $_POST['invoice_number'] ) );
		update_post_meta( $post_id, 'invoice_id', $invoice_id );
	}

	if ( isset( $_POST['invoice_project'] ) ) {
		$project_id = sanitize_text_field( wp_unslash( $_POST['invoice_project'] ) );
		$invoice_details = get_post_meta( $post_id, 'invoice_details', true );
		$invoice_details = $invoice_details && is_array( $invoice_details ) ? $invoice_details : array();
		$invoice_details['project_id'] = $project_id;
		update_post_meta( $post_id, 'invoice_details', $invoice_details );
		update_post_meta( $post_id, 'invoice_project', $project_id );
	}
	
	$invoice_details = get_post_meta( $post_id, 'invoice_details', true );
	$invoice_details = $invoice_details && is_array( $invoice_details ) ? $invoice_details : array();
	$invoice_details['allow_partial'] = isset( $_POST['allow_partial'] ) ? sanitize_text_field( wp_unslash( $_POST['allow_partial'] ) ) : 0;
	update_post_meta( $post_id, 'invoice_details', $invoice_details );

	// Update the invoice layout meta.
	$invoice_details['invoice_layout_rtl'] = 0;
	if ( ! empty( $_POST['invoice_layout_rtl'] ) ) {
		$invoice_details['invoice_layout_rtl'] = 1;
		update_post_meta( $post_id, 'invoice_details', $invoice_details );
	}
	else {
		update_post_meta( $post_id, 'invoice_details', $invoice_details );
	}

	if ( isset( $_POST['client_contact'] ) ) {
		$client_contact = sanitize_text_field( wp_unslash( $_POST['client_contact'] ) );
		update_post_meta( $post_id, 'client_contact', $client_contact );
	}

	if ( isset( $_POST['invoice_client'] ) ) {
		$client_id = sanitize_text_field( wp_unslash( $_POST['invoice_client'] ) );
		$invoice_details = get_post_meta( $post_id, 'invoice_details', true );
		$invoice_details = $invoice_details && is_array( $invoice_details ) ? $invoice_details : array();
		$invoice_details['client_id'] = $client_id;
		update_post_meta( $post_id, 'invoice_details', $invoice_details );
		update_post_meta( $post_id, 'invoice_client', $client_id );   
	}

	$currency = get_option('currency_symbol');
	$currency_code = get_option('currency_code');
	$currency_position = get_option('currency_symbol_position');
	$currency_space = get_option('currency_symbol_space'); 
	$client_currency = get_post_meta($client_id, 'currency_symbol', true);
	$client_currency_code = get_post_meta($client_id, 'currency_code', true);
	$client_currency_space = get_post_meta($client_id, 'currency_space', true);     
	$client_currency_position = get_post_meta($client_id, 'currency_position', true);
	$quote_currency = isset($_POST['currency_symbol']) ? sanitize_text_field( wp_unslash( $_POST['currency_symbol'] ) ) : '';
	$quote_currency_code = isset($_POST['currency_code']) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : '';
	$quote_currency_space = isset($_POST['currency_space']) ? sanitize_text_field( wp_unslash( $_POST['currency_space'] ) ) : '';
	$quote_currency_position = isset($_POST['currency_position']) ? sanitize_text_field( wp_unslash( $_POST['currency_position'] ) ) : '';
	if ( ! empty($quote_currency) ) {
		update_post_meta($post_id, 'currency_symbol', $quote_currency);
	} else {
		if ( ! empty($client_currency) ) {
			update_post_meta($post_id, 'currency_symbol', $client_currency);
		} else {
			update_post_meta($post_id, 'currency_symbol', $currency);
		}
	}
	if ( ! empty($quote_currency_code) ) {
		update_post_meta($post_id, 'currency_code', $quote_currency_code);
	} else {
		if ( ! empty($client_currency_code) ) {
			update_post_meta($post_id, 'currency_code', $client_currency_code);
		} else {
			update_post_meta($post_id, 'currency_code', $currency_code);
		}
	}
	if ( ! empty($quote_currency_space) ) {
		update_post_meta($post_id, 'currency_space', $quote_currency_space);
	} else {
		if ( ! empty($client_currency_space) ) {
			update_post_meta($post_id, 'currency_space', $client_currency_space);
		} else {
			update_post_meta($post_id, 'currency_space', $currency_space);
		}
	}
	if ( ! empty($quote_currency_position) ) {
		update_post_meta($post_id, 'currency_position', $quote_currency_position);
	} else {
		if ( ! empty($client_currency_position) ) {
			update_post_meta($post_id, 'currency_position', $client_currency_position);
		} else {
			update_post_meta($post_id, 'currency_position', $currency_position);
		}
	}
	if ( isset($_POST['invoice_date']) ) {
		$invoice_details = get_post_meta($post_id, 'invoice_details', true);
		$invoice_details = $invoice_details && is_array($invoice_details) ? $invoice_details : array();
		$client_id = isset($invoice_details['client_id']) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
		$submitted = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['invoice_date'] ) ) );
		if ( $client_terms ) {
			$terms = $client_terms;
		} else {
			$terms = get_option('company_invoice_terms');
		}
		if ( $terms == 1 ) {
			$invoice_details['on_receipt'] = true;
		}
		$invoice_details['invoice_date'] = $submitted;
		if ( empty($invoice_details['custom_terms']) ) {
			$invoice_details['terms_over'] = strtotime('+' . $terms . ' days', $submitted);
		}
		update_post_meta($post_id, 'invoice_details', $invoice_details);
	}
	$tax_app = get_post_meta($post_id, 'tax_set', true);
	if ( empty($tax_app) ) {
		$client_details = get_post_meta($client_id, 'client_details', true);
		$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
		$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : '';
		$system_tax = get_option('sales_tax_rate');
		$system_stax = get_option('secondary_sales_tax_rate');
		if ( ! empty($system_tax) && empty($client_tax) ) {
			update_post_meta($post_id, 'tax_applicable', 1);
			update_post_meta($post_id, 'tax_set', 1);   
			update_post_meta($post_id, 'tax_rate', $system_tax);    
			if ( ! empty($system_stax) && empty($client_stax) ) {
				update_post_meta($post_id, 'stax_applicable', 1);
				update_post_meta($post_id, 'stax_set', 1);  
				update_post_meta($post_id, 'stax_rate', $system_stax);          
			} else {
				update_post_meta($post_id, 'stax_applicable', 0);
				update_post_meta($post_id, 'stax_set', 1);
				update_post_meta($post_id, 'stax_rate', 0);             
			}
		} else {
			update_post_meta($post_id, 'tax_applicable', 0);
			update_post_meta($post_id, 'tax_set', 1);
			update_post_meta($post_id, 'tax_rate', 0);          
		}
	}
	remove_action( 'save_post_cqpim_invoice', 'save_pto_invoice_client_project_metabox_data' );
	wp_update_post( array(
		'ID'        => $post_id,
		'post_name' => $invoice_id,
	));
	add_action( 'save_post_cqpim_invoice', 'save_pto_invoice_client_project_metabox_data' );
}