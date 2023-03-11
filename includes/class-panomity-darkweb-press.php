<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since	  8.1
 *
 * @author	 Sascha Endlicher, M.A. <support@panomity.com>
 */
class Panomity_Darkweb_Press {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since	8.1
	 *
	 * @var object maintains and registers all hooks for the plugin
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since	8.1
	 *
	 * @var string the string used to uniquely identify this plugin
	 */
	protected $panomity_darkweb_press;

	/**
	 * The current version of the plugin.
	 *
	 * @since	8.1
	 *
	 * @var string the current version of the plugin
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since	8.1
	 */
	public function __construct() {
		$this->panomity_darkweb_press='panomity-darkweb-press';
		$this->version='8.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - panomity-darkweb-press-loader. Orchestrates the hooks of the plugin.
	 * - panomity-darkweb-press-i18n. Defines internationalization functionality.
	 * - panomity-darkweb-press-admin. Defines all hooks for the admin area.
	 * - panomity-darkweb-press-public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since	8.1
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ )
		. 'includes/class-panomity-darkweb-press-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ )
		. 'includes/class-panomity-darkweb-press-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ )
		. 'admin/class-panomity-darkweb-press-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ )
		. 'public/class-panomity-darkweb-press-public.php';

		/**
		 * The class responsible for defining the dashboard widgets on the admin facing
		 * side of the plugin.
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ )
		 . 'includes/class-panomity-darkweb-press-custom-widgets.php';

		$this->loader=new Panomity_Darkweb_Press_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Panomity_Darkweb_Press_I18n class in order to set the domain and to
	 * register the hook with WordPress.
	 *
	 * @since	8.1
	 */
	private function set_locale() {
		$plugin_i18n=new Panomity_Darkweb_Press_I18n();

		$this->loader->add_action(
			'plugins_loaded',
			$plugin_i18n,
			'load_plugin_textdomain'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since	8.1
	 */
	private function define_admin_hooks() {
		$plugin_admin=new Panomity_Darkweb_Press_Admin( $this->get_panomity_darkweb_press(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since	8.1
	 */
	private function define_public_hooks() {
		$plugin_public=new Panomity_Darkweb_Press_Public( $this->get_panomity_darkweb_press(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since	8.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since	 8.1
	 *
	 * @return string the name of the plugin
	 */
	public function get_panomity_darkweb_press() {
		return $this->panomity_darkweb_press;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since	 8.1
	 *
	 * @return object loader orchestrates the hooks of the plugin
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since	 8.1
	 *
	 * @return string the version number of the plugin
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Checks if the given value is found on the dark web using the specified check type.
	 *
	 * @param string $check_type  the type of check to perform (password, domain, or email)
	 * @param string $check_value the value to check (password, domain, or email address)
	 *
	 * @return mixed|false the response body from the dark web check service, or false if
	 *					 there was an error
	 */
	public static function panomity_check( $check_type, $check_value ) {
		$panomity_token=get_option( 'panomity_token' );

		switch ( $check_type ) {
			case 'password':
				$service_id=get_option( 'panomity_darkweb_press_check_password_service_id' );

				if ( get_option( 'panomity_api' . $check_type ) == 'free' ) {
					$url="https://support.panomity.com/api/darkweb/{$service_id}/check_password";
				} else {
					$url="https://support.panomity.com/api/darkwebpro/{$service_id}/check_password_pro";
				}

				$data=[ 'check_password' => $check_value ];
				break;

			case 'domain':
				$service_id=get_option( 'panomity_darkweb_press_check_domain_service_id' );

				if ( get_option( 'panomity_api' . $check_type ) == 'free' ) {
					$url="https://support.panomity.com/api/darkweb/{$service_id}/check_domain";
				} else {
					$url="https://support.panomity.com/api/darkwebpro/{$service_id}/check_domain_pro";
				}

				$data=[ 'check_domain' => $check_value ];
				break;

			case 'email':
				$service_id=get_option( 'panomity_darkweb_press_check_email_service_id' );

				if ( get_option( 'panomity_api' . $check_type ) == 'free' ) {
					$url="https://support.panomity.com/api/darkweb/{$service_id}/check_email";
				} else {
					$url="https://support.panomity.com/api/darkwebpro/{$service_id}/check_email_pro";
				}

				$data=[ 'check_email' => $check_value ];
				break;

			default:
				return false;
		}

		$date=date( 'Y-m' );
		$option_name="panomity_darkweb_press_statistics_{$check_type}_{$date}";
		// Increment the option value
		$new_value=get_option( $option_name, 0 ) + 1;

		// Update the option with the new value
		update_option( $option_name, $new_value );

		$args=[
			'headers' => [
				'Authorization' => 'Bearer ' . $panomity_token,
				'Content-Type'  => 'application/json',
			],
			'body' => wp_json_encode( $data ),
		];

		$response=wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response_body=wp_remote_retrieve_body( $response );

		if ( strpos( $response_body, 'token_expired' ) !== false ) {
			// If the response contains "token_expired", get a new token and call the
			// function again
			$panomity_username=get_option( 'panomity_username' );
			$panomity_password=get_option( 'panomity_password' );
			Panomity_Darkweb_Press_Admin::get_panomity_token(
				$panomity_username,
				$panomity_password
			);

			return Panomity_Darkweb_Press::panomity_check( $check_type, $check_value );
		} else {
			// Otherwise, return the response body
			return $response_body;
		}
	}

	/**
	 * Generates a shortcode for displaying the dark web checker form(s) and handling
	 * the form submission via AJAX.
	 *
	 * @param array $atts	array of shortcode attributes
	 * @param mixed $content Optional. Shortcode content.
	 *
	 * @return string the shortcode output
	 */
	public static function panomity_darkweb_press_shortcode( $atts, $content=null ) {
		$output='';

		// Check if the form has been submitted via AJAX
		if ( isset( $_POST['check_value'] ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$check_type=sanitize_text_field( $_POST['check_type'] );
			$check_value=sanitize_text_field( $_POST['check_value'] );
			$response=Panomity_Darkweb_Press::panomity_check( $check_type, $check_value );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( esc_html__(
					'There was an error checking the value.',
					'panomity-darkweb-press'
				) );
			} else {
				$result=json_decode( wp_remote_retrieve_body( $response ) );
				$message=$result == 1
				? __( 'This ', 'panomity-darkweb-press' ) . $check_type . __( ' was found on the dark web!', 'panomity-darkweb-press' )
				: __( 'Great! This ', 'panomity-darkweb-press' ) . $check_type . __( ' was not found on the dark web!', 'panomity-darkweb-press' );
				wp_send_json_success( $message );
			}
		}

		// Display shortcode form(s)
		if ( isset( $atts['type'] ) ) {
			$check_type=sanitize_text_field( $atts['type'] );

			switch ( $check_type ) {
				case 'password':
					$output .= '
					<form id="check-password-form">
						<label class="required" for="check_password">'
							. esc_html__( 'Enter a password to check:', 'panomity-darkweb-press' ) . '
						</label>
						<input type="password" id="check_password_shortcode" name="check_password" 
						class="input-password" required>
						<input type="hidden" id="check_password_type_shortcode" name="check_type" value="' . $check_type . '">
						<input type="submit" class="button primary-button" value="' . esc_attr__(
								'Check',
								'panomity-darkweb-press'
							) . '">
					</form>
				';
					break;

				case 'domain':
					$output .= '
					<form id="check-domain-form">
						<label class="required" for="check_domain">'
							. esc_html__( 'Enter a domain to check:', 'panomity-darkweb-press' ) . '
						</label>
						<input type="text" id="check_domain_shortcode" name="check_domain" 
						class="input-domain" required>
						<input type="hidden" id="check_domain_type_shortcode" name="check_type" value="' . $check_type . '"><br />
						<input type="checkbox" id="confirm_access" name="confirm_access" required>
						<label class="required" for="confirm_access" style="font-size: 0.8rem;">'
							. esc_html__( 'We will send the report to the hostmaster email address of your domain. Please confirm that you have access to this email address by checking the checkbox.', 'panomity-darkweb-press' ) . '
						</label><br />
						<input type="submit" class="button primary-button" value="' . esc_attr__(
								'Check',
								'panomity-darkweb-press'
							) . '">
					</form>
				';
					break;

				case 'email':
					$output .= '
				<form id="check-email-form">
					<label class="required" for="check_email">'
							. esc_html__( 'Enter an email address to check:', 'panomity-darkweb-press' ) . '
					</label>
					<input type="email" id="check_email_shortcode" name="check_email" 
					class="input-email" required>
					<input type="hidden" id="check_email_type_shortcode" name="check_type" value="' . $check_type . '"><br />
					<input type="checkbox" id="confirm_email" name="confirm_access" required>
					<label class="required" for="confirm_email" style="font-size: 0.8rem;">'
						. esc_html__( 'The result will be sent to the email. Please confirm that you are authorized and have have access to this email address by checking the checkbox.', 'panomity-darkweb-press' ) . '
					</label><br />
					<input type="submit" class="button primary-button" value="' . esc_attr__(
							'Check',
							'panomity-darkweb-press'
						) . '">
				</form>
			';

					// Add JavaScript to handle the form submission via AJAX
					$output .= '
			<script>

		

			jQuery(document).ready(function($) {
				// Add a function to validate the domain name
				function isValidDomain(domain) {
				  // Regular expression to match a valid domain name
				  var domainRegex = /^((?!-)[A-Za-z0-9-]{1,63}(?<!-)\.)+[A-Za-z]{2,6}$/;
				  return domainRegex.test(domain);
				}			
				$("#check-password-form, #check-domain-form, #check-email-form").on("submit", function(event) {
					event.preventDefault();
					var formId = $(this).attr("id");
					var ajaxUrl = "' . esc_url_raw( admin_url( 'admin-ajax.php' ) ) . '";
					var inputName = "";
					var errorMessage = "";
					var successMessage = "";
					var notFoundMessage = "";
					switch (formId) {
						case "check-password-form":
							ajaxUrl = "' . esc_url_raw( rest_url( 'panomity-darkweb-press/v1/check-password/' ) ) . '";
							inputName = "check_password";
							errorMessage = "' . esc_html__(
						'There was an error checking the password.',
						'panomity-darkweb-press'
					) . '";
							successMessage = "' . esc_html__(
						'This password was found on the dark web!',
						'panomity-darkweb-press'
					) . '";
							notFoundMessage = "' . esc_html__(
						'Great! This password was not found on the dark web!',
						'panomity-darkweb-press'
					) . '";
							break;
						case "check-domain-form":
							// Get the input value
							  var domain = $("#check-domain-form input[name=\"check_domain\"]").val();
							
							  // Check if the input is a valid domain name
							  if (!isValidDomain(domain)) {
								$(".updated").remove();
								$("#check-domain-form").after("<div class=\"updated error-notification\"><p>" + "' . esc_html__( 'Invalid domain name.', 'panomity-darkweb-press' ) . '" + "</p></div>");
															
								return;
							  }
							ajaxUrl = "' . esc_url_raw( rest_url( 'panomity-darkweb-press/v1/check-domain/' ) ) . '";
							inputName = "check_domain";
							errorMessage = "' . esc_html__(
						'There was an error checking the domain.',
						'panomity-darkweb-press'
					) . '";
							successMessage = "' . esc_html__(
						'The result of the dark web check has been sent.',
						'panomity-darkweb-press'
					) . '";
							notFoundMessage = "' . esc_html__(
						'There was a problem sending the result of the dark web check.',
						'panomity-darkweb-press'
					) . '";
							break; 
						case "check-email-form":
							ajaxUrl = "' . esc_url_raw( rest_url( 'panomity-darkweb-press/v1/check-email/' ) ) . '";
							inputName = "check_email";
							errorMessage = "' . esc_html__(
						'There was an error checking the email address.',
						'panomity-darkweb-press'
					) . '";
							successMessage = "' . esc_html__(
						'The result of the dark web check has been sent.',
						'panomity-darkweb-press'
					) . '";
							notFoundMessage = "' . esc_html__(
						'There was a problem sending the result of the dark web check.',
						'panomity-darkweb-press'
					) . '";
							break;
					}
					$.ajax({
						url: ajaxUrl,
						method: "POST",
						data: {
							[inputName]: $("#" + formId + " input[name=\'" + inputName + "\']").val()
						},
						beforeSend: function() {
							// Show spinning wheel before AJAX call
							$(".updated").remove(); // Remove any existing response messages
							//$(this).after("<div class=\"updated\"><p><i class=\"fa fa-spinner fa-spin\"></i>' . esc_html__( 'Checking...' ) . '</p></div>");
						},
						success: function(response) {
							var message;
							console.log(response);
							
							if (JSON.stringify(response).indexOf("1") !== -1) {
								message = successMessage;
							} else {
								message = notFoundMessage;
							}
							$(".updated").remove(); // Remove any existing response messages
							$("#" + formId).after("<div class=\"updated success-notification\"><p>" + message + "</p></div>");
						},				 
						error: function(response) {
							$(".updated").remove(); // Remove any existing response messages
							$("#" + formId).after("<div class=\"updated error-notification\"><p>" + errorMessage + "</p></div>");
						},
						complete: function() {
							// Hide spinning wheel after AJAX call is complete
							$(".updated p:contains(\"' . esc_html__( 'Checking...' ) . '\")").remove();
						}
					});
				});
			});
			</script>
			';
			}
		}

		return $output;
	}
}
