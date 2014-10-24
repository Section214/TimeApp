<?php
/**
 * Dashboard functions
 *
 * @package     TimeApp\Admin\Dashboard
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Remove builtin WordPress help tabs
 *
 * @since       1.0.0
 * @param       string $contextual_help The content we are replacing
 * @param       string $screen_id The ID of the current screen
 * @param       object $screen The WordPress object for this screen
 * @return      string $contextual_help The updated content
 */
function timeapp_remove_contextual_help( $contextual_help, $screen_id, $screen ) {
    $screen->remove_help_tabs();

    return $contextual_help;
}
add_filter( 'contextual_help', 'timeapp_remove_contextual_help', 999, 3 );


/**
 * Remove screen options from the dashboard
 *
 * @since       1.0.0
 * @param       bool $show_screen True if visible, false otherwise
 * @param       object $screen The WordPress object for this screen
 * @return      bool
 */
function timeapp_remove_screen_options( $show_screen, $screen ) {
    if( ! current_user_can( 'manage_options' ) ) {
        return false;
    }

    return $show_screen;
}
add_filter( 'screen_options_show_screen', 'timeapp_remove_screen_options', 10, 2 );
