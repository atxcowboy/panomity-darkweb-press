<?php
/**
 * Plugin Name: Email List Admin Page
 * Description: A page to display a list of email addresses registered on the site in the WordPress admin backend and check them against the darkweb leak database.
 * Version: 8.1
 * Author: Sascha Endlicher, M.A.
 */
function query_darkweb_leaks( $emails ) {
	// Sanitize the email addresses in the $emails array
	$emails=array_map( 'sanitize_email', $emails );
	// Get the current user's information
	$current_user=wp_get_current_user();
	$site_name=get_bloginfo( 'name' );

	// Set up the email subject and message
	$subject=__( 'Dark Web Check Notification', 'panomity-darkweb-press' );
	$message_template=__( 'Hello %s,', 'panomity-darkweb-press' ) . "\r\n\r\n" .
						__( 'This is to notify you that %s (%s) has triggered a dark web check on your behalf. You should receive an email soon from Panomity GmbH containing a link to a page where you can view the results.', 'panomity-darkweb-press' ) . "\r\n\r\n" .
						__( "Please be advised that email addresses provided for the check will be automatically deleted from Panomity's database after 30 days.", 'panomity-darkweb-press' ) . "\r\n\r\n" .
						__( 'Thank you,', 'panomity-darkweb-press' ) . "\r\n" .
						esc_html( $site_name );

	// Set up the email headers
	$headers=[
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', esc_html( $site_name ), sanitize_email( get_option( 'admin_email' ) ) ),
	];

	// Send the emails
	$num_emails=count( $emails );

	for ( $i=0; $i < $num_emails; $i++ ) {
		$email=$emails[ $i ];

		$message=sprintf(
			$message_template,
			esc_html( $email ),
			esc_html( $current_user->display_name ),
			esc_html( $current_user->user_email )
		);

		wp_mail( $email, $subject, $message, $headers );
	}

	// Set up the remote curl call
	$panomity_token=get_option( 'panomity_token' );
	$service_id=Panomity_Darkweb_Press_Admin::get_panomity_darkweb_press_service_id( $panomity_token, 'bulkcheck' );

	$post_data=[
		'accounts' => $emails,
	];
	$post_data_json=json_encode( $post_data );

	$request_args=[
		'method'  => 'POST',
		'headers' => [
			'Authorization' => 'Bearer ' . $panomity_token,
			'Content-Type'  => 'application/json',
		],
		'body' => $post_data_json,
	];

	// Make the remote curl call
	$result=wp_remote_request( 'https://support.panomity.com/api/darkweb/' . $service_id . '/bulkcheck_accounts', $request_args );
	//print_r(  $result  );

	return true;
}

