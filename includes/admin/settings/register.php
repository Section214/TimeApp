<?php
/**
 * Register settings
 *
 * @package     TimeApp\Admin\Settings\Register
 * @since       2.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Retrieve the settings tabs
 *
 * @since       2.0.0
 * @return      array $tabs The registered settings tabs
 */
function timeapp_get_settings_tabs() {
    $settings = timeapp_get_registered_settings();

    $tabs               = array();
    $tabs['general']    = __( 'General', 'timeapp' );
    $tabs['email']      = __( 'Email', 'timeapp' );

    if( current_user_can( 'manage_options' ) ) {
        $tabs['debugging'] = __( 'Debugging', 'timeapp' );
    }
    
    return apply_filters( 'timeapp_settings_tabs', $tabs );
}


/**
 * Retrieve the array of plugin settings
 *
 * @since       2.0.0
 * @return      array $timeapp_settings The registered settings
 */
function timeapp_get_registered_settings() {
    $timeapp_settings = array(
        // General Settings
        'general' => apply_filters( 'timeapp_settings_general', array(
            array(
                'id'        => 'general_header',
                'name'      => __( 'General Settings', 'timeapp' ),
                'desc'      => '',
                'type'      => 'header'
            ),
            array(
                'id'        => 'login_logo',
                'name'      => __( 'Login Logo', 'timeapp' ),
                'desc'      => __( 'Upload a logo to display on the login page', 'timeapp' ),
                'type'      => 'upload',
                'std'       => TIMEAPP_URL . 'assets/img/login-logo.png'
            ),
            array(
                'id'        => 'admin_logo',
                'name'      => __( 'Admin Bar Logo', 'timeapp' ),
                'desc'      => __( 'Upload a logo to display in the admin bar', 'timeapp' ),
                'type'      => 'upload',
                'std'       => TIMEAPP_URL . 'assets/img/admin-logo.png'
            )
        ) ),
        'email' => apply_filters( 'timeapp_settings_email', array(
            array(
                'id'        => 'email_header',
                'name'      => __( 'Email Settings', 'timeapp' ),
                'desc'      => '',
                'type'      => 'header'
            ),
            array(
                'id'        => 'email_from_name',
                'name'      => __( 'From Name', 'timeapp' ),
                'desc'      => __( 'The display name emails should be sent from', 'timeapp' ),
                'type'      => 'text',
                'std'       => 'Time Music Agency, Inc'
            ),
            array(
                'id'        => 'email_from_address',
                'name'      => __( 'From Address', 'timeapp' ),
                'desc'      => __( 'The email address emails should be sent from', 'timeapp' ),
                'type'      => 'text',
                'std'       => 'contracts@timemusicagency.com'
            ),
            array(
                'id'        => 'email_cc_addresses',
                'name'      => __( 'CC Addresses', 'timeapp' ),
                'desc'      => __( 'A comma separated list of additional emails that should be CC\'d on all emails', 'timeapp' ),
                'type'      => 'textarea',
                'std'       => 'alyssa@timemusicagency.com'
            ),
            array(
                'id'        => 'email_template_tags',
                'name'      => '',
                'desc'      => timeapp_tags_list(),
                'type'      => 'info',
                'style'     => 'success',
                'header'    => __( 'The following template tags can be entered into email fields:', 'timeapp' )
            ),
            array(
                'id'        => 'booking_email_subject',
                'name'      => __( 'Booking Email Subject', 'timeapp' ),
                'desc'      => __( 'Enter the subject line for booking emails', 'timeapp' ),
                'type'      => 'text',
                'std'       => sprintf( __( 'Time Music Agency Contract - %1$s %2$s', 'timeapp' ), '{artist_name}', '{start_date}' )
            ),
            array(
                'id'        => 'booking_email_content',
                'name'      => __( 'Booking Email Content', 'timeapp' ),
                'desc'      => __( 'Enter the content for booking emails', 'timeapp' ),
                'type'      => 'editor',
                'std'       => timeapp_get_booking_email_content()
            ),
            array(
                'id'        => 'cancelled_email_subject',
                'name'      => __( 'Cancellation Email Subject', 'timeapp' ),
                'desc'      => __( 'Enter the subject line for cancellation emails', 'timeapp' ),
                'type'      => 'text',
                'std'       => __( 'Time Music Agency Contract - Cancellation Notice', 'timeapp' )
            ),
            array(
                'id'        => 'cancelled_email_content',
                'name'      => __( 'Cancellation Email Content', 'timeapp' ),
                'desc'      => __( 'Enter the content for cancellation emails', 'timeapp' ),
                'type'      => 'editor',
                'std'       => timeapp_get_cancelled_email_content()
            )
        ) ),
        'debugging' => apply_filters( 'timeapp_settings_debugging', array(
            array(
                'id'        => 'debugging_header',
                'name'      => __( 'Debugging Settings', 'timeapp' ),
                'desc'      => '',
                'type'      => 'header'
            ),
            array(
                'id'        => 'enable_debugging',
                'name'      => __( 'Debugging', 'timeapp' ),
                'desc'      => __( 'Enable debug mode', 'timeapp' ),
                'type'      => 'checkbox'
            )
        ) )
    );

    return apply_filters( 'timeapp_registered_settings', $timeapp_settings );
}


