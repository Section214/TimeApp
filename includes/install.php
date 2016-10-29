<?php
/**
 * Install
 *
 * @package     TimeApp\Install
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Runs on install to setup post types, flush rewrite rules, and
 * create our new user rules and capabilities.
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_install() {
	// Setup our new post types
	timeapp_register_post_types();

	// Clear permalinks
	flush_rewrite_rules();

	// Add upgraded from option
	$current_version = get_option( 'timeapp_version' );
	if ( $current_version ) {
		update_option( 'timeapp_version_upgraded_from', $current_version );
	}

	update_option( 'timeapp_version', TIMEAPP_VER );

	// Create new roles
	$roles = new TimeApp_Roles;
	$roles->add_roles();
	$roles->add_caps();
}
register_activation_hook( TIMEAPP_FILE, 'timeapp_install' );
