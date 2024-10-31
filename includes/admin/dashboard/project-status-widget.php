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

$user_id          = get_current_user_id();
$project_order    = get_option( 'pto_dashboard_project_order_' . $user_id );
$project_posts    = get_option( 'pto_dashboard_project_posts_' . $user_id );
$project_category = get_option( 'pto_dashboard_project_category_' . $user_id );
$assigned         = pto_get_team_from_userid(); 

$p_order       = ! empty( $project_order ) ? $project_order : 'title';
$no_of_project = ! empty( $project_posts ) ? $project_posts : 10; 
$pcat          = ! empty( $project_category ) ? $project_category : 0;

?>
<div class="projectActivities">						
    <div class="card">

        <!-- Widget header and title -->
        <div class="card-header d-block d-md-flex">
            <div class="card-header-info d-inline-flex align-items-center">
                <img src="<?php echo esc_url( PTO_PLUGIN_URL ) .'/assets/admin/img/taskIcon.png' ?>" alt="" class="img-fluid mr-2" />
                <h5 class="mb-0"><?php esc_html_e( 'Projects', 'projectopia-core' ); ?></h5>
            </div>

            <?php if ( current_user_can('cqpim_create_new_project') && current_user_can('publish_cqpim_projects') ) { ?>
                <div class="card-header-btn mt-2 mt-md-0">
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=cqpim_project' ) ); ?>" class="piaBtn d-inline-block"><?php esc_html_e('Add Project', 'projectopia-core') ?></a>
                </div>
            <?php } ?>
        </div>

        <!-- These are filters -->
        <div id="dash_project_filters" class="cardNavTabWrapper d-block d-sm-flex">
            <div class="dropdownInner padding-ls-medium mt-2 mt-sm-2" data-prev-content="Order">
                <select id="dash_project_order" class="selectDropdown-init form-control">
                    <option value="title" <?php selected('title', $p_order, true); ?>><?php esc_html_e( 'By Title', 'projectopia-core'); ?></option>
                    <option value="date" <?php selected('date', $p_order, true); ?>><?php esc_html_e( 'By Date', 'projectopia-core'); ?></option>
                </select>
            </div>

            <div class="dropdownInner padding-ls-medium mt-2 mt-sm-2" data-prev-content="Show">
                <select id="dash_project_posts" class="selectDropdown-init form-control">
                    <option value="10" <?php selected(10, $no_of_project, true); ?>><?php esc_html_e('10', 'projectopia-core'); ?></option>
                    <option value="25" <?php selected(25, $no_of_project, true); ?>><?php esc_html_e('25', 'projectopia-core'); ?></option>
                    <option value="50" <?php selected(50, $no_of_project, true); ?>><?php esc_html_e('50', 'projectopia-core'); ?></option>
                    <option value="100" <?php selected(100, $no_of_project, true); ?>><?php esc_html_e('100', 'projectopia-core'); ?></option>
                </select>
            </div>

            <div class="dropdownInner padding-ls-medium mt-2 mt-sm-2" data-prev-content="Show">
                <select id="dash_project_category" class="selectDropdown-init form-control">
                    <option value="0" <?php selected(0, $pcat, true); ?>><?php esc_html_e('All Category', 'projectopia-core'); ?></option>
                    <?php $p_terms = get_terms( 'cqpim_project_cat', array( 'hide_empty' => false )); ?>
                    <?php if ( ! empty($p_terms) ) { ?>
                        <?php foreach ( $p_terms as $p_term ) { ?>
                            <option value="<?php echo esc_attr( $p_term->slug ); ?>" <?php selected( $pcat, $p_term->slug ); ?>><?php echo esc_html( $p_term->name ); ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>

        <?php
        $query_param = [
            'post_type'      => 'cqpim_project',
            'posts_per_page' => -1,
            'post_status'    => 'private',
            'orderby'        => $p_order,
            'order'          => $p_order == 'title' ? 'asc' : 'desc',
        ];

        if ( ! empty( $pcat ) ) {
            $query_param['tax_query'] = [
                [
                    'taxonomy' => 'cqpim_project_cat',
                    'field'    => 'slug',
                    'terms'    => $pcat,
                ],
            ];
        }

        $projects = get_posts( $query_param );
        $index = 0;
        ?>
        <div class="card-body">  
            <div class="table-responsive">
                <?php if ( ! empty( $projects ) ) {
                    echo '<table class="table table-hover">';

                    foreach ( $projects as $project ) {

                        //Break the loop once reach to no of project we required.
                        if ( $index >= $no_of_project ) {
                            break;
                        }

                        $project_details = get_post_meta( $project->ID, 'project_details', true );
                        $closed = isset( $project_details['closed'] ) ? $project_details['closed'] : '';

                        //Check if project is already closed.
                        if ( ! empty( $closed ) ) {
                            continue;
                        }

                        $project_contributors = get_post_meta($project->ID, 'project_contributors', true);

                        //Check project access permission.
                        $access = false;
                        if ( ! empty( current_user_can('cqpim_view_all_projects') ) ) {
                            $access = true;
                        } elseif ( ! empty( $project_contributors ) && is_array( $project_contributors ) ) {
                            foreach ( $project_contributors as $contributor ) {
                                if ( ! empty($contributor['team_id'] ) && $assigned == $contributor['team_id'] ) {
                                    $access = true;
                                }
                            }
                        }

                        if ( false === $access ) {
                            continue;
                        }

                        $edit_url = get_edit_post_link($project->ID);
                        $p_title = get_the_title($project->ID); 
                        $client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
                        $sent = isset($project_details['sent']) ? $project_details['sent'] : '';
                        $confirmed = isset($project_details['confirmed']) ? $project_details['confirmed'] : '';
                        $signoff = isset($project_details['signoff']) ? $project_details['signoff'] : '';
                        $contract_status = get_post_meta($project->ID, 'contract_status', true );

                        //Check project current status.
                        $project_status = __( 'In Progress', 'projectopia-core');
                        $status_class = '';
                        if ( ! empty( get_option( 'enable_project_contracts' ) ) && 1 == $contract_status ) {

                            if ( ! empty( $client_id ) && empty( $confirmed ) ) {
                                $project_status = __('Contract Not Sent', 'projectopia-core');
                                $status_class = 'contractNotSent';
                            }

                            if ( ! empty( $client_id ) && ! empty( $sent ) ) {
                                $project_status = __('Contract Sent', 'projectopia-core');
                                $status_class = 'contractSent';
                            }
                        }

                        if ( ! empty( $client_id ) && ! empty( $signoff ) ) {
                            $project_status = __( 'Signed Off', 'projectopia-core' );
                            $status_class = 'contractSignOff';
                        }

                        $project_elements = get_post_meta($project->ID, 'project_elements', true);
                        $client_id = isset($project_details['client_id']) ? $project_details['client_id'] : '';
                        $project_ref = isset($project_details['quote_ref']) ? $project_details['quote_ref'] : '';
                        $client_details = get_post_meta($client_id, 'client_details', true);
                        $client_contact_name = isset($client_details['client_contact']) ? $client_details['client_contact'] : '';
                        $client_company_name = isset($client_details['client_company']) ? $client_details['client_company'] : '';
                        
                        if ( empty( $client_company_name ) ) {
                            $client_company_name = $project->post_title;
                        }

                        //Get project finish date.
                        $finish_date = isset($project_details['finish_date']) ? $project_details['finish_date'] : '';   
                        if ( ! empty( $finish_date ) ) {
                            $finish_date = wp_date( get_option( 'cqpim_date_format' ), $finish_date );
                        } else {
                            $finish_date = __( 'No Deadline' , 'projectopia-core');
                        }

                        //Get count the no of task for this particular project.
                        $task_count = 0;
                        $task_total_count = 0;
                        $task_complete_count = 0;
                        if ( ! empty( $project_elements ) ) {
                            foreach ( $project_elements as $element ) {

                                if ( empty( $element ) || empty( $element['id'] ) ) {
                                    continue;
                                }

                                $args = array(
                                    'post_type'      => 'cqpim_tasks',
                                    'posts_per_page' => -1,
                                    'meta_key'       => 'milestone_id',
                                    'meta_value'     => $element['id'],
                                    'orderby'        => 'date',
                                    'order'          => 'ASC',
                                );

                                $tasks = get_posts($args);  
                                foreach ( $tasks as $task ) {
                                    $task_total_count++;
                                    $task_details = get_post_meta($task->ID, 'task_details', true);
                                    $p_status = isset($task_details['status']) ? $task_details['status'] : '';
                                    if ( $p_status != 'complete' ) {
                                        $task_count++;
                                    }
                                    if ( $p_status == 'complete' ) {
                                        $task_complete_count++;
                                    }
                                }
                            }
                        }

                        //Calculate project completed in percentage.
                        $project_complete_per = 0;
                        if ( ! empty( $task_total_count ) ) {
                            $pc_per_task = 100 / $task_total_count;
                            $project_complete_per = round( $pc_per_task * $task_complete_count );
                        }

                        $index++;
                    ?>
                        <tr>
                            <td>
                                <div class="singleInfo">
                                    <div class="title">
                                        <h4>
                                            <a href="<?php echo esc_url( $edit_url ); ?>">
                                                <?php if ( ! empty($project->post_title) ) { echo esc_html( $project->post_title ); } else { esc_html_e('Untitled', 'projectopia-core'); } ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="singleInfoDateline">
                                        <p><?php esc_html_e('Deadline: ' , 'projectopia-core') ?><span><?php echo esc_html( $finish_date ); ?></span></p>
                                        <p><?php esc_html_e('Open Tasks: ' , 'projectopia-core') ?><span><?php echo esc_html( $task_count ); ?></span></p>
                                        <span class="singleProgress <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $project_status ); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="circleProgressBar">
                                    <div id="<?php echo 'progress' . esc_attr( $project->ID ) . '-circle'; ?>"
                                        data-percent="<?php echo esc_attr( $project_complete_per ); ?>"
                                        class="small float-right">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="completedProject">
                                    <p><?php esc_html_e('Complete: ' , 'projectopia-core') ?></p>
                                    <span><?php echo esc_html( $project_complete_per ); ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    echo ' </table>';
                } elseif ( $index == 0 ) { ?>
                    <div class="cqpim-dash-item-inside">
                        <div style="padding:20px">
                            <h4 style="margin:0"><?php esc_html_e('Nothing Here!', 'projectopia-core'); ?></h4>
                            <span><?php esc_html_e('You have not been assigned to any open projects...', 'projectopia-core'); ?></span>	
                        </div>	
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
