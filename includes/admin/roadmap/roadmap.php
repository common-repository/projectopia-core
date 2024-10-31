<?php
add_action( 'admin_menu' , 'pto_register_roadmap_page', 999 ); 
function pto_register_roadmap_page() {
	global $submenu;
	// add the external links to the slug you used when adding the top level menu
    $submenu['pto-dashboard'][] = array( __( 'Roadmap', 'projectopia-core' ), 'cqpim_view_dashboard', 'https://projectopia.canny.io/' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
}   