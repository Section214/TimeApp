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
    $suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    $ui_style   = ( get_user_option( 'admin_color' ) == 'classic' ) ? 'classic' : 'fresh';
    
    wp_enqueue_style( 'timeapp-select2', TIMEAPP_URL . 'assets/css/select2' . $suffix . '.css' );
    wp_enqueue_script( 'timeapp-select2', TIMEAPP_URL . 'assets/js/select2' . $suffix . '.js', array( 'jquery' ) );

    wp_enqueue_style( 'jquery-ui-css', TIMEAPP_URL . 'assets/css/jquery-ui-' . $ui_style . $suffix . '.css' );

    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-slider' );
    wp_enqueue_script( 'timeapp-timepicker', TIMEAPP_URL . 'assets/js/jquery-ui-timepicker-addon' . $suffix . '.js', array( 'jquery-ui-datepicker', 'jquery-ui-slider' ) );

    wp_enqueue_style( 'timeapp', TIMEAPP_URL . 'assets/css/admin' . $suffix . '.css', array(), TIMEAPP_VER );
    wp_enqueue_script( 'timeapp', TIMEAPP_URL . 'assets/js/admin' . $suffix . '.js', array( 'jquery' ), TIMEAPP_VER );
    wp_localize_script( 'timeapp', 'timeapp_vars', array(
        'required_fields'       => __( 'Please enter all required fields!', 'timeapp' ),
        'select_agent'          => __( 'Select an Agent', 'timeapp' ),
        'select_purchaser'      => __( 'Select a Purchaser', 'timeapp' ),
        'close_button'          => __( 'Exit Preview', 'timeapp' ),
        'title_placeholder'     => __( 'Play title will be generated on save', 'timeapp' )
    ) );

    wp_enqueue_style( 'colorbox', TIMEAPP_URL . 'assets/css/colorbox' . $suffix . '.css' );
    wp_enqueue_script( 'colorbox', TIMEAPP_URL . 'assets/js/jquery.colorbox' . $suffix . '.js', array( 'jquery' ) );

    wp_enqueue_style( 'thickbox' );
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_script( 'media-upload' );
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
