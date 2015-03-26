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
 * Change the login image URL
 *
 * @since       1.1.0
 * @return      null
 */
function timeapp_login_header_url() {
    return null;
}
add_filter( 'login_headerurl', 'timeapp_login_header_url' );


/**
 * Change the login image alt text
 *
 * @since       1.1.0
 * @return      null
 */
function timeapp_login_header_title() {
    return null;
}
add_filter( 'login_headertitle', 'timeapp_login_header_title' );


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
    global $wp_query;
    
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
 * @param       string $type The type of agent to display
 * @return      array $agents The array of agents
 */
function timeapp_get_agents( $type = null ) {
    $args = array(
        'post_type'     => 'agent',
        'posts_per_page'=> 999999,
        'post_status'   => 'publish'
    );

    $meta   = array();
    $label  = '';

    if( $type == 'internal' ) {
        $meta = array(
            'meta_query'    => array(
                'relation'  => 'AND',
                array(
                    'key'       => '_timeapp_internal_agent',
                    'compare'   => 'EXISTS'
                )
            )
        );

        $label = __( ' internal', 'timeapp' );
    } else if( $type == 'external' ) {
        $meta = array(
            'meta_query'    => array(
                'relation'  => 'AND',
                array(
                    'key'       => '_timeapp_internal_agent',
                    'compare'   => 'NOT EXISTS'
                )
            )
        );

        $label = __( ' external', 'timeapp' );
    }

    $args = array_merge( $args, $meta );

    $all_agents = get_posts( $args );

    if( $all_agents ) {
        foreach( $all_agents as $id => $data ) {
            $agents[$data->ID] = $data->post_title;
        }
    } else {
        $agents[] = sprintf( __( 'No%s agents defined!', 'timeapp' ), $label );
    }

    return $agents;
}


/**
 * Get months array for start dates
 *
 * @since       1.2.0
 * @return      mixed bool false|array $months The months array
 */
