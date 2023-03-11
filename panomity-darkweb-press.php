<?php

/**
 * The Panomity DarkWeb Press bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name: Panomity DarkWeb Press
 * Plugin URI: https://panomity.com/software/panomity-darkweb-press/
 * Description: The Panomity DarkWeb Press plugin is the foundation for our Dark web Gateway and allows to search if a password has been compromised on the Darkweb.
 * Version: 8.1.2
 * Author: Sascha Endlicher, M.A.
 * Author URI: https://panomity.com/
 * License: GPL3
 * License URI:	   https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:	   panomity-darkweb-press
 * Domain Path:	   /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-panomity-darkweb-press-activator.php
 */
function activate_panomity_darkweb_press() {
	require_once plugin_dir_path( __FILE__ )
				. 'includes/class-panomity-darkweb-press-activator.php';
	Panomity_Darkweb_Press_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-panomity-darkweb-press-deactivator.php
 */
function deactivate_panomity_darkweb_press() {
	require_once plugin_dir_path( __FILE__ )
				. 'includes/class-panomity-darkweb-press-deactivator.php';
	Panomity_Darkweb_Press_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_panomity_darkweb_press' );
register_deactivation_hook( __FILE__, 'deactivate_panomity_darkweb_press' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-panomity-darkweb-press.php';

/**
 * Begins execution of the plugin.
 *
 * @since	8.1
 */
function run_panomity_darkweb_press() {
	$panomity_darkweb_press=new Panomity_Darkweb_Press();
	$panomity_darkweb_press->run();
}

run_panomity_darkweb_press();
