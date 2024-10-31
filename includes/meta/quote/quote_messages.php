<?php
function pto_quote_messages_metabox_callback( $post ) {
 	wp_nonce_field( 'quote_messages_metabox', 'quote_messages_metabox_nonce' );

	$quote_messages = get_post_meta( $post->ID, 'quote_messages', true );

	//error_log( print_r( $quote_messages, true ) );
	//Group the project updates as per day.
	$quote_messages_details = [];

	if ( ! empty( $quote_messages ) ) {
		$quote_messages = array_reverse( $quote_messages ); 
		
		foreach ( (array) $quote_messages as $quote_id => $quote_message ) {

			//If project update date is empty then continue.
			if ( empty( $quote_message['date'] ) ) {
				continue;
			}

			$update_timestamp = $quote_message['date'];
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

			$user = get_user_by( 'id', $quote_message['author'] );

			//Set avatar.
			if ( empty( $avatar ) ) {
				$profile_avatar = get_avatar( $user->ID, 40, '', false, [
					'force_display' => true,
					'class'         => 'img-fluid',
				] );

				if ( empty( $profile_avatar ) ) {
					$profile_avatar = sprintf(
						'<img src="%s" alt="%s" class="img-fluid" />',
						PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
						esc_html( $user->display_name )
					);
				}
			}

			$delete_link = sprintf(
				'<button class="delete_message btn" data-id="%s" style="margin-top: -5px;">
					<img src="%s" alt="delete" class="img-fluid"/>
				</button>',
				$quote_id,
				PTO_PLUGIN_URL .'/assets/admin/img/trash.svg'
			);

			$quote_messages_details[ $date_key ]['date'] = $update_date;

			//Group updates day wise.
			$quote_messages_details[ $date_key ][] = [
				'member_name'    => esc_html( $quote_message['by'] ),
				'avatar'         => $profile_avatar,
				'time'           => $update_time,
				'email'          => $user->user_email,
				'update_message' => $quote_message['message'],
				'delete_btn'     => $delete_link,
				'timestamp'      => $update_timestamp,
			];
		}
	}
		
	pto_project_updates_element( $quote_messages_details );
	
	?>

	<button id="add_message_trigger" class="piaBtn right mt-3"><?php esc_html_e( 'Send Message', 'projectopia-core' ); ?></button>
	<div class="clear"></div>
	<div id="add_message_container" style="display: none;">
		<div id="add_message">
			<div style="padding: 12px;">
				<h3><?php esc_html_e( 'Send Message', 'projectopia-core' ); ?></h3>
				<?php

				pto_generate_fields( array(
					'type'  => 'hidden',
					'id'    => 'message_who',
					'value' => 'admin',
				) );

				pto_generate_fields( array(
					'type'  => 'hidden',
					'id'    => 'add_message_visibility',
					'value' => 'all',
				) );

				echo '<p><strong>' . esc_html__( 'Message Notifications', 'projectopia-core' ) . '</strong></p>';

				pto_generate_fields( array(
					'type'  => 'checkbox',
					'id'    => 'send_to_client',
					'label' => __( 'Send a notification to the client', 'projectopia-core' ),
				) );

				pto_generate_fields( array(
					'type'  => 'textarea',
					'id'    => 'add_message_text',
					'label' => __( 'Message', 'projectopia-core' ),
				) );

				?>

				<div id="message_messages"></div>
				<button id="add_message_ajax" class="piaBtn right mt-1"><?php esc_html_e( 'Send Message', 'projectopia-core' ); ?></button>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php
}