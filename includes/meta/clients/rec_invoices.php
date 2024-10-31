<?php
function pto_client_recinvoices_metabox_callback( $post ) {
 	wp_nonce_field( 'client_recinvoices_metabox', 'client_recinvoices_metabox_nonce' ); ?>

	<p><?php esc_html_e( 'Recurring Invoices can be automatically created and sent on specified intervals.', 'projectopia-core' ); ?></p>
	<?php $recurring_invoices = get_post_meta( $post->ID, 'recurring_invoices', true );
	if ( empty( $recurring_invoices ) ) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__( 'No recurring invoices found...', 'projectopia-core' ) . '</div>';
	} else {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_recinvoices_table" data-sort="false" data-rows="10">';
		echo '<thead>';
		echo '<tr><th>' . esc_html__('Title', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th><th>' . esc_html__('Start', 'projectopia-core') . '</th><th>' . esc_html__('End', 'projectopia-core') . '</th><th>' . esc_html__('Frequency', 'projectopia-core') . '</th><th>' . esc_html__('Last Issue', 'projectopia-core') . '</th><th>' . esc_html__('Next Issue', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach ( $recurring_invoices as $key => $invoice ) {
			$start = isset( $invoice['start'] ) && ! empty( $invoice['start'] ) ? $invoice['start'] : __( 'N/A', 'projectopia-core' );
			if ( is_numeric( $start ) ) { 
				$start = wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $start ); 
			} 
			if ( $invoice['auto'] == 1 ) {
				$auto = '<span class="task_complete">' . __( 'Yes', 'projectopia-core' ) . '</span>';
			} else {
				$auto = '<span class="task_pending">' . __( 'No', 'projectopia-core' ) . '</span>';
			}
			if ( $invoice['status'] == 1 ) {
				$status = '<span class="status approved">' . __( 'Active', 'projectopia-core' ) . '</span>';
			} else {
				$status = '<span class="status notSent">' . __( 'Disabled', 'projectopia-core' ) . '</span>';
			}
			$end = $invoice['end'];
			if ( empty( $end ) ) {
				$end = __( 'Ongoing', 'projectopia-core' );
			}
			if ( is_numeric( $end ) ) { 
				$end = wp_date( get_option( 'cqpim_date_format') . ' H:i', $end ); 
			}

			if ( is_numeric( $invoice['next_run'] ) ) {
				$next = wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $invoice['next_run'] );
			} else {
				$next = $invoice['next_run'];
			} 
			?>
			<tr>
				<td><?php echo esc_html( $invoice['title'] ); ?></td>
				<td><?php echo wp_kses_post( $status ); ?></td>
				<td><?php echo esc_html( $start ); ?></td>
				<td><?php echo wp_kses_post( $end ); ?></td>
				<td><?php echo isset( $invoice['frequency'] ) ? esc_html( ucfirst( $invoice['frequency'] ) ) : ''; ?></td>
				<td><?php echo isset( $invoice['last_run'] ) ? esc_html( wp_date( get_option( 'cqpim_date_format' ), $invoice['last_run'] ) ) : esc_html__( 'N/A', 'projectopia-core' ); ?></td>
				<td><?php echo wp_kses_post( $next ); ?></td>
				<td><?php if ( $invoice['status'] == 1 ) { ?><button class="edit-task cqpim_button cqpim_small_button font-amber border-amber" value="<?php echo esc_attr( $key ); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <?php } ?><button class="delete_task cqpim_button cqpim_small_button font-red border-red" value="<?php echo esc_attr( $key ); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
			</tr>
		<?php }
		echo '</tbody>';
		echo '</table></div>';
		
		if ( ! empty( $recurring_invoices ) ) {
			foreach ( $recurring_invoices as $key => $invoice ) {
				$entry_id = $key;
				$start = isset( $invoice['start'] ) ? $invoice['start'] : '';
				if ( is_numeric( $start ) ) { 
					$start = wp_date( get_option( 'cqpim_date_format' ), $start );
				}
				$end = isset( $invoice['end'] ) ? $invoice['end'] : ''; 
				if ( is_numeric( $end ) ) { 
					$end = wp_date( get_option( 'cqpim_date_format' ), $end );
				} ?>
				<div style="display: none;" id="edit-recurring-invoice-container-<?php echo esc_attr( $key ); ?>">
					<div id="edit-recurring-invoice-<?php echo esc_attr( $key ); ?>" class="edit-rec-inv" style="width: 830px;">
						<div style="padding: 12px;">
							<h3><?php 
							/* translators: %s: Invoice Title */
							printf( esc_html__( 'Edit %s', 'projectopia-core' ), esc_html( $invoice['title'] ) ); ?></h3>
							<div style="float:left; width:390px;">
								<div class="form-group">
									<label for="rec-inv-title-<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Title', 'projectopia-core'); ?></label>
									<div class="input-group">
										<input type="text" id="rec-inv-title-<?php echo esc_attr( $key ); ?>" class="form-control input" value="<?php echo isset( $invoice['title'] ) ? esc_html( $invoice['title'] ) : ''; ?>" />
									</div>
								</div>
								<div class="form-group">
									<label for="client_contact_select_<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Contact', 'projectopia-core'); ?></label>
									<div class="input-group">
										<select id="client_contact_select_<?php echo esc_attr( $key ); ?>" class="form-control input">
											<option value=""><?php esc_html_e('Choose a contact...', 'projectopia-core'); ?></option>
											<?php 
											$client_details = get_post_meta($post->ID, 'client_details', true);
											echo '<option value="' . esc_attr( $client_details['user_id'] ) . '" ' . selected( $client_details['user_id'], $invoice['contact'], false ) . '>' . esc_html( $client_details['client_contact'] ) . ' ' . esc_html__('(Main contact)', 'projectopia-core') . '</option>';
											$contacts = get_post_meta($post->ID, 'client_contacts', true);
											if ( $contacts ) {
												foreach ( $contacts as $contact ) {
													echo '<option value="' . esc_attr( $contact['user_id'] ) . '" ' . selected( $contact['user_id'], $invoice['contact'], false ) . '>' . esc_html( $contact['name'] ) . '</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
								<div class="row">
								    <div class="col-6">
										<div class="form-group">
											<label for="rec-inv-start-<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Start', 'projectopia-core'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('You can specify a Start date for this invoice. If you leave the start blank then the invoice will start sending today.', 'projectopia-core' ); ?>"></i></label>
											<div class="input-group">
												<input type="text" class="form-control input datepicker" id="rec-inv-start-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $start ); ?>" placeholder="<?php esc_attr_e('Start', 'projectopia-core'); ?>" />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group">
											<label for="rec-inv-end-<?php echo esc_attr( $key ); ?>"><?php esc_html_e('End', 'projectopia-core'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('You can specify a End date for this invoice. If you leave End blank, then the invoice will send according to the frequency until you delete or disable it.', 'projectopia-core' ); ?>"></i></label>
											<div class="input-group">
												<input type="text" class="form-control input datepicker" id="rec-inv-end-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $end ); ?>" placeholder="<?php esc_attr_e('End', 'projectopia-core'); ?>" />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
								    <div class="col-6">
										<div class="form-group">
											<label for="rec-inv-frequency-<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Frequency', 'projectopia-core'); ?></label>
											<div class="input-group">
												<select id="rec-inv-frequency-<?php echo esc_attr( $key ); ?>" class="form-control input">
													<option value=""><?php esc_html_e('Choose a frequency', 'projectopia-core'); ?></option>
													<option value="daily" <?php if ( $invoice['frequency'] == 'daily' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Daily', 'projectopia-core'); ?></option>
													<option value="weekly" <?php if ( $invoice['frequency'] == 'weekly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Weekly', 'projectopia-core'); ?></option>
													<option value="biweekly" <?php if ( $invoice['frequency'] == 'biweekly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Fortnightly', 'projectopia-core'); ?></option>
													<option value="monthly" <?php if ( $invoice['frequency'] == 'monthly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Monthly', 'projectopia-core'); ?></option>
													<option value="bimonthly" <?php if ( $invoice['frequency'] == 'bimonthly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Bi Monthly', 'projectopia-core'); ?></option>
													<option value="threemonthly" <?php if ( $invoice['frequency'] == 'threemonthly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Every Three Months', 'projectopia-core'); ?></option>
													<option value="sixmonthly" <?php if ( $invoice['frequency'] == 'sixmonthly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Every Six Months', 'projectopia-core'); ?></option>
													<option value="yearly" <?php if ( $invoice['frequency'] == 'yearly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Yearly', 'projectopia-core'); ?></option>
													<option value="biyearly" <?php if ( $invoice['frequency'] == 'biyearly' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Biyearly', 'projectopia-core'); ?></option>
												</select>
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group">
											<label for="rec-inv-status-<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Status', 'projectopia-core'); ?></label>
											<div class="input-group">
												<select id="rec-inv-status-<?php echo esc_attr( $key ); ?>" class="form-control input">
													<option value="1" <?php if ( $invoice['status'] == 1 ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Active', 'projectopia-core'); ?></option>
													<option value="0" <?php if ( $invoice['status'] == 0 ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Disabled', 'projectopia-core'); ?></option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="pto-inline-item-wrapper">
									<input type="checkbox" id="rec-inv-auto-<?php echo esc_attr( $key ); ?>" <?php if ( $invoice['auto'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e('Send the recurring invoices to the client on creation.', 'projectopia-core'); ?>
									<i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('It will be send by system when your wp cron runs.', 'projectopia-core'); ?>"></i>
								</div>
								<div class="pto-inline-item-wrapper">
									<input type="checkbox" id="rec-inv-partial-<?php echo esc_attr( $key ); ?>" <?php if ( $invoice['partial'] == 1 ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e('Allow partial payments on this invoice.', 'projectopia-core'); ?>
								</div>
							</div>
							<div style="float:right; width:390px;">
								<p><strong><?php esc_html_e('Invoice Items', 'projectopia-core'); ?></strong></p>
								<div class="repeater">	
									<?php if ( ! empty( $invoice['items'] ) ) { ?>
										<div data-repeater-list="group<?php echo esc_attr( $key ); ?>-a">
											<?php foreach ( $invoice['items'] as $item ) { 
												$key = wp_rand( 99, 99999 ); ?>
												<div class="line_item" data-repeater-item>
													<table style="table-layout: fixed;" class="milestones invoice-items">
														<tbody>
															<tr>
																<td style="width: 20%;"><div class="form-group"><div class="input-group"><input data-row="0" data-key="<?php echo esc_attr( $key ); ?>" id="edit_<?php echo esc_attr( $key ); ?>_invoice_qty" class="form-control input edit_<?php echo esc_attr( $key ); ?>_invoice_qty" type="text" name="qty" value="<?php echo esc_attr( $item['qty'] ); ?>" placeholder="<?php esc_attr_e('Qty', 'projectopia-core'); ?>" /></div></div></td>
																<td style="width: 50%;padding: 0 5px;"><div class="form-group"><div class="input-group"><input data-row="0" data-key="<?php echo esc_attr( $key ); ?>" id="edit_<?php echo esc_attr( $key ); ?>_invoice_desc" class="form-control input edit_<?php echo esc_attr( $key ); ?>_invoice_desc" type="text" name="desc" value="<?php echo esc_attr( $item['desc'] ); ?>" placeholder="<?php esc_attr_e('Description', 'projectopia-core'); ?>" /></div></div></td>
																<td style="width: 20%;"><div class="form-group"><div class="input-group"><input data-row="0" data-key="<?php echo esc_attr( $key ); ?>" id="edit_<?php echo esc_attr( $key ); ?>_invoice_price" class="form-control input edit_<?php echo esc_attr( $key ); ?>_invoice_price" type="text" name="price" value="<?php echo esc_attr( $item['price'] ); ?>" placeholder="<?php esc_attr_e('Price', 'projectopia-core'); ?>" /></div></div></td>
																<td style="width: 10%;"><input data-row="0" data-key="<?php echo esc_attr( $key ); ?>" class="line_delete bg-red cqpim_button cqpim_small_button rounded_2" style="padding: 10px 15px;" data-repeater-delete type="button" value=""/></td>
															</tr>
														</tbody>
													</table>
												</div>
											<?php } ?>
										</div>
										<?php } else { ?>
										<div data-repeater-list="group-a">
											<div class="line_item" data-repeater-item>
												<table style="table-layout: fixed;" class="milestones invoice-items">
													<tbody>
														<tr>
															<td style="width: 20%;"><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_qty" class="form-control input invoice_qty" type="text" name="qty" value="" placeholder="<?php esc_attr_e('Qty', 'projectopia-core'); ?>" /></div></div></td>
															<td style="width: 50%;padding: 0 5px;"><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_desc" class="form-control input invoice_desc" type="text" name="desc" value="" placeholder="<?php esc_attr_e('Description', 'projectopia-core'); ?>" /></div></div></td>
															<td style="width: 20%;"><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_price" class="form-control input invoice_price" type="text" name="price" value="" placeholder="<?php esc_attr_e('Price', 'projectopia-core'); ?>" /></div></div></td>
															<td style="width: 10%;"><input data-row="0" class="line_delete bg-red cqpim_button cqpim_small_button rounded_2" data-repeater-delete type="button" style="padding: 10px 15px;" value=""/></td>
														</tr>
													</tbody>
												</table>
											</div>				
										</div>							
									<?php } ?>
									<input class="add_line_item_row cqpim_button" data-repeater-create type="button" value=""/>
								</div>
								<div class="edit-inv-messages"></div>
								<div class="clear"></div>
								<button class="cancel-colorbox mt-3 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
								<button class="edit-rec-inv-btn mt-3 piaBtn right" value="<?php echo esc_attr( $post->ID ); ?>" data-key="<?php echo esc_attr( $entry_id ); ?>"><?php esc_html_e('Save', 'projectopia-core'); ?><span id="edit-rec-inv-spinner-<?php echo esc_attr( $entry_id ); ?>" class="ajax_loader" style="display: none;"></span></button>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
		}
	} ?>

	<button class="mt-10 piaBtn right" id="create-rec-inv"><?php esc_html_e( 'Add Recurring Invoice', 'projectopia-core' ); ?></button>
	<div class="clear"></div>
	<div style="display: none;" id="add-recurring-invoice-container">
		<div id="add-recurring-invoice" style="width: 830px;">
			<div style="padding: 12px;">
				<h3><?php esc_html_e('Add Recurring Invoice', 'projectopia-core'); ?></h3>
				<div style="float:left; width: 390px;">
					<div class="form-group">
						<label for="rec-inv-title"><?php esc_html_e('Title', 'projectopia-core'); ?></label>
						<div class="input-group">
							<input type="text" id="rec-inv-title" class="form-control input" value="" />
						</div>
					</div>
					<div class="form-group">
						<label for="client_contact_select"><?php esc_html_e('Contact', 'projectopia-core'); ?></label>
						<div class="input-group">
							<select id="client_contact_select" class="form-control input">
								<option value=""><?php esc_html_e('Choose a contact...', 'projectopia-core'); ?></option>
								<?php 
								$client_details = get_post_meta( $post->ID, 'client_details', true );
								$client_details = $client_details && is_array( $client_details ) ? $client_details : array( 'user_id' => 0 );                       
								$user_id = isset( $client_details['user_id'] ) ? $client_details['user_id'] : '';
								$client_contact = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
								echo '<option value="' . esc_attr( $user_id ) . '">' . esc_html( $client_contact ) . ' ' . esc_html__( '(Main contact)', 'projectopia-core' ) . '</option>';
								$contacts = get_post_meta( $post->ID, 'client_contacts', true );
								$contacts = $contacts && is_array( $contacts ) ? $contacts : array();
								foreach ( $contacts as $contact ) {
									echo '<option value="' . esc_attr( $contact['user_id'] ) . '">' . esc_html( $contact['name'] ) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="row">
					    <div class="col-6">
							<div class="form-group">
								<label for="rec-inv-start"><?php esc_html_e('Start', 'projectopia-core'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('You can specify a Start date for this invoice. If you leave the start blank then the invoice will start sending today.', 'projectopia-core' ); ?>"></i></label>
								<div class="input-group">
									<input type="text" class="form-control input datepicker" id="rec-inv-start" placeholder="<?php esc_attr_e('Start', 'projectopia-core'); ?>" />
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<label for="rec-inv-end"><?php esc_html_e('End', 'projectopia-core'); ?> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('You can specify a End date for this invoice. If you leave End blank, then the invoice will send according to the frequency until you delete or disable it.', 'projectopia-core' ); ?>"></i></label>
								<div class="input-group">
									<input type="text" class="form-control input datepicker" id="rec-inv-end" placeholder="<?php esc_attr_e('End', 'projectopia-core'); ?>" />
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								<label for="rec-inv-frequency"><?php esc_html_e('Frequency', 'projectopia-core'); ?></label>
								<div class="input-group">
									<select id="rec-inv-frequency" class="form-control input">
										<option value=""><?php esc_html_e('Choose a frequency', 'projectopia-core'); ?></option>
										<option value="daily"><?php esc_html_e('Daily', 'projectopia-core'); ?></option>
										<option value="weekly"><?php esc_html_e('Weekly', 'projectopia-core'); ?></option>
										<option value="biweekly"><?php esc_html_e('Fortnightly', 'projectopia-core'); ?></option>
										<option value="monthly"><?php esc_html_e('Monthly', 'projectopia-core'); ?></option>
										<option value="bimonthly"><?php esc_html_e('Bi Monthly', 'projectopia-core'); ?></option>
										<option value="threemonthly"><?php esc_html_e('Every Three Months', 'projectopia-core'); ?></option>
										<option value="sixmonthly"><?php esc_html_e('Every Six Months', 'projectopia-core'); ?></option>
										<option value="yearly"><?php esc_html_e('Yearly', 'projectopia-core'); ?></option>
										<option value="biyearly"><?php esc_html_e('Biyearly', 'projectopia-core'); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<label for="rec-inv-status"><?php esc_html_e('Status', 'projectopia-core'); ?></label>
								<div class="input-group">
									<select id="rec-inv-status" class="form-control input">
										<option value="1"><?php esc_html_e('Active', 'projectopia-core'); ?></option>
										<option value="0"><?php esc_html_e('Disabled', 'projectopia-core'); ?></option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="pto-inline-item-wrapper">
						<input type="checkbox" id="rec-inv-auto" /> <?php esc_html_e('Send the recurring invoices to the client on creation.', 'projectopia-core'); ?>
						<i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('It will be send by system when your wp cron runs.', 'projectopia-core'); ?>"></i>
					</div>
					<div class="pto-inline-item-wrapper">
						<input type="checkbox" id="rec-inv-partial" /> <?php esc_html_e('Allow partial payments on this invoice.', 'projectopia-core'); ?>
					</div>
				</div>
				<div style="float: right;width: 390px;">
					<p><strong><?php esc_html_e('Invoice Items', 'projectopia-core'); ?></strong></p>
					<div class="repeater">	
						<div data-repeater-list="ngroup-a">
							<div class="line_item" data-repeater-item>
								<table style="table-layout: fixed;" class="milestones invoice-items">
									<tbody>
										<tr>
											<td style="width: 20%;"><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_qty" class="form-control input invoice_qty" type="text" name="qty" value="" placeholder="<?php esc_attr_e('Qty', 'projectopia-core'); ?>" /></div></div></td>
											<td style="width: 50%;padding: 0 5px;"><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_desc" class="form-control input invoice_desc" type="text" name="desc" value="" placeholder="<?php esc_attr_e('Description', 'projectopia-core'); ?>" /></div></div></td>
											<td style="width: 20%;"><div class="form-group"><div class="input-group"><input data-row="0" id="invoice_price" class="form-control input invoice_price" type="text" name="price" value="" placeholder="<?php esc_attr_e('Price', 'projectopia-core'); ?>" /></div></div></td>
											<td style="width: 10%;"><input data-row="0" class="line_delete bg-red cqpim_button cqpim_small_button rounded_2" data-repeater-delete type="button" style="padding: 10px 15px;" value=""/></td>
										</tr>
									</tbody>
								</table>
							</div>				
						</div>
						<input class="add_line_item_row cqpim_button" data-repeater-create type="button" value=""/>
					</div>
					<div class="rec-inv-messages"></div>
					<div class="clear"></div>
					<button class="cancel-colorbox mt-3 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
					<button id="add_rec_inv" class="mt-3 piaBtn right" value="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('Add Recurring Invoice', 'projectopia-core'); ?> <span id="rec-inv-spinner" class="ajax_loader" style="display: none;"></span></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}