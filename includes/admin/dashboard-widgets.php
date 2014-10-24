<?php
/**
 * Dashboard Widgets
 *
 * @package     TimeApp\Admin\DashboardWidgets
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Remove the default dashboard widgets
 *
 * @since       1.0.0
 * @global      array $wp_meta_boxes Registered meta boxes
 * @return      void
 */
function timeapp_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    
    if( ! current_user_can( 'manage_options' ) ) {
        unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
        unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
    }
}
add_action( 'wp_dashboard_setup', 'timeapp_remove_dashboard_widgets' );


/**
 * Register the dashboard widgets
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_register_dashboard_widgets() {
    wp_add_dashboard_widget(
        'timeapp_upcoming_plays',
        __( 'Upcoming Plays', 'timeapp' ),
        'timeapp_upcoming_plays_widget'
    );
}


/**
 * Render Upcoming Plays widget
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_upcoming_plays_widget() {
?>
    <div class="timeapp_dashboard_widget">

    </div>
<?php
}
