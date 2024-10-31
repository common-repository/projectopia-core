<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$p_title = get_the_title();
$p_title = str_replace('Private:', '', $p_title);
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	/* translators: %1$s: Project ID, %2$s: Project Title */
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Files Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($assigned, 'client_logs', $client_logs);
?>
<div class="masonry-grid">
	<div class="grid-sizer"></div>
	<div class="cqpim-dash-item-full grid-item tasks-box">
		<div class="cqpim_block">
			<div class="cqpim_block_title">
				<div class="caption">
					<span class="caption-subject font-light-violet sbold"><?php esc_html_e('Project Files', 'projectopia-core'); ?></span>
				</div>	
			</div>
			<?php
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
					echo '<table class="cqpim_table files"><thead><tr>';
					echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('Related Task', 'projectopia-core') . '</th><th>' . esc_html__('File Type', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded By', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
					echo '</tr></thead><tbody>';
					foreach ( $all_attached_files as $file ) {
						$file_object = get_post($file->ID);
						$url = get_the_permalink($file->ID);
						$parent = $file->post_parent;
						$parent_title = get_the_title($parent);
						$parent_title = str_replace('Protected: ', '', $parent_title);
						$parent_url = get_the_permalink($parent);
						$user = get_user_by( 'id', $file->post_author );
						echo '<tr>';
						echo '<td><span class="nodesktop"><strong>' . esc_html__('Title', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( $file->guid ) . '" download="' . esc_attr( $file->post_title ) . '">' . esc_html( $file->post_title ) . '</a></td>';
						echo '<td><span class="nodesktop"><strong>' . esc_html__('Task', 'projectopia-core') . '</strong>: </span> <a class="cqpim-link" href="' . esc_url( $parent_url ) . '">' . wp_kses_post( $parent_title ) . '</a></td>';
						echo '<td><span class="nodesktop"><strong>' . esc_html__('Type', 'projectopia-core') . '</strong>: </span> ' . esc_html( $file->post_mime_type ) . '</td>';
						echo '<td><span class="nodesktop"><strong>' . esc_html__('Added', 'projectopia-core') . '</strong>: </span> ' . esc_html( $file->post_date ) . '</td>';
						echo '<td><span class="nodesktop"><strong>' . esc_html__('Added By', 'projectopia-core') . '</strong>: </span> ' . ( isset( $user->display_name ) ? esc_html( $user->display_name ) : esc_html__('System', 'projectopia-core') ) . '</td>';
						echo '<td><a href="' . esc_url( $file->guid ) . '" download="' . esc_attr( $file->post_title ) . '" class="cqpim_button cqpim_small_button border-green font-green op" value="' . esc_attr( $file->ID ) . '"><i class="fa fa-download" aria-hidden="true"></i></a></td>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				}
			?>
			<br />
			<h3><?php esc_html_e('Upload Files', 'projectopia-core'); ?></h3>
			<form id="project_fe_files">
				<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" />
				<div id="upload_attachments"></div>
				<div class="clear"></div>
				<br />
				<select id="task_id">
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
									'post_type'      => 'cqpim_tasks',
									'posts_per_page' => -1,
									'meta_key'       => 'milestone_id',
									'meta_value'     => $element['id'],
									'orderby'        => 'date',
									'order'          => 'ASC',
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
										'post_type'      => 'cqpim_tasks',
										'posts_per_page' => -1,
										'meta_key'       => 'milestone_id',
										'meta_value'     => $element['id'],
										'post_parent'    => $task->ID,
										'orderby'        => 'date',
										'order'          => 'ASC',
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
				<br /><br />
				<input type="hidden" name="image_id" id="upload_attachment_ids" value="" />
				<input type="hidden" name="project_id" id="project_id" value="<?php echo esc_attr( $post->ID ); ?>" />
				<input type="submit" id="client_fe_files_submit" class="cqpim_button font-white bg-violet rounded_2 op" value="<?php esc_html_e('Submit Files', 'projectopia-core'); ?>">
			</form>
		</div>
	</div>
</div>