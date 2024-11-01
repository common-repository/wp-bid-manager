<?php
/*
Plugin Name: WP Bid Manager
Plugin URI: http://wp-bid-manager.com
Description: WordPress bid management system.  Create and manage bids. Then get quotes for those bids by sending them via email from the dashboard.
Version: 1.3.3
Author: WP Bid Manager
Author URI: http://wp-bid-manager.com
License: GPL2
*/

// No direct access allowed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setup constants to be used throught the plugin
define( "BM_SITE_URL", get_bloginfo( "url" ) );
define( "BM_CDBOARD", BM_SITE_URL . '/wp-admin/admin.php?page=bid_manager_dashboard' );
define( "BM_CINFO", BM_SITE_URL . '/wp-admin/admin.php?page=company_information' );
define( "BM_CBID", BM_SITE_URL . '/wp-admin/admin.php?page=new_bid' );
define( "BM_EMAIL_SETTINGS", BM_SITE_URL . '/wp-admin/admin.php?page=bm_email_settings' );
define( "BM_REPORTING", BM_SITE_URL . '/wp-admin/admin.php?page=bm_report' );
define( "BM_BMSETTINGS", BM_SITE_URL . '/wp-admin/admin.php?page=bm_settings' );

define( "PLUGIN_ROOT", plugins_url( '/', __FILE__ ) ); // Plugin root folder

if ( ! defined( "BM_BIDS" ) ) {

	global $table_prefix;

	define( "BM_BIDS", $table_prefix . 'bm_bids' );
	define( "BM_USER", $table_prefix . 'bm_user' );
	define( "BM_RESPONDERS", $table_prefix . 'bm_responder' );
	define( "BM_BIDS_RESPONSES", $table_prefix . 'bm_bids_responses' );
	define( "BM_EMAILS", $table_prefix . 'bm_responder_emails' );
	define( "BM_NOTIFICATIONS", $table_prefix . 'bm_notifications' );
	define( "BM_USERMETA", $table_prefix . 'usermeta' );
	define( "BM_OPTIONS", $table_prefix . 'options' );

}

// Load all the files that are necessary for the plugin
require_once('includes/bm_user.php');
require_once('includes/bm_responder.php');
require_once('includes/reports.php');
require_once('includes/dbtables.php');
require_once('includes/ajax.php');
require_once('includes/shortcodes.php');
require_once('includes/notifications.php');
require_once('includes/classes/bids.class.php');
require_once('includes/classes/membership.class.php');
require_once('includes/classes/settings.class.php');

function bid_manager_menu() {

	add_menu_page( 'Bid Manager', __( 'Bid Manager' ), 'read', 'bid_manager', 'bm_main', 'dashicons-media-text', 3 );
	add_submenu_page( 'bid_manager', 'Dashboard', __( 'Dashboard' ), 'read', 'bid_manager_dashboard', 'bm_dashboard' );
	add_submenu_page( 'bid_manager', 'New Bid', __( 'New Bid' ), 'read', 'new_bid', 'bm_new_bid' );
	add_submenu_page( 'bid_manager', 'Company Information', __( 'Company Information' ), 'read', 'company_information', 'bm_company_info' );
	add_submenu_page( 'bid_manager', 'Reports', __( 'Reports' ), 'read', 'bm_report', 'bm_reports' );
	add_submenu_page( 'bid_manager', 'Emails', __( 'Email Settings' ), 'read', 'bm_email_settings', 'bm_user_email_page' );
	add_submenu_page( 'bid_manager', 'BM Settings', __( 'BM Settings' ), 'read', 'bm_settings', 'bm_settings' );
	add_submenu_page( 'bid_manager', 'Support', __( 'Support' ), 'read', 'bm_support', 'bm_support' );
	// add_submenu_page( 'bid_manager', 'Account Info', __( 'Account Info' ), 'read', 'bm_account_info', 'bm_account_info' );

	add_action( 'admin_init', 'bm_admin_init' );

}

