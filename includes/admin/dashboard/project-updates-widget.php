<?php
/**
 * Project updates widget
 *
 * This is Project updates widget showing inside projectopia dashboard.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

$avatar     = get_option( 'cqpim_disable_avatars' );
$projects   = get_posts( [
    'post_type'      => 'cqpim_project',
    'posts_per_page' => -1,
    'post_status'    => 'private',
] );

$updates = [];
if ( ! empty( $projects ) ) {
    foreach ( $projects as $project ) {
        $access = false;
        $edit_url = get_edit_post_link($project->ID);
        $p_title = get_the_title($project->ID); 
        $project_details = get_post_meta($project->ID, 'project_details', true);
        $closed = isset($project_details['closed']) ? $project_details['closed'] : '';
        $project_progress = get_post_meta($project->ID, 'project_progress', true);
        $project_progress = ( $project_progress && is_array( $project_progress ) ) ? $project_progress : array();

        $project_contributors = get_post_meta($project->ID, 'project_contributors', true);

        if ( current_user_can( 'cqpim_view_all_projects' ) && empty( $closed ) ) {
            foreach ( $project_progress as $progress ) {
                $date = isset($progress['date']) ? $progress['date'] : '';
                if ( ! empty( $date ) ) {
                    $updates[ $date ] = array(
                        'pid'    => $project->ID,
                        'by'     => isset( $progress['by'] ) ? $progress['by'] : '',
                        'date'   => $date,
                        'update' => isset( $progress['update'] ) ? $progress['update'] : '',
                    );
                }
            }
        } else {
            if ( ! is_array($project_contributors) ) {
                $project_contributors = array( $project_contributors );
            }
            foreach ( $project_contributors as $contributor ) {
                if ( ! empty($contributor['team_id']) && $assigned == $contributor['team_id'] ) {
                    $access = true;
                }
            }

            if ( ! empty( $access ) && empty( $closed ) ) {
                foreach ( $project_progress as $progress ) {
                    $date = isset( $progress['date'] ) ? $progress['date'] : '';
                    $updates[ $date ] = array(
                        'pid'    => $project->ID,
                        'by'     => $progress['by'],
                        'date'   => $progress['date'],
                        'update' => $progress['update'],
                    );
                }                       
            }
        }
    }

    krsort($updates);
}

// Filter value for project updates ( in days) 
$no_of_days = get_option( 'pto_project_update_days' );
if ( empty( $no_of_days ) ) {
    $no_of_days = 3;
}

//Group the project updates as per day.
$project_updates = [];
foreach ( $updates as $project_update ) {

    //If project update date is empty then continue.
    if ( empty( $project_update['date'] ) ) {
        continue;
    }

    $update_date = $project_update['date'];
    $update_time = '';

    //Check if date is unix timestemps.
    if ( is_numeric( $update_date ) ) { 
        $update_date  = gmdate( 'M d Y' , $project_update['date'] );
        $update_time  = wp_date( 'h:i A', $project_update['date'] );
    }

    //Calculate date and time line for updates.
    $today = new DateTime( 'today' );
    $modified_date = new DateTime( gmdate( 'Y-m-d', strtotime( $update_date ) ) );
    $today->setTime( 0, 0, 0 );
    $modified_date->setTime( 0, 0, 0 );

    //If updates are exceeds the number of days then continue.
    if ( $today->diff( $modified_date )->days > $no_of_days ) {
        continue;
    }

    //Make date label for updates group.
    if ( $today->diff( $modified_date )->days === 0 ) {
        $update_date = __( 'Today', 'projectopia-core' );
    } elseif ( $today->diff( $modified_date )->days === -1 ) {
        $update_date = __( 'Yesterday', 'projectopia-core' );
    }

    //Set avatar.
    if ( empty( $avatar ) ) {
        $profile_avatar = get_avatar(
            pto_get_user_id_by_display_name( $project_update['by'] ),
            40,
            '',
            false,
            [
				'force_display' => true,
				'class'         => 'img-fluid',
			]
        );
    }

    if ( empty( $profile_avatar ) ) {
        $profile_avatar = sprintf(
            '<img src="%s" alt="%s" class="img-fluid" />',
            PTO_PLUGIN_URL .'/assets/admin/img/header-profile.png',
            esc_html( $project_update['by'] )
        );
    }

    //Group updates day wise.
    $project_updates[ $update_date ][] = [
        'member_name'    => $project_update['by'],
        'avatar'         => $profile_avatar,
        'time'           => $update_time,
        'update_message' => $project_update['update'],
        'project_url'    => get_edit_post_link($project_update['pid']),
        'project_title'  => get_the_title($project_update['pid']),
    ];
}

?>

<!-- Project updates widget -->
<div class="projectActivities ProjectUpdatesWrapper">
    <div class="card">
        <!-- Widget header -->
        <div class="card-header d-block d-md-flex ">
            <div class="card-header-info d-flex align-items-center">
                <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" alt="" class="img-fluid mr-2" />
                <h5 class="mb-0"><?php esc_html_e( 'Projects Updates', 'projectopia-core' ); ?></h5>
            </div>
            <div class="dropdownInner padding-ls-medium mt-2 mt-md-0" data-prev-content="Show">
                <select id="pto_filter_project_updates" class="selectDropdown-init form-control">
                    <option value="3" <?php selected( 3, $no_of_days, true ); ?>>
                        <?php esc_html_e( 'Last 3 Days', 'projectopia-core'); ?>
                    </option>
                    <option value="7" <?php selected( 7, $no_of_days, true ); ?>>
                        <?php esc_html_e( 'Last 7 Days', 'projectopia-core'); ?>
                    </option>
                    <option value="15" <?php selected( 15, $no_of_days, true ); ?>>
                        <?php esc_html_e('Last 15 Days', 'projectopia-core'); ?>
                    </option>
                    <option value="30" <?php selected( 30, $no_of_days, true); ?>>
                        <?php esc_html_e( 'Last 30 Days', 'projectopia-core' ); ?>
                    </option>
                </select>
            </div>
        </div>

        <!-- Widget contents -->
        <div class="card-body dailyUpdate">
            <?php
            foreach ( $project_updates as $date => $updates ) { ?>
                <div class="dailyUpdateInner">
                    <h3 class="dailyUpdateTitle"> <?php echo esc_html( $date ); ?> </h3>
                    <ul>
                    <?php foreach ( $updates as  $update ) { ?>
                        <li>
                            <div class="singleUpdateContainer d-flex justify-content-between">
                                <div class="singleUpdate">
                                    <div class="upImg"><?php echo wp_kses_post( $update['avatar'] ); ?></div>
                                    <div class="singleUpdateInfo">
                                        <span class="singleUpdateInfoName"><?php echo esc_html( $update['member_name'] ); ?></span>
                                        <h4 class="title"><a href="<?php echo esc_url( $update['project_url'] ); ?>"> <?php echo esc_html( $update['project_title'] ); ?> </a> </h4>
                                        <p><?php echo wp_kses_post( $update['update_message'] ); ?></p>
                                    </div>
                                </div>
                                <div class="activeDate text-right">
                                    <p>&#x1F551; <?php echo esc_html( $update['time'] ); ?> </p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <!-- If these is no updates -->
            <?php if ( empty( $project_updates ) ) { ?>
            <div class="dailyUpdateInner">
                <h3 class="dailyUpdateTitle"><?php esc_html_e( 'Nothing Here!', 'projectopia-core' ); ?></h3>
                <span><?php esc_html_e( 'No project updates...', 'projectopia-core' ); ?></span>	
            </div>
            <?php } ?>

        </div>
    </div>
</div>
