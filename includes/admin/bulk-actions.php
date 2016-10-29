<?php
/**
 * Bulk actions
 *
 * @package     TimeApp\Admin\BulkActions
 * @since       1.3.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add our new actions to the dropdown
 *
 * @since       1.3.0
 * @return      void
 */
function timeapp_add_bulk_actions() {
	global $post_type;

	if ( $post_type == 'play' ) {
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('<option>').val('follow-up').text('<?php _e( 'Mark Followed Up', 'timeapp' ); ?>').appendTo('select[name="action"]');
			$('<option>').val('follow-up').text('<?php _e( 'Mark Followed Up', 'timeapp' ); ?>').appendTo('select[name="action2"]');
			$('<option>').val('pay').text('<?php _e( 'Mark Commission Paid', 'timeapp' ); ?>').appendTo('select[name="action"]');
			$('<option>').val('pay').text('<?php _e( 'Mark Commission Paid', 'timeapp' ); ?>').appendTo('select[name="action2"]');
		});
	</script>
	<?php
	}
}
add_action( 'admin_footer-edit.php', 'timeapp_add_bulk_actions' );


/**
 * Process bulk actions
 *
 * @since       1.3.0
 * @global      string $typenow The post type
 * @return      void
 */
function timeapp_process_bulk_actions() {
	global $typenow;

	if ( $typenow == 'play' ) {
		// Get the action
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();
		$allowed       = array( 'follow-up', 'pay' );

		// Bail if the action isn't permitted
		if ( ! in_array( $action, $allowed ) ) {
			return;
		}

		check_admin_referer( 'bulk-posts' );

		// Bail if no IDs are submitted
		if ( isset( $_REQUEST['post'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['post'] );
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		$sendback = remove_query_arg( array( 'follow-up', 'pay', 'untrashed', 'deleted', 'ids' ), wp_get_referer() );

		if ( ! $sendback ) {
			$sendback = admin_url( "edit.php?post_type=$post_type" );
		}

		$pagenum  = $wp_list_table->get_pagenum();
		$sendback = add_query_arg( 'paged', $pagenum, $sendback );

		switch ( $action ) {
			case 'follow-up':
				$followed_up = 0;

				foreach ( $post_ids as $post_id ) {
					update_post_meta( $post_id, '_timeapp_followed_up', '1' );
					$followed_up++;
				}

				$sendback = add_query_arg( array( 'followed_up' => $followed_up, 'ids' => join( ',', $post_ids ) ), $sendback );
				break;
			case 'pay':
				$paid = 0;
				$now  = current_time( 'm/d/Y g:i a' );

				foreach ( $post_ids as $post_id ) {
					update_post_meta( $post_id, '_timeapp_date_paid', $now );
					$paid++;
				}

				$sendback = add_query_arg( array( 'paid' => $paid, 'ids' => join( ',', $post_ids ) ), $sendback );
				break;
			default:
				return;
		}

		$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

		wp_safe_redirect( $sendback );
		exit();
	}
}
add_action( 'load-edit.php', 'timeapp_process_bulk_actions' );


/**
 * Display admin notices
 *
 * @since       1.3.0
 * @global      string $post_type The post type
 * @global      string $pagenow The current page
 * @return      void
 */
function timeapp_bulk_action_notices() {
	global $post_type, $pagenow;

	if ( $pagenow == 'edit.php' && $post_type == 'play' && isset( $_REQUEST['followed_up'] ) && (int) $_REQUEST['followed_up'] ) {
		$message = sprintf( _n( 'Play marked as followed up.', '%s plays marked as followed up.', $_REQUEST['followed_up'] ), number_format_i18n( $_REQUEST['followed_up'] ) );
		echo '<div class="updated"><p>' . $message . '</p></div>';
	}

	if ( $pagenow == 'edit.php' && $post_type == 'play' && isset( $_REQUEST['paid'] ) && (int) $_REQUEST['paid'] ) {
		$message = sprintf( _n( 'Play marked as commission paid.', '%s plays marked as commission paid.', $_REQUEST['paid'] ), number_format_i18n( $_REQUEST['paid'] ) );
		echo '<div class="updated"><p>' . $message . '</p></div>';
	}
}
add_action( 'admin_notices', 'timeapp_bulk_action_notices' );
