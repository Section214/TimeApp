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
                    <td class="timeapp-venue-title"><?php _e( 'Purchaser', 'timeapp' ); ?></td>
                    <td class="timeapp-date-title"><?php _e( 'Date', 'timeapp' ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $plays as $id => $play ) {
                        $date       = get_post_meta( $play->ID, '_timeapp_start_date', true );
                        $purchaser  = get_post_meta( $play->ID, '_timeapp_purchaser', true );
                        $purchaser  = get_post( $purchaser );

                        echo '<tr>';
                        echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $play->ID ) . '">' . $play->post_title . '</a></td>';
                        echo '<td>' . $purchaser->post_title . '</td>';
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
    $now        = date( 'Ymd', time() );
    $deposits   = array();
    $plays      = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish'
    ) );

    foreach( $plays as $id => $play ) {
        $has_deposits = get_post_meta( $play->ID, '_timeapp_deposit', true ) ? true : false;

        if( $has_deposits ) {
            $deposit1_date  = get_post_meta( $play->ID, '_timeapp_deposit1_date', true );
            $deposit1_paid  = get_post_meta( $play->ID, '_timeapp_deposit1_paid', true );
            $deposit1_amt   = get_post_meta( $play->ID, '_timeapp_deposit1_amt', true );
            $deposit2_date  = get_post_meta( $play->ID, '_timeapp_deposit2_date', true );
            $deposit2_paid  = get_post_meta( $play->ID, '_timeapp_deposit2_paid', true );
            $deposit2_amt   = get_post_meta( $play->ID, '_timeapp_deposit2_amt', true );
            $deposit3_date  = get_post_meta( $play->ID, '_timeapp_deposit3_date', true );
            $deposit3_paid  = get_post_meta( $play->ID, '_timeapp_deposit3_paid', true );
            $deposit3_amt   = get_post_meta( $play->ID, '_timeapp_deposit3_amt', true );
            $purchaser      = get_post_meta( $play->ID, '_timeapp_purchaser', true );
            $purchaser      = get_post( $purchaser );

            if( $deposit1_date && date( 'Ymd', strtotime( $deposit1_date ) ) < $now && ( ! $deposit1_paid || $deposit1_paid == '' ) ) {
                $deposits[$play->ID][1]['title']        = $play->post_title;
                $deposits[$play->ID][1]['date']         = $deposit1_date;
                $deposits[$play->ID][1]['amt']          = $deposit1_amt;
                $deposits[$play->ID][1]['purchaser']    = $purchaser->post_title;
            }

            if( $deposit2_date && date( 'Ymd', strtotime( $deposit2_date ) ) < $now && ( ! $deposit2_paid || $deposit2_paid == '' ) ) {
                $deposits[$play->ID][2]['title']        = $play->post_title;
                $deposits[$play->ID][2]['date']         = $deposit2_date;
                $deposits[$play->ID][2]['amt']          = $deposit2_amt;
                $deposits[$play->ID][2]['purchaser']    = $purchaser->post_title;
            }

            if( $deposit3_date && date( 'Ymd', strtotime( $deposit3_date ) ) < $now && ( ! $deposit3_paid || $deposit3_paid == '' ) ) {
                $deposits[$play->ID][3]['title']        = $play->post_title;
                $deposits[$play->ID][3]['date']         = $deposit3_date;
                $deposits[$play->ID][3]['amt']          = $deposit3_amt;
                $deposits[$play->ID][3]['purchaser']    = $purchaser->post_title;
            }
        }
    }

    echo '<div class="timeapp-dashboard-widget">';

    if( $deposits ) {
        ?>
        <table class="timeapp-past-due-deposits-widget">
            <thead>
                <tr>
                    <td class="timeapp-play-title"><?php _e( 'Play', 'timeapp' ); ?></td>
                    <td class="timeapp-venue-title"><?php _e( 'Purchaser', 'timeapp' ); ?></td>
                    <td class="timeapp-amount-title"><?php _e( 'Amount', 'timeapp' ); ?></td>
                    <td class="timeapp-date-title"><?php _e( 'Due Date', 'timeapp' ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $deposits as $id => $deposit ) {
                        if( array_key_exists( '1', $deposit ) ) {
                            echo '<tr>';
                            echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $id ) . '">' . $deposit[1]['title'] . '</a></td>';
                            echo '<td>' . $deposit[1]['purchaser'] . '</td>';
                            echo '<td>' . timeapp_format_price( $deposit[1]['amt'] ) . '</td>';
                            echo '<td>' . date( 'm/d/Y', strtotime( $deposit[1]['date'] ) ) . '</td>';
                            echo '</tr>';
                        }
                        
                        if( array_key_exists( '2', $deposit ) ) {
                            echo '<tr>';
                            echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $id ) . '">' . $deposit[2]['title'] . '</a></td>';
                            echo '<td>' . $deposit[2]['purchaser'] . '</td>';
                            echo '<td>' . timeapp_format_price( $deposit[2]['amt'] ) . '</td>';
                            echo '<td>' . date( 'm/d/Y', strtotime( $deposit[2]['date'] ) ) . '</td>';
                            echo '</tr>';
                        }
                        
                        if( array_key_exists( '3', $deposit ) ) {
                            echo '<tr>';
                            echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $id ) . '">' . $deposit[3]['title'] . '</a></td>';
                            echo '<td>' . $deposit[3]['purchaser'] . '</td>';
                            echo '<td>' . timeapp_format_price( $deposit[3]['amt'] ) . '</td>';
                            echo '<td>' . date( 'm/d/Y', strtotime( $deposit[3]['date'] ) ) . '</td>';
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

    // Quick hack to handle upating notes from the dashboard
    if( isset( $_POST['timeapp_play_id'] ) ) {
        if( isset( $_POST['_timeapp_followup_notes'] ) && $_POST['_timeapp_followup_notes'] != '' ) {
            update_post_meta( $_POST['timeapp_play_id'], '_timeapp_followup_notes', $_POST['_timeapp_followup_notes'] );
        } elseif( $_POST['_timeapp_followup_notes'] == '' ) {
            delete_post_meta( $_POST['timeapp_play_id'], '_timeapp_followup_notes' );
        }
    }

    echo '<div class="timeapp-dashboard-widget">';
    
    if( $plays ) {
        foreach( $plays as $id => $play ) {
            $date           = get_post_meta( $play->ID, '_timeapp_end_date', true );
            $purchaser      = get_post_meta( $play->ID, '_timeapp_purchaser', true );
            $purchaser      = get_post( $purchaser );
            $artist         = get_post_meta( $play->ID, '_timeapp_artist', true );
            $artist         = get_post( $artist );
            $contact_fname  = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );
            $contact_lname  = get_post_meta( $purchaser->ID, '_timeapp_last_name', true );
            $contact_email  = get_post_meta( $purchaser->ID, '_timeapp_email', true );
            $contact_phone  = get_post_meta( $purchaser->ID, '_timeapp_phone_number', true );
            $follow_up_notes= get_post_meta( $play->ID, '_timeapp_followup_notes', true );
            $contact_name   = '';

            // Is a contact first name specified?
            if( $contact_fname && $contact_fname != '' ) {
                $contact_name .= $contact_fname;
            }

            // Is a contact last name specified?
            if( $contact_lname && $contact_lname != '' ) {
                if( $contact_name != '' ) {
                    $contact_name .= ' ';
                }

                $contact_name .= $contact_lname;
            }

            // No contact name specified
            $contact_name = ( $contact_name != '' ? $contact_name : __( 'None Specified', 'timeapp' ) );
            ?>
            <form method="post">
                <table class="timeapp-follow-up-widget">
                    <thead>
                        <tr>
                            <td class="timeapp-play-title" colspan="2">
                                <?php echo $play->post_title; ?>
                                <span>
                                    <a href="<?php echo admin_url( 'post.php?action=edit&post=' . $play->ID ); ?>"><? _e( 'Edit', 'timeapp' ); ?></a>
                                    &nbsp;&middot;&nbsp;
                                    <a href="<?php echo wp_nonce_url( add_query_arg( array( 'timeapp-action' => 'update_meta', 'type' => 'play', 'id' => $play->ID, 'key' => '_timeapp_followed_up', 'value' => '1' ) ), 'update-meta', 'update-nonce' ); ?>#timeapp_follow_up"><?php _e( 'Mark as followed up', 'timeapp' ); ?></a>
                                </span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php _e( 'Play Date', 'timeapp' ); ?></td>
                            <td><?php echo $date; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e( 'Artist', 'timeapp' ); ?></td>
                            <td><?php echo $artist->post_title; ?></td>
                        </tr>
                        <tr>
                            <td><?php _e( 'Purchaser', 'timeapp' ); ?></td>
                            <td><?php echo $purchaser->post_title; ?></td>
                        </tr>
                        <?php if( $purchaser->post_title != $contact_name ) { ?>
                        <tr>
                            <td><?php _e( 'Contact', 'timeapp' ); ?></td>
                            <td><?php echo $contact_name; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if( $contact_email ) { ?>
                        <tr>
                            <td><?php _e( 'Email', 'timeapp' ); ?></td>
                            <td><?php echo '<a href="mailto:' . $contact_email . '">' . $contact_email . '</a>'; ?></td>
                        </tr>
                        <?php } ?>
                        <?php if( $contact_phone ) { ?>
                        <tr>
                            <td><?php _e( 'Phone Number', 'timeapp' ); ?></td>
                            <td><?php echo $contact_phone; ?></td>
                        </tr>
                        <?php } ?>
                        <tr class="timeapp-dashboard-notes">
                            <td colspan="2"><?php _e( 'Notes', 'timeapp' ); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><textarea name="_timeapp_followup_notes" id="_timeapp_followup_notes_<?php echo $play->ID; ?>"><?php echo $follow_up_notes; ?></textarea></td>
                        </tr>
                    </tbody>
                </table>

                <input type="hidden" name="timeapp_play_id" value="<?php echo $play->ID; ?>" />
                <?php submit_button(); ?>
            </form>
            <div class="timeapp-clear"></div>
            <?php
        }
    } else {
        _e( 'No plays require following up!', 'timeapp' );
    }

    echo '</div>';
}
