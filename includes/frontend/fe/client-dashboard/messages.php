<?php
$user = wp_get_current_user();
$client_logs = get_post_meta($assigned, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	'page' => __('Client Dashboard Messages Page', 'projectopia-core'),
);
update_post_meta($assigned, 'client_logs', $client_logs);
$users = pto_retrieve_messageble_users($user->ID);
$p_search = array();
if ( ! empty($users) ) {
	foreach ( $users as $key => $suser ) {
		$p_search[] = array(
			'id'   => $key,
			'name' => $suser,
		);
	}
}   
$p_search = wp_json_encode($p_search);
$conversations = pto_fetch_conversations($user->ID);
$text = __('Search for team member name...', 'projectopia-core');  
$conversation = isset($_GET['conversation']) ? sanitize_text_field(wp_unslash($_GET['conversation'])) : '';
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
$conversation = get_posts($args); 
$conversation = isset($conversation[0]) ? $conversation[0] : ''; 
?>
<br />
<div>
	<div id="cqpim-new-message" class="cqpim_block" style="display:none">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-envelope-open font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('New Conversation', 'projectopia-core'); ?></span>
			</div>
			<div class="actions">
				<button id="send" style="margin-right:10px" class="cqpim_button cqpim_small_button border-green font-green rounded_2 op"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('Send', 'projectopia-core'); ?></button>	
				<button id="cancel" class="cqpim_button cqpim_small_button border-red font-red rounded_2 op"><i class="fa fa-times" aria-hidden="true"></i> <?php esc_html_e('Cancel', 'projectopia-core'); ?></button>					
			</div>
		</div>
		<form id="cqpim-create-new-message">
			<p><span class="cqpim-heading"><?php esc_html_e('Recipients:', 'projectopia-core'); ?></span><input type="text" id="to" name="to" placeholder="<?php echo esc_attr( $text ); ?>" /></p>
			<div class="clear"></div>
			<p><span class="cqpim-heading"><?php esc_html_e('Subject:', 'projectopia-core'); ?></span><input type="text" id="subject" name="subject" /></p>
			<p><span class="cqpim-heading"><?php esc_html_e('Message:', 'projectopia-core'); ?></span><textarea id="message" name="message" /></textarea></p>
			<div class="clear"></div>
			<p><span class="cqpim-heading"><?php esc_html_e('Attachments:', 'projectopia-core'); ?></span>
				<input type="file" class="cqpim-file-upload" name="async-upload" id="attachments" multiple />
				<div id="upload_attachments"></div>
				<div class="clear"></div>
				<input type="hidden" name="image_id" id="upload_attachment_ids">
				<input type="hidden" name="action" value="image_submission">						
			</p>
			<div class="clear"></div>
			<div id="message-ajax-response"></div>
		</form>			
	</div>
	<div id="cqpim-reply-message" class="cqpim_block" style="display:none">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-envelope-open font-green-sharp" aria-hidden="true"></i>
				<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('Reply to Conversation', 'projectopia-core'); ?></span>
			</div>
			<div class="actions">
				<button id="send-reply" style="margin-right:10px" class="cqpim_button cqpim_small_button border-green font-green rounded_2 op" data-conversation="<?php echo isset($conversation->ID) ? esc_attr( $conversation->ID ) : ''; ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('Send', 'projectopia-core'); ?></button>	
				<button id="cancel-reply" class="cqpim_button cqpim_small_button border-red font-red rounded_2 op"><i class="fa fa-times" aria-hidden="true"></i> <?php esc_html_e('Cancel', 'projectopia-core'); ?></button>					
			</div>
		</div>
		<form id="rcqpim-create-new-message">
			<p><span class="cqpim-heading"><?php esc_html_e('Message:', 'projectopia-core'); ?></span><textarea id="rmessage" name="message" /></textarea></p>
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
	<div class="cqpim_block">
		<div class="cqpim_block_title">
			<div class="caption">
				<i class="fa fa-envelope-open font-green-sharp" aria-hidden="true"></i>
				<?php if ( ! empty($conversation) ) { ?>
					<span class="caption-subject font-green-sharp sbold"><?php echo esc_html( str_replace('Private: ', '', get_the_title($conversation->ID)) ); ?></span>
				<?php } else { ?>
					<span class="caption-subject font-green-sharp sbold"><?php esc_html_e('My Messages', 'projectopia-core'); ?></span>
				<?php } ?>
			</div>
			<div class="actions">
				<button id="send-message" class="cqpim_button cqpim_small_button border-green font-green rounded_2 sbold op"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?php esc_html_e('New Conversation', 'projectopia-core'); ?></button>
			</div>
		</div>
		<?php if ( ! empty($_GET['convdeleted']) ) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The conversation was successfully deleted.', 'projectopia-core'); ?>
			</div>
		<?php } ?>
		<?php if ( ! empty($_GET['convcreated']) ) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The conversation was successfully created.', 'projectopia-core'); ?>
			</div>
		<?php } ?>
		<?php if ( ! empty($_GET['convleft']) ) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('You have been removed from the conversation.', 'projectopia-core'); ?>
			</div>
		<?php } ?>
		<?php if ( ! empty($_GET['convremoved']) ) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The user has been removed.', 'projectopia-core'); ?>
			</div>
		<?php } ?>
		<?php if ( ! empty($_GET['convadded']) ) { ?>
			<br />
			<div class="malert cqpim-alert cqpim-alert-success">
				<i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('The user has been added.', 'projectopia-core'); ?>
			</div>
		<?php } ?>
		<?php if ( empty($conversations) ) { ?>
			<div id="cqpim-no-messages">
				<br />
				<p><?php esc_html_e('You do not have any messages.', 'projectopia-core'); ?></p>				
			</div>
		<?php } else { ?>
			<?php $conversation = isset($_GET['conversation']) ? sanitize_text_field(wp_unslash($_GET['conversation'])) : '';
			if ( ! empty($conversation) ) {
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
				$conversation = get_posts($args); 
				$conversation = $conversation[0]; 
				$conversation_id = get_post_meta($conversation->ID, 'conversation_id', true);
				$recipients = get_post_meta($conversation->ID, 'recipients', true);
				if ( ! in_array($user->ID, $recipients) ) {
					echo '<h1>' . esc_html__('ACCESS DENIED', 'projectopia-core') . '</h1>';
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
				$messages = get_posts($args); ?>
				<input type="text" id="cqpim-title-editable-field" value="<?php  echo esc_attr( get_the_title($conversation->ID) ); ?>" />
				<input type="hidden" id="jq-user-id" value="<?php echo esc_attr( $user->ID ); ?>" />
				<input type="hidden" id="jq-conv-id" value="<?php echo esc_attr( $conversation->ID ); ?>" />
				<div id="cqpim-messaging-buttons">
					<button id="cqpim-convo-reply" class="cqpim_button cqpim_small_button font-white bg-green rounded_2 op right"><i class="fa fa-reply" aria-hidden="true"></i><span class="desktop_only"> <?php esc_html_e('Reply', 'projectopia-core'); ?></span></button>
					<button id="cqpim-convo-leave" class="cqpim_button cqpim_small_button font-white bg-amber rounded_2 op right"><i class="fa fa-sign-out" aria-hidden="true"></i><span class="desktop_only"> <?php esc_html_e('Leave', 'projectopia-core'); ?></span></button>
					<?php if ( $user->ID == $conversation->post_author || current_user_can('cqpim_do_all') ) { ?>
						<button id="cqpim-convo-delete" class="cqpim_button cqpim_small_button font-white bg-red rounded_2 op right"><i class="fa fa-trash" aria-hidden="true"></i><span class="desktop_only"> <?php esc_html_e('Delete', 'projectopia-core'); ?></span></button>
					<?php } ?>
					<div class="clear"></div>
				</div>
				<div id="delete-confirm" style="display:none" title="<?php esc_html_e('Delete Conversation', 'projectopia-core'); ?>">
					<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php esc_html_e('This conversation and all messages will be permanently deleted. Are you sure?', 'projectopia-core'); ?></p>
				</div>
				<div id="leave-confirm" style="display:none" title="<?php esc_html_e('Leave Conversation', 'projectopia-core'); ?>">
					<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php esc_html_e('Are you sure you want to leave the conversation?', 'projectopia-core'); ?></p>
				</div>
				<div id="remove-confirm" style="display:none" title="<?php esc_html_e('Remove User', 'projectopia-core'); ?>">
					<p><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php esc_html_e('Choose which user you would like to remove and click Remove User.', 'projectopia-core'); ?></p>
					<select id="cqpim-remove-user">
						<?php foreach ( $recipients as $recipient ) { 
							$recip = get_user_by('id', $recipient); ?>
							<option value="<?php echo esc_attr( $recip->ID ); ?>"><?php echo esc_html( $recip->display_name ); ?></option>
						<?php } ?>
					</select>
				</div>
				<div id="add-confirm" style="display:none" title="<?php esc_html_e('Add User', 'projectopia-core'); ?>">
					<p><i class="fa fa-check" aria-hidden="true"></i> <?php esc_html_e('Search for a user to add to the conversation.', 'projectopia-core'); ?></p>
					<input type="text" id="ato" />
				</div>
				<div id="cqpim-dmessage-container">
					<?php foreach ( $messages as $message ) { 
						$sender = get_post_meta($message->ID, 'sender', true);
						$system = get_post_meta($message->ID, 'system', true);
						$sender_obj = get_user_by('id', $sender);
						if ( $user->ID != $sender ) {
							$read = get_post_meta($message->ID, 'read', true);
							if ( ! empty($read) && is_array($read) && ! in_array($user->ID, $read) ) {
								$read[] = $user->ID;
							}
							update_post_meta($message->ID, 'read', $read);
						}
						$update = get_post_meta($message->ID, 'message', true);
						$stamp = get_post_meta($message->ID, 'stamp', true);
						if ( $sender == $user->ID ) {
							$class = ' own';
						} else {
							$class = '';
						}
						if ( ! empty($system) ) {
							$class = ' system';
						}
						if ( ! empty($system) ) {
							echo '<div style="text-align:center; clear:both">';
						} ?>
						<div class="cqpim-dmessage-bubble<?php echo esc_attr( $class ); ?>">
							<div class="cqpim-messagelist-avatar" style="float:right; margin-left:20px;">
								<?php echo get_avatar( $sender_obj->ID, 40, '', $sender_obj->display_name, array( 'force_display' => true )); ?>
							</div>
							<?php echo wp_kses_post( $update ); ?>
							<div class="clear"></div>
							<?php $all_attached_files = get_attached_media( '', $message->ID ); 
							if ( ! empty($all_attached_files) ) { ?>
								<div class="cqpim-dmessage-attachments<?php echo esc_attr( $class ); ?>">
									<div><strong><i class="fa fa-paperclip" aria-hidden="true"></i> <?php esc_html_e('Attachments', 'projectopia-core'); ?></strong></div>
									<ul>
										<?php foreach ( $all_attached_files as $file ) { ?>
											<li><a href="<?php echo esc_url( $file->guid ); ?>" target="_blank"><?php echo esc_html( $file->post_title ); ?></a> | <i class="fa fa-download" aria-hidden="true"></i>  <a href="<?php echo esc_url( $file->guid ); ?>" download ><?php esc_html_e('Download', 'projectopia-core'); ?></a></li>
										<?php } ?>
									</ul>
								</div>
							<?php } ?>
							<div class="cqpim-dmessage-date<?php echo esc_attr( $class ); ?>">
								<i class="fa fa-paper-plane" aria-hidden="true"></i>
								<?php 
								$date = wp_date(get_option('cqpim_date_format') . ' H:i', $stamp);
								/* translators: %1$s: Sender Name, %2$s: Date */
								printf(esc_html__('Posted by %1$s on %2$s', 'projectopia-core'), esc_html($sender_obj->display_name), esc_html($date));
								$read = get_post_meta($message->ID, 'read', true);
								if ( ! empty($read) ) {
									echo '&nbsp;&nbsp;';
									echo '<i class="fa fa-envelope-open" aria-hidden="true"></i> ';
									esc_html_e('Seen by:', 'projectopia-core') . ' ';
									$count = count($read);
									$i = 0;
									foreach ( $read as $p ) {
										$i++;
										$po = get_user_by('id', $p);
										echo ' ' . esc_html( $po->display_name );
										if ( $i != $count ) { echo ','; }
									}
								}
								$piping = get_post_meta($message->ID, 'piping', true);
								if ( ! empty($piping) ) {
									echo ' - ' . esc_html__('Sent via email', 'projectopia-core');
								}
								?>
							</div>
						</div>	
						<?php if ( ! empty($system) ) {
							echo '</div>';
						} ?>							
					<?php } ?>	
				</div>
			<?php } else { ?>
				<table class="dataTable datatable_style milestones" id="front_milestones_table">
					<thead>
						<tr>
							<th><?php esc_html_e('Subject', 'projectopia-core'); ?></th>
							<th><?php esc_html_e('Created', 'projectopia-core'); ?></th>
							<th><?php esc_html_e('Updated', 'projectopia-core'); ?></th>
							<th><?php esc_html_e('Members', 'projectopia-core'); ?></th>
							<th style="display:none"><?php esc_html_e('Stamp', 'projectopia-core'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $conversations as $conversation ) { 
							$n_id = get_post_meta($conversation->ID, 'conversation_id', true);
							$created = get_post_meta($conversation->ID, 'created', true);
							$updated = get_post_meta($conversation->ID, 'updated', true);
							$update_user = get_user_by('id', $updated['by']);
							$timestamp = strtotime(gmdate('Y-m-d H:i', $updated['at']));
							$update_user = $update_user->display_name;
							$members = get_post_meta($conversation->ID, 'recipients', true);
							$args = array(
								'post_type'      => 'cqpim_messages',
								'posts_per_page' => -1,
								'post_status'    => 'private',
								'meta_query'     => array(
									array(
										'key'     => 'conversation_id',
										'value'   => $n_id,
										'compare' => '=',
									),
								),
							);
							$messages = get_posts($args);
							$read_val = true;
							foreach ( $messages as $message ) {
								$read = get_post_meta($message->ID, 'read', true);
								if ( ! in_array($user->ID, $read) ) {
									$read_val = false;
								}
							}
							?>
							<tr <?php if ( empty($read_val) ) { echo ' class="cqpim-unread"'; } ?>>
								<td><?php if ( empty($read_val) ) { echo '<i class="fa fa-envelope" aria-hidden="true"></i> '; } else { echo '<i class="fa fa-envelope-open" aria-hidden="true"></i> '; } ?>&nbsp;&nbsp;<a href="<?php echo esc_url( get_the_permalink($client_dash) ) . '?pto-page=messages&conversation=' . esc_attr( $n_id ); ?>"><?php echo esc_html( str_replace('Private: ','', get_the_title($conversation->ID)) ); ?></a></td>
								<td><?php echo esc_html( wp_date(get_option('cqpim_date_format') . ' H:i', $created) ); ?></td>
								<td><?php echo esc_html( wp_date(get_option('cqpim_date_format') . ' H:i', $updated['at']) ); ?></td>
								<td>
									<?php foreach ( $members as $member ) {
										$recip = get_user_by('id', $member);
										echo '<div class="cqpim-messagelist-avatar">';
										echo get_avatar( $recip->ID, 80, '', $recip->display_name, array( 'force_display' => true ));
										echo '</div>';
									} ?>
								</td>
								<td style="display:none"><?php echo esc_html( $timestamp ); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		<?php } ?>
	</div>
</div>