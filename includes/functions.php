<?php
/**
 * Helper functions
 *
 * @package     TimeApp\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Disable the site frontend
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_disable_frontend() {
    if( ! is_admin() && $GLOBALS['pagenow'] != 'wp-login.php' ) {
        wp_safe_redirect( admin_url() );
        exit;
    }
}
add_action( 'template_redirect', 'timeapp_disable_frontend' );


/**
 * Change login redirect to point to dashboard
 *
 * @since       1.0.0
 * @return      string New URL to redirect to
 */
function timeapp_default_page( $redirect_to ) {
    return admin_url( 'index.php' );
}
add_filter( 'login_redirect', 'timeapp_default_page' );


/**
 * Rewrite the 'Powered by WordPress' footer text
 *
 * @since       1.0.0
 * @param       string $footer_text The existing footer text
 * @return      string $footer_text The updated footer text
 */
function timeapp_footer_text( $footer_text ) {
    $footer_text = sprintf( __( 'Copyright &copy; 2014%s Time Music Agency &middot; All Rights Reserved' ), ( date( 'Y' ) > 2014 ? '-' . date( 'Y' ) : '' ) );

    return $footer_text;
}
add_filter( 'admin_footer_text', 'timeapp_footer_text' );


/**
 * Remove WordPress version from footer
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_hide_footer_version() {
    if( ! current_user_can( 'manage_options' ) ) {
        remove_filter( 'update_footer', 'core_update_footer' );
    }
}
add_action( 'admin_menu', 'timeapp_hide_footer_version' );


/**
 * Retrieves an array of states
 *
 * @since       1.0.0
 * @return      array $states The array of states
 */
function timeapp_get_states() {
    $states = array(
        'AL' => 'Alabama',
        'AK' => 'Alaska', 
        'AZ' => 'Arizona', 
        'AR' => 'Arkansas', 
        'CA' => 'California', 
        'CO' => 'Colorado', 
        'CT' => 'Connecticut', 
        'DE' => 'Delaware', 
        'DC' => 'District Of Columbia', 
        'FL' => 'Florida', 
        'GA' => 'Georgia', 
        'HI' => 'Hawaii', 
        'ID' => 'Idaho', 
        'IL' => 'Illinois', 
        'IN' => 'Indiana', 
        'IA' => 'Iowa', 
        'KS' => 'Kansas', 
        'KY' => 'Kentucky', 
        'LA' => 'Louisiana', 
        'ME' => 'Maine', 
        'MD' => 'Maryland', 
        'MA' => 'Massachusetts', 
        'MI' => 'Michigan', 
        'MN' => 'Minnesota', 
        'MS' => 'Mississippi', 
        'MO' => 'Missouri', 
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio', 
        'OK' => 'Oklahoma', 
        'OR' => 'Oregon', 
        'PA' => 'Pennsylvania', 
        'RI' => 'Rhode Island', 
        'SC' => 'South Carolina', 
        'SD' => 'South Dakota',
        'TN' => 'Tennessee', 
        'TX' => 'Texas', 
        'UT' => 'Utah', 
        'VT' => 'Vermont', 
        'VA' => 'Virginia', 
        'WA' => 'Washington', 
        'WV' => 'West Virginia', 
        'WI' => 'Wisconsin', 
        'WY' => 'Wyoming'
    );

    return $states;
}


/**
 * Retrieves an array of purchasers
 *
 * @since       1.0.0
 * @return      array $purchasers The array of purchasers
 */
function timeapp_get_purchasers() {
    $all_purchasers = get_posts(
        array(
            'post_type'     => 'purchaser',
            'posts_per_page'=> 999999,
            'post_status'   => 'publish'
        )
    );

    if( $all_purchasers ) {
        foreach( $all_purchasers as $id => $data ) {
            $purchasers[$data->ID] = $data->post_title;
        }
    } else {
        $purchasers[] = __( 'No purchasers defined!', 'timeapp' );
    }

    return $purchasers;
}


/**
 * Retrieves an array of artists
 *
 * @since       1.0.0
 * @return      array $artists The array of artists
 */
function timeapp_get_artists() {
    $all_artists = get_posts(
        array(
            'post_type'     => 'artist',
            'posts_per_page'=> 999999,
            'post_status'   => 'publish'
        )
    );

    if( $all_artists ) {
        foreach( $all_artists as $id => $data ) {
            $artists[$data->ID] = $data->post_title;
        }
    } else {
        $artists[] = __( 'No artists defined!', 'timeapp' );
    }

    return $artists;
}


/**
 * Retrieves an array of agents
 *
 * @since       1.0.0
 * @return      array $agents The array of agents
 */
function timeapp_get_agents() {
    $all_agents = get_posts(
        array(
            'post_type'     => 'agent',
            'posts_per_page'=> 999999,
            'post_status'   => 'publish'
        )
    );

    if( $all_agents ) {
        foreach( $all_agents as $id => $data ) {
            $agents[$data->ID] = $data->post_title;
        }
    } else {
        $agents[] = __( 'No agents defined!', 'timeapp' );
    }

    return $agents;
}


/**
 * Allow updating meta through the dashboard
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_update_meta() {
    // Don't process if nonce can't be verified
    if( ! wp_verify_nonce( $_GET['update-nonce'], 'update-meta' ) ) return;

    // Don't process if the current user shouldn't be editing this
    if( ! isset( $_GET['type'] ) || ! isset( $_GET['id'] ) || ! current_user_can( 'edit_' . $_GET['type'], $_GET['id'] ) ) return;

    // Don't process if no key or value is passed
    if( ! isset( $_GET['key'] ) || ! isset( $_GET['value'] ) ) return;

    update_post_meta( $_GET['id'], $_GET['key'], $_GET['value'] );
}
add_action( 'timeapp_update_meta', 'timeapp_update_meta' );


/**
 * Sanitize and format prices
 *
 * @since       1.0.0
 * @param       string $price The unformatted price
 * @return      string $price The formatted price
 */
function timeapp_format_price( $price ) {
    if( ! $price ) {
        $price = '0.00';
    }

    if( $price[0] == '$' ) {
        $price = substr( $price, 1 );
    }

    $price = '$' . number_format( $price, 2 );

    return $price;
}
