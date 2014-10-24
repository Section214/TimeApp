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
    add_meta_box( 'calendar_info', __( 'Calendar Information', 'timeapp' ), 'timeapp_render_calendar_info_meta_box', 'play', 'normal', 'default' );
    add_meta_box( 'communications', __( 'Communications', 'timeapp' ), 'timeapp_render_communications_meta_box', 'play', 'normal', 'default' );
    add_meta_box( 'play_details', __( 'Play Details', 'timeapp' ), 'timeapp_render_play_details_meta_box', 'play', 'normal', 'default' );

    // Purchaser post type
    add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'purchaser', 'normal', 'high' );
    add_meta_box( 'actions_bottom', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'purchaser', 'normal', 'low' );
    add_meta_box( 'contact_info', __( 'Contact Information', 'timeapp' ), 'timeapp_render_contact_info_meta_box', 'purchaser', 'normal', 'default' );
    add_meta_box( 'venue_info', __( 'Venue Information', 'timeapp' ), 'timeapp_render_venue_info_meta_box', 'purchaser', 'normal', 'default' );

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
 * Render calendar info meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_calendar_info_meta_box() {
    global $post;

    $post_id        = $post->ID;
    $start_date     = get_post_meta( $post_id, '_timeapp_start_date', true );
    $start_date     = ( isset( $start_date ) && ! empty( $start_date ) ? date( 'm/d/Y g:i a', strtotime( $start_date ) ) : '' );
    $end_date       = get_post_meta( $post_id, '_timeapp_end_date', true );
    $end_date       = ( isset( $end_date ) && ! empty( $end_date ) ? date( 'g:i a', strtotime( $end_date ) ) : '' );

    // Start date
    echo '<p class="timeapp-half">';
    echo '<strong><label for="_timeapp_start_date">' . __( 'Start Date', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_start_date" id="_timeapp_start_date" value="' . $start_date . '" />';
    echo '</p>';

    // End date
    echo '<p class="timeapp-half">';
    echo '<strong><label for="_timeapp_end_date">' . __( 'End Date', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_end_date" id="_timeapp_end_date" value="' . $end_date . '" />';
    echo '</p>';

    echo '<div class="timeapp-clear"></div>';

    do_action( 'timeapp_calendar_info_fields', $post_id );

    wp_nonce_field( basename( __FILE__ ), 'timeapp_play_nonce' );
}


/**
 * Render communications meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_communications_meta_box() {
    global $post;

    $post_id        = $post->ID;
    $contract_sent  = get_post_meta( $post_id, '_timeapp_contract_sent', true );
    $contract_sent  = ( isset( $contract_sent ) && ! empty( $contract_sent ) ? date( 'm/d/Y g:i a', strtotime( $contract_sent ) ) : '' );
    $contract_rcvd  = get_post_meta( $post_id, '_timeapp_contract_rcvd', true );
    $contract_rcvd  = ( isset( $contract_rcvd ) && ! empty( $contract_rcvd ) ? date( 'm/d/Y g:i a', strtotime( $contract_rcvd ) ) : '' );
    $promo_sent     = get_post_meta( $post_id, '_timeapp_promo_sent', true );
    $promo_sent     = ( isset( $promo_sent ) && ! empty( $promo_sent ) ? date( 'm/d/Y g:i a', strtotime( $promo_sent ) ) : '' );
    $promo_rcvd     = get_post_meta( $post_id, '_timeapp_promo_rcvd', true );
    $promo_rcvd     = ( isset( $promo_rcvd ) && ! empty( $promo_rcvd ) ? date( 'm/d/Y g:i a', strtotime( $promo_rcvd ) ) : '' );
    $followed_up    = get_post_meta( $post_id, '_timeapp_followed_up', true ) ? true : false;
    $followup_notes = get_post_meta( $post_id, '_timeapp_followup_notes', true );

    // Contract sent
    echo '<p class="timeapp-half">';
    echo '<strong><label for="_timeapp_contract_sent">' . __( 'Contract Sent', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_contract_sent" id="_timeapp_contract_sent" value="' . $contract_sent . '" />';
    echo '</p>';

    // Contract received
    echo '<p class="timeapp-half">';
    echo '<strong><label for="_timeapp_contract_rcvd">' . __( 'Contract Received', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_contract_rcvd" id="_timeapp_contract_rcvd" value="' . $contract_rcvd . '" />';
    echo '</p>';

    echo '<div class="timeapp-clear"></div>';

    // Promo sent
    echo '<p class="timeapp-half">';
    echo '<strong><label for="_timeapp_promo_sent">' . __( 'Promo Sent', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_promo_sent" id="_timeapp_promo_sent" value="' . $promo_sent . '" />';
    echo '</p>';

    // Promo received
    echo '<p class="timeapp-half">';
    echo '<strong><label for="_timeapp_promo_rcvd">' . __( 'Promo Received', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_promo_rcvd" id="_timeapp_promo_rcvd" value="' . $promo_rcvd . '" />';
    echo '</p>';

    // Followed up
    echo '<p>';
    echo '<strong><label for="_timeapp_followed_up">' . __( 'Followed Up?', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="checkbox" name="_timeapp_followed_up" id="_timeapp_followed_up" value="1" ' . checked( true,  $followed_up, false ) . ' />';
    echo '<label for="_timeapp_followed_up">' . __( 'Check when gig has been followed up on.', 'timeapp' ) . '</label>';
    echo '</p>';

    // Follow up notes
    echo '<p>';
    echo '<strong><label for="_timeapp_followup_notes">' . __( 'Follow Up Notes', 'timeapp' ) . '</label></strong><br />';
    echo '<textarea cols="30" rows="5" name="_timeapp_followup_notes" id="_timeapp_followup_notes">' . $followup_notes . '</textarea>';
    echo '</p>';

    echo '<div class="timeapp-clear"></div>';

    do_action( 'timeapp_communications_fields', $post_id );
}


/**
 * Render play details meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_play_details_meta_box() {
    global $post;

    $post_id        = $post->ID;
    $status         = get_post_meta( $post_id, '_timeapp_status', true );
    $type           = get_post_meta( $post_id, '_timeapp_type', true );
    $agent          = get_post_meta( $post_id, '_timeapp_agent', true );
    $purchaser      = get_post_meta( $post_id, '_timeapp_purchaser', true );
    $artist         = get_post_meta( $post_id, '_timeapp_artist', true );
    $approved       = get_post_meta( $post_id, '_timeapp_approved', true ) ? true : false;
    $set_reqs       = get_post_meta( $post_id, '_timeapp_set_reqs', true );
    $notes          = get_post_meta( $post_id, '_timeapp_notes', true );

    // Status
    echo '<p>';
    echo '<strong><label for="_timeapp_status">' . __( 'Event Status', 'timeapp' ) . '</label></strong><br />';
    echo '<select name="_timeapp_status" id="_timeapp_status">';
    echo '<option value="hold"' . ( ! isset( $status ) || $status == 'hold' ? ' selected' : '' ) . '>' . __( 'Hold', 'timeapp' ) . '</option>';
    echo '<option value="contracted"' . ( $status == 'contracted' ? ' selected' : '' ) . '>' . __( 'Contracted', 'timeapp' ) . '</option>';
    echo '</select>';

    // Type
    echo '<p>';
    echo '<strong><label for="_timeapp_type">' . __( 'Event Type', 'timeapp' ) . '</label></strong><br />';
    echo '<select name="_timeapp_type" id="_timeapp_type">';
    echo '<option value="club"' . ( ! isset( $type ) || $type == 'club' ? ' selected' : '' ) . '>' . __( 'Club', 'timeapp' ) . '</option>';
    echo '<option value="event"' . ( $type == 'event' ? ' selected' : '' ) . '>' . __( 'Event', 'timeapp' ) . '</option>';
    echo '</select>';
    
    // Agent
    echo '<p>';
    echo '<strong><label for="_timeapp_agent">' . __( 'Agent', 'timeapp' ) . '</label></strong><br />';
    echo '<select name="_timeapp_agent" id="_timeapp_agent">';
    echo '<option value="mfindling"' . ( ! isset( $agent ) || $agent == 'mfindling' ? ' selected' : '' ) . '>Mike Findling</option>';
    echo '<option value="chiggins"' . ( $agent == 'chiggins' ? ' selected' : '' ) . '>Chad Higgins</option>';
    echo '</select>';

    // Purchaser

    // Artist

    // Artist approved
    echo '<p>';
    echo '<strong><label for="_timeapp_approved">' . __( 'Approved?', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="checkbox" name="_timeapp_approved" id="_timeapp_approved" value="1" ' . checked( true,  $approved, false ) . ' />';
    echo '<label for="_timeapp_approved">' . __( 'Check when rider has been approved.', 'timeapp' ) . '</label>';
    echo '</p>';

    // Set requirements
    echo '<p>';
    echo '<strong><label for="_timeapp_set_reqs">' . __( 'Set Requirements', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_set_reqs" id="_timeapp_set_reqs" value="' . ( isset( $set_reqs ) && ! empty( $set_reqs ) ? $set_reqs : '' ) . '" />';
    echo '</p>';

    // Notes
    echo '<p>';
    echo '<strong><label for="_timeapp_notes">' . __( 'Notes', 'timeapp' ) . '</label></strong><br />';
    echo '<textarea cols="30" rows="5" name="_timeapp_notes" id="_timeapp_notes">' . $notes . '</textarea>';
    echo '</p>';

    do_action( 'timeapp_play_details_fields', $post_id );
}


/**
 * Save post meta when the save_post action is called
 *
 * @since       1.0.0
 * @param       int $post_id The ID of the post we are saving
 * @global      object $post The post we are saving
 * @return      void
 */
function timeapp_save_play_meta_box( $post_id ) {
    global $post;
    
    // Bail if this isn't the artist post type
    if( ! isset( $post->post_type ) || $post->post_type != 'play' ) return $post_id;

    // Don't process if nonce can't be validated
    if( ! isset( $_POST['timeapp_play_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_play_nonce'], basename( __FILE__ ) ) ) return $post_id;
    
    // Don't process if this is an autosave
    if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) return $post_id;

    // Don't process if this is a revision
    if( $post->post_type == 'revision' ) return $post_id;

    // Don't process if the current user shouldn't be editing this product
    if( ! current_user_can( 'edit_play', $post_id ) ) return $post_id;

    // The fields to save
    $fields = apply_filters( 'timeapp_play_fields_save', array(
        '_timeapp_start_date',
        '_timeapp_end_date',
        '_timeapp_contract_sent',
        '_timeapp_contract_rcvd',
        '_timeapp_promo_sent',
        '_timeapp_promo_rcvd',
        '_timeapp_followed_up',
        '_timeapp_followup_notes',
    ) );

    foreach( $fields as $field ) {
        if( isset( $_POST[$field] ) ) {
            if( is_string( $_POST[$field] ) ) {
                $new = esc_attr( $_POST[$field] );
            } else {
                $new = $_POST[$field];
            }

            $new = apply_filters( 'timeapp_play_save_' . $field, $new );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'timeapp_save_play_meta_box' );


/**
 * Render contact info meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_contact_info_meta_box() {
    global $post;

    $post_id        = $post->ID;
    $first_name     = get_post_meta( $post_id, '_timeapp_first_name', true );
    $last_name      = get_post_meta( $post_id, '_timeapp_last_name', true );
    $email          = get_post_meta( $post_id, '_timeapp_email', true );
    $phone_number   = get_post_meta( $post_id, '_timeapp_phone_number', true );

    // First name
    echo '<p>';
    echo '<strong><label for="_timeapp_first_name">' . __( 'First Name', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_first_name" id="_timeapp_first_name" value="' . ( isset( $first_name ) && ! empty( $first_name ) ? $first_name : '' ) . '" />';
    echo '</p>';
    
    // Last name
    echo '<p>';
    echo '<strong><label for="_timeapp_last_name">' . __( 'Last Name', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_last_name" id="_timeapp_last_name" value="' . ( isset( $last_name ) && ! empty( $last_name ) ? $last_name : '' ) . '" />';
    echo '</p>';
    
    // Email
    echo '<p>';
    echo '<strong><label for="_timeapp_email">' . __( 'Email', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_email" id="_timeapp_email" value="' . ( isset( $email ) && ! empty( $email ) ? $email : '' ) . '" />';
    echo '</p>';
    
    // Phone number
    echo '<p>';
    echo '<strong><label for="_timeapp_phone_number">' . __( 'Phone Number', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_phone_number" id="_timeapp_phone_number" value="' . ( isset( $phone_number ) && ! empty( $phone_number ) ? $phone_number : '' ) . '" />';
    echo '</p>';
    
    do_action( 'timeapp_contact_info_fields', $post_id );

    wp_nonce_field( basename( __FILE__ ), 'timeapp_purchaser_nonce' );
}


/**
 * Render venue info meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_venue_info_meta_box() {
    global $post;

    $post_id        = $post->ID;
    $venue_url      = get_post_meta( $post_id, '_timeapp_venue_url', true );
    $address        = get_post_meta( $post_id, '_timeapp_address', true );
    $city           = get_post_meta( $post_id, '_timeapp_city', true );
    $state          = get_post_meta( $post_id, '_timeapp_state', true );
    $zip            = get_post_meta( $post_id, '_timeapp_zip', true );

    // Venue URL
    echo '<p>';
    echo '<strong><label for="_timeapp_venue_url">' . __( 'Venue URL', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_venue_url" id="_timeapp_venue_url" value="' . ( isset( $venue_url ) && ! empty( $venue_url ) ? $venue_url : '' ) . '" placeholder="' . __( 'http://', 'timeapp' ) . '" />';
    echo '</p>';

    // Address
    echo '<p>';
    echo '<strong><label for="_timeapp_address">' . __( 'Address', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_address" id="_timeapp_address" value="' . ( isset( $address ) && ! empty( $address ) ? $address : '' ) . '" />';
    echo '</p>';

    // City
    echo '<p>';
    echo '<strong><label for="_timeapp_city">' . __( 'City', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_city" id="_timeapp_city" value="' . ( isset( $city ) && ! empty( $city ) ? $city : '' ) . '" />';
    echo '</p>';

    // State
    echo '<p>';
    echo '<strong><label for="_timeapp_state">' . __( 'State', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
    echo '<select name="_timeapp_state" id="_state">';
    echo '<option value=""' .  ( ! isset( $state ) || $state == '' ? ' selected' : '' ) . '>' . __( 'Select State', 'timeapp' ) . '</option>';
    
    $states = timeapp_get_states();
    foreach( $states as $abbr => $name ) {
        echo '<option value="' . $abbr . '"' . ( $state == $abbr ? ' selected' : '' ) . '>' . $name . '</option>';
    }

    echo '</select>';

    // Zip Code
    echo '<p>';
    echo '<strong><label for="_timeapp_zip">' . __( 'Zip Code', 'timeapp' ) . '</label></strong><br />';
    echo '<input type="text" class="regular-text" name="_timeapp_zip" id="_timeapp_zip" value="' . ( isset( $zip ) && ! empty( $zip ) ? $zip : '' ) . '" />';
    echo '</p>';
    
    do_action( 'timeapp_venue_info_fields', $post_id );
}


/**
 * Save post meta when the save_post action is called
 *
 * @since       1.0.0
 * @param       int $post_id The ID of the post we are saving
 * @global      object $post The post we are saving
 * @return      void
 */
function timeapp_save_purchaser_meta_box( $post_id ) {
    global $post;

    // Bail if this isn't the purchaser post type
    if( ! isset( $post->post_type ) || $post->post_type != 'purchaser' ) return $post_id;

    // Don't process if nonce can't be validated
    if( ! isset( $_POST['timeapp_purchaser_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_purchaser_nonce'], basename( __FILE__ ) ) ) return $post_id;

    // Don't process if this is an autosave
    if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) return $post_id;

    // Don't process if this is a revision
    if( $post->post_type == 'revision' ) return $post_id;

    // Don't process if the current user shouldn't be editing this product
    if( ! current_user_can( 'edit_purchaser', $post_id ) ) return $post_id;

    // The fields to save
    $fields = apply_filters( 'timeapp_purchaser_fields_save', array(
        '_timeapp_first_name',
        '_timeapp_last_name',
        '_timeapp_email',
        '_timeapp_phone_number',
        '_timeapp_venue_url',
        '_timeapp_address',
        '_timeapp_city',
        '_timeapp_state',
        '_timeapp_zip'
    ) );

    foreach( $fields as $field ) {
        if( isset( $_POST[$field] ) ) {
            if( is_string( $_POST[$field] ) ) {
                $new = esc_attr( $_POST[$field] );
            } else {
                $new = $_POST[$field];
            }

            $new = apply_filters( 'timeapp_purchaser_save_' . $field, $new );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'timeapp_save_purchaser_meta_box' );


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

    wp_nonce_field( basename( __FILE__ ), 'timeapp_artist_nonce' );
}


/**
 * Save post meta when the save_post action is called
 *
 * @since       1.0.0
 * @param       int $post_id The ID of the post we are saving
 * @global      object $post The post we are saving
 * @return      void
 */
function timeapp_save_artist_meta_box( $post_id ) {
    global $post;
    
    // Bail if this isn't the artist post type
    if( ! isset( $post->post_type ) || $post->post_type != 'artist' ) return $post_id;

    // Don't process if nonce can't be validated
    if( ! isset( $_POST['timeapp_artist_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_artist_nonce'], basename( __FILE__ ) ) ) return $post_id;
    
    // Don't process if this is an autosave
    if( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) return $post_id;

    // Don't process if this is a revision
    if( $post->post_type == 'revision' ) return $post_id;

    // Don't process if the current user shouldn't be editing this product
    if( ! current_user_can( 'edit_artist', $post_id ) ) return $post_id;

    // The fields to save
    $fields = apply_filters( 'timeapp_artist_fields_save', array(
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

            $new = apply_filters( 'timeapp_artist_save_' . $field, $new );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'timeapp_save_artist_meta_box' );