// Add the jQuery UI Dialog library
function my_enqueue_scripts() {
	wp_enqueue_script( 'jquery-ui-dialog' );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue_scripts' );

$service_types=['bulkcheck'];
Panomity_Darkweb_Press_Admin::register_and_check_services( $service_types );

// Add the JavaScript function to handle the form submission
function my_submit_form() {
	?>
<script>
jQuery(document).ready(function($) {
	$("#bulk-email-form").submit(function(event) {
		if (!confirm( <?php echo wp_json_encode( esc_html__( 'Before proceeding, please note that by submitting this form, you declare that you have the authorization to share the provided email addresses with Panomity GmbH for a dark web check.\n\nPlease be advised that upon submission, an email notification will be sent from this server to inform your user that the check has been triggered. Following this, Panomity will send an email containing a link to a page where the user can view the results. Please note that email addresses provided for the check will be automatically deleted from Panomity\'s database after 30 days.', 'panomity-darkweb-press' ) ); ?> )) {
			event.preventDefault();
		}
	});
});
</script>
<?php
}
add_action( 'admin_footer', 'my_submit_form' );

if ( isset( $_POST['bulk_action'] ) ) {
	$bulk_action=sanitize_text_field( wp_unslash( $_POST['bulk_action'] ) );

	if ( $bulk_action == 'darkweb' ) {
		$selected_emails=isset( $_POST['email'] ) ? array_map( 'sanitize_email', wp_unslash( $_POST['email'] ) ) : [];

		if ( !empty( $selected_emails ) ) {
			// Query the dark web for each email and display the results
			$num_emails=count( $selected_emails );
			$batch_size=100;

			for ( $i=0; $i < $num_emails; $i += $batch_size ) {
				$batch_emails=array_slice( $selected_emails, $i, $batch_size );
				query_darkweb_leaks( $batch_emails );
			}

			// Output success message
			foreach ( $selected_emails as $email ) {
				echo '<p>' . esc_html( sprintf( __( 'Message sent to %s.', 'panomity-darkweb-press' ), sanitize_email( $email ) ) ) . '</p>';
			}
		} else {
			// No emails selected, display an error message
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Please select at least one email.', 'panomity-darkweb-press' ) . '</p></div>';
		}
	}
}

// Query the database for email addresses
$search=isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
$users=get_users( [
	'search' => '*' . $search . '*',
] );
$emails=[];

foreach ( $users as $user ) {
	$email=$user->user_email;

	if ( !empty( $email ) ) {
		$emails[]=$email;
	}
}
// Determine the current page
$current_page=isset( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : 1;
$current_page=absint( $current_page );
$per_page=100;
$total_emails=count( $emails );
$total_pages=ceil( $total_emails / $per_page );
$start_index=( $current_page - 1 ) * $per_page;
$paginated_emails=array_slice( $emails, $start_index, $per_page );

// Display the email list table
echo '<div class="wrap"><h1>' . esc_html( __( 'Email List', 'panomity-darkweb-press' ) ) . '</h1>';
?>
<h2><?php esc_html_e( 'Bulk Email Check', 'panomity-darkweb-press' ); ?></h2>
<div style="display: flex; margin-bottom: 20px;">
	<div style="flex: 0 0 40%; background-color: white; margin-right: 10px; border: 1px solid #ccc; padding: 10px;">
		<p style="height: 100%;"><?php esc_html_e( 'This page displays a list of all email addresses registered with your website. You can use this tool to check for data breaches. However, please ensure that you have notified and obtained authorization from your users before checking their email addresses.', 'panomity-darkweb-press' ); ?></p>
	</div>
	<div style="flex: 1; background-color: white; margin-right: 10px; border: 1px solid #ccc; padding: 10px;">
		<p style="height: 100%;"><?php esc_html_e( 'To perform bulk actions, select the email addresses you want to take action on and choose the corresponding action from the "Bulk Actions" dropdown. Then, click on the "Apply" button to execute the action.', 'panomity-darkweb-press' ); ?></p>
	</div>
</div>
<div style="display: flex; margin-bottom: 10px;">
	<div style="flex: 0 0 40%; background-color: white; margin-right: 10px; border: 1px solid #ccc; padding: 10px;">
		<p style="height: 100%;"><?php esc_html_e( 'You can also search for specific email addresses using the search box located above the email list. Additionally, you can navigate through different pages of the email list using the pagination links at the bottom of the page.', 'panomity-darkweb-press' ); ?></p>
	</div>
	<div style="flex: 1; background-color: white; border: 1px solid #ccc; padding: 10px;">
		<p style="height: 100%;"><?php esc_html_e( 'Once you check an email for a data breach, Panomity GmbH will send an email from gatekeeper@panomity.com to the email address with a link so that the account holder can check if their email address has been breached on the dark web.', 'panomity-darkweb-press' ); ?></p>
	</div>
</div>
<form method="get">
	<input type="hidden" name="page" value="panomity-darkweb-press-emails">
	<p class="search-box">
		<label class="screen-reader-text" for="email-list-search"><?php esc_html_e( 'Search Email List', 'panomity-darkweb-press' ); ?></label>
		<input type="search" id="email-list-search" name="s" value="<?php echo esc_attr( $search ); ?>">
		<input type="submit" name="search" id="email-list-search-submit" class="button" value="<?php esc_attr_e( 'Search', 'panomity-darkweb-press' ); ?>">
	</p>
</form>

<form method="post" id="bulk-email-form">
	<table class="wp-list-table widefat striped" style="margin-top: 10px;">
		<thead>
			<tr>
				<th class="manage-column check-column"><input type="checkbox" id="check-all"></th>
				<th class="manage-column"><?php esc_html_e( 'Email', 'panomity-darkweb-press' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $row_class='';

foreach ( $paginated_emails as $email ) {
	$row_class=( $row_class == 'alternate' ) ? '' : 'alternate'; ?>
			<tr class="<?php echo esc_attr( $row_class ); ?>">
				<td><input type="checkbox" name="email[]" value="<?php echo esc_attr( $email ); ?>"></td>
				<td><?php echo esc_html( $email ); ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th class="manage-column check-column"><input type="checkbox" id="check-all-bottom"></th>
				<th class="manage-column"><?php esc_html_e( 'Email', 'panomity-darkweb-press' ); ?></th>
			</tr>
		</tfoot>
	</table>
	<div class="tablenav" style="margin-top: 10px;">
		<div class="alignleft actions">
			<select name="bulk_action">
				<option value=""><?php esc_html_e( 'Bulk Actions', 'panomity-darkweb-press' ); ?></option>
				<option value="darkweb"><?php esc_html_e( 'Check for Data Breaches', 'panomity-darkweb-press' ); ?></option>
			</select>
			<input type="submit" name="submit" id="doaction" class="button action" value="<?php esc_attr_e( 'Apply', 'panomity-darkweb-press' ); ?>">
		</div>
		<div class="tablenav-pages">
			<?php echo paginate_links( [
			'total'			   => $total_pages,
			'current'	   => $current_page,
			'prev_text'	 => esc_html__( '« Previous', 'panomity-darkweb-press' ),
			'next_text'	 => esc_html__( 'Next »', 'panomity-darkweb-press' ),
			'base'				   => esc_url( add_query_arg( [ 'paged' => '%#%' ], admin_url( 'admin.php?page=' . sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) ) ),
		] ); ?>
		</div>
	</div>
</form>

<script>
// Select all checkboxes when the "check-all" checkbox is clicked at the top or bottom of the table
jQuery(document).ready(function($) {
	$('#check-all, #check-all-bottom').click(function(event) {
		if (this.checked) {
			$(':checkbox').each(function() {
				this.checked = true;
			});
		} else {
			$(':checkbox').each(function() {
				this.checked = false;
			});
		}
	});
});
</script>
</div>