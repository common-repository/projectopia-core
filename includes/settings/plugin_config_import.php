<?php
add_action( 'admin_init', 'pto_import_default_settings' );
function pto_import_default_settings() {
	include( 'config_template.php' );
	$installed = get_option( 'cqpim_settings_imported' );
	if ( ! $installed ) {
		$settings = pto_settings_values();
		foreach ( $settings as $element ) {
			foreach ( $element as $key => $setting ) {
				$data = get_option( $key );
				if ( ! $data ) {
					update_option( $key, $setting );
				}
			}
		}
	}
}