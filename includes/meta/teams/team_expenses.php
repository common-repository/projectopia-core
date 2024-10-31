<?php

function pto_team_expenses_metabox_callback( $post ) {
	$team_details = get_post_meta( $post->ID, 'team_details', true );
	$team_user = isset( $team_details['user_id'] ) ? $team_details['user_id'] : '';
	$expenses = array();
	if ( ! empty( $team_user ) ) {
		$team_user_object = get_user_by( 'id', $team_user );
		if ( isset( $team_user_object->ID ) ) {
			$args = array(
				'post_type'      => 'cqpim_expense',
				'posts_per_page' => -1,
				'post_status'    => 'private',
				'author'         => $team_user_object->ID,
			);
			$expenses = get_posts( $args );  
		}
	} 
	if ( empty( $expenses ) ) { ?>
		<p class="mt-3"><?php esc_html_e( 'This team member has not added any expenses', 'projectopia-core' ); ?></p>
	<?php } else { ?>
		<div class="card p-0 m-0">
			<table class="p-0 piaTableData table-responsive-lg table table-bordered w-100 dataTable" id="front_teamexpences">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Title', 'projectopia-core' ); ?></th>
						<th style="display: none;"><?php esc_html_e('Stamp', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Date', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Supplier', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'projectopia-core' ); ?></th>
						<th><?php esc_html_e( 'Status', 'projectopia-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $expenses as $expense ) { 
						$expense_date = get_post_meta( $expense->ID, 'expense_date', true );
						$auth = get_post_meta( $expense->ID, 'auth_active', true );
						$auth_limit = get_option( 'cqpim_expense_auth_limit' );
						$totals = get_post_meta( $expense->ID, 'invoice_totals', true );
						$total = isset( $totals['total'] ) ? $totals['total'] : '';
						$authorised = get_post_meta( $expense->ID, 'authorised', true ); 
						$auth_requested = get_post_meta( $expense->ID, 'auth_requested', true );
						$supplier_id = get_post_meta( $expense->ID, 'supplier_id', true ); 
						$supplier = get_post( $supplier_id );
						$author = $expense->post_author;
						if ( empty( $auth ) ) {
							$status = array(
								'color'   => 'approved',
								'message' => __( 'Live', 'projectopia-core' ),
							);
						} else {
							if ( ! empty( $authorised ) ) {
								if ( $authorised == 1 ) {
									$status = array(
										'color'   => 'approved',
										'message' => __( 'Live (Authorised)', 'projectopia-core' ),
									);      
								}
								if ( $authorised == 2 ) {
									$status = array(
										'color'   => 'overdue',
										'message' => __( 'Authorisation Declined', 'projectopia-core' ),
									);      
								}
							} else {
								if ( ! empty( $auth_requested ) ) {
									$status = array(
										'color'   => 'clientApproval',
										'message' => __( 'Awaiting Authorisation', 'projectopia-core' ),
									);                                              
								} else {
									if ( ! empty( $total ) ) {
										if ( ! empty( $auth_limit ) ) {
											if ( $total < $auth_limit || user_can( $author, 'cqpim_bypass_expense_auth' ) ) {
												$status = array(
													'color'   => 'approved',
													'message' => __( 'Live (Authorisation Not Required)', 'projectopia-core' ),
												);                                                          
											} else {
												$status = array(
													'color'   => 'overdue',
													'message' => __( 'Requires Authorisation', 'projectopia-core'),
												);                                                          
											}
										} else {
											if ( user_can( $author, 'cqpim_bypass_expense_auth' ) ) {
												$status = array(
													'color'   => 'approved',
													'message' => __( 'Live (Authorisation Not Required)', 'projectopia-core' ),
												);                                                          
											} else {
												$status = array(
													'color'   => 'overdue',
													'message' => __( 'Requires Authorisation', 'projectopia-core' ),
												);                                                          
											}                                                       
										}
									} else {
										$status = array(
											'color'   => 'normal',
											'message' => __( 'New', 'projectopia-core' ),
										);                                                  
									}
								}
							}                                   
						}
						?>
						<tr>
							<td><a href="<?php echo esc_url( get_edit_post_link( $expense->ID ) ); ?>"><?php echo esc_html( $expense->ID ); ?></a></td>
							<td><a href="<?php echo esc_url( get_edit_post_link( $expense->ID ) ); ?>"><?php echo esc_html( $expense->post_title ); ?></a></td>
							<td style="display: none;"><?php echo esc_html( $expense_date ); ?></td>
							<td><?php echo esc_html( wp_date( get_option( 'cqpim_date_format' ), $expense_date ) ); ?></td>
							<td><?php echo isset( $supplier->post_title ) ? esc_html( $supplier->post_title ) : ''; ?></td>
							<td><?php echo esc_html( pto_calculate_currency( $expense->ID, $total ) ); ?></td>
							<td><span class="status <?php echo esc_attr( $status['color'] ); ?>"><?php echo esc_html( $status['message'] ); ?></span></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
 		<?php 

		// Year wise filter value.
		$invoice_year = pto_get_transient( 'invoice_year' );
		$selected = ! empty( $invoice_year ) ? $invoice_year : date( 'Y' );

		// Prepare income array of current year monthly wise.
		if ( empty( $invoice_year ) ) {
		    pto_set_transient( 'invoice_year', date( 'Y' ) );
		}

		foreach ( $expenses as $expense ) {
			unset( $auth );
			$author = $expense->post_author;
			$invoice_date = $expense_date = get_post_meta( $expense->ID, 'expense_date', true );
			$invoice_totals = get_post_meta( $expense->ID, 'invoice_totals', true ); 
			$invoice_total = isset( $invoice_totals['total'] ) ? $invoice_totals['total'] : 0;
			$invoice_date = gmdate( 'd,m,Y', $invoice_date );
			$invoice_date = explode( ',', $invoice_date );
			$auth = get_post_meta( $expense->ID, 'auth_active', true );
			$auth_limit = get_option( 'cqpim_expense_auth_limit' );
			$authorised = get_post_meta( $expense->ID, 'authorised', true );
			if ( empty($invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ]) ) {
				$invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = 0;
			}
			if ( user_can($author, 'cqpim_bypass_expense_auth') || empty($auth) || ! empty($auth) && ! empty($authorised) && $authorised == 1 || ! empty($auth) && empty($authorised) && ! empty($auth_limit) && $auth_limit > $invoice_total ) {                          
				$invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] + $invoice_total;
			}
		}
		$invoice_year = pto_get_transient( 'invoice_year' );
		$data = isset( $invoices_generated[ $invoice_year ] ) ? $invoices_generated[ $invoice_year ] : '';
		$amounts = array();
		$months = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
		foreach ( $months as $month ) {
			if ( empty( $data[ $month ] ) ) {
				$data[ $month ] = 0;
			}
		}
		$data = is_array( $data ) ? $data : array();
		ksort( $data );
		foreach ( $data as $key => $month ) {
			$amounts[] = $month;
		}
		$data = implode( ', ', $amounts );

		?>
		<div class="card p-0 m-0">
		    <div class="card-header d-block d-md-flex mt-3">
				<!-- Widget Filters -->
		        <div class="card-header-btn mt-2 mt-md-0">
		            <div class="selectDropdown d-block d-sm-flex flex-wrap align-items-center">
		                <div class="dropdownInner team-member-expenses padding-ls-small mr-3 mt-sm-2" data-prev-content="<?php esc_attr_e('Team Member Expenses by Month', 'projectopia-core'); ?>">
		                    <select id="income_control_date" class="selectDropdown-init form-control">
		                        <?php $date = date('Y'); ?>
		                        <option value="<?php echo esc_attr( $date ); ?>" <?php if ( $date == $selected ) { ?> selected="selected"<?php } ?>><?php echo esc_html( $date ); ?></option>
		                        <?php $date = date('Y', strtotime("-1 year")); ?>
		                        <option value="<?php echo esc_attr( $date ); ?>" <?php if ( $date == $selected ) { ?> selected="selected"<?php } ?>><?php echo esc_html( $date ); ?></option>
		                        <?php $date = date('Y', strtotime("-2 years")); ?>
		                        <option value="<?php echo esc_attr( $date ); ?>" <?php if ( $date == $selected ) { ?> selected="selected"<?php } ?>><?php echo esc_html( $date ); ?></option>
		                        <?php $date = date('Y', strtotime("-3 years")); ?>
		                        <option value="<?php echo esc_attr( $date ); ?>" <?php if ( $date == $selected ) { ?> selected="selected"<?php } ?>><?php echo esc_html( $date ); ?></option>
		                    </select>
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="card-body mt-4">
		        <div class="tab-content" id="monthOfrevenue-tab-content">
		            <div class="tab-pane fade show active" id="thisMonth" role="tabpanel" aria-labelledby="thisMonth">
		                <!-- Income expense graph -->
		                <div id="bar-chart-1" data-expense="<?php if ( ! empty( $data ) ) { echo esc_attr( $data ); } ?>" data-expense-active="<?php echo esc_attr( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ); ?>"></div>
		            </div>            
		        </div>
		    </div>
		</div>
		<?php
	}
}