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
