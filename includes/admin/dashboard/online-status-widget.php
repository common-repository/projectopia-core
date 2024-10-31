<?php 
/**
 * Online status widget
 *
 * This is online status widget showing inside projectopia dashboard.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

//Check user access permission and it is enabled or not.
if ( current_user_can( 'cqpim_dash_view_whos_online') && get_option( 'cqpim_online_widget' ) == 1 ) {

    $logged_in_users = get_transient( 'online_status' );
    $online_clients  = $online_team_members = '';

    if ( ! empty( $logged_in_users ) ) {

        foreach ( $logged_in_users as $key => $value ) {

            $user     = get_user_by( 'id', $key );
            $avatar   = get_avatar( $user->ID, 54, '', false, array(
				'force_display' => true,
				'class'         => 'img-fluid',
			) );
            if ( empty( $avatar ) ) {
                $avatar = sprintf(
                    '<img src="%s" alt="%s" class="img-fluid" />',
                    PTO_PLUGIN_URL . '/assets/admin/img/header-profile.png',
                    $user->display_name
                );
            }

            //Get client/team cpt post id from user.
            $member_post_id = 0;
            if ( in_array( 'cqpim_client', $user->roles ) ) {
                $client = pto_get_client_from_userid( $user );
                $member_post_id = $client['assigned'];
            } else {
                $member_post_id = pto_get_team_from_userid( $user );
            }

            $member = sprintf(
                '<div class="activeInfo">
                    <a href="%1$s" title="%2$s">
                        <div class="activeImg">
                            %3$s
                            <span class="chatOn"></span>
                        </div>
                        <div class="activeName">
                            <h3> %2$s </h3>
                        </div>
                    </a>
                </div>',
                get_edit_post_link( $member_post_id ),
                $user->display_name,
                $avatar
            );

            if ( in_array( 'cqpim_client', $user->roles ) ) {
                $online_clients .= $member;
            } else {
                $online_team_members .= $member;
            }
        }
    }
?>

<!-- This is online status widget -->
<div class="projectActivities ProjectUpdatesWrapper activeClientWrapper">
    <div class="card">

        <!-- Widget title -->
        <div class="card-header d-block d-md-flex">
            <div class="card-header-info d-flex align-items-center">
                <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
                <h5 class="mb-0"><?php esc_html_e('Who\'s Online?', 'projectopia-core'); ?></h5>
            </div>
        </div>

        <!-- List of clients who is currently active -->
        <div class="card-body activeClientInfo">
            <div class="activePersonTitle">
                <h3><?php esc_html_e( 'Clients', 'projectopia-core' ) ?></h3>
            </div>
            <div class="activeCard">
            <?php
                if ( empty( $online_clients ) ) {
                    echo '<p>' . esc_html__( 'There are no clients online', 'projectopia-core' ) . '</p>';
                } else {
                    echo wp_kses_post( $online_clients );
                }
            ?>
            </div>

            <!-- List of team members who is currently active -->
            <div class="activePersonTitle">
                <h3><?php esc_html_e( 'Team Member', 'projectopia-core' ) ?></h3>
            </div>
            <div class="activeCard">
            <?php
                if ( empty( $online_team_members ) ) {
                    echo '<p>' . esc_html__( 'There are no team members online', 'projectopia-core' ) . '</p>';
                } else {
                    echo wp_kses_post( $online_team_members );
                }
            ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
