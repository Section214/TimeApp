<?php
/**
 * Admin actions
 *
 * @package     TimeApp\Admin\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Process all actions sent via POST and GET by looking for
 * the 'timeapp-action' request and running do_action() to
 * call the function.
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_process_actions() {
	if ( isset( $_POST['timeapp-action'] ) ) {
		do_action( 'timeapp_' . $_POST['timeapp-action'], $_POST );
	}

	if ( isset( $_GET['timeapp-action'] ) ) {
		do_action( 'timeapp_' . $_GET['timeapp-action'], $_GET );
	}
}
add_action( 'admin_init', 'timeapp_process_actions' );


/**
 * Register this site for plugin updates
 *
 * @since       2.0.4
 * @return      void
 */
function timeapp_register_site() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$license = get_option( 'timeapp_license', false );

	if ( $license != 'valid' ) {
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => 'b76ae062e3cd62424479cafed2000529',
			'item_name'  => 'TimeApp',
			'url'        => home_url()
		);

		// Call the API
		$response = wp_remote_post( 'http://ingroupconsulting.com', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'timeapp_license', $license_data->license );
		delete_transient( 'timeapp_license' );
	}
}


/**
 * Rewrite the 'Powered by WordPress' footer text
 *
 * @since       1.0.0
 * @param       string $footer_text The existing footer text
 * @return      string $footer_text The updated footer text
 */
function timeapp_footer_text( $footer_text ) {
	$footer_text = sprintf( __( 'Copyright &copy; 2014%s Time Music Agency &middot; All Rights Reserved' ), ( date( 'Y' ) > 2014 ? '-' . date( 'Y' ) : '' ) );

	return $footer_text;
}
add_filter( 'admin_footer_text', 'timeapp_footer_text' );


/**
 * Change WordPress version from footer
 *
 * @since       2.2.0
 * @return      void
 */
function timeapp_set_footer_version( $version_text ) {
	return sprintf( __( 'Version %s', 'timeapp' ), TIMEAPP_VER );
}
add_action( 'update_footer', 'timeapp_set_footer_version', 999 );


