<?php
function pto_invoice_escrow_metabox_callback( $post ) {
 	wp_nonce_field( 'invoice_escrow_metabox', 'invoice_escrow_metabox_nonce' );

	$escrow_transaction = get_post_meta( $post->ID, 'escrow_transaction', true ); ?>
	<?php if ( ! empty( $escrow_transaction ) ) { ?>
		<?php $schedule = $escrow_transaction->items[0]->schedule[0]; ?>
		<?php $items = $escrow_transaction->items[0]; ?>
		<?php $fees = $escrow_transaction->items[0]->fees[0]; ?>
		<table class="cqpim_table">
			<tr>
				<th><?php esc_html_e('ID', 'projectopia-core'); ?></th>
				<td><?php echo isset($escrow_transaction->id) ? esc_html( $escrow_transaction->id ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Creation Date', 'projectopia-core'); ?></th>
				<td><?php echo isset($escrow_transaction->creation_date) ? esc_html( $escrow_transaction->creation_date ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Title', 'projectopia-core'); ?></th>
				<td><?php echo isset($items->title) ? esc_html( $items->title ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Description', 'projectopia-core'); ?></th>
				<td><?php echo isset($escrow_transaction->description) ? esc_html( $escrow_transaction->description ) : 0; ?></td>
			</tr>
			<tr>

				<th><?php esc_html_e('Beneficiary Customer', 'projectopia-core'); ?></th>
				<td><?php echo isset($schedule->beneficiary_customer) ? esc_html( $schedule->beneficiary_customer ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Payer Customer', 'projectopia-core'); ?></th>
				<td><?php echo isset($schedule->payer_customer) ? esc_html( $schedule->payer_customer ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Type', 'projectopia-core'); ?></th>
				<td><?php echo isset($items->type) ? esc_html( $items->type ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Amount', 'projectopia-core'); ?></th>
				<td><?php echo isset($schedule->amount) ? esc_html( $schedule->amount ) : 0; ?></td>
			</tr>	
			<tr>
				<th><?php esc_html_e('Fees', 'projectopia-core'); ?></th>
				<td><?php echo isset($fees->amount) ? esc_html( $fees->amount ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Fees Payer', 'projectopia-core'); ?></th>
 				<td><?php echo isset($fees->payer_customer) ? esc_html( $fees->payer_customer ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Currency', 'projectopia-core'); ?></th>
				<td><?php echo isset($escrow_transaction->currency) ? esc_html( $escrow_transaction->currency ) : 0; ?></td>
			</tr>	
			<tr>
				<th><?php esc_html_e('Inspection Period', 'projectopia-core'); ?></th>
				<td><?php echo isset($items->inspection_period) ? esc_html( $items->inspection_period ) : 0; ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Status', 'projectopia-core'); ?></th>
				<td>
					<?php foreach ( $items->status as $key => $value ) { 
						$yes = '<span class="font-green">' . esc_html__('Yes', 'projectopia-core') . '</span>';
						$no = '<span class="font-red">' . esc_html__('No', 'projectopia-core') . '</span>';
						$key = str_replace('_', ' ', $key); ?>
						<strong><?php echo esc_html( ucwords($key) ); ?>:</strong> <?php echo ! empty($value) ? esc_html( $yes ) : esc_html( $no ); ?><br />
					<?php } ?>
				</td>
			</tr>			
		</table>
	<?php } else { ?>
		<p><?php esc_html_e('There are no Escrow Transactions available.', 'projectopia-core'); ?></p>
	<?php } ?>
	<div class="clear"></div>
	<?php if ( current_user_can('cqpim_create_escrow') && empty($escrow_transaction->id) ) { ?>
		<a id="create_escrow_trigger" class="cqpim_button font-blue border-blue op right mt-20" href="#"><?php esc_html_e('Create Escrow Transaction', 'projectopia-core'); ?></a>
	<?php } ?>
	<div class="clear"></div>
	<div id="escrow_return_messages"></div>
<?php }