/**
 * Retrieve an option
 *
 * @since       2.0.0
 * @global      array $timeapp_options The TimeApp options
 * @return      mixed
 */
function timeapp_get_option( $key = '', $default = false ) {
    global $timeapp_options;

    $value = ! empty( $timeapp_options[$key] ) ? $timeapp_options[$key] : $default;
    $value = apply_filters( 'timeapp_get_option', $value, $key, $default );

    return apply_filters( 'timeapp_get_option_' . $key, $value, $key, $default );
}


/**
 * Retrieve all options
 *
 * @since       2.0.0
 * @return      array $timeapp_options The TimeApp options
 */
function timeapp_get_settings() {
    $timeapp_settings = get_option( 'timeapp_settings' );

    if( empty( $timeapp_settings ) ) {
        $timeapp_settings = array();

        update_option( 'timeapp_settings', $timeapp_settings );
    }

    return apply_filters( 'timeapp_get_settings', $timeapp_settings );
}


/**
 * Add settings sections and fields
 *
 * @since       2.0.0
 * @return      void
 */
function timeapp_register_settings() {
    if( get_option( 'timeapp_settings' ) == false ) {
        add_option( 'timeapp_settings' );
    }

    foreach( timeapp_get_registered_settings() as $tab => $settings ) {
        add_settings_section(
            'timeapp_settings_' . $tab,
            __return_null(),
            '__return_false',
            'timeapp_settings_' . $tab
        );

        foreach( $settings as $option ) {
            $name = isset( $option['name'] ) ? $option['name'] : '';

            add_settings_field(
                'timeapp_settings[' . $option['id'] . ']',
                $name,
                function_exists( 'timeapp_' . $option['type'] . '_callback' ) ? 'timeapp_' . $option['type'] . '_callback' : 'timeapp_missing_callback',
                'timeapp_settings_' . $tab,
                'timeapp_settings_' . $tab,
                array(
                    'section'       => $tab,
                    'id'            => isset( $option['id'] )           ? $option['id']             : null,
                    'desc'          => ! empty( $option['desc'] )       ? $option['desc']           : '',
                    'name'          => isset( $option['name'] )         ? $option['name']           : null,
                    'size'          => isset( $option['size'] )         ? $option['size']           : null,
                    'options'       => isset( $option['options'] )      ? $option['options']        : '',
                    'std'           => isset( $option['std'] )          ? $option['std']            : '',
                    'min'           => isset( $option['min'] )          ? $option['min']            : null,
                    'max'           => isset( $option['max'] )          ? $option['max']            : null,
                    'step'          => isset( $option['step'] )         ? $option['step']           : null,
                    'placeholder'   => isset( $option['placeholder'] )  ? $option['placeholder']    : null,
                    'rows'          => isset( $option['rows'] )         ? $option['rows']           : null,
                    'buttons'       => isset( $option['buttons'] )      ? $option['buttons']        : null,
                    'wpautop'       => isset( $option['wpautop'] )      ? $option['wpautop']        : null,
                    'teeny'         => isset( $option['teeny'] )        ? $option['teeny']          : null,
                    'notice'        => isset( $option['notice'] )       ? $option['notice']         : false,
                    'style'         => isset( $option['style'] )        ? $option['style']          : null,
                    'header'        => isset( $option['header'] )       ? $option['header']         : null,
                    'icon'          => isset( $option['icon'] )         ? $option['icon']           : null,
                    'class'         => isset( $option['class'] )        ? $option['class']          : null
                )
            );
        }
    }

    register_setting( 'timeapp_settings', 'timeapp_settings', 'timeapp_settings_sanitize' );
}
add_action( 'admin_init', 'timeapp_register_settings' );


