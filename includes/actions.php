<?php
/**
 * Actions
 *
 * @package     TimeApp\Actions
 * @since       2.2.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Disable the site frontend
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_disable_frontend() {
	if ( ! is_admin() && $GLOBALS['pagenow'] != 'wp-login.php' ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'template_redirect', 'timeapp_disable_frontend' );


/**
 * Change login redirect to point to dashboard
 *
 * @since       1.0.0
 * @return      string New URL to redirect to
 */
function timeapp_default_page( $redirect_to ) {
	return admin_url( 'index.php' );
}
add_filter( 'login_redirect', 'timeapp_default_page' );


/**
 * Change the login image URL
 *
 * @since       1.1.0
 * @return      null
 */
function timeapp_login_header_url() {
	return null;
}
add_filter( 'login_headerurl', 'timeapp_login_header_url' );


/**
 * Change the login image alt text
 *
 * @since       1.1.0
 * @return      null
 */
function timeapp_login_header_title() {
	return null;
}
add_filter( 'login_headertitle', 'timeapp_login_header_title' );
