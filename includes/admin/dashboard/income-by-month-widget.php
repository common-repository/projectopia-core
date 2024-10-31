<?php
/**
 * Project status widget
 *
 * This is Project status widget showing inside projectopia dashboard.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

//Year wise filter value.
$invoice_year = pto_get_transient('invoice_year');
$selected = ! empty($invoice_year) ? $invoice_year : date('Y');

//Payment type wise filter value.
$invoice_payments = pto_get_transient('invoice_payments');
$control_type = ! empty($invoice_payments) ? $invoice_payments : 'invoice';

//Widget title.
$widget_title = __( 'Income by Month', 'projectopia-core' );
if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) {
    $widget_title = __( 'Income / Expenditure by Month', 'projectopia-core' );
}

//Prepare income array of current year monthly wise.
if ( empty( $invoice_year ) ) {
    pto_set_transient( 'invoice_year', date( 'Y' ) );
}

if ( empty( $invoice_payments ) ) {
    pto_set_transient('invoice_payments','invoice');
}

$invoices = get_posts( [
    'post_type'      => 'cqpim_invoice',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
] );

$invoices_generated = [];
if ( pto_get_transient( 'invoice_payments' ) == 'invoice' ) {
    foreach ( $invoices as $invoice ) {
        unset($invoice_date);
        $invoice_details = get_post_meta( $invoice->ID, 'invoice_details', true );
        $invoice_date    = isset($invoice_details['invoice_date']) ? $invoice_details['invoice_date'] : '';
        $invoice_totals  = get_post_meta($invoice->ID, 'invoice_totals', true);
        $invoice_total   = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
        if ( is_numeric( $invoice_date ) ) {
            $invoice_date = gmdate( 'd,m,Y', $invoice_date );
            $invoice_date = explode( ',', $invoice_date );
            if ( empty( $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] ) ) {
                $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = 0;
            }
            $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] + $invoice_total;
        }
    }
} else {
    foreach ( $invoices as $invoice ) {
        $invoice_details = get_post_meta($invoice->ID, 'invoice_details', true);
        $invoice_payments = get_post_meta($invoice->ID, 'invoice_payments', true);
        if ( ! empty( $invoice_payments ) ) {
            foreach ( $invoice_payments as $payment ) {
                $invoice_date = gmdate('d,m,Y', $payment['date']);
                $invoice_date = explode(',', $invoice_date);
                if ( empty($invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ]) ) {
                    $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = 0;
                }
                $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] + $payment['amount'];
            }
        }
    }                           
}
$invoice_year = pto_get_transient( 'invoice_year' );

$data = isset( $invoices_generated[ $invoice_year ] ) ? $invoices_generated[ $invoice_year ] : '';
$amounts = array();
$months = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
foreach ( $months as $month ) {
    if ( empty($data[ $month ]) ) {
        $data[ $month ] = 0;
    }
}
$data = is_array( $data ) ? $data : array();
ksort( $data );
foreach ( $data as $key => $month ) {
    $amounts[] = $month;
}

//Get current month income.
$current_month_income = 0;
if ( ! empty( $amounts[ gmdate( 'm' ) - 1 ] ) ) {
    $current_month_income = $amounts[ gmdate( 'm' ) - 1 ];
}

$data = implode(', ', $amounts);

//Prepare expenses array of current year monthly wise.
if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) {
    if (empty($invoice_year))
        pto_set_transient('invoice_year', date('Y')); 
    $invoices_generated = array();
    $args = array(
        'post_type'      => 'cqpim_expense',
        'posts_per_page' => -1,
        'post_status'    => 'private',
    );
    $invoices = get_posts($args);
    $invoices_generated = array();
    foreach ( $invoices as $invoice ) {
        unset($auth);
        $author = $invoice->post_author;
        $invoice_date = $expense_date = get_post_meta($invoice->ID, 'expense_date', true);
        $invoice_totals = get_post_meta($invoice->ID, 'invoice_totals', true); 
        $invoice_total = isset($invoice_totals['total']) ? $invoice_totals['total'] : 0;
        $invoice_date = gmdate('d,m,Y', $invoice_date);
        $invoice_date = explode(',', $invoice_date);
        $auth = get_post_meta($invoice->ID, 'auth_active', true);
        $auth_limit = get_option('cqpim_expense_auth_limit');
        $authorised = get_post_meta($invoice->ID, 'authorised', true);
        if ( empty($invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ]) ) {
            $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = 0;
        }
        if ( user_can($author, 'cqpim_bypass_expense_auth') || empty($auth) || ! empty($auth) && ! empty($authorised) && $authorised == 1 || ! empty($auth) && empty($authorised) && ! empty($auth_limit) && $auth_limit > $invoice_total ) {                          
            $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] = $invoices_generated[ $invoice_date[2] ][ $invoice_date[1] ] + $invoice_total;
        }
    }
    $invoice_year = pto_get_transient('invoice_year');
    $data2 = isset($invoices_generated[ $invoice_year ]) ? $invoices_generated[ $invoice_year ] : '';
    $amounts = array();
    $months = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
    foreach ( $months as $month ) {
        if ( empty($data2[ $month ]) ) {
            $data2[ $month ] = 0;
        }
    }
    $data2 = is_array($data2) ? $data2 : array();
    ksort($data2);
    foreach ( $data2 as $key => $month ) {
        $amounts[] = $month;
    }

    $data2 = implode(', ', $amounts);                                       
}

?>

<!-- Start income and expense widget markups -->
<div class="card ">
    <div class="card-header d-block d-md-flex ">
        <div class="card-header-info d-flex align-items-center">
            <img src="<?php echo esc_url( PTO_PLUGIN_URL ) ?>/assets/admin/img/taskIcon.png" alt="" class="img-fluid mr-2" />
            <h5 class="mb-0"><?php echo esc_html( $widget_title ); ?> </h5>
        </div>
    </div>
    <div class="card-header d-block d-md-flex mt-3">
        <!-- Widget Header -->
        <div class="card-header-info d-flex align-items-center">
            <img src="<?php echo esc_url( PTO_PLUGIN_URL ) ?>/assets/admin/img/revenue.svg" alt="" class="img-fluid mr-2" />
            <div class="content">
                <h4 class="mb-0">                    
                    <?php echo pto_format_currency($current_month_income); ?>
                </h4>
                <p><?php esc_html_e( 'This Month Revenue', 'projectopia-core' )?></p>
            </div>
        </div>
        <!-- Widget Filters -->
        <div class="card-header-btn mt-2 mt-md-0">
            <div class="selectDropdown d-block d-sm-flex flex-wrap align-items-center">
                <div class="dropdownInner padding-ls-small mr-3 mt-sm-2" data-prev-content="Year">
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
                <div class="dropdownInner padding-ls-medium mt-2 mt-sm-2" data-prev-content="Show">
                    <select id="income_control_type" class="selectDropdown-init form-control">
                        <?php $p_type = 'invoice'; ?>
                        <option value="<?php echo esc_attr( $p_type ); ?>" <?php if ( $p_type == $control_type ) { ?> selected="selected"<?php } ?>><?php esc_html_e('All Invoices', 'projectopia-core'); ?></option>
                        <?php $p_type = 'payment'; ?>
                        <option value="<?php echo esc_attr( $p_type ); ?>" <?php if ( $p_type == $control_type ) { ?> selected="selected"<?php } ?>><?php esc_html_e('Only Paid Invoices', 'projectopia-core'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body mt-4">
        <div class="tab-content" id="monthOfrevenue-tab-content">
            <div class="tab-pane fade show active" id="thisMonth" role="tabpanel" aria-labelledby="thisMonth">
                <!-- Income expense graph -->
                <div id="bar-chart-1"
                    data-income="<?php if ( ! empty( $data ) ) { echo esc_attr( $data ); } ?>"
                    data-income-active = "1" 
                    data-expense="<?php if ( ! empty( $data2 ) ) { echo esc_attr( $data2 ); } ?>"
                    data-expense-active="<?php echo esc_attr( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ); ?>"
                    >
                </div>
            </div>            
        </div>
    </div>
</div>
