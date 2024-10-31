<?php
/**
 * My Messages Page Admin
 *
 * This is my messages page showing list of messages admin.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

//Register my messages admin as sub menu page.
add_action( 'admin_menu' , 'register_pto_messaging_admin_page', 29 ); 
function register_pto_messaging_admin_page() {
	$mypage = add_submenu_page( 
		'pto-dashboard',
		__('All Messages (Admin)', 'projectopia-core'),        
		'<span class="pto-sm-hidden">' . esc_html__('All Messages (Admin)', 'projectopia-core') . '</span>',            
		'access_cqpim_messaging_admin',             
		'pto-messages-admin',       
		'pto_messages_admin'
	);

	add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
}

function pto_messages_admin() {
	$user = wp_get_current_user();
	$users = pto_retrieve_messageble_users( $user->ID );
	$search = array();

	if ( ! empty( $users ) ) {
		foreach ( $users as $key => $suser ) {
			$search[] = array(
				'id'   => $key,
				'name' => $suser,
			);
		}
	}

	$text = __( 'Search for team member name...', 'projectopia-core' );

	if ( user_can( $user->ID, 'cqpim_message_clients_from_projects' ) ) {
		$text = __( 'Search for team member name, client name or client company...', 'projectopia-core' );
	}

	if ( user_can( $user->ID, 'cqpim_message_all_clients' ) ) {
		$text = __( 'Search for team member name, client name or client company...', 'projectopia-core' );
	}
?>

<div class="dashboardWrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<!-- Create new message in conversion -->
				<div class="card" id="cqpim-new-message" style="display: none;">
					<div class="card-header d-block d-md-flex mb-3">
						<div class="card-header-info d-flex align-items-center">
							<span class="img-fluid mr-2"> <i class="fa fa-envelope-open font-blue-sharp" aria-hidden="true"></i> </span>
							<h5 class="mb-0"><?php esc_html_e('New Conversation', 'projectopia-core'); ?></h5>
						</div>
						<div class="card-header-btn mt-2 mt-md-0">
							<button id="send" class="cqpim_button cqpim_small_button font-white bg-green-sharp rounded_4 op" ><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('Send', 'projectopia-core'); ?></button>	
							<button id="cancel" class="cqpim_button cqpim_small_button font-white bg-red rounded_4 op"><i class="fa fa-times" aria-hidden="true"></i> <?php esc_html_e('Cancel', 'projectopia-core'); ?></button>					
						</div>
					</div>
					<div class="card-body">
						<form id="cqpim-create-new-message">
							<div class="form-group">
								<label class="cqpim-heading">
									<?php esc_html_e('Recipients:', 'projectopia-core'); ?>
								</label>
								<div class="input-group">
									<input class="form-control input" type="text" id="to" name="to" placeholder="<?php echo esc_attr( $text ); ?>" />
								</div>
							</div>

							<div class="form-group">
								<label class="cqpim-heading">
									<?php esc_html_e('Subject:', 'projectopia-core'); ?>
								</label>
								<div class="input-group">
									<input class="form-control input" type="text" id="subject" name="subject" />
								</div>
							</div>

							<div class="form-group">
								<label class="cqpim-heading">
									<?php esc_html_e('Message:', 'projectopia-core'); ?>
								</label>
								<div class="input-group">
									<textarea rows="5" style="min-height: 120px;" id="cmessage" name="cmessage" class="form-control input"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label class="cqpim-heading"><?php esc_html_e('Attachments:', 'projectopia-core'); ?></label>
								<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" multiple />

								<div id="upload_attachments"></div>
								<div class="clear"></div>
								<input type="hidden" name="image_id" id="upload_attachment_ids">
								<input type="hidden" name="action" value="image_submission">						
							</div>

							<div class="clear"></div>
							<div id="message-ajax-response"></div>
						</form>		
					</div>
				</div>

				<!-- All conversion list -->
				<div class="card all-project-card">
					<div class="card-header d-block d-md-flex">
						<div class="card-header-info d-flex align-items-center">
							<span class="img-fluid mr-2"> <i class="fa fa-envelope-open font-blue-sharp" aria-hidden="true"></i></span>
							<h5 class="mb-0"><?php esc_html_e( 'My Messages (Admin)', 'projectopia-core'); ?></h5>
						</div>
						<div class="card-header-btn mt-2 mt-md-0">
							<button id="send-message" class="piaBtn mt-2" ><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('New Conversation', 'projectopia-core'); ?></button>
						</div>
					</div>
					<div class="card-body">
						<?php if ( ! empty($_GET['convdeleted']) ) { ?>
							<div class="cqpim-alert cqpim-alert-success fadeout">
								<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The conversation was successfully deleted.', 'projectopia-core'); ?>
							</div>
						<?php } ?>
						<?php if ( ! empty($_GET['convcreated']) ) { ?>
							<div class="cqpim-alert cqpim-alert-success fadeout">
								<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The conversation was successfully created.', 'projectopia-core'); ?>
							</div>
						<?php } ?>
						<?php if ( ! empty($_GET['convleft']) ) { ?>
							<div class="cqpim-alert cqpim-alert-success fadeout">
								<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('You have been removed from the conversation.', 'projectopia-core'); ?>
							</div>
						<?php } ?>
						<?php if ( ! empty($_GET['convremoved']) ) { ?>
							<div class="cqpim-alert cqpim-alert-success fadeout">
								<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The user has been removed.', 'projectopia-core'); ?>
							</div>
						<?php } ?>
						<?php if ( ! empty($_GET['convadded']) ) { ?>
							<div class="cqpim-alert cqpim-alert-success fadeout">
								<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The user has been added.', 'projectopia-core'); ?>
							</div>
						<?php }
						
						$conversations = pto_fetch_conversations( $user->ID, true );
						if ( empty( $conversations ) ) { ?>
							<div id="cqpim-no-messages" class="mt-3">
								<p class="mb-0"><?php esc_html_e('You do not have any messages.', 'projectopia-core'); ?></p>				
							</div>
						<?php } else { ?>
							<table class="piaTableData table-responsive-sm table table-bordered w-100 no-footer dataTable" id="pto-my-work-page-table" data-ordering="[[ 4, 'desc' ]]" data-rows="10">
								<thead>
									<tr>
										<th><?php esc_html_e('Subject', 'projectopia-core'); ?></th>
										<th><?php esc_html_e('Created', 'projectopia-core'); ?></th>
										<th><?php esc_html_e('Updated', 'projectopia-core'); ?></th>
										<th><?php esc_html_e('Members', 'projectopia-core'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $conversations as $conversation ) { 
										$id = get_post_meta( $conversation->ID, 'conversation_id', true );
										$created = get_post_meta( $conversation->ID, 'created', true );
										$updated = get_post_meta( $conversation->ID, 'updated', true );
										$update_user = get_user_by( 'id', $updated['by'] );
										$update_user = $update_user->display_name;
										$timestamp = strtotime( gmdate( 'Y-m-d H:i', $updated['at'] ) );
										$members = get_post_meta( $conversation->ID, 'recipients', true );
										$args = array(
											'post_type'   => 'cqpim_messages',
											'posts_per_page' => -1,
											'post_status' => 'private',
											'meta_query'  => array(
												array(
													'key' => 'conversation_id',
													'value' => $id,
													'compare' => '=',
												),
											),
										);
										$messages = get_posts( $args );
										$read_val = true;
										foreach ( $messages as $message ) {
											$read = get_post_meta( $message->ID, 'read', true );
											if ( ! empty( $read ) && ! in_array( $user->ID, $read ) ) {
												$read_val = false;
											}
										}
										?> 
										<tr <?php if ( ! $read_val ) { echo 'class="cqpim-unread"'; } ?>>
											<td><?php if ( ! $read_val ) { echo '<i class="fa fa-envelope" aria-hidden="true"></i> '; } else { echo '<i class="fa fa-envelope-open" aria-hidden="true"></i> '; } ?>&nbsp;&nbsp;<a href="<?php echo esc_url( admin_url() ) . 'admin.php?page=pto-messages&conversation=' . esc_attr( $id ); ?>"><?php echo esc_html( get_the_title( $conversation->ID ) ); ?></a></td>
											<td><span style="display: none;"><?php echo esc_html( $created ); ?></span><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $created ) ); ?></td>
											<td><span style="display: none;"><?php echo esc_html( $updated['at'] ); ?></span><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $updated['at'] ) ); ?></td>
											<td> 
												<?php foreach ( $members as $member ) {
													$recip = get_user_by('id', $member);
													if ( empty( $recip ) || ! is_object( $recip ) ) {
														continue;
													}
													echo '<div class="cqpim-messagelist-avatar cqpim_tooltip" title="' . esc_attr( $recip->display_name ) . '" >';
													echo get_avatar( $recip->ID, 80, '', $recip->display_name, array( 'force_display' => true ));
													echo '</div>';
												} ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		jQuery(document).ready(function() {
			jQuery('#to').tokenInput(<?php echo wp_json_encode( $search ); ?>, {
				hintText: '<?php echo esc_js( $text ); ?>',
				noResultsText: '<?php esc_html_e('No Results', 'projectopia-core'); ?>',
				searchingText: '<?php esc_html_e('Searching', 'projectopia-core'); ?>'
			});						
		});
	</script>
	<script>
		jQuery(document).ready(function() {
			jQuery('#ato').tokenInput(<?php echo wp_json_encode( $search ); ?>, {
				hintText: '<?php echo esc_js( $text ); ?>',
				noResultsText: '<?php esc_html_e('No Results', 'projectopia-core'); ?>',
				searchingText: '<?php esc_html_e('Searching', 'projectopia-core'); ?>'
			});						
		});
	</script>

<?php }