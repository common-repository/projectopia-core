<!DOCTYPE html>
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="ie ie9" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php wp_title(); ?></title>   
    <?php wp_head(); ?>
	<?php echo '<style>' . esc_textarea(get_option('cqpim_dash_css')) . '</style>'; ?>
</head>
<body <?php body_class(); ?>>
	<div class="quote_print" style="width:800px; margin:0 auto; background:#fff; padding:0 20px 20px;" id="content" role="main">
		<?php if ( current_user_can( 'cqpim_send_quotes' ) OR $client_user_id == $user_id OR in_array($user->ID, $quote_client_ids) ) { ?>
			<div class="cqpim-dash-item-full grid-item">
				<div>
					<div class="quote_logo">
						<img src="<?php echo esc_url( $logo_url ); ?>" />
					</div>
					<div class="quote_contacts">
						<?php echo esc_html( get_option('company_name') ); ?><br />
						<?php esc_html_e('Tel:', 'projectopia-core'); ?> <?php echo esc_html(get_option('company_telephone')); ?><br />
						<?php esc_html_e('Email:', 'projectopia-core'); ?> <a href="mailto:<?php echo esc_attr(get_option('company_sales_email')); ?>"><?php echo esc_html(get_option('company_sales_email')); ?></a>
					</div>
					<div class="clear"></div>
					<?php 
					if ( ! empty( $quote_header ) ) {
						echo wp_kses_post( wpautop( $quote_header ) );
					}
					if ( $quote_summary ) {
						echo '<h2> ' . esc_html__('Summary', 'projectopia-core') . '</h2>';
						echo wp_kses_post (wpautop( $quote_summary ) );
					}
					if ( $start_date || $finish_date ) {
						echo '<h2>' . esc_html__('Project Dates', 'projectopia-core') . '</h2>';
					}
					if ( $start_date ) {
						if ( is_numeric($start_date) ) { $start_date = wp_date(get_option('cqpim_date_format'), $start_date); } else { $start_date = $start_date; }
						echo '<p>' . esc_html__('Start Date', 'projectopia-core') . ' - ' . esc_html( $start_date ). '</p>';
					}
					if ( $finish_date ) {
						if ( is_numeric($finish_date) ) { $finish_date = wp_date(get_option('cqpim_date_format'), $finish_date); } else { $finish_date = $finish_date; }
						echo '<p>' . esc_html__('Completion/Launch Date', 'projectopia-core') . ' - ' . esc_html( $finish_date ) . '</p>';
					}
					if ( ! empty( $quote_elements ) ) { ?>
						<h2><?php esc_html_e('Milestones', 'projectopia-core'); ?></h2>
						<?php
						$msordered = array();
						$i = 0;
						$mi = 0;
						foreach ( $quote_elements as $key => $element ) {
							$weight = isset($element['weight']) ? $element['weight'] : $mi;
							$msordered[ $weight ] = $element;
							$mi++;
						}
						ksort($msordered);                  
						foreach ( $msordered as $element ) {  ?>
							<div class="dd-milestone">
								<div class="dd-milestone-title">
									<span class="cqpim_button cqpim_small_button font-white bg-blue-madison nolink op rounded_2"><?php esc_html_e('Milestone', 'projectopia-core'); ?></span>  <span class="ms-title"><?php echo esc_html( $element['title'] ); ?></span>
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
											'post_type'  => 'cqpim_tasks',
											'posts_per_page' => -1,
											'meta_key'   => 'milestone_id',
											'meta_value' => $element['id'],
											'orderby'    => 'date',
											'order'      => 'ASC',
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
					if ( ! empty($quote_elements) ) { ?>
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
						$project_details = get_post_meta($post->ID, 'quote_details', true);
						$client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
						$client_details = get_post_meta($client_id, 'client_details', true);
						$client_tax = isset($client_details['tax_disabled']) ? $client_details['tax_disabled'] : '';
						$client_stax = isset($client_details['stax_disabled']) ? $client_details['stax_disabled'] : ''; 
						$vat = get_post_meta($post->ID, 'tax_applicable', true);
						if ( ! empty($vat) ) {
							$vat = get_post_meta($post->ID, 'tax_rate', true);
						}
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
							echo '<tr><td align="right" class="align-right"><strong>' . esc_html__('Subtotal', 'projectopia-core') . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $subtotal) ) . '</td></tr>';
							echo '<tr><td align="right" class="align-right"><strong>' . esc_html( $tax_name ) . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_vat) ) . '</td></tr>';
							if ( ! empty($stax_rate) && empty($client_stax) ) {
								echo '<tr><td align="right" class="align-right"><strong>' . esc_html( $stax_name ) . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total_stax) ) . '</td></tr>';
							}
							echo '<tr><td align="right" class="align-right"><strong>' . esc_html__('TOTAL', 'projectopia-core') . ': </strong></td><td class="subtotal">' . esc_html( pto_calculate_currency($post->ID, $total) ) . '</td></tr>';
						} else {
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
						echo '<p>' . esc_html__('We do not require an up-front deposit payment on this project.', 'projectopia-core') . '</p>';
					} else {
						if ( empty( $subtotal ) ) {
							$subtotal = 0;
						}
						$deposit_amount = $subtotal / 100 * (int)$deposit;
						echo '<p>';
						/* translators: %s: Deposit Percentage */
						printf(esc_html__('We require an initial deposit payment of %s percent on this project which will be invoiced on acceptance.', 'projectopia-core'), esc_html( $deposit ));
						echo '</p>';
					}
					$terms = get_option( 'enable_quote_terms' );
					$default_contract = get_option( 'default_contract_text' );
					$quote_contract = isset( $quote_details['default_contract_text'] ) ? intval( $quote_details['default_contract_text'] ) : '';
					if ( empty( $quote_contract ) ) {
						$quote_contract = $default_contract;
					}
					if ( $quote_contract ) {
						$quote_contract_text = get_post_meta( $quote_contract, 'terms', true );
						if ( $quote_contract_text ) {
							$text = pto_replacement_patterns( $quote_contract_text, $post->ID, 'quote' );
						}
					}
					if ( $terms == 1 && ! empty( $text ) ) {
						echo '<h2>' . esc_html__( 'TERMS &amp; CONDITIONS', 'projectopia-core' ) . '</h2>';
						echo wp_kses_post( wpautop( $text ) );  
					}
					if ( ! empty( $quote_footer ) ) {
						echo wp_kses_post( wpautop( $quote_footer ) ); 
					} ?>
					<div id="acceptance">
						<?php
						$is_confirmed = isset( $quote_details['confirmed'] ) ? $quote_details['confirmed'] : '';
						if ( ! $is_confirmed ) { ?>
						<h2><?php esc_html_e('Acceptance', 'projectopia-core'); ?></h2>
						<?php 
							$acceptance = get_option( 'quote_acceptance_text' );
							$acceptance = pto_replacement_patterns( $acceptance, $post->ID, 'quote' );
							echo wp_kses_post( wpautop( $acceptance ) ); 
						?>
						<div class="quote_acceptance">
							<form id="submit-quote-conf">
								<input type="hidden" id="quote_id" value="<?php the_ID(); ?>" />
								<input type="hidden" id="pm_name" value="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" />
								<input type="text" id="conf_name" name="conf_name" placeholder="<?php esc_attr_e('Enter your name', 'projectopia-core'); ?>" required /><br />
								<input class="cqpim_button font-white bg-blue mt-20 rounded_2" type="submit" id="accept_quote" value="<?php esc_html_e('Accept', 'projectopia-core'); ?>" />
								<div id="messages"></div>
							</form>	
						</div>
						<?php } else { 
							$conf_by = isset($quote_details['confirmed_details']['by']) ? $quote_details['confirmed_details']['by'] : '';
							$conf_date = isset($quote_details['confirmed_details']['date']) ? $quote_details['confirmed_details']['date'] : '';
							if ( is_numeric($conf_date) ) { $conf_date = wp_date(get_option('cqpim_date_format') . ' H:i', $conf_date); } else { $conf_date = $conf_date; }
							$conf_ip = isset($quote_details['confirmed_details']['ip']) ? $quote_details['confirmed_details']['ip'] : '';               
							?>
							<div class="cqpim-alert cqpim-alert-success alert-display">
								<h5 style="text-transform:uppercase"><?php 
								/* translators: %s: Quote Type */
								printf(esc_html__('THIS %s HAS BEEN ACCEPTED', 'projectopia-core'), esc_html( $upper_type )); ?></h5>
								<p><?php 
								/* translators: %1$s: Accepted By author, %2$s: Timestamp, %3$s: IP */
								printf(esc_html__('Accepted by %1$s @ %2$s from IP Address %3$s', 'projectopia-core'), wp_kses_post( $conf_by ), esc_html( $conf_date ), esc_html( $conf_ip )); ?></p>
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