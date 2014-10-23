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
    add_meta_box(
        'actions_top',
        __( 'Actions', 'timeapp' ),
        'timeapp_render_actions_meta_box',
        'play',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'timeapp_add_meta_boxes' );


/**
 * Render our actions meta boxes
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_render_actions_meta_box() {
    submit_button( __( 'Save Play', 'timeapp' ), 'primary', null, false );
    do_action( 'timeapp_meta_box_actions' );

    echo '<div class="timeapp-action-delete">';
    echo '<a class="submitdelete" href="' . get_delete_post_link() . '">' . __( 'Move to Trash', 'timeapp' ) . '</a>';
    echo '</div>';
}
