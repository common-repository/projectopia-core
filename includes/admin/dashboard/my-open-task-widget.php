<?php 
/**
 * My open task widget
 *
 * This is my open task widget showing inside projectopia dashboard.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

$assigned      = pto_get_team_from_userid();
$ticket_status = pto_get_transient( 'task_status' );
$sess_status   = ! empty( $ticket_status ) ? $ticket_status : array( 'pending', 'progress' );

//Get all the task and filter the assigned/watching tasks.
$tasks = get_posts( [
    'post_type'      => 'cqpim_tasks',
    'posts_per_page' => -1,
    'meta_query'     => array(
        'relation' => 'OR',
        [
            'key'     => 'owner',
            'value'   => $assigned,
            'compare' => '=',
        ],
		[
			'key'     => 'task_watchers',
			'value'   => $assigned,
			'compare' => 'LIKE',
		],
    ),
] );

$own_tasks = [];
foreach ( $tasks as $task ) {
    $active = get_post_meta($task->ID, 'active', true);
    $task_details = get_post_meta($task->ID, 'task_details', true);
    $owner = get_post_meta($task->ID, 'owner', true);
    $watchers = get_post_meta($task->ID, 'task_watchers', true);
    if ( empty( $watchers ) ) {
        $watchers = [];
    }

    $task_status = isset( $task_details['status'] ) ? $task_details['status'] : '';                 
    if ( ! empty( $active ) && $task_status != 'complete' && $owner == $assigned || ! empty($active) && $task_status != 'complete' && in_array( $assigned, $watchers ) ) {
        $own_tasks[] = $task;
    }
}


$ordered = [];
//Ordered the ask as per deadlines.
if ( ! empty( $own_tasks ) ) {
    $deadlines = $without_deadline = [];
    foreach ( $own_tasks as $task ) {
        $task_details = get_post_meta($task->ID, 'task_details', true);
        $task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
        if ( ! empty( $task_deadline ) ) {
            $deadlines[ $task_deadline ] = $task;
        } else {
            $without_deadline[] = $task;
        }
    }

    ksort( $deadlines );
    $ordered = array_merge( $deadlines , $without_deadline );
}

?>
<div class="card" id="pto-my-open-task-widget">
    <!-- Widget header -->
    <div class="card-header d-block d-md-flex">
        <div class="card-header-info d-flex align-items-center">
            <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" class="img-fluid mr-2" />
            <h5 class="mb-0"><?php esc_html_e( 'My Open Tasks (Assign or Watching)', 'projectopia-core'); ?></h5>
        </div>
        <div class="card-header-btn mt-2 mt-md-0">
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cqpim_tasks' ) ); ?>" class="piaBtn"><?php esc_html_e( 'Add Task', 'projectopia-core' ); ?></a>
        </div>
    </div>

    <!-- Widget filters and contents. -->
    <div class="card-body">
        <div class="cardNavTabWrapper d-block d-sm-flex">
            <button class="piaBtn redColor d-none" id="pto-tasks-delete"><?php esc_html_e( 'Delete', 'projectopia-core' ); ?></button>

            <div class="dropdownInner padding-ls-medium mt-2 mt-sm-0" data-prev-content="Status">
                <select id="task_status_filter" class="selectDropdown-init form-control">
                    <option value="" <?php if ( in_array('pending', $sess_status) && in_array('progress', $sess_status) ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Pending & In Progress', 'projectopia-core'); ?></option>
                    <option value="pending" <?php if ( in_array('pending', $sess_status) && ! in_array('progress', $sess_status) ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Pending', 'projectopia-core'); ?></option>
                    <option value="on_hold" <?php if ( in_array('on_hold', $sess_status) ) { echo 'selected="selected"'; } ?>><?php esc_html_e('On Hold', 'projectopia-core'); ?></option>
                    <option value="progress" <?php if ( in_array('progress', $sess_status) && ! in_array('pending', $sess_status) ) { echo 'selected="selected"'; } ?>><?php esc_html_e('In Progress', 'projectopia-core'); ?></option>
                    <option value="complete" <?php if ( in_array('complete', $sess_status) ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Complete', 'projectopia-core'); ?></option>					
                    <option value="all" <?php if ( in_array('pending', $sess_status) && in_array('progress', $sess_status) && in_array('on_hold', $sess_status) && in_array('complete', $sess_status) ) { echo 'selected="selected"'; } ?>><?php esc_html_e('Show All', 'projectopia-core'); ?></option>
                </select>
            </div>

            <div class="searchWrapper mt-3 mt-sm-0">
                <button class="searchBtn">
                    <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/searchicon.png' ?>" class="img-fluid mr-2" />
                </button>
                <input id="pto-dashboard-task-search" type="text" placeholder="Search ..." class="inputSearch">
            </div>
        </div>
        <div class="tabContentWrapper">
            <div id="tab-1" class="customTab-content current">
                <?php
                $index = 0;
                if ( ! empty( $ordered ) ) { ?>
                    <div class="table-responsive">
                        <ul class="open-tasks-lists table-responsive">
                        <?php
                        $styles = array();
                        foreach ( $ordered as $task ) { 
                            $task_details = get_post_meta($task->ID, 'task_details', true); 
                            $owner = get_post_meta($task->ID, 'owner', true); 
                            $task_owner = get_post_meta($task->ID, 'owner', true);
                            $client_check = preg_replace('/[0-9]+/', '', $task_owner);
                            $client = false;
                            if ( $client_check == 'C' ) {
                                $client = true;
                            }   
                            if ( $task_owner ) {
                                if ( $client == true ) {
                                    $n_id = preg_replace("/[^0-9,.]/", "", $task_owner);
                                    $client_object = get_user_by('id', $n_id);
                                    $task_owner = $client_object->display_name;
                                } else {
                                    $team_details = get_post_meta($task_owner, 'team_details', true);
                                    $team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
                                    if ( ! empty($team_name) ) {
                                        $task_owner = $team_name;
                                    }
                                }
                            } else {
                                $task_owner = '';
                            }
                            $team_details = get_post_meta($owner, 'team_details', true);
                            $team_name = isset($team_details['team_name']) ? $team_details['team_name'] : '';
                            $project = get_post_meta($task->ID, 'project_id', true); 
                            $active = get_post_meta( $task->ID, 'active', true );
                            $project_details = get_post_meta($project, 'project_details', true);
                            $project_object = get_post($project);
                            $project_ref = isset($project_object->post_title) ? $project_object->post_title : '';
                            $project_url = get_edit_post_link($project);
                            $task_status = isset($task_details['status']) ? $task_details['status'] : '';

                            //If task status doesn't match.
                            if ( ! empty( $task_status ) && ! in_array( $task_status, $sess_status ) ) {
                                continue;
                            }

                            $task_deadline = isset($task_details['deadline']) ? $task_details['deadline'] : '';
                            $task_pc = isset($task_details['task_pc']) ? $task_details['task_pc'] : '0';
                            if ( ! is_numeric($task_deadline) ) {
                                $str_deadline = str_replace('/','-', $task_deadline);
                                $str_deadline = str_replace('.','-', $task_deadline);
                                $deadline_stamp = strtotime($str_deadline);
                            } else {
                                $deadline_stamp = $task_deadline;
                            }
                            $time_spent = get_post_meta($task->ID, 'task_time_spent', true);
                            $total = (int) 0;
                            if ( $time_spent ) {
                                foreach ( $time_spent as $key => $time ) {
                                    $total = $total + $time['time'];
                                }
                                $total = str_replace(',','.', $total);
                                $time_split = explode('.', $total);
                                if ( ! empty($time_split[1]) ) {
                                    $minutes = '0.' . $time_split[1];
                                } else {
                                    $minutes = '0';
                                    $time_split[1] = '0';
                                }
                                $minutes = $minutes * 60;
                                $minutes = number_format( (float)$minutes, 0, '.', '');
                                if ( $time_split[0] > 1 ) {
                                    $hours  = __('hours', 'projectopia-core');
                                } else {
                                    $hours = __('hour', 'projectopia-core');
                                }
                                $time = '<span><strong>' . number_format( (float)$total, 2, '.', '') . ' ' . __('hours', 'projectopia-core') . '</strong> (' . $time_split[0] . ' ' . $hours . ' + ' . $minutes . ' ' . __('minutes', 'projectopia-core') . ')</span>';
                            } else {
                                $time = '<span>0</span>';
                            }
                            $now = time();
                            if ( $task_status != 'complete' ) {
                                if ( $deadline_stamp && $now > $deadline_stamp ) {
                                    $progress_class = 'red';
                                    $milestone_status_string = __('OVERDUE', 'projectopia-core') . ' - ' . $task_pc;
                                } else {
                                    $milestone_status_string = isset($task_details['status']) ? $task_details['status'] : '';
                                    if ( ! $milestone_status_string || $milestone_status_string == 'pending' ) {
                                        $progress_class = 'amber';
                                        $milestone_status_string = __('Pending', 'projectopia-core') . ' - ' . $task_pc;
                                    } elseif ( $milestone_status_string == 'on_hold' ) {
                                        $progress_class = 'green';
                                        $milestone_status_string = __('On Hold', 'projectopia-core') . ' - ' . $task_pc;
                                    } elseif ( $milestone_status_string == 'progress' ) {
                                        $progress_class = 'green';
                                        $milestone_status_string = __('In Progress', 'projectopia-core') . ' - ' . $task_pc;
                                    }
                                }
                            } else {
                                $milestone_status_string = __('Complete', 'projectopia-core') . ' - ' . $task_pc;
                            }   
                            if ( empty($progress_class) ) {
                                $progress_class = 'green';
                            }
                            if ( ! empty($task->post_parent) ) {
                                $parent_object = get_post($task->post_parent);
                            }

                            $index++;
                            ?>
                            <li class="<?php pto_is_task_overdue( $task->ID, 'bg' ); ?>">
                                <label class="checkContainer">
                                    <div class="check-content">
                                        <p class="mb-0">
                                            <a href="<?php echo esc_url( get_edit_post_link( $task->ID ) ); ?>" class="checkLink">
                                                <span class="task-title">
                                                    <?php
                                                    echo esc_html( $task->post_title );
                                                    if ( ! empty( $task->post_parent ) && ! empty( $parent_object->ID ) ) {
                                                        printf(
                                                            ' [ %s <a href="%s"> %s </a> ] ',
                                                            esc_html__( 'Parent Task :', 'projectopia-core' ),
                                                            esc_url( get_edit_post_link( $parent_object->ID ) ),
                                                            esc_html( get_the_title( $parent_object->ID ) )
                                                        );
                                                    }
                                                    ?>
                                                </span>
                                            </a>
                                            <input type="checkbox" class="pto-open-task" value="<?php echo esc_attr( $task->ID ); ?>" />
                                            <span class="checkMark"></span>
                                        </p>
                                        <p class="assign mb-0">
                                        <?php          
                                            if ( is_numeric( $task_deadline ) ) {
                                                $task_deadline = wp_date( get_option( 'cqpim_date_format' ), $task_deadline );
                                            }
                                            printf( '<span class="pr-3"> %s %s </span>', esc_html__( 'Assigned to ', 'projectopia-core' ), esc_html( $task_owner ) );
                                            printf( '<span class="pl-3"> %s %s </span>', esc_html__( 'Deadline:', 'projectopia-core' ), esc_html( $task_deadline ) );                    
                                        ?>
                                        </p>
                                    </div>
                                </label>
                                <div class="updateBtnWrapper">
                                    <button class="upBtn btn-edit">
                                        <a href="<?php echo esc_url( get_edit_post_link( $task->ID ) ); ?>" >
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        </a>
                                    </button>
                                    <button class="upBtn pto-dashboard-task-delete-button" data-task-id="<?php echo esc_attr( $task->ID ); ?>">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </li>
                        <?php } ?>
                        </ul>
                    </div>

                    <!-- If there is no task then show below message. -->
                    <div id="task-not-found" class="p-3  <?php if ( ! empty( $index ) ) { echo 'd-none'; } ?>">		
                        <h4 style="m-0"><?php esc_html_e( 'Nothing Here!', 'projectopia-core' ); ?></h4>
                        <span><?php esc_html_e( 'No tasks to show...', 'projectopia-core' ); ?></span>
                    </div>

                    <div class="seaAllTaskWrapper">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=pto-alltasks' ) ); ?>" class="seaAllBtn"><?php esc_html_e( 'See All Tasks', 'projectopia-core'); ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
