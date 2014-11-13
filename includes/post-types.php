<?php
/**
 * Post type functions
 *
 * @package     TimeApp\PostTypes
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register our new CPTs
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_register_post_types() {
    // Play post type
    $labels = apply_filters( 'timeapp_play_labels', array(
        'name'              => __( 'Plays', 'timeapp' ),
        'singular_name'     => __( 'Play', 'timeapp' ),
        'add_new'           => __( 'Add New', 'timeapp' ),
        'add_new_item'      => __( 'Add New Play', 'timeapp' ),
        'new_item'          => __( 'New Play', 'timeapp' ),
        'edit_item'         => __( 'Edit Play', 'timeapp' ),
        'all_items'         => __( 'All Plays', 'timeapp' ),
        'view_item'         => __( 'View Play', 'timeapp' ),
        'search_items'      => __( 'Search Plays', 'timeapp' ),
        'not_found'         => __( 'No plays found', 'timeapp' ),
        'not_found_in_trash'=> __( 'No plays found in Trash', 'timeapp' )
    ) );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'publicly_queryable'=> true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'capability_type'   => 'play',
        'hierarchical'      => false,
        'supports'          => apply_filters( 'timeapp_play_supports', array( 'title' ) )
    );

    register_post_type( 'play', apply_filters( 'timeapp_play_post_type_args', $args ) );


    // Purchaser post type
    $labels = apply_filters( 'timeapp_purchaser_labels', array(
        'name'              => __( 'Purchasers', 'timeapp' ),
        'singular_name'     => __( 'Purchaser', 'timeapp' ),
        'add_new'           => __( 'Add New', 'timeapp' ),
        'add_new_item'      => __( 'Add New Purchaser', 'timeapp' ),
        'new_item'          => __( 'New Purchaser', 'timeapp' ),
        'edit_item'         => __( 'Edit Purchaser', 'timeapp' ),
        'all_items'         => __( 'All Purchasers', 'timeapp' ),
        'view_item'         => __( 'View Purchaser', 'timeapp' ),
        'search_items'      => __( 'Search Purchasers', 'timeapp' ),
        'not_found'         => __( 'No purchasers found', 'timeapp' ),
        'not_found_in_trash'=> __( 'No purchasers found in Trash', 'timeapp' )
    ) );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'publicly_queryable'=> true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'capability_type'   => 'purchaser',
        'hierarchical'      => false,
        'supports'          => apply_filters( 'timeapp_purchaser_supports', array( 'title' ) )
    );

    register_post_type( 'purchaser', apply_filters( 'timeapp_purchaser_post_type_args', $args ) );


    // Artist post type
    $labels = apply_filters( 'timeapp_artist_labels', array(
        'name'              => __( 'Artists', 'timeapp' ),
        'singular_name'     => __( 'Artist', 'timeapp' ),
        'add_new'           => __( 'Add New', 'timeapp' ),
        'add_new_item'      => __( 'Add New Artist', 'timeapp' ),
        'new_item'          => __( 'New Artist', 'timeapp' ),
        'edit_item'         => __( 'Edit Artist', 'timeapp' ),
        'all_items'         => __( 'All Artists', 'timeapp' ),
        'view_item'         => __( 'View Artist', 'timeapp' ),
        'search_items'      => __( 'Search Artists', 'timeapp' ),
        'not_found'         => __( 'No artists found', 'timeapp' ),
        'not_found_in_trash'=> __( 'No artists found in Trash', 'timeapp' )
    ) );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'publicly_queryable'=> true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'capability_type'   => 'artist',
        'hierarchical'      => false,
        'supports'          => apply_filters( 'timeapp_artist_supports', array( 'title' ) )
    );

    register_post_type( 'artist', apply_filters( 'timeapp_artist_post_type_args', $args ) );


    // Agents post type
    $labels = apply_filters( 'timeapp_agents_labels', array(
        'name'              => __( 'Agents', 'timeapp' ),
        'singular_name'     => __( 'Agent', 'timeapp' ),
        'add_new'           => __( 'Add New', 'timeapp' ),
        'add_new_item'      => __( 'Add New Agent', 'timeapp' ),
        'new_item'          => __( 'New Agent', 'timeapp' ),
        'edit_item'         => __( 'Edit Agent', 'timeapp' ),
        'all_items'         => __( 'All Agents', 'timeapp' ),
        'view_item'         => __( 'View Agent', 'timeapp' ),
        'search_items'      => __( 'Search Agents', 'timeapp' ),
        'not_found'         => __( 'No agents found', 'timeapp' ),
        'not_found_in_trash'=> __( 'No agents found in Trash', 'timeapp' )
    ) );

    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'publicly_queryable'=> true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'capability_type'   => 'agent',
        'hierarchical'      => false,
        'supports'          => apply_filters( 'timeapp_agent_supports', array( 'title' ) )
    );

    register_post_type( 'agent', apply_filters( 'timeapp_agent_post_type_args', $args ) );
}
add_action( 'init', 'timeapp_register_post_types', 1 );


/**
 * Change default "Enter title here" placeholder
 *
 * @since       1.0.0
 * @param       string $title The default placeholder
 * @return      string $title The updated placeholder
 */
