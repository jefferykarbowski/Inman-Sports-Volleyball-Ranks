<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://inmansports.com
 * @since             1.0.0
 * @package           Inman_Sports_Volleyball_Ranks
 *
 * @wordpress-plugin
 * Plugin Name:       Inman Sports Volleyball Ranks
 * Plugin URI:        https://inmansports.com
 * Description:       Shortcodes to display and manage volleyball ranks.
 * Version:           1.1.8
 * Author:            Andrew Inman
 * Author URI:        https://inmansports.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       inman_sports_volleyball_ranks
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'INMAN_SPORTS_VOLLEYBALL_RANKS_VERSION', '1.1.8' );

//global $vball_db_version;
//$vball_db_version = '1.0';




function is_user_pending() {
    global $user_ID;
    $pending = get_user_meta( $user_ID, 'exp_type', true );
    return ( $pending == 'pending' ) ? true : false;
}


add_action( 'get_header', 'inman_sports_volleyball_ranks_do_acf_form_head', 1 );
function inman_sports_volleyball_ranks_do_acf_form_head() {
    if ( is_page( 'registration' ) ) {
        acf_form_head();
    }

}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-inman-sports-volleyball-ranks-activator.php
 */
function activate_inman_sports_volleyball_ranks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-inman-sports-volleyball-ranks-activator.php';
	Inman_Sports_Volleyball_Ranks_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-inman-sports-volleyball-ranks-deactivator.php
 */
function deactivate_inman_sports_volleyball_ranks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-inman-sports-volleyball-ranks-deactivator.php';
	Inman_Sports_Volleyball_Ranks_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_inman_sports_volleyball_ranks' );
register_deactivation_hook( __FILE__, 'deactivate_inman_sports_volleyball_ranks' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-inman-sports-volleyball-ranks.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_inman_sports_volleyball_ranks() {

	$plugin = new Inman_Sports_Volleyball_Ranks();
	$plugin->run();

}
run_inman_sports_volleyball_ranks();



function has_player_access() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        if ( in_array( 'administrator', (array) $user->roles ) ) {
            return true;
        }
        if (wpmem_user_has_access(880, $user->ID)) {
            return true;
        }
    }
    return false;
}

function has_coach_scout_access() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        if ( in_array( 'administrator', (array) $user->roles ) ) {
            return true;
        }
        if (wpmem_user_has_access(879, $user->ID)) {
            return true;
        }
    }
    return false;
}



/**
 * The class responsible for upgrading the plugin.
 */
require_once  plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';

$apis_update_checker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/jefferykarbowski/Inman-Sports-Volleyball-Ranks/',
    __file__,
    'Inman-Sports-Volleyball-Ranks'
);
$apis_update_checker->getVcsApi()->enableReleaseAssets();