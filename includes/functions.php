<?php
/**
 * Helper functions
 *
 * @package     TimeApp\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


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
			'post_type'      => 'purchaser',
			'posts_per_page' => 999999,
			'post_status'    => 'publish'
		)
	);

	if ( $all_purchasers ) {
		foreach ( $all_purchasers as $id => $data ) {
			$purchasers[ $data->ID ] = $data->post_title;
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
			'post_type'      => 'artist',
			'posts_per_page' => 999999,
			'post_status'    => 'publish'
		)
	);

	if ( $all_artists ) {
		foreach ( $all_artists as $id => $data ) {
			$artists[ $data->ID ] = $data->post_title;
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
		'post_type'      => 'agent',
		'posts_per_page' => 999999,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC'
	);

	$meta  = array();
	$label = '';

	if ( $type == 'internal' ) {
		$meta = array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_timeapp_internal_agent',
					'compare' => 'EXISTS'
				)
			)
		);

		$label = __( ' internal', 'timeapp' );
	} elseif ( $type == 'external' ) {
		$meta = array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_timeapp_internal_agent',
					'compare' => 'NOT EXISTS'
				)
			)
		);

		$label = __( ' external', 'timeapp' );
	}

	$args       = array_merge( $args, $meta );
	$all_agents = get_posts( $args );

	if ( $all_agents ) {
		foreach ( $all_agents as $id => $data ) {
			$agents[ $data->ID ] = $data->post_title;
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
 * @param       bool $hide_future Whether to hide or show future dates
 * @return      mixed bool false|array $months The months array
 */
function timeapp_get_months( $hide_future = false ) {
	$args = array(
		'post_type'      => 'play',
		'posts_per_page' => 999999,
		'post_status'    => 'publish',
	);

	$all_plays = get_posts( $args );
	$months    = array();

	if ( $all_plays ) {
		foreach ( $all_plays as $id => $data ) {
			$date       = get_post_meta( $data->ID, '_timeapp_start_date', true );
			$text_date  = date( 'F Y', strtotime( $date ) );
			$short_date = date( 'Y-m', strtotime( $date ) );

			if ( $hide_future ) {
				$now   = date( 'Ymd', time() );
				$check = date( 'Ymd', strtotime( $date ) );

				if ( $check < $now ) {
					if ( ! array_key_exists( $short_date, $months ) ) {
						$months[ $short_date ] = $text_date;
					}
				}
			} else {
				if ( ! array_key_exists( $short_date, $months ) ) {
					$months[ $short_date ] = $text_date;
				}
			}
		}

		$months = array_unique( $months );
		krsort( $months );
	} else {
		$months = false;
	}

	return $months;
}


/**
 * Sanitize and format prices
 *
 * @since       1.0.0
 * @param       string $price The unformatted price
 * @param       bool $textualize Whether to return numerically or textually
 * @return      string $price The formatted price
 */
function timeapp_format_price( $price, $textualize = false ) {
	if ( ! $price ) {
		$price = '0.00';
	}

	if ( ! $textualize ) {
		if ( $price[0] == '$' ) {
			$price = substr( $price, 1 );
		}

		$price = '$' . number_format( $price, 2 );
	} else {
		$price = number_format( $price, 2, '.', '' );

		list( $dollars, $cents ) = explode( '.', $price );

		$textualizer = new TimeApp_Textualizer();
		$dollars     = $textualizer->textualize( $dollars );

		$price = sprintf( __( '%s and %s/100 dollars - U.S.', 'timeapp' ), ucwords( $dollars ), $cents );
	}

	return $price;
}


/**
 * Retrieve email content
 *
 * @since       1.0.0
 * @return      string $message The email content
 */
function timeapp_get_booking_email_content() {
	$message	= '{purchaser_name},' . "\n";
	$message   .= sprintf( __( 'Thank you for booking %s. Please print, sign and return the attached PDF contract to secure and finalize your booking.', 'timeapp' ), '{artist_name}' ) . "\n\n";
	$message   .= __( 'Your business is appreciated, have a great day!', 'timeapp' ) . "\n";
	$message   .= __( 'Time Music Agency', 'timeapp' ) . "\n";
	$message   .= __( 'PO Box 353', 'timeapp' ) . "\n";
	$message   .= __( 'Long Lake, MN 55356', 'timeapp' ) . "\n";
	$message   .= __( '952-448-4202', 'timeapp' );
	$message	= apply_filters( 'timeapp_booking_email_content', $message );

	return $message;
}


/**
 * Retrieve email content
 *
 * @since       2.0.0
 * @return      string $message The email content
 */
function timeapp_get_cancelled_email_content() {
	$message	= '{purchaser_name},' . "\n";
	$message   .= __( 'This email serves as the formal cancellation for the show reflected on the attached cancelled contract.', 'timeapp' ) . "\n\n";
	$message   .= __( 'Your business is appreciated, have a great day!', 'timeapp' ) . "\n";
	$message   .= __( 'Time Music Agency', 'timeapp' ) . "\n";
	$message   .= __( 'PO Box 353', 'timeapp' ) . "\n";
	$message   .= __( 'Long Lake, MN 55356', 'timeapp' ) . "\n";
	$message   .= __( '952-448-4202', 'timeapp' );
	$message	= apply_filters( 'timeapp_cancelled_email_content', $message );

	return $message;
}


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
		'guarantee'  => $guarantee,
		'production' => $production,
		'rate'       => $rate,
		'split'      => $split
	);

	// Strip dollar signs
	foreach ( $values as $id => $value ) {
		if ( $value && $value[0] == '$' ) {
			$values[ $id ] = substr( $value, 1 );
		}
	}

	$commission = $values['guarantee'];

	// Maybe subtract production
	if ( $values['production'] ) {
		$commission = $commission - $production;
	}

	// Mod rate
	$commission = $commission * ( (float) str_replace( '%', '', $values['rate'] ) / 100 );

	// Maybe mod split
	if ( $values['split'] ) {
		if ( $show_split ) {
			$split = (float) str_replace( '%', '', $values['split'] ) / 100;
		} else {
			$split = ( 1 -(float) str_replace( '%', '', $values['split'] ) / 100 );
		}

		$commission = $commission * $split;
	}

	return timeapp_format_price( $commission );
}
