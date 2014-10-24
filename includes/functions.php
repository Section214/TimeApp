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
