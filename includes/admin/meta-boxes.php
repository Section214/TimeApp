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
 * Register new metaboxes
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_add_meta_boxes() {
    //remove_meta_box( 'submitdiv', 'play', 'normal' );
    remove_meta_box( 'submitdiv', 'purchaser', 'normal' );
    remove_meta_box( 'submitdiv', 'artist', 'normal' );

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
    $delete_url = wp_nonce_url( admin_url( 'post.php?post=' . get_the_ID() . '&action=trash' ) );

    submit_button( __( 'Save Play', 'timeapp' ), 'primary', null, false );
    do_action( 'timeapp_meta_box_actions' );

    echo '<div id="delete-action">';
    echo '<a class="submitdelete" href="' . $delete_url . '">' . __( 'Move to Trash', 'timeapp' ) . '</a>';
    echo '</div>';
}
