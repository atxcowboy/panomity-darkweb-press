<?php
/**
 * Panomity Darkweb Press admin-specific functionality.
 *
 * Defines the plugin name, version, and hooks to enqueue the admin-specific
 * stylesheet and JavaScript when necessary.
 *
 * @author	 Sascha Endlicher, M.A. <support@panomity.com>
 */
class Panomity_Darkweb_Press_Admin {

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
	 * @param string $panomity_darkweb_press the name of this plugin
	 * @param string $version				the version of this plugin
	 */
	public function __construct( $panomity_darkweb_press, $version ) {
		$this->panomity_darkweb_press=$panomity_darkweb_press;
		$this->version=$version;
		add_action( 'admin_init', [ $this, 'register_panomity_darkweb_press_settings' ] );
		add_action( 'admin_menu', [ $this, 'panomity_darkweb_press_admin_menu' ] );
		add_action( 'admin_menu', [ $this, 'panomity_darkweb_press_add_email_menu' ] );
		add_action( 'admin_menu', [ $this, 'panomity_darkweb_press_add_shortcodes_menu' ] );
	}

	/**
	 * Registers the options for the administrative settings
	 *
	 * @since	8.1
	 */
	public function register_panomity_darkweb_press_settings() {
		$settings=[
			'panomity_token',
			'panomity_darkweb_press_check_password_service_id',
			'panomity_darkweb_press_check_domain_service_id',
			'panomity_darkweb_press_check_email_service_id',
			'panomity_darkweb_press_bulkcheck_service_id',
			'panomity_darkweb_press_domaindetails_service_id',
			'panomity_darkweb_press_passworddetails_service_id',
			'panomity_darkweb_press_keyworddetails_service_id',
		];

		foreach ( $settings as $setting ) {
			register_setting( 'panomity-darkweb-press-group', $setting );
		}
	}

	/**
	 * Adds the options page and a dashboard menu item
	 *
	 * @since	8.1
	 */
	public function panomity_darkweb_press_admin_menu() {
		add_menu_page(
			'Panomity DarkWeb Press',
			'Panomity DarkWeb Press',
			'manage_options',
			'panomity-darkweb-press',
			'Panomity_Darkweb_Press_Admin::panomity_darkweb_press_admin_page',
			'dashicons-dashboard',
			81
		);
	}

	public function panomity_darkweb_press_add_email_menu() {
		add_submenu_page(
			'panomity-darkweb-press', // Parent menu slug
			'Bulk Check Emails', // Page title
			'Bulk Check Emails', // Menu title
			'manage_options', // Capability required to access the page
			'panomity-darkweb-press-emails', // Menu slug
			'Panomity_Darkweb_Press_Admin::panomity_darkweb_press_bulk_check_emails' // Callback function to render the page content
		);
	}

	public function panomity_darkweb_press_add_shortcodes_menu() {
		add_submenu_page(
			'panomity-darkweb-press', // Parent menu slug
			'Shortcodes', // Page title
			'Shortcodes', // Menu title
			'manage_options', // Capability required to access the page
			'panomity-darkweb-press-shortcodes', // Menu slug
			'Panomity_Darkweb_Press_Admin::panomity_darkweb_press_shortcodes' // Callback function to render the page content
		);
	}

	/**
	 * Displays the admin page for the Panomity Darkweb Press plugin.
	 *
	 * @since 8.1
	 */
	public static function panomity_darkweb_press_admin_page() {
		require_once 'partials/panomity-darkweb-press-admin-display.php';
	}

	public static function panomity_darkweb_press_bulk_check_emails() {
		require_once 'partials/panomity-darkweb-press-admin-bulk-check-emails-display.php';
	}

