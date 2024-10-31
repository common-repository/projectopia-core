<?php
function pto_client_logs_metabox_callback( $post ) {
 	wp_nonce_field( 'client_logs_metabox', 'client_logs_metabox_nonce' );

	$client_logs = get_post_meta( $post->ID, 'client_logs', true ); 
	if ( empty( $client_logs ) ) {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__( 'The client has not accessed their dashboard yet', 'projectopia-core' ) . '</div>'; 
	} else { 
	krsort( $client_logs ); ?>
	<div class="card p-0 m-0">
		<table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_logs_table" data-sort="[[ 0, \'desc\' ]]" data-rows="5">
			<thead>
				<tr>
					<th><?php esc_html_e('Date', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('User', 'projectopia-core'); ?></th>
					<th><?php esc_html_e('Page', 'projectopia-core'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $client_logs as $key => $log ) { 
					$user = get_user_by('id', $log['user']); ?>
					<tr>
						<td data-sort="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( wp_date(get_option('cqpim_date_format') . ' H:i:s', $key) ); ?></td>
						<td><?php echo isset($user->display_name) ? esc_html( $user->display_name ) : ''; ?></td>
						<td><?php echo wp_kses_post( $log['page'] ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php }
}