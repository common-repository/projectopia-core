<?php 
/**
 * All files page
 *
 * This is all files page showing list of files for admin.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

//Register all files as sub menu page.
add_action( 'admin_menu' , 'register_pto_files_page', 29 ); 
function register_pto_files_page() {
	$my_page = add_submenu_page(    
		'pto-dashboard',
		__('All Files (Admin)', 'projectopia-core'),           
		'<span class="pto-sm-hidden">' . esc_html__('All Files (Admin)', 'projectopia-core') . '</span>',       
		'cqpim_view_all_files',             
		'pto-files-admin',      
		'pto_files_admin'
	);

	add_action( 'load-' . $my_page, 'pto_enqueue_plugin_option_scripts' );
}

/**
 * Function to show the all files in table format.
 */
function pto_files_admin() {

	$user = wp_get_current_user(); 
	$roles = $user->roles;
	$assigned = pto_get_team_from_userid();

	$all_attached_files = [];
	$tasks = get_posts([
		'post_type'      => 'cqpim_tasks',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	]);

	foreach ( $tasks as $task ) {
		$children = get_children( [
			'post_parent' => $task->ID,
			'post_type'   => 'attachment',
			'numberposts' => -1,
		] );

		foreach ( $children as $child ) {
			$all_attached_files[] = $child;
		}
	}

	?>
	<!-- Markup for dashboardWrapper -->
	<div class="dashboardWrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card all-project-card">
						<div class="card-header d-block d-md-flex">
							<div class="card-header-info d-flex align-items-center">
								<img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
								<h5 class="mb-0"><?php esc_html_e('All Files (Admin)', 'projectopia-core'); ?></h5>
							</div>
						</div>
						<div class="card-body">
							<?php
							if ( empty( $all_attached_files ) ) {
								echo '<p class="p-3">' . esc_html__('There are no files uploaded', 'projectopia-core') . '</p>';
							} else {
								echo '<table class="piaTableData table-responsive-lg table table-bordered w-100 no-footer" id="pto-my-work-page-table"><thead><tr>';
								echo '<th>' . esc_html__('File Name', 'projectopia-core') . '</th><th>' . esc_html__('Related Project', 'projectopia-core') . '</th><th>' . esc_html__('Related Task', 'projectopia-core') . '</th><th>' . esc_html__('File Type', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded', 'projectopia-core') . '</th><th>' . esc_html__('Uploaded By', 'projectopia-core') . '</th><th>' . esc_html__('Actions', 'projectopia-core') . '</th>';
								echo '</tr></thead><tbody>';
								foreach ( $all_attached_files as $file ) {
									$file_object = get_post( $file->ID );
									$url = get_the_permalink( $file->ID );
									$parent = $file->post_parent;
									$project = get_post_meta( $parent, 'project_id', true );
									$parent_object = get_post( $parent );
									$parent_url = get_edit_post_link( $parent_object->ID );

									$project_url = $project_title = '';
									if ( ! empty( $project ) ) {
										$project_object = get_post( $project );
										$project_url = get_edit_post_link( $project_object->ID );
										$project_title = $project_object->post_title;
									}

									$user = get_user_by( 'id', $file->post_author );

									//Format the date.
									$uploaded_date = $file->post_date;
									if ( is_numeric( $uploaded_date ) ) {
										$uploaded_date = wp_date( get_option('cqpim_date_format'), $uploaded_date); 
									}

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

									echo '<tr>';
									echo '<td style="max-width:250px"><a class="cqpim-link" href="' . esc_url( $file->guid ) . '">' . esc_html( $file->post_title ) . '</a></td>';
									echo '<td><a class="cqpim-link" href="' . esc_url( $project_url ) . '">' . esc_html( $project_title ) . '</a></td>';
									echo '<td><a class="cqpim-link" href="' . esc_url( $parent_url ) . '">' . esc_html( $parent_object->post_title ). '</a></td>';
									echo '<td>' . wp_kses_post( $file_type_icon ) . '</td>';
									echo '<td>' . esc_html( $uploaded_date ) . '</td>';
									echo '<td>' . esc_html( $user->display_name ) . '</td>';
									echo '<td>'. wp_kses_post( $download_link ) .'</td>';
									echo '</tr>';
								}
								echo '</tbody></table>'; ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--/ Markup for dashboardWrapper -->
<?php }
