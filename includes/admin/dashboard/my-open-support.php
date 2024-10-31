<?php 
/**
 * My Open Support Tickets Widget
 *
 * This is my open support ticket widget showing inside projectopia dashboard.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

$tickets = get_option( 'disable_tickets' );
if ( current_user_can( 'edit_cqpim_supports' ) && empty( $tickets ) ) {

$user = wp_get_current_user();
$assigned = pto_get_team_from_userid(); 

$args = array(
    'post_type'      => 'cqpim_support',
    'posts_per_page' => -1,
    'post_status'    => 'private',
    'meta_query'     => array(
        'relation' => 'OR',
        array(
            'key'     => 'ticket_owner',
            'value'   => $assigned,
            'compare' => '=',
        ),
        array(
            'key'     => 'ticket_watchers',
            'value'   => $assigned,
            'compare' => 'LIKE',
        ),
    ),
);

$tickets = get_posts($args);
$total_tickets = count($tickets);
$open_tickets = array();

foreach ( $tickets as $ticket ) {
    $ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
    if ( $ticket_status != 'resolved' ) {
        $open_tickets[] = $ticket;
    }
}

$ordered = array();
foreach ( $open_tickets as $ticket ) {
    $ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);
    if ( ! empty($ticket_updated) ) {
        $ordered[ $ticket_updated ] = $ticket;
    }
}

krsort($ordered);

?>

<!-- This is My Open Support Tickets Widget -->
<div class="card">

    <!-- Widget header -->
    <div class="card-header d-block d-md-flex text-center text-md-left">
        <div class="card-header-info d-inline-flex align-items-center">
            <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
            <h5 class="mb-0"><?php esc_html_e( 'My Open Support Tickets', 'projectopia-core' ); ?></h5>
        </div>
        <div class="card-header-btn mt-2 mt-md-0">
            <a class="piaBtn d-inline-block"
                href="<?php echo esc_url( admin_url('post-new.php?post_type=cqpim_support') ); ?>">
                <?php esc_html_e('Add Ticket', 'projectopia-core') ?>
            </a>
        </div>
    </div>

    <!-- Widget contents. -->
    <div class="card-body">
        <div class="table-responsive-wrapper">
            <?php if ( ! empty( $ordered ) ) { ?>
            <table id="pto-my-open-support-data"
                class="table-responsive-sm piaTableData table table-bordered w-100">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '#', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Client', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Ticket Title', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Assigned To', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Priority', 'projectopia-core' ); ?></th>
                        <th><?php esc_html_e( 'Last Updated', 'projectopia-core' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $index = 0;
                        foreach ( $ordered as $ticket ) {

                            $ticket_author = $ticket->post_author;
							$author_details = get_user_by('id', $ticket_author);
							$ticket_owner = get_post_meta($ticket->ID, 'ticket_owner', true);
							$owner_details = get_post_meta($ticket_owner, 'team_details', true);
							$owner_name = isset($owner_details['team_name']) ? $owner_details['team_name'] : '';
							$ticket_client = get_post_meta($ticket->ID, 'ticket_client', true);
							$client_details = get_post_meta($ticket_client, 'client_details', true);
							$client_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
							$ticket_status = get_post_meta($ticket->ID, 'ticket_status', true);
							$ticket_watchers = get_post_meta($ticket->ID, 'ticket_watchers', true);

                            if ( empty( $ticket_watchers ) ) {
								$ticket_watchers = array();
							}

                            $watching = '';
							if ( in_array( $assigned, $ticket_watchers ) ) {
								$watching = '<img title="' . esc_attr__('Watched Support Ticket', 'projectopia-core') . '" src="' . PTO_PLUGIN_URL . '/img/watching.png" />';
							}

							$ticket_priority = get_post_meta($ticket->ID, 'ticket_priority', true);
							$ticket_updated = get_post_meta($ticket->ID, 'last_updated', true);

							if ( is_numeric( $ticket_updated ) ) {
                                $ticket_updated = wp_date( get_option('cqpim_date_format') . ' H:i', $ticket_updated);
                            }

							$priority = 'low';
							if ( ! empty( $ticket_priority ) ) {
								$support_ticket_priorities = get_option( 'support_ticket_priorities');
								if ( ! empty( $support_ticket_priorities[ $ticket_priority ] ) ) {
									$color_code = $support_ticket_priorities[ $ticket_priority ];
									$priority = '<span style="text-transform:capitalize;border:solid 1px '. esc_attr( $color_code ) .' !important;color:'. esc_attr( $color_code ) .' !important" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . esc_html( $ticket_priority ) . '</span>';
								} else {
									$priority = '<span style="text-transform:capitalize;" class="cqpim_button cqpim_small_button font-blue-sharp border-blue-sharp op nolink">' . esc_html( $ticket_priority ) . '</span>';
								}
							}

                            $index++;

                            //Prepare the row
                            printf(
                                '<tr>
                                    <td>%s</td>
                                    <td><a href="%s"> %s </a></td>
                                    <td><a href="%s"> %s </a></td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                </tr>',
                                esc_html( $index ),
                                esc_url( get_edit_post_link( $ticket_client ) ),
                                esc_html( $client_name ),
                                esc_url( get_edit_post_link( $ticket->ID ) ),
                                esc_html( $ticket->post_title ),
                                wp_kses_post( $owner_name . ' ' . $watching ),
                                wp_kses_post( $ticket_priority ),
                                esc_html( $ticket_updated )
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
                    esc_html__( 'No open tickets to show...', 'projectopia-core' )
                );
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>
