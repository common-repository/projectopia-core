<?php
/**
 * Function to callback the project details metabox
 */
function pto_project_details_metabox_callback( $post ) {
	wp_nonce_field( 'project_details_metabox', 'project_details_metabox_nonce' );

	$project_details = get_post_meta($post->ID, 'project_details', true);
	$project_elements = get_post_meta($post->ID, 'project_elements', true);
	$project_extras = get_post_meta($post->ID, 'project_extras', true);
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contract = get_post_meta($client_id, 'client_contract', true);
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
	$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$project_contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : '';
	$terms = get_post($project_contract);
	$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$deposit_amount = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$pm_name = isset($project_details['pm_name']) ? $project_details['pm_name'] : '';
	$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$deposit_invoice_id = isset($project_details['deposit_invoice_id']) ? $project_details['deposit_invoice_id'] : '';
	$completion_invoice_id = isset($project_details['completion_invoice_id']) ? $project_details['completion_invoice_id'] : '';
	$deposit_invoice_details = get_post_meta($deposit_invoice_id, 'invoice_details', true);
	$completion_invoice_details = get_post_meta($completion_invoice_id, 'invoice_details', true);
	$deposit_sent = isset($deposit_invoice_details['sent']) ? $deposit_invoice_details['sent'] : '';
	$deposit_paid = isset($deposit_invoice_details['paid']) ? $deposit_invoice_details['paid'] : '';
	$completion_sent = isset($completion_invoice_details['sent']) ? $completion_invoice_details['sent'] : '';
	$completion_paid = isset($completion_invoice_details['paid']) ? $completion_invoice_details['paid'] : '';
	$contract_link = get_the_permalink($post->ID) . '?pto-page=contract-print';
	$summary_link = get_the_permalink($post->ID) . '?page=summary&sub=updates';
	$contract_status = get_post_meta($post->ID, 'contract_status', true); 
	if ( ! empty($client_contact) ) {
		if ( ! empty($client_details['user_id']) && $client_details['user_id'] == $client_contact ) {
			$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
			$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
		} else {
			$client_contact_name = isset($client_contacts[ $client_contact ]['name']) ? $client_contacts[ $client_contact ]['name'] : '';
			$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
			$client_telephone = isset($client_contacts[ $client_contact ]['telephone']) ? $client_contacts[ $client_contact ]['telephone'] : '';
			$client_email = isset($client_contacts[ $client_contact ]['email']) ? $client_contacts[ $client_contact ]['email'] : '';        
		}
	} else {
		$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
		$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
		$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
		$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';      
	}
	$project_contributors = get_post_meta($post->ID, 'project_contributors', true);?>	

	<?php if ( current_user_can('cqpim_edit_project_dates') ) { ?>
		<button id="edit-quote-details" class="piaBtn btn btn-primary btn-block btn-orange mb-2"><?php esc_html_e('Edit Project Details / Dates', 'projectopia-core'); ?></button>
		<div class="clear"></div>
	<?php } ?>

	<button class="piaBtn btn btn-primary btn-block mt-0 save mb-4">
		<?php esc_html_e('Update Project', 'projectopia-core'); ?>
	</button>

	<!-- Project Information  -->
	<?php	
	if ( ! empty($quote_ref) ) { 
		$checked = get_post_meta($post->ID, 'contract_status', true); ?>
		<div class="pto-project-information mt-2">
			<h5 class="mb-2"> <?php esc_html_e('Project Information', 'projectopia-core'); ?> </h5>

			<div class="d-flex mb-2">
				<figure class="mr-2">
					<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/fakeicon.png' ); ?>" />
				</figure>
				<div class="content">
					<p><?php esc_html_e('Project Reference Id:', 'projectopia-core'); ?></p>
					<h6> <?php echo esc_html( $quote_ref ); ?> </h6>
				</div>
			</div>

			<?php if ( ! empty($checked) && $checked == 1 ) { ?>
				<div class="d-flex mb-2">
					<figure class="mr-2">
						<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/fakeicon.png' ); ?>" />
					</figure>
					<div class="content">
						<p><?php esc_html_e('Contract Terms:', 'projectopia-core'); ?></p>
						<h6> <a href="<?php echo esc_url( get_edit_post_link($project_contract) ); ?>" target="_blank"><?php echo esc_html( $terms->post_title ); ?></a> </h6>
					</div>
				</div>
			<?php } ?>

			<?php

			if ( $start_date ) {
				if ( is_numeric($start_date ) ) { 
					$start_date = wp_date(get_option('cqpim_date_format'), $start_date);
				} else {
					$start_date = $start_date;
				} ?>

				<div class="d-flex mb-2">
					<figure class="mr-2">
						<img class="bg-blue-sharp full-rounded" src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/clander.svg' ); ?>" />
					</figure>
					<div class="content">
						<p><?php esc_html_e('Start Date:', 'projectopia-core'); ?></p>
						<h6> <?php echo esc_html( $start_date ); ?></h6>
					</div>
				</div>

			<?php } ?>

			<?php
			if ( $finish_date ) { 
				if ( is_numeric( $finish_date ) ) {
					$finish_date = wp_date(get_option('cqpim_date_format'), $finish_date); 
				} else { 
					$finish_date = $finish_date; 
				}
			?>

				<div class="d-flex mb-2">
					<figure class="mr-2">
						<img class="bg-blue-sharp full-rounded" src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/clander.svg' ); ?>" />
					</figure>
					<div class="content">
						<p><?php esc_html_e('Launch Date:', 'projectopia-core'); ?></p>
						<h6> <?php echo esc_html( $finish_date ); ?></h6>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<!-- Project Manager details -->
	<?php echo '<h5>' . esc_html__('Project Manager', 'projectopia-core') . '</h5>';
	if ( ! is_array($project_contributors) ) {
		echo '<p class="underline"><strong>' . esc_html__('No project manager added, add one in the Team Members section.', 'projectopia-core') . '</strong></p>';
	} else {
		$i = 0;
		foreach ( $project_contributors as $key => $contributor ) {
			if ( ! empty( $contributor['pm'] ) && $contributor['pm'] == 1 ) {
				$i++;
				$team_details = get_post_meta( $contributor['team_id'], 'team_details', true );
				$team_name = isset( $team_details['team_name'] ) ? $team_details['team_name'] : '';
				if ( current_user_can( 'edit_cqpim_teams' ) ) {
					$team_url = get_edit_post_link( $contributor['team_id'] );
					$team_name = '<a href="' . $team_url . '" target="_blank">' . $team_name . '</a>';
				}
				$team_job = isset( $team_details['team_job'] ) ? $team_details['team_job'] : '';
				$team_email = isset( $team_details['team_email'] ) ? $team_details['team_email'] : '';
				$team_telephone = isset( $team_details['team_telephone'] ) ? $team_details['team_telephone'] : '';
				$user_id = isset( $team_details['user_id'] ) ? $team_details['user_id'] : '';
					
				//Set avatar.
				$profile_avatar = '';
				if ( empty( get_option( 'cqpim_disable_avatars') ) ) {
					$profile_avatar = get_avatar( $user_id, 40, '', false, [
						'force_display' => true,
						'class'         => 'img-fluid',
					]
					);

					if ( empty( $profile_avatar ) ) {
						$profile_avatar = sprintf(
							'<img src="%s" alt="%s" class="img-fluid" />',
							PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
							esc_html( $team_name  )
						);
					}
				}

				?>

				<div class="member-grid text-center mx-3" style="max-width: 12rem;">
					<div class="cqpim_gravatar"><?php echo wp_kses_post( $profile_avatar ); ?></div>
					<div class="team_details">
						<h5 class="member-name py-2 team_name"> <?php echo wp_kses_post( $team_name ); ?> </h5>
						<?php if ( ! empty( $team_telephone ) ) { ?>
							<p class="pb-1">
								<a href="tel:<?php echo esc_attr( $team_telephone ); ?>">
									<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/phone-fill.svg' ); ?>" class="icon img-fluid mr-2" />
									<?php echo esc_html( $team_telephone ); ?>
								</a>
							</p>
						<?php } ?>

						<p class="pb-1">
							<a href="mailto:<?php echo esc_attr( $team_email ); ?>">
								<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/mail-fill.svg' ); ?>" class="icon img-fluid mr-2" />
								<?php echo esc_html( $team_email ); ?>
							</a>
						</p>
					</div>
				</div>
				<?php
			}
		}
		if ( $i == 0 ) {
			echo '<p>' . esc_html__('No project manager added, add one in the Team Members section.', 'projectopia-core') . '</p>';
		}
		echo '<div class="clear"></div>';
	}

	// Project Client Details
	if ( ! empty($client_id) && current_user_can('cqpim_view_project_client_info') ) { ?>
		<div class="pto-project-client-details mt-4">
			<h5 class="mb-2"> <?php esc_html_e('Client Information', 'projectopia-core'); ?> </h5>
			<div class="d-flex mb-2">
				<figure class="mr-2">
					<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/fakeicon.png' ); ?>" />
				</figure>
				<div class="content">
					<p><?php esc_html_e('Company Name:', 'projectopia-core'); ?></p>
					<h6> <?php echo esc_html( $client_company_name ); ?> </h6>
				</div>
			</div>

			<div class="d-flex mb-2">
				<figure class="mr-2">
					<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/fakeicon.png' ); ?>" />
				</figure>
				<div class="content">
					<p><?php esc_html_e('Contact Name:', 'projectopia-core'); ?></p>
					<h6> <?php echo esc_html( $client_contact_name ); ?> </h6>
				</div>
			</div>

			<div class="d-flex mb-2">
				<figure class="mr-2">
					<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/mail-fill.svg' ); ?>" />
				</figure>
				<div class="content">
					<p> <?php esc_html_e('Email:', 'projectopia-core'); ?> </p>
					<h6> <?php printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $client_email ) ); ?> </h6>
				</div>
			</div>

			<div class="d-flex mb-2">
				<figure class="mr-2">
					<img src="<?php echo esc_url( PTO_PLUGIN_URL .'/assets/admin/img/phone-fill.svg' ); ?>" />
				</figure>
				<div class="content">
					<p><?php esc_html_e('Phone:', 'projectopia-core'); ?></p>
					<h6> <?php printf( '<a href="tel:%1$s">%1$s</a>', esc_attr( $client_telephone ) ); ?> </h6>
				</div>
			</div>

		</div>

	<?php } ?>

	<!-- Project Update model -->
	<input type="hidden" id="project_ref_for_basics" value="<?php echo esc_attr( $quote_ref ); ?>" />
	<div id="quote_basics_container" style="display:none">
		<div id="quote_basics">
			<div class="p-3">
				<h3><?php esc_html_e('Project Details', 'projectopia-core'); ?></h3>

				<div class="form-group">
					<label for="ptitle"><?php esc_html_e('Project Title:', 'projectopia-core'); ?></label>
					<div class="input-group">
						<input class="form-control input"
							type="text" name="ptitle"
							value="<?php echo esc_attr( $post->post_title ); ?>" />			
					</div>
				</div>

				<?php
				$args = array(
					'post_type'      => 'cqpim_client',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$clients = get_posts($args);

				?>

				<p><?php esc_html_e('Please choose a client to assign this project to. Leave this blank if you do not want to assign this project to a Client.', 'projectopia-core'); ?></p>
				<div class="form-group">
					<label for="quote_client"><?php esc_html_e('Choose a Client:', 'projectopia-core'); ?></label>
					<div class="input-group">
						<select class="quote_client_dropdown form-control input customSelect"
							name="quote_client" id="quote_client" required >';
							<?php
								echo '<option value="0">' . esc_html__('Select a Client... ', 'projectopia-core') . '</option>';
								foreach ( $clients as $client ) {
									setup_postdata($client);
									$client_details = get_post_meta($client->ID, 'client_details', true);
									$client_contact_name = isset( $client_details['client_contact'] ) ? $client_details['client_contact'] : '';
									$client_company_name = isset( $client_details['client_company'] ) ? $client_details['client_company'] : $client_contact_name;
									if ( $client_id == $client->ID ) {
										echo '<option value="' . esc_attr( $client->ID ) . '" selected="selected">' . esc_html( $client_company_name ) . '</option>';                  
									} else {
										echo '<option value="' . esc_attr( $client->ID ) . '">' . esc_html( $client_company_name ) . '</option>';
									}
								}                       
							?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="client_contact"><?php esc_html_e('Choose a Contact:', 'projectopia-core'); ?></label>

					<div class="input-group">
						<?php
							$quote_details = get_post_meta($post->ID, 'project_details', true);
							$client_id = isset($quote_details['client_id']) ? $quote_details['client_id'] : '';
							$client_details = get_post_meta($client_id, 'client_details', true);
							$client_contact = isset($quote_details['client_contact']) ? $quote_details['client_contact'] : '';
							$client_contacts = get_post_meta($client_id, 'client_contacts', true);
							$client_contacts = $client_contacts && is_array($client_contacts) ? $client_contacts : array();
							if ( ! empty($client_id) && ! empty($client_contact) ) { ?>
								<select name="client_contact" id="client_contact" class="form-control input customSelect">
									<option value=""><?php esc_html_e('Choose a Contact:', 'projectopia-core'); ?></option>
									<?php if ( ! empty($client_details['user_id']) && ! empty($client_contact) ) { ?>
										<option value="<?php echo esc_attr( $client_details['user_id'] ); ?>" <?php if ( $client_contact == $client_details['user_id'] ) { echo 'selected="selected"'; } ?>><?php echo esc_html( $client_details['client_contact'] ); ?> <?php esc_html_e('(Main Contact)', 'projectopia-core'); ?></option>
										<?php 
										foreach ( $client_contacts as $contact ) { ?>
											<option value="<?php echo esc_attr( $contact['user_id'] ); ?>" <?php if ( $client_contact == $contact['user_id'] ) { echo 'selected="selected"'; } ?>><?php echo esc_html( $contact['name'] ); ?></option>							
										<?php }
									}
									?>
								</select>					
							<?php } else { ?>
								<select name="client_contact" class="form-control input customSelect" id="client_contact" disabled >
									<option value=""><?php esc_html_e('Choose a Contact:', 'projectopia-core'); ?></option>
								</select>
							<?php } ?>
					</div>
				</div>

				<?php if ( ! $quote_ref ) {
					$quote_ref = $post->ID;
				} ?>

				<div class="form-group">
					<label for="quote_ref"><?php esc_html_e('Project Ref:', 'projectopia-core'); ?></label>
					<div class="input-group">
						<input class="form-control input"
							type="text" name="quote_ref"
							id="quote_ref"
							required
							value="<?php echo esc_attr( $quote_ref ); ?>" />			
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						<div class="form-group">
							<label for="start_date"><?php esc_html_e('Start Date:', 'projectopia-core'); ?></label>
							<div class="input-group">
								<input class="form-control input datepicker"
									type="text" name="start_date"
									id="start_date"
									value="<?php echo esc_attr( $start_date ); ?>" />			
							</div>
						</div>	
					</div>
					<div class="col-6">
						<div class="form-group">
							<label for="finish_date"><?php esc_html_e('Launch Date:', 'projectopia-core'); ?></label>
							<div class="input-group">
								<input class="form-control input datepicker"
									type="text" name="finish_date"
									id="finish_date"
									value="<?php echo esc_attr( $finish_date ); ?>" />			
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col">
						<div class="form-group">
							<label for="deposit_amount"><?php esc_html_e('Deposit Amount:', 'projectopia-core'); ?></label>
							<div class="input-group">
								<select id="deposit_amount" name="deposit_amount" class="form-control input customSelect">
									<option value=""><?php esc_html_e('Choose an Option', 'projectopia-core'); ?></option>			
									<option value="none" <?php if ( $deposit_amount == 0 ) { echo 'selected'; } ?>><?php esc_html_e('No Deposit Required', 'projectopia-core'); ?></option>
									<option value="10" <?php if ( $deposit_amount == 10 ) { echo 'selected'; } ?>><?php esc_html_e('10%', 'projectopia-core'); ?></option>
									<option value="20" <?php if ( $deposit_amount == 20 ) { echo 'selected'; } ?>><?php esc_html_e('20%', 'projectopia-core'); ?></option>
									<option value="30" <?php if ( $deposit_amount == 30 ) { echo 'selected'; } ?>><?php esc_html_e('30%', 'projectopia-core'); ?></option>
									<option value="40" <?php if ( $deposit_amount == 40 ) { echo 'selected'; } ?>><?php esc_html_e('40%', 'projectopia-core'); ?></option>
									<option value="50" <?php if ( $deposit_amount == 50 ) { echo 'selected'; } ?>><?php esc_html_e('50%', 'projectopia-core'); ?></option>
									<option value="60" <?php if ( $deposit_amount == 60 ) { echo 'selected'; } ?>><?php esc_html_e('60%', 'projectopia-core'); ?></option>
									<option value="70" <?php if ( $deposit_amount == 70 ) { echo 'selected'; } ?>><?php esc_html_e('70%', 'projectopia-core'); ?></option>
									<option value="80" <?php if ( $deposit_amount == 80 ) { echo 'selected'; } ?>><?php esc_html_e('80%', 'projectopia-core'); ?></option>
									<option value="90" <?php if ( $deposit_amount == 90 ) { echo 'selected'; } ?>><?php esc_html_e('90%', 'projectopia-core'); ?></option>
									<option value="100" <?php if ( $deposit_amount == 100 ) { echo 'selected'; } ?>><?php esc_html_e('100%', 'projectopia-core'); ?></option>
								</select>			
							</div>
						</div>
					</div>
				</div>
				<?php
				$contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : ''; 
				$default = get_option( 'default_contract_text' );
				$checked = get_option('enable_project_contracts');
				if ( ! empty($checked) ) { ?>
				<div class="row">
					<div class="col">
						<div class="form-group">
							<label for="default_contract_text"><?php esc_html_e('Terms & Conditions Template:', 'projectopia-core'); ?></label>
							<div class="input-group">
								<select name="default_contract_text" class="form-control input customSelect">
									<?php 
									$args = array(
										'post_type'      => 'cqpim_terms',
										'posts_per_page' => -1,
										'post_status'    => 'private',
									);
									$terms = get_posts($args);
									foreach ( $terms as $term ) {
										if ( ! empty( $contract ) ) {
											$default = $contract;
										}
										echo '<option value="' . esc_attr( $term->ID ) . '" ' . selected( $term->ID, $default, false ) . '>' . esc_html( $term->post_title ) . '</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<div id="basics-error"></div>
				<div class="mt-3 d-flex align-items-center justify-content-between">
					<button class="cancel-colorbox cancel-creation mt-0 piaBtn redColor" ><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
					<button class="save-basics mt-0 piaBtn right"><?php esc_html_e('Save', 'projectopia-core'); ?></button>
				</div>
			</div>
		</div>
	</div>

	<?php
}

/**
 * Function to callback the project status metabox.
 */
function pto_project_status_metabox_callback( $post ) {
	$project_details = get_post_meta($post->ID, 'project_details', true);
	$project_elements = get_post_meta($post->ID, 'project_elements', true);
	$project_extras = get_post_meta($post->ID, 'project_extras', true);
	$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
	$client_contacts = get_post_meta($client_id, 'client_contacts', true);
	$client_contract = get_post_meta($client_id, 'client_contract', true);
	$client_contact = isset($project_details['client_contact']) ? $project_details['client_contact'] : '';
	$client_details = get_post_meta($client_id, 'client_details', true);
	$client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
	$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
	$client_telephone = isset($client_details['client_telephone']) ? $client_details['client_telephone'] : '';
	$client_email = isset($client_details['client_email']) ? $client_details['client_email'] : '';
	$quote_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
	$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
	$project_contract = isset($project_details['default_contract_text']) ? $project_details['default_contract_text'] : '';
	$terms = get_post($project_contract);
	$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$deposit_amount = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
	$confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$pm_name = isset($project_details['pm_name']) ? $project_details['pm_name'] : '';
	$sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$deposit_invoice_id = isset($project_details['deposit_invoice_id']) ? $project_details['deposit_invoice_id'] : '';
	$completion_invoice_id = isset($project_details['completion_invoice_id']) ? $project_details['completion_invoice_id'] : '';
	$deposit_invoice_details = get_post_meta($deposit_invoice_id, 'invoice_details', true);
	$completion_invoice_details = get_post_meta($completion_invoice_id, 'invoice_details', true);
	$deposit_sent = isset($deposit_invoice_details['sent']) ? $deposit_invoice_details['sent'] : '';
	$deposit_paid = isset($deposit_invoice_details['paid']) ? $deposit_invoice_details['paid'] : '';
	$completion_sent = isset($completion_invoice_details['sent']) ? $completion_invoice_details['sent'] : '';
	$completion_paid = isset($completion_invoice_details['paid']) ? $completion_invoice_details['paid'] : '';
	$contract_link = get_the_permalink($post->ID) . '?pto-page=contract-print';
	$summary_link = get_the_permalink($post->ID) . '?page=summary&sub=updates';
	$contract_status = get_post_meta($post->ID, 'contract_status', true); 
	?>	

	<table class="quote_status">
	<?php if ( current_user_can('cqpim_view_project_contract') && ! empty($client_id) ) { 
		$checked = get_option('enable_project_contracts'); ?>
		<?php if ( $contract_status == 1 || ! empty($deposit_amount) && $deposit_amount != 'none' ) { ?>
			<tr>
				<td class="title">
					<h5 class="mt-3"><?php esc_html_e('Prerequisites', 'projectopia-core'); ?></h5>
				</td>
				<td></td>
			</tr>
		<?php } ?>
		<?php if ( $contract_status == 1 ) { ?>
			<tr>
				<?php $classes = ( ! empty( $sent ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php esc_html_e('Contract Sent', 'projectopia-core'); ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>
			<tr>
				<?php $classes = ( ! empty( $confirmed ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php esc_html_e('Contract Signed', 'projectopia-core'); ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>
		<?php } ?>
	<?php } ?>
		<?php if ( current_user_can('edit_cqpim_invoices') && ! empty($client_id) && get_option('disable_invoices') != 1 ) { ?>
			<?php if ( $deposit_amount && $deposit_amount != 'none' ) { ?>
				<tr>
					<?php $classes = ( ! empty( $deposit_invoice_id ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php esc_html_e('Deposit Invoice Created', 'projectopia-core'); ?></td>
					<td class="<?php echo esc_attr( $classes ); ?>"></td>
				</tr>
				<tr>
					<?php $classes = ( ! empty( $deposit_sent ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php esc_html_e('Deposit Invoice Sent', 'projectopia-core'); ?></td>
					<td class="<?php echo esc_attr( $classes ); ?>"></td>
				</tr>
				<tr>
					<?php $classes = ( ! empty( $deposit_paid ) ) ? 'green' : 'red'; ?>
					<td class="title"><?php esc_html_e('Deposit Paid', 'projectopia-core'); ?></td>
					<td class="<?php echo esc_attr( $classes ); ?>"></td>
				</tr>
				<?php if ( $contract_status == 2 && empty($deposit_invoice_id) ) { ?>
					<tr>
						<td colspan="2"><button class="piaBtn btn btn-primary btn-block mt-0 save my-2" id="send_deposit" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e('Generate Deposit Invoice', 'projectopia-core'); ?></button></td>
					</tr>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<tr>
			<td class="title">
				<h5 class="mt-3"><?php esc_html_e('Milestones', 'projectopia-core'); ?></h5>
			</td>
			<td></td>
		</tr>
		<?php
		if ( ! empty($project_elements) ) {
			$ordered = array();
			$i = 0;
			$mi = 0;
			foreach ( $project_elements as $key => $element ) {
				$weight = isset($element['weight']) ? $element['weight'] : $mi;
				$ordered[ $weight ] = $element;
				$mi++;
			}
			ksort($ordered);
			foreach ( $ordered as $element ) {
			$status = isset($element['status']) ? $element['status'] : '';
			if ( ! empty($status) && $status == 'complete' ) {
				$classes = 'green';
			} else {
				$classes = 'red';
				$no_sign = true;
			} ?>
			<tr>
				<td class="title"><?php if ( ! empty( $element['title'] ) ) { echo esc_html( $element['title'] ); } ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>				
		<?php }
		} else { ?>
			<tr>
				<td class="title"><?php esc_html_e('No milestones added', 'projectopia-core'); ?></td>
				<td class="red"></td>
			</tr>				
		<?php }
		?>
		<?php if ( ! empty($client_id) ) { ?>
			<tr>
				<td class="title">
					<h5 class="mt-3"><?php esc_html_e('Completion', 'projectopia-core'); ?></h5>	
				</td>
				<td></td>
			</tr>
			<tr>
				<?php $classes = ( ! empty( $signoff ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php esc_html_e('Signed Off / Launched', 'projectopia-core'); ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>
			<?php
			$checked = get_option('invoice_workflow');          
			$project_elements = get_post_meta($post->ID, 'project_elements', true);
			$project_elements = $project_elements && is_array($project_elements) ? $project_elements : array();
			$project_total = 0;
			foreach ( $project_elements as $element ) {
				$element_cost = isset($element['cost']) ? $element['cost'] : 0;
				$cost = preg_replace("/[^\\d.]+/","", $element_cost);
				if ( empty($cost) ) {
					$cost = 0;
				}
				$project_total = $project_total + $cost;
			}
			if ( current_user_can('edit_cqpim_invoices') && $checked != 1 && ! empty($client_id) && $project_total > 0 ) { if ( get_option('disable_invoices') != 1 ) {?>
			<tr>
				<?php $classes = ( ! empty( $completion_invoice_id ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php esc_html_e('Completion Invoice Created', 'projectopia-core'); ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>
			<tr>
				<?php $classes = ( ! empty( $completion_sent ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php esc_html_e('Completion Invoice Sent', 'projectopia-core'); ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>
			<tr>
				<?php $classes = ( ! empty( $completion_paid ) ) ? 'green' : 'red'; ?>
				<td class="title"><?php esc_html_e('Completion Invoice Paid', 'projectopia-core'); ?></td>
				<td class="<?php echo esc_attr( $classes ); ?>"></td>
			</tr>
		<?php } ?>
		<?php } 
} ?>
	</table>
	<?php
	$project_sent = isset($project_details['sent']) ? $project_details['sent'] : '';
	$project_accepted = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
	$signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
	$closed = isset($project_details['closed']) ? $project_details['closed'] : '';
	$checked = get_option('enable_project_contracts'); 
	$contract_status = get_post_meta($post->ID, 'contract_status', true); 
	if ( $client_id ) {
		if ( ! $closed ) {
			if ( ! $signoff ) {
				if ( $contract_status == 1 ) {
					if ( ! $project_accepted ) {
						if ( empty($project_sent) ) {
							echo '<div class="cqpim-alert cqpim-alert-danger alert-display">';
							esc_html_e('The contract has not yet been sent to the client.', 'projectopia-core');
							echo '</div>';
							if ( current_user_can('cqpim_view_project_contract') && ! empty($client_id) ) {
								echo '<button id="send_contract" class="piaBtn btn btn-primary btn-block caribbeanGreen" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Send Contract', 'projectopia-core') . '</button>';
							}
						}
						if ( $project_sent ) {
							$project_sent = $project_details['sent_details'];
							$to = isset($project_sent['to']) ? $project_sent['to'] : '';
							$by = isset($project_sent['by']) ? $project_sent['by'] : '';
							$at = isset($project_sent['date']) ? $project_sent['date'] : '';
							if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
							echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
							/* translators: %1$s: Accepted By author, %2$s: Timestamp, %3$s: IP */
							printf(esc_html__('This contract was sent to %1$s on %2$s by %3$s', 'projectopia-core'), esc_html( $to ), esc_html( $at ), wp_kses_post( $by ) );
							echo '</div>';
							if ( current_user_can('cqpim_view_project_contract') && ! empty($client_id) ) {
								echo '<button class="piaBtn btn btn-primary btn-block caribbeanGreen" id="send_contract" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Resend Contract', 'projectopia-core') . '</button>';
							}
						}
					} else {
						if ( current_user_can('cqpim_view_project_contract') && ! empty($client_id) ) { ?>
							<p class="underline"><strong><?php esc_html_e('Resend Contract', 'projectopia-core'); ?></strong> <i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" title="<?php esc_html_e('If you have made changes to this project, such as adding milestones, changing dates or costs, you should resend it to the client for acceptance', 'projectopia-core'); ?>"></i></p>
							<?php echo '<button class="piaBtn btn btn-primary btn-block caribbeanGreen" id="send_contract" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Resend Contract', 'projectopia-core') . '</button>';
						}
						$project_accepted = $project_details['confirmed_details'];
						$ip = isset($project_accepted['ip']) ? $project_accepted['ip'] : '';
						$by = isset($project_accepted['by']) ? $project_accepted['by'] : '';
						$at = isset($project_accepted['date']) ? $project_accepted['date'] : '';
						if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
						echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
						/* translators: %1$s: Accepted By author, %2$s: Timestamp, %3$s: IP */
						printf(esc_html__('The contract was accepted by %1$s on %2$s from IP Address %3$s', 'projectopia-core'), wp_kses_post( $by ), esc_html( $at ), esc_html( $ip ));
						echo '</div>';
						if ( ! $signoff ) {
							if ( current_user_can('cqpim_mark_project_signedoff') ) {
								$disabled = '';
								if ( ! empty( $no_sign ) ) {
									$disabled = 'disabled="disabled"';
								}

								echo '<button class="piaBtn btn btn-primary btn-block btn-orange" id="signed_off" ' . esc_attr( $disabled ) . ' data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Mark as Signed Off', 'projectopia-core') . '</button>';
								if ( ! empty( $no_sign ) ) {
									echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
									esc_html_e('The project cannot be signed off until all Milestones are complete.', 'projectopia-core');
									echo '</div>';                                  
								}
							}
						}
					}   
				} else {
					echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
					esc_html_e('Project in Progress', 'projectopia-core');
					echo '</div>';

					if ( ! $signoff ) {
						$disabled = '';
						if ( ! empty( $no_sign ) ) {
							$disabled = 'disabled="disabled"';
						}

						if ( current_user_can('cqpim_mark_project_signedoff') ) {
							echo '<button class="piaBtn btn btn-primary btn-block btn-orange" id="signed_off" ' . esc_attr( $disabled ) . ' data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Mark as Signed Off', 'projectopia-core') . '</button>';
						}

						if ( ! empty( $no_sign ) ) {
							echo '<div class="cqpim-alert cqpim-alert-warning alert-display">';
							esc_html_e('The project cannot be signed off until all Milestones are complete.', 'projectopia-core');
							echo '</div>';                                  
						}
					}                       
				}
			} else {
				$project_signedoff = $project_details['signoff_details'];
				$by = isset($project_signedoff['by']) ? $project_signedoff['by'] : '';
				$at = isset($project_signedoff['at']) ? $project_signedoff['at'] : '';  
				if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }
				echo '<div class="cqpim-alert cqpim-alert-success alert-display">';
				/* translators: %1$s: Signed off By author, %2$s: Timestamp */
				printf(esc_html__('This project was signed off by %1$s on %2$s', 'projectopia-core'), wp_kses_post( $by ), esc_html( $at ));
				echo '</div>';
				if ( ! $closed ) {
					$value = get_option('invoice_workflow');
					if ( $value != 1 ) {
						if ( current_user_can('cqpim_mark_project_signedoff') ) {
							echo '<button class="piaBtn redColor btn btn-primary btn-block btn-orange" id="unsigned_off" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Remove Signed-Off Status', 'projectopia-core') . '</button>';
						}
					}
					if ( current_user_can('cqpim_mark_project_closed') ) {
						echo '<button class="piaBtn btn btn-primary btn-block redColor" id="close_off" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Close Project', 'projectopia-core') . '</button>';
					}
				}           
			}
		} else {
			$project_closed = $project_details['closed_details'];
			$by = isset($project_closed['by']) ? $project_closed['by'] : '';
			$at = isset($project_closed['at']) ? $project_closed['at'] : '';
			if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }               
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">';
			/* translators: %1$s: Closed By author, %2$s: Timestamp */
			printf(esc_html__('This project was closed by %1$s on %2$s', 'projectopia-core'), wp_kses_post( $by ), esc_html( $at ));
			echo '</div>';  
			if ( current_user_can('cqpim_mark_project_closed') ) {
				echo '<button class="piaBtn redColor btn btn-primary btn-block caribbeanGreen" id="unclose_off" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Re-open Project', 'projectopia-core') . '</button>';
			}
		} 
	} else {
		if ( ! $closed ) {
			echo '<div class="alert-display cqpim-alert cqpim-alert-info">';
			esc_html_e('This is not a client project, no contract needs to be signed.', 'projectopia-core');
			echo '</div>';
			if ( ! $closed ) {
				if ( current_user_can('cqpim_mark_project_closed') ) {
					echo '<button class="piaBtn btn btn-primary btn-block redColor" id="close_off" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Close Project', 'projectopia-core') . '</button>';
				}
			}
		} else {
			$project_closed = $project_details['closed_details'];
			$by = isset($project_closed['by']) ? $project_closed['by'] : '';
			$at = isset($project_closed['at']) ? $project_closed['at'] : '';
			if ( is_numeric($at) ) { $at = wp_date(get_option('cqpim_date_format') . ' H:i', $at); } else { $at = $at; }               
			echo '<div class="cqpim-alert cqpim-alert-info alert-display">';
			/* translators: %1$s: Closed By author, %2$s: Timestamp */
			printf(esc_html__('This project was closed by %1$s on %2$s', 'projectopia-core'), wp_kses_post( $by ), esc_html( $at ));
			echo '</div>';  
			if ( current_user_can('cqpim_mark_project_closed') ) {
				echo '<button class="piaBtn redColor btn btn-primary btn-block caribbeanGreen" id="unclose_off" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__('Re-open Project', 'projectopia-core') . '</button>';
			}   
		}
	}
	if ( ! empty($checked) ) {
		if ( current_user_can('cqpim_view_project_contract') && ! empty($client_id) && empty($client_contract) ) { ?>
			<a class="piaBtn btn btn-primary btn-block" href="<?php echo esc_url( $contract_link ); ?>" target="_blank"><?php esc_html_e('Preview Contract', 'projectopia-core'); ?></a>
	<?php } 
	} ?>
	<div id="messages"></div>
	<div id="quote_unsign_container" style="display: none;">
		<div id="quote_unsign">
			<div style="padding:12px">
				<h3><?php esc_html_e('Remove Signed-off Status', 'projectopia-core'); ?></h3>
				<p><?php esc_html_e('Removing Signed-off status will also delete the completion invoice, if one is present. The invoice will be regenerated when the project is signed off again.', 'projectopia-core'); ?>
				<br /><br />
				<div id="unsign-error"></div>
				<button class="cancel-creation mt-20 piaBtn redColor"><?php esc_html_e('Cancel', 'projectopia-core'); ?></button>
				<button class="save-unsigned btn piaBtn right"><?php esc_html_e('Remove Signed-off Status', 'projectopia-core'); ?></button>
			</div>
		</div>
	</div>				
	<?php

}

/**
 * Project colors metabox register callback function.
 * 
 * @since 5.0
 *
 * @param Object $post
 * 
 * @return void
 */
function pto_project_color_metabox_callback( $post ) { 

	$project_colours = get_post_meta($post->ID, 'project_colours', true);
	$project_colour = isset($project_colours['project_colour']) ? $project_colours['project_colour'] : '#3B3F51';
	$ms_colour = isset($project_colours['ms_colour']) ? $project_colours['ms_colour'] : '#337ab7'; 
	$task_colour = isset($project_colours['task_colour']) ? $project_colours['task_colour'] : '#36c6d3';
	?>

	<?php if ( current_user_can('cqpim_edit_project_colours') ) { ?>
		<div class="selected-color">
			<div class="single-color mb-2">
				<h5 class="mb-1"><?php esc_html_e('Project', 'projectopia-core'); ?></h5>
				<div class="color-inp d-flex align-items-center">
					<input type="text" class="cqpim_picker" name="project_colour" id="project_colour" 
						value="<?php echo esc_attr( $project_colour ); ?>" />
				</div>
			</div>
		</div>

		<div class="selected-color">
			<div class="single-color mb-2">
				<h5 class="mb-1"><?php esc_html_e('Milestone', 'projectopia-core'); ?></h5>
				<div class="color-inp d-flex align-items-center">
					<input type="text" class="cqpim_picker" name="ms_colour" id="ms_colour" 
						value="<?php echo esc_attr( $ms_colour ); ?>" />
				</div>
			</div>
		</div>

		<div class="selected-color">
			<div class="single-color mb-2">
				<h5 class="mb-1"><?php esc_html_e('Task', 'projectopia-core'); ?></h5>
				<div class="color-inp d-flex align-items-center">
					<input type="text" class="cqpim_picker" name="task_colour" id="task_colour" 
						value="<?php echo esc_attr( $task_colour ); ?>" />
				</div>
			</div>
		</div>

		<p class="text-right">
			<i class="fa fa-question-circle cqpim_tooltip" aria-hidden="true" 
				title="<?php esc_html_e('You can set colours for project elements here, these colours will be used in the calendar to make projects easier to identify. NOTE: Overdue Tasks will always display in red', 'projectopia-core'); ?>"></i>
		</p>

	<?php }
}

add_action( 'save_post_cqpim_project', 'save_pto_project_details_metabox_data' );
function save_pto_project_details_metabox_data( $post_id ) {
	if ( ! isset( $_POST['project_details_metabox_nonce'] ) ) {
		return $post_id;
	}
	
	$nonce = sanitize_text_field( wp_unslash( $_POST['project_details_metabox_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'project_details_metabox' ) ) {
		return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
 
	$project_colours = array();
	$project_colours['project_colour'] = isset( $_POST['project_colour'] ) ? sanitize_text_field( wp_unslash( $_POST['project_colour'] ) ) : '';
	$project_colours['ms_colour'] = isset( $_POST['ms_colour'] ) ? sanitize_text_field( wp_unslash( $_POST['ms_colour'] ) ) : '';
	$project_colours['task_colour'] = isset( $_POST['task_colour'] ) ? sanitize_text_field( wp_unslash( $_POST['task_colour'] ) ) : '';
	update_post_meta( $post_id, 'project_colours', $project_colours );
	
	$quote_details = get_post_meta( $post_id, 'project_details', true );
	$quote_details = $quote_details && is_array( $quote_details ) ? $quote_details : array();
	if ( isset( $_POST['start_date'] ) ) {
		$start_date = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
		$timestamp = pto_convert_date( $start_date );
		$quote_details['start_date'] = $timestamp;
	}

	if ( isset( $_POST['finish_date'] ) ) {
		$finish_date = sanitize_text_field( wp_unslash( $_POST['finish_date'] ) );
		$timestamp = pto_convert_date( $finish_date );
		$quote_details['finish_date'] = $timestamp;
	}

	if ( isset( $_POST['quote_client'] ) ) {
		$quote_client = sanitize_text_field( wp_unslash( $_POST['quote_client'] ) );
		$quote_details['client_id'] = $quote_client;
	}

	$currency = get_option( 'currency_symbol' );
	$currency_code = get_option( 'currency_code' );
	$currency_position = get_option( 'currency_symbol_position' );
	$currency_space = get_option( 'currency_symbol_space' ); 
	$client_currency = get_post_meta( $quote_client, 'currency_symbol', true );
	$client_currency_code = get_post_meta( $quote_client, 'currency_code', true );
	$client_currency_space = get_post_meta( $quote_client, 'currency_space', true );      
	$client_currency_position = get_post_meta( $quote_client, 'currency_position', true );
	
	$quote_currency = isset( $_POST['currency_symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_symbol'] ) ) : '';
	$quote_currency_code = isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : '';
	$quote_currency_space = isset( $_POST['currency_space'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_space'] ) ) : '';
	$quote_currency_position = isset( $_POST['currency_position'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_position'] ) ) : '';

	if ( ! empty( $quote_currency ) ) {
		update_post_meta( $post_id, 'currency_symbol', $quote_currency );
	} else {
		if ( ! empty( $client_currency ) ) {
			update_post_meta( $post_id, 'currency_symbol', $client_currency );
		} else {
			update_post_meta( $post_id, 'currency_symbol', $currency );
		}
	}

	if ( ! empty( $quote_currency_code ) ) {
		update_post_meta( $post_id, 'currency_code', $quote_currency_code );
	} else {
		if ( ! empty( $client_currency_code ) ) {
			update_post_meta( $post_id, 'currency_code', $client_currency_code );
		} else {
			update_post_meta( $post_id, 'currency_code', $currency_code );
		}
	}

	if ( ! empty($quote_currency_space) ) {
		update_post_meta($post_id, 'currency_space', $quote_currency_space);
	} else {
		if ( ! empty($client_currency_space) ) {
			update_post_meta($post_id, 'currency_space', $client_currency_space);
		} else {
			update_post_meta($post_id, 'currency_space', $currency_space);
		}
	}

	if ( ! empty( $quote_currency_position ) ) {
		update_post_meta( $post_id, 'currency_position', $quote_currency_position );
	} else {
		if ( ! empty( $client_currency_position ) ) {
			update_post_meta( $post_id, 'currency_position', $client_currency_position );
		} else {
			update_post_meta( $post_id, 'currency_position', $currency_position );
		}
	}

	if ( isset( $_POST['client_contact'] ) ) {
		$quote_details['client_contact'] = sanitize_text_field( wp_unslash( $_POST['client_contact'] ) );
	}

	if ( isset( $_POST['default_contract_text'] ) ) {
		$quote_details['default_contract_text'] = sanitize_textarea_field( wp_unslash( $_POST['default_contract_text'] ) );
	} else {
		$quote_details['default_contract_text'] = get_option( 'default_contract_text' );
	}

	if ( isset( $_POST['quote_ref'] ) ) {
		$quote_details['quote_ref'] = sanitize_text_field( wp_unslash( $_POST['quote_ref'] ) );
	}

	if ( isset( $_POST['deposit_amount'] ) ) {
		$quote_details['deposit_amount'] = sanitize_text_field( wp_unslash( $_POST['deposit_amount'] ) );
	}

	update_post_meta( $post_id, 'project_details', $quote_details );

	$tax_app = get_post_meta( $post_id, 'tax_set', true );
	if ( empty( $tax_app ) ) {
		$client_details = get_post_meta( $quote_client, 'client_details', true );
		$client_tax = isset( $client_details['tax_disabled'] ) ? $client_details['tax_disabled'] : '';
		$client_stax = isset( $client_details['stax_disabled'] ) ? $client_details['stax_disabled'] : '';
		$system_tax = get_option( 'sales_tax_rate' );
		$system_stax = get_option( 'secondary_sales_tax_rate' );
		if ( ! empty( $system_tax ) && empty( $client_tax ) ) {
			update_post_meta( $post_id, 'tax_applicable', 1 );
			update_post_meta( $post_id, 'tax_set', 1 );   
			update_post_meta( $post_id, 'tax_rate', $system_tax );    
			if ( ! empty( $system_stax ) && empty( $client_stax ) ) {
				update_post_meta( $post_id, 'stax_applicable', 1 );
				update_post_meta( $post_id, 'stax_set', 1 );  
				update_post_meta( $post_id, 'stax_rate', $system_stax );          
			} else {
				update_post_meta( $post_id, 'stax_applicable', 0 );
				update_post_meta( $post_id, 'stax_set', 1 );
				update_post_meta( $post_id, 'stax_rate', 0 );             
			}
		} else {
			update_post_meta( $post_id, 'tax_applicable', 0 );
			update_post_meta( $post_id, 'tax_set', 1 );
			update_post_meta( $post_id, 'tax_rate', 0 );          
		}
	}

	if ( ! empty( $_POST['ptitle'] ) ) {
		$quote_updated = array(
			'ID'         => $post_id,
			'post_title' => wp_kses_post( wp_unslash( $_POST['ptitle'] ) ),
			'post_name'  => $post_id,
		);
		if ( ! wp_is_post_revision( $post_id ) ) {
			remove_action( 'save_post_cqpim_project', 'save_pto_project_details_metabox_data' );
			wp_update_post( $quote_updated );
			add_action( 'save_post_cqpim_project', 'save_pto_project_details_metabox_data' );
		}   
	}

	$contract_status = get_post_meta( $post_id, 'contract_status', true );
	if ( empty( $contract_status ) ) {
		$contract = pto_get_contract_status( $post_id );
		update_post_meta( $post_id, 'contract_status', $contract );
	}
}