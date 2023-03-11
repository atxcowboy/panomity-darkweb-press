<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since	  8.1
 *
 * @author	 Sascha Endlicher, M.A. <support@panomity.com>
 */
class Panomity_Darkweb_Press_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since	8.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'panomity-darkweb-press',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
