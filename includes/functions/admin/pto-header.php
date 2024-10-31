<?php
/**
 * Projectopia header template.
 *
 * This is header template file for projectopia.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

$pto_logo = get_option( 'company_logo' );
$logo_url = PTO_PLUGIN_URL . '/assets/admin/img/projectopia-logo.svg';
if ( ! empty( $pto_logo['company_logo'] ) ) {
    $logo_url = $pto_logo['company_logo'];
}

?>
<header class="header" id="pto-header">
    <div class="container-fluid">
        <nav class="navbar d-flex flex-row flex-nowrap justify-content-between align-items-center">

            <!-- This is mobile device toggle button. -->
            <button type="button" class="btnNavToggle btn border btn-sm  mr-2 d-block d-xl-none">
                <i class="fa fa-bars text-light" aria-hidden="true"></i>
            </button>
            <!-- This is wp admin logo. -->
            <a class="navbar-brand my-0 py-0" href="<?php echo esc_url( get_dashboard_url() ); ?>">
                <img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/wp.png' ); ?>" class="img-fluid wp-logo" />
            </a>

            <!-- This is title/logo for projectopia. -->
            <a class="navbar-brand my-0 py-0" href="<?php echo esc_url( admin_url( 'admin.php?page=pto-dashboard' ) ); ?>">
                <img src="<?php echo esc_url( $logo_url ); ?>" class="img-fluid projectopia-logo" />
            </a>

            <div class="navbar-collapse d-flex flex-row">

                <!-- This is top main nav menu -->
                <ul class="navbar-nav responsiveMenu mr-auto d-block d-xl-flex flex-row">

                    <!-- This is mobile device toggle button inside menu sidebar. -->
                    <li class="nav-item mb-3 d-block d-xl-none">
                        <button type="button" class="btnNavToggle border btn-sm btn mr-2">
                            <i class="fa fa-times text-light" aria-hidden="true"></i>
                        </button>
                    </li>

                    <?php
                        $screen = get_current_screen();
                        $style = '';
                        foreach ( $pto_header_nav as $key => $item ) {

                            // Check menu item is accessible or not for current user.
                            if ( isset( $item['can_access'] ) && 1 != $item['can_access'] ) {
                                continue;
                            }

                            //Match current screen with menu item.
                            $item_classes = '';
                            if ( ! empty( $item['screen_id'] ) && strpos( $screen->id, $item['screen_id'] ) !== false ) {
                                $item_classes  = 'active';
                            }

                            // if ( empty( $item['sub_items'] ) ) {
                            //     $style = 'style="margin-right: 10px;"';
                            // }

                            //Prepare menu item for top nav.
                            $nav_item = sprintf(
                                '<li class="nav-item %s"> <a class="nav-link" href="%s" %s> %s </a> </li>',
                                $item_classes,
                                esc_url( $item['link'] ),
                                $style,
                                esc_html( $item['text'] )
                            );

                            //Prepare sub menu items.
                            if ( ! empty( $item['sub_items'] ) ) {

                                $nav_item  = '<div class="dropdown-menu">';

                                foreach ( $item['sub_items'] as $key => $sub_item ) {

                                    // Check menu item is accessible or not for current user.
                                    if ( isset( $sub_item['can_access'] ) && 1 != $sub_item['can_access'] ) {
                                        continue;
                                    }

                                    if ( ! empty( $sub_item['screen_id'] ) && strpos( $screen->id, $sub_item['screen_id'] ) !== false ) {
                                        $item_classes  = 'active';
                                    }

                                    $nav_item .= sprintf( '<a class="dropdown-item" href="%s">%s</a>', $sub_item['link'], $sub_item['text']  );
                                }

                                $nav_item  .= '</div>';

                                $nav_item = sprintf (
                                    '<li class="nav-item dropdown %s">
                                        <a class="nav-link text-light dropdown-toggle" href="%s" role="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">%s</a>
                                            %s
                                    </li>',
                                    $item_classes,
                                    esc_url( $item['link'] ),
                                    esc_html( $item['text'] ) . '<i class="fa fa-angle-down" aria-hidden="true"></i>',
                                    $nav_item
                                );
                            }

                            echo wp_kses_post( $nav_item );
                        }
                    ?>
                </ul>

                <!-- This is right side nav menu like settings, notification, profile and etc. -->
                <ul class="nav navbar-nav navbar-right rightNavbar flex-nowrap flex-row ml-auto justify-content-end align-items-center">
                    <?php if ( pto_is_free_plan() ) { ?>
                        <li>
                            <!-- This is Upgrade Link. -->
                            <div class="dropdown">
                                <a href="<?php echo esc_url( projectopia_fs()->get_upgrade_url() ); ?>" target="_blank" class="btn btn-notify btn-upgrade"><span class="upgrade-text mr-2"><?php esc_html_e('Upgrade', 'projectopia-core'); ?></span><i class="fa fa-rocket" aria-hidden="true"></i></a>
                            </div>
                        </li>
                    <?php } ?>
                    <li>
                        <!-- This is message notification widget. -->
                        <div class="dropdown">
                            <a class="btn btn-notify" style="line-height: 1.8"
                                href="<?php echo esc_url( admin_url( 'admin.php?page=pto-messages' ) ); ?>" aria-expanded="false">
                                <img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/massageicon.svg' ); ?>" class="img-fluid" />
                                <?php
                                    if ( ! empty( $unread_qty ) ) {
                                        printf( '<span class="count">%s</span>', wp_kses_post( $unread_qty ) );
                                    }
                                ?>
                            </a>
                        </div>
                    </li>
                    <li>
                        <!-- This is general notification widget. -->
                        <div class="dropdown">
                            <button class="btn btn-notify pto-noti-count" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/notification.png' ); ?>" class="img-fluid" />
                                <?php
                                    if ( ! empty( $notification_count ) ) {
                                        printf( '<span class="count">%s</span>', wp_kses_post( $notification_count ) );
                                    }
                                ?>
                            </button>
                            <div id="cqpim_notifications" class="pto-notifications-widget dropdown-menu notification-dropdown-menu">
                                <h3 class="font-white"><?php esc_html_e( 'Notifications', 'projectopia-core'); ?></h3>
                                <div id="notification_list" class="rounded_4">
                                    <?php if ( ! empty($notifications) ) { ?>
                                        <ul id="notifications_ul">
                                            <?php
                                            foreach ( $notifications as $key => $notification ) { ?>
                                                <li <?php if ( empty($notification['read']) ) { ?>class="unread"<?php } ?>>
                                                    <?php if ( empty($avatar) ) { ?>
                                                        <div class="notification_avatar">
                                                            <?php echo get_avatar( $notification['from'], 25, '', false, array( 'force_display' => true ) ) ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="notification_message" <?php if ( empty($avatar) ) { ?>style="width:calc(100% - 40px)"<?php } ?>>
                                                        <a href="#" class="notification_item" data-item="<?php echo esc_attr( $notification['item'] ); ?>" data-key="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $notification['message'] ); ?></a><br />
                                                        <span class="notification_time"><?php echo esc_attr( wp_date(get_option('cqpim_date_format') . ' H:i', $notification['time']) ); ?></span>
                                                        <div class="notification_remove"><a class="nf_remove_button" href="#" data-key="<?php echo esc_attr( $key ); ?>" title="<?php esc_attr_e('Clear Notification', 'projectopia-core'); ?>"><i class="fa fa-times-circle"></i></a></div>
                                                    </div>
                                                    <div class="clear"></div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else { ?>
                                        <p style="padding:10px 0;text-align:center;margin:0;"><?php esc_html_e('You do not have any notifications', 'projectopia-core'); ?></p>
                                    <?php } ?>
                                </div>
                                <div id="notification_actions">
                                    <a id="mark_all_read_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#" style="width: 40%;"><?php esc_html_e('Mark All as Read', 'projectopia-core'); ?></a>
                                    <a id="clear_all_read_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#" style="width: 35%;"><?php esc_html_e('Clear All Read', 'projectopia-core'); ?></a>
                                    <a id="clear_all_nf" class="cqpim_button cqpim_small_button font-black rounded_2" href="#"><?php esc_html_e('Clear All', 'projectopia-core'); ?></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php if ( ( pto_has_addon_active_license( 'pto_re', 'reporting' ) && current_user_can( 'cqpim_access_reporting' ) )
                                || current_user_can( 'edit_cqpim_settings' )
                                || get_option( 'cqpim_show_docs_link' ) && current_user_can( 'edit_cqpim_help' ) ) { ?>
                        <li>
                            <!-- This is settings sub menu widget. -->
                            <div class="dropdown">
                                <button class="btn btn-notify" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/settingicon.png' ); ?>" class="img-fluid" />
                                </button>
                                <div class="dropdown-menu notification-dropdown-menu ">
                                    <?php if ( pto_has_addon_active_license( 'pto_re', 'reporting' ) && current_user_can('cqpim_access_reporting') ) { ?>
                                        <a class="dropdown-item" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=pto-reporting"><?php esc_html_e('Reporting', 'projectopia-core'); ?></a>
                                    <?php } ?>
                                    <?php if ( current_user_can('edit_cqpim_settings') ) { ?>
                                        <a class="dropdown-item" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=pto-settings"><?php esc_html_e('Settings', 'projectopia-core'); ?></a>
                                        <?php if ( pto_has_addon_active_license( 'pto_cf', 'customfields' ) && current_user_can('edit_cqpim_settings') ) { ?>
                                            <a class="dropdown-item" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=pto-custom-fields"><?php esc_html_e('Custom Fields', 'projectopia-core'); ?></a>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ( get_option('cqpim_show_docs_link') && current_user_can('edit_cqpim_help') & false ) { ?>
                                        <a class="dropdown-item" href="http://projectopia.io" target="_blank"><?php esc_html_e('Documentation', 'projectopia-core'); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    <li>
                        <!-- This is user profile widget. -->
                        <div class="dropdown">
                            <button class="btn btn-notify btn-profile" type="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 user-name"><?php echo esc_html( $user_name ); ?></span>
                                <?php
                                    $user_avatar = get_avatar( $user->ID, 50, '', false, array( 'force_display' => true ) );
                                    if ( empty( $user_avatar ) ) {
                                        $user_avatar = sprintf(
                                            '<img src="%s" class="userImg img-fluid" />',
                                            esc_url( PTO_PLUGIN_URL . '/assets/admin/img/header-profile.png' )
                                        );
                                    }

                                    echo wp_kses_post( $user_avatar );
                                ?>
                            </button>

                            <div class="dropdown-menu notification-dropdown-menu">
                                <?php
                                    if ( current_user_can( 'cqpim_team_edit_profile' ) ) {
                                        printf (
                                            '<a class="dropdown-item" href="%s">My Profile</a>',
                                            esc_url( admin_url() . 'admin.php?page=pto-manage-profile' )
                                        );
                                    }

                                    $login_page_id = get_option( 'cqpim_login_page' );
                                    $login_url     = get_option( 'cqpim_logout_url' );
                                    if ( empty( $login_url ) ) {
                                        $login_url = get_the_permalink( $login_page_id );
                                    }

                                    printf (
                                        '<a class="dropdown-item" href="%s">Log Out</a>',
                                        esc_url( wp_logout_url( $login_url ) )
                                    );
                                ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
