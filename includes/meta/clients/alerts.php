<?php
function pto_client_alerts_metabox_callback( $post ) {
 	wp_nonce_field( 'client_alerts_metabox', 'client_alerts_metabox_nonce' );

	esc_html_e( 'If you would like to display custom alerts on the dashboard for this client, you can add them here. You will be able to see once the client has seen the alert and marked it as seen.', 'projectopia-core' );
	
	$custom_alerts = get_post_meta( $post->ID, 'custom_alerts', true );
	$alert_names = pto_get_alert_names();

	if ( empty( $custom_alerts ) ) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' .esc_html__( 'You have not added any custom alerts for this client.', 'projectopia-core' ) . '</div>';
	} else {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_alerts_table">';
		echo '<thead><tr><th>' .esc_html__('ID', 'projectopia-core') . '</th><th>' .esc_html__('Message', 'projectopia-core') . '</th><th>' .esc_html__('Seen', 'projectopia-core') . '</th><th>' .esc_html__('Cleared', 'projectopia-core') . '</th><th>' .esc_html__('Global', 'projectopia-core') . '</th><th>' .esc_html__('Actions', 'projectopia-core') . '</th></tr></thead>';
		echo '<tbody>';
		foreach ( $custom_alerts as $key => $alert ) { 
			$seen = ! empty( $alert['seen'] ) ? $alert['seen'] : esc_html__( 'N/A', 'projectopia-core' );
			$cleared = ! empty( $alert['cleared'] ) ? $alert['cleared'] : esc_html__( 'N/A', 'projectopia-core' ); ?>
			<tr>
				<td><?php if ( empty( $alert['global'] ) ) { echo esc_html( $post->ID ) . '-'; } ?><?php echo esc_html( $key ); ?></td>
				<td><div class="cqpim-alert cqpim-alert-<?php echo esc_attr( $alert['level'] ); ?>"><?php echo wp_kses_post( $alert['message'] ); ?></div></td>
				<td><?php if ( is_numeric( $seen ) ) { echo esc_html( wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $seen ) ); } else { echo esc_html( $seen ); } ?></td>
				<td><?php if ( is_numeric( $cleared ) ) { echo esc_html( wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $cleared ) ); } else { echo esc_html( $cleared ); } ?></td>
				<td><?php if ( ! empty( $alert['global'] ) ) { ?><i style="font-size: 18px;" class="fa fa-check" aria-hidden="true"></i><?php } else { ?><i style="font-size: 18px;" class="fa fa-times" aria-hidden="true"></i><?php } ?></td>
				<td><button class="edit_alert cqpim_button cqpim_small_button font-amber border-amber" value="<?php echo esc_attr( $key ); ?>" data-global="<?php echo esc_attr( $alert['global'] ); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <button class="delete_alert cqpim_button cqpim_small_button font-red border-red" value="<?php echo esc_attr( $key ); ?>" data-global="<?php echo esc_attr( $alert['global'] ); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
			</tr>
			<div id="edit_client_alert_<?php echo esc_attr( $key ); ?>_ajax_container" style="display: none;">
				<div id="edit_client_alert_<?php echo esc_attr( $key ); ?>_ajax">
					<div style="padding: 12px;">
						<h3><?php esc_html_e( 'Edit Custom Alert', 'projectopia-core' ); ?></h3>
						<div class="form-group">
							<label for="alert_level_<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Alert Level', 'projectopia-core' ); ?></label>
							<div class="input-group">
								<select id="alert_level_<?php echo esc_attr( $key ); ?>" name="alert_level" class="form-control input">
									<option value="0"><?php esc_html_e('Choose... ', 'projectopia-core'); ?></option>
									<option value="info" <?php if ( $alert['level'] == 'info' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Notice (Blue)', 'projectopia-core'); ?></option>
									<option value="success" <?php if ( $alert['level'] == 'success' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Success (Green)', 'projectopia-core'); ?></option>
									<option value="warning" <?php if ( $alert['level'] == 'warning' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Warning (Amber)', 'projectopia-core'); ?></option>
									<option value="danger" <?php if ( $alert['level'] == 'danger' ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Error (Red)', 'projectopia-core'); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="alert_message_<?php echo esc_attr( $key ); ?>"><?php esc_html_e('Message', 'projectopia-core'); ?></label>
							<div class="input-group">
								<input type="text" id="alert_message_<?php echo esc_attr( $key ); ?>" class="form-control input" name="alert_message" value="<?php echo isset( $alert['message'] ) ? wp_kses_post( $alert['message'] ) : ''; ?>" style="min-width: 350px;" />
							</div>
						</div>
						<div id="client_alert_messages_<?php echo esc_attr( $key ); ?>"></div>
						<button class="cancel-colorbox piaBtn redColor mt-10"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>			
						<button id="edit_client_alert_<?php echo esc_attr( $key ); ?>_submit" data-key="<?php echo esc_attr( $key ); ?>" class="edit_alert_submit piaBtn contact_edit_submit mt-10 right"><?php esc_html_e('Update Custom Alert', 'projectopia-core'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display: none;"></span></button>
					</div>
				</div>
			</div>
		<?php }
		echo '</tbody>';
		echo '</table></div>';
	} ?>
	<button id="add_client_alert" class="mt-10 piaBtn right" value="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Add Custom Alert', 'projectopia-core' ); ?></button>
	<div class="clear"></div>
	<div id="add_client_alert_ajax_container" style="display: none;">
		<div id="add_client_alert_ajax">
			<div style="padding: 12px;">
				<h3><?php esc_html_e('Add Custom Alert', 'projectopia-core'); ?></h3>
				<div class="form-group">
					<label for="alert_level"><?php esc_html_e( 'Alert Level', 'projectopia-core' ); ?></label>
					<div class="input-group">
						<select id="alert_level" name="alert_level" class="form-control input">
							<option value="0"><?php esc_html_e('Choose... ', 'projectopia-core'); ?></option>
							<option value="info"><?php esc_html_e('Notice (Blue)', 'projectopia-core'); ?></option>
							<option value="success"><?php esc_html_e('Success (Green)', 'projectopia-core'); ?></option>
							<option value="warning"><?php esc_html_e('Warning (Amber)', 'projectopia-core'); ?></option>
							<option value="danger"><?php esc_html_e('Error (Red)', 'projectopia-core'); ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="alert_message"><?php esc_html_e('Message', 'projectopia-core'); ?></label>
					<div class="input-group">
						<textarea style="min-width: 350px;" type="text" id="alert_message" class="form-control input pto-textarea" name="alert_message"></textarea>
					</div>
				</div>
				<div class="pto-inline-item-wrapper">
					<input type="checkbox" id="alert_global" name="alert_global" /> <?php esc_html_e('Make this a global alert (Add to ALL Client\'s Dashboards)', 'projectopia-core'); ?>
				</div>
				<?php
					/**
					 * Create new action to add option in client custom alert.
					 * @since 5.0.4
					 * 
					 * @return HTML $option_contents
					 */
					do_action( 'pto_client_add_alert_filter' );
				?>
				<div id="client_alert_messages"></div>
				<button class="cancel-colorbox piaBtn redColor mt-10"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>			
				<button id="add_client_alert_submit" class="piaBtn mt-10 right"><?php esc_html_e('Add Custom Alert', 'projectopia-core'); ?><span id="ajax_spinner_add_contact" class="ajax_loader" style="display: none;"></span></button>
			</div>
		</div>
	</div>
<?php }