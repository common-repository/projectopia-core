<?php
function pto_project_files_metabox_callback( $post ) {
 	wp_nonce_field( 'project_files_metabox', 'project_files_metabox_nonce' );

	$all_attached_files = get_attached_media( '', $post->ID );
	$args = array(
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'meta_key'       => 'project_id',
		'meta_value'     => $post->ID,
	);
	$tasks = get_posts($args);
	foreach ( $tasks as $task ) {
		$args = array(
			'post_parent' => $task->ID,
			'post_type'   => 'attachment',
			'numberposts' => -1,
		);
		$children = get_children($args);
		foreach ( $children as $child ) {
			$all_attached_files[] = $child;
		}
	}
	if ( ! $all_attached_files ) {
		echo '<p>' . esc_html__('There are no files uploaded to this project.', 'projectopia-core') . '</p>';
	} else {
		echo '<div class="card p-0 m-0">';
		echo '<table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_projectfiles_table"><thead><tr>';
		echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('Related Task', 'projectopia-core') . '</th><th>' . esc_html__('File Type', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded By', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
		echo '</tr></thead><tbody>';
		foreach ( $all_attached_files as $file ) {
			$file_object = get_post($file->ID);
			$url = get_the_permalink($file->ID);
			$parent = $file->post_parent;
			$parent_title = get_the_title($parent);
			$parent_title = str_replace('Protected: ', '', $parent_title);
			$parent_url = get_edit_post_link($parent);
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
			echo '<td><a class="cqpim-link" href="' . esc_url( $file->guid ) . '">' . esc_html( $file->post_title ) . '</a></td>';
			echo '<td><a class="cqpim-link" href="' . esc_url( $parent_url ) . '">' . esc_html( $parent_title ) . '</a></td>';
			echo '<td>' . wp_kses_post( $file_type_icon ) . '</td>';
			echo '<td>' . esc_html( $file->post_date ) . '</td>';
			echo '<td>' . esc_html( $user->display_name ) . '</td>';
			echo '<td>' . wp_kses_post( $download_link . ' ' . $delete_link ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table></div>'; ?>
		<div class="clear"></div>		
		<?php
	}
	echo '<button id="add_file_trigger" class="mt-20 piaBtn right colorbox">'.esc_html__('Add New File', 'projectopia-core').'</button><div class="clear"></div>	';
	?>
	<div id="add_file_container" style="display:none">
		<div id="add_file">
			<div style="padding:20px">
				<h3><?php esc_html_e('Add New File', 'projectopia-core'); ?></h3>
				<form>

					<div class="input-group">
						<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
					</div>

					<div id="upload_attachments"></div>
					<div class="clear"></div>

					<div class="form-group">
						<label for="task_id"> </label>
						<div class="input-group">
							<select id="task_id" class="form-control input customSelect">
								<?php
								$ordered = array();
								$project_elements = get_post_meta($post->ID, 'project_elements', true);
								if ( empty($project_elements) ) {
									echo '<option value="0">' . esc_html__('There are no tasks available to add this file to.', 'projectopia-core') . '</option>';
								} else {
									echo '<option value="0">' . esc_html__('Choose a Task', 'projectopia-core') . '</option>';
									foreach ( $project_elements as $key => $element ) {
										$weight = isset($element['weight']) ? $element['weight'] : '';
										$ordered[ $weight ] = $element;
									}
									ksort($ordered);
									foreach ( $ordered as $key => $element ) {
										echo '<optgroup label="' . esc_attr( $element['title'] ) . '">';
											$args = array(
												'post_type' => 'cqpim_tasks',
												'posts_per_page' => -1,
												'meta_key' => 'milestone_id',
												'meta_value' => $element['id'],
												'orderby'  => 'date',
												'order'    => 'ASC',
											);
											$tasks = get_posts($args);
											$ordered = array();
											foreach ( $tasks as $task ) {
												$task_details = get_post_meta($task->ID, 'task_details', true);
												$weight = isset($task_details['weight']) ? $task_details['weight'] : $wi;
												if ( empty($task->post_parent) ) {
													$ordered[ $weight ] = $task;
												}
											}
											ksort($ordered);
											foreach ( $ordered as $task ) {                            
												echo '<option value="' . esc_attr( $task->ID ) . '">' . esc_html__('TASK', 'projectopia-core') . ': ' . esc_html( $task->post_title ) . '</option>';
												$args = array(
													'post_type' => 'cqpim_tasks',
													'posts_per_page' => -1,
													'meta_key' => 'milestone_id',
													'meta_value' => $element['id'],
													'post_parent' => $task->ID,
													'orderby' => 'date',
													'order' => 'ASC',
												);
												$subtasks = get_posts($args);
												if ( ! empty($subtasks) ) {
													$subordered = array();
													foreach ( $subtasks as $subtask ) {
														$task_details = get_post_meta($subtask->ID, 'task_details', true);
														$sweight = isset($task_details['weight']) ? $task_details['weight'] : $sti;
														$subordered[ $sweight ] = $subtask;
													}
													ksort($subordered);
													foreach ( $subordered as $subtask ) {
														echo '<option value="' . esc_attr( $subtask->ID ) . '">' . esc_html__('SUBTASK', 'projectopia-core') . ': ' . esc_html( $subtask->post_title ) . '</option>';
													}
												}
											}
										echo '</optgroup>';                         
									}
								}
								?>
							</select>
						</div>
					</div>

					<input type="hidden" name="image_id" id="upload_attachment_ids" value="" />
					<input type="hidden" name="project_id" id="project_id" value="<?php echo esc_attr( $post->ID ); ?>" />

					<button id="add_file_ajax" class="mt-20 piaBtn right"><?php esc_html_e('Add File', 'projectopia-core'); ?></button>
					<br/> 
					<div class="" id="file_messages"></div>
				</form>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php }

add_action( 'save_post_cqpim_project', 'save_pto_project_files_metabox_data' );
function save_pto_project_files_metabox_data( $post_id ) {
	if ( ! isset( $_POST['project_files_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_textarea_field( wp_unslash( $_POST['project_files_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'project_files_metabox' ) ) {
	    return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	$duplicate = get_post_meta( $post_id, 'duplicate', true );
 	$duplicate = isset( $duplicate ) ? $duplicate : 0;
	$now = time();
	$diff = intval( $now ) - intval( $duplicate );
	if ( $diff > 3 ) {
		if ( isset( $_POST['delete_file'] ) ) {
			$att_to_delete = array_map( 'sanitize_text_field', wp_unslash( $_POST['delete_file'] ) );
			foreach ( $att_to_delete as $key => $attID ) {
				$file = get_post( $attID );
				$current_user = wp_get_current_user();
				$project_progress = get_post_meta( $post_id, 'project_progress', true );
				$project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

				$project_progress[] = array(
					/* translators: %s: File Name */
					'update' => sprintf( esc_html__( 'File Deleted: %s', 'projectopia-core' ), $file->post_title ),
					'date'   => time(),
					'by'     => $current_user->display_name,
				);
				update_post_meta( $post_id, 'project_progress', $project_progress );
				global $wpdb;
				$wpdb->update( $wpdb->posts, [ 'post_parent' => '' ], [ 'ID' => $attID ] );
			}
		}
		update_post_meta( $post_id, 'duplicate', time() );
	}
}