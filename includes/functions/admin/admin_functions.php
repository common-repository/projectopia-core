<?php
/**
 * Admin functions.
 *
 * This is core file responsible for admin functionality.
 * Please don't touch this file
 *
 * @version 5.0.0
 * @author  Projectopia
 *
 * @package projectopia
 */

/**
 * Function to check current admin page is for pto plugin or not.
 *
 * @since 5.0.0
 *
 * @return boolean
 */
function check_is_pto_plugin() {
	$screen = get_current_screen();
	$check = false;

	// Plugin's Screen
	if ( strpos( $screen->base, 'cqpim' ) !== false 
		|| strpos( $screen->post_type, 'cqpim' ) !== false 
		|| strpos( $screen->base, 'pto' ) !== false 
		|| strpos( $screen->post_type, 'pto' ) !== false ) {
		$check = true;
	}

	// Activation Screen
	if ( strpos( $screen->base, 'pto-dashboard-' ) !== false || projectopia_fs()->is_activation_mode() ) {
		$check = false;
	}

	$paid_addons = pto_get_fs_addons();
	foreach ( $paid_addons as $data ) {
		if ( ! function_exists( $data['fs_func'] ) ) {
			continue;
		}

		// Initiate the Freemius instance.
		$addon_fs = call_user_func( $data['fs_func'] );

		if ( ! method_exists( $addon_fs, 'is_activation_mode' ) ) {
			continue;
		}

		if ( strpos( $screen->base, $data['slug'] ) !== false && $addon_fs->is_activation_mode() ) {
			$check = false;
		}
	}

	return $check;
}

/**
 * Function to add some inline styles and scripts to handle the sidebar,
 * admin bar and admin menu item.
 *
 * @since 1.0.0
 * @since 5.0.0 Added styles to hide the sidebar and admin bar.
 *
 * @return void
 */
function pto_admin_head_css() { ?>
	<?php if ( check_is_pto_plugin() ) { ?>
		<style>
			#wpcontent {
				margin-left: 140px;
			}
			.wrap h1 {
				display:none;
			}
			.wrap {
				clear:both;
				padding-top:10px;
			}

			@media only screen and (max-width: 960px) {
				#wpbody {
					padding-left:0px;
				}
			}

			html.wp-toolbar,
			#wpcontent{
				padding:0 !important;
			}

			#wpcontent, #footer {
				margin-left: 0px !important;
			}

			#adminmenuback,
			#wpadminbar,
			#adminmenuwrap {
				display: none !important;
			}

			#wpbody {
				max-width: 1440px;
				margin: auto;
			}
		</style>

		<script type="text/javascript">
			jQuery( document).ready( function($) {
				$( '#adminmenuback, #adminmenuwrap, #wpadminbar' ).remove();
			});
		</script>
	<?php } else { ?>
		<style>
			#toplevel_page_pto-dashboard .wp-submenu li a:not(.wp-first-item) { display: none; }
		</style>

		<script type="text/javascript">
			jQuery( document).ready( function( $ ) {
				$( 'span.pto-sm-hidden' ).closest( 'li' ).remove();
				$( '#toplevel_page_pto-dashboard .wp-submenu li a' ).attr( 'style', 'display:block;');
			});
		</script>
	<?php }
}

add_action( 'admin_head', 'pto_admin_head_css', 10 );

/**
 * Add overlay loader template.
 *
 * @since 5.0.0
 * 
 * @return void
 */
function pto_overlay_template() {

	printf(
		'<div style="display:none" id="cqpim_overlay">
			<div id="cqpim_spinner">
			<img src="%s" />		
			</div>
		</div>',
		esc_url( PTO_PLUGIN_URL . '/img/loading_spinner.gif' )
	);

}

add_action( 'in_admin_header', 'pto_overlay_template', 10 );

/**
 * Function to prepare the projectopia admin header section.
 * 
 * @since 5.0.0
 * 
 * @return void
 */

