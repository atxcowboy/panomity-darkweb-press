<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @see	   https://panomity.com/software/panomity-darkweb-press/
 * @since	  8.1
 */
add_action( 'admin_menu', 'panomity_darkweb_press_admin_menu' );
add_action( 'admin_init', 'register_panomity_darkweb_press_settings' );

if ( isset( $_POST['submit'] ) ) {
	// Sanitize the input values
	$username=sanitize_text_field( $_POST['panomity_username'] );
	$password=sanitize_text_field( $_POST['panomity_password'] );

	// Save the values as options using update_option()
	update_option( 'panomity_username', $username );
	update_option( 'panomity_password', $password );
}

// Retrieve the values from options
$username=isset( $_POST['submit'] ) ? $username : get_option( 'panomity_username' );
$password=isset( $_POST['submit'] ) ? $password : get_option( 'panomity_password' );

/**
 * Adds a menu page for the Panomity DarkWeb Press plugin.
 *
 * @since 8.1
 *
 * @return void
 */
function panomity_darkweb_press_admin_menu() {
	add_menu_page(
		__( 'Panomity DarkWeb Press', 'panomity-darkweb-press' ),
		__( 'Panomity DarkWeb Press', 'panomity-darkweb-press' ),
		'manage_options',
		'panomity-darkweb-press/panomity-darkweb-press-admin-page.php',
		'panomity_darkweb_press_admin_page',
		'dashicons-dashboard',
		81
	);
}

