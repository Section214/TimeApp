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
