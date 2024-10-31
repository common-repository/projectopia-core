<?php
function pto_invoice_items_metabox_callback( $post ) {
 	wp_nonce_field( 'invoice_items_metabox', 'invoice_items_metabox_nonce' ); 

	$line_items = get_post_meta( $post->ID, 'line_items', true ); 
	$client = get_post_meta( $post->ID, 'invoice_client', true );
	$tax_name = get_option( 'sales_tax_name');
	$stax_name = get_option( 'secondary_sales_tax_name' );
	$tax_applicable = get_post_meta( $post->ID, 'tax_applicable', true );
	$stax_applicable = get_post_meta( $post->ID, 'stax_applicable', true );
	if ( ! empty( $client ) ) { ?>
		<div class="card p-0 m-0 repeater">
			<table class="p-0 piaTableData table-responsive-lg table table-bordered w-100">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Qty', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Description', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Price', 'projectopia-core' ); ?> ( <?php echo esc_html( pto_get_currency_symbol( $post->ID ) ); ?> )</th>
						<th><?php esc_html_e( 'Total', 'projectopia-core' ); ?> ( <?php echo esc_html( pto_get_currency_symbol( $post->ID ) ); ?> )</th>
						<?php if ( ! empty( $tax_applicable ) ) { ?>
							<th><?php 
								/* translators: %s: Tax Name */
								printf( esc_html__( 'Ex. %s', 'projectopia-core' ), esc_html( $tax_name ) ); ?></th>
						<?php } ?>
						<?php if ( ! empty( $stax_applicable ) ) { ?>
							<th><?php 
								/* translators: %s: Secondary Tax Name */
								printf( esc_html__( 'Ex. %s', 'projectopia-core' ), esc_html( $stax_name ) ); ?></th>
						<?php } ?>
						<th><?php esc_html_e( 'Action', 'projectopia-core' ); ?></th>
					</tr>
				</thead>
				<tbody data-repeater-list="group-a">
					<?php if ( ! empty( $line_items ) ) {
						foreach ( $line_items as $key => $item ) { ?>
							<tr class="line_item" data-repeater-item>							
								<td><div class="form-group"><div class="input-group"><input data-row="<?php echo esc_attr( $key ); ?>" id="invoice_qty_<?php echo esc_attr( $key ); ?>" class="invoice_qty form-control input" type="text" name="qty" value="<?php echo esc_attr( $item['qty'] ); ?>" placeholder="<?php esc_attr_e('Quantity', 'projectopia-core'); ?>" required /></div></div></td>
								<td><div class="form-group"><div class="input-group"><input data-row="<?php echo esc_attr( $key ); ?>" id="invoice_desc_<?php echo esc_attr( $key ); ?>" class="invoice_desc form-control input" type="text" name="desc" value="<?php echo esc_attr( $item['desc'] ); ?>" placeholder="<?php esc_attr_e('Description', 'projectopia-core'); ?>" required /></div></div></td>
								<td><div class="form-group"><div class="input-group"><input data-row="<?php echo esc_attr( $key ); ?>" id="invoice_price_<?php echo esc_attr( $key ); ?>" class="invoice_price form-control input" type="text" name="price" value="<?php echo esc_attr( $item['price'] ); ?>" placeholder="<?php esc_attr_e('Price', 'projectopia-core'); ?>" required /></div></div></td>
								<td><div class="form-group"><div class="input-group"><input data-row="<?php echo esc_attr( $key ); ?>" id="invoice_line_total_<?php echo esc_attr( $key ); ?>" class="invoice_line_total form-control input" type="text" name="line_total" value="<?php echo esc_attr( $item['sub'] ); ?>" placeholder="<?php esc_attr_e('Subtotal', 'projectopia-core'); ?>" readonly /></div></div></td>
								<?php if ( ! empty( $tax_applicable ) ) { ?>
									<td><input data-row="<?php echo esc_attr( $key ); ?>" id="invoice_line_tax_<?php echo esc_attr( $key ); ?>" name="line_tax" class="line_tax" value="1" style="margin:auto" type="checkbox" <?php if ( ! empty($item['tax_ex']) && $item['tax_ex'] == 1 ) { echo 'checked="checked"'; } ?> /></td>
								<?php } ?>
								<?php if ( ! empty( $stax_applicable ) ) { ?>
									<td><input data-row="<?php echo esc_attr( $key ); ?>" id="invoice_line_stax_<?php echo esc_attr( $key ); ?>" name="line_stax" class="line_stax" value="1" style="margin:auto" type="checkbox" <?php if ( ! empty($item['stax_ex']) && $item['stax_ex'] == 1 ) { echo 'checked="checked"'; } ?> /></td>		
								<?php } ?>
								<td><input data-row="<?php echo esc_attr( $key ); ?>" class="line_delete cqpim_button cqpim_small_button bg-red rounded_2 border-red op" data-repeater-delete type="button" value=""/></td>
							</tr>
						<?php }
					} else { ?>
						<tr class="line_item" data-repeater-item>
							<td><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_qty" class="invoice_qty form-control input" type="text" name="qty" value="" placeholder="<?php esc_attr_e('Quantity', 'projectopia-core'); ?>" required /></div></div></td>
							<td><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_desc" class="invoice_desc form-control input" type="text" name="desc" value="" placeholder="<?php esc_attr_e('Description', 'projectopia-core'); ?>" required /></div></div></td>
							<td><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_price" class="invoice_price form-control input" type="text" name="price" value="" placeholder="<?php esc_attr_e('Price', 'projectopia-core'); ?>" required /></div></div></td>
							<td><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_line_total" class="invoice_line_total form-control input" type="text" name="line_total" value="" placeholder="<?php esc_attr_e('Subtotal', 'projectopia-core'); ?>" readonly required /></div></div></td>
							<?php if ( ! empty( $tax_applicable ) ) { ?>
								<td><input data-row="0" id="invoice_line_tax" name="line_tax" class="line_tax" style="margin:auto" type="checkbox" value="1" /></td>
							<?php } ?>
							<?php if ( ! empty( $stax_applicable ) ) { ?>
								<td><input data-row="0" id="invoice_line_stax" name="line_stax" class="line_stax" style="margin:auto" type="checkbox" value="1" /></td>		
							<?php } ?>
							<td><input data-row="0" class="line_delete cqpim_button cqpim_small_button bg-red rounded_2 border-red op" data-repeater-delete type="button" value=""/></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<input class="add_line_item_row cqpim_button bg-green rounded_2 border-green op" data-repeater-create type="button" />
		</div>
		<hr>
		<p class="underline"><?php esc_html_e('Invoice Totals', 'projectopia-core'); ?></p>
		<div class="totals">
			<?php 
			$vat_rate = get_post_meta( $post->ID, 'tax_rate', true );
			$vat_active = get_option( 'sales_tax_rate' );
			$svat_active = get_option( 'secondary_sales_tax_rate' );
			if ( ! empty( $vat_rate ) || pto_is_edit_page( 'new' ) && ! empty( $vat_active ) ) {
				$subtotal = __( 'Subtotal', 'projectopia-core' );
			} else {
				$subtotal = __( 'Total', 'projectopia-core' );
			}
			$totals = get_post_meta( $post->ID, 'invoice_totals', true );
			$sub = isset( $totals['sub'] ) ? $totals['sub'] : 0;
			$vat = isset( $totals['tax'] ) ? $totals['tax'] : 0;
			$svat = isset( $totals['stax'] ) ? $totals['stax'] : 0;
			$total = isset( $totals['total'] ) ? $totals['total'] : 0;

			pto_generate_fields( array(
				'id'    => 'invoice_subtotal',
				'label' => $subtotal . sprintf( ' ( %s )', pto_get_currency_symbol( $post->ID ) ),
				'value' => $sub,
				'class' => 'total_fields',
			) );

			pto_generate_fields( array(
				'type'  => 'hidden',
				'id'    => 'stax_rate',
				'value' => isset( $svat_active ) ? $svat_active : 0,
			) );

			pto_generate_fields( array(
				'type'  => 'hidden',
				'id'    => 'tax_rate',
				'value' => isset( $vat_active ) ? $vat_active : 0,
			) );

			if ( ! empty( $vat_rate ) || pto_is_edit_page( 'new' ) && ! empty( $vat_active ) ) {
				$tax_name = get_option( 'sales_tax_name' );
				$stax_name = get_option( 'secondary_sales_tax_name' );
				$tax_applicable = get_post_meta( $post->ID, 'tax_applicable', true );
				$stax_applicable = get_post_meta( $post->ID, 'stax_applicable', true );
				if ( ! empty( $tax_applicable ) ) {
					pto_generate_fields( array(
						'id'    => 'invoice_vat',
						'label' => $tax_name . sprintf( ' ( %s )', pto_get_currency_symbol( $post->ID ) ),
						'value' => $vat,
						'class' => 'total_fields',
					) );
				}

				if ( ! empty( $stax_applicable ) ) {
					pto_generate_fields( array(
						'id'    => 'invoice_svat',
						'label' => $stax_name . sprintf( ' ( %s )', pto_get_currency_symbol( $post->ID ) ),
						'value' => $svat,
						'class' => 'total_fields',
					) );
				}

				pto_generate_fields( array(
					'id'    => 'invoice_total',
					/* translators: %s: Currency Symbol */
					'label' => sprintf( __( 'Total ( %s )', 'projectopia-core' ), pto_get_currency_symbol( $post->ID ) ),
					'value' => $total,
					'class' => 'total_fields',
				) );
				
			} else {
				$total = $sub;
			}

			$received = 0;
			$payments = get_post_meta( $post->ID, 'invoice_payments', true );
			if ( empty( $payments ) ) {
				$payments = array();
			}
			foreach ( $payments as $payment ) {
				$amount = ( isset( $payment['amount'] ) && ! empty( $payment['amount'] ) ) ? $payment['amount'] : 0;
				$received = $received + (float) $amount;
			}
			$outstanding = $total - $received;

			pto_generate_fields( array(
				'id'        => 'payments_received',
				/* translators: %s: Currency Symbol */
				'label'     => sprintf( __( 'Payments / Deductions ( %s )', 'projectopia-core' ), pto_get_currency_symbol( $post->ID ) ),
				'value'     => number_format( (float) $received, 2, '.', '' ),
				'class'     => 'total_fields',
				'readonly'  => true,
				'row_start' => true,
				'col'       => true,
			) );

			pto_generate_fields( array(
				'id'       => 'total_outstanding',
				/* translators: %s: Currency Symbol */
				'label'    => sprintf( __( 'Total Outstanding ( %s )', 'projectopia-core' ), pto_get_currency_symbol( $post->ID ) ),
				'value'    => number_format( (float) $outstanding, 2, '.', '' ),
				'class'    => 'total_fields',
				'readonly' => true,
				'row_end'  => true,
				'col'      => true,
			) );

			?>
		</div>
	<?php } else { ?>
		<?php esc_html_e( 'You must select a client before you can add line items.', 'projectopia-core' ); ?>
	<?php }
}

add_action( 'save_post_cqpim_invoice', 'save_pto_invoice_items_metabox_data' );
function save_pto_invoice_items_metabox_data( $post_id ) {
	if ( ! isset( $_POST['invoice_items_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['invoice_items_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'invoice_items_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$line_items = array();
	if ( isset( $_POST['group-a'] ) ) {
		$items_to_add = pto_sanitize_rec_array( wp_unslash( $_POST['group-a'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$i = 0;
		foreach ( $items_to_add as $item ) {
			$line_items[] = array(
				'qty'     => $item['qty'],
				'desc'    => $item['desc'],
				'price'   => $item['price'],
				'sub'     => $item['line_total'],
				'tax_ex'  => isset( $item['line_tax'][0] ) ? $item['line_tax'][0] : 0,
				'stax_ex' => isset( $item['line_stax'][0]) ? $item['line_stax'][0] : 0,
			);
			$i++;
		}
		update_post_meta( $post_id, 'line_items', $line_items );

		$sub = isset( $_POST['invoice_subtotal'] ) ? sanitize_text_field( wp_unslash( $_POST['invoice_subtotal'] ) ) : '';
		$tax_rate = get_option( 'sales_tax_rate' );
		$invoice_details = get_post_meta( $post_id, 'invoice_details', true );
		$client_id = isset( $invoice_details['client_id'] ) ? $invoice_details['client_id'] : '';
		$client_details = get_post_meta( $client_id, 'client_details', true );
		$client_tax = isset( $client_details['tax_disabled'] ) ? $client_details['tax_disabled'] : '';
		$client_stax = isset( $client_details['stax_disabled'] ) ? $client_details['stax_disabled'] : '';
		
 		if ( ! empty( $tax_rate ) && empty( $client_tax ) ) {
			$tax = 0;
			foreach ( $items_to_add as $item ) {
				if ( empty( $item['line_tax'][0] ) ) {
					$amount = $item['line_total'];
					$tax_amount = $amount / 100 * $tax_rate;
					$tax = $tax + $tax_amount;
				}
			}
			$stax_rate = get_option( 'secondary_sales_tax_rate' );
			if ( ! empty($stax_rate) && empty($client_stax) ) {
				$stax = 0;
				foreach ( $items_to_add as $item ) {
					if ( empty($item['line_stax'][0]) ) {
						$amount = $item['line_total'];
						$stax_amount = $amount / 100 * $stax_rate;
						$stax = $stax + $stax_amount;
					}
				}
				$total = $sub + $tax + $stax;
			} else {
				$stax = 0;
				$total = $sub + $tax;
			}
		} else {
			$tax = 0;
			$stax = 0;
			$total = $sub;
		}
		$invoice_totals = array(
			'sub'   => number_format( (float)$sub, 2, '.', ''),
			'tax'   => number_format( (float)$tax, 2, '.', ''),
			'stax'  => number_format( (float)$stax, 2, '.', ''),
			'total' => number_format( (float)$total, 2, '.', ''),
		);
		update_post_meta($post_id, 'invoice_totals', $invoice_totals);
	} else {
		delete_post_meta($post_id, 'line_items');
		delete_post_meta($post_id, 'invoice_totals');
	}
}