<?php
/**
 * Plugin Name: Panomity DarkWeb Press
 *
 * Defines the plugin name, version, and hooks to enqueue the public-specific
 * stylesheet and JavaScript when necessary.
 *
 * @author	 Sascha Endlicher, M.A. <support@panomity.com>
 */
class Panomity_Darkweb_Press_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since	8.1
	 *
	 * @var string the ID of this plugin
	 */
	private $panomity_darkweb_press;

	/**
	 * The version of this plugin.
	 *
	 * @since	8.1
	 *
	 * @var string the current version of this plugin
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	8.1
	 *
	 * @param string $panomity_token The Panomity API Bearer Token
	 */
	public function __construct( $panomity_darkweb_press, $version ) {
		$this->panomity_darkweb_press=$panomity_darkweb_press;
		$this->version=$version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since	8.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->panomity_darkweb_press, plugin_dir_url( __FILE__ ) . 'css/panomity-darkweb-press-public.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since	8.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->panomity_darkweb_press, plugin_dir_url( __FILE__ ) . 'js/panomity-darkweb-press-public.js', [ 'jquery' ], $this->version, false );
	}
}
