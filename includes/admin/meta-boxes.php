<?php
/**
 * Meta boxes
 *
 * @package     TimeApp\Admin\MetaBoxes
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


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
	add_meta_box( 'play_details', __( 'Play Details', 'timeapp' ), 'timeapp_render_play_details_meta_box', 'play', 'normal', 'default' );
	add_meta_box( 'financials', __( 'Financials', 'timeapp' ), 'timeapp_render_financials_meta_box', 'play', 'normal', 'default' );
	add_meta_box( 'communications', __( 'Communications', 'timeapp' ), 'timeapp_render_communications_meta_box', 'play', 'normal', 'default' );

	// Purchaser post type
	add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'purchaser', 'normal', 'high' );
	add_meta_box( 'actions_bottom', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'purchaser', 'normal', 'low' );
	add_meta_box( 'contact_info', __( 'Contact Information', 'timeapp' ), 'timeapp_render_contact_info_meta_box', 'purchaser', 'normal', 'default' );
	add_meta_box( 'contract_signatory', __( 'Contract Signatory', 'timeapp' ), 'timeapp_render_contract_signatory_meta_box', 'purchaser', 'normal', 'default' );
	add_meta_box( 'venue_info', __( 'Venue Information', 'timeapp' ), 'timeapp_render_venue_info_meta_box', 'purchaser', 'normal', 'default' );
	add_meta_box( 'mailing_address', __( 'Mailing Address', 'timeapp' ), 'timeapp_render_mailing_address_meta_box', 'purchaser', 'normal', 'default' );

	// Artist post type
	add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'artist', 'normal', 'high' );
	add_meta_box( 'actions_bottom', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'artist', 'normal', 'low' );
	add_meta_box( 'artist_details', __( 'Artist Details', 'timeapp' ), 'timeapp_render_artist_details_meta_box', 'artist', 'normal', 'default' );

	// Agent post type
	add_meta_box( 'actions_top', __( 'Actions', 'timeapp' ), 'timeapp_render_actions_meta_box', 'agent', 'normal', 'high' );
	add_meta_box( 'agent_details', __( 'Agent Details', 'timeapp' ), 'timeapp_render_agent_details_meta_box', 'agent', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'timeapp_add_meta_boxes' );


/**
 * Render our actions meta boxes
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_actions_meta_box() {
	global $post;

	$post_type = get_post_type();

	echo '<a class="button button-primary timeapp-save">' . sprintf( __( 'Save %s', 'timeapp' ), ucwords( $post_type ) ) . '</a>';
	//submit_button( sprintf( __( 'Save %s', 'timeapp' ), ucwords( $post_type ) ), 'primary timeapp-save', 'publish', false );
	do_action( 'timeapp_meta_box_' . $post_type . '_actions' );

	echo '<div class="timeapp-action-delete">';
	echo '<a class="submitdelete" href="' . get_delete_post_link() . '">' . __( 'Move to Trash', 'timeapp' ) . '</a>';
	echo '</div>';
}


/**
 * Add Send PDF button on 'play' post type
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_add_send_pdf_button() {
	//echo '<a href="' . wp_nonce_url( add_query_arg( array( 'timeapp-action' => 'generate_pdf' ) ), 'generate-pdf', 'pdf-nonce' ) . '" class="button button-secondary">' . __( 'Generate PDF', 'timeapp' ) . '</a>';
	echo '<a class="button button-secondary colorbox">' . ( timeapp()->settings->get_option( 'enable_debugging', false ) ? __( 'Preview & Display PDF', 'timeapp' ) : __( 'Preview & Send PDF', 'timeapp' ) ) . '</a>';
}
add_action( 'timeapp_meta_box_play_actions', 'timeapp_add_send_pdf_button' );


/**
 * Render calendar info meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_calendar_info_meta_box() {
	global $post;

	$post_id	= $post->ID;
	$start_date = get_post_meta( $post_id, '_timeapp_start_date', true );
	$start_date = ( isset( $start_date ) && ! empty( $start_date ) ? date( 'm/d/Y g:i a', strtotime( $start_date ) ) : '' );
	$end_date   = get_post_meta( $post_id, '_timeapp_end_date', true );
	$end_date   = ( isset( $end_date ) && ! empty( $end_date ) ? date( 'm/d/Y g:i a', strtotime( $end_date ) ) : '' );

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

	$post_id       = $post->ID;
	$contract_sent = get_post_meta( $post_id, '_timeapp_contract_sent', true );
	$last_sent     = ( isset( $contract_sent ) && is_array( $contract_sent ) ? date( 'm/d/Y g:i a', strtotime( end( $contract_sent ) ) ) : '' );

	if ( is_array( $contract_sent ) ) {
		array_pop( $contract_sent );
		$contract_sent = array_reverse( $contract_sent );
	} else {
		$contract_sent = '';
	}

	$contract_rcvd  = get_post_meta( $post_id, '_timeapp_contract_rcvd', true );
	$contract_rcvd  = ( isset( $contract_rcvd ) && ! empty( $contract_rcvd ) ? date( 'm/d/Y g:i a', strtotime( $contract_rcvd ) ) : '' );
	$promo_sent     = get_post_meta( $post_id, '_timeapp_promo_sent', true );
	$promo_sent     = ( isset( $promo_sent ) && ! empty( $promo_sent ) ? date( 'm/d/Y g:i a', strtotime( $promo_sent ) ) : '' );
	$followed_up    = get_post_meta( $post_id, '_timeapp_followed_up', true ) ? true : false;
	$followup_notes = get_post_meta( $post_id, '_timeapp_followup_notes', true );

	// Contract sent
	echo '<p class="timeapp-half">';
	echo '<strong><label for="_timeapp_contract_sent">' . __( 'Contract Sent', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_contract_sent" id="_timeapp_contract_sent" value="' . $last_sent . '" /><br />';

	if ( is_array( $contract_sent ) ) {
		echo '<a class="timeapp-contract-log-toggle">' . __( 'Toggle Contract Log', 'timeapp' ) . '</a>';
		echo '<span class="timeapp-contract-log">';

		$odd = true;

		foreach ( $contract_sent as $item ) {
			if ( $item ) {
				if ( $odd ) {
					echo '<span class="timeapp-contract-log-item timeapp-contract-log-item-odd">' . $item . '</span>';
				} else {
					echo '<span class="timeapp-contract-log-item">' . $item . '</span>';
				}

				$odd = ! $odd;
			}
		}
		echo '</span>';
	}
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

	echo '<div class="timeapp-clear"></div>';

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
	//$type           = get_post_meta( $post_id, '_timeapp_type', true );
	$agent          = get_post_meta( $post_id, '_timeapp_agent', true );
	$purchaser      = get_post_meta( $post_id, '_timeapp_purchaser', true );
	$artist         = get_post_meta( $post_id, '_timeapp_artist', true );
	$artist         = get_post( $artist );
	$play_emails    = get_post_meta( $post_id, '_timeapp_play_emails', true );
	$play_emails    = ( is_array( $play_emails ) ? implode( "\n", $play_emails ) : '' );
	$contract_terms = get_post_meta( $artist->ID, '_timeapp_contract_terms', true );
	$contract_terms = ( $contract_terms && $contract_terms != '' ? $contract_terms . ' ' . __( 'has', 'timeapp' ) : __( 'contract terms have', 'timeapp' ) );
	$approved       = get_post_meta( $post_id, '_timeapp_approved', true ) ? true : false;
	$set_reqs       = get_post_meta( $post_id, '_timeapp_set_reqs', true );
	$notes          = get_post_meta( $post_id, '_timeapp_notes', true );


	// Status
	echo '<p>';
	echo '<strong><label for="_timeapp_status">' . __( 'Event Status', 'timeapp' ) . '</label></strong><br />';
	echo '<select class="timeapp-select2" name="_timeapp_status" id="_timeapp_status">';
	echo '<option value="hold"' . ( ! isset( $status ) || $status == 'hold' ? ' selected' : '' ) . '>' . __( 'Hold', 'timeapp' ) . '</option>';
	echo '<option value="contracted"' . ( $status == 'contracted' ? ' selected' : '' ) . '>' . __( 'Contracted', 'timeapp' ) . '</option>';
	echo '<option value="cancelled"' . ( $status == 'cancelled' ? ' selected' : '' ) . '>' . __( 'Cancelled', 'timeapp' ) . '</option>';
	echo '</select>';

	// Type
	// Removed in 2.1.0
	/*echo '<p>';
	echo '<strong><label for="_timeapp_type">' . __( 'Event Type', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<select class="timeapp-select2" name="_timeapp_type" id="_timeapp_type">';
	echo '<option value="club"' . ( ! isset( $type ) || $type == 'club' ? ' selected' : '' ) . '>' . __( 'Club', 'timeapp' ) . '</option>';
	echo '<option value="event"' . ( $type == 'event' ? ' selected' : '' ) . '>' . __( 'Event', 'timeapp' ) . '</option>';
	echo '</select>';*/

	// Agent
	echo '<p>';
	echo '<strong><label for="_timeapp_agent">' . __( 'Agent', 'timeapp' ) . '</label></strong><br />';
	echo '<select class="timeapp-select2" name="_timeapp_agent" id="_timeapp_agent">';

	$agents = timeapp_get_agents( 'internal' );
	foreach ( $agents as $id => $name ) {
		echo '<option value="' . $id  . '"' . ( $agent == $id ? ' selected' : '' ) . '>' . $name . '</option>';
	}

	echo '</select>';

	// Purchaser
	echo '<p>';
	echo '<strong><label for="_timeapp_purchaser">' . __( 'Purchaser', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<select class="timeapp-select2" name="_timeapp_purchaser" id="_timeapp_purchaser">';

	$purchasers = timeapp_get_purchasers();
	foreach ( $purchasers as $id => $name ) {
		echo '<option value="' . $id . '"' . ( $purchaser == $id ? ' selected' : '' ) . '>' . $name . '</option>';
	}

	echo '</select>';

	// Artist
	echo '<p>';
	echo '<strong><label for="_timeapp_artist">' . __( 'Artist', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<select class="timeapp-select2" name="_timeapp_artist" id="_artist">';

	$artists = timeapp_get_artists();
	foreach ( $artists as $id => $name ) {
		echo '<option value="' . $id . '"' . ( $artist->ID == $id ? ' selected' : '' ) . '>' . $name . '</option>';
	}

	echo '</select>';

	// Additional contacts
	echo '<p>';
	echo '<strong><label for="_timeapp_play_emails">' . __( 'Additional Emails', 'timeapp' ) . '</label></strong><br />';
	echo '<textarea name="_timeapp_play_emails" id="_timeapp_play_emails" rows="5">' . $play_emails . '</textarea><br />';
	echo '<label for="_timeapp_play_emails">' . __( 'Enter additional emails for this play, one per line.', 'timeapp' ) . '</label>';
	echo '</p>';

	// Contract approved
	echo '<p>';
	echo '<strong><label for="_timeapp_approved">' . __( 'Approved?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_approved" id="_timeapp_approved" value="1" ' . checked( true,  $approved, false ) . ' />';
	echo '<label for="_timeapp_approved">' . sprintf( __( 'Check when %s been approved.', 'timeapp' ), $contract_terms ) . '</label>';
	echo '</p>';

	// Set requirements
	echo '<p>';
	echo '<strong><label for="_timeapp_set_reqs">' . __( 'Set Requirements', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_set_reqs" id="_timeapp_set_reqs" value="' . ( isset( $set_reqs ) && ! empty( $set_reqs ) ? $set_reqs : '' ) . '" />';
	echo '</p>';

	// Notes
	echo '<p>';
	echo '<strong><label for="_timeapp_notes">' . __( 'Additional Terms', 'timeapp' ) . '</label></strong><br />';
	echo '<textarea cols="30" rows="5" name="_timeapp_notes" id="_timeapp_notes">' . $notes . '</textarea>';
	echo '</p>';

	do_action( 'timeapp_play_details_fields', $post_id );
}


/**
 * Render financials meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_financials_meta_box() {
	global $post;

	$post_id            = $post->ID;
	$guarantee          = get_post_meta( $post_id, '_timeapp_guarantee', true );
	$bonus              = get_post_meta( $post_id, '_timeapp_bonus', true ) ? true : false;
	$bonus_css          = ( $bonus ? '' : ' style="display: none;"' );
	$bonus_details      = get_post_meta( $post_id, '_timeapp_bonus_details', true );
	$deposit            = get_post_meta( $post_id, '_timeapp_deposit', true ) ? true : false;
	$deposit_css        = ( $deposit ? '' : ' style="display: none;"' );
	// Add deposit payments
	$deposit1_amt       = get_post_meta( $post_id, '_timeapp_deposit1_amt', true );
	$deposit1_date      = get_post_meta( $post_id, '_timeapp_deposit1_date', true );
	$deposit1_date      = ( isset( $deposit1_date ) && ! empty( $deposit1_date ) ? date( 'm/d/Y g:i a', strtotime( $deposit1_date ) ) : '' );
	$deposit1_paid      = get_post_meta( $post_id, '_timeapp_deposit1_paid', true ) ? true : false;
	$accommodations     = get_post_meta( $post_id, '_timeapp_accommodations', true );
	$production         = get_post_meta( $post_id, '_timeapp_production', true ) ? true : false;
	$production_css     = ( $production ? ' style="display: none;"' : '' );
	$production_cost    = get_post_meta( $post_id, '_timeapp_production_cost', true );
	$date_paid          = get_post_meta( $post_id, '_timeapp_date_paid', true );
	$split              = get_post_meta( $post_id, '_timeapp_split_comm', true ) ? true : false;
	$split_css          = ( $split ? '' : ' style="display: none;"' );
	$split_perc         = get_post_meta( $post_id, '_timeapp_split_perc', true );
	$split_agent        = get_post_meta( $post_id, '_timeapp_split_agent', true );
	$split_paid         = get_post_meta( $post_id, '_timeapp_split_paid', true );
	$artist             = get_post_meta( $post_id, '_timeapp_artist', true );
	$artist             = get_post( $artist );
	$commission_rate    = get_post_meta( $artist->ID, '_timeapp_commission', true );
	$commission_display = timeapp_get_commission( $guarantee, $production_cost, $commission_rate, $split_perc );

	// Gross guarantee
	echo '<p>';
	echo '<strong><label for="_timeapp_guarantee">' . __( 'Gross Guarantee', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_guarantee" id="_timeapp_guarantee" value="' . ( isset( $guarantee ) && ! empty( $guarantee ) ? $guarantee : '' ) . '" placeholder="' . __( '$', 'timeapp' ) . '" />';
	echo '</p>';

	// Performance bonus
	echo '<p>';
	echo '<strong><label for="_timeapp_bonus">' . __( 'Performance Bonus', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_bonus" id="_timeapp_bonus" value="1" ' . checked( true,  $bonus, false ) . ' />';
	echo '<label for="_timeapp_bonus">' . __( 'Check if a performance bonus has been promised.', 'timeapp' ) . '</label>';
	echo '</p>';

	// Bonus details
	echo '<p' . $bonus_css . '>';
	echo '<strong><label for="_timeapp_bonus_details">' . __( 'Bonus Details', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_bonus_details" id="_timeapp_bonus_details" value="' . ( isset( $bonus_details ) && ! empty( $bonus_details ) ? $bonus_details : '' ) . '" />';
	echo '</p>';

	// Deposit
	echo '<p>';
	echo '<strong><label for="_timeapp_deposit">' . __( 'Deposit', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_deposit" id="_timeapp_deposit" value="1" ' . checked( true, $deposit, false ) . ' />';
	echo '<label for="_timeapp_deposit">' . __( 'Check if a deposit is required.', 'timeapp' ) . '</label>';
	echo '</p>';

	echo '<div id="timeapp-deposits" ' . $deposit_css . '>';

	// Deposit 1
	echo '<p class="timeapp-third">';
	echo '<strong><label for="_timeapp_deposit1_amt">' . __( 'Amount', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" name="_timeapp_deposit1_amt" id="_timeapp_deposit1_amt" value="' . ( isset( $deposit1_amt ) && ! empty( $deposit1_amt ) ? $deposit1_amt : '' ) . '" placeholder="' . __( '$', 'timeapp' ) . '" />';
	echo '</p>';

	echo '<p class="timeapp-third">';
	echo '<strong><label for="_timeapp_deposit1_date">' . __( 'Due Date', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="timeapp-datetime" name="_timeapp_deposit1_date" id="_timeapp_deposit1_date" value="' . $deposit1_date . '" />';
	echo '</p>';

	echo '<p class="timeapp-third">';
	echo '<strong><label for="_timeapp_deposit1_paid">' . __( 'Paid?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_deposit1_paid" id="_timeapp_deposit1_paid" value="1" ' . checked( true, $deposit1_paid, false ) . ' />';
	echo '<label for="_timeapp_deposit1_paid">' . __( 'Check if this deposit has been paid.', 'timeapp' ) . '</label>';
	echo '</p>';

	echo '<div class="timeapp-clear"></div>';

	echo '</div>';

	// Accommodations
	echo '<p>';
	echo '<strong><label for="_timeapp_accommodations">' . __( 'Accommodations', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_accommodations" id="_timeapp_accommodations" value="' . ( isset( $accommodations ) && ! empty( $accommodations ) ? $accommodations : '' ) . '" />';
	echo '</p>';

	// Production provided
	echo '<p>';
	echo '<strong><label for="_timeapp_production">' . __( 'Production Provided', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_production" id="_timeapp_production" value="1" ' . checked( true,  $production, false ) . ' />';
	echo '<label for="_timeapp_production">' . __( 'Check if purchaser is providing production.', 'timeapp' ) . '</label>';
	echo '</p>';

	// Production cost
	echo '<p' . $production_css . '>';
	echo '<strong><label for="_timeapp_production_cost">' . __( 'Production Cost', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_production_cost" id="_timeapp_production_cost" value="' . ( isset( $production_cost ) && ! empty( $production_cost ) ? $production_cost : '' ) . '" placeholder="' . __( '$', 'timeapp' ) . '" />';
	echo '</p>';

	// Commission
	echo '<p>';
	echo '<strong><label for="_timeapp_commission_display">' . __( 'Commission Due', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" disabled="disabled" id="_timeapp_commission_display" value="' . $commission_display . '" />';
	echo '</p>';

	// Date paid
	echo '<p>';
	echo '<strong><label for="_timeapp_date_paid">' . __( 'Date Commission Received', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text timeapp-datetime" name="_timeapp_date_paid" id="_timeapp_date_paid" value="' . $date_paid . '" />';
	echo '</p>';

	// Split commission
	echo '<p>';
	echo '<strong><label for="_timeapp_split_comm">' . __( 'Split Commission', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_split_comm" id="_timeapp_split_comm" value="1" ' . checked( true,  $split, false ) . ' />';
	echo '<label for="_timeapp_split_comm">' . __( 'Check if commission is being split.', 'timeapp' ) . '</label>';
	echo '</p>';

	echo '<div' . $split_css . '>';

	// Split Agent
	echo '<p>';
	echo '<strong><label for="_timeapp_split_agent">' . __( 'Split Agent', 'timeapp' ) . '</label></strong><br />';
	echo '<select class="timeapp-select2" name="_timeapp_split_agent" id="_timeapp_split_agent">';

	$agents = timeapp_get_agents( 'external' );
	foreach ( $agents as $id => $name ) {
		echo '<option value="' . $id  . '"' . ( $split_agent == $id ? ' selected' : '' ) . '>' . $name . '</option>';
	}

	echo '</select>';

	// Split percentage
	echo '<p>';
	echo '<strong><label for="_timeapp_split_perc">' . __( 'Percentage Paid to Agent', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_split_perc" id="_timeapp_split_perc" value="' . ( isset( $split_perc ) && ! empty( $split_perc ) ? $split_perc : '50' ) . '" placeholder="' . __( '%', 'timeapp' ) . '" />';
	echo '</p>';

	// Split paid?
	echo '<p>';
	echo '<strong><label for="_timeapp_split_paid">' . __( 'Split Commission Paid?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_split_paid" id="_timeapp_split_paid" value="1" ' . checked( true, $split_paid, false ) . ' />';
	echo '<label for="_timeapp_split_paid">' . __( 'Check if the split commission has been paid.', 'timeapp' ) . '</label>';
	echo '</p>';

	echo '</div>';

	do_action( 'timeapp_financials_fields', $post_id );
}


/**
 * Colorbox holder
 *
 * @since       1.0.0
 * @param       int $post_id The ID of this post
 * @return      void
 */
function timeapp_colorbox_holder( $post_id ) {
	echo '<div class="timeapp-colorbox-holder">';
	echo '<div id="timeapp-pdf-preview">';

	echo '<div class="timeapp-pdf-preview-title">' . __( 'Please review the following contract details.<br />If everything looks correct, hit the \'Send Email\' button at the bottom.', 'timeapp' ) . '</div>';

	$textualizer     = new TimeApp_Textualizer();
	$play            = get_post( $post_id );
	$effective_date  = date( 'F jS, Y', strtotime( $play->post_date ) );
	$artist          = get_post_meta( $post_id, '_timeapp_artist', true );
	$artist          = get_post( $artist );
	$artist_email    = get_post_meta( $artist->ID, '_timeapp_artist_email', true );
	$artist_email    = ( $artist_email ? $artist_email : __( 'No email specified', 'timeapp' ) );
	$contract_terms  = get_post_meta( $artist->ID, '_timeapp_contract_terms', true );
	$purchaser       = get_post_meta( $post_id, '_timeapp_purchaser', true );
	$purchaser       = get_post( $purchaser );
	$purchaser_email = get_post_meta( $purchaser->ID, '_timeapp_email', true );
	$purchaser_email = ( $purchaser_email ? $purchaser_email : __( 'No email specified', 'timeapp' ) );
	$address         = get_post_meta( $purchaser->ID, '_timeapp_address', true );
	$city            = get_post_meta( $purchaser->ID, '_timeapp_city', true );
	$state           = get_post_meta( $purchaser->ID, '_timeapp_state', true );
	$zip_code        = get_post_meta( $purchaser->ID, '_timeapp_zip', true );
	$address         = $address . ', ' . $city . ', ' . $state . ' ' . $zip_code;
	$address         = trim( $address );
	$phone           = get_post_meta( $purchaser->ID, '_timeapp_phone_number', true );
	$email           = get_post_meta( $purchaser->ID, '_timeapp_email', true );
	$start_date      = get_post_meta( $post_id, '_timeapp_start_date', true );
	$end_date        = get_post_meta( $post_id, '_timeapp_end_date', true );
	$compensation    = get_post_meta( $post_id, '_timeapp_guarantee', true );
	$bonus           = get_post_meta( $post_id, '_timeapp_bonus', true ) ? true : false;
	$bonus_details   = get_post_meta( $post_id, '_timeapp_bonus_details', true );
	$deposit1_date   = get_post_meta( $post_id, '_timeapp_deposit1_date', true );
	$deposit1_amt    = get_post_meta( $post_id, '_timeapp_deposit1_amt', true );
	$balance         = $compensation - (int) $deposit1_amt;
	$production_cost = get_post_meta( $post_id, '_timeapp_production_cost', true );
	$production      = get_post_meta( $post_id, '_timeapp_production', true ) ? 'Venue to provide production' : 'Artist to provide production';
	$notes           = get_post_meta( $post_id, '_timeapp_notes', true );
	$contract_terms  = get_post_meta( $artist->ID, '_timeapp_contract_terms', true );
	$accommodations  = get_post_meta( $post_id, '_timeapp_accommodations', true );
	$contact_fname   = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );
	$contact_lname   = get_post_meta( $purchaser->ID, '_timeapp_last_name', true );
	$set_reqs        = get_post_meta( $post_id, '_timeapp_set_reqs', true );
	$approved        = get_post_meta( $post_id, '_timeapp_approved', true ) ? true : false;
	$tax_id          = get_post_meta( $artist->ID,'_timeapp_tax_id', true );
	$signatory       = get_post_meta( $purchaser->ID, '_timeapp_signatory', true ) ? true : false;
	$status          = get_post_meta( $post_id, '_timeapp_status', true );
	$payable_to      = get_post_meta( $artist->ID, '_timeapp_payable_to', true );
	$contact_name    = '';
	$purchaser_cc    = get_post_meta( $purchaser->ID, '_timeapp_additional_emails', true );
	$purchaser_cc    = ( is_array( $purchaser_cc ) ? $purchaser_cc : array() );
	$play_cc         = get_post_meta( $play->ID, '_timeapp_play_emails', true );
	$play_cc         = ( is_array( $play_cc ) ? $play_cc : array() );
	$cc_emails       = array_merge( $play_cc, $purchaser_cc );

	// Is a contact first name specified?
	if ( $contact_fname && $contact_fname != '' ) {
		$contact_name .= $contact_fname;
	}

	// Is a contact last name specified?
	if ( $contact_lname && $contact_lname != '' ) {
		if ( $contact_name != '' ) {
			$contact_name .= ' ';
		}

		$contact_name .= $contact_lname;
	}

	if ( $signatory ) {
		$purchaser_fname = get_post_meta( $purchaser->ID, '_timeapp_signatory_first_name', true );
		$purchaser_lname = get_post_meta( $purchaser->ID, '_timeapp_signatory_last_name', true );

		$contact_name = $purchaser_fname . ( $purchaser_lname ? ' ' . $purchaser_lname : '' );
	}

	$contact_name = ( $contact_name != $purchaser->post_title ? $contact_name . ', ' . $purchaser->post_title : $contact_name );

	$date  = '';
	$terms = '';

	// Setup the date
	if ( $start_date && $start_date != '' ) {
		$date .= $start_date;
	}

	if ( $end_date && $end_date != '' ) {
		if ( $date != '' ) {
			$date .= ' - ';
		}

		$date .= $end_date;
	}

	// Setup the terms
	if ( $approved && $contract_terms && $contract_terms != '' ) {
		$terms .= $contract_terms;
	}

	if ( $notes && $notes != '' ) {
		if ( $terms != '' ) {
			$terms .= '<br />';
		}

		$terms .= $notes;
	}

	$terms .= ( $terms != '' ? '<br />' : '' ) . sprintf( __( 'See attached "%s RIDER"', 'timeapp' ), strtoupper( $artist->post_title ) );
	?>
		<table class="timeapp-pdf-preview wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th><?php _e( 'Field', 'timeapp' ); ?></th>
					<th><?php _e( 'Value', 'timeapp' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php _e( 'Artist', 'timeapp' ); ?></td>
					<td><?php echo $artist->post_title . ' &lt;' . $artist_email . '&gt;'; ?></td>
				</tr>
				<?php if ( $payable_to && $payable_to != '' ) { ?>
					<tr>
						<td><?php _e( 'Payable To', 'timeapp' ); ?></td>
						<td><?php echo $payable_to; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td><?php _e( 'Tax ID', 'timeapp' ); ?></td>
					<td><?php echo $tax_id; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Purchaser', 'timeapp' ); ?></td>
					<td><?php echo $contact_name . ' &lt;' . $purchaser_email . '&gt;'; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Date(s) of Engagement', 'timeapp' ); ?></td>
					<td><?php echo $date; ?></td>
				</tr>
				<?php if ( $set_reqs && $set_reqs != '' ) { ?>
					<tr>
						<td><?php _e( 'Set Requirements', 'timeapp' ); ?></td>
						<td><?php echo $set_reqs; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td><?php _e( 'Compensation', 'timeapp' ); ?></td>
					<td><?php echo timeapp_format_price( $compensation ); ?></td>
				</tr>
				<?php if ( $bonus && $bonus_details && $bonus_details != '' ) { ?>
					<tr>
						<td><?php _e( 'Bonus Details', 'timeapp' ); ?></td>
						<td><?php echo $bonus_details; ?></td>
					</tr>
				<?php } ?>
				<?php if ( $deposit1_amt && $deposit1_amt != '' ) { ?>
					<?php $deposit1_date  = ( $deposit1_date ) ? ' by ' . date( 'F jS, Y', strtotime( $deposit1_date ) ) : ' with signed contract'; ?>
					<tr>
						<td><?php _e( 'Deposit', 'timeapp' ); ?></td>
						<td><?php echo timeapp_format_price( $deposit1_amt ) . $deposit1_date; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td><?php _e( 'Production', 'timeapp' ); ?></td>
					<td><?php echo $production; ?></td>
				</tr>
				<?php if ( $terms && $terms != '' ) { ?>
					<tr>
						<td><?php _e( 'Additional Terms', 'timeapp' ); ?></td>
						<td><?php echo $terms; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td><?php _e( 'Accommodations', 'timeapp' ); ?></td>
					<td><?php echo ( $accommodations ? $accommodations : __( 'None', 'timeapp' ) ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Event Status', 'timeapp' ); ?></td>
					<td><?php echo '<span class="timeapp-status-' . $status . '">' . ucfirst( $status ) . '</span>'; ?></td>
				</tr>
				<?php if( count( $cc_emails ) > 0 ) { ?>
					<tr>
						<td colspan="2" style="color: #32373c; border-top: 1px solid #e1e1e1; border-bottom: 1px solid #e1e1e1"><strong><?php _e( 'Emails regarding this play will also be sent to the following address(es):', 'timeapp' ); ?></strong></td>
					</tr>
					<tr>
						<td colspan="2" style="color: #555; font-weight: normal">
							<ul class="cc-list">
							<?php
							foreach( $cc_emails as $email ) {
								echo '<li>' . $email . '</li>';
							}
							?>
							</ul>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<br />
	<?php

	$label = timeapp()->settings->get_option( 'enable_debugging', false ) ? __( 'Display PDF', 'timeapp' ) : __( 'Send Email', 'timeapp' );
	echo '<a href="' . wp_nonce_url( add_query_arg( array( 'timeapp-action' => 'generate_pdf' ) ), 'generate-pdf', 'pdf-nonce' ) . '" class="button button-primary">' . $label . '</a>';
	echo '<a onClick="jQuery.colorbox.close(); return false;" class="button button-secondary">' . __( 'Return to Editor', 'timeapp' ) . '</a>';

	echo '</div>';
	echo '</div>';
}
add_action( 'timeapp_financials_fields', 'timeapp_colorbox_holder' );


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
	if ( ! isset( $post->post_type ) || $post->post_type != 'play' ) {
		return $post_id;
	}

	// Don't process if nonce can't be validated
	if ( ! isset( $_POST['timeapp_play_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_play_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Don't process if this is an autosave
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// Don't process if this is a revision
	if ( $post->post_type == 'revision' ) {
		return $post_id;
	}

	// Don't process if the current user shouldn't be editing this
	if ( ! current_user_can( 'edit_play', $post_id ) ) {
		return $post_id;
	}

	// The fields to save
	$fields = apply_filters( 'timeapp_play_fields_save', array(
		'_timeapp_start_date',
		'_timeapp_end_date',
//		'_timeapp_contract_sent',
		'_timeapp_contract_rcvd',
		'_timeapp_promo_sent',
		'_timeapp_followed_up',
		'_timeapp_followup_notes',
		'_timeapp_status',
//		'_timeapp_type',
		'_timeapp_agent',
		'_timeapp_purchaser',
		'_timeapp_artist',
		'_timeapp_play_emails',
		'_timeapp_approved',
		'_timeapp_set_reqs',
		'_timeapp_notes',
		'_timeapp_guarantee',
		'_timeapp_bonus',
		'_timeapp_bonus_details',
		'_timeapp_deposit',
		'_timeapp_deposit1_amt',
		'_timeapp_deposit1_date',
		'_timeapp_deposit1_paid',
		'_timeapp_accommodations',
		'_timeapp_production',
		'_timeapp_production_cost',
		'_timeapp_date_paid',
		'_timeapp_split_comm',
		'_timeapp_split_perc',
		'_timeapp_split_agent',
		'_timeapp_split_paid'
	) );

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			if ( $field == '_timeapp_play_emails' ) {
				$emails = array_map( 'trim', explode( "\n", $_POST[ $field ] ) );
				$emails = array_unique( $emails );
				$emails = array_map( 'sanitize_text_field', $emails );

				foreach ( $emails as $id => $email ) {
					if ( ! is_email( $email ) ) {
						unset( $emails[ $id ] );
					}
				}

				$new = $emails;
			} else {
				if ( is_string( $_POST[ $field ] ) ) {
					$new = esc_attr( $_POST[ $field ] );
				} else {
					$new = $_POST[ $field ];
				}
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

	$post_id                  = $post->ID;
	$first_name               = get_post_meta( $post_id, '_timeapp_first_name', true );
	$last_name                = get_post_meta( $post_id, '_timeapp_last_name', true );
	$email                    = get_post_meta( $post_id, '_timeapp_email', true );
	$enable_additional_emails = get_post_meta( $post_id, '_timeapp_enable_additional_emails', true ) ? true : false;
	$additional_emails        = get_post_meta( $post_id, '_timeapp_additional_emails', true );
	$additional_emails        = ( is_array( $additional_emails ) ? implode( "\n", $additional_emails ) : '' );
	$phone_number             = get_post_meta( $post_id, '_timeapp_phone_number', true );
	$signatory                = get_post_meta( $post_id, '_timeapp_signatory', true ) ? true : false;

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

	// Additional contacts
	echo '<p>';
	echo '<strong><label for="_timeapp_enable_additional_emails">' . __( 'Additional Emails?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_enable_additional_emails" id="_timeapp_enable_additional_emails" value="1" ' . checked( true, $enable_additional_emails, false ) . ' />';
	echo '<label for="_timeapp_enable_additional_emails">' . __( 'Check to add additonal contact/CC email addresses for this purchaser.', 'timeapp' ) . '</label>';
	echo '<div class="timeapp-additional-emails">';
	echo '<textarea name="_timeapp_additional_emails" id="_timeapp_additional_emails" rows="5">' . $additional_emails . '</textarea><br />';
	echo '<label for="_timeapp_additional_emails">' . __( 'Enter additional emails for this purchaser, one per line.', 'timeapp' ) . '</label>';
	echo '</div>';
	echo '</p>';

	// Phone number
	echo '<p>';
	echo '<strong><label for="_timeapp_phone_number">' . __( 'Phone Number', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_phone_number" id="_timeapp_phone_number" value="' . ( isset( $phone_number ) && ! empty( $phone_number ) ? $phone_number : '' ) . '" />';
	echo '</p>';

	// Different signatory?
	echo '<p>';
	echo '<strong><label for="_timeapp_signatory">' . __( 'Different Signatory?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_signatory" id="_timeapp_signatory" value="1" ' . checked( true,  $signatory, false ) . ' />';
	echo '<label for="_timeapp_signatory">' . __( 'Check if Contract Signatory is different than Contact.', 'timeapp' ) . '</label>';
	echo '</p>';

	do_action( 'timeapp_contact_info_fields', $post_id );

	wp_nonce_field( basename( __FILE__ ), 'timeapp_purchaser_nonce' );
}


/**
 * Render contract signatory meta box
 *
 * @since       1.0.9
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_contract_signatory_meta_box() {
	global $post;

	$post_id    = $post->ID;
	$first_name = get_post_meta( $post_id, '_timeapp_signatory_first_name', true );
	$last_name  = get_post_meta( $post_id, '_timeapp_signatory_last_name', true );

	// First name
	echo '<p>';
	echo '<strong><label for="_timeapp_signatory_first_name">' . __( 'First Name', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_signatory_first_name" id="_timeapp_signatory_first_name" value="' . ( isset( $first_name ) && ! empty( $first_name ) ? $first_name : '' ) . '" />';
	echo '</p>';

	// Last name
	echo '<p>';
	echo '<strong><label for="_timeapp_signatory_last_name">' . __( 'Last Name', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_signatory_last_name" id="_timeapp_signatory_last_name" value="' . ( isset( $last_name ) && ! empty( $last_name ) ? $last_name : '' ) . '" />';
	echo '</p>';

	do_action( 'timeapp_contract_signatory_fields', $post_id );
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

	$post_id     = $post->ID;
	$venue_url   = get_post_meta( $post_id, '_timeapp_venue_url', true );
	$address     = get_post_meta( $post_id, '_timeapp_address', true );
	$city        = get_post_meta( $post_id, '_timeapp_city', true );
	$state       = get_post_meta( $post_id, '_timeapp_state', true );
	$zip         = get_post_meta( $post_id, '_timeapp_zip', true );
	$alt_mailing = get_post_meta( $post_id, '_timeapp_alt_mailing', true );

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
	echo '<select class="timeapp-select" name="_timeapp_state" id="_timeapp_state">';
	echo '<option value=""' .  ( ! isset( $state ) || $state == '' ? ' selected' : '' ) . '>' . __( 'Select State', 'timeapp' ) . '</option>';

	$states = timeapp_get_states();
	foreach ( $states as $abbr => $name ) {
		echo '<option value="' . $abbr . '"' . ( $state == $abbr ? ' selected' : '' ) . '>' . $name . '</option>';
	}

	echo '</select>';

	// Zip Code
	echo '<p>';
	echo '<strong><label for="_timeapp_zip">' . __( 'Zip Code', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_zip" id="_timeapp_zip" value="' . ( isset( $zip ) && ! empty( $zip ) ? $zip : '' ) . '" />';
	echo '</p>';

	// Different Mailing Address?
	echo '<p>';
	echo '<strong><label for="_timeapp_alt_mailing">' . __( 'Different Mailing Address?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_alt_mailing" id="_timeapp_alt_mailing" value="1" ' . checked( true,  $alt_mailing, false ) . ' />';
	echo '<label for="_timeapp_alt_mailing">' . __( 'Check if Mailing Address is different than Venue Address.', 'timeapp' ) . '</label>';
	echo '</p>';

	do_action( 'timeapp_venue_info_fields', $post_id );
}


/**
 * Render mailing address meta box
 *
 * @since       1.0.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_mailing_address_meta_box() {
	global $post;

	$post_id = $post->ID;
	$address = get_post_meta( $post_id, '_timeapp_mailing_address', true );
	$city    = get_post_meta( $post_id, '_timeapp_mailing_city', true );
	$state   = get_post_meta( $post_id, '_timeapp_mailing_state', true );
	$zip     = get_post_meta( $post_id, '_timeapp_mailing_zip', true );

	// Address
	echo '<p>';
	echo '<strong><label for="_timeapp_mailing_address">' . __( 'Address', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_mailing_address" id="_timeapp_mailing_address" value="' . ( isset( $address ) && ! empty( $address ) ? $address : '' ) . '" />';
	echo '</p>';

	// City
	echo '<p>';
	echo '<strong><label for="_timeapp_mailing_city">' . __( 'City', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_mailing_city" id="_timeapp_mailing_city" value="' . ( isset( $city ) && ! empty( $city ) ? $city : '' ) . '" />';
	echo '</p>';

	// State
	echo '<p>';
	echo '<strong><label for="_timeapp_mailing_state">' . __( 'State', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<select class="timeapp-select" name="_timeapp_mailing_state" id="_timeapp_mailing_state">';
	echo '<option value=""' .  ( ! isset( $state ) || $state == '' ? ' selected' : '' ) . '>' . __( 'Select State', 'timeapp' ) . '</option>';

	$states = timeapp_get_states();
	foreach ( $states as $abbr => $name ) {
		echo '<option value="' . $abbr . '"' . ( $state == $abbr ? ' selected' : '' ) . '>' . $name . '</option>';
	}

	echo '</select>';

	// Zip Code
	echo '<p>';
	echo '<strong><label for="_timeapp_mailing_zip">' . __( 'Zip Code', 'timeapp' ) . '<span class="timeapp-required">*</span></label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_mailing_zip" id="_timeapp_mailing_zip" value="' . ( isset( $zip ) && ! empty( $zip ) ? $zip : '' ) . '" />';
	echo '</p>';

	do_action( 'timeapp_mailing_address_fields', $post_id );
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
	if ( ! isset( $post->post_type ) || $post->post_type != 'purchaser' ) {
		return $post_id;
	}

	// Don't process if nonce can't be validated
	if ( ! isset( $_POST['timeapp_purchaser_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_purchaser_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Don't process if this is an autosave
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// Don't process if this is a revision
	if ( $post->post_type == 'revision' ) {
		return $post_id;
	}

	// Don't process if the current user shouldn't be editing this
	if ( ! current_user_can( 'edit_purchaser', $post_id ) ) {
		return $post_id;
	}

	// The fields to save
	$fields = apply_filters( 'timeapp_purchaser_fields_save', array(
		'_timeapp_first_name',
		'_timeapp_last_name',
		'_timeapp_email',
		'_timeapp_enable_additional_emails',
		'_timeapp_additional_emails',
		'_timeapp_phone_number',
		'_timeapp_signatory',
		'_timeapp_signatory_first_name',
		'_timeapp_signatory_last_name',
		'_timeapp_venue_url',
		'_timeapp_address',
		'_timeapp_city',
		'_timeapp_state',
		'_timeapp_zip',
		'_timeapp_alt_mailing',
		'_timeapp_mailing_address',
		'_timeapp_mailing_city',
		'_timeapp_mailing_state',
		'_timeapp_mailing_zip',
	) );

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			if ( $field == '_timeapp_additional_emails' ) {
				$emails = array_map( 'trim', explode( "\n", $_POST[ $field ] ) );
				$emails = array_unique( $emails );
				$emails = array_map( 'sanitize_text_field', $emails );

				foreach ( $emails as $id => $email ) {
					if ( ! is_email( $email ) ) {
						unset( $emails[ $id ] );
					}
				}

				$new = $emails;
			} else {
				if ( is_string( $_POST[ $field ] ) ) {
					$new = esc_attr( $_POST[ $field ] );
				} else {
					$new = $_POST[ $field ];
				}
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
	$payable_to     = get_post_meta( $post_id, '_timeapp_payable_to', true );
	$tax_id         = get_post_meta( $post_id, '_timeapp_tax_id', true );
	$artist_url     = get_post_meta( $post_id, '_timeapp_artist_url', true );
	$promo_url      = get_post_meta( $post_id, '_timeapp_promo_url', true );
	$commission     = get_post_meta( $post_id, '_timeapp_commission', true );
	$contract_terms = get_post_meta( $post_id, '_timeapp_contract_terms', true );
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

	// Make payments to
	echo '<p>';
	echo '<strong><label for="_timeapp_payable_to">' . __( 'Make Payments To', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="text" class="regular-text" name="_timeapp_payable_to" id="_timeapp_payable_to" value="' . ( isset( $payable_to ) && ! empty( $payable_to ) ? $payable_to : '' ) . '" />';
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
	echo '<select class="timeapp-select2" name="_timeapp_commission" id="_timeapp_commission">';
	echo '<option value="5"' . ( ! isset( $commission ) || $commission == '5' ? ' selected' : '' ) . '>' . __( '5%', 'timeapp' ) . '</option>';
	echo '<option value="10"' . ( $commission == '10' ? ' selected' : '' ) . '>' . __( '10%', 'timeapp' ) . '</option>';
	echo '<option value="15"' . ( $commission == '15' ? ' selected' : '' ) . '>' . __( '15%', 'timeapp' ) . '</option>';
	echo '<option value="20"' . ( $commission == '20' ? ' selected' : '' ) . '>' . __( '20%', 'timeapp' ) . '</option>';
	echo '</select>';

	// Contract Terms
	echo '<p>';
	echo '<strong><label for="_timeapp_contract_terms">' . __( 'Contract Terms', 'timeapp' ) . '</label></strong><br />';
	echo '<textarea cols="30" rows="5" name="_timeapp_contract_terms" id="_timeapp_contract_terms">' . $contract_terms . '</textarea>';
	echo '</p>';

	// Rider
	echo '<p>';
	echo '<strong><label for="_timeapp_rider">' . __( 'Rider', 'timeapp' ) . '</label></strong><br />';
	if ( $rider ) {
		echo '<a href="' . add_query_arg( array( 'timeapp-action' => 'download_rider' ) ) . '" class="button button-secondary">' . __( 'Download', 'timeapp' ) . '</a>';
		echo '<a href="' . add_query_arg( array( 'timeapp-action' => 'remove_rider' ) ) . '" class="button button-secondary">' . __( 'Remove', 'timeapp' ) . '</a>';
	} else {
		echo '<input id="_timeapp_rider" name="_timeapp_rider" type="file" value="" size="25" />';
	}
	echo '</p>';

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
	if ( ! isset( $post->post_type ) || $post->post_type != 'artist' ) {
		return $post_id;
	}

	// Don't process if nonce can't be validated
	if ( ! isset( $_POST['timeapp_artist_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_artist_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Don't process if this is an autosave
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// Don't process if this is a revision
	if ( $post->post_type == 'revision' ) {
		return $post_id;
	}

	// Don't process if the current user shouldn't be editing this
	if ( ! current_user_can( 'edit_artist', $post_id ) ) {
		return $post_id;
	}

	// The fields to save
	$fields = apply_filters( 'timeapp_artist_fields_save', array(
		'_timeapp_signer_name',
		'_timeapp_artist_email',
		'_timeapp_payable_to',
		'_timeapp_tax_id',
		'_timeapp_artist_url',
		'_timeapp_promo_url',
		'_timeapp_commission',
		'_timeapp_contract_terms'
	) );

	if ( ! empty( $_FILES ) && isset( $_FILES['_timeapp_rider'] ) && ! empty( $_FILES['_timeapp_rider']['name'] ) ) {
		$rider = wp_upload_bits( $_FILES['_timeapp_rider']['name'], null, file_get_contents( $_FILES['_timeapp_rider']['tmp_name'] ) );

		if ( $rider['error'] == false ) {
			update_post_meta( $post_id, '_timeapp_rider', $rider['url'] );
		}
	}

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			if( is_string( $_POST[ $field ] ) ) {
				$new = esc_attr( $_POST[ $field ] );
			} else {
				$new = $_POST[ $field ];
			}

			$new = apply_filters( 'timeapp_artist_save_' . $field, $new );
			update_post_meta( $post_id, $field, $new );
		} else {
			delete_post_meta( $post_id, $field );
		}
	}
}
add_action( 'save_post', 'timeapp_save_artist_meta_box' );


/**
 * Render agent details meta box
 *
 * @since       1.1.0
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_render_agent_details_meta_box() {
	global $post;

	$post_id     = $post->ID;
	$signer_name = get_post_meta( $post_id, '_timeapp_internal_agent', true );

	// Is agent internal?
	echo '<p>';
	echo '<strong><label for="_timeapp_internal_agent">' . __( 'Internal Agent?', 'timeapp' ) . '</label></strong><br />';
	echo '<input type="checkbox" name="_timeapp_internal_agent" id="_timeapp_internal_agent" value="1" ' . checked( true,  $signer_name, false ) . ' />';
	echo '<label for="_timeapp_internal_agent">' . __( 'Check if agent is an internal agent.', 'timeapp' ) . '</label>';
	echo '</p>';

	do_action( 'timeapp_agent_details_fields', $post_id );

	wp_nonce_field( basename( __FILE__ ), 'timeapp_agent_nonce' );
}


/**
 * Save post meta when the save_post action is called
 *
 * @since       1.1.0
 * @param       int $post_id The ID of the post we are saving
 * @global      object $post The WordPress object for this post
 * @return      void
 */
function timeapp_save_agent_meta_box( $post_id ) {
	global $post;

	// Bail if this isn't the agent post type
	if ( ! isset( $post->post_type ) || $post->post_type != 'agent' ) {
		return $post_id;
	}

	// Don't process if nonce can't be validated
	if ( ! isset( $_POST['timeapp_agent_nonce'] ) || ! wp_verify_nonce( $_POST['timeapp_agent_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Don't process if this is an autosave
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// Don't process if this is a revision
	if ( $post->post_type == 'revision' ) {
		return $post_id;
	}

	// Don't process if the current user shouldn't be editing this
	if ( ! current_user_can( 'edit_agent', $post_id ) ) {
		return $post_id;
	}

	// The fields to save
	$fields = apply_filters( 'timeapp_agent_fields_save', array(
		'_timeapp_internal_agent'
	) );

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			if( is_string( $_POST[ $field ] ) ) {
				$new = esc_attr( $_POST[ $field ] );
			} else {
				$new = $_POST[ $field ];
			}

			$new = apply_filters( 'timeapp_agent_save_' . $field, $new );
			update_post_meta( $post_id, $field, $new );
		} else {
			delete_post_meta( $post_id, $field );
		}
	}
}
add_action( 'save_post', 'timeapp_save_agent_meta_box' );