function pto_admin_header() {

	$screen = get_current_screen();
	if ( ! empty( $screen->post_type ) ) {
		$cpt = get_post_type_object( $screen->post_type );
	}

	if ( check_is_pto_plugin() ) {
		$user = wp_get_current_user();
		$team_id = pto_get_team_from_userid($user);
		$user_name = $user->display_name;
		$role = pto_get_user_role($user);
		$unread = pto_new_messages($user->ID);
		$unread_stat = isset($unread['read_val']) ? $unread['read_val'] : '';
		$unread_qty = isset($unread['new_messages']) ? $unread['new_messages'] : '';
		$avatar = get_option('cqpim_disable_avatars');
		$messaging = get_option('cqpim_enable_messaging');
		$notification_count = pto_check_unread_team_notifications($team_id);

		$notifications = pto_get_team_notifications($team_id);
		if ( ! empty( $notifications ) && is_array( $notifications ) ) {
			$notifications = array_reverse($notifications);
		}

		//This array contain the header main nav items.
		$pto_header_nav = [
			'dashboard' => [
				'text'      => __( 'Dashboard', 'projectopia-core' ),
				'link'      => admin_url( 'admin.php?page=pto-dashboard' ),
				'screen_id' => 'toplevel_page_pto-dashboard',
			],
			'my_work'   => [
				'text'      => __( 'My Work', 'projectopia-core' ),
				'link'      => '#',
				'sub_items' => [
					'my_message'  => [
						'text'       => __( 'My Messages', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-messages' ),
						'can_access' => get_option( 'cqpim_enable_messaging' ),
						'screen_id'  => 'pto-messages',
					],
					'all_message' => [
						'text'       => __( 'All Messages (Admin)', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-messages-admin' ),
						'screen_id'  => 'pto-messages-admin',
						'can_access' => ( get_option( 'cqpim_enable_messaging' ) && current_user_can( 'access_cqpim_messaging_admin' ) ) ? true : false,
					],
					'my_tasks'    => [
						'text'      => __( 'My Tasks', 'projectopia-core' ),
						'link'      => admin_url( 'admin.php?page=pto-tasks' ),
						'screen_id' => 'pto-tasks',
					],
					'my_calendar' => [
						'text'      => __( 'My Calendar', 'projectopia-core' ),
						'link'      => admin_url() . 'admin.php?page=pto-calendar',
						'screen_id' => 'pto-calendar',
					],
					'all_tasks'   => [
						'text'       => __( 'All Tasks (Admin) ', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-alltasks' ),
						'can_access' => current_user_can( 'cqpim_dash_view_all_tasks' ),
						'screen_id'  => 'pto-alltasks',
					],
					'all_files'   => [
						'text'       => __( 'All Files (Admin) ', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-files-admin' ),
						'can_access' => current_user_can('cqpim_view_all_files'),
						'screen_id'  => 'pto-files-admin',
					],
				],
			],
			'leads'     => [
				'text'       => __( 'Leads', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can( 'edit_cqpim_leads' ) || current_user_can( 'edit_cqpim_leadforms' ),
				'sub_items'  => [
					'leads'      => [
						'text'       => __( 'Leads', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_lead' ),
						'can_access' => current_user_can('edit_cqpim_leads'),
						'screen_id'  => 'edit-cqpim_lead',
					],
					'lead_forms' => [
						'text'       => __( 'Lead Forms', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_leadform' ),
						'can_access' => current_user_can('edit_cqpim_leadforms'),
						'screen_id'  => 'edit-cqpim_leadform',
					],
				],
			],
			'client'    => [
				'text'       => __( 'Clients', 'projectopia-core' ),
				'link'       => admin_url( 'edit.php?post_type=cqpim_client' ),
				'can_access' => current_user_can('edit_cqpim_clients'),
				'screen_id'  => 'edit-cqpim_client',
			],
			'quotes'    => [
				'text'       => __( 'Quotes', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => ( current_user_can('edit_cqpim_quotes') || current_user_can('edit_cqpim_forms') || current_user_can('edit_cqpim_templates') ) && get_option('enable_quotes') == 1,
				'sub_items'  => [
					'quote'           => [
						'text'       => __( 'Quotes', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_quote' ),
						'can_access' => current_user_can('edit_cqpim_quotes'),
						'screen_id'  => 'edit-cqpim_quote',
					],
					'quote_forms'     => [
						'text'       => __( 'Quote Forms', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_forms' ),
						'can_access' => current_user_can('edit_cqpim_forms'),
						'screen_id'  => 'edit-cqpim_forms',
					],
					'quote_milestone' => [
						'text'       => __( 'Milestone / Task Templates', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_templates' ),
						'can_access' => current_user_can('edit_cqpim_templates'),
						'screen_id'  => 'edit-cqpim_templates',
					],
				],
			],
			'projects'  => [
				'text'       => __( 'Projects', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can('edit_cqpim_projects') || current_user_can('edit_cqpim_terms') || current_user_can('edit_cqpim_templates'),
				'sub_items'  => [
					'project'           => [
						'text'       => __( 'Projects', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_project' ),
						'can_access' => current_user_can('edit_cqpim_projects'),
						'screen_id'  => 'edit-cqpim_project',
					],
					'project_terms'     => [
						'text'       => __( 'Terms Templates', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_terms' ),
						'can_access' => current_user_can('edit_cqpim_terms'),
						'screen_id'  => 'edit-cqpim_terms',
					],
					'project_milestone' => [
						'text'       => __( 'Milestone / Task Templates', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_templates' ),
						'can_access' => current_user_can('edit_cqpim_templates'),
						'screen_id'  => 'edit-cqpim_templates',
					],
					'project_updates'   => [
						'text'       => __( 'Project Updates', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-updates' ),
						'can_access' => current_user_can('edit_cqpim_projects'),
						'screen_id'  => 'pto-updates',
					],
				],
			],
			'invoices'  => [
				'text'       => __( 'Invoices', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can('edit_cqpim_invoices') && get_option('disable_invoices') != 1, 
				'sub_items'  => [
					'invoice'           => [
						'text'       => __( 'Invoices', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_invoice' ),
						'can_access' => current_user_can('edit_cqpim_invoices'),
						'screen_id'  => 'edit-cqpim_invoice',
					],
					'recurring_invoice' => [
						'text'       => __( 'Recurring Invoices', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-recinvoices' ),
						'can_access' => current_user_can('edit_cqpim_invoices'),
						'screen_id'  => 'pto-recinvoices',
					],
				],
			],
			'teams'     => [
				'text'       => __( 'Teams', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can('edit_cqpim_teams') || ( current_user_can('edit_cqpim_permissions') && pto_has_addon_active_license( 'pto_roles', 'roles' ) ), 
				'sub_items'  => [
					'invoice' => [
						'text'       => __( 'Team Members', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_teams' ),
						'can_access' => current_user_can('edit_cqpim_teams'),
						'screen_id'  => 'edit-cqpim_teams',
					],
					'roles'   => [
						'text'       => __( 'Roles & Permissions', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-permissions' ),
						'can_access' => current_user_can('edit_cqpim_permissions') && pto_has_addon_active_license( 'pto_roles', 'roles' ),
						'screen_id'  => 'pto-permissions',
					],
				],
			],
			'support'   => [
				'text'       => __( 'FAQ', 'projectopia-core' ),
				'link'       => admin_url( 'edit.php?post_type=cqpim_faq' ),
				'screen_id'  => 'edit-cqpim_faq',
				'can_access' => get_option( 'cqpim_enable_faq' ) && current_user_can( 'edit_cqpim_faqs' ),
			],
		];

		if ( pto_has_addon_active_license( 'pto_st', 'tickets' ) ) {
			$pto_header_nav['support'] = [
				'text'       => __( 'Support', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can('cqpim_view_tickets') && empty($tickets) || get_option('cqpim_enable_faq') && current_user_can('edit_cqpim_faqs'),
				'sub_items'  => [
					'invoice' => [
						'text'       => __( 'Support Tickets', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-tickets' ),
						'can_access' => current_user_can( 'cqpim_view_tickets' ) && empty( $tickets ),
						'screen_id'  => 'pto-tickets',
					],
					'faq'     => [
						'text'       => __( 'FAQ', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_faq' ),
						'screen_id'  => 'edit-cqpim_faq',
						'can_access' => get_option('cqpim_enable_faq') && current_user_can('edit_cqpim_faqs'),
					],
				],
			];
		}

		if ( pto_has_addon_active_license( 'pto_sub', 'subscriptions' ) ) {
			$pto_header_nav['subscriptions'] = [
				'text'       => __( 'Subscriptions', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can('edit_cqpim_subscriptions') || current_user_can('edit_cqpim_plans'), 
				'sub_items'  => [
					'subscription_dashboard' => [
						'text'       => __( 'Subscriptions Dashboard', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-subscriptions' ),
						'can_access' => current_user_can('edit_cqpim_subscriptions'),
						'screen_id'  => 'pto-subscriptions',
					],
					'subscription_plan'      => [
						'text'       => __( 'Subscription Plans', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_plan' ),
						'can_access' => current_user_can('edit_cqpim_plans'),
						'screen_id'  => 'edit-cqpim_plan',
					],
				],
			];
		}

		if ( pto_check_addon_status( 'kanban' ) ) {
			$pto_header_nav['my_work']['sub_items'][] = [
				'text'       => __( 'Kanban Board', 'projectopia-core' ),
				'link'       => admin_url( 'admin.php?page=pto-kanban' ),
				'can_access' => current_user_can( 'view_cqpim_board' ),
				'screen_id'  => 'pto-kanban',
			];
		}

		if ( pto_has_addon_active_license( 'pto_woo', 'woocommerce' ) ) {
			$pto_header_nav['woocommerce'] = [
				'text'       => __( 'WooCommerce', 'projectopia-core' ),
				'link'       => admin_url( 'admin.php?page=pto-woocommerce' ),
				'can_access' => current_user_can('view_cqpim_woocommerce'),
				'screen_id'  => 'pto-woocommerce',
			];
		}

		if ( pto_has_addon_active_license( 'pto_exp', 'expenses' ) ) {
			$pto_header_nav['expenses'] = [
				'text'       => __( 'Expenses', 'projectopia-core' ),
				'link'       => '#',
				'can_access' => current_user_can('edit_cqpim_suppliers') || current_user_can('edit_cqpim_expenses') || current_user_can('cqpim_view_expenses_admin'),
				'sub_items'  => [
					'suppliers'    => [
						'text'       => __( 'Suppliers', 'projectopia-core' ),
						'link'       => admin_url( 'edit.php?post_type=cqpim_supplier' ),
						'can_access' => current_user_can('edit_cqpim_suppliers'),
						'screen_id'  => 'edit-cqpim_supplier',
					],
					'my_expenses'  => [
						'text'       => __( 'My Expenses', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-expenses' ),
						'can_access' => current_user_can('edit_cqpim_expenses'),
						'screen_id'  => 'pto-expenses',
					],
					'all_expenses' => [
						'text'       => __( 'All Expenses (Admin)', 'projectopia-core' ),
						'link'       => admin_url( 'admin.php?page=pto-allexpenses' ),
						'can_access' => current_user_can('cqpim_view_expenses_admin'),
						'screen_id'  => 'pto-allexpenses',
					],
				],
			];
		}

		/**
		 * Filters the top nav menu items.
		 * 
		 * @since 5.0.0
		 * 
		 * @param array $pto_header_nav
		 */
		$pto_header_nav = apply_filters( 'pto_admin_header_nav_menu', $pto_header_nav );

		include_once( 'pto-header.php' );
	}
}

/**
 * Function to prepare the page title with breadcrumbs.
 * 
 * @since 5.0.0
 *
 * @return void
 */
function pto_header_breadcrumbs() {
	$screen = get_current_screen();
	if ( check_is_pto_plugin() ) { ?>

	<!-- Markup for page-title -->
    <section class="page-title">
        <div class="container-fluid">
            <div class="pto-admin-breadcrumbs d-block d-sm-flex">
				<!--div class="pto-admin-breadcrumbs__back-button">
                    <a href="<?php //echo get_dashboard_url(); ?>" class="pto-admin-breadcrumbs__back-button"><span class="ni ni-arrow-long-left"></span>Back to WordPress Menu</a>
                </!--div-->

                <div class="pto-admin-breadcrumbs__title d-flex align-items-center">                    
					<?php
						if ( $screen->base == 'toplevel_page_pto-dashboard' ) {
							printf( '<h1 class="mb-0"> %s </h1>', esc_html__( 'Dashboard', 'projectopia-core') );
						} elseif ( $screen->id == 'cqpim_client' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_client' . '">' . esc_html__('Clients', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_lead' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_lead' . '">' . esc_html__('Leads', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_leadform' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_leadform' . '">' . esc_html__('Lead Forms', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_tasks' ) {
							$id = get_the_ID();
							$post = get_post( $id );
							$project_id = get_post_meta( $post->ID, 'project_id', true );
							if ( ! empty( $project_id ) ) {
								$project = get_post( $project_id );
								$project_link = get_edit_post_link( $project );
								if ( $project->post_type == 'cqpim_project' ) {
									echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
									echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_project' . '">' . esc_html__('Projects', 'projectopia-core') . '</a> <span>/</span> ';
									echo '<a href="' . esc_url( $project_link ) . '">' . esc_html( $project->post_title ) . '</a> <span>/</span> ';
									echo esc_html( $post->post_title );
								} elseif ( $project->post_type == 'cqpim_support' ) {
									echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
									echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-tickets' . '">' . esc_html__('Support Tickets', 'projectopia-core') . '</a> <span>/</span> ';
									echo '<a href="' . esc_url( $project_link ) . '">' . esc_html( $project->post_title ) . '</a> <span>/</span> ';
									echo esc_html( $post->post_title );
								} else {
									echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
									echo esc_html( get_admin_page_title() ) ;
								}
							} else {
								echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
								echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-tasks' . '">' . esc_html__('My Tasks', 'projectopia-core') . '</a> <span>/</span> ';
								echo esc_html( $post->post_title );
							}                           
						} elseif ( $screen->id == 'cqpim_bug' ) {
							$id = get_the_ID();
							$post = get_post($id);
							$project = get_post_meta($id, 'bug_project', true);
							$project = get_post($project);
							$project_link = get_edit_post_link($project);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_project' . '">' . esc_html__('Projects', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( $project_link ) . '">' . esc_html( $project->post_title ) . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_project' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_project' . '">' . esc_html__('Projects', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_plan' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_plan' . '">' . esc_html__('Subscription Plans', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_subscription' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-subscriptions' . '">' . esc_html__('Subscriptions Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_forms' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_forms' . '">' . esc_html__('Quote Forms', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_invoice' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_invoice' . '">' . esc_html__('Invoices', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_teams' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_teams' . '">' . esc_html__('Team Members', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_support' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-tickets' . '">' . esc_html__('Support Tickets', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_templates' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_templates' . '">' . esc_html__('Milestone Templates', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_faq' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_faq' . '">' . esc_html__('FAQ', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_quote' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_quote' . '">' . esc_html__('Quotes', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_supplier' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=cqpim_supplier' . '">' . esc_html__('Suppliers', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'cqpim_expense' ) {
							$id = get_the_ID();
							$post = get_post($id);
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-expenses' . '">' . esc_html__('Expenses', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( $post->post_title );
							
						} elseif ( $screen->id == 'projectopia_page_pto-messages' ) {
							$conversation = isset( $_GET['conversation'] ) ? intval( wp_unslash( $_GET['conversation'] ) ) : '';
							if ( ! empty( $conversation ) ) {
								$args = array(
									'post_type'      => 'cqpim_conversations',
									'post_status'    => 'private',
									'posts_per_page' => 1,
									'meta_key'       => 'conversation_id',
									'meta_value'     => $conversation,
								);
								$conversations = get_posts( $args );
								$conversation = isset( $conversations[0] ) ? $conversations[0] : array();
								echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
								echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-messages' . '">' . esc_html__( 'My Messages', 'projectopia-core') . '</a> <span>/</span> ';
								if ( ! empty( $conversation ) ) {
									echo esc_html( $conversation->post_title ) ;
								}
							} else {
								echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
								echo esc_html( get_admin_page_title() ) ;
							}
						} else {
							echo '<a href="' . esc_url( admin_url() ) . 'admin.php?page=pto-dashboard' . '">' . esc_html__('Dashboard', 'projectopia-core') . '</a> <span>/</span> ';
							echo esc_html( get_admin_page_title() ) ;
						}
					?>
                </div>
            </div>
        </div>
    </section>
    <!--/ Markup for page-title -->

	<?php
	}
}

/**
 * Show the admin notice to linked as team member.
 * 
 * @since 5.0.0
 * 
 * @return void
 */
function pto_team_member_link_notice() {

	$assigned = pto_get_team_from_userid(); 
	$user = wp_get_current_user();
	if ( ! ( in_array('administrator', $user->roles ) && pto_get_team_from_userid( $user ) == false ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( 'toplevel_page_pto-dashboard' === $screen->id || 'projectopia_page_pto-settings' == $screen->id ) {
		if ( projectopia_fs()->is_activation_page() ) {
			return;
		} ?>

		<div class="pto-notification-alert">
			<div class="container-fluid">
				<div class="pto-notification-alert__content">
					<div class="row align-items-center">
						<div class="col-xl-8 col-lg-7">
							<div class="pto-notification-alert__content-info">
								<div class="pto-notification-alert__image">
									<img src="<?php echo esc_url( PTO_PLUGIN_URL . '/assets/admin/img/alerticon.svg' ); ?>" class="img-fluid" />
								</div>
								<div class="alert_info">
									<h3><?php esc_html_e('You need to link your account to a Team Member', 'projectopia-core'); ?></h3>
									<p><?php esc_html_e('It would appear that the WordPress Administrator account that you are logged in with is not related to a Team Member. In order for the plugin to work correctly, you need to add a Team Member that is linked to your WP User Account.', 'projectopia-core'); ?></p>
									<p><?php esc_html_e('We can do this for you though, just click Create Linked Team Member. You will then be able to add other team members or just work with this account.', 'projectopia-core') ?></p>
								</div>
							</div>
						</div>
						<div class="col-xl-4 col-lg-5">
							<div class="teamBtn text-left text-lg-right mt-3 mt-lg-0">
								<button id="create_linked_team" href="#" class="btn btn-danger btn-danger-alert px-4 d-inline-block" data-uid="<?php echo esc_attr( $user->ID ); ?>"><?php esc_html_e('Create Linked Team Member', 'projectopia-core'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php }
}

add_action('in_admin_header', 'pto_admin_header', 10);
add_action('in_admin_header', 'pto_header_breadcrumbs', 10);
add_action('in_admin_header', 'pto_team_member_link_notice', 10);

add_filter( 'default_title', 'pto_set_default_quote_post_title', 10, 2 );
function pto_set_default_quote_post_title( $title, $post ) {
	if ( $post->post_type == 'cqpim_quote' ) {
		$id = $post->ID;
		$title = "%%CLIENT_COMPANY%% - %%TYPE%%: %%QUOTE_REF%%";
		return $title;
	}
	if ( $post->post_type == 'cqpim_client' ) {
		$id = $post->ID;
		$title = '%%CLIENT_COMPANY%%';
		return $title;
	}
	if ( $post->post_type == 'cqpim_invoice' ) {
		$id = $post->ID;
		$title = pto_get_invoice_id();
		return $title;
	}
	if ( $post->post_type == 'cqpim_teams' ) {
		$id = $post->ID;
		$title = "%%NAME%%";
		return $title;
	}
	if ( $post->post_type == 'cqpim_support' ) {
		$id = $post->ID;
		$title = "$id";
		return $title;
	}
	if ( $post->post_type == 'cqpim_subscription' ) {
		$id = $post->ID;
		$title = "$id";
		return $title;
	}
}

add_action( "wp_ajax_pto_filter_calendar", "pto_filter_calendar" );
function pto_filter_calendar() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$filters = isset($_POST['filters']) ? pto_sanitize_rec_array(wp_unslash($_POST['filters'])) : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	pto_set_transient('cal_filters',$filters);

	wp_send_json_success();
}

add_action( 'edit_post', 'pto_assign_post_visibility', 50 );
function pto_assign_post_visibility( $post_id ) {
	$post = get_post($post_id);
	$password = isset($post->post_password) ? $post->post_password : '';
	if ( empty($password) ) {
		$password = pto_random_string(10);
	}
	if ( $post->post_status != 'trash' && $post->post_status != 'draft' ) {
		if ( $post->post_type == 'cqpim_invoice' || $post->post_type == 'cqpim_tasks' ) {
			$post_updated = array(
				'ID'            => $post_id,
				'post_status'   => 'publish',
				'post_password' => $password,
			);
			remove_action('edit_post', 'pto_assign_post_visibility', 50 );
			wp_update_post( $post_updated );
			add_action('edit_post', 'pto_assign_post_visibility', 50 );
		}
		if ( $post->post_type == 'cqpim_templates' ||
		$post->post_type == 'cqpim_quote' ||
		$post->post_type == 'cqpim_terms' ||
		$post->post_type == 'cqpim_forms' ||
		$post->post_type == 'cqpim_project' ||
		$post->post_type == 'cqpim_client' ||
		$post->post_type == 'cqpim_teams' ||
		$post->post_type == 'cqpim_support' ||
		$post->post_type == 'cqpim_supplier' ||
		$post->post_type == 'cqpim_expense' ||
		$post->post_type == 'cqpim_bug' ||
		$post->post_type == 'cqpim_plan' ||
		$post->post_type == 'cqpim_subscription' ||
		$post->post_type == 'cqpim_leadform' ||
		$post->post_type == 'cqpim_lead'
		) {
			$post_updated = array(
				'ID'          => $post_id,
				'post_status' => 'private',
			);
			remove_action('edit_post', 'pto_assign_post_visibility', 50 );
			wp_update_post( $post_updated );
			add_action('edit_post', 'pto_assign_post_visibility', 50 );
		}
	}
}

add_action( 'init', 'pto_user_online_update' );
/**
 * Function to update the online status for user.
 */
function pto_user_online_update() {
	if ( is_user_logged_in() ) {
		$logged_in_users = get_transient('online_status');
		$user = wp_get_current_user();
		$no_need_to_update = isset( $logged_in_users[ $user->ID ] ) && $logged_in_users[ $user->ID ] > ( time() - (5 * 60) );
		if ( empty( $no_need_to_update ) ) {
			$logged_in_users[ $user->ID ] = time();
			set_transient('online_status', $logged_in_users, ( 14 * 60 ) );
		}
	}
}

add_action('clear_auth_cookie', 'pto_clear_transient_on_logout');
function pto_clear_transient_on_logout() {
	$user_id = get_current_user_id();
	$users_transient_id = get_transient('online_status');
	if ( is_array($users_transient_id) ) {
		foreach ( $users_transient_id as $id => $value ) {
			if ( $id == $user_id ) {
				unset($users_transient_id[ $user_id ]);
				set_transient('online_status', $users_transient_id, ( 14 * 60 ) );
				break;
			}
		}
	} else {
		delete_transient('online_status');
	}
}

add_action( "wp_ajax_pto_remove_logo", "pto_remove_logo" );
function pto_remove_logo() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	update_option( $type, '' );
	wp_send_json_success();
}

function pto_return_currency_select() {
	$codes = array(
		'AUD' => __( 'Australian Dollar (AUD)', 'projectopia-core' ),
		'BRL' => __( 'Brazilian Real (BRL)', 'projectopia-core' ),
		'CAD' => __( 'Canadian Dollar (CAD)', 'projectopia-core' ),
		'CZK' => __( 'Czech Koruna (CZK)', 'projectopia-core' ),
		'DKK' => __( 'Danish Krone (DKK)', 'projectopia-core' ),
		'EUR' => __( 'Euro (EUR)', 'projectopia-core' ),
		'HKD' => __( 'Hong Kong Dollar (HKD)', 'projectopia-core' ),
		'IDR' => __( 'Indonesian Rupiah (IDR)', 'projectopia-core' ),
		'ILS' => __( 'Israeli New Sheqel (ILS)', 'projectopia-core' ),
		'INR' => __( 'Indian Rupee (INR)', 'projectopia-core' ),
		'MXN' => __( 'Mexican Peso (MXN)', 'projectopia-core' ),
		'NOK' => __( 'Norwegian Krone (NOK)', 'projectopia-core' ),
		'TWD' => __( 'New Taiwan Dollar (TWD)', 'projectopia-core' ),
		'NZD' => __( 'New Zealand Dollar (NZD)', 'projectopia-core' ),
		'PHP' => __( 'Philippine Peso (PHP)', 'projectopia-core' ),
		'PLN' => __( 'Polish Zloty (PLN)', 'projectopia-core' ),
		'GBP' => __( 'Pound Sterling (GBP)', 'projectopia-core' ),
		'RUB' => __( 'Russian Ruble (RUB)', 'projectopia-core' ),
		'SGD' => __( 'Singapore Dollar (SGD)', 'projectopia-core' ),
		'SEK' => __( 'Swedish Krona (SEK)', 'projectopia-core' ),
		'CHF' => __( 'Swiss Franc (CHF)', 'projectopia-core' ),
		'THB' => __( 'Thai Baht (THB)', 'projectopia-core' ),
		'USD' => __( 'U.S. Dollar (USD)', 'projectopia-core' ),
		'COP' => __( 'Colombian Peso (COP)', 'projectopia-core' ),
		'ARS' => __( 'Argentine Peso (ARS)', 'projectopia-core' ),
		'PEN' => __( 'Peruvian Sol (PEN)', 'projectopia-core' ),
		'TRY' => __( 'Turkish Lira (TRY)', 'projectopia-core' ),
	);
	$codes = apply_filters( 'pto_supported_currencies', $codes );
	
	asort( $codes );
	
	return $codes;
}

function pto_paypal_supported_currencies() {
	$codes = array( 'AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'TWD', 'NZD', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD' );
	$codes = apply_filters( 'pto_paypal_supported_currencies', $codes );
	return $codes;
}

function pto_get_user_id_by_display_name( $display_name ) {
	global $wpdb;
	$user = $wpdb->get_row( $wpdb->prepare(
		"SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $display_name
	) );

	if ( ! $user ) {
		return false;
	}

	return $user->ID;
}

function pto_remove_date_filters() {
	$screen = get_current_screen();
	global $typenow;
	if ( strpos($typenow, 'projectopia-core') !== false ) {
		return array();
	}
}

add_action( "wp_ajax_nopriv_pto_ajax_login", "pto_ajax_login" );
add_action( "wp_ajax_pto_ajax_login", "pto_ajax_login" );
function pto_ajax_login() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
	$password = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
	$dash_page = get_option('cqpim_client_page');
	if ( empty($username) || empty($password) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Please enter a username and password', 'projectopia-core') . '</div>',
		) );
	} else {

		/** Verify the google recaptcha if it is enable for frontend login forms. */
		if ( ! empty( get_option( 'pto_frontend_form_google_recaptcha') ) && ! empty( $_POST['g_captacha_response'] ) ) {
			$recaptcha_url    = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = get_option( 'google_recaptcha_secret_key' );
			$recaptcha_token  = sanitize_text_field(wp_unslash($_POST['g_captacha_response']));
			$response         = wp_remote_get( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token );
			$recaptcha        = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $recaptcha->success != 1 ) {
				pto_send_json( array(
					'error'   => true,
					'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Please verify the google reCaptcha.', 'projectopia-core') . '</span>',
				) );
			}
		}

		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] = $password;
		$creds['remember'] = is_ssl();
		$login = wp_signon( $creds, is_ssl() );
		if ( is_wp_error($login) ) {
			pto_send_json( array(
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Login Failed. Please try again.', 'projectopia-core') . '</div>',
			) );
		} else {
			$roles = $login->roles;
			if ( in_array('cqpim_client', $roles) ) {
				$redirect = get_the_permalink($dash_page);
			} else {
				$redirect = admin_url() . 'admin.php?page=pto-dashboard';
			}

			//Reference for below 2 line of code from PR-290
			$requested_redirect_to = isset($_REQUEST['redirect_to']) ? wp_kses_post(wp_unslash($_REQUEST['redirect_to'])) : '';
			$redirect = apply_filters('login_redirect', $redirect, $requested_redirect_to, $login);

			pto_send_json( array(
				'error'    => false,
				'message'  => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Login Successful. Redirecting to your dashboard.', 'projectopia-core') . '</div>',
				'redirect' => $redirect,
			) );
		}
	}
}

add_action( "wp_ajax_nopriv_pto_ajax_reset", "pto_ajax_reset" );
add_action( "wp_ajax_pto_ajax_reset", "pto_ajax_reset" );
function pto_ajax_reset() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
	if ( empty($username) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must enter an email address.', 'projectopia-core') . '</div>',
		) );
	} else {
		$user = get_user_by('email', $username);
		if ( empty($user) ) {
			pto_send_json( array(
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('User not found.', 'projectopia-core') . '</div>',
			) );
		} else {
			$string = pto_random_string(10);
			$hash = md5($string);
			update_user_meta( $user->ID, 'reset_hash', $hash );
			$reset = get_option('cqpim_reset_page');
			$reset = get_the_permalink($reset);
			$reset = $reset . '?h=' . $hash;
			$to = $user->user_email;
			$telephone = get_option('company_telephone');
			$sender_name = get_option('company_name');
			$sender_email = get_option('company_sales_email');
			$subject = get_option('client_password_reset_subject');
			$content = get_option('client_password_reset_content');
			$content = str_replace('%%CLIENT_NAME%%', $user->display_name, $content);
			$content = str_replace('%%PASSWORD_RESET_LINK%%', $reset, $content);
			$content = str_replace('%%COMPANY_NAME%%', $sender_name, $content);
			$content = str_replace('%%COMPANY_TELEPHONE%%', $telephone, $content);
			$content = str_replace('%%COMPANY_SALES_EMAIL%%', $sender_email, $content);
			$subject = str_replace('%%COMPANY_NAME%%', $sender_name, $subject);
			$attachments = array();
			if ( pto_send_emails( $to, $subject, $content, '', $attachments, 'sales' ) ) {
				pto_send_json( array(
					'error'   => false,
					'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Further instructions have been sent to your registered address.', 'projectopia-core') . '</div>',
				) );
			} else {
				pto_send_json( array(
					'error'   => true,
					'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The email failed to send. Please try again.', 'projectopia-core') . '</div>',
				) );
			}
		}
	}
}

add_action( "wp_ajax_nopriv_pto_ajax_reset_conf", "pto_ajax_reset_conf" );
add_action( "wp_ajax_pto_ajax_reset_conf", "pto_ajax_reset_conf" );
function pto_ajax_reset_conf() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$hash = isset($_POST['hash']) ? sanitize_text_field(wp_unslash($_POST['hash'])) : '';
	$pass = isset($_POST['pass']) ? sanitize_text_field(wp_unslash($_POST['pass'])) : '';
	$pass2 = isset($_POST['pass2']) ? sanitize_text_field(wp_unslash($_POST['pass2'])) : '';
	if ( empty($pass) || empty($pass2) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must fill in both fields.', 'projectopia-core') . '</div>',
		) );
	} else {
		if ( $pass != $pass2 ) {
			pto_send_json( array(
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Passwords do not match.', 'projectopia-core') . '</div>',
			) );
		} else {
			if ( strlen($pass) < 8 || ! preg_match("#[0-9]+#", $pass) || ! preg_match("#[a-zA-Z]+#", $pass) ) {
				pto_send_json( array(
					'error'   => true,
					'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Passwords should be at least 8 characters and should contain at least one letter and one number.', 'projectopia-core') . '</div>',
				) );
			} else {
				$args = array(
					'meta_key'   => 'reset_hash',
					'meta_value' => $hash,
					'number'     => 1,
				);
				$users = get_users($args);
				if ( empty($users[0]) ) {
					pto_send_json( array(
						'error'   => true,
						'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Invalid User or reset link', 'projectopia-core') . '</div>',
					) );
				} else {
					$user = $users[0];
					wp_set_password($pass, $user->ID);
					delete_user_meta($user->ID, 'reset_hash');
					pto_send_json( array(
						'error'   => false,
						'message' => '<div class="cqpim-alert cqpim-alert-success alert-display">' . esc_html__('Your password has been reset, you can now log in with your email address and new password.', 'projectopia-core') . '</div>',
					) );
				}
			}
		}
	}
}

add_action( "wp_ajax_nopriv_pto_ajax_register", "pto_ajax_register" );
add_action( "wp_ajax_pto_ajax_register", "pto_ajax_register" );
function pto_ajax_register() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
	$name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
	$company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : $name;
	$password = isset($_POST['password']) ? sanitize_text_field(wp_unslash($_POST['password'])) : '';
	$rpassword = isset($_POST['rpassword']) ? sanitize_text_field(wp_unslash($_POST['rpassword'])) : '';
	$company_req = get_option('cqpim_login_reg_company');
	if ( empty($username) || ! empty($company_req) && empty($company) || empty($name) || empty($password) || empty($rpassword) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('You must complete all fields.', 'projectopia-core') . '</div>',
		) );
	}

	/** Verify the google recaptcha if it is enable for frontend forms. */
	if ( ! empty( get_option( 'pto_frontend_form_google_recaptcha') ) && ! empty( $_POST['g_captacha_response'] ) ) {
		$recaptcha_url    = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_secret = get_option( 'google_recaptcha_secret_key' );
		$recaptcha_token  = sanitize_text_field(wp_unslash($_POST['g_captacha_response']));
		$response         = wp_remote_get( $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_token );
		$recaptcha        = json_decode( wp_remote_retrieve_body( $response ) );
		if ( $recaptcha->success != 1 ) {
			pto_send_json( array(
				'error'   => true,
				'message' => '<span style="display:block; width:96%; padding:2%; color:#fff; background:#d9534f">' . esc_html__('Please verify the google reCaptcha.', 'projectopia-core') . '</span>',
			) );
		}
	}

	if ( $password != $rpassword ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The passwords do not match.', 'projectopia-core') . '</div>',
		) );
	}

	if ( username_exists( $username ) || email_exists( $username ) ) {
		pto_send_json( array(
			'error'   => true,
			'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('The email address entered is already in our system, please try again with a different email address or contact us.', 'projectopia-core') . '</div>',
		) );
	} else {
		// Remove this user_register action before register new client.
		remove_action( 'user_register', 'pto_create_user_as_client_from_user_page', 10, 1);
		$new_client = array(
			'post_type'    => 'cqpim_client',
			'post_status'  => 'private',
			'post_content' => '',
			'post_title'   => $company,
		);
		$client_pid = wp_insert_post( $new_client, true );
		if ( ! is_wp_error( $client_pid ) ) {
			$client_updated = array(
				'ID'        => $client_pid,
				'post_name' => $client_pid,
			);
			wp_update_post( $client_updated );
			$client_details = array(
				'client_ref'     => $client_pid,
				'client_company' => $company,
				'client_contact' => $name,
				'client_email'   => $username,
			);
			update_post_meta($client_pid, 'client_details', $client_details);
			$require_approval = get_option('pto_dcreg_approve');
			if ( $require_approval == 1 ) {
				update_post_meta($client_pid, 'pending', 1);
				$args = array(
					'post_type'      => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$team_members = get_posts($args);
				foreach ( $team_members as $member ) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
					$user_obj = get_user_by('id', $user_id);
					if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients') ) {
						pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_auth');
					}
				}
				pto_send_json( array(
					'error'   => false,
					'message' => '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('The account has been created, but it must be approved by an admin. You will receive login details via email once the account has been approved.', 'projectopia-core') . '</div>',
				) );
			} else {
				$args = array(
					'post_type'      => 'cqpim_teams',
					'posts_per_page' => -1,
					'post_status'    => 'private',
				);
				$team_members = get_posts($args);
				foreach ( $team_members as $member ) {
					$team_details = get_post_meta($member->ID, 'team_details', true);
					$user_id = isset($team_details['user_id']) ? $team_details['user_id'] : '';
					$user_obj = get_user_by('id', $user_id);
					if ( ! empty($user_obj) && user_can($user_obj, 'edit_cqpim_clients') ) {
						pto_add_team_notification($member->ID, 'system', $client_pid, 'creg_noauth');
					}
				}
				$login = $username;
				$user_id = wp_create_user( $login, $password, $username );
				$user = new WP_User( $user_id );
				$user->set_role( 'cqpim_client' );
				$client_details = get_post_meta($client_pid, 'client_details', true);
				$client_details['user_id'] = $user_id;
				update_post_meta($client_pid, 'client_details', $client_details);
				$client_ids = array();
				$client_ids[] = $user_id;
				update_post_meta($client_pid, 'client_ids', $client_ids);
				$user_data = array(
					'ID'           => $user_id,
					'display_name' => $name,
					'first_name'   => $name,
				);
				wp_update_user($user_data);
				$form_auto_welcome = get_option('form_reg_auto_welcome');
				if ( $form_auto_welcome == 1 ) {
					send_pto_welcome_email($client_pid, $password);
				}
			}
			pto_send_json( array(
				'error'   => false,
				'message' => '<div class="cqpim-alert cqpim-alert-info alert-display">' . esc_html__('Account created, please check your email for your password.', 'projectopia-core') . '</div>',
			) );
		} else {
			pto_send_json( array(
				'error'   => true,
				'message' => '<div class="cqpim-alert cqpim-alert-danger alert-display">' . esc_html__('Unable to create client entry, please try again or contact us.', 'projectopia-core') . '</div>',
			) );
		}
	}
}

function pto_client_no_admin_access() {
	$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : home_url( '/' );
	$user = wp_get_current_user();
	if ( ! empty(array_intersect(array( 'cqpim_client', 'ptouploader' ), $user->roles)) && isset($_SERVER['PHP_SELF']) && strpos(esc_url_raw(wp_unslash($_SERVER['PHP_SELF'])), '/admin-ajax.php') === false && strpos(esc_url_raw(wp_unslash($_SERVER['PHP_SELF'])), '/async-upload.php') === false ) {
		wp_safe_redirect( esc_url( $redirect ) );
		exit();
	}
}

add_action( 'admin_init', 'pto_client_no_admin_access', 100 );
function pto_hide_admin_bar(){
	$client_login = get_option('cqpim_login_page');
	$client_dash = get_option('cqpim_client_page');
	$client_reset = get_option('cqpim_reset_page');
	$client_reset = get_option('cqpim_register_page');
	$user = wp_get_current_user();
	$roles = $user->roles;
	if ( is_page($client_login) || is_page($client_dash) || is_page($client_reset) || in_array('cqpim_client', $roles) || in_array('ptouploader', $roles) ) {
		show_admin_bar(false);
	}
}

add_action( 'wp', 'pto_hide_admin_bar', 100 );
function pto_restrict_dash() {
	$user = wp_get_current_user();
	$roles = $user->roles;
	if ( ! is_array($roles) ) {
		$roles = array( $roles );
	}
	foreach ( $roles as $role ) {
		if ( strpos($role, 'cqpim_') !== false ) {
			$restrict = true;
		}
	}
	if ( ! empty($restrict) && ! empty($GLOBALS['menu']) ) {
		$plugin_name = get_option('cqpim_plugin_name');
		if ( empty($plugin_name) ) {
			$plugin_name = 'Projectopia';
		}
		foreach ( $GLOBALS['menu'] as $key => $item ) {
			if ( $item[0] != $plugin_name ) {
				unset($GLOBALS['menu'][ $key ]);
			}
		}
	}
}

add_action('admin_init', 'pto_restrict_dash');
add_filter('authenticate', 'pto_allow_email_login', 20, 3);
function pto_allow_email_login( $user, $username, $password ) {
	if ( is_email( $username ) ) {
		$user = get_user_by('email',  $username );
		if ( $user ) $username = $user->user_login;
	}
	return wp_authenticate_username_password( null, $username, $password );
}

add_action( 'wp_before_admin_bar_render', 'pto_add_all_node_ids_to_toolbar' );
function pto_add_all_node_ids_to_toolbar() {
	$user = wp_get_current_user();
	$roles = $user->roles;
	if ( ! is_array($roles) ) {
		$roles = array( $roles );
	}
	foreach ( $roles as $role ) {
		if ( strpos($role, 'cqpim_') !== false ) {
			$restrict = true;
		}
	}
	if ( ! empty($restrict) ) {
		global $wp_admin_bar;
		$all_toolbar_nodes = $wp_admin_bar->get_nodes();
		if ( $all_toolbar_nodes ) {
			foreach ( $all_toolbar_nodes as $node ) {
				if ( $node->id != 'menu-toggle' ) {
					$wp_admin_bar->remove_node($node->id);
				}
			}
		}
	}
}

add_filter( 'bulk_actions-edit-cqpim_project', 'pto_remove_from_bulk_actions' );
function pto_remove_from_bulk_actions( $actions ) {
	unset( $actions['edit'] );
	return $actions;
}

add_action( 'wp_ajax_pto_refresh_dash_projects', 'pto_refresh_dash_projects' );
function pto_refresh_dash_projects() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$value = isset($_POST['vvalue']) ? sanitize_text_field(wp_unslash($_POST['vvalue'])) : '';
	$type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
	$user_id = get_current_user_id();
	if ( $type == 'order' ) {
		$option = 'pto_dashboard_project_order_'.$user_id;
		update_option( $option, $value );
	} elseif ( $type == 'posts' ) {
		$option = 'pto_dashboard_project_posts_'.$user_id;
		update_option( $option ,$value );
	} elseif ( $type == 'cats' ) {
		$option = 'pto_dashboard_project_category_'.$user_id;
		update_option( $option ,$value );
	}
	pto_send_json( array(
		'error'  => false,
		'errors' => '',
	) );
}

add_action( 'wp_ajax_pto_edit_calendar_range', 'pto_edit_calendar_range' );
function pto_edit_calendar_range() {
	check_ajax_referer( PTO_GLOBAL_NONCE, 'pto_nonce' );
	$value = isset($_POST['vvalue']) ? sanitize_text_field(wp_unslash($_POST['vvalue'])) : 'end';
	pto_set_transient('cal_range',$value);
	pto_send_json( array(
		'error'  => false,
		'errors' => '',
	) );
}

add_action('wp', 'pto_cronstarter_activation');
function pto_cronstarter_activation() {
	if ( ! wp_next_scheduled( 'pto_check_email_pipe' ) ) {
	   	wp_schedule_event( time(), 'every_minute', 'pto_check_email_pipe' );
	}
}

add_action( 'wp_ajax_pto_filter_project_updates', 'pto_filter_project_updates' );
/**
 * Function to call the ajax to set project update days.
 *
 * @return json
 */
function pto_filter_project_updates() {
	//Verify the nonce.
	check_ajax_referer( 'pto_nonce', 'ajax_nonce' );

	$days = isset( $_POST['days'] ) ? sanitize_text_field( wp_unslash( $_POST['days'] ) ) : '';
	update_option( 'pto_project_update_days', $days );

	wp_send_json_success();
}
