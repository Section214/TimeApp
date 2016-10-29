<?php
/**
 * Admin notices
 *
 * @package     TimeApp\Admin\Notices
 * @since       1.3.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Display admin notices
 *
 * @since       1.3.0
 * @global      string $typenow The type of post we are editing
 * @global      string $pagenow The page we are viewing
 * @return      void
 */
function timeapp_display_admin_notices() {
	global $typenow, $pagenow;

	// Bail if this isn't a post edit page
	if ( $pagenow != 'post.php' || ! isset( $_GET['post'] ) ) {
		return;
	}

	// Bail if this isn't the play post type
	if ( $typenow != 'play' ) {
		return;
	}

	// Display the cancelled play notice
	$status = get_post_meta( $_GET['post'], '_timeapp_status', true );

	if ( $status == 'cancelled' ) {
		echo '<div class="error"><p>' . __( 'Play cancelled.', 'timeapp' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'timeapp_display_admin_notices' );
