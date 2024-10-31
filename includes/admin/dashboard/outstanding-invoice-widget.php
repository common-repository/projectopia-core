<?php 
/**
 * Pending quote and estimate widget
 *
 * This is quote and estimate widget showing inside projectopia dashboard.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */


$disable_invoices = get_option( 'disable_invoices' );
if ( empty( $disable_invoices ) && current_user_can( 'edit_cqpim_invoices' ) ) { 
    $args = array(
        'post_type'      => 'cqpim_invoice',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );
    $invoices = get_posts($args);
    $this_client = array();
    $total_value = 0;
    $total_out = 0;
    foreach ( $invoices as $invoice ) {
        $invoice_details = get_post_meta( $invoice->ID, 'invoice_details', true );
        $paid = isset( $invoice_details['paid'] ) ? $invoice_details['paid'] : '';
        if ( $paid != 1 ) {
            $this_client[] = $invoice;
        }
    }
?>

<!-- This pending quotes and estimate widget -->
<div class="card">

    <!-- Widget header -->
    <div class="card-header d-block d-md-flex text-center text-md-left">
        <div class="card-header-info d-inline-flex align-items-center">
            <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
            <h5 class="mb-0"><?php esc_html_e( 'Outstanding Invoices', 'projectopia-core' ); ?></h5>
        </div>
        <div class="card-header-btn mt-2 mt-md-0">
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cqpim_invoice' ) ); ?>"
                class="piaBtn d-inline-block"><?php esc_html_e('Add Invoice', 'projectopia-core') ?>
            </a>
        </div>
    </div>

    <!-- Widget contents. -->
    <div class="card-body">
        <div class="table-responsive-wrapper">
        <?php if ( ! empty( $this_client ) && is_array( $this_client ) ) { ?>
            <table id="pto-dashboard-invoice-outstanding"
                class="table-responsive-sm piaTableData table table-bordered w-100">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Due', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Total', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Outstanding', 'projectopia-core' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $index = 0;
                        foreach ( $this_client as $invoice ) {
                            $outstanding = 0;
                            $currency = get_option('currency_symbol');
                            $invoice_link = get_edit_post_link($invoice->ID);
                            $invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);    
                            $invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true);
                            $invoice_date = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
                            $total = isset($invoice_totals['total']) ? $invoice_totals['total'] : '';
                            $project_id = isset($invoice_details['project_id']) ? $invoice_details['project_id'] : '';
                            $invoice_id = get_post_meta($invoice->ID, 'invoice_id', true);
                            $project_details = get_post_meta($project_id, 'project_details', true);
                            $project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
                            $project_link = get_edit_post_link($project_id);
                            $due = isset($invoice_details['terms_over']) ? $invoice_details['terms_over'] : '';
                            $on_receipt = isset($invoice_details['on_receipt']) ? $invoice_details['on_receipt'] : '';
                            if ( empty($on_receipt) ) {
                                $due_string = wp_date(get_option('cqpim_date_format'), $due);
                            } else {
                                $due_string = __('Due on Receipt', 'projectopia-core');
                            }
                            $paid = isset($invoice_details['paid']) ? $invoice_details['paid'] : '';
                            $sent = isset($invoice_details['sent']) ? $invoice_details['sent'] : '';
                            $invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
                            $total_paid = 0;
                            if ( empty($invoice_payments) ) {
                                $invoice_payments = array();
                            }
                            foreach ( $invoice_payments as $payment ) {
                                $amount = isset($payment['amount']) ? $payment['amount'] : 0;
                                $total_paid = $total_paid + (float) $amount;
                            }
                            if ( ! empty( $total_paid ) && is_numeric( $total_paid ) && is_numeric( $total ) ) {
                                $outstanding = $total - $total_paid;
                            } else {
                                $outstanding = $total;
                            }
                            $now = time();
                            if ( empty( $paid ) ) {
                                if ( ! empty( $due ) ) {
                                    if ( $now > $due ) {
                                        $p_status = '<span class="status clientApproval">' . __( 'OVERDUE', 'projectopia-core' ) . '</span>';     
                                    } else {
                                        if ( ! empty($sent) ) {
                                            $p_status = '<span class="status approved">' . __( 'Sent', 'projectopia-core' ) . '</span>';                          
                                        } else {
                                            $p_status = '<span class="status notSent">' . __( 'Not Sent', 'projectopia-core' ) . '</span>';                           
                                        }
                                    }
                                }
                            } else {
                                $p_status = '<span class="task_complete status approved">' . __( 'PAID', 'projectopia-core' ) . '</span>';
                            }

                            //Prepare the row
                            printf(
                                '<tr>
                                    <td><a href="%s"> %s </a></td>
                                    <td>%s</td>
                                    <td> %s </td>
                                    <td>%s</td>
                                    <td>%s</td>
                                </tr>',
                                esc_url( $invoice_link ),
                                esc_html( $invoice_id ),
                                esc_html( $due_string ),
                                wp_kses_post( $p_status ),
                                esc_html( pto_calculate_currency( $invoice->ID, $total ) ),
                                esc_html( pto_calculate_currency( $invoice->ID, $outstanding ) )
                            );
                        }
                    ?>		
                </tbody>
            </table>
            <?php 
            } else {
                //There is no data then show message.
                printf(
                    '<div class="p-3">%s</div>',
                    esc_html__( 'No outstanding invoices to show...', 'projectopia-core' )
                );
            } ?>
        </div>
    </div>
</div>
<?php } ?>