/**
 * Registers settings for the Panomity DarkWeb Press plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_panomity_darkweb_press_settings() {
	$settings=[
		'panomity_token',
		'panomity_darkweb_press_check_password_service_id',
		'panomity_darkweb_press_check_domain_service_id',
		'panomity_darkweb_press_check_email_service_id',
	];

	foreach ( $settings as $setting ) {
		register_setting(
			'panomity-darkweb-press-group',
			$setting
		);
	}
}
function panomity_darkweb_press_admin_page() {
	//		include_once('panomity-darkweb-press-admin-page.php');
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Panomity DarkWeb Press', 'panomity-darkweb-press' ); ?></h1>
	<div class="notice notice-info">
		<p><?php echo wp_kses(
	sprintf(
				__( 'For more information about Panomity DarkWeb Press, please visit our <a href="%1$s" target="_blank"><strong>official product page</strong></a>. If you have additional questions, please open a <a href="%2$s" target="_blank"><strong>support request</strong></a>.', 'panomity-darkweb-press' ),
				esc_url( 'https://panomity.com/software/panomity-darkweb-press/' ),
				esc_url( 'https://support.panomity.com/tickets/new/' )
			),
	[
				'a' => [
					'href'   => [],
					'target' => [],
					'class'  => [],
					'rel'	=> [],
					'style'  => [],
				],
				'strong' => [],
			]
); ?></p>
	</div>
</div>


<style>
.wp-shortcodes {
	margin: 0 auto;
	padding: 20px;
	background-color: #f7f7f7;
	border: 1px solid #ccc;
	border-radius: 5px;
	box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
	width: 100%;
	box-sizing: border-box;
}

.wp-shortcodes h2 {
	font-size: 20px;
	font-weight: 700;
	margin-top: 0;
	margin-bottom: 20px;
}

.wp-shortcodes p {
	font-size: 16px;
	line-height: 1.5;
	margin-bottom: 20px;
}

.wp-shortcodes .shortcode-container {
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	margin-top: 20px;
}

.wp-shortcodes .shortcode-container .shortcode-box {
	flex-basis: 30%;
	padding: 20px;
	background-color: #fff;
	border: 1px solid #ccc;
	border-radius: 5px;
	box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
	margin-bottom: 20px;
}

.wp-shortcodes .shortcode-container .shortcode-box h3 {
	font-size: 20px;
	font-weight: 700;
	margin-top: 0;
	margin-bottom: 10px;
}

.wp-shortcodes .shortcode-container .shortcode-box p {
	font-size: 16px;
	line-height: 1.5;
	margin-top: 0;
	margin-bottom: 10px;
}
</style>

<div class="wp-shortcodes">
	<h2><?php esc_html_e( 'Make Your Site Stand Out', 'panomity-darkweb-press' ); ?></h2>
	<p><?php esc_html_e( 'With WordPress shortcodes, you can easily add dynamic content and functionality to your pages and posts, without needing to write any code. Plus, they work with any WordPress theme, so you don\'t need to worry about compatibility issues.', 'panomity-darkweb-press' ); ?></p>
	<p><?php printf( esc_html__( 'If you want to make some extra money from your site, join our affiliate program at %s. You\'ll earn a commission every time a customer requests a detailed dark web report. It\'s a win-win!', 'panomity-darkweb-press' ), '<a href="https://support.panomity.com/affiliates/"><strong>https://support.panomity.com/affiliates/</strong></a>' ); ?></p>

	<div class="shortcode-container">
		<div class="shortcode-box">
			<h3><?php esc_html_e( 'Password', 'panomity-darkweb-press' ); ?></h3>
			<?php echo do_shortcode( '[panomity_darkweb_press type="password"]' ); ?><br />
			<?php esc_html_e( 'The [panomity_darkweb_press type="password"] shortcode allows to check if a password has been leaked on the dark web.', 'panomity-darkweb-press' ); ?>
		</div>
		<div class="shortcode-box">
			<h3><?php esc_html_e( 'Domain', 'panomity-darkweb-press' ); ?></h3>
			<?php echo do_shortcode( '[panomity_darkweb_press type="domain"]' ); ?><br />
			<?php esc_html_e( 'The [panomity_darkweb_press type="domain"] shortcode will check if accounts of a certain domain have been compromised and send a report to hostmaster@domain.com.', 'panomity-darkweb-press' ); ?>
		</div>
		<div class="shortcode-box">
			<h3><?php esc_html_e( 'Email', 'panomity-darkweb-press' ); ?></h3>
			<?php echo do_shortcode( '[panomity_darkweb_press type="email"]' ); ?><br />
			<?php esc_html_e( 'The [panomity_darkweb_press type="email"] shortcode will allow website visitors to check if their email address was found on the dark web.', 'panomity-darkweb-press' ); ?>
		</div>
	</div>
</div>


<?php
					$service_types=[ 'password', 'domain', 'email' ];
Panomity_Darkweb_Press_Admin::register_and_check_services( $service_types );
$panomity_token=get_option( 'panomity_token' );
?>

<div id="product_details" style="display:none">
	<table class="widefat">
		<tbody>
			<?php foreach ( $service_types as $service_type ) { ?>
			<tr>
				<td><strong><?php echo esc_html_e( 'Panomity DarkWeb Check', 'panomity-darkweb-press' ) . ' ' . ucfirst( esc_html( $service_type ) ) . ' ' . esc_html_e( 'Service ID', 'panomity-darkweb-press' ); ?></strong>:</td>
				<td>
					<?php
						$service_id=Panomity_Darkweb_Press_Admin::get_panomity_darkweb_press_service_id( $panomity_token, $service_type );

				if ( is_numeric( $service_id ) ) {
					echo esc_html( $service_id );
				} else {
					echo '<span style="color: red;">' . esc_html_e( 'Not set. Click', 'panomity-darkweb-press' ) . ' <a href="https://support.panomity.com/cart/dark-web-api/" target="_blank"><strong>' . esc_html_e( 'HERE', 'panomity-darkweb-press' ) . '</strong></a> ' . esc_html_e( 'to add product.', 'panomity-darkweb-press' ) . '</span>';
				}
				?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>


<button id="toggle_product_details" style="margin-top: 20px;"><?php esc_html_e( 'Show Debug Details', 'panomity-darkweb-press' ); ?></button>

<script>
var toggleButton = document.getElementById('toggle_product_details');
var tokenDiv = document.getElementById('product_details');

toggleButton.addEventListener('click', function() {
	if (tokenDiv.style.display === 'none') {
		tokenDiv.style.display = 'block';
		toggleButton.textContent = '<?php _e( 'Hide Product Details', 'panomity-darkweb-press' ); ?>';
	} else {
		tokenDiv.style.display = 'none';
		toggleButton.textContent = '<?php _e( 'Show Product Details', 'panomity-darkweb-press' ); ?>';
	}
});
</script>

</div>