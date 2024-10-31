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

$quotes_enabled = get_option('enable_quotes');
if ( ! empty( $quotes_enabled ) && current_user_can( 'edit_cqpim_quotes' ) ) { 

    $quotes = get_posts( [
        'post_type'      => 'cqpim_quote',
        'posts_per_page' => -1,
        'post_status'    => 'private',
    ] );

    $index = 0;
?>

<!-- This pending quotes and estimate widget -->
<div class="card">

    <!-- Widget header -->
    <div class="card-header d-block d-md-flex text-center text-md-left">
        <div class="card-header-info d-inline-flex align-items-center">
            <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
            <h5 class="mb-0"><?php esc_html_e( 'Pending Quotes / Estimates', 'projectopia-core' ); ?></h5>
        </div>
        <div class="card-header-btn mt-2 mt-md-0">
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cqpim_quote' ) ); ?>"
                class="piaBtn d-inline-block"><?php esc_html_e('Add Quote', 'projectopia-core') ?>
            </a>
        </div>
    </div>

    <!-- Widget contents. -->
    <div class="card-body">
        <div class="table-responsive-wrapper">
            <?php if ( ! empty( $quotes ) ) { ?>
            <table id="pto-pending-quote-estimate-data"
                class="table-responsive-sm piaTableData table table-bordered w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php esc_html_e('Title', 'projectopia-core'); ?></th>
                        <th><?php esc_html_e('Client', 'projectopia-core'); ?></th>
                        <th><?php esc_html_e('Status', 'projectopia-core'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ( $quotes as $quote ) {

                        //If quote is empty then continue
                        if ( empty( $quote ) || empty( $quote->ID ) ) {
                            continue;
                        }

                        //If quote detail is empty then continue
                        $quote_details = get_post_meta($quote->ID, 'quote_details', true);
                        if ( empty( $quote_details ) || empty( $quote_details['client_id'] ) ) {
                            continue;
                        }

                        //If client is already approved the quote then continue
                        $confirmed = isset($quote_details['confirmed']) ? $quote_details['confirmed'] : '';
                        if ( ! empty( $confirmed ) ) {
                            continue;
                        }

                        $client         = get_post_meta($quote_details['client_id'], 'client_details', true);
                        $client_company = isset($client['client_company']) ? $client['client_company'] : '';
                        $sent           = isset( $quote_details['sent'] ) ? $quote_details['sent'] : '';

                        $p_status       = __('New / Not Sent', 'projectopia-core');
                        $status_class = 'status notSent';

                        if ( ! empty( $sent ) ) {
                            $p_status       = __('Awaiting Client Approval', 'projectopia-core');
                            $status_class = 'status clientApproval';
                        }

                        $index++;

                        //Prepare the row
                        printf(
                            '<tr>
                                <td>%s</td>
                                <td><a href="%s"> %s </a></td>
                                <td><a href="%s"> %s </a></td>
                                <td><span class="%s">%s</span></td>
                            </tr>',
                            esc_html( $index ),
                            esc_url( get_edit_post_link( $quote->ID ) ),
                            esc_html( $quote->post_title ),
                            esc_url( get_edit_post_link( $quote_details['client_id'] ) ),
                            esc_html( $client_company ),
                            esc_attr( $status_class ),
                            esc_html( $p_status )
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
                    esc_html__( 'There are no pending quotes or estimates', 'projectopia-core' )
                );
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>
