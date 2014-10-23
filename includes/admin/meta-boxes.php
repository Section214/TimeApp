<?php
/**
 * Meta boxes
 *
 * @package     TimeApp\Admin\MetaBoxes
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Remove the default submit meta box
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_remove_meta_boxes() {
    remove_meta_box( 'submitdiv', 'play', 'normal' );
    remove_meta_box( 'submitdiv', 'purchaser', 'normal' );
    remove_meta_box( 'submitdiv', 'artist', 'normal' );
}
add_action( 'admin_init', 'timeapp_remove_meta_boxes' );


/**
 * Register new metaboxes
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_add_meta_boxes() {
    // Play post type
    add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'play', 'normal', 'high' );
    add_meta_box( 'actions_bottom', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'play', 'normal', 'low' );

    // Purchaser post type
    add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'purchaser', 'normal', 'high' );
    add_meta_box( 'actions_bottom', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'purchaser', 'normal', 'low' );

    // Artist post type
    add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'artist', 'normal', 'high' );
    add_meta_box( 'actions_bottom', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'artist', 'normal', 'low' );
}
add_action( 'add_meta_boxes', 'timeapp_add_meta_boxes' );


/**
 * Render our actions meta boxes
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_render_actions_meta_box() {
    $post_type = get_post_type();

    submit_button( __( 'Save Play', 'timeapp' ), 'primary', null, false );
    do_action( 'timeapp_meta_box_' . $post_type . '_actions' );

    echo '<div class="timeapp-action-delete">';
    echo '<a class="submitdelete" href="' . get_delete_post_link() . '">' . __( 'Move to Trash', 'timeapp' ) . '</a>';
    echo '</div>';
}


/**
 * Add Generate PDF button on 'play' post type
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_add_generate_pdf_button() {
    echo '<a class="button button-secondary">' . __( 'Generate PDF', 'timeapp' ) . '</a>';
}
add_action( 'timeapp_meta_box_play_actions', 'timeapp_add_generate_pdf_button' );