function timeapp_get_months() {
    $args = array(
        'post_type'     => 'play',
        'posts_per_page'=> 999999,
        'post_status'   => 'publish',
    );

    $all_plays  = get_posts( $args );
    $months     = array();

    if( $all_plays ) {
        foreach( $all_plays as $id => $data ) {
            $date       = get_post_meta( $data->ID, '_timeapp_start_date', true );
            $text_date  = date( 'F Y', strtotime( $date ) );
            $short_date = date( 'm-Y', strtotime( $date ) );

            if( ! array_key_exists( $short_date, $months ) ) {
                $months[$short_date] = $text_date;
            }
        }

        $months = array_unique( $months );
        ksort( $months );
    } else {
        $months = false;
    }

    return $months;
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
 * @param       bool $textualize Whether to return numerically or textually
 * @return      string $price The formatted price
 */
function timeapp_format_price( $price, $textualize = false ) {
    if( ! $price ) {
        $price = '0.00';
    }

    if( ! $textualize ) {
        if( $price[0] == '$' ) {
            $price = substr( $price, 1 );
        }

        $price = '$' . number_format( $price, 2 );
    } else {
        $price = number_format( $price, 2, '.', '' );

        list( $dollars, $cents ) = explode( '.', $price );

        $textualizer = new TimeApp_Textualizer();
        $dollars = $textualizer->textualize( $dollars );

        $price = sprintf( __( '%s and %s/100 dollars - U.S.', 'timeapp' ), ucwords( $dollars ), $cents );
    }

    return $price;
}


/**
 * Generate and email contracts
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_generate_pdf() {
    // Don't process if nonce can't be verified
    if( ! wp_verify_nonce( $_GET['pdf-nonce'], 'generate-pdf' ) ) return;

    // Include the generator class
    require_once TIMEAPP_DIR . 'includes/class.pdf-generator.php';

    // Setup cache
    $wp_upload_dir  = wp_upload_dir();
    $cache_dir      = $wp_upload_dir['basedir'] . '/timeapp-cache/';

    // Ensure that the cache directory is protected
    if( get_transient( 'timeapp_check_protection_files' ) === false ) {
        wp_mkdir_p( $cache_dir );

        // Top level blank index.php
        if( ! file_exists( $cache_dir . 'index.php' ) ) {
            @file_put_contents( $cache_dir . 'index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
        }

        // Top level .htaccess
        $rules = "Options -Indexes";
        if( file_exists( $cache_dir . '.htaccess' ) ) {
            $contents = @file_get_contents( $cache_dir . '.htaccess' );

            if( $contents !== $rules || ! $contents ) {
                @file_put_contents( $cache_dir . '.htaccess', $rules );
            }
        } else {
            @file_put_contents( $cache_dir . '.htaccess', $rules );
        }

        // Check daily
        set_transient( 'timeapp_check_protection_files', true, 3600 * 24 );
    }

    $play       = get_post( $_GET['post'] );
    $date       = date( 'm-d-Y' );
    $filename   = $play->post_name . '-contract-' . $date . '.pdf';

    // We don't store contracts!
    if( file_exists( $cache_dir . $filename ) ) {
        unlink( $cache_dir . $filename );
    }

    $file = new TimeApp_Generate_PDF( $cache_dir . $filename, $_GET['post'] );
    $file->build();

    if( TIMEAPP_DEBUG ) {
        $url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $cache_dir . $filename );
        wp_safe_redirect( $url );
        exit;
    }

    // Get rider
    $artist     = get_post_meta( $play->ID, '_timeapp_artist', true );
    $artist     = get_post( $artist );
    $purchaser  = get_post_meta( $play->ID, '_timeapp_purchaser', true );
    $purchaser  = get_post( $purchaser );
    $rider_url  = get_post_meta( $artist->ID, '_timeapp_rider', true );
    $email      = get_post_meta( $purchaser->ID, '_timeapp_email', true );
    $cc_email   = get_post_meta( $artist->ID, '_timeapp_artist_email', true );
    $start_date = get_post_meta( $play->ID, '_timeapp_start_date', true );
    $start_date = ( isset( $start_date ) && ! empty( $start_date ) ? date( 'm/d/Y g:i a', strtotime( $start_date ) ) : '' );

    // Send the email!
    $to         = $email;
    $subject    = sprintf( __( 'Time Music Agency Contract - %1$s %2$s', 'timeapp' ), $artist->post_title, $start_date );
    $message    = timeapp_get_email_content( $play->ID );
    $headers[]  = 'From: Time Music Agency, Inc <contracts@timemusicagency.com>';
    $headers[]  = 'Cc: Danielle Findling <danielle@timemusicagency.com>';

    if( $cc_email ) {
        $headers[] = 'Cc: ' . $cc_email;
    }

    $attachments= array(
        $cache_dir . $filename
    );

    if( $rider_url ) {
        $rider_url      = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $rider_url );
        $attachments[]  = $rider_url;
    }

    wp_mail( $to, $subject, $message, $headers, $attachments );

    // Tag as sent
    update_post_meta( $play->ID, '_timeapp_contract_sent', current_time( 'm/d/Y g:i a' ) );

    wp_safe_redirect( add_query_arg( array( 'timeapp-action' => null, 'pdf-nonce' => null ) ) );
    exit;
}
add_action( 'timeapp_generate_pdf', 'timeapp_generate_pdf' );


/**
 * Retrieve email content
 *
 * @since       1.0.0
 * @param       int $id The ID of a given play
 * @return      string $message The email content
 */
function timeapp_get_email_content( $id ) {
    $purchaser  = get_post_meta( $id, '_timeapp_purchaser', true );
    $purchaser  = get_post( $purchaser );
    $artist     = get_post_meta( $id, '_timeapp_artist', true );
    $artist     = get_post( $artist );

    $first_name = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );

    $message    = ucwords( $first_name ) . ',' . "\n";
    $message   .= sprintf( __( 'Thank you for booking %s. Please print, sign and return the attached PDF contract to secure and finalize your booking.', 'timeapp' ), $artist->post_title ) . "\n\n";
    $message   .= __( 'Your business is appreciated, have a great day!', 'timeapp' ) . "\n";
    $message   .= __( 'Time Music Agency', 'timeapp' ) . "\n";
    $message   .= __( 'PO Box 353', 'timeapp' ) . "\n";
    $message   .= __( 'Long Lake, MN 55356', 'timeapp' ) . "\n";
    $message   .= __( '952-448-4202', 'timeapp' );

    $message    = apply_filters( 'timeapp_email_content', $message );

    return $message;
}


