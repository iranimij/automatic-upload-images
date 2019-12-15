<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.iranimij.com
 * @since             1.0.0
 * @package           Automatic_Upload_Images
 *
 * @wordpress-plugin
 * Plugin Name:       automatic upload images
 * Plugin URI:        http://www.hamyarwoo.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.1
 * Author:            iman heydari
 * Author URI:        http://www.iranimij.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       automatic-upload-images
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
define( 'AUTOMATIC_UPLOAD_IMAGES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-automatic-upload-images-activator.php
 */
function activate_automatic_upload_images() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-automatic-upload-images-activator.php';
	Automatic_Upload_Images_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-automatic-upload-images-deactivator.php
 */
function deactivate_automatic_upload_images() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-automatic-upload-images-deactivator.php';
	Automatic_Upload_Images_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_automatic_upload_images' );
register_deactivation_hook( __FILE__, 'deactivate_automatic_upload_images' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-automatic-upload-images.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_automatic_upload_images() {

	$plugin = new Automatic_Upload_Images();
	$plugin->run();

}
run_automatic_upload_images();