function timeapp_enter_title_here( $title ) {
    $screen = get_current_screen();

    if( $screen->post_type == 'play' ) {
        $title = __( 'Play title will be generated on save', 'timeapp' );
    } elseif( $screen->post_type == 'purchaser' ) {
        $title = __( 'Enter purchaser name here', 'timeapp' );
    } elseif( $screen->post_type == 'artist' ) {
        $title = __( 'Enter artist name here', 'timeapp' );
    } elseif( $screen->post_type == 'agent' ) {
        $title = __( 'Enter agent name here', 'timeapp' );
    }

    return $title;
}
add_filter( 'enter_title_here', 'timeapp_enter_title_here' );


/**
 * Update messages
 *
 * @since       1.0.0
 * @param       array $messages The default messages
 * @return      array $messages The updated messages
 */
function timeapp_updated_messages( $messages ) {
    $messages['play'] = array(
        1 => __( 'Play updated.', 'timeapp' ),
        4 => __( 'Play updated.', 'timeapp' ),
        6 => __( 'Play published.', 'timeapp' ),
        7 => __( 'Play saved.', 'timeapp' ),
        8 => __( 'Play submitted.', 'timeapp' )
    );

    $messages['purchaser'] = array(
        1 => __( 'Purchaser updated.', 'timeapp' ),
        4 => __( 'Purchaser updated.', 'timeapp' ),
        6 => __( 'Purchaser published.', 'timeapp' ),
        7 => __( 'Purchaser saved.', 'timeapp' ),
        8 => __( 'Purchaser submitted.', 'timeapp' )
    );

    $messages['artist'] = array(
        1 => __( 'Artist updated.', 'timeapp' ),
        4 => __( 'Artist updated.', 'timeapp' ),
        6 => __( 'Artist published.', 'timeapp' ),
        7 => __( 'Artist saved.', 'timeapp' ),
        8 => __( 'Artist submitted.', 'timeapp' )
    );

    $messages['agent'] = array(
        1 => __( 'Agent updated.', 'timeapp' ),
        4 => __( 'Agent updated.', 'timeapp' ),
        6 => __( 'Agent published.', 'timeapp' ),
        7 => __( 'Agent saved.', 'timeapp' ),
        8 => __( 'Agent submitted.', 'timeapp' )
    );

    return $messages;
}
add_filter( 'post_updated_messages', 'timeapp_updated_messages' );


/**
 * Enforce a single-column layout
 *
 * @since       1.0.0
 * @return      int 1
 */
function timeapp_cpt_layout() {
    return 1;
}
add_filter( 'get_user_option_screen_layout_play', 'timeapp_cpt_layout' );
add_filter( 'get_user_option_screen_layout_purchaser', 'timeapp_cpt_layout' );
add_filter( 'get_user_option_screen_layout_artist', 'timeapp_cpt_layout' );
add_filter( 'get_user_option_screen_layout_agent', 'timeapp_cpt_layout' );
