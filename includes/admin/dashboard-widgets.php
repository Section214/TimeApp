<?php
/**
 * Dashboard Widgets
 *
 * @package     TimeApp\Admin\DashboardWidgets
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Remove the default dashboard widgets
 *
 * @since       1.0.0
 * @global      array $wp_meta_boxes Registered meta boxes
 * @return      void
 */
function timeapp_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    
    if( ! current_user_can( 'manage_options' ) ) {
        unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
        unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
    }
}
add_action( 'wp_dashboard_setup', 'timeapp_remove_dashboard_widgets' );


/**
 * Register the dashboard widgets
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_register_dashboard_widgets() {
    wp_add_dashboard_widget(
        'timeapp_upcoming_plays',
        __( 'Upcoming Plays', 'timeapp' ),
        'timeapp_upcoming_plays_widget'
    );

    wp_add_dashboard_widget(
        'timeapp_past_due_deposits',
        __( 'Past Due Deposits', 'timeapp' ),
        'timeapp_past_due_deposits_widget'
    );

    wp_add_dashboard_widget(
        'timeapp_follow_up',
        __( 'Follow Up', 'timeapp' ),
        'timeapp_follow_up_widget'
    );
}
add_action( 'wp_dashboard_setup', 'timeapp_register_dashboard_widgets', 10 );


/**
 * Render Upcoming Plays widget
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_upcoming_plays_widget() {
    $latest = date( 'm/d/Y g:i a', time() + ( 86400 * 7 ) );
    $now    = date( 'm/d/Y g:i a', time() );
    
    $plays = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_start_date',
                'value'     => $latest,
                'compare'   => '<'
            ),
            array(
                'key'       => '_timeapp_start_date',
                'value'     => $now,
                'compare'   => '>='
            )
        )
    ) );

    echo '<div class="timeapp-dashboard-widget">';
    
    if( $plays ) {
        ?>
        <table class="timeapp-upcoming-plays-widget">
            <thead>
                <tr>
                    <td class="timeapp-play-title"><?php _e( 'Play', 'timeapp' ); ?></td>
                    <td class="timeapp-venue-title"><?php _e( 'Venue', 'timeapp' ); ?></td>
                    <td class="timeapp-date-title"><?php _e( 'Date', 'timeapp' ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $plays as $id => $play ) {
                        $date = get_post_meta( $play->ID, '_timeapp_start_date', true );

                        echo '<tr>';
                        echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $play->ID ) . '">' . $play->post_title . '</a></td>';
                        echo '<td></td>';
                        echo '<td>' . $date . '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        _e( 'No upcoming plays found!', 'timeapp' );
    }

    echo '</div>';
}


/**
 * Render Past Due Deposits widget
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_past_due_deposits_widget() {
    $now    = date( 'm/d/Y g:i a', time() );

    $deposit1 = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_deposit',
                'compare'   => 'EXISTS',
            ),
            array(
                'key'       => '_timeapp_deposit1_date',
                'value'     => $now,
                'compare'   => '<'
            ),
            array(
                'key'       => '_timeapp_deposit1_date',
                'value'     => '',
                'compare'   => '!='
            ),
            array(
                'key'       => '_timeapp_deposit1_paid',
                'compare'   => 'NOT EXISTS'
            )
        )
    ) );
    
    $deposit2 = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_deposit',
                'compare'   => 'EXISTS'
            ),
            array(
                'key'       => '_timeapp_deposit2_date',
                'value'     => $now,
                'compare'   => '<'
            ),
            array(
                'key'       => '_timeapp_deposit2_date',
                'value'     => '',
                'compare'   => '!='
            ),
            array(
                'key'       => '_timeapp_deposit2_paid',
                'compare'   => 'NOT EXISTS'
            )
        )
    ) );
    
    $deposit3 = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_deposit',
                'compare'   => 'EXISTS'
            ),
            array(
                'key'       => '_timeapp_deposit3_date',
                'value'     => $now,
                'compare'   => '<'
            ),
            array(
                'key'       => '_timeapp_deposit3_date',
                'value'     => '',
                'compare'   => '!='
            ),
            array(
                'key'       => '_timeapp_deposit3_paid',
                'compare'   => 'NOT EXISTS'
            )
        )
    ) );

    $all_deposits   = array_merge( $deposit1, $deposit2, $deposit3 );
    $deposits       = array();

    foreach( $all_deposits as $id => $deposit ) {
        if( ! array_key_exists( $deposit->ID, $all_deposits ) ) {
            $deposits[$deposit->ID] = $deposit;
        }
    }

    echo '<div class="timeapp-dashboard-widget">';

    if( $deposits ) {
        ?>
        <table class="timeapp-past-due-deposits-widget">
            <thead>
                <tr>
                    <td class="timeapp-play-title"><?php _e( 'Play', 'timeapp' ); ?></td>
                    <td class="timeapp-venue-title"><?php _e( 'Venue', 'timeapp' ); ?></td>
                    <td class="timeapp-date-title"><?php _e( 'Due Date', 'timeapp' ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $deposits as $id => $deposit ) {
                        $deposit1 = get_post_meta( $deposit->ID, '_timeapp_deposit1_date', true );
                        $deposit2 = get_post_meta( $deposit->ID, '_timeapp_deposit2_date', true );
                        $deposit3 = get_post_meta( $deposit->ID, '_timeapp_deposit3_date', true );

                        if( $deposit1 && $deposit1 < $now ) {
                            echo '<tr>';
                            echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $deposit->ID ) . '">' . $deposit->post_title . '</a></td>';
                            echo '<td></td>';
                            echo '<td>' . $deposit1 . '</td>';
                            echo '</tr>';
                        }
                        
                        if( $deposit2 && $deposit2 < $now ) {
                            echo '<tr>';
                            echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $deposit->ID ) . '">' . $deposit->post_title . '</a></td>';
                            echo '<td></td>';
                            echo '<td>' . $deposit2 . '</td>';
                            echo '</tr>';
                        }
                        
                        if( $deposit3 && $deposit3 < $now ) {
                            echo '<tr>';
                            echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $deposit->ID ) . '">' . $deposit->post_title . '</a></td>';
                            echo '<td></td>';
                            echo '<td>' . $deposit3 . '</td>';
                            echo '</tr>';
                        }
                    }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        _e( 'No past due deposits found!', 'timeapp' );
    }

    echo '</div>';
}


/**
 * Render Follow Up widget
 *
 * @since       1.0.0
 * @return      void
 */
