<?php
/**
 * Project expense meta box register callback function.
 *
 * @param Object $post
 */
function pto_project_expenses_metabox_callback( $post ) {
	$args = array(
		'post_type'      => 'cqpim_expense',
		'posts_per_page' => -1,
		'post_status'    => 'private',
		'meta_key'       => 'project_id',
		'meta_value'     => $post->ID,
	);
	$expenses = get_posts($args);
	if ( empty($expenses) ) { ?>
		<p><?php esc_html_e('There are no expenses on this project.', 'projectopia-core'); ?></p>
	<?php } else { ?>
		<div class="card p-0 m-0">
			<table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 no-footer dataTable">
				<thead>
					<tr>
						<th><?php esc_html_e('ID', 'projectopia-core'); ?></th>
						<th><?php esc_html_e('Title', 'projectopia-core'); ?></th>
						<th style="display:none"><?php esc_html_e('Stamp', 'projectopia-core'); ?></th>
						<th><?php esc_html_e('Date', 'projectopia-core'); ?></th>
						<th><?php esc_html_e('Supplier', 'projectopia-core'); ?></th>
						<th><?php esc_html_e('Amount', 'projectopia-core'); ?></th>
						<th><?php esc_html_e('Status', 'projectopia-core'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $expenses as $expense ) { 
						$expense_date = get_post_meta($expense->ID, 'expense_date', true);
						$auth = get_post_meta($expense->ID, 'auth_active', true);
						$auth_limit = get_option('cqpim_expense_auth_limit');
						$totals = get_post_meta($expense->ID, 'invoice_totals', true);
						$total = isset($totals['total']) ? $totals['total'] : '';
						$authorised = get_post_meta($expense->ID, 'authorised', true); 
						$auth_requested = get_post_meta($expense->ID, 'auth_requested', true);
						$supplier_id = get_post_meta($expense->ID, 'supplier_id', true); 
						$supplier = get_post($supplier_id);
						if ( empty($auth) ) {
							$status = array(
								'classes' => 'badgeOverdue approved',
								'message' => __('Live', 'projectopia-core'),
							);
						} else {
							if ( ! empty($authorised) ) {
								if ( $authorised == 1 ) {
									$status = array(
										'classes' => 'badgeOverdue approved',
										'message' => __('Live (Authorised)', 'projectopia-core'),
									);      
								}
								if ( $authorised == 2 ) {
									$status = array(
										'classes' => 'badgeOverdue',
										'message' => __('Authorisation Declined', 'projectopia-core'),
									);      
								}
							} else {
								if ( ! empty($auth_requested) ) {
									$status = array(
										'classes' => 'badgeOverdue clientApproval',
										'message' => __('Awaiting Authorisation', 'projectopia-core'),
									);                                              
								} else {
									if ( ! empty($total) ) {
										if ( ! empty($auth_limit) ) {
											if ( $total < $auth_limit ) {
												$status = array(
													'classes' => 'badgeOverdue approved',
													'message' => __('Live (Authorisation Not Required)', 'projectopia-core'),
												);                                                          
											} else {
												$status = array(
													'classes' => 'badgeOverdue',
													'message' => __('Requires Authorisation', 'projectopia-core'),
												);                                                          
											}
										} else {
											$status = array(
												'classes' => 'badgeOverdue',
												'message' => __('Requires Authorisation', 'projectopia-core'),
											);                                                      
										}
									} else {
										$status = array(
											'classes' => 'badgeOverdue normal',
											'message' => __('New', 'projectopia-core'),
										);                                                  
									}
								}
							}                               
						}
						?>
						<tr>
							<td><a href="<?php echo esc_url( get_edit_post_link($expense->ID) ); ?>"><?php echo esc_html( $expense->ID ); ?></a></td>
							<td><a href="<?php echo esc_url( get_edit_post_link($expense->ID) ); ?>"><?php echo esc_html( $expense->post_title ); ?></a></td>
							<td style="display:none"><?php echo esc_html( $expense_date ); ?></td>
							<td><?php echo esc_html( wp_date(get_option('cqpim_date_format'), $expense_date) ); ?></td>
							<td><?php echo isset($supplier->post_title) ? esc_html( $supplier->post_title ) : ''; ?></td>
							<td><?php echo esc_html( pto_calculate_currency($expense->ID, $total) ); ?></td>
							<td><div class="<?php echo esc_attr( $status['classes'] ); ?>"><?php echo esc_html( $status['message'] ); ?></div></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php 
	}
	do_action('pto_project_expenses_metabox', $post);
}