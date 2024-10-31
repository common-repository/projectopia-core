<?php
/**
 * My Messages Page
 *
 * This is my messages page showing list of messages for team member.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

//Register my messages as sub menu page.
add_action( 'admin_menu' , 'register_pto_messaging_page', 29 );
function register_pto_messaging_page() {
	$my_page = add_submenu_page(    
		'pto-dashboard',
		__( 'My Messages', 'projectopia-core' ),       
		'<span class="pto-sm-hidden">' . __( 'My Messages', 'projectopia-core' ) . '</span>',           
		'access_cqpim_messaging',           
		'pto-messages',         
		'pto_messages'
	);

	add_action( 'load-' . $my_page, 'pto_enqueue_plugin_option_scripts' );
}

/**
 * Function to show the messages in table list.
 */
function pto_messages() {
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

	$has_conversion = false;
	$conversation = isset( $_GET['conversation'] ) ? intval( $_GET['conversation'] ) : '';
	if ( ! empty( $conversation ) ) {
		$args = array(
			'post_type'      => 'cqpim_conversations',
			'posts_per_page' => 1,
			'post_status'    => 'private',
			'meta_query'     => array(
				array(
					'key'     => 'conversation_id',
					'value'   => $conversation,
					'compare' => '=',
				),
			),
		);

		$conversation = get_posts( $args );
		$conversation = isset( $conversation[0] ) ? $conversation[0] : '';
		if ( ! empty( $conversation ) ) {
			$has_conversion = true;
		}
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
							<button id="send" class="piaBtn caribbeanGreen" ><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('Send', 'projectopia-core'); ?></button>	
							<button id="cancel" class="piaBtn redColor"><i class="fa fa-times" aria-hidden="true"></i> <?php esc_html_e('Cancel', 'projectopia-core'); ?></button>					
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

				<?php if ( $has_conversion ) { ?>
					<!-- Reply on existing conversion -->
					<div class="card" id="cqpim-reply-message" style="display: none;">
						<div class="card-header d-block d-md-flex">
							<div class="card-header-info d-flex align-items-center">
								<span class="img-fluid mr-2"> <i class="fa fa-envelope-open font-blue-sharp" aria-hidden="true"></i> </span>
								<h5 class="mb-0"><?php esc_html_e('Reply to Conversation', 'projectopia-core'); ?></h5>
							</div>
							<div class="card-header-btn mt-2 mt-md-0">
								<button id="send-reply" class="piaBtn caribbeanGreen" data-conversation="<?php echo isset( $conversation->ID ) ? esc_attr( $conversation->ID ) : ''; ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('Send', 'projectopia-core'); ?></button>	
								<button id="cancel-reply" class="piaBtn redColor"><i class="fa fa-times" aria-hidden="true"></i> <?php esc_html_e('Cancel', 'projectopia-core'); ?></button>					
							</div>
						</div>
						<div class="card-body">
							<form id="rcqpim-create-new-message" class="mt-2">
								<div class="form-group">
									<label class="cqpim-heading">
										<?php esc_html_e('Message:', 'projectopia-core'); ?>
									</label>
									<div class="input-group">
										<textarea rows="5" style="min-height: 120px;" id="rmessage" name="message" class="form-control input"></textarea>
									</div>
								</div>
								<div class="clear"></div>
								<p><span class="cqpim-heading"><?php esc_html_e('Attachments:', 'projectopia-core'); ?></span>
									<input type="file" class="rcqpim-file-upload" name="async-upload" id="attachments" multiple />
									<div id="rupload_attachments"></div>
									<div class="clear"></div>
									<input type="hidden" name="rimage_id" id="rupload_attachment_ids">
									<input type="hidden" name="action" value="image_submission">						
								</p>
								<div class="clear"></div>
							</form>
						</div>
					</div>
				<?php } ?>

				<!-- All conversion list -->
				<div class="card all-project-card">
					<div class="card-header d-block d-md-flex">
						<div class="card-header-info d-flex align-items-center">
							<span class="img-fluid mr-2"> <i class="fa fa-envelope-open font-blue-sharp" aria-hidden="true"></i></span>
							<?php if ( $has_conversion ) { ?>
								<h5 class="mb-0"><?php echo esc_html( get_the_title( $conversation->ID ) ); ?></h5>
							<?php } else { ?>
								<h5 class="mb-0"><?php esc_html_e('My Messages', 'projectopia-core'); ?></h5>
							<?php } ?>
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
						
						if ( $has_conversion ) {
							$conversation_id = get_post_meta( $conversation->ID, 'conversation_id', true );
							$recipients = get_post_meta( $conversation->ID, 'recipients', true );
							if ( ! empty( $recipients ) && ! in_array( $user->ID, $recipients ) && ! current_user_can( 'access_cqpim_messaging_admin' ) ) {
								echo '<h1>' . esc_html__( 'ACCESS DENIED', 'projectopia-core' ) . '</h1>';
								return;
							}
							$args = array(
								'post_type'      => 'cqpim_messages',
								'posts_per_page' => -1,
								'post_status'    => 'private',
								'meta_query'     => array(
									array(
										'key'     => 'conversation_id',
										'value'   => $conversation_id,
										'compare' => '=',
									),
								),
								'order'          => 'DESC',
								'orderby'        => 'meta_value',
								'meta_key'       => 'stamp',
							);

							$messages = get_posts( $args ); ?>

							<div id="cqpim-messaging-edit-subject">
								<?php
								pto_generate_fields( array(
									'id'    => 'cqpim-title-editable-field',
									'value' => get_the_title( $conversation->ID ),
								) );
								?>
								<input type="hidden" id="jq-user-id" value="<?php echo esc_attr( $user->ID ); ?>" />
								<input type="hidden" id="jq-conv-id" value="<?php echo esc_attr( $conversation->ID ); ?>" />
								<div id="cqpim-messaging-buttons">
									<button id="cqpim-cancel-edit-subject" class="piaBtn redColor"><i class="fa fa-times" aria-hidden="true"></i><span class="desktop_only"> <?php esc_html_e('Cancel', 'projectopia-core'); ?></span></button>
									<button id="cqpim-save-subject" class="piaBtn caribbeanGreen"><i class="fa fa-floppy-o" aria-hidden="true"></i><span class="desktop_only"> <?php esc_html_e('Save', 'projectopia-core'); ?></span></button>
								</div>
							</div>
							<div id="delete-confirm" style="display: none;" title="<?php esc_html_e('Delete Conversation', 'projectopia-core'); ?>">
								<p>
									<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
									<?php esc_html_e('This conversation and all messages will be permanently deleted. Are you sure?', 'projectopia-core'); ?>
								</p>
							</div>
							<div id="leave-confirm" style="display: none;" title="<?php esc_html_e('Leave Conversation', 'projectopia-core'); ?>">
								<p>
									<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 
									<?php esc_html_e('Are you sure you want to leave the conversation?', 'projectopia-core'); ?>
								</p>
							</div>
							<div id="remove-confirm" style="display: none;" title="<?php esc_html_e('Remove User', 'projectopia-core'); ?>">
								<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php esc_html_e('Choose which user you would like to remove and click Remove User.', 'projectopia-core'); ?></p>
								<select id="cqpim-remove-user" style="width: 100%;">
									<?php 
									if ( ! empty( $recipients ) ) {
										foreach ( $recipients as $recipient ) { 
											$recip = get_user_by( 'id', $recipient );
											$recip_id = isset( $recip->ID ) ? $recip->ID : '';
											$display_name = isset( $recip->display_name ) ? $recip->display_name : '';
											if ( ! empty( $recip_id ) && ! empty( $display_name ) ) {
												echo '<option value="' . esc_attr( $recip_id ) . '">' . esc_html( $display_name ) . '</option>';
											}
										}
									}
									?>
								</select>
							</div>
							<div id="add-confirm" style="display: none;" title="<?php esc_html_e('Add User', 'projectopia-core'); ?>">
								<p><i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('Search for a user to add to the conversation.', 'projectopia-core'); ?></p>
								<input type="text" id="ato" />
							</div>
							<div class="row">
								<div class="col-lg-2">
									<div id="pto-message-menu-container">
										<div class="vertical-menu">
											<a href="#" id="cqpim-edit-subject" class="active"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit Subject</a>
											<a href="#" id="cqpim-convo-add"><i class="fa fa-user-plus" aria-hidden="true"></i>Add User</a>
											<a href="#" id="cqpim-convo-remove"><i class="fa fa-user-times " aria-hidden="true"></i>Remove User</a>
											<a href="#" id="cqpim-convo-leave"><i class="fa fa-sign-out" aria-hidden="true"></i>Leave</a>
											<a href="#" id="cqpim-convo-reply"><i class="fa fa-reply" aria-hidden="true"></i>Reply</a>
											<?php if ( $user->ID == $conversation->post_author || current_user_can('cqpim_do_all') ) { ?>
												<a href="#" id="cqpim-convo-delete"><i class="fa fa-trash" aria-hidden="true"></i>Delete</a>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="col-lg-10">
									<div id="cqpim-dmessage-container">
										<?php foreach ( $messages as $message ) { 
											$sender = get_post_meta( $message->ID, 'sender', true );
											$system = get_post_meta( $message->ID, 'system', true );
											$sender_obj = get_user_by( 'id', $sender );
											if ( $user->ID != $sender ) {
												$read = get_post_meta( $message->ID, 'read', true );
												if ( ! empty( $read ) && is_array( $read ) && ! in_array( $user->ID, $read ) ) {
													$read[] = $user->ID;
												}
												update_post_meta( $message->ID, 'read', $read );
											}
											$update = get_post_meta( $message->ID, 'message', true );
											$stamp = get_post_meta( $message->ID, 'stamp', true );
											$class = '';
											if ( $sender == $user->ID ) {
												$class = ' own';
											}
											if ( ! empty( $system ) ) {
												$class = ' system';
												echo '<div style="text-align:center; clear:both">';
											} ?>
											<div class="cqpim-dmessage-bubble<?php echo esc_attr( $class ); ?>">
												<div class="cqpim-messagelist-avatar">
													<?php echo get_avatar( $sender_obj->ID, 40, '', $sender_obj->display_name, array( 'force_display' => true ) ); ?>
												</div>
												<?php echo wp_kses_post( $update ); ?>
												<div class="clear"></div>
												<?php $all_attached_files = get_attached_media( '', $message->ID ); 
												if ( ! empty( $all_attached_files ) ) { ?>
													<div class="cqpim-dmessage-attachments<?php echo esc_attr( $class ); ?>">
														<div><strong><i class="fa fa-paperclip" aria-hidden="true"></i> <?php esc_html_e( 'Attachments', 'projectopia-core' ); ?></strong></div>
														<ul>
															<?php foreach ( $all_attached_files as $file ) { ?>
																<li><a href="<?php echo esc_url( $file->guid ); ?>" target="_blank"><?php echo esc_html( $file->post_title ); ?></a><span class="separator">|</span><i class="fa fa-download" aria-hidden="true"></i>  <a href="<?php echo esc_url( $file->guid ); ?>" download ><?php esc_html_e('Download', 'projectopia-core'); ?></a></li>
															<?php } ?>
														</ul>
													</div>
												<?php } ?>
													<div class="cqpim-dmessage-date<?php echo esc_attr( $class ); ?>">
														<i class="fa fa-paper-plane" aria-hidden="true"></i>
														<?php 
														$date = wp_date( get_option( 'cqpim_date_format' ) . ' H:i', $stamp );
														printf( '<span class="pr-3">%1$s %2$s on %3$s</span>', esc_html__( 'Posted by', 'projectopia-core' ), esc_html( $sender_obj->display_name ), esc_html( $date ) );
														$read = get_post_meta( $message->ID, 'read', true );
														if ( ! empty( $read ) ) {
															echo '&nbsp;&nbsp;';
															echo '<i class="fa fa-envelope-open" aria-hidden="true"></i> ';
															$users_see = [];
															foreach ( $read as $p ) {
																$po = get_user_by( 'id', $p );
																if ( is_object( $po ) ) {
																	$users_see[] = $po->display_name;
																}
															}
															if ( ! empty( $users_see ) ) {
																echo esc_html__( 'Seen by:', 'projectopia-core' ) . ' ' . esc_html( implode( ', ', $users_see ) );
															}
														}
														$piping = get_post_meta( $message->ID, 'piping', true );
														if ( ! empty( $piping ) ) {
															echo ' - ' . esc_html__( 'Sent via email', 'projectopia-core' );
														}
														?>
													</div>
												</div>	
												<?php if ( ! empty( $system ) ) {
													echo '</div>';
												} ?>							
											<?php } ?>	
										</div>
									</div>
								</div>
							</div>
						<?php } else {
							$conversations = pto_fetch_conversations( $user->ID );
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
												'post_type' => 'cqpim_messages',
												'posts_per_page' => -1,
												'post_status' => 'private',
												'meta_query' => array(
													array(
														'key'       => 'conversation_id',
														'value'     => $id,
														'compare'   => '=',
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
														$recip = get_user_by( 'id', $member );
														if ( empty( $recip ) || ! is_object( $recip ) || $recip->ID == get_current_user_id() ) {
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
</div>

<?php }