<?php
function pto_client_tickets_metabox_callback( $post ) {
 	wp_nonce_field( 'client_tickets_metabox', 'client_tickets_metabox_nonce' );

	$client_details = get_post_meta( $post->ID, 'client_details', true );
	$ticket_assignee = isset( $client_details['ticket_assignee'] ) ? $client_details['ticket_assignee'] : '';
	?>
	<div class="form-group">
		<label for="ticket_assignee"><?php esc_html_e( 'Default Ticket Assignee:', 'projectopia-core' ); ?> </label>
		<div class="input-group">
			<select id="ticket_assignee" name="ticket_assignee" class="form-control input">
				<option value=""><?php esc_html_e( 'No Default (Tickets will be unassigned until claimed)', 'projectopia-core' ); ?> </option>
				<?php
				$args = array(
					'post_type'      => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status'    => 'any',
				);
				$team_members = get_posts( $args );
				foreach ( $team_members as $member ) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					$user_id = isset( $team_details['user_id'] ) ? $team_details['user_id'] : '';
					if ( ! empty( $user_id ) ) {
						$user = get_user_by( 'id', $user_id );
						$caps = $user->allcaps;
						foreach ( $caps as $key => $cap ) {
							if ( $key == 'edit_cqpim_supports' && $cap == 1 ) {
								echo '<option value="' . esc_attr( $member->ID ) . '" ' . selected( $member->ID, $ticket_assignee, false ) . '>' . esc_html( $team_details['team_name'] ) . '</option>';
							}
						}
					}
				}
				?>
			</select>
		</div>
	</div>
	<?php
	$args = array(
		'post_type'      => 'cqpim_support',
		'posts_per_page' => -1,
		'post_status'    => 'private',
		'meta_key'       => 'ticket_client',
		'meta_value'     => $post->ID,
	);
	$tickets = get_posts( $args );
	if ( $tickets ) {
		echo '<div class="card p-0 m-0"><table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_clientstickets_table" data-sort="[[ 0, \'desc\' ]]" data-rows="5">';
		echo '<thead>';
		echo '<tr><th>' . esc_html__('Ticket ID', 'projectopia-core') . '</th><th>' . esc_html__('Title', 'projectopia-core') . '</th><th>' . esc_html__('Assigned To', 'projectopia-core') . '</th><th>' . esc_html__('Priority', 'projectopia-core') . '</th><th>' . esc_html__('Created', 'projectopia-core') . '</th><th>' . esc_html__('Updated', 'projectopia-core') . '</th><th>' . esc_html__('Status', 'projectopia-core') . '</th></tr>';
		echo '</thead>';
		foreach ( $tickets as $ticket ) {
				$ticket_author = $ticket->post_author;
				$author_details = get_user_by('id', $ticket_author);
				$ticket_owner = get_post_meta($ticket->ID, 'ticket_owner', true);
				$owner_details = get_post_meta($ticket_owner, 'team_details', true);
				$owner_name = isset( $owner_details['team_name'] ) ? $owner_details['team_name'] : esc_html__('Not Assigned', 'projectopia-core');
				$ticket_client = get_post_meta($ticket->ID, 'ticket_client', true);
				$client_details = get_post_meta($ticket_client, 'client_details', true);
				$client_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
				$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
				$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
				$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
				if ( is_numeric( $ticket_updated ) ) {
					$ticket_updated = wp_date(get_option('cqpim_date_format') . ' H:i', $ticket_updated);
				}

				if ( empty( $ticket_status ) ) {
					$status = '<span class="status closed">' . esc_html__( 'Unpublished', 'projectopia-core' ) . '</span>';
				} else {
					$support_status = get_option( 'support_status' );
					$ticket_key = array_search( $ticket_status, $support_status['key'] );
					$ticket_status_new = $support_status['value'][ $ticket_key ];
					$ticket_status_color = $support_status['color'][ $ticket_key ];
					$status = '<span class="status" style="background-color:' .esc_attr( pto_adjust_color_brightness( $ticket_status_color, 0.8 ) ) .' !important;color:'. esc_attr( $ticket_status_color ) . ' !important;">' . esc_html( ucwords( $ticket_status_new ) ) . '</span>';
				}
			  
				if ( empty( $ticket_priority ) ) {
					$priority = '<span class="status closed">' . esc_html__( 'Unpublished', 'projectopia-core' ) . '</span>';
				} else {
					$support_ticket_priorities = get_option( 'support_ticket_priorities' );
					if ( ! empty( $support_ticket_priorities[ $ticket_priority ] ) ) {
						$color_code = $support_ticket_priorities[ $ticket_priority ];
						$priority = '<span style="background-color:' . pto_adjust_color_brightness( $color_code, 0.8 ) .' !important;color:'. $color_code . ' !important;" class="status">' . ucwords( $ticket_priority ) . '</span>';
					} else {
						$priority = '<span class="status off">' . ucwords( $ticket_priority ) . '</span>';
					}
				}

				echo '<tr>';
				echo '<td>' . esc_html( $ticket->ID ) . '</td>';
				echo '<td><a href="' . esc_url( get_edit_post_link($ticket->ID) ) . '">' . esc_html( $ticket->post_title ) . '</a></td>';
				echo '<td>' . esc_html( $owner_name ) . '</td>';
				echo '<td>' . wp_kses_post( $priority ) . '</td>';
				echo '<td>' . esc_html( get_the_date(get_option('cqpim_date_format') . ' H:i', $ticket->ID) ) . '</td>';
				echo '<td>' . esc_html( $ticket_updated ) . '</td>';
				echo '<td>' . wp_kses_post( $status ) . '</td>';
				echo '</tr>';               
		}
		echo '</table></div>';
	} else {
		echo '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('This client does not have any support tickets', 'projectopia-core') . '</div>';
	}       
}

add_action( 'save_post_cqpim_client', 'save_pto_client_tickets_metabox_data' );
function save_pto_client_tickets_metabox_data( $post_id ) {
	if ( ! isset( $_POST['client_tickets_metabox_nonce'] ) ) {
	    return $post_id;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['client_tickets_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'client_tickets_metabox' ) ) {
	    return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	    return $post_id;
	}

	if ( isset( $_POST['ticket_assignee'] ) ) {
		$client_details = get_post_meta( $post_id, 'client_details', true );
		$ticket_assignee = sanitize_text_field( wp_unslash( $_POST['ticket_assignee'] ) );
		$client_details['ticket_assignee'] = $ticket_assignee;
		update_post_meta( $post_id, 'client_details', $client_details );
	}
}