/**
 * Settings sanitization
 *
 * @since       2.0.0
 * @param       array $input The value entered in the field
 * @global      array $timeapp_options The TimeApp options
 * @return      string $input The sanitized value
 */
function timeapp_settings_sanitize( $input = array() ) {
    global $timeapp_options;

    if( empty( $_POST['_wp_http_referer'] ) ) {
        return $input;
    }
    
    parse_str( $_POST['_wp_http_referer'], $referrer );

    $settings   = timeapp_get_registered_settings();
    $tab        = isset( $referrer['tab'] ) ? $referrer['tab'] : 'settings';

    $input = $input ? $input : array();
    $input = apply_filters( 'timeapp_settings_' . $tab . '_sanitize', $input );

    foreach( $input as $key => $value ) {
        $type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

        if( $type ) {
            // Field type specific filter
            $input[$key] = apply_filters( 'timeapp_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $input[$key] = apply_filters( 'timeapp_settings_sanitize', $input[$key], $key );
    }

    if( ! empty( $settings[$tab] ) ) {
        foreach( $settings[$tab] as $key => $value ) {
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $input[$key] ) || ! isset( $input[$key] ) ) {
                unset( $timeapp_options[$key] );
            }
        }
    }

    // Merge our new settings with the existing
    $input = array_merge( $timeapp_options, $input );

    add_settings_error( 'timeapp-notices', '', __( 'Settings updated.', 'timeapp' ), 'updated' );

    return $input;
}


/**
 * Sanitize text fields
 *
 * @since       2.0.0
 * @param       array $input The value entered in the field
 * @return      string $input The sanitized value
 */
function timeapp_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'timeapp_settings_sanitize_text', 'timeapp_sanitize_text_field' );