function bm_user_email_page() {

	$settings = new WPBM_Settings();

	// Pull the information from the database if it is there and populate the form for the returning user
	$copy = stripslashes( $settings->get( 'bm_email_content' ) );
	$subject = stripslashes( $settings->get( 'bm_subject_line' ) );
	$from = stripslashes( $settings->get( 'email_from_name' ) );
	$email_from = stripslashes( $settings->get( 'bm_from_line' ) );

	if ( 'email_settings_updated' == $_GET[ 'bm_message' ] ) {
		?>
		<div class="notice notice-success"><p>You have successfully updated the email settings.</p></div>
		<?php
	}

	?>
	<div class="wrap">
		<form id="admin_email_settings" action="" method="post">
			<table id="email_body_editor">
				<tbody>
				<tr>
					<td>
						<h1><?php echo __( 'Email Settings' ); ?></h1>

						<p><?php echo __( 'These settings will allow you to customize the email that is sent to the person you want a
							quote
							from.' ); ?></p>

						<div>
							<label for="email_from_name"><?php echo __( 'From Name' ); ?></label>
							<input id="email_from_name" value="<?php echo $from ?>" name="email_from_name"
								   placeholder="Ex: Your Company"/>

							<p><?php echo __( 'This defaults to the company name if left blank.' ); ?></p>
						</div>
						<div>
							<label for="subject_line"><?php echo __( 'Subject Line' ); ?></label>
							<input id="subject_line" value="<?php echo $subject ?>" name="subject_line"
								   placeholder="Ex: You are receiving this email from..."/>

							<p><?php echo __( 'This defaults to "Invitation for Quote Response" if left blank.' ); ?></p>
						</div>
						<div>
							<label for="email_from">From Email</label>
							<input id="email_from" value="<?php echo $email_from ?>" name="email_from"
								   placeholder="Ex: abc123@gmail.com" type="email" required/>

							<p><?php echo __( 'This defaults to "no-reply@wordpress.org" if left blank.' ); ?></p>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php echo __( 'Email Body' ); ?></p>
					</td>
				</tr>
				<tr>
					<td>
						<?php
						$args = array(
							'media_buttons' => FALSE,
							'textarea_name' => 'bm_email_body_content'
						);
						wp_editor( html_entity_decode( $copy ), 'email_body', $args );

						$bm_settings = get_option( 'bid_manager_settings' );
						$bm_settings = json_decode( $bm_settings );

						if ( $bm_settings->email_smtp_notification == TRUE || $bm_settings->email_footer_notification == TRUE ) {
							?>
							<h2><?php echo _e( 'Configuration notes:' ); ?></h2>
							<?php
						}

						if ( $bm_settings->email_smtp_notification == TRUE ) {
							?>
							<p id="note_wrapper_1" class="bm_note"><span
									style="float: right; padding-left: 50px;"><label
										for="smtp_notification"><?php echo __( 'Dismiss' ); ?></label> <input
										id="smtp_notification" class="hide_notification_checkbox"
										type="checkbox"></span>
								<?php echo __( '&ndash; WordPress does not send
								mail via SMTP by default. For this reason, the email may or may not end up in your spam/junk
								folder. We absolutely recommend this plugin to configure SMTP so your mail does not go to
								anybody\'s spam/junk: <a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank">WP
									Mail SMTP</a>' ); ?></p>
							<?php
						}

						if ( $bm_settings->email_footer_notification == TRUE ) {
							?>
							<p class="bm_note"><span style="float: right; padding-left: 50px;"><label
										for="email_footer"><?php echo __( 'Dismiss' ); ?></label> <input
										id="email_footer" class="hide_notification_checkbox"
										type="checkbox"></span> <?php echo __( '&ndash; The text
								in the box below will appear <strong><em>after</em></strong> your custom message. It is
								system
								text and is mandatory. Feel free to use the customize box above to add anything prior to
								this
								text. In addition, it is always a good idea to test an email to yourself first, before
								sending
								it out.' ); ?></p>
							<?php
						}

						?>

						<p class="bm_example"><em><?php echo __( '"Please follow the link below to sign in and review the quote request.<br><br><a>Click
									here to view and respond</a>."</em>' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
			<input class="button-primary" type="submit" name="bm_admin_email_settings" value="Save &raquo;"/>
		</form>
	</div>
	<?php
}

function bm_admin_init() {
	/**
	 * if this is the right page...
	 */
	$page = (! empty($_GET[ 'page' ])) ? $_GET[ 'page' ] : '';

	process_company_info_save( $page );
	process_email_info_save( $page );
	process_bm_settings( $page );
	process_bm_bid_form( $page );
}

function process_company_info_save($page) {
	if ( 'company_information' !== $page ) {
		return;
	}

	if ( ! isset($_POST[ 'bmuser_company_info' ]) ) {
		return;
	}

	// verify nonce
	// save the user
	bm_update_user_record();

	wp_safe_redirect( admin_url( 'admin.php?page=company_information&bm_message=company_info_saved' ) );
}

function process_email_info_save($page) {
	if ( 'bm_email_settings' !== $page ) {
		return;
	}

	if ( ! isset($_POST[ 'email_from_name' ]) ) {
		return;
	}

	// verify nonce
	bm_user_email_settings();

	wp_safe_redirect( admin_url( 'admin.php?page=bm_email_settings&bm_message=email_settings_updated' ) );

}

function process_bm_settings($page) {
	if ( 'bm_settings' !== $page ) {
		return;
	}

	if ( ! isset($_POST[ 'page_id' ]) ) {
		return;
	}

	// verify nonce
	// save the user
	bm_save_settings();

	wp_safe_redirect( admin_url( 'admin.php?page=bm_settings&bm_message=settings_updated' ) );
}

function process_bm_bid_form($page) {
	if ( 'new_bid' !== $page ) {
		return;
	}

	if ( ! isset($_POST[ 'job_name' ]) ) {
		return;
	}

	bm_save_bid_form_submission();

	wp_safe_redirect( admin_url( 'admin.php?page=bid_manager_dashboard&bm_message=bid_saved' ) );
}

function bm_support() {
	?>
	<div class="wrap">
		<h1><?php echo __( 'Bid Manager Support' ); ?></h1>
		<p><?php echo __( 'We love to help!  And, we love to hear your ideas and feedback.  If you need support or have a suggestion please contact us at <a href="mailto:suppcontractors@gmail.com">suppcontractors@gmail.com</a>' ); ?></p>
		<h2><?php __( 'Shortcode Usage' ); ?></h2>
		<p><?php echo __( 'You can display your bids on your website.  To do so, simply use the shortcode <strong>[bm-bid-display]</strong>' ); ?></p>
		<p><?php echo __( 'From there, you can use a few shortcode attributes to turn on/off bid displays.  For example:' ); ?></p>
		<ul>
			<li>[bm-bid-display]
				- <?php echo __( 'This will show all active bids only.  By default, the shortcode only displays active bids.' ); ?></li>
			<li>[bm-bid-display with_responses="TRUE"]
				- <?php echo __( 'This will display all active bids AND bids with responses.' ); ?></li>
			<li>[bm-bid-display active_bids="FALSE" with_responses="TRUE"]
				- <?php echo __( 'This will NOT show active bids and will only show the bids with responses.' ); ?></li>
			<li>[bm-bid-display past_bids="TRUE"]
				- <?php echo __( 'This will show all active bids and past bids (because active bids was not turned off like above).' ); ?></li>
			<li>[bm-bid-display loggedin="TRUE"]
				- <?php echo __( 'This will display all active bids ONLY for a logged in user of the website.' ); ?></li>
		</ul>
		<h3><?php echo __( 'You can add as many or few shortcode attribues as you would like.  The only one that is set to "TRUE" by default is active bids.  The complete list of attributes are:' ); ?></h3>
		<ul>
			<li><?php echo __( '<strong>loggedin</strong>: default FALSE' ); ?></li>
			<li><?php echo __( '<strong>accepted_bids</strong>:  default FALSE' ); ?></li>
			<li><?php echo __( '<strong>past_bids</strong>: default FALSE' ); ?></li>
			<li><?php echo __( '<strong>with_responses</strong>: default FALSE' ); ?></li>
			<li><?php echo __( '<strong>active_bids</strong>: default TRUE' ); ?></li>
		</ul>
	</div>
	<?php
}

function bm_account_info() {

	$tier = $min_user_role = 'N/A';

	$member = new WPBM_Member();

	if ( 'tier_1' == $member->bm_get_membership_tier() ) {
		$tier = 'Tier 1';
	}

	if ( 'tier_2' == $member->bm_get_membership_tier() ) {
		$tier = 'Tier 2';
	}

	if ( 'tier_3' == $member->bm_get_membership_tier() ) {
		$tier = 'Tier 3';
	}

	$report_ability = ($member->bm_membership_can_run_reports() == TRUE ? 'Enabled' : 'Disabled');

	if ( 0 == $member->bm_get_minimum_user_role() ) {
		$min_user_role = 'Subscriber';
	}

	if ( 1 == $member->bm_get_minimum_user_role() ) {
		$min_user_role = 'Contributor';
	}

	if ( 2 == $member->bm_get_minimum_user_role() ) {
		$min_user_role = 'Author';
	}

	if ( 7 == $member->bm_get_minimum_user_role() ) {
		$min_user_role = 'Editor';
	}

	if ( 10 == $member->bm_get_minimum_user_role() ) {
		$min_user_role = 'Administrator';
	}

	?>
	<h2><?php echo __( 'Membership Details' ); ?></h2>
	<p><?php echo __( 'Membership Level: ' . $tier ); ?></p>
	<p><?php echo __( 'Membership Status: ' . $member->bm_get_membership_status() ); ?></p>
	<p><?php echo __( 'Minimum User Level: ' . $min_user_role ); ?></p>
	<p><?php echo __( 'Reports Capability: ' . $report_ability ); ?></p>
	<?php
}

//  Geo spatial code for finding out radius on addresses for bid requesters and responders

/**
 * Geocode service response
 * @param string $address - e.g. 123 Main St, Denver, CO 80221
 */
function bm_get_lat_and_lng($address) {

	$settings = new WPBM_Settings();

	if ( ! $address ) {
		return FALSE;
	}

	//  Query for the API key
	$key = $settings->get( 'bm_google_api_key' );

	$address = str_replace( " ", "+", urlencode( $address ) );

	// sample URL: https://maps.googleapis.com/maps/api/geocode/json?address=122+Flinders+St,+Darlinghurst,+NSW,+Australia&sensor=false&key=AIzaSyDjtX-Q1FYasO0wcQKqrOktFLghekf9Uns

	$details_url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&sensor=false&key={$key}";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $details_url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$response = json_decode( curl_exec( $ch ), TRUE );

	// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
	if ( $response[ 'status' ] != 'OK' ) {
		return FALSE;
	}

	$geometry = $response[ 'results' ][ 0 ][ 'geometry' ];

	$lat = $geometry[ 'location' ][ 'lat' ];
	$lng = $geometry[ 'location' ][ 'lng' ];

	$array = array(
		'lat'           => $lat,
		'lng'           => $lng,
		'location_type' => $geometry[ 'location_type' ],
	);

	return $array;
}

//  Scramble the file names for bid request/response file uploads and put them on the server
function bm_handle_file_upload($key, $upload_path) {

	$upload_path = rtrim( $upload_path, '/' ) . '/';

	if ( ! isset($_FILES[ $key ]) ) {
		return FALSE;
	}

	$file = $_FILES[ $key ];

	if ( empty($file[ 'name' ]) ) {
		return FALSE;
	}

	// We need the PATH, for moving / saving files
	$base_path = wp_upload_dir();
	$base_url = $base_path[ 'baseurl' ] . '/' . $upload_path;
	$base_path = $base_path[ 'basedir' ] . '/' . $upload_path;

	$pathinfo = pathinfo( $file[ 'name' ] );
	$ext = $pathinfo[ 'extension' ];

	$salt = '1234SomeRandomPatternOfLettersAndNumbers!!!$&#$';

	// Get the name of the file.  But we only care a little, because we want to make it unique / random
	$name = basename( $file[ 'name' ] );

	// Create the random file name
	$name = md5( $name . $salt ) . '.' . $ext;

	// Assign the PATH to move the file to
	$path = $base_path . $name;
	// Set up the URL to view the file
	$url = $base_url . $name;

	if ( move_uploaded_file( $file[ 'tmp_name' ], $path ) ) {
		return $url;
	} else {
		// Move failed. Possible duplicate?
		return "The upload failed.  There is a possibility there could be a duplicate.";
	}

}


// This function ties into the admin_init() to load the necessary javascript and CSS files
function bm_head() {

	/*
	* Styles
	*/
	// Load Styles
	wp_enqueue_style( 'bm-dashboard-style', PLUGIN_ROOT . '/css/style.css' );
	wp_enqueue_style( 'jquery-ui-style', PLUGIN_ROOT . '/css/jquery-ui.css' );

	/*
	 * Scripts
	 */
	// Load Scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'bm-datatables', PLUGIN_ROOT . '/javascript/jquery.dataTables.js' );
	wp_register_script( 'bm-common-scripts', PLUGIN_ROOT . '/javascript/bm.common.js', array('jquery') );
	wp_localize_script( 'bm-common-scripts', 'ajax_object',
		array('ajax_url' => admin_url( 'admin-ajax.php' )) );
	wp_enqueue_script( 'bm-common-scripts' );

	/*
	 * If the bid manager has previously never been assigned any settings lets assign some now
	 */
	$settings = get_option( 'bid_manager_settings' );

	// If they do not have any settings then write the default settings to the database
	if ( ! $settings ) {
		$bm_settings = array(
			'email_smtp_notification'   => TRUE,
			'email_footer_notification' => TRUE
		);
		$bm_settings = json_encode( $bm_settings );
		add_option( 'bid_manager_settings', $bm_settings );
	}

	global $pagenow;
	/* Check current admin page. */
	if ( $pagenow == 'admin.php?page=company_information' ) {
		wp_redirect( admin_url( '/admin.php?page=company_information', 'http' ), 301 );
		exit;
	}
}

add_action( 'admin_enqueue_scripts', 'bm_head' );

add_action( 'wp_ajax_notification_actions', 'bm_hide_notes' );
add_action( 'wp_ajax_nopriv_notification_actions', 'bm_hide_notes' );

// Bid Manager Main screen

function bm_main() {

	$info = BM_CINFO;
	$settings = BM_BMSETTINGS;
	$email_settings = BM_EMAIL_SETTINGS;
	$new_bid = BM_CBID;
	$bm_dashboard = BM_CDBOARD;


	?>
	<div class="wrap">
		<table>
			<tr>
				<td>
					<?php
					sc_show_notifications();
					?>
				</td>
			</tr>
			<tr>
				<td>
					<h1><?php echo __( 'WP Bid Manager' ); ?></h1>

					<p><?php echo __( 'Here is a list of things to do in order to get up and running smoothly.' ); ?></p>
					<ol>
						<li><?php echo __( 'Enter your <a href="' . $info . '">company information</a>. You must enter your info
							to use
							the system. It identifies you and is also necessary when using the quote request option.' ); ?>
						</li>
						<li><?php echo __( 'Manage your <a href="' . $settings . '">settings</a>. Set your page for the
							[bm-invite]
							shortcode to get quote responses and enter your API key for Google Maps.' ); ?>
						</li>
						<li><?php echo __( 'Configure your <a href="' . $email_settings . '">email settings</a>. This allows you
							to
							customize the from, subject line, and the body of the email template for quote requests.' ); ?>
						</li>
						<li><?php echo __( 'Create a <a href="' . $new_bid . '">new bid</a>. You are able to create a real bid
							that you
							would like to manage or send out for a quote.' ); ?>
						</li>
						<li><?php echo __( 'Keep track of all of your bids in <a href="' . $bm_dashboard . '">the bid manager
								dashboard</a>.' ); ?>
						</li>
					</ol>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

// Bid Manager Dashboard

function bm_dashboard() {
	bm_user_dashboard();
}


// Company Information

function bm_company_info() {
	bm_user_info();
}

// New Bid

function bm_new_bid() {
	bm_bid_form();
}

// Reports

function bm_reports() {
	bm_report_controller();
}

// Allows the user to customize their emails to the bid responder

/**
 *
 */
function bm_user_email_settings() {

	$settings = new WPBM_Settings();

	if ( isset($_POST[ 'bm_admin_email_settings' ]) ) {
		$email_subject = sanitize_text_field( $_POST[ 'subject_line' ] );
		$email_from = sanitize_text_field( $_POST[ 'email_from' ] );
		$email_content = $_POST[ 'bm_email_body_content' ];
		$email_from_name = sanitize_text_field( $_POST[ 'email_from_name' ] );

		//  Add the subject line
		$settings->set( 'bm_subject_line', $email_subject );

		//  Add the from line
		$settings->set( 'bm_from_line', $email_from );

		//  Add the email body
		$settings->set( 'bm_email_content', $email_content );

		//  Add the from name line
		$settings->set( 'email_from_name', $email_from_name );

	}
}

function bm_settings() {
	$settings = new WPBM_Settings();

	$content = '';


	$content .= '<div class="wrap">';
	if ( 'settings_updated' == $_GET[ 'bm_message' ] ) {
		$content .= '<div class="notice notice-success"><p>Your settings have been saved.</p></div>';
	}
	$content .= '<h1>' . __( 'Bid Management Settings' ) . '</h1>';
	$content .= '<h2>' . __( 'Invitation Details Page' ) . '</h2>';
	$content .= '<p>' . __( 'Put this shortcode on the page you select below:' ) . ' <strong>[bm-invite]</strong></p>';
	$content .= '<p>' . __( 'If you don\'t put this shortcode on the page you select, you will not have a page for the bid information to be displayed for whoever you want to get a response from.  This <strong>MUST</strong> be done in order to send email requests to people for quote responses.' ) . '</p>';

	$link_id = $settings->get( 'bm_invite_page' );

	$defaults = array(
		'depth'                 => 0,
		'child_of'              => 0,
		'selected'              => $link_id,
		'echo'                  => 0,
		'name'                  => 'page_id',
		'id'                    => NULL, // string
		'class'                 => NULL, // string
		'show_option_none'      => 'Please select a page', // string
		'show_option_no_change' => NULL, // string
		'option_none_value'     => NULL, // string
	);

	$content .= '
    <form id="invite_scode_page" method="post" action="">

    <p>' . wp_dropdown_pages( $defaults ) . '</p>
    <h2>' . __( 'Google Maps API Key' ) . '</h2>';

	$key = $settings->get( 'bm_google_api_key' );
	$allow_response_actions = $settings->get( 'bm_bid_response_actions' );

	if ( ! $allow_response_actions ) {
		$settings->set( 'bm_bid_response_actions', 0 );
	}

	$allow_response_actions = $settings->get( 'bm_bid_response_actions' );

	$bid_response_actions_yes = $bid_response_actions_no = '';
	if ( $allow_response_actions == 1 ) {
		$bid_response_actions_yes = 'checked';
	}

	if ( $allow_response_actions == 0 ) {
		$bid_response_actions_no = 'checked';
	}


	$content .= '<p><input id="google_maps_api" class="" type="text" value="' . $key . '" name="google_maps_api" size="50"></p>
                    <p>This will put a Google Map with your bids pinned to it at the bottom of your <a href="' . BM_CINFO . '">company information page</a>.</p>
                    <p>If you need help getting a Google Maps API key, you can <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key?hl=en">get started here</a>.</p>
                    <div class="switch-field">
                    <h3 class="switch-title">Allow Bid Response View Actions on Front</h3>
                    <p>If selected, this will allow visitors/users on the front end to take action on bids with responses.  It is advised to keep this set to "No" unless you know what you are doing.</p>
                    <input id="bid_actions_yes" type="radio" name="bid_response_actions" value="1" ' . $bid_response_actions_yes . '>
                    <label for="bid_actions_yes">Yes</label>
                    <input id="bid_actions_no" type="radio" name="bid_response_actions" value="0" ' . $bid_response_actions_no . '>
                    <label for="bid_actions_no">No</label>
                    </div>

                    <p><input class="button-primary" type="submit" value="Submit &raquo;" name="bm_settings_save" /></p>
                    </form></div>';

	echo $content;


}

function bm_save_settings() {

	$settings = new WPBM_Settings();

	if ( isset($_POST[ 'bm_settings_save' ]) ) {

		// Write the permalink ID to the database if page selected
		if ( $_POST[ 'page_id' ] ) {
			$settings->set( 'bm_invite_page', $_POST[ 'page_id' ] );
		}


		if ( empty($_POST[ 'google_maps_api' ]) ) {
			$_POST[ 'google_maps_api' ] = '';
		}

		$settings->set( 'bm_google_api_key', $_POST[ 'google_maps_api' ] );

		if ( isset($_POST[ 'bid_response_actions' ]) ) {
			$settings->set( 'bm_bid_response_actions', $_POST[ 'bid_response_actions' ] );
		}

	}
}


function bm_activate() {

	// Make the directories
	$upload = wp_upload_dir();
	$upload_dir = $upload[ 'basedir' ];
	$upload_dir = $upload_dir . '/bid_requests';  // Bid requests file folder
	if ( ! is_dir( $upload_dir ) ) {
		mkdir( $upload_dir, 0700 );
	}


	$upload = wp_upload_dir();
	$upload_dir = $upload[ 'basedir' ];
	$upload_dir = $upload_dir . '/bid_responses';  //  Bid responses file folder
	if ( ! is_dir( $upload_dir ) ) {
		mkdir( $upload_dir, 0700 );
	}

	//Check to see if we need to write the tables to the DB
	bm_user_check();
	bm_email_check();
	bm_responder_check();
	bm_bids_check();
	bm_responses_check();
	bm_notifications_check();

	// This injects the notification to the bm_notifications table
	bm_notice_injection();  // Turn this on if there is a notification to be run in the notifications.php file

}

register_activation_hook( __FILE__, 'bm_activate' );

function bm_plugin_version() {
	require_once('includes/updates.php');

	/*
	 * We initially want to setup a version number.  Either inserting it or updating it.  Then we can do some checks against it.
	 *
	 */

	$current_version = (float)1.22;  //  Set my version # - previous was 1.21
	$option = 'bm_plugin_version';

	//  Find if the option exists
	$db_version = (float)get_option( $option );

	if ( ! $db_version ) { // If the option does not exist, run all the updates and write it
		bm_update_02162016();
		bm_update_08112017();
		bm_update_08312017();
		bm_update_09132017();
		add_option( $option, $current_version );
	}

	if ( $db_version < 1.20 ) {
		bm_update_02162016();
		bm_update_08112017();
		update_option( $option, $current_version );
	}

	if ( $db_version < 1.21 ) {
		bm_update_08312017();
		update_option( $option, $current_version );
	}

	if ( $db_version < $current_version ) {
		bm_update_09132017();
		update_option( $option, $current_version );
	}

}

register_deactivation_hook( __FILE__, 'bm_uninstall_process' );
function bm_uninstall_process() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$today = date( "Y-m-d H:i:s" );
	$expire = '2017-11-27 16:01:55';
	$send_survey = TRUE;
	if ( $today > $expire ) {
		$send_survey = FALSE;
	}

	if ( $send_survey == TRUE ) {

		$plugin = isset($_REQUEST[ 'plugin' ]) ? $_REQUEST[ 'plugin' ] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );

		# Uncomment the following line to see the function in action
//	exit( var_dump( $_GET ) );

		$user = wp_get_current_user();

		$user_email = $user->data->user_email;

		$link = 'http://wp-bid-manager.com/wp-bid-manager-plugin-uninstall-survey/?user_email=' . $user_email;

		$message = <<<EMAIL
	<p>We are sorry to see you go.  We hope that you may be interested in taking a brief, 60 second survey, to help us better the product.  In return, we will put you into a monthly drawing to receive a $50 Visa gift card.</p>
	<p>Simply <a href="{$link}">Follow this link</a> to our survey page.  <strong>It's that easy!</strong></p>
	<p>Thank you,<br>WP Bid Manager Team</p>
EMAIL;


		// $from = 'From: WP Bid Manager <no-reply@mrwpress.com>';

		$headers = array(
			'Content-Type: text/html; charset=UTF-8'
			// $from
			// 'Cc: somebody@rezdox.com'
		);
		// https://blog.jetbrains.com/idea/2008/01/using-local-history-to-restore-deleted-files/ - Delete the damn plugin and don't have a backup???....

		wp_mail( $user_email, 'WP Bid Manager: We are sorry to see you go', $message, $headers );
	}


}

/**
 * Turn this back on when ready to start charging for the plugin
 */

//function bm_initialize() {
//	$settings = new WPBM_Settings();
//	$is_paid = $settings->get('membership_level');
//
//	if ( $is_paid == NULL ) {
//
//		$settings->set('membership_level', 'tier_1');
//		$settings->set('membership_status', 'active');
//		$settings->set('num_allowed_users', 10);
//		$settings->set('report_capabilities', TRUE);
//		$settings->set('minimum_user_level', 10);
//
//	}
//}
//
//add_action( 'admin_init', 'bm_initialize' );

/*
 * The following is where we tie into actions/hooks/filters, etc to harness WordPress native functionality
 */

//  Creates the admin menu on the left hand navigation
add_action( 'admin_menu', 'bid_manager_menu' );

// Tap into the admin_init() so we can enque/register/deregister any styles or scripts
add_action( 'admin_init', 'bm_head' );
add_action( 'admin_init', 'bm_plugin_version' );
