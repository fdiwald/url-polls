<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/fdiwald/url-polls
 * @since             1.0.0
 * @package           Url_Polls
 *
 * @wordpress-plugin
 * Plugin Name:       URL Polls
 * Plugin URI:        https://github.com/fdiwald/url-polls
 * Description:       Send out polls and receive feedback via URLs
 * Version:           1.0.0
 * Author:            Florian Diwald
 * Author URI:        https://github.com/fdiwald
 * License:           GPL-2.0+
 * License URI:       https://github.com/fdiwald/url-polls/blob/main/LICENSE
 * Text Domain:       url-polls
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'URL_POLLS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-url-polls-activator.php
 */
function activate_url_polls() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-url-polls-activator.php';
	Url_Polls_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-url-polls-deactivator.php
 */
function deactivate_url_polls() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-url-polls-deactivator.php';
	Url_Polls_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_url_polls' );
register_deactivation_hook( __FILE__, 'deactivate_url_polls' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-url-polls.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_url_polls() {

	$plugin = new Url_Polls();
	$plugin->run();

}
run_url_polls();
