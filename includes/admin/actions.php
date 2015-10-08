<?php
/**
 * Admin actions
 *
 * @package     TimeApp\Admin\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Process all actions sent via POST and GET by looking for
 * the 'timeapp-action' request and running do_action() to
 * call the function.
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_process_actions() {
    if( isset( $_POST['timeapp-action'] ) ) {
        do_action( 'timeapp_' . $_POST['timeapp-action'], $_POST );
    }

    if( isset( $_GET['timeapp-action'] ) ) {
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
    if( ! current_user_can( 'update_plugins' ) ) {
        return;
    }

    $license = get_option( 'timeapp_license', false );

    if( $license != 'valid' ) {
        $api_params = array(
            'edd_action' => 'activate_license',
            'license'    => 'b76ae062e3cd62424479cafed2000529',
            'item_name'  => 'TimeApp',
            'url'        => home_url()
        );

        // Call the API
        $response = wp_remote_post( 'http://ingroupconsulting.com', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        if( is_wp_error( $response ) ) {
            return false;
        }

        // Decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        update_option( 'timeapp_license', $license_data->license );
        delete_transient( 'timeapp_license' );
    }
}