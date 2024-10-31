<?php
function pto_quote_client_metabox_callback( $post ) {
	wp_nonce_field( 'quote_client_metabox', 'quote_client_metabox_nonce' );

	$quote_details = get_post_meta( $post->ID, 'quote_details', true );
	$quote_type = isset( $quote_details['quote_type'] ) ? $quote_details['quote_type'] : '';
	$quote_ref = isset( $quote_details['quote_ref'] ) ? $quote_details['quote_ref'] : '';
	$start_date = isset( $quote_details['start_date'] ) ? $quote_details['start_date'] : '';
	$finish_date = isset( $quote_details['finish_date'] ) ? $quote_details['finish_date'] : '';
	$client_id = isset( $quote_details['client_id'] ) ? $quote_details['client_id'] : '';
	$client_contact = isset( $quote_details['client_contact'] ) ? $quote_details['client_contact'] : '';
	$deposit_amount = isset( $quote_details['deposit_amount'] ) ? $quote_details['deposit_amount'] : '';
	$client_details = get_post_meta( $client_id, 'client_details', true );
	$client_contacts = get_post_meta( $client_id, 'client_contacts', true );
	if ( ! empty( $client_contact ) ) {
		if ( $client_details['user_id'] == $client_contact ) {
			$client_contact_name = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
			$client_company_name = isset( $client_details['client_company'] ) ? $client_details['client_company'] : '';
			$client_telephone = isset( $client_details['client_telephone'] ) ? $client_details['client_telephone'] : '';
			$client_email = isset( $client_details['client_email'] ) ? $client_details['client_email'] : '';
		} else {
			$client_contact_name = isset( $client_contacts[ $client_contact ]['name'] ) ? $client_contacts[ $client_contact ]['name'] : '';
			$client_company_name = isset( $client_details['client_company'] ) ? $client_details['client_company'] : '';
			$client_telephone = isset( $client_contacts[ $client_contact ]['telephone'] ) ? $client_contacts[ $client_contact ]['telephone'] : '';
			$client_email = isset( $client_contacts[ $client_contact ]['email'] ) ? $client_contacts[ $client_contact ]['email'] : '';        
		}
	} else {
		$client_contact_name = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
		$client_company_name = isset( $client_details['client_company'] ) ? $client_details['client_company'] : '';
		$client_telephone = isset( $client_details['client_telephone'] ) ? $client_details['client_telephone'] : '';
		$client_email = isset( $client_details['client_email'] ) ? $client_details['client_email'] : '';      
	}
	if ( ! $quote_type ) {  
		echo '<p>' . esc_html__( 'This section is currently unavailable', 'projectopia-core' ) . '</p>';
	} else { ?>
		<p><strong><?php esc_html_e( 'Client Details', 'projectopia-core' ); ?></strong></p>
		<p><?php echo '<strong>' . esc_html__('Company Name:', 'projectopia-core') . ' </strong>' . esc_html( $client_company_name ) . '<br />
		<strong>' . esc_html__('Contact Name:', 'projectopia-core') . '</strong> ' . esc_html( $client_contact_name ) . '<br />
		<strong>' . esc_html__('Email:', 'projectopia-core') . ' </strong><a href="mailto:' . esc_attr( $client_email ) . '">' . esc_html( $client_email ) . '</a>
		<br /><strong>' . esc_html__('Phone:', 'projectopia-core') . ' </strong>' . esc_html( $client_telephone ) . '</p>';
		?><hr>
		<p><strong><?php esc_html_e( 'Quote Details', 'projectopia-core' ); ?></strong></p>
		<p>
			<strong><?php esc_html_e('Start Date:', 'projectopia-core'); ?> </strong><?php if ( is_numeric($start_date) ) { echo esc_html( wp_date(get_option('cqpim_date_format'), $start_date) ); } else { echo esc_html( $start_date ); } ?><br />
			<strong><?php esc_html_e('Deadline:', 'projectopia-core'); ?> </strong><?php if ( is_numeric($finish_date) ) { echo esc_html( wp_date(get_option('cqpim_date_format'), $finish_date) ); } else { echo esc_html( $finish_date ); } ?><br />
			<strong><?php esc_html_e('Deposit Required:', 'projectopia-core'); ?> </strong><?php if ( ! $deposit_amount || $deposit_amount == 'none' ) { esc_html_e('Not Required', 'projectopia-core'); } else { echo esc_html( $deposit_amount ) . '%'; } ?>
		</p>
		<?php if ( current_user_can( 'publish_cqpim_quotes' ) ) {
			/* translators: %s: Quote Type */ ?>
			<button id="edit-quote-details" class="piaBtn btn btn-primary btn-block mt-2"><?php printf( esc_html__( 'Edit %s Details', 'projectopia-core' ), $quote_type == 'estimate' ? esc_html__( 'Estimate', 'projectopia-core' ) : esc_html__( 'Quote', 'projectopia-core' ) ); ?></button>
		<?php } ?>
		<div class="clear separator"></div>			
	<?php } ?>
	<div id="quote_basics_container" style="display: none;">
		<div id="quote_basics">
			<div style="padding: 12px;">
				<?php if ( ! $quote_type ) { ?>
				<h3><?php esc_html_e( 'Quote / Estimate Basics', 'projectopia-core' ); ?></h3>
				<p><?php esc_html_e( 'These initial questions will help you to get your Quote / Estimate set up properly', 'projectopia-core' ); ?></p>
				<?php } else { ?>
				<h3><?php 
					/* translators: %s: Quote Type */
					printf( esc_html__( '%s Details', 'projectopia-core' ), ( $quote_type == 'quote' ) ? esc_html__( 'Quote', 'projectopia-core' ) : esc_html__( 'Estimate', 'projectopia-core' ) ); ?></h3>
				<?php }

				pto_generate_fields( array(
					'type'      => 'select',
					'id'        => 'quote_type',
					'label'     => esc_html__( 'Quote or Estimate?', 'projectopia-core' ),
					'value'     => $quote_type,
					'options'   => array(
						'quote'    => esc_html__( 'Quote', 'projectopia-core' ),
						'estimate' => esc_html__( 'Estimate', 'projectopia-core' ),
					),
					'default'   => true,
					'row_start' => true,
					'col'       => true,
				) );

				pto_generate_fields( array(
					'id'      => 'quote_ref',
					'label'   => esc_html__( 'Quote / Estimate Ref:', 'projectopia-core' ),
					'value'   => ! empty( $quote_ref ) ? $quote_ref : $post->ID,
					'row_end' => true,
					'col'     => true,
				) );

				$args = array(
					'post_type'      => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$clients = get_posts( $args );
		
				$options = [];
				foreach ( $clients as $client ) {
					$client_details_new = get_post_meta( $client->ID, 'client_details', true );
					$client_contact_name_new = isset( $client_details_new['client_contact'] ) ? $client_details_new['client_contact'] : '';
					$client_company_name_new = isset( $client_details_new['client_company'] ) ? $client_details_new['client_company'] : $client_contact_name_new;
					$options[ $client->ID ] = $client_company_name_new;
				}

				pto_generate_fields( array(
					'type'    => 'select',
					'id'      => 'quote_client',
					'class'   => [ 'full-width', 'quote_client_dropdown' ],
					'label'   => esc_html__( 'Choose a Client:', 'projectopia-core' ),
					'value'   => $client_id,
					'options' => $options,
					'default' => esc_html__( 'Select a Client...', 'projectopia-core' ),
				) );

				$options = [];
				if ( ! empty( $client_details ) ) {
					/* translators: %s: Client Contact */
					$options[ $client_details['user_id'] ] = sprintf( esc_html__( '%s (Main Contact)', 'projectopia-core'), $client_details['client_contact'] );
				}

				if ( ! empty( $client_contacts ) ) {
					foreach ( $client_contacts as $contact ) {
						$options[ $contact['user_id'] ] = $contact['name'];
					}
				}

				pto_generate_fields( array(
					'type'    => 'select',
					'id'      => 'client_contact',
					'label'   => esc_html__( 'Choose a Contact:', 'projectopia-core' ),
					'value'   => $client_contact,
					'options' => $options,
					'default' => esc_html__( 'Choose a Contact...', 'projectopia-core' ),
					'class'   => 'full-width',
				) );

				pto_generate_fields( array(
					'id'        => 'start_date',
					'label'     => esc_html__( 'Proposed Start/Launch Date:', 'projectopia-core' ),
					'value'     => is_numeric( $start_date ) ? wp_date( get_option( 'cqpim_date_format' ), $start_date ) : $start_date,
					'class'     => 'datepicker',
					'row_start' => true,
					'col'       => true,
				) );

				pto_generate_fields( array(
					'id'      => 'finish_date',
					'label'   => esc_html__( 'Proposed Finished Date:', 'projectopia-core' ),
					'value'   => is_numeric( $finish_date ) ? wp_date( get_option( 'cqpim_date_format' ), $finish_date ) : $finish_date,
					'class'   => 'datepicker',
					'row_end' => true,
					'col'     => true,
				) );

				$options = [
					'none' => __( 'No Deposit Required', 'projectopia-core' ),
				];
				for ( $x = 10; $x <= 100; $x++ ) {
					$options[ $x ] = $x . '%';
					$x = $x + 9;
				}

				pto_generate_fields( array(
					'type'    => 'select',
					'id'      => 'deposit_amount',
 					'label'   => esc_html__( 'Deposit Amount:', 'projectopia-core' ),
					'value'   => $deposit_amount ? $deposit_amount : 'none',
					'options' => $options,
					'default' => esc_html__( 'Choose an Option..', 'projectopia-core' ),
					'class'   => 'full-width',
				) );

				$auto_terms = get_option( 'auto_contract' ); 
				$quote_terms = get_option( 'enable_quote_terms' ); 
				if ( $auto_terms == 1 || $quote_terms == 1 ) { 
					$contract = isset( $quote_details['default_contract_text'] ) ? $quote_details['default_contract_text'] : ''; 
					$default = get_option( 'default_contract_text' );
					
					$args = array(
						'post_type'      => 'cqpim_terms',
						'posts_per_page' => -1,
						'post_status'    => 'private',
					);
					$terms = get_posts( $args );
			
					$options = [];
					foreach ( $terms as $term ) {
						$options[ $term->ID ] = esc_html( $term->post_title  );
					}

					pto_generate_fields( array(
						'type'    => 'select',
						'id'      => 'default_contract_text',
						'label'   => esc_html__( 'Terms & Conditions Template:', 'projectopia-core' ),
						'value'   => ! empty( $contract ) ? $contract : $default,
						'options' => $options,
						'default' => esc_html__( 'Select an Option..', 'projectopia-core' ),
						'class'   => 'full-width',
					) );
				}

				?>
				<div id="basics-error"></div>
				<a id="quote-edit-cancel" class="cancel-creation mt-10 piaBtn redColor" href="<?php echo esc_url( admin_url() ); ?>edit.php?post_type=cqpim_quote"><?php esc_html_e('Cancel', 'projectopia-core'); ?></a>
				<button class="save-basics mt-10 piaBtn right"><?php esc_html_e('Save', 'projectopia-core'); ?></button>
			</div>
		</div>
	</div>		
	<?php	
}

add_action( 'save_post_cqpim_quote', 'save_pto_quote_client_metabox_data' );
function save_pto_quote_client_metabox_data( $post_id ) {
	if ( ! isset( $_POST['quote_client_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['quote_client_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'quote_client_metabox' ) ) {
	    return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$quote_details = get_post_meta( $post_id, 'quote_details', true );
	$quote_details = $quote_details && is_array( $quote_details ) ? $quote_details : array();
	if ( isset( $_POST['quote_type'] ) ) {
		$quote_details['quote_type'] = sanitize_text_field( wp_unslash( $_POST['quote_type'] ) );
	}
	if ( isset( $_POST['quote_client'] ) ) {
		$quote_client = sanitize_text_field( wp_unslash( $_POST['quote_client'] ) );
		$quote_details['client_id'] = $quote_client;
	}
	$currency = get_option( 'currency_symbol' );
	$currency_code = get_option( 'currency_code' );
	$currency_position = get_option( 'currency_symbol_position' );
	$currency_space = get_option( 'currency_symbol_space' ); 
	$client_currency = get_post_meta( $quote_client, 'currency_symbol', true );
	$client_currency_code = get_post_meta( $quote_client, 'currency_code', true );
 	$client_currency_space = get_post_meta( $quote_client, 'currency_space', true);      
	$client_currency_position = get_post_meta( $quote_client, 'currency_position', true );
	$quote_currency = isset( $_POST['currency_symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_symbol'] ) ) : '';
	$quote_currency_code = isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : '';
	$quote_currency_space = isset( $_POST['currency_space'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_space'] ) ) : '';
	$quote_currency_position = isset( $_POST['currency_position'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_position'] ) ) : '';
	
	if ( ! empty( $quote_currency ) ) {
		update_post_meta( $post_id, 'currency_symbol', $quote_currency );
	} else {
		if ( ! empty( $client_currency ) ) {
			update_post_meta( $post_id, 'currency_symbol', $client_currency );
		} else {
			update_post_meta( $post_id, 'currency_symbol', $currency );
		}
	}

	if ( ! empty( $quote_currency_code ) ) {
		update_post_meta( $post_id, 'currency_code', $quote_currency_code );
	} else {
		if ( ! empty( $client_currency_code ) ) {
			update_post_meta( $post_id, 'currency_code', $client_currency_code );
		} else {
			update_post_meta( $post_id, 'currency_code', $currency_code );
		}
	}

	if ( ! empty( $quote_currency_space ) ) {
		update_post_meta( $post_id, 'currency_space', $quote_currency_space );
	} else {
		if ( ! empty( $client_currency_space ) ) {
			update_post_meta( $post_id, 'currency_space', $client_currency_space );
		} else {
			update_post_meta( $post_id, 'currency_space', $currency_space );
		}
	}

	if ( ! empty( $quote_currency_position ) ) {
		update_post_meta( $post_id, 'currency_position', $quote_currency_position );
	} else {
		if ( ! empty( $client_currency_position ) ) {
			update_post_meta( $post_id, 'currency_position', $client_currency_position );
		} else {
			update_post_meta( $post_id, 'currency_position', $currency_position );
		}
	}

	if ( isset( $_POST['default_contract_text'] ) ) {
		$quote_details['default_contract_text'] = sanitize_textarea_field( wp_unslash( $_POST['default_contract_text'] ) );
	} else {
		$quote_details['default_contract_text'] = get_option( 'default_contract_text' );
	}

	if ( isset( $_POST['client_contact'] ) ) {
		$quote_details['client_contact'] = sanitize_text_field( wp_unslash( $_POST['client_contact'] ) );
	}

	if ( isset( $_POST['quote_ref'] ) ) {
		$quote_details['quote_ref'] = sanitize_text_field( wp_unslash( $_POST['quote_ref'] ) );
	}

	if ( isset( $_POST['start_date'] ) ) {
		$timestamp = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['start_date'] ) ) );
		$quote_details['start_date'] = $timestamp;
	}

	if ( isset( $_POST['finish_date'] ) ) {
		$timestamp = pto_convert_date( sanitize_text_field( wp_unslash( $_POST['finish_date'] ) ) );
		$quote_details['finish_date'] = $timestamp;
	}

	if ( isset( $_POST['deposit_amount'] ) ) {
		$quote_details['deposit_amount'] = sanitize_text_field( wp_unslash( $_POST['deposit_amount'] ) );
	}

	update_post_meta( $post_id, 'quote_details', $quote_details );
	$tax_app = get_post_meta( $post_id, 'tax_set', true );
	if ( empty( $tax_app ) ) {
		$client_details = get_post_meta( $quote_client, 'client_details', true );
		$client_tax = isset( $client_details['tax_disabled'] ) ? $client_details['tax_disabled'] : '';
		$client_stax = isset( $client_details['stax_disabled'] ) ? $client_details['stax_disabled'] : '';
		$system_tax = get_option( 'sales_tax_rate' );
		$system_stax = get_option( 'secondary_sales_tax_rate' );
		if ( ! empty( $system_tax ) && empty( $client_tax ) ) {
			update_post_meta( $post_id, 'tax_applicable', 1 );
			update_post_meta( $post_id, 'tax_set', 1 );   
			update_post_meta( $post_id, 'tax_rate', $system_tax );    
			if ( ! empty( $system_stax ) && empty( $client_stax ) ) {
				update_post_meta( $post_id, 'stax_applicable', 1 );
				update_post_meta( $post_id, 'stax_set', 1 );  
				update_post_meta( $post_id, 'stax_rate', $system_stax );          
			} else {
				update_post_meta( $post_id, 'stax_applicable', 0 );
				update_post_meta( $post_id, 'stax_set', 1 );
				update_post_meta( $post_id, 'stax_rate', 0 );             
			}
		} else {
			update_post_meta( $post_id, 'tax_applicable', 0 );
			update_post_meta( $post_id, 'tax_set', 1 );
			update_post_meta( $post_id, 'tax_rate', 0 );          
		}
	}

	$title = get_the_title( $post_id );
	$client_token = '%%CLIENT_COMPANY%%';
	$ref_token = '%%QUOTE_REF%%';
	$type_token = '%%TYPE%%';
	$quote_details = get_post_meta( $post_id, 'quote_details', true );
	$client_id = $quote_details['client_id'];
	$type = $quote_details['quote_type'];
	$upper_type = ucfirst( $type );
	$client_details = get_post_meta( $client_id, 'client_details', true );
	$quote_ref = $quote_details['quote_ref'];
	$client_company = isset( $client_details['client_company'] ) ? $client_details['client_company'] : '';
	$title = str_replace( $client_token, $client_company, $title );
	$title = str_replace( $ref_token, $quote_ref, $title );
	$title = str_replace( $type_token, $type == 'estimate' ? esc_html__( 'Estimate', 'projectopia-core' ) : esc_html__( 'Quote', 'projectopia-core' ), $title );
	$quote_updated = array(
		'ID'         => $post_id,
		'post_title' => $title,
		'post_name'  => $post_id,
	);
	
	if ( ! wp_is_post_revision( $post_id ) ) {
		remove_action( 'save_post_cqpim_quote', 'save_pto_quote_client_metabox_data' );
		wp_update_post( $quote_updated );
		add_action( 'save_post_cqpim_quote', 'save_pto_quote_client_metabox_data' );
	}
}