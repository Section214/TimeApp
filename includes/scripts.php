<?php
/**
 * Scripts
 *
 * @package     TimeApp\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_admin_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    wp_enqueue_style( 'timeapp', TIMEAPP_URL . 'assets/css/admin' . $suffix . '.css', array(), TIMEAPP_VER );
}
add_action( 'admin_enqueue_scripts', 'timeapp_admin_scripts' );


/**
 * Load login scripts
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_login_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    wp_enqueue_style( 'timeapp-login', TIMEAPP_URL . 'assets/css/login' . $suffix . '.css', array(), TIMEAPP_VER );
    wp_enqueue_script( 'timeapp-login', TIMEAPP_URL . 'assets/js/login' . $suffix . '.js', array( 'jquery' ), TIMEAPP_VER );
}
add_action( 'login_enqueue_scripts', 'timeapp_login_scripts' );
