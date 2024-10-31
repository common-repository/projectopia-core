<?php
/*
Plugin Name: Projectopia - WordPress Project Management Plugin
Description: Projectopia is a solution for small and medium businesses who want to manage their clients, quotes, projects and invoices more efficiently. Work individually or as part of a team, and start streamlining your processes!
Version: 5.1.6
Author: Projectopia
Author URI: https://projectopia.io
Text Domain: projectopia-core
Domain Path: /languages
*/

define( 'PTO_VERSION', '5.1.6' );
define( 'PTO_PHP_VERSION', phpversion() );

// Paths
define( 'PTO_PATH', plugin_dir_path( __FILE__ ) );
define( 'PTO_FILE', plugin_dir_path( __FILE__ ) . 'projectopia-core.php' );
define( 'PTO_FUNCTIONS_PATH', plugin_dir_path( __FILE__ ) . 'includes/functions' );   
define( 'PTO_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'includes/admin' );
define( 'PTO_CAPS_PATH', plugin_dir_path( __FILE__ ) . 'includes/capabilities' );
define( 'PTO_CONTENT_PATH', plugin_dir_path( __FILE__ ) . 'includes/cpt' );
define( 'PTO_CSS_PATH', plugin_dir_path( __FILE__ ) . 'includes/css' );
define( 'PTO_ENQ_PATH', plugin_dir_path( __FILE__ ) . 'includes/enqueing' );
define( 'PTO_FE_PATH', plugin_dir_path( __FILE__ ) . 'includes/frontend' );
define( 'PTO_INSTALL_PATH', plugin_dir_path( __FILE__ ) . 'includes/install' );
define( 'PTO_META_PATH', plugin_dir_path( __FILE__ ) . 'includes/meta' );
define( 'PTO_SCRIPTS_PATH', plugin_dir_path( __FILE__ ) . 'includes/scripts' );
define( 'PTO_SC_PATH', plugin_dir_path( __FILE__ ) . 'includes/shortcodes' );
define( 'PTO_SETTINGS_PATH', plugin_dir_path( __FILE__ ) . 'includes/settings' );
define( 'PTO_UNINSTALL_PATH', plugin_dir_path( __FILE__ ) . 'includes/uninstall' );
define( 'PTO_DIRNAME', basename( plugin_basename( PTO_PATH ) ) );
define( 'PTO_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'PTO_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/admin/' );
define( 'PTO_GLOBAL_NONCE', 'pto-global-nonce' );

if ( function_exists( 'projectopia_fs' ) ) {
    projectopia_fs()->set_basename( true, __FILE__ );
} else {
    if ( ! function_exists( 'projectopia_fs' ) ) {
        // Create a helper function for easy SDK access.
        function projectopia_fs() {
            global $projectopia_fs;

            if ( ! isset( $projectopia_fs ) ) {
                // Include Freemius SDK.
                require_once dirname(__FILE__) . '/assets/freemius/start.php';

                $projectopia_fs = fs_dynamic_init( array(
                    'id'                             => '9169',
                    'bundle_id'                      => '9210',
                    'slug'                           => 'projectopia-core',
                    'type'                           => 'plugin',
                    'public_key'                     => 'pk_4a3106685fe24166508cf1f070438',
                    'bundle_public_key'              => 'pk_1f7e1fb302b66b54831e0cfb7247d',
                    'is_premium'                     => false,
                    'has_addons'                     => true,
                    'has_paid_plans'                 => true,
                    'bundle_license_auto_activation' => true,
                    'is_org_compliant'               => true,
                    'menu'                           => array(
                        'slug'       => 'pto-dashboard',
                        'first-path' => 'admin.php?page=pto-settings&sub-page=pto-initial-settings',
                        'account'    => true,
                        'support'    => false,
                        'contact'    => false,
                    ),
                ) );
            }

            return $projectopia_fs;
        }

        // Init Freemius.
        projectopia_fs();
        // Signal that SDK was initiated.
        do_action( 'projectopia_fs_loaded' );
    }
}

projectopia_fs()->add_filter( 'has_paid_plan_account', '__return_false' );
projectopia_fs()->add_filter( 'is_submenu_visible', 'pto_fs_is_submenu_visible', 10, 2 );

function pto_fs_is_submenu_visible( $is_visible, $submenu_id ) {
	if ( 'pricing' === $submenu_id ) {
		return pto_is_free_plan();
	}
	return $is_visible;
}

function pto_is_free_plan() {
    $paid_addons = pto_get_fs_addons();
    $free_plan = true;
    foreach ( $paid_addons as $data ) {
        if ( ! function_exists( $data['fs_func'] ) ) {
            continue;
        }

        // Initiate the Freemius instance.
        $addon_fs = call_user_func( $data['fs_func'] );

        if ( ! method_exists( $addon_fs, 'has_active_valid_license' ) ) {
            continue;
        }

        if ( $addon_fs->has_active_valid_license() ) {
            $licenses = $addon_fs->_get_license();

            if ( is_object( $licenses ) && $licenses->parent_license_id ) {
                $free_plan = ! FS_Plugin_License::is_valid_id( $licenses->parent_license_id );
            } else {
                $free_plan = false;
            }
            break;
        }
    }
    return $free_plan;
}

// Load I18n.
add_action( 'plugins_loaded', 'pto_languages_setup' );
function pto_languages_setup() {
	load_plugin_textdomain( 'projectopia-core', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

// Includes
require_once( PTO_SETTINGS_PATH . '/settings.php' );
require_once( PTO_INSTALL_PATH . '/install.php' );
require_once( PTO_INSTALL_PATH . '/compat.php' );
require_once( PTO_UNINSTALL_PATH . '/uninstall.php' );
require_once( PTO_ADMIN_PATH . '/admin.php' );    
require_once( PTO_FUNCTIONS_PATH . '/functions.php' );
require_once( PTO_ENQ_PATH . '/enqueing.php' );
require_once( PTO_CAPS_PATH . '/capabilities.php' );
require_once( PTO_CONTENT_PATH . '/cpt.php' );
require_once( PTO_META_PATH . '/metaboxes.php' );
require_once( PTO_SC_PATH . '/shortcodes.php' );