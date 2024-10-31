<?php
function pto_lead_files_metabox_callback( $post ) {
 	wp_nonce_field( 'lead_files_metabox', 'lead_files_metabox_nonce' );

	$all_attached_files = get_attached_media( '', $post->ID );
	
	if ( ! $all_attached_files ) {
		echo '<p>' . esc_html__( 'There are no files attached to this lead.', 'projectopia-core' ) . '</p>';
	} else {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_leadfiles_table"><thead><tr>';
		echo '<th>' . esc_html__( 'File Name', 'projectopia-core' ) . '</th><th>' . esc_html__( 'File Type', 'projectopia-core' ) . '</th><th>' . esc_html__( 'Uploaded', 'projectopia-core' ) . '</th><th>' . esc_html__( 'Uploaded By', 'projectopia-core' ) . '</th><th>' . esc_html__( 'Actions', 'projectopia-core' ) . '</th>';
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
				esc_url( $file->guid ),
				$file->post_title,
				PTO_PLUGIN_URL .'/assets/admin/img/download.svg'
			);

			$delete_link = sprintf(
				'<button href="%s" class="delete_file btn" data-id="%s">
					<img src="%s" alt="delete" class="img-fluid"/>
				</button>',
				esc_url( $file->guid ),
				$file->ID,
				PTO_PLUGIN_URL .'/assets/admin/img/trash.svg'
			);


			echo '<tr>';
			echo '<td><a href="' . esc_url( $file->guid ) . '" target="_blank">' . esc_html( $file->post_title ) . '</a></td>';
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
		<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
		<div id="upload_attachments"></div>
	</div>
	<input type="hidden" name="image_id" id="upload_attachment_ids">
	<?php
}

add_action( 'save_post_cqpim_lead', 'save_pto_lead_files_metabox_data' );
function save_pto_lead_files_metabox_data( $post_id ) {
	global $wpdb;
	if ( ! isset( $_POST['lead_files_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['lead_files_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'lead_files_metabox' ) ) {
	    return $post_id;
	}
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$attachments = isset( $_POST['image_id'] ) ? sanitize_text_field( wp_unslash( $_POST['image_id'] ) ) : '';
	if ( ! empty( $attachments ) ) {
		$attachments = explode( ',', $attachments );
		foreach ( $attachments as $attachment ) {
			$wpdb->update( $wpdb->posts, [ 'post_parent' => $post_id ], [ 'ID' => $attachment ] );
			update_post_meta( $attachment, 'cqpim', true );
		}
	}

	if ( isset( $_POST['delete_file'] ) ) {
		$att_to_delete = array_map( 'sanitize_text_field', wp_unslash( $_POST['delete_file'] ) );
		foreach ( $att_to_delete as $key => $attID ) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, [ 'post_parent' => '' ], [ 'ID' => $attID ] );
		}
	}   
}