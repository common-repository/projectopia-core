<?php
function pto_task_messages_metabox_callback( $post ) {
	wp_nonce_field( 'task_messages_metabox', 'task_messages_metabox_nonce' );

	$messages = get_post_meta( $post->ID, 'task_messages', true );

	//error_log( print_r( $project_messages, true ) );
	//Group the project updates as per day.
	$project_messages_details = [];

	if ( ! empty( $messages ) ) {
		$project_messages = array_reverse( $messages ); 
		
		foreach ( (array) $project_messages as $project_id => $project_message ) {

			//If project update date is empty then continue.
			if ( empty( $project_message['date'] ) ) {
				continue;
			}

			$update_timestamp = $project_message['date'];
			$update_date = $update_time = '';

			// Check if date is unix timestemps.
			if ( ! is_numeric( $update_timestamp ) ) {
				$update_timestamp = strtotime( $update_timestamp );
			}
			
			$date_stamp = gmdate( 'Y-m-d', $update_timestamp );
			$date_key = strtotime( $date_stamp . ' 00:00:00' );
				
			$update_date = gmdate( 'M d Y', $update_timestamp );
			$update_time = wp_date( 'h:i A', $update_timestamp );

			//Calculate date and time line for updates.
			$today = new DateTime( 'today' );
			$modified_date = new DateTime( gmdate( 'Y-m-d', $update_timestamp ) );
			$today->setTime( 0, 0, 0 );
			$modified_date->setTime( 0, 0, 0 );

			//Make date label for updates group.
			if ( $today->diff( $modified_date )->days === 0 ) {
				$update_date = __( 'Today', 'projectopia-core' );
			} elseif ( $today->diff( $modified_date )->days === -1 ) {
				$update_date = __( 'Yesterday', 'projectopia-core' );
			}

			$user = get_user_by( 'id', $project_message['author'] );

			//Set avatar.
			if ( empty( $avatar ) ) {
				$profile_avatar = get_avatar( $user->ID, 40, '', false, [
					'force_display' => true,
					'class'         => 'img-fluid',
				] );

				if ( empty( $profile_avatar ) ) {
					$profile_avatar = sprintf(
						'<img src="%s" alt="%s" class="img-fluid" />',
						esc_url( PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png' ),
						esc_html( $user->display_name )
					);
				}
			}

			$delete_link = sprintf(
				'<button class="delete_message btn" data-id="%s" style="margin-top: -5px;">
					<img src="%s" alt="delete" class="img-fluid"/>
				</button>',
				$project_id,
				esc_url( PTO_PLUGIN_URL .'/assets/admin/img/trash.svg' )
			);

			if ( ! empty( $project_message['message'] ) ) {
				array_push( $project_message['changes'], $project_message['message'] );
			}

			$project_messages_details[ $date_key ]['date'] = $update_date;

			//Group updates day wise.
			$project_messages_details[ $date_key ][] = [
				'member_name'    => esc_html( $project_message['by'] ),
				'avatar'         => $profile_avatar,
				'time'           => $update_time,
				'email'          => $user->user_email,
				'update_message' => $project_message['changes'],
				'delete_btn'     => $delete_link,
				'timestamp'      => $update_timestamp,
			];
		}
	}
		
	pto_project_updates_element( $project_messages_details ); ?>
	<hr>
	<div class="form-group mt-3">
		<label for="add_task_message"><?php esc_html_e( 'Add Message', 'projectopia-core' ); ?></label>
		<div class="input-group">
			<textarea id="add_task_message" class="form-control input pto-textarea pto-h-200" name="add_task_message"></textarea>
		</div>
	</div>
	<button class="s_button piaBtn right mt-1"><?php esc_html_e( 'Add Message', 'projectopia-core' ); ?></button>
	<div class="clear"></div>
	<?php
}