/**
 * Header callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function timeapp_header_callback( $args ) {
    echo '<hr />';
}


/**
 * Checkbox callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_checkbox_callback( $args ) {
    global $timeapp_options;

    $checked = isset( $timeapp_options[$args['id']] ) ? checked( 1, $timeapp_options[$args['id']], false ) : '';

    $html  = '<input type="checkbox" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_color_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $default = isset( $args['std'] ) ? $args['std'] : '';
    $size    = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="timeapp-color-picker" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />&nbsp;';
    $html .= '<span class="timeapp-color-picker-label"><label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

    echo $html;
}


/**
 * Editor callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_editor_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $rows       = ( isset( $args['rows'] ) && ! is_numeric( $args['rows'] ) ) ? $args['rows'] : '10';
    $wpautop    = isset( $args['wpautop'] ) ? $args['wpautop'] : true;
    $buttons    = isset( $args['buttons'] ) ? $args['buttons'] : true;
    $teeny      = isset( $args['teeny'] ) ? $args['teeny'] : false;

    wp_editor(
        $value,
        'timeapp_settings_' . $args['id'],
        array(
            'wpautop'       => $wpautop,
            'media_buttons' => $buttons,
            'textarea_name' => 'timeapp_settings[' . $args['id'] . ']',
            'textarea_rows' => $rows,
            'teeny'         => $teeny
        )
    );
    echo '<br /><label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';
}


/**
 * Info callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_info_callback( $args ) {
    global $timeapp_options;

    $notice = ( $args['notice'] == true ? '-notice' : '' );
    $class  = ( isset( $args['class'] ) ? $args['class'] : '' );
    $style  = ( isset( $args['style'] ) ? $args['style'] : 'normal' );
    $header = '';

    if( isset( $args['header'] ) ) {
        $header = '<b>' . $args['header'] . '</b><br />';
    }

    echo '<div id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" class="timeapp-info' . $notice . ' timeapp-info-' . $style . '">';

    if( isset( $args['icon'] ) ) {
        echo '<p class="timeapp-info-icon">';
        echo '<i class="fa fa-' . $args['icon'] . ' ' . $class . '"></i>';
        echo '</p>';
    }

    echo '<p class="timeapp-info-desc">' . $header . $args['desc'] . '</p>';
    echo '</div>';
}


/**
 * Multicheck callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_multicheck_callback( $args ) {
    global $timeapp_options;

    if( ! empty( $args['options'] ) ) {
        foreach( $args['options'] as $key => $option ) {
            $enabled = ( isset( $timeapp_options[$args['id']][$key] ) ? $option : NULL );

            echo '<input name="timeapp_settings[' . $args['id'] . '][' . $key . ']" id="timeapp_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . ' />&nbsp;';
            echo '<label for="timeapp_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
        }
        echo '<p class="description">' . $args['desc'] . '</p>';
    }
}


/**
 * Number callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_number_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $max    = isset( $args['max'] ) ? $args['max'] : 999999;
    $min    = isset( $args['min'] ) ? $args['min'] : 0;
    $step   = isset( $args['step'] ) ? $args['step'] : 1;
    $size   = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Password callback
 * 
 * @since       2.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_password_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="password" class="' . $size . '-text" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" value="' . esc_attr( $value )  . '" />&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Radio callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_radio_callback( $args ) {
    global $timeapp_options;

    if( ! empty( $args['options'] ) ) {
        foreach( $args['options'] as $key => $option ) {
            $checked = false;

            if( isset( $timeapp_options[$args['id']] ) && $timeapp_options[$args['id']] == $key ) {
                $checked = true;
            } elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $timeapp_options[$args['id']] ) ) {
                $checked = true;
            }

            echo '<input name="timeapp_settings[' . $args['id'] . ']" id="timeapp_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
            echo '<label for="timeapp_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
        }

        echo '<p class="description">' . $args['desc'] . '</p>';
    }
}


/**
 * Select callback
 * 
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_select_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

    $html = '<select id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" placeholder="' . $placeholder . '" />';

    foreach( $args['options'] as $option => $name ) {
        $selected = selected( $option, $value, false );

        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    }

    $html .= '</select>&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Text callback
 * 
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_text_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="' . $size . '-text" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) )  . '" />&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Textarea callback
 * 
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_textarea_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $html  = '<textarea class="large-text" cols="50" rows="5" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Upload callback
 * 
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $timeapp_options The TimeApp options
 * @return      void
 */
function timeapp_upload_callback( $args ) {
    global $timeapp_options;

    if( isset( $timeapp_options[$args['id']] ) ) {
        $value = $timeapp_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="' . $size . '-text" id="timeapp_settings[' . $args['id'] . ']" name="timeapp_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
    $html .= '<span><input type="button" class="timeapp_settings_upload_button button-secondary" value="' . __( 'Upload File', 'timeapp' ) . '" /></span>&nbsp;';
    $html .= '<label for="timeapp_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    if( $value ) {
        $html .= '<br /><img src="' . $value . '" class="timeapp_settings_upload_image" />';
    }

    echo $html;
}


/**
 * Hook callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function timeapp_hook_callback( $args ) {
    do_action( 'timeapp_' . $args['id'] );
}


/**
 * Missing callback
 *
 * @since       2.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function timeapp_missing_callback( $args ) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'timeapp' ), $args['id'] );
}
