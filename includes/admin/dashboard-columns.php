<?php
/**
 * Dashboard columns
 *
 * @package     TimeApp\Admin\DashboardColumns
 * @since       1.1.3
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Tweak dashboard columns
 *
 * @since       1.1.3
 * @param       array $columns The current columns
 * @return      array $columns The updated columns
 */
function timeapp_dashboard_columns( $columns ) {
    $columns = array(
        'cb'            => '<input type="checkbox" />',
        'title'         => __( 'Title', 'timeapp' )
    );

    return apply_filters( 'timeapp_dashboard_columns', $columns );
}
add_filter( 'manage_edit-play_columns', 'timeapp_dashboard_columns' );
add_filter( 'manage_edit-artist_columns', 'timeapp_dashboard_columns' );
add_filter( 'manage_edit-purchaser_columns', 'timeapp_dashboard_columns' );
add_filter( 'manage_edit-agent_columns', 'timeapp_dashboard_columns' );


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

    if( in_array( $typenow, $cpts ) ) {
        $dates = array();
    }

    return $dates;
}
add_filter( 'months_dropdown_results', 'timeapp_remove_date_filter', 99 );


function timeapp_filter_columns() {
    global $typenow, $wpdb, $wp_locale;
    
    if( $typenow == 'play' ) {
        $artists    = timeapp_get_artists();
        $purchasers = timeapp_get_purchasers();
        $months     = timeapp_get_months();

        if( $months ) {
            echo '<select name="filter_start_date">';
            echo '<option value="">' . __( 'Show all months', 'timeapp' ) . '</option>';
            foreach( $months as $id => $month ) {
                $selected = isset( $_GET['filter_start_date'] ) && $_GET['filter_start_date'] == $id ? ' selected="selected"' : '';
                echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $month ) . '</option>';
            }
            echo '</select>';
        }

        if( count( $artists ) > 0 ) {
            echo '<select name="filter_artist">';
            echo '<option value="">' . __( 'Show all artists', 'timeapp' ) . '</option>';
            foreach( $artists as $id => $artist ) {
                $selected = isset( $_GET['filter_artist'] ) && $_GET['filter_artist'] == $id ? ' selected="selected"' : '';
                echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $artist ) . '</option>';
            }
            echo '</select>';
        }

        if( count( $purchasers ) > 0 ) {
            echo '<select name="filter_purchaser">';
            echo '<option value="">' . __( 'Show all purchasers', 'timeapp' ) . '</option>';
            foreach( $purchasers as $id => $purchaser ) {
                $selected = isset( $_GET['filter_purchaser'] ) && $_GET['filter_purchaser'] == $id ? ' selected="selected"' : '';
                echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $purchaser ) . '</option>';
            }
            echo '</select>';
        }
    }
}
add_action( 'restrict_manage_posts', 'timeapp_filter_columns' );


function timeapp_filter_query( $query ) {
    global $typenow, $pagenow;

    if( $typenow == 'play' && $pagenow == 'edit.php' ) {
        if( isset( $_GET['filter_start_date'] ) && ! empty( $_GET['filter_start_date'] ) ) {
            $start_date = explode( '-', $_GET['filter_start_date'] );

            $query['meta_query'][] = array(
                'key'       => '_timeapp_start_date',
                'value'     => $start_date[0] . '(.*)' . $start_date[1] . '(.*)',
                'compare'   => 'REGEXP'
            );
        }

        if( isset( $_GET['filter_artist'] ) && ! empty( $_GET['filter_artist'] ) ) {
            $query['meta_query'][] = array(
                'key'       => '_timeapp_artist',
                'value'     => $_GET['filter_artist'],
                'compare'   => '='
            );
        }

        if( isset( $_GET['filter_purchaser'] ) && ! empty( $_GET['filter_purchaser'] ) ) {
            $query['meta_query'][] = array(
                'key'       => '_timeapp_purchaser',
                'value'     => $_GET['filter_purchaser'],
                'compare'   => '='
            );
        }
    }

    return $query;
}
add_filter( 'request', 'timeapp_filter_query', 2 );
