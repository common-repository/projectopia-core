<?php
function pto_invoice_payments_metabox_callback( $post ) {
 	wp_nonce_field( 'invoice_payments_metabox', 'invoice_payments_metabox_nonce' ); 

	$invoice_payments = get_post_meta( $post->ID, 'invoice_payments', true );
	if ( ! empty( $invoice_payments ) ) {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable">';
		echo '<thead>';
		echo '<tr><th>' . esc_html__('Payment Date', 'projectopia-core') . '</th><th>' . esc_html__('Payment Amount', 'projectopia-core') . '</th><th>' . esc_html__('By', 'projectopia-core') . '</th><th>' . esc_html__('Notes', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach ( $invoice_payments as $key => $payment ) {
			$notes = isset( $payment['notes'] ) ? $payment['notes'] : '';
			echo '<tr>';
			echo '<td>' . esc_html( wp_date(get_option('cqpim_date_format'), $payment['date']) ) . '</td>';
			echo '<td>' . esc_html( get_option('currency_symbol') . $payment['amount'] ) . '</td>';
			echo '<td>' . esc_html( apply_filters( 'pto_invoice_payment_method_title', $payment['by'] ) ) . '</td>';
			echo '<td>' . wp_kses_post( $notes ) . '</td>';
			echo '<td>'; ?> 
				<button class="edit-milestone cqpim_button cqpim_small_button font-amber border-amber op cqpim_tooltip" value="<?php echo esc_attr( $key ); ?>" title="<?php esc_html_e('Edit Payment', 'projectopia-core'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_stage_conf cqpim_button cqpim_small_button font-red border-red op cqpim_tooltip" data-id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $key ); ?>" title="<?php esc_html_e('Delete Payment', 'projectopia-core'); ?>"><i class="fa fa-trash"></i></button>		
			<?php echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table></div>';      
	} else {
		esc_html_e( 'No payments made on this invoice', 'projectopia-core' );
	}

	if ( ! empty( $invoice_payments ) ) {
		foreach ( $invoice_payments as $key => $payment ) {
		?>
			<div id="invoice_payment_container_<?php echo esc_attr( $key ); ?>" style="display: none;">
				<div id="invoice_payment_<?php echo esc_attr( $key ); ?>" class="invoice_payment_edit">
					<div style="padding: 12px;">
						<h3><?php esc_html_e( 'Edit Payment', 'projectopia-core' ); ?></h3>
						<?php

						pto_generate_fields( array(
							'id'      => 'payment_amount_' . $key,
							'label'   => __( 'Payment Amount', 'projectopia-core' ),
							'value'   => $payment['amount'],
							'prepend' => get_option( 'currency_symbol' ),
						) );

						pto_generate_fields( array(
							'id'    => 'payment_date_' . $key,
							'label' => __( 'Payment Date', 'projectopia-core' ),
							'value' => wp_date( get_option( 'cqpim_date_format' ), $payment['date'] ),
							'class' => 'datepicker',
						) );

						pto_generate_fields( array(
							'type'  => 'textarea',
							'id'    => 'payment_notes_' . $key,
							'label' => __( 'Payment Notes', 'projectopia-core' ),
							'value' => isset( $payment['notes'] ) ? $payment['notes'] : '',
						) );

						echo '<button class="piaBtn btn btn-primary btn-block mt-0 save" id="edit_paid" data-key="' . esc_attr( $key ) . '" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Save Payment', 'projectopia-core' ) . '</button><div style="display: none;" class="ajax_spinner"></div>';

						?>
					</div>
				</div>
			</div>		
		<?php
		}
	}
}