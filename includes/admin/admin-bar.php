<?php
/**
 * Admin Bar functions
 *
 * @package     TimeApp\Admin\AdminBar
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Cleanup the default admin bar
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_cleanup_admin_bar( $wp_admin_bar ) {
    if( ! current_user_can( 'manage_options' ) ) {
        $wp_admin_bar->remove_node( 'wp-logo' );
        $wp_admin_bar->remove_node( 'site-name' );
        $wp_admin_bar->remove_node( 'updates' );
        $wp_admin_bar->remove_node( 'comments' );

        $wp_admin_bar->remove_menu( 'new-page' );
        $wp_admin_bar->remove_menu( 'new-post' );
        $wp_admin_bar->remove_menu( 'new-media' );
    }
}
add_action( 'admin_bar_menu', 'timeapp_cleanup_admin_bar', 999 );


/**
 * Add new icon to admin bar
 *
 * @since       1.0.0
 * @param       object $wp_admin_bar The Admin Bar object
 * @return      void
 */
function timeapp_admin_bar_icon( $wp_admin_bar ) {
    if( ! current_user_can( 'manage_options' ) ) {
        $wp_admin_bar->add_node( array(
            'id'    => 'timeapp-logo',
            'href'  => admin_url(),
            'title' => '<span class="timeapp-icon-time"></span>',
            'meta'  => array(
                'class' => 'timeapp-logo',
                'title' => __( 'TimeApp', 'timeapp' )
            )
        ) );
    }
}
add_action( 'admin_bar_menu', 'timeapp_admin_bar_icon' );
