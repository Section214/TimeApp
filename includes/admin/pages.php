<?php
/**
 * Admin pages
 *
 * @package     TimeApp\Admin\Pages
 * @since       2.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Create the settings menu pages
 *
 * @since       2.0.0
 * @global      string $timeapp_settings_page The TimeApp settings page hook
 * @return      void
 */
function timeapp_add_settings_pages() {
    global $timeapp_settings_page;

    $timeapp_settings_page = add_options_page( __( 'TimeApp Settings', 'timeapp' ), __( 'TimeApp', 'timeapp' ), 'edit_plays', 'timeapp-settings', 'timeapp_render_settings_page' );

    // Remove the media menu item for staff
    if( ! current_user_can( 'manage_options' ) ) {
        remove_menu_page( 'upload.php' );
    }
}
add_action( 'admin_menu', 'timeapp_add_settings_pages', 10 );


/**
 * Determines whether or not the current admin page is a TimeApp page
 *
 * @since       2.0.0
 * @param       string $hook The hook for this page
 * @global      string $typenow The post type we are viewing
 * @global      string $pagenow The page we are viewing
 * @global      string $timeapp_settings_page The TimeApp settings page hook
 * @return      bool $ret True if TimeApp page, false otherwise
 */
function timeapp_is_admin_page( $hook ) {
    global $typenow, $pagenow, $timeapp_settings_page;

    $ret    = false;
    $pages  = apply_filters( 'timeapp_admin_pages', array( $timeapp_settings_page ) );

    if( in_array( $hook, $pages ) ) {
        $ret = true;
    }

    return (bool) apply_filters( 'timeapp_is_admin_page', $ret );
}
