<?php
/**
 * Register settings
 *
 * @package     TimeApp\Admin\Settings\Register
 * @since       2.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Create the setting menu item
 *
 * @since       2.2.0
 * @param       array $menu The default menu args
 * @return      array $menu Our defined menu args
 */
function timeapp_create_menu( $menu ) {
	$menu['page_title'] = __( 'Settings', 'timeapp' );
	$menu['menu_title'] = __( 'Settings', 'timeapp' );
	$menu['icon']       = 'dashicons-admin-settings';
	$menu['capability'] = 'edit_plays';

	return $menu;
}
add_filter( 'timeapp_menu', 'timeapp_create_menu' );


/**
 * Define our settings tabs
 *
 * @since       2.2.0
 * @param       array $tabs The default tabs
 * @return      array $tabs Our defined tabs
 */
function timeapp_settings_tabs( $tabs ) {
	$tabs['general'] = __( 'General', 'timeapp' );
	$tabs['email']   = __( 'Email', 'timeapp' );

	if ( current_user_can( 'manage_options' ) ) {
		$tabs['debugging'] = __( 'Debugging', 'timeapp' );
	}

	return $tabs;
}
add_filter( 'timeapp_settings_tabs', 'timeapp_settings_tabs' );


/**
 * Define settings sections
 *
 * @since       2.2.0
 * @param       array $sections The default sections
 * @return      array $sections Our defined sections
 */
function timeapp_registered_settings_sections( $sections ) {
	$sections = array(
		'general' => apply_filters( 'timeapp_settings_sections_general', array(
			'main' => __( 'General', 'timeapp' )
		) ),
		'email'     => apply_filters( 'timeapp_settings_sections_email', array() ),
		'debugging' => apply_filters( 'timeapp_settings_sections_debugging', array() )
	);

	return $sections;
}
add_filter( 'timeapp_registered_settings_sections', 'timeapp_registered_settings_sections' );



/**
 * Define our settings
 *
 * @since       1.0.0
 * @param       array $settings The default settings
 * @return      array $settings Our defined settings
 */
function timeapp_registered_settings( $settings ) {
	$new_settings = array(
		// General Settings
		'general' => apply_filters( 'timeapp_settings_general', array(
			'main' => array(
				array(
					'id'   => 'general_header',
					'name' => __( 'General Settings', 'timeapp' ),
					'desc' => '',
					'type' => 'header'
				),
				array(
					'id'   => 'login_logo',
					'name' => __( 'Login Logo', 'timeapp' ),
					'desc' => __( 'Upload a logo to display on the login page', 'timeapp' ),
					'type' => 'upload',
					'std'  => TIMEAPP_URL . 'assets/img/login-logo.png'
				),
				array(
					'id'   => 'admin_logo',
					'name' => __( 'Admin Bar Logo', 'timeapp' ),
					'desc' => __( 'Upload a logo to display in the admin bar', 'timeapp' ),
					'type' => 'upload',
					'std'  => TIMEAPP_URL . 'assets/img/admin-logo.png'
				),
				array(
					'id'   => 'favicon',
					'name' => __( 'Favicon', 'timeapp' ),
					'desc' => __( 'Upload a logo to use as the favicon', 'timeapp' ),
					'type' => 'upload',
					'std'  => TIMEAPP_URL . 'assets/img/favicon.png'
				)
			)
		) ),
		'email' => apply_filters( 'timeapp_settings_email', array(
			array(
				'id'   => 'email_header',
				'name' => __( 'Email Settings', 'timeapp' ),
				'desc' => '',
				'type' => 'header'
			),
			array(
				'id'   => 'email_from_name',
				'name' => __( 'From Name', 'timeapp' ),
				'desc' => __( 'The display name emails should be sent from', 'timeapp' ),
				'type' => 'text',
				'std'  => 'Time Music Agency, Inc'
			),
			array(
				'id'   => 'email_from_address',
				'name' => __( 'From Address', 'timeapp' ),
				'desc' => __( 'The email address emails should be sent from', 'timeapp' ),
				'type' => 'text',
				'std'  => 'contracts@timemusicagency.com'
			),
			array(
				'id'   => 'email_cc_addresses',
				'name' => __( 'CC Addresses', 'timeapp' ),
				'desc' => __( 'A list of additional emails that should be CC\'d on all emails, one per line', 'timeapp' ),
				'type' => 'textarea',
				'std'  => 'alyssa@timemusicagency.com'
			),
			array(
				'id'     => 'email_template_tags',
				'name'   => '',
				'desc'   => __( 'The following template tags can be entered into email fields:', 'timeapp' ) . '<br />' . timeapp_tags_list(),
				'type'   => 'descriptive_text'
			),
			array(
				'id'   => 'booking_email_subject',
				'name' => __( 'Booking Email Subject', 'timeapp' ),
				'desc' => __( 'Enter the subject line for booking emails', 'timeapp' ),
				'type' => 'text',
				'std'  => sprintf( __( 'Time Music Agency Contract - %1$s %2$s', 'timeapp' ), '{artist_name}', '{start_date}' )
			),
			array(
				'id'   => 'booking_email_content',
				'name' => __( 'Booking Email Content', 'timeapp' ),
				'desc' => __( 'Enter the content for booking emails', 'timeapp' ),
				'type' => 'editor',
				'std'  => timeapp_get_booking_email_content()
			),
			array(
				'id'   => 'cancelled_email_subject',
				'name' => __( 'Cancellation Email Subject', 'timeapp' ),
				'desc' => __( 'Enter the subject line for cancellation emails', 'timeapp' ),
				'type' => 'text',
				'std'  => __( 'Time Music Agency Contract - Cancellation Notice', 'timeapp' )
			),
			array(
				'id'   => 'cancelled_email_content',
				'name' => __( 'Cancellation Email Content', 'timeapp' ),
				'desc' => __( 'Enter the content for cancellation emails', 'timeapp' ),
				'type' => 'editor',
				'std'  => timeapp_get_cancelled_email_content()
			)
		) ),
		'debugging' => apply_filters( 'timeapp_settings_debugging', array(
			array(
				'id'   => 'debugging_header',
				'name' => __( 'Debugging Settings', 'timeapp' ),
				'desc' => '',
				'type' => 'header'
			),
			array(
				'id'   => 'enable_debugging',
				'name' => __( 'Debugging', 'timeapp' ),
				'desc' => __( 'Enable debug mode', 'timeapp' ),
				'type' => 'checkbox'
			)
		) )
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'timeapp_registered_settings', 'timeapp_registered_settings' );