/**
 * Download rider
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_download_rider() {
    $rider_url  = get_post_meta( $_GET['post'], '_timeapp_rider', true );
    $rider_name = basename( $rider_url );

    nocache_headers();
    header( 'Robots: none' );
    if( wp_is_mobile() ) {
        header( 'Content-Type: application/octet-stream' );
    } else {
        header( 'Content-Type: application/force-download' );
    }
    header( 'Content-Disposition: attachment; filename="' . $rider_name . '"' );
    header( 'Content-Transfer-Encoding: Binary' );

    readfile( $rider_url );

    wp_safe_redirect( add_query_arg( array( 'timeapp-action' => null ) ) );
    exit;
}
add_action( 'timeapp_download_rider', 'timeapp_download_rider' );


/**
 * Remove rider
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_remove_rider() {
    delete_post_meta( $_GET['post'], '_timeapp_rider' );

    wp_safe_redirect( add_query_arg( array( 'timeapp-action' => null ) ) );
    exit;
}
add_action( 'timeapp_remove_rider', 'timeapp_remove_rider' );


/**
 * Override post title on play post type
 *
 * @since       1.0.7
 * @param       string $post_title The current post title
 * @return      string $post_title The new post title
 */
function timeapp_update_play_title( $post_title ) {
    if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'play' ) {
        $artist = get_post( $_POST['_timeapp_artist'] );
        $artist = $artist->post_title;

        $purchaser = get_post( $_POST['_timeapp_purchaser'] );
        $purchaser = $purchaser->post_title;

        $date = $_POST['_timeapp_start_date'];
        $date = date( 'm-d-Y', strtotime( $date ) );

        $post_title = $artist . '@' . $purchaser . ' - ' . $date;
    }

    return $post_title;
}
add_filter( 'title_save_pre', 'timeapp_update_play_title' );


/**
 * Calculate commission for a given play
 *
 * @since       1.1.1
 * @param       string $guarantee The guarantee value
 * @param       mixed string $production The production cost | false
 * @param       int $rate The artist commission
 * @param       mixed string $split The split commission rate | false
 * @param       bool $show_split True to show split commission, false otherwise
 * @return      string $commission The calculated commission
 */
function timeapp_get_commission( $guarantee = 0, $production = false, $rate, $split = false, $show_split = false ) {
    $values = array(
        'guarantee' => $guarantee,
        'production'=> $production,
        'rate'      => $rate,
        'split'     => $split
    );

    // Strip dollar signs
    foreach( $values as $id => $value ) {
        if( $value && $value[0] == '$' ) {
            $values[$id] = substr( $value, 1 );
        }
    }

    $commission = $values['guarantee'];

    // Maybe subtract production
    if( $values['production'] ) {
        $commission = $commission - $production;
    }

    // Mod rate
    $commission = $commission * ( (float) str_replace( '%', '', $values['rate'] ) / 100 );

    // Maybe mod split
    if( $values['split'] ) {
        if( $show_split ) {
            $split = (float) str_replace( '%', '', $values['split'] ) / 100;
        } else {
            $split = ( 1 -(float) str_replace( '%', '', $values['split'] ) / 100 );
        }

        $commission = $commission * $split;
    }

    return timeapp_format_price( $commission );
}