	public static function panomity_darkweb_press_shortcodes() {
		require_once 'partials/panomity-darkweb-press-admin-shortcodes-display.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since	8.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->panomity_darkweb_press,
			plugin_dir_url( __FILE__ ) . 'css/panomity-darkweb-press-admin.css',
			[],
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since	8.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->panomity_darkweb_press,
			plugin_dir_url( __FILE__ ) . 'js/panomity-darkweb-press-admin.js',
			[ 'jquery' ],
			$this->version,
			false
		);
	}

	/**
	 * Retrieves a Panomity token.
	 *
	 * @return string|bool returns the Panomity token as a string, or false on error
	 */
	public static function get_panomity_token() {
		$url='https://support.panomity.com/api/login';

		$post_data=[
			'username' => get_option( 'panomity_username' ),
			'password' => get_option( 'panomity_password' ),
		];

		$args=[
			'body'	   => wp_json_encode( $post_data ),
			'headers' => [
				'Content-Type' => 'application/json',
			],
		];

		$response=wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false; // handle error
		} else {
			$body=wp_remote_retrieve_body( $response );
			$json=json_decode( $body );
			$panomity_token=$json->token;
			update_option( 'panomity_token', $panomity_token );

			return $panomity_token;
		}
	}

	 /**
	  * Retrieves the service ID for the specified Panomity Dark Web Press service type.
	  *
	  * @param string $panomity_token the Panomity token to use for authorization
	  * @param string $service_type the type of Panomity Dark Web Press service to retrieve the ID for
	  *
	  * @return int|null the service ID for the specified service type, or null if the service could not be found
	  */
	 public static function get_panomity_darkweb_press_service_id( $panomity_token, $service_type ) {
		 $url='https://support.panomity.com/api/service';
		 $args=[
			 'headers' => [
				 'Authorization' => "Bearer $panomity_token",
			 ],
		 ];

		 $response=wp_remote_get( $url, $args );

		 if ( is_wp_error( $response ) ) {
			 return false; // handle error
		 }

		 $body=wp_remote_retrieve_body( $response );
		 $json=json_decode( $body );
		 $services=$json->services;

		 $service_id=self::get_service_id_by_name( $service_type, $services );

		 if ( $service_id ) {
			 update_option( "panomity_darkweb_press_check_{$service_type}_service_id", $service_id );
		 }

		 return $service_id;
	 }

	/**
	 * Retrieves the service ID for the specified service type from the provided list of services.
	 *
	 * @param string	 $service_type the type of service to retrieve the ID for
	 * @param stdClass[] $services	 an array of service objects returned from the Panomity API
	 *
	 * @return int|null the service ID for the specified service type, or null if the service could not be found
	 */
	private static function get_service_id_by_name( $service_type, $services ) {
		switch ( $service_type ) {
			case 'password':
				$service_names=[
					'Dark Web API Leaked Passwords Pro',
					'Dark Web API Leaked Passwords',
				];
				break;

			case 'domain':
				$service_names=[
					'Dark Web API Leaked Domains Pro',
					'Dark Web API Leaked Domains',
				];
				break;

			case 'email':
				$service_names=[
					'Dark Web API Leaked Emails Pro',
					'Dark Web API Leaked Emails',
				];
				break;

			case 'bulkcheck':
				$service_names=[
					'Dark Web API Bulk Check Emails',
				];
				break;

			case 'domaindetails':
				$service_names=[
					'Dark Web API Domain Details',
				];
				break;

			case 'passworddetails':
				$service_names=[
					'Dark Web API Password Details',
				];
				break;

			case 'keyworddetails':
				$service_names=[
					'Dark Web API Keyword Detailss',
				];
				//no break
			default:
				return null;
		}

		$service_id=null;

		foreach ( $services as $service ) {
			if ( in_array( $service->name, $service_names ) ) {
				if ( strpos( $service->name, 'Pro' ) !== false ) {
					$service_id=$service->id;
					update_option( 'panomity_api' . $service_type, 'pro' );
					break;
				} else {
					update_option( 'panomity_api' . $service_type, 'pro' );
					$service_id=$service->id;
				}
			}
		}

		return $service_id;
	}

	public static function register_and_check_services( $service_types ) {
		foreach ( $service_types as $service_type ) {
			switch ( $service_type ) {
				case 'password':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=811';
					break;

				case 'domain':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=823';
					break;

				case 'email':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=825';
					break;

				case 'bulkcheck':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=827';
					break;

				case 'domaindetails':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=828';
					break;

				case 'passworddetails':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=829';
					break;

				case 'keyworddetails':
					$registration_link='https://support.panomity.com/?cmd=cart&action=add&id=830';
					break;
			}
			self::check_service( $service_type, $registration_link );
		}
	}

// Function to check service ID and display notification
	public static function check_service( $service_type, $registration_link ) {
		$panomity_token=Panomity_Darkweb_Press_Admin::get_panomity_token();
		$service_id=Panomity_Darkweb_Press_Admin::get_panomity_darkweb_press_service_id( $panomity_token, $service_type );
		update_option( 'panomity_token', $panomity_token );
		update_option( 'panomity_darkweb_check_' . $service_type . '_service_id', $service_id );

		if ( is_numeric( $service_id ) ) {
			// Display success notification if service ID is a number
			echo '<div class="notice notice-success is-dismissible">';
			$allowed_types=[ 'password', 'domain', 'email' ];

			if ( in_array( $service_type, $allowed_types ) ) {
				/* translators: %1$s is the capitalized service type and %2$s is the service type */
				printf(
					'<p>%1$s %2$s %3$s. %4$s [panomity_darkweb_press type="%5$s"] %6$s.</p>',
					esc_html__( 'Panomity DarkWeb Press for', 'panomity-darkweb-press' ),
					esc_html( ucfirst( $service_type ) ),
					esc_html__( 'Leaks is licensed. Use the shortcode', 'panomity-darkweb-press' ),
					esc_html__( 'to add it anywhere on your site.', 'panomity-darkweb-press' ),
					esc_attr( $service_type ),
					esc_html__( 'to add it anywhere on your site', 'panomity-darkweb-press' )
				);
			} else {
				/* translators: %1$s is the capitalized service type */
				printf(
					'<p>%1$s %2$s.</p>',
					esc_html__( 'Panomity DarkWeb Press for', 'panomity-darkweb-press' ),
					esc_html( ucfirst( $service_type ) )
				);
			}
			echo '</div>';
		} else {
			// Display warning notification if service ID is not a number
			echo '<div class="notice notice-warning is-dismissible">';
			/* translators: %1$s is the capitalized service type, %2$s is the service type and %3$s is the registration link */
			printf(
				'<p><strong>%1$s %2$s</strong>: %4$s %2$s %5$s. %6$s <a href="%7$s" target="_blank"><strong>%8$s</strong></a>.</p>',
				esc_html__( 'Panomity DarkWeb Press for', 'panomity-darkweb-press' ),
				esc_html( ucfirst( $service_type ) ),
				esc_html__( 'Leaks', 'panomity-darkweb-press' ),
				esc_html__( 'This feature allows your users to search for', 'panomity-darkweb-press' ),
				esc_html__( 'related data breaches. To access Panomity DarkWeb Press for', 'panomity-darkweb-press' ),
				esc_html__( 'Please register for our free product. Click', 'panomity-darkweb-press' ),
				esc_url( $registration_link ),
				esc_html__( 'HERE', 'panomity-darkweb-press' )
			);
			echo '</div>';
		}
	}
}
