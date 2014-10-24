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
    add_meta_box( 'artist_details', __( 'Artist Details', 'timeapp' ), 'timeapp_render_artist_details_meta_box', 'artist', 'normal', 'default' );
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

    submit_button( __( 'Save Play', 'timeapp' ), 'primary timeapp-save', null, false );
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


/**
 * Render artist details meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_artist_details_meta_box() {
    global $post;

    $post_id        = $post->ID;
    $signer_name    = get_post_meta( $post_id, '_timeapp_signer_name', true );
    $artist_email   = get_post_meta( $post_id, '_timeapp_artist_email', true );
    $tax_id         = get_post_meta( $post_id, '_timeapp_tax_id', true );
    $artist_url     = get_post_meta( $post_id, '_timeapp_artist_url', true );
    $promo_url      = get_post_meta( $post_id, '_timeapp_promo_url', true );
    $commission     = get_post_meta( $post_id, '_timeapp_commission', true );
    $rider          = get_post_meta( $post_id, '_timeapp_rider', true );

    // Signer name
    echo '<p>';
    echo '<strong><label for="_timeapp_signer_name">' . __( 'Signer Name', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_signer_name" id="_timeapp_signer_name" value="' . ( isset( $signer_name ) && ! empty( $signer_name ) ? $signer_name : '' ) . '" />';
    echo '</p>';

    // Artist email
    echo '<p>';
    echo '<strong><label for="_timeapp_artist_email">' . __( 'Artist Email', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_artist_email" id="_timeapp_artist_email" value="' . ( isset( $artist_email ) && ! empty( $artist_email ) ? $artist_email : '' ) . '" />';
    echo '</p>';
    
    // Tax ID
    echo '<p>';
    echo '<strong><label for="_timeapp_tax_id">' . __( 'Tax ID', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_tax_id" id="_timeapp_tax_id" value="' . ( isset( $tax_id ) && ! empty( $tax_id ) ? $tax_id : '' ) . '" />';
    echo '</p>';
    
    // Artist URL
    echo '<p>';
    echo '<strong><label for="_timeapp_artist_url">' . __( 'Artist URL', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_artist_url" id="_timeapp_artist_url" value="' . ( isset( $artist_url ) && ! empty( $artist_url ) ? $artist_url : '' ) . '" placeholder="' . __( 'http://', 'timeapp' ) . '" />';
    echo '</p>';
    
    // Promo URL
    echo '<p>';
    echo '<strong><label for="_timeapp_promo_url">' . __( 'Promo URL', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_promo_url" id="_timeapp_promo_url" value="' . ( isset( $promo_url ) && ! empty( $promo_url ) ? $promo_url : '' ) . '" placeholder="' . __( 'http://', 'timeapp' ) . '" />';
    echo '</p>';

    // Commission
    echo '<p>';
    echo '<strong><label for="_timeapp_commission">' . __( 'Commission %', 'timeapp' ) . '</label></strong><br />';
    echo '<select name="_timeapp_commission" id="_timeapp_commission">';
    echo '<option value="5"' . ( ! isset( $commission ) || $commission == '5' ? ' selected' : '' ) . '>' . __( '5%', 'timeapp' ) . '</option>';
    echo '<option value="10"' . ( $commission == '10' ? ' selected' : '' ) . '>' . __( '10%', 'timeapp' ) . '</option>';
    echo '<option value="15"' . ( $commission == '15' ? ' selected' : '' ) . '>' . __( '15%', 'timeapp' ) . '</option>';
    echo '</select>';

    // Rider
//    echo '<p>';
//    echo '<strong><label for="_timeapp_rider">' . __( 'Rider', 'timeapp' ) . '</label></strong><br />';
//    echo '<input type="text" class="timeapp-upload-field regular-text" name="_timeapp_rider" id="_timeapp_rider" value="' . ( ! isset( $rider ) && ! empty( $rider ) ? $rider : '' ) . '" />';
//    echo '<input type="button" class="button" name="_timeapp_rider_button" id="_timeapp_rider_button" value="' . __( 'Upload File', 'timeapp' ) . '" />';
//    echo '</p>';

    do_action( 'timeapp_artist_details_fields', $post_id );

    wp_nonce_field( basename( __FILE__ ), 'timeapp_artist_details_nonce' );
}


/**
 * Save post meta when the save_post action is called
 *
 * @since       1.0.0
 * @param       int $post_id The ID of the post we are saving
 * @global      object $post The post we are saving
 * @return      void
 */
function timeapp_save_artist_details_meta_box( $post_id ) {
    global $post;

    // Don't process if nonce can't be validated
    if( ! isset( $_POST['timeapp_artist_details_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_artist_details_nonce'], basename( __FILE__ ) ) ) return $post_id;

    // Don't process if this is an autosave
    if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) return $post_id;

    // Don't process if this is a revision
    if( isset( $post->post_type ) && $post->post_type == 'revision' ) return $post_id;

    // Don't process if the current user shouldn't be editing this product
    if( ! current_user_can( 'edit_artist', $post_id ) ) return $post_id;

    // The fields to save
    $fields = apply_filters( 'timeapp_artist_details_fields_save', array(
        '_timeapp_signer_name',
        '_timeapp_artist_email',
        '_timeapp_tax_id',
        '_timeapp_artist_url',
        '_timeapp_promo_url',
        '_timeapp_commission',
        '_timeapp_rider'
    ) );

    foreach( $fields as $field ) {
        if( isset( $_POST[$field] ) ) {
            if( is_string( $_POST[$field] ) ) {
                $new = esc_attr( $_POST[$field] );
            } else {
                $new = $_POST[$field];
            }

            $new = apply_filters( 'timeapp_artist_details_save_' . $field, $new );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'timeapp_save_artist_details_meta_box' );
