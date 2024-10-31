<?php
/**
 * Function register_pto_dashboard
 * Register projectopia admin dashboard page.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'register_pto_dashboard' ) ) {      
	add_action( 'admin_menu' , 'register_pto_dashboard', 9 ); 
	function register_pto_dashboard() {
		$plugin_name = get_option( 'cqpim_plugin_name' );
		if ( empty( $plugin_name ) ) {
			$plugin_name = 'Projectopia';
		}

		$icon = get_option( 'cqpim_use_default_icon' );
		$adicon = empty( $icon ) ? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTExLjM3MzUgNS42MDk3N1YxNC4yMTQyQzExLjM3MjIgMTUuMjE4OCAxMC45NzIyIDE2LjE4MTcgMTAuMjYxNCAxNi44OTE2QzkuNTUwNTggMTcuNjAxNSA4LjU4NzA4IDE4LjAwMDIgNy41ODI1MiAxOC4wMDAyVjE4LjAwMDJDNi41NzczNyAxNy45OTk3IDUuNjEzNTUgMTcuNjAwMSA0LjkwMjk2IDE2Ljg4OTJDNC4xOTIzNyAxNi4xNzgzIDMuNzkzMTkgMTUuMjE0MyAzLjc5MzE5IDE0LjIwOTJMMy43OTMxOSA2Ljk5NDkyQzMuNzk1NDIgNi4zNTQ3MyA0LjA1MTMxIDUuNzQxNTEgNC41MDQ3OCA1LjI4OTYyQzQuOTU4MjYgNC44Mzc3MiA1LjU3MjM2IDQuNTgzOTggNi4yMTI1NiA0LjU4Mzk5SDEwLjM0NjFDMTAuNDgwOSA0LjU4Mzc2IDEwLjYxNDUgNC42MTAxMyAxMC43MzkxIDQuNjYxNThDMTAuODYzOCA0LjcxMzA0IDEwLjk3NzEgNC43ODg1NiAxMS4wNzI1IDQuODgzODNDMTEuMTY3OSA0Ljk3OTExIDExLjI0MzYgNS4wOTIyNyAxMS4yOTUzIDUuMjE2ODNDMTEuMzQ2OSA1LjM0MTM5IDExLjM3MzUgNS40NzQ5MiAxMS4zNzM1IDUuNjA5NzdWNS42MDk3N1oiIGZpbGw9InVybCgjcGFpbnQwX2xpbmVhcl81OjcpIi8+CjxwYXRoIGQ9Ik0xNy4yMDc1IDUuNzM4MDZDMTcuMjA3IDYuNzQzMDcgMTYuODA3NyA3LjcwNjgyIDE2LjA5NzIgOC40MTc2MkMxNS4zODY3IDkuMTI4NDMgMTQuNDIzMSA5LjUyODE4IDEzLjQxODEgOS41MjkwOEgxMS4zNzMzQzQuNjI2NDIgOS41MjkwOCAzLjc5Mjk3IDE0LjIwOTIgMy43OTI5NyAxNC4yMDkyVjYuMzA0OTRDMy43OTI5NyA1LjczMjU5IDMuOTA1NzYgNS4xNjU4NSA0LjEyNDg5IDQuNjM3MTFDNC4zNDQwMiA0LjEwODM3IDQuNjY1MiAzLjYyOCA1LjA3MDA3IDMuMjIzNDRDNS40NzQ5MyAyLjgxODg5IDUuOTU1NTYgMi40OTgwOCA2LjQ4NDQ3IDIuMjc5MzZDNy4wMTMzOCAyLjA2MDY0IDcuNTgwMiAxLjk0ODI5IDguMTUyNTUgMS45NDg3M0gxMy40MTgxQzEzLjkxNTggMS45NDg1MSAxNC40MDg3IDIuMDQ2MzcgMTQuODY4NSAyLjIzNjczQzE1LjMyODQgMi40MjcwOCAxNS43NDYyIDIuNzA2MTkgMTYuMDk4MSAzLjA1ODExQzE2LjQ1IDMuNDEwMDIgMTYuNzI5MSAzLjgyNzg0IDE2LjkxOTUgNC4yODc2OUMxNy4xMDk4IDQuNzQ3NTMgMTcuMjA3NyA1LjI0MDM4IDE3LjIwNzUgNS43MzgwNlY1LjczODA2WiIgZmlsbD0idXJsKCNwYWludDFfbGluZWFyXzU6NykiLz4KPGRlZnM+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQwX2xpbmVhcl81OjciIHgxPSIxMi4xMjc3IiB5MT0iMTUuMDI1NyIgeDI9IjMuNDQ3MzIiIHkyPSI2LjM0NTM4IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IndoaXRlIi8+CjxzdG9wIG9mZnNldD0iMC4xMSIgc3RvcC1jb2xvcj0iI0ZERkRGRCIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiNCQ0JFQzAiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDFfbGluZWFyXzU6NyIgeDE9IjIuOTk0OTUiIHkxPSIxMy40MTEyIiB4Mj0iMTQuNzIyMyIgeTI9IjEuNjgyMTYiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0id2hpdGUiLz4KPHN0b3Agb2Zmc2V0PSIwLjIzIiBzdG9wLWNvbG9yPSIjRkJGQkZCIi8+CjxzdG9wIG9mZnNldD0iMC40OSIgc3RvcC1jb2xvcj0iI0VFRUVFRiIvPgo8c3RvcCBvZmZzZXQ9IjAuNzUiIHN0b3AtY29sb3I9IiNEOEQ5REEiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjQkNCRUMwIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg==' : '';
	
		add_menu_page( __( 'My Dashboard', 'projectopia-core'), $plugin_name, 'cqpim_view_dashboard', 'pto-dashboard', 'pto_dashboard', $adicon, 28 );
		$mypage = add_submenu_page( 'pto-dashboard', __( 'My Dashboard', 'projectopia-core'), __( 'Dashboard', 'projectopia-core' ), 'cqpim_view_dashboard', 'pto-dashboard' );
		add_action( 'load-' . $mypage, 'pto_enqueue_plugin_option_scripts' );
	}
}

/**
 * Function to add the specific class in body element for 5.x version.
 *
 * @param  String $classes Current body classes.
 * @return String          Altered body classes.
 */