function timeapp_follow_up_widget() {
    $now    = date( 'm/d/Y g:i a', time() );
    
    $plays = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_end_date',
                'value'     => $now,
                'compare'   => '<'
            ),
            array(
                'key'       => '_timeapp_followed_up',
                'compare'   => 'NOT EXISTS'
            )
        )
    ) );

    echo '<div class="timeapp-dashboard-widget">';
    
    if( $plays ) {
        ?>
        <table class="timeapp-follow-up-widget">
            <thead>
                <tr>
                    <td class="timeapp-play-title"><?php _e( 'Play', 'timeapp' ); ?></td>
                    <td class="timeapp-venue-title"><?php _e( 'Venue', 'timeapp' ); ?></td>
                    <td class="timeapp-date-title"><?php _e( 'Date', 'timeapp' ); ?></td>
                    <td class="timeapp-follow-up-title"><?php _e( 'Followed Up?', 'timeapp' ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $plays as $id => $play ) {
                        $date = get_post_meta( $play->ID, '_timeapp_end_date', true );

                        echo '<tr>';
                        echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $play->ID ) . '">' . $play->post_title . '</a></td>';
                        echo '<td></td>';
                        echo '<td>' . $date . '</td>';
                        echo '<td><a href="' . wp_nonce_url( add_query_arg( array( 'timeapp-action' => 'update_meta', 'type' => 'play', 'id' => $play->ID, 'key' => '_timeapp_followed_up', 'value' => '1' ) ), 'update-meta', 'update-nonce' ) . '#timeapp_follow_up">' . __( 'Update', 'timeapp' ) . '</a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        _e( 'No plays require following up!', 'timeapp' );
    }

    echo '</div>';
}
