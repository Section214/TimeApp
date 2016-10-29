<?php
/**
 * Dashboard columns
 *
 * @package     TimeApp\Admin\DashboardColumns
 * @since       1.1.3
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Tweak dashboard columns
 *
 * @since       1.1.3
 * @param       array $columns The current columns
 * @global      string $typenow The post type we are viewing
 * @return      array $columns The updated columns
 */
function timeapp_dashboard_columns( $columns ) {
	global $typenow;

	$columns = array(
		'cb'    => '<input type="checkbox" />',
		'title' => __( 'Title', 'timeapp' ),
	);

	if ( $typenow == 'play' ) {
		$columns['commission'] = __( 'Commission', 'timeapp' );
		$columns['status']     = __( 'Status', 'timeapp' );
	}

	return apply_filters( 'timeapp_dashboard_columns', $columns );
}
add_filter( 'manage_edit-play_columns', 'timeapp_dashboard_columns' );
add_filter( 'manage_edit-artist_columns', 'timeapp_dashboard_columns' );
add_filter( 'manage_edit-purchaser_columns', 'timeapp_dashboard_columns' );
add_filter( 'manage_edit-agent_columns', 'timeapp_dashboard_columns' );


/**
 * Render our custom dashboard columns
 *
 * @since      1.3.0
 * @param      string $column_name The column name for a given column
 * @param      int $post_id The post ID for a given row
 * @return     void
 */
function timeapp_render_dashboard_columns( $column_name, $post_id ) {
	if ( get_post_type( $post_id ) == 'play' ) {
		switch ( $column_name ) {
			case 'status':
				$status = get_post_meta( $post_id, '_timeapp_status', true );
				if ( $status == 'cancelled' ) {
					echo '<div class="timeapp-contract-cancelled">' . __( 'Cancelled', 'timeapp' ) . '</div>';
				} elseif ( $status == 'contracted' ) {
					echo '<div class="timeapp-contract-contracted">' . __( 'Contracted', 'timeapp' ) . '</div>';
				} elseif ( $status == 'hold' ) {
					echo '<div class="timeapp-contract-hold">' . __( 'Hold', 'timeapp' ) . '</div>';
				}
				break;
			case 'commission':
				if ( get_post_meta( $post_id, '_timeapp_date_paid', true ) ) {
					echo '<div class="timeapp-commission-paid">' . __( 'Paid', 'timeapp' ) . '</div>';
				} else {
					if ( get_post_meta( $post_id, '_timeapp_status', true ) == 'cancelled' ) {
						$commission = '---';
					} else {
						$artist          = get_post_meta( $post_id, '_timeapp_artist', true );
						$artist          = get_post( $artist );
						$guarantee       = get_post_meta( $post_id, '_timeapp_guarantee', true );
						$production      = get_post_meta( $post_id, '_timeapp_production', true );
						$production_cost = ( ! $production ? get_post_meta( $post_id, '_timeapp_production_cost', true ) : false );
						$commission_rate = get_post_meta( $artist->ID, '_timeapp_commission', true );
						$split_comm      = get_post_meta( $post_id, '_timeapp_split_comm', true );
						$split_rate      = ( $split_comm ? get_post_meta( $post_id, '_timeapp_split_perc', true ) : false );
						$commission      = timeapp_get_commission( $guarantee, $production_cost, $commission_rate, $split_rate );
					}

					echo '<div class="timeapp-commission-unpaid">' . sprintf( __( '%s', 'timeapp' ), $commission ) . '</div>';
				}

				break;
		}
	}
}
add_action( 'manage_posts_custom_column', 'timeapp_render_dashboard_columns', 10, 2 );


/**
 * Remove date filter
 *
 * @since       1.1.3
 * @param       array $dates The current dates
 * @global      string $typenow The current post type
 * @return      array $dates The updated (empty) dates array
 */
function timeapp_remove_date_filter( $dates ) {
	global $typenow;

	$cpts = array( 'play', 'artist', 'purchaser', 'agent' );

	if ( in_array( $typenow, $cpts ) ) {
		$dates = array();
	}

	return $dates;
}
add_filter( 'months_dropdown_results', 'timeapp_remove_date_filter', 99 );


/**
 * Add play custom filters to play CPT
 *
 * @since       1.2.0
 * @return      void
 */
function timeapp_filter_columns() {
	global $typenow, $wpdb, $wp_locale;

	if ( $typenow == 'play' ) {
		$artists    = timeapp_get_artists();
		$purchasers = timeapp_get_purchasers();
		$months     = timeapp_get_months();

		asort( $artists );
		asort( $purchasers );

		if ( $months ) {
			echo '<select name="filter_start_date">';
			echo '<option value="">' . __( 'Show all months', 'timeapp' ) . '</option>';
			foreach ( $months as $id => $month ) {
				$selected = isset( $_GET['filter_start_date'] ) && $_GET['filter_start_date'] == $id ? ' selected="selected"' : '';
				echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $month ) . '</option>';
			}
			echo '</select>';
		}

		if ( count( $artists ) > 0 ) {
			echo '<select name="filter_artist">';
			echo '<option value="">' . __( 'Show all artists', 'timeapp' ) . '</option>';
			foreach ( $artists as $id => $artist ) {
				$selected = isset( $_GET['filter_artist'] ) && $_GET['filter_artist'] == $id ? ' selected="selected"' : '';
				echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $artist ) . '</option>';
			}
			echo '</select>';
		}

		if ( count( $purchasers ) > 0 ) {
			echo '<select name="filter_purchaser">';
			echo '<option value="">' . __( 'Show all purchasers', 'timeapp' ) . '</option>';
			foreach ( $purchasers as $id => $purchaser ) {
				$selected = isset( $_GET['filter_purchaser'] ) && $_GET['filter_purchaser'] == $id ? ' selected="selected"' : '';
				echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $purchaser ) . '</option>';
			}
			echo '</select>';
		}
	}
}
add_action( 'restrict_manage_posts', 'timeapp_filter_columns' );


/**
 * Process filters for play CPT
 *
 * @since       1.2.0
 * @param       object $query The WordPress query object
 * @return      object $query The modified query object
 */
function timeapp_filter_query( $query ) {
	global $typenow, $pagenow;

	if ( $typenow == 'play' && $pagenow == 'edit.php' ) {
		if ( isset( $_GET['filter_start_date'] ) && ! empty( $_GET['filter_start_date'] ) ) {
			$start_date = explode( '-', $_GET['filter_start_date'] );

			$query['meta_query'][] = array(
				'key'     => '_timeapp_start_date',
				'value'   => '^' . $start_date[1] . '(.*)' . $start_date[0],
				'compare' => 'REGEXP'
			);
		}

		if ( isset( $_GET['filter_artist'] ) && ! empty( $_GET['filter_artist'] ) ) {
			$query['meta_query'][] = array(
				'key'     => '_timeapp_artist',
				'value'   => $_GET['filter_artist'],
				'compare' => '='
			);
		}

		if ( isset( $_GET['filter_purchaser'] ) && ! empty( $_GET['filter_purchaser'] ) ) {
			$query['meta_query'][] = array(
				'key'     => '_timeapp_purchaser',
				'value'   => $_GET['filter_purchaser'],
				'compare' => '='
			);
		}
	}

	return $query;
}
add_filter( 'request', 'timeapp_filter_query', 2 );
