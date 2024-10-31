<?php
/**
 * Function to show the project updates inside post meta box.
 * 
 * @since 5.0.0
 * 
 * @param Object $post This post object.
 * 
 * @return void
 */
function pto_project_updates_metabox_callback( $post ) {

	$project_progress = get_post_meta( $post->ID, 'project_progress', true );

	//Group the project updates as per day.
	$project_updates = [];

	//error_log( print_r( $project_progress, true ) );

	foreach ( (array) $project_progress as $project_update ) {

		//If project update date is empty then continue.
		if ( empty( $project_update['date'] ) ) {
			continue;
		}

		$update_timestamp = $project_update['date'];
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

		//Set avatar.
		if ( empty( $avatar ) ) {
			$profile_avatar = get_avatar(
				pto_get_user_id_by_display_name( $project_update['by'] ),
				40,
				'',
				false,
				[
					'force_display' => true,
					'class'         => 'img-fluid',
				]
			);

			if ( empty( $profile_avatar ) ) {
				$profile_avatar = sprintf(
					'<img src="%s" alt="%s" class="img-fluid" />',
					PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
					esc_html( $project_update['by'] )
				);
			}
		}

		$project_updates[ $date_key ]['date'] = $update_date;
	
		//Group updates day wise.
		$project_updates[ $date_key ][] = [
			'member_name'    => $project_update['by'],
			'avatar'         => $profile_avatar,
			'time'           => $update_time,
			'update_message' => $project_update['update'],
			'timestamp'      => $update_timestamp,
		];
	}

	pto_project_updates_element( $project_updates );
}