/**
 * Allow updating meta through the dashboard
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_update_meta() {
	// Don't process if nonce can't be verified
	if ( ! wp_verify_nonce( $_GET['update-nonce'], 'update-meta' ) ) {
		return;
	}

	// Don't process if the current user shouldn't be editing this
	if ( ! isset( $_GET['type'] ) || ! isset( $_GET['id'] ) || ! current_user_can( 'edit_' . $_GET['type'], $_GET['id'] ) ) {
		return;
	}

	// Don't process if no key or value is passed
	if ( ! isset( $_GET['key'] ) || ! isset( $_GET['value'] ) ) {
		return;
	}

	update_post_meta( $_GET['id'], $_GET['key'], $_GET['value'] );
}
add_action( 'timeapp_update_meta', 'timeapp_update_meta' );


/**
 * Generate and email contracts
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_generate_pdf() {
	// Don't process if nonce can't be verified
	if ( ! wp_verify_nonce( $_GET['pdf-nonce'], 'generate-pdf' ) ) {
		return;
	}

	// Include the generator class
	require_once TIMEAPP_DIR . 'includes/class.pdf-generator.php';

	// Setup cache
	$wp_upload_dir = wp_upload_dir();
	$cache_dir     = $wp_upload_dir['basedir'] . '/timeapp-cache/';

	// Ensure that the cache directory is protected
	if ( get_transient( 'timeapp_check_protection_files' ) === false ) {
		wp_mkdir_p( $cache_dir );

		// Top level blank index.php
		if ( ! file_exists( $cache_dir . 'index.php' ) ) {
			@file_put_contents( $cache_dir . 'index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
		}

		// Top level .htaccess
		$rules = "Options -Indexes";
		if ( file_exists( $cache_dir . '.htaccess' ) ) {
			$contents = @file_get_contents( $cache_dir . '.htaccess' );

			if ( $contents !== $rules || ! $contents ) {
				@file_put_contents( $cache_dir . '.htaccess', $rules );
			}
		} else {
			@file_put_contents( $cache_dir . '.htaccess', $rules );
		}

		// Check daily
		set_transient( 'timeapp_check_protection_files', true, 3600 * 24 );
	}

	$play      = get_post( $_GET['post'] );
	$artist    = get_post_meta( $play->ID, '_timeapp_artist', true );
	$artist    = get_post( $artist );
	$purchaser = get_post_meta( $play->ID, '_timeapp_purchaser', true );
	$purchaser = get_post( $purchaser );
	$date      = get_post_meta( $play->ID, '_timeapp_start_date', true );
	$date      = date( 'm-d-Y', strtotime( $date ) );
	$filename  = strtolower( $artist->post_title ) . '-' . $date . '.pdf';
	$filename  = str_replace( ' ', '', $filename );

	// We don't store contracts!
	if ( file_exists( $cache_dir . $filename ) ) {
		unlink( $cache_dir . $filename );
	}

	$file = new TimeApp_Generate_PDF( $cache_dir . $filename, $_GET['post'] );
	$file->build();

	// Tag as sent
	$contract_log = get_post_meta( $play->ID, '_timeapp_contract_sent', true );

	if ( ! is_array( $contract_log ) ) {
		$new_log      = array();
		$new_log[]    = $contract_log;
		$contract_log = $new_log;
	}

	$contract_log[] = current_time( 'm/d/Y g:i a' );
	update_post_meta( $play->ID, '_timeapp_contract_sent', $contract_log );

	if ( timeapp()->settings->get_option( 'enable_debugging', false ) ) {
		$url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $cache_dir . $filename );
		wp_safe_redirect( $url );
		exit;
	}

	// Get the email type
	$status = get_post_meta( $play->ID, '_timeapp_status', true );

	// Get rider
	$rider_url = get_post_meta( $artist->ID, '_timeapp_rider', true );
	$email     = get_post_meta( $purchaser->ID, '_timeapp_email', true );
	$cc_email  = get_post_meta( $artist->ID, '_timeapp_artist_email', true );

	// Send the email!
	$to[] = $email;

	// Maybe append purchaser CC emails
	$purchaser_cc = get_post_meta( $purchaser->ID, '_timeapp_additional_emails', true );
	$purchaser_cc = $purchaser_cc ? $purchaser_cc : array();
	$play_cc      = get_post_meta( $play->ID, '_timeapp_play_emails', true );
	$play_cc      = $play_cc ? $play_cc : array();

	$to = array_merge( $to, $purchaser_cc, $play_cc );

	if ( $status == 'cancelled' ) {
		$subject = timeapp()->settings->get_option( 'cancelled_email_subject', __( 'Time Music Agency Contract - Cancellation Notice', 'timeapp' ) );
		$message = timeapp()->settings->get_option( 'cancelled_email_content', timeapp_get_cancelled_email_content() );
	} else {
		$subject = timeapp()->settings->get_option( 'booking_email_subject', sprintf( __( 'Time Music Agency Contract - %1$s %2$s', 'timeapp' ), '{artist_name}', '{start_date}' ) );
		$message = timeapp()->settings->get_option( 'booking_email_content', timeapp_get_booking_email_content() );
	}
	$subject   = timeapp_do_tags( $subject, $play->ID );
	$message   = timeapp_do_tags( $message, $play->ID );
	$headers[] = 'From: ' . timeapp()->settings->get_option( 'email_from_name', 'Time Music Agency, Inc' ) . ' <' . timeapp()->settings->get_option( 'email_from_address', 'contracts@timemusicagency.com' ) . '>';

	// Global CCs
	$cc_emails = timeapp()->settings->get_option( 'email_cc_addresses', false );

	if ( $cc_emails ) {
		$cc_emails = array_map( 'trim', explode( "\n", $cc_emails ) );
		$cc_emails = array_unique( $cc_emails );
		$cc_emails = array_map( 'sanitize_text_field', $cc_emails );

		foreach ( $cc_emails as $email_address ) {
			$headers[] = 'Cc: ' . $email_address;
		}
	}

	if ( $cc_email ) {
		$headers[] = 'Cc: ' . $cc_email;
	}

	$attachments = array(
		$cache_dir . $filename
	);

	if ( $status != 'cancelled' ) {
		if ( $rider_url ) {
			$rider_url     = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $rider_url );
			$attachments[] = $rider_url;
		}
	}

	wp_mail( $to, $subject, $message, $headers, $attachments );

	wp_safe_redirect( add_query_arg( array( 'timeapp-action' => null, 'pdf-nonce' => null ) ) );
	exit;
}
add_action( 'timeapp_generate_pdf', 'timeapp_generate_pdf' );


/**
 * Download rider
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_download_rider() {
	$rider_url  = get_post_meta( $_GET['post'], '_timeapp_rider', true );
	$rider_name = basename( $rider_url );

	nocache_headers();
	header( 'Robots: none' );

	if ( wp_is_mobile() ) {
		header( 'Content-Type: application/octet-stream' );
	} else {
		header( 'Content-Type: application/force-download' );
	}
	header( 'Content-Disposition: attachment; filename="' . $rider_name . '"' );
	header( 'Content-Transfer-Encoding: Binary' );

	readfile( $rider_url );

	wp_safe_redirect( add_query_arg( array( 'timeapp-action' => null ) ) );
	exit;
}
add_action( 'timeapp_download_rider', 'timeapp_download_rider' );


/**
 * Remove rider
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_remove_rider() {
	delete_post_meta( $_GET['post'], '_timeapp_rider' );

	wp_safe_redirect( add_query_arg( array( 'timeapp-action' => null ) ) );
	exit;
}
add_action( 'timeapp_remove_rider', 'timeapp_remove_rider' );


/**
 * Override post title on play post type
 *
 * @since       1.0.7
 * @param       string $post_title The current post title
 * @return      string $post_title The new post title
 */
function timeapp_update_play_title( $post_title ) {
	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'play' ) {
		$artist    = get_post( $_POST['_timeapp_artist'] );
		$artist    = $artist->post_title;
		$purchaser = get_post( $_POST['_timeapp_purchaser'] );
		$purchaser = $purchaser->post_title;
		$date      = $_POST['_timeapp_start_date'];
		$date      = date( 'm-d-Y', strtotime( $date ) );

		$post_title = $artist . '@' . $purchaser . ' - ' . $date;
	}

	return $post_title;
}
add_filter( 'title_save_pre', 'timeapp_update_play_title' );


function timeapp_debug_bar() {
	if ( timeapp()->desktop && timeapp()->settings->get_option( 'enable_debugging', false ) ) {
		echo '<div class="timeapp-debugging-enabled">' . __( 'Debugging<br />Enabled', 'timeapp' ) . '</div>';
	}
}
add_action( 'admin_menu', 'timeapp_debug_bar', 999 );