function pto_add_admin_body_class( $classes ) {

	if ( check_is_pto_plugin() ) {
		return $classes . ' wp-pto-v5x';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'pto_add_admin_body_class', 99 );

if ( ! function_exists( 'pto_dashboard' ) ) {
	function pto_dashboard() {
		$assigned = pto_get_team_from_userid(); 
		$user = wp_get_current_user();
	?>
		<div class="dashboardWrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-6">
						<figure id="dashboardWrapperPanel-left">
							<?php

							/**
							 * Action to add HTML content after dashboard widgets.
							 */
							do_action('pto_dashboard_before_content');

							//Income/Expense by month widget file.
							if ( current_user_can( 'edit_cqpim_invoices' ) ) { 
								if ( get_option( 'disable_invoices ') != 1 ) {
									include_once 'income-by-month-widget.php';
								}
							}

							//My open tasks widget file.
							include_once 'my-open-task-widget.php';

							//Pending quotes and estimate widget file.
							include_once 'pending-quotes-estimate-widget.php';

							//My open support tickets.
							if ( pto_has_addon_active_license( 'pto_st', 'tickets' ) ) {
								include_once 'my-open-support.php';
							}

							?>	
						</figure>
					</div>
					<div class="col-xl-6">
						<figure id="dashboardWrapperPanel-left">
							<?php 

							//Project status widget
							include_once 'project-status-widget.php';

							//Project updates widget
							include_once 'project-updates-widget.php';

							//Outstanding invoice widget.
							include_once 'outstanding-invoice-widget.php';

							//Online member status widget
							include_once 'online-status-widget.php'; 

							/**
							 * Action to add HTML content after dashboard widgets.
							 */
							do_action( 'pto_dashboard_after_content' );

							?>
						</figure>
					</div>
				</div>
			</div>
		</div>
	<?php }
}
