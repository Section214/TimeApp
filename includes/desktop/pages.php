<?php
/**
 * Pages
 *
 * @package     TimeApp\Desktop\Pages
 * @since       2.2.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Remove menu items on desktop
 *
 * @since       2.2.0
 * @return      void
 */
function timeapp_desktop_remove_menus() {
	remove_menu_page( 'plugins.php' );
	remove_menu_page( 'users.php' );
	remove_submenu_page( 'index.php', 'update-core.php' );
}
add_action( 'admin_menu', 'timeapp_desktop_remove_menus' );
