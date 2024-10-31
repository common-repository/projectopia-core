<?php
function pto_task_files_metabox_callback( $post ) {
	$all_attached_files = get_attached_media( '', $post->ID );
	if ( ! $all_attached_files ) {
		echo '<p>' . esc_html__('There are no files uploaded to this task.', 'projectopia-core') . '</p>';
	} else {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable"><thead><tr>';
		echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('File Type', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded By', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
		echo '</tr></thead><tbody>';
		foreach ( $all_attached_files as $file ) {
			$file_object = get_post( $file->ID );
			$url = get_the_permalink( $file->ID );
			$user = get_user_by( 'id', $file->post_author );

			//Check file extension and mime type.
			$extension = pathinfo( $file->guid , PATHINFO_EXTENSION );
			$file_type = $extension;
			if ( ! empty( explode( '/', $file->post_mime_type )[0] == 'image' ) ) {
				$file_type = 'jpg';
			}

			//If file type icon is not available then assign docx as default for unknown type
			if ( ! in_array( $file_type, [ 'jpg', 'pdf', 'zip', 'docx' ] ) ) {
				$file_type = 'docx';
			}

			//Prepare file type icon.
			$file_type_icon = sprintf(
				'<span class="fileTypeWrapper align-items-center">
					<img src="%s" alt="%s" class="fileTypeIcon img-fluid mr-2" />
					<span class="mb-0">%s</span>
				</span>',
				PTO_PLUGIN_URL .'/assets/admin/img/' . $file_type . '.svg',
				$file_type,
				$extension
			);

			//Prepare download icon.
			$download_link = sprintf(
				'<a href="%s" download="%s" class="btn">
					<img src="%s" alt="download" class="img-fluid"/>
				</a>',
				$file->guid,
				$file->post_title,
				PTO_PLUGIN_URL .'/assets/admin/img/download.svg'
			);

			$delete_link = sprintf(
				'<button href="%s" class="delete_file btn" data-id="%s">
					<img src="%s" alt="delete" class="img-fluid"/>
				</button>',
				$file->guid,
				$file->ID,
				PTO_PLUGIN_URL .'/assets/admin/img/trash.svg'
			);

			echo '<tr>';
			echo '<td><a href="' . esc_attr( $file->guid ) . '" target="_blank">' . esc_html( $file->post_title ) . '</a></td>';
			echo '<td>' . wp_kses_post( $file_type_icon ) . '</td>';
			echo '<td>' . esc_html( $file->post_date ) . '</td>';
			if ( is_object( $user ) ) {
				echo '<td>' . esc_html( $user->display_name ) . '</td>';
			} else {
				echo '<td></td>';
			}
			echo '<td>' . wp_kses_post( $download_link . ' ' . $delete_link ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table></div><hr>';
	} ?>
	<div class="form-group mb-0">
    	<label for="attachments"><?php esc_html_e( 'Upload Files', 'projectopia-core' ); ?></label>
		<input type="file" class="cqpim-file-upload form-control-file" name="async-upload" id="attachments" />
		<div id="upload_attachments"></div>
	</div>
	<input type="hidden" name="image_id" id="upload_attachment_ids">
	<?php
}