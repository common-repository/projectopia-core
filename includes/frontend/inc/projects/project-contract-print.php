<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title><?php wp_title(); ?></title>   
<?php wp_head(); ?>
<?php echo '<style>' . esc_textarea( get_option('cqpim_dash_css') ) . '</style>'; ?>
</head>
<?php
$user = wp_get_current_user(); 
$user_id = $user->ID;
$logo = get_option('company_logo');
$logo_url = isset($logo['company_logo']) ? esc_url( $logo['company_logo'] ) : '';
$p_title = get_the_title();
$p_title = str_replace('Private: ', '', $p_title);
$company_name = get_option('company_name');
$company_address = get_option('company_address');
$company_postcode = get_option('company_postcode');
$contract_text = get_option('default_contract_text');
$currency = get_option('currency_symbol');
$vat = get_post_meta($post->ID, 'tax_applicable', true);
if ( ! empty($vat) ) {
	$vat = get_post_meta($post->ID, 'tax_rate', true);
}
$tax_name = get_option('sales_tax_name');
if ( ! empty($vat) ) {
	$vat_string = '';
} else {
	$vat_string = '';
}
$project_details = get_post_meta($post->ID, 'project_details', true);
$project_elements = get_post_meta($post->ID, 'project_elements', true);
$p_type = isset($project_details['quote_type']) ? $project_details['quote_type'] : '';
$upper_type = ucfirst($p_type);
$quote_id = isset($project_details['quote_id']) ? $project_details['quote_id'] : '';
$quote_details = get_post_meta($quote_id, 'quote_details', true);
$project_summary = isset($project_details['project_summary']) ? $project_details['project_summary'] : '';
$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
$client_details = get_post_meta($client_id, 'client_details', true);
$client_ids = get_post_meta($client_id, 'client_ids', true);
if ( empty($client_ids) ) {
	$client_ids = array();
}
$client_user_id = isset($client_details['user_id']) ? $client_details['user_id'] : '';
$client_terms = isset($client_details['invoice_terms']) ? $client_details['invoice_terms'] : '';
$client_address = isset($client_details['client_address']) ? $client_details['client_address'] : '';
$client_postcode = isset($client_details['client_postcode']) ? $client_details['client_postcode'] : '';
$client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
$start_date = isset($project_details['start_date']) ? $project_details['start_date'] : '';
$finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';
$deposit = isset($project_details['deposit_amount']) ? $project_details['deposit_amount'] : '';
if ( $client_terms ) {
	$invoice_terms = $client_terms;
} else {
	$invoice_terms = get_option('company_invoice_terms');
}
if ( in_array('cqpim_client', $user->roles) ) {
$user = wp_get_current_user();
$client_logs = get_post_meta($client_id, 'client_logs', true);
if ( empty($client_logs) ) {
	$client_logs = array();
}
$now = time();
$client_logs[ $now ] = array(
	'user' => $user->ID,
	/* translators: %1$s: Project ID, %2$s: Project Title */
	'page' => sprintf(esc_html__('Project %1$s - %2$s (Contract Page)', 'projectopia-core'), get_the_ID(), $p_title),
);
update_post_meta($client_id, 'client_logs', $client_logs);
}
?>
<body <?php body_class(); ?>>
<div class="quote_print" style="width:800px; margin:0 auto; background:#fff; padding:0 20px 20px;" id="content" role="main">
	<?php if ( current_user_can( 'cqpim_view_project_contract' ) OR $client_user_id == $user_id OR in_array($user->ID, $client_ids) ) { ?>
		<div class="cqpim-dash-item-full grid-item">
			<div>				
				<div class="quote_logo">
					<img src="<?php echo esc_url( $logo_url ); ?>" />
				</div>
				<div class="quote_contacts">
					<?php echo esc_html( get_option('company_name') ); ?><br />
					<?php esc_html_e('Tel:', 'projectopia-core'); ?><?php echo esc_html( get_option('company_telephone') ); ?><br />
					<?php esc_html_e('Email:', 'projectopia-core'); ?> <a href="mailto:<?php echo esc_attr( get_option('company_sales_email') ); ?>"><?php echo esc_html( get_option('company_sales_email') ); ?></a>
				</div>
				<div class="clear"></div>
				<h1><?php echo esc_html( $p_title ); ?></h1>
				<div class="contract-specifics">
					<h1><?php esc_html_e('CONTRACT DOCUMENTATION', 'projectopia-core'); ?></h1>
					<p><strong><?php esc_html_e('This is an agreement between "us"', 'projectopia-core'); ?></strong></p>
					<p><?php echo esc_html( $company_name ); ?></p>
					<p><?php echo wp_kses_post( nl2br( $company_address ) ); ?> <?php echo esc_html( $company_postcode ); ?></p>
					<p><strong><?php esc_html_e('and "you"', 'projectopia-core'); ?></strong></p>
					<p><?php echo esc_html( $client_company_name ); ?></p>
					<p><?php echo esc_textarea( $client_address ); ?> <?php echo esc_html( $client_postcode ); ?></p>
				</div>
				<h1><?php esc_html_e('ABOUT THE PROJECT', 'projectopia-core'); ?></h1>
				<?php
				if ( $project_summary ) {
					echo '<h2>' . esc_html__('Summary', 'projectopia-core') . '</h2>';
					echo wp_kses_post( wpautop($project_summary) );
				}
				if ( $start_date || $finish_date ) {
					echo '<h2>' . esc_html__('Project Dates', 'projectopia-core') . '</h2>';
				}
				if ( $start_date ) {
					if ( is_numeric($start_date) ) { $start_date = wp_date(get_option('cqpim_date_format'), $start_date); } else { $start_date = $start_date; }
					echo '<p>' . esc_html__('Start Date', 'projectopia-core') . ' - ' . esc_html( $start_date ) . '</p>';
				}
				if ( $finish_date ) {
					if ( is_numeric($finish_date) ) { $finish_date = wp_date(get_option('cqpim_date_format'), $finish_date); } else { $finish_date = $finish_date; }
					echo '<p>' . esc_html__('Completion/Launch Date', 'projectopia-core') . ' - ' . esc_html( $finish_date ) . '</p>';
				}
				if ( ! empty($project_elements) ) { ?>
					<h2><?php esc_html_e('Milestones', 'projectopia-core'); ?></h2>
					<?php
					$msordered = array();
					$i = 0;
					$mi = 0;
					foreach ( $project_elements as $key => $element ) {
						$weight = isset($element['weight']) ? $element['weight'] : $mi;
						$msordered[ $weight ] = $element;
						$mi++;
					}
					ksort($msordered);
					foreach ( $msordered as $element ) { ?>
						<div class="dd-milestone">
							<div class="dd-milestone-title">
								<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2"><?php esc_html_e('Milestone', 'projectopia-core'); ?></span>  <span class="ms-title"><?php echo wp_kses_post( $element['title'] ); ?></span>
								<div class="dd-milestone-info">
									<?php if ( ! empty($element['cost']) ) { ?>
											<?php echo esc_html( pto_calculate_currency($post->ID, $element['cost']) ); ?>
									<?php } ?>
									<?php if ( ! empty($element['start']) ) { ?>
										<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Start Date:', 'projectopia-core'); ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element['start']) ); ?>
									<?php } ?>
									<?php if ( ! empty($element['deadline']) ) { ?>
										<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Deadline:', 'projectopia-core'); ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $element['deadline']) ); ?>
									<?php } ?>
								</div>
								<div class="clear"></div>											
								<div class="dd-tasks">
									<?php
									$args = array(
										'post_type'      => 'cqpim_tasks',
										'posts_per_page' => -1,
										'meta_key'       => 'milestone_id',
										'meta_value'     => $element['id'],
										'orderby'        => 'date',
										'order'          => 'ASC',
									);
									$tasks = get_posts($args);
									if ( $tasks ) {
										$ti = 0;
										$ordered = array();
										$wi = 0;
										foreach ( $tasks as $task ) {
											$task_details = get_post_meta($task->ID, 'task_details', true);
											$weight = isset($task_details['weight']) ? $task_details['weight'] : $wi;
											if ( empty($task->post_parent) ) {
												$ordered[ $weight ] = $task;
											}
											$wi++;
										}
										ksort($ordered);
										foreach ( $ordered as $task ) {
											$task_details = get_post_meta($task->ID, 'task_details', true);
											$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
											$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
											$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
											$weight = isset($task_details['weight']) ? $task_details['weight'] : 0;
											$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : ''; ?>
											<div class="dd-task">
												<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php esc_html_e('Task', 'projectopia-core'); ?></span> <span class="ms-title"><?php echo esc_html( $task->post_title ); ?></span>
												<div class="dd-task-info">
													<?php if ( ! empty($start) ) { ?>
														<strong><?php esc_html_e('Start Date:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?>
													<?php } ?>
													<?php if ( ! empty($task_deadline) ) { ?>
														<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Deadline:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?>
													<?php } ?>	
													<?php if ( ! empty($task_est_time) ) { ?>
														<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Est. Time:', 'projectopia-core') ?></strong> <?php echo esc_html( $task_est_time ); ?>
													<?php } ?>										
												</div>
												<div class="clear"></div>
												<div class="dd-subtasks">
													<?php $ti++;
													$args = array(
														'post_type' => 'cqpim_tasks',
														'posts_per_page' => -1,
														'meta_key' => 'milestone_id',
														'meta_value' => $element['id'],
														'post_parent' => $task->ID,
														'orderby' => 'date',
														'order' => 'ASC',
													);
													$subtasks = get_posts($args);
													if ( ! empty($subtasks) ) {
														$subordered = array();
														$sti = 0;
														$ssti = 0;
														foreach ( $subtasks as $subtask ) {
															$task_details = get_post_meta($subtask->ID, 'task_details', true);
															$weight = isset($task_details['weight']) ? $task_details['weight'] : $sti;
															$subordered[ $weight ] = $subtask;
															$sti++;
														}
														ksort($subordered);
														foreach ( $subordered as $subtask ) {
															$task_details = get_post_meta($subtask->ID, 'task_details', true);
															$task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
															$start = isset($task_details['task_start']) ? $task_details['task_start'] : '';
															$description = isset($task_details['task_description']) ? $task_details['task_description'] : '';
															$sweight = isset($task_details['weight']) ? $task_details['weight'] : 0;
															$task_est_time = isset($task_details['task_est_time']) ? $task_details['task_est_time'] : ''; ?>
															<div class="dd-task">
																<span class="table-task cqpim_button cqpim_small_button font-white bg-grey-cascade op nolink rounded_2"><?php esc_html_e('Subtask', 'projectopia-core'); ?></span> <span class="ms-title"><?php echo esc_html( $subtask->post_title ); ?></span>
																<div class="dd-task-info">
																	<?php if ( ! empty($start) ) { ?>
																		<strong><?php esc_html_e('Start Date:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $start) ); ?>
																	<?php } ?>
																	<?php if ( ! empty($task_deadline) ) { ?>
																		<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Deadline:', 'projectopia-core') ?></strong> <?php echo esc_html( wp_date(get_option('cqpim_date_format'), $task_deadline) ); ?>
																	<?php } ?>	
																	<?php if ( ! empty($task_est_time) ) { ?>
																		<i class="fa fa-circle dd-circle"></i> <strong><?php esc_html_e('Est. Time:', 'projectopia-core') ?></strong> <?php echo esc_html( $task_est_time ); ?>
																	<?php } ?>										
																</div>	
															</div>												
															<?php $ssti++;
														}
													} ?>
												</div>
											</div>
										<?php }
									}
									?>
								</div>
							</div>
						</div>
					<?php $i++;
					}
				}
				if ( $project_elements ) { ?>
					<h2><?php esc_html_e('Cost Breakdown', 'projectopia-core'); ?></h2>
					<?php
					echo '<table class="cqpim_table"><thead><tr>';
					echo '<th>' . esc_html__('Milestone', 'projectopia-core') . '</th>';
					if ( $p_type == 'estimate' ) {
						echo '<th>' . esc_html__('Estimated Cost', 'projectopia-core') . '</th>';
					} else {
						echo '<th>' . esc_html__('Cost', 'projectopia-core') . '</th>';
					}
					echo '</tr></thead>';
					echo '<tbody>';
					$subtotal = 0;
					foreach ( $msordered as $key => $element ) {
						$cost = preg_replace("/[^\\d.]+/","", $element['cost']);
						if ( ! empty($cost) ) {
							$subtotal = $subtotal + $cost;
						}
						echo '<tr><td class="qtitle">' . esc_html( $element['title'] ) . '</td>';
						echo '<td class="qcost">' . esc_html( pto_calculate_currency($post->ID, $cost) ) . '</td></tr>';
					}
					$project_details = get_post_meta($post->ID, 'project_details', true);
					$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
					$client_details = get_post_meta($client_id, 'client_details', true);
					$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
					$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : ''; 
					if ( ! empty($vat) && empty($client_tax) ) {
						$stax_rate = get_option('secondary_sales_tax_rate');
						$total_vat = $subtotal / 100 * $vat;
						if ( ! empty($stax_rate) ) {
							$total_stax = $subtotal / 100 * $stax_rate;
						}
						if ( ! empty($stax_rate) && empty($client_stax) ) {
							$total = $subtotal + $total_vat + $total_stax;
						} else {
							$total = $subtotal + $total_vat;
						}
						$tax_name = get_option('sales_tax_name');
						$stax_name = get_option('secondary_sales_tax_name');
						$span = '';
						echo '<tr><td align="right" class="align-right"><strong>' . esc_html__('Subtotal', 'projectopia-core') . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $subtotal) ) . '</td></tr>';
						echo '<tr><td align="right" class="align-right"><strong>' . esc_html( $tax_name ) . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_vat) ) . '</td></tr>';
						if ( ! empty($stax_rate) && empty($client_stax) ) {
							echo '<tr><td align="right" class="align-right"><strong>' . esc_html( $stax_name ) . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_stax) ) . '</td></tr>';
						}
						echo '<tr><td align="right" class="align-right"><strong>' . esc_html__('TOTAL', 'projectopia-core') . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total) ) . '</td></tr>';
					} else {
						$span = '';
						echo '<tr><td align="right" class="align-right"><strong>' . esc_html__('TOTAL', 'projectopia-core') . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $subtotal) ) . '</td></tr>'; 
					}
					echo '</tbody></table>'; 
				}
				if ( $p_type == 'estimate' ) { ?>
				<br />
				<h4><strong><?php esc_html_e('NOTE:', 'projectopia-core'); ?> </strong><?php esc_html_e('THIS IS AN ESTIMATE, SO THESE PRICES MAY NOT REFLECT THE FINAL PROJECT COST.', 'projectopia-core'); ?></h4>
				<?php } ?>
				<h2><?php esc_html_e('Payment Plan', 'projectopia-core'); ?></h2>
				<p><strong><?php esc_html_e('Deposit', 'projectopia-core'); ?></strong></p>
				<?php
				if ( ! $deposit || $deposit == 'none' ) {
					echo '<p>' . esc_html__('We do not require an up-front deposit payment on this project. The full balance will be due on completion.', 'projectopia-core') . '</p>';
				} else {
					if ( empty( $subtotal ) ) {
						$subtotal = 0;
					}
					$deposit_amount = (int)$subtotal / 100 * (int)$deposit;
					echo '<p>';
					/* translators: %s: Deposit Percentage */
					printf(esc_html__('We require an initial deposit payment of %s percent on this project which will be invoiced on acceptance.', 'projectopia-core'), esc_html( $deposit ));
					echo '</p>';
				}   
				$default_contract = get_option( 'default_contract_text' );
				$project_contract = isset( $project_details['default_contract_text'] ) ? intval( $project_details['default_contract_text'] ) : '';
				if ( empty( $project_contract ) ) {
					$project_contract = $default_contract;
				}

				if ( $project_contract ) {
					$project_contract_text = get_post_meta( $project_contract, 'terms', true );
					if ( $project_contract_text ) {
						$text = pto_replacement_patterns( $project_contract_text, $post->ID, 'project' );
					}
				}
				
				if ( ! empty( $text ) ) {
					echo '<h2>' . esc_html__( 'TERMS &amp; CONDITIONS', 'projectopia-core' ) . '</h2>';
					echo wp_kses_post( wpautop( $text ) );          
				} ?>
				<div id="acceptance">
					<?php
					$is_confirmed = isset( $project_details['confirmed'] ) ? $project_details['confirmed'] : '';
					if ( ! $is_confirmed ) { ?>
					<h1><?php esc_html_e('CONTRACT ACCEPTANCE', 'projectopia-core'); ?></h1>
					<?php echo wp_kses_post(wpautop(get_option('contract_acceptance_text'))); ?>
					<div class="quote_acceptance">
						<form id="submit-quote-conf">
							<input type="hidden" id="project_id" value="<?php the_ID(); ?>" />
							<input type="hidden" id="pm_name" value="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" />
							<input type="text" id="conf_name" name="conf_name" placeholder="<?php esc_attr_e('Enter your name', 'projectopia-core'); ?>" required /><br />
							<input type="submit" id="accept_contract" class="cqpim_button font-white bg-violet mt-20 rounded_2" value="<?php esc_html_e('Confirm Contract', 'projectopia-core'); ?>" />
							<div id="messages"></div>
						</form>	
					</div>
					<?php } else { 
						$conf_by = isset($project_details['confirmed_details']['by']) ? $project_details['confirmed_details']['by'] : '';
						$conf_date = isset($project_details['confirmed_details']['date']) ? $project_details['confirmed_details']['date'] : '';
						if ( is_numeric($conf_date) ) { $conf_date = wp_date(get_option('cqpim_date_format') . ' H:i', $conf_date); } else { $conf_date = $conf_date; }
						$conf_ip = isset($project_details['confirmed_details']['ip']) ? $project_details['confirmed_details']['ip'] : '';               
						?>
						<div class="cqpim-alert cqpim-alert-success alert-display">
							<h5><?php esc_html_e('THIS CONTRACT HAS BEEN CONFIRMED', 'projectopia-core'); ?></h5>
							<p><?php 
							/* translators: %1$s: Accepted By author, %2$s: Timestamp, %3$s: IP */
							printf(esc_html__('Confirmed by %1$s @ %2$s from IP Address %3$s', 'projectopia-core'), wp_kses_post($conf_by), esc_html($conf_date), esc_html($conf_ip)); ?></p>
						</div>
					<?php } ?>
				</div>
				<div class="clear"></div>	
			</div>
		</div>	
	<?php } else { ?>
	<h1><?php esc_html_e('Access Denied', 'projectopia-core'); ?></h1>
	<?php } ?>
</div><!-- #content -->
<?php wp_footer(); ?>
</body>
</html>
<?php exit; ?>