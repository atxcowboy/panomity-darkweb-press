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

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$nonce=isset( $_POST['panomity_update_account_sec'] )
	? sanitize_text_field( $_POST['panomity_update_account_sec'] )
	: '';
	$action='panomity_update_account';

	if ( ! wp_verify_nonce( $nonce, $action ) ) {
		die( 'Security check failed' );
	}
	// Sanitize the input values
	$username=sanitize_text_field( $_POST['panomity_username'] );
	$password=sanitize_text_field( $_POST['panomity_password'] );

	// Save the values as options using update_option()
	update_option( 'panomity_username', $username );
	update_option( 'panomity_password', $password );
} else {
	$username=get_option( 'panomity_username' );
	$password=get_option( 'panomity_password' );
}

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
 * @since 8.1
 *
 * @return void
 */
function register_panomity_darkweb_press_settings() {
	$settings=[
		'panomity_token',
		'panomity_darkweb_check_password_service_id',
		'panomity_darkweb_check_domain_service_id',
		'panomity_darkweb_check_email_service_id',
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
	<h1><?php _e( 'Panomity DarkWeb Press', 'panomity-darkweb-press' ); ?></h1>
	<h2><?php _e( 'Panomity Settings', 'panomity-darkweb-press' ); ?></h2>
	<?php if ( get_option( 'panomity_username' ) ) { ?>
	<div class="notice notice-info is-dismissible">
		<p><?php _e(
	'Activated! Your Panomity account is valid.',
	'panomity-darkweb-press'
); ?></p>
	</div>
	<?php
	} else { ?>
	<div class="notice notice-error">
		<p><?php _e( 'No support.panomity.com account has been provided yet or the provided account is invalid.', 'panomity-darkweb-press' ); ?></p>
	</div>
	<?php } ?>
	<p><?php _e(
		'Enter your Panomity username and password to access the features of your licensed products.',
		'panomity-darkweb-press'
	); ?></p>
	<form method="post" action="">
		<?php wp_nonce_field(
		'panomity_update_account',
		'panomity_update_account_sec'
	); ?>
		<table>
			<tr>
				<th><?php _e( 'Panomity Username', 'panomity-darkweb-press' ); ?></th>
				<td>
					<input type="text" name="panomity_username" size="50" placeholder="<?php
									echo get_option( 'panomity_username' )
										? get_option( 'panomity_username' )
										: __(
											'Enter your support.panomity.com username here',
											'panomity-darkweb-press'
										);
?>" required>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Panomity Password', 'panomity-darkweb-press' ); ?></th>
				<td>
					<input type="password" name="panomity_password" size="50" placeholder="<?php
	echo get_option( 'panomity_password' )
	? __(
		'Enter your support.panomity.com password here to update',
		'panomity-darkweb-press'
	)
		: __(
			'Enter your support.panomity.com password here',
			'panomity-darkweb-press'
		);
?>" required>
				</td>
			</tr>
			<tr>
				<td>
					<div style="padding-top: 10px;">
						<?php submit_button(); ?>
					</div>
				</td>
			</tr>
		</table>
	</form>
	<div class="notice notice-info">
		<p><?php printf(
	wp_kses(
				__( 'For more information about Panomity DarkWeb Press, please visit our <a href="%1$s" target="_blank"><strong>official product page</strong></a>. If you have additional questions, please open a <a href="%2$s" target="_blank"><strong>support request</strong></a>.', 'panomity-darkweb-press' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'class'  => [],
						'rel'	   => [],
						'style'  => [],
					],
					'strong' => [],
				]
			),
	esc_url( 'https://panomity.com/software/panomity-darkweb-press/' ),
	esc_url( 'https://support.panomity.com/tickets/new/' )
); ?></p>
	</div>

	<?php
								$panomity_token=get_option( 'panomity_token' );
?>

	<div id="product_details" style="display:none">
		<table class="widefat">
			<tbody>
				<tr>
					<td><strong><?php echo esc_html__( 'Panomity Token', 'panomity-darkweb-press' ); ?></strong>:</td>
					<td style="word-break: break-all;"><?php echo esc_html( $panomity_token ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>


	<button id="toggle_product_details"><?php esc_html_e( 'Show Debug Details', 'panomity-darkweb-press' ); ?></button>

	<script>
	var toggleButton = document.getElementById('toggle_product_details');
	var tokenDiv = document.getElementById('product_details');

	toggleButton.addEventListener('click', function() {
		if (tokenDiv.style.display === 'none') {
			tokenDiv.style.display = 'block';
			toggleButton.textContent = '<?php _e(
	'Hide Product Details',
	'panomity-darkweb-press'
); ?>';
		} else {
			tokenDiv.style.display = 'none';
			toggleButton.textContent = '<?php _e(
				'Show Product Details',
				'panomity-darkweb-press'
			); ?>';
		}
	});
	</script>

</div>