<br />
<div class="cqpim_block">
	<div class="cqpim_block_title">
		<div class="caption">
			<i class="fa fa-file font-light-violet" aria-hidden="true"></i>
			<span class="caption-subject font-light-violet sbold"> <?php esc_html_e('Client Files', 'projectopia-core'); ?></span>
		</div>
	</div>
	<?php 
	$user = wp_get_current_user();
	$client_logs = get_post_meta($assigned, 'client_logs', true);
	if ( empty($client_logs) ) {
		$client_logs = array();
	}
	$now = time();
	$client_logs[ $now ] = array(
		'user' => $user->ID,
		'page' => __('Client Dashboard Files Page', 'projectopia-core'),
	);
	update_post_meta($assigned, 'client_logs', $client_logs);
	$client_details = get_post_meta($assigned, 'client_details', true);
	$fe_files = get_post_meta($assigned, 'fe_files', true);
	$all_attached_files = get_attached_media( '', $assigned );
	$fe_files = get_post_meta($assigned, 'fe_files', true); ?>
	<div id="cqpim_backend_quote">
		<?php
		if ( ! $all_attached_files ) {
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('There are no client files available to view.', 'projectopia-core') . '</div>';
		} else {
			$sort = "[[3, 'desc']]";
			echo '<table id="client_files_table" class="datatable_style dataTable" data-ordering="' . esc_attr( $sort ) . '" data-rows="10"><thead><tr>';
			echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('File Type', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded By', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
			echo '</tr></thead><tbody>';
			foreach ( $all_attached_files as $file ) {
				$p_type = wp_check_filetype( get_attached_file( $file->ID ) );
				$file_user = get_user_by( 'id', $file->post_author );                
				if ( isset( $fe_files[ $file->ID ] ) && $fe_files[ $file->ID ] == 1 ) {
					$file_url = apply_filters( 'pto_client_file_url', $file->guid, $file, $p_type, $user );
					echo '<tr>';
					echo '<td><a href="' . esc_url( $file_url ) . '" target="_blank">' . esc_html( $file->post_title ) . '</a></td>';
					echo '<td>' . esc_html( $p_type['ext'] ) . '</td>';
					echo '<td>' . esc_html( $file->post_date ) . '</td>';
					echo '<td>' . ( isset( $file_user->display_name ) ? esc_html( $file_user->display_name ) : esc_html__('System', 'projectopia-core') ) . '</td>';
					echo '<td><a href="' . esc_url( $file_url ) . '" download="' . esc_attr( $file->post_title ) . '" class="cqpim_button cqpim_small_button border-green font-green" value="' . esc_attr( $file->ID ) . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
					echo '</tr>';               
				}
			}
			echo '</tbody></table>';
		} ?>
		<br />
		<h3><?php esc_html_e('Upload Files', 'projectopia-core'); ?></h3>
		<form id="client_fe_files">
			<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
			<div id="upload_attachments"></div>
			<div class="clear"></div>
			<br />
			<input type="hidden" name="image_id" id="upload_attachment_ids" value="" />
			<input type="hidden" name="client_id" id="client_id" value="<?php echo esc_attr( $assigned ); ?>" />
			<input type="submit" id="client_fe_files_submit" class="cqpim_button font-white bg-violet border-violet rounded_2 op" value="<?php esc_html_e('Submit Files', 'projectopia-core'); ?>">
		</form>
	</div>	
</div>