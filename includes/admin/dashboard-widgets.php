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

    wp_add_dashboard_widget(
        'timeapp_commissions_due',
        __( 'Commissions Due', 'timeapp' ),
        'timeapp_commissions_due_widget'
    );

    wp_add_dashboard_widget(
        'timeapp_total_commissions',
        __( 'Total Commissions', 'timeapp' ),
        'timeapp_total_commissions_widget'
    );

    wp_add_dashboard_widget(
        'timeapp_split_commissions',
        __( 'Split Commissions', 'timeapp' ),
        'timeapp_split_commissions_widget'
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
    $latest = date( 'Ymd', time() + ( 86400 * 7 ) );
    $now    = date( 'Ymd', time() );
    
    $plays = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
    ) );

    echo '<div class="timeapp-dashboard-widget">';

    foreach( $plays as $key => $play ) {
        $date = get_post_meta( $play->ID, '_timeapp_start_date', true );
        $date = date( 'Ymd', strtotime( $date ) );

        if( $date < $now || $date >= $latest ) {
            unset( $plays[$key] );
        }
    }

    if( $plays ) {
        ?>
        <table class="timeapp-upcoming-plays-widget">
            <thead>
                <tr>
                    <td class="timeapp-play-title"><?php _e( 'Play', 'timeapp' ); ?></td>
                    <td class="timeapp-venue-title"><?php _e( 'Purchaser', 'timeapp' ); ?></td>
                    <td class="timeapp-date-title"><?php _e( 'Date', 'timeapp' ); ?></td>
                    <td class="timeapp-edit-title"></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $plays as $id => $play ) {
                        $date       = get_post_meta( $play->ID, '_timeapp_start_date', true );
                        $artist     = get_post_meta( $play->ID, '_timeapp_artist', true );
                        $artist     = get_post( $artist );
                        $purchaser  = get_post_meta( $play->ID, '_timeapp_purchaser', true );
                        $purchaser  = get_post( $purchaser );

                        echo '<tr>';
                        echo '<td>' . $artist->post_title . '</td>';
                        echo '<td>' . $purchaser->post_title . '</td>';
                        echo '<td>' . $date . '</td>';
                        echo '<td><a href="' . admin_url( 'post.php?action=edit&post=' . $play->ID ) . '">' . __( 'Edit', 'timeapp' ) . '</a></td>';
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
            $purchaser      = get_post_meta( $play->ID, '_timeapp_purchaser', true );
            $purchaser      = get_post( $purchaser );

            if( $deposit1_date && date( 'Ymd', strtotime( $deposit1_date ) ) < $now && ( ! $deposit1_paid || $deposit1_paid == '' ) ) {
                $deposits[$play->ID]['deposit'][1]['date']         = $deposit1_date;
                $deposits[$play->ID]['deposit'][1]['amt']          = $deposit1_amt;
            }

            if( ! $deposit1_date && ( ! $deposit1_paid || $deposit1_paid == '' ) ) {
                $deposits[$play->ID]['deposit'][1]['date']         = $now;
                $deposits[$play->ID]['deposit'][1]['amt']          = $deposit1_amt;
            }

            if( isset( $deposits[$play->ID] ) ) {
                $deposits[$play->ID]['title']        = $play->post_title;
                $deposits[$play->ID]['purchaser']    = $purchaser->post_title;
            }
        }
    }

    echo '<div class="timeapp-dashboard-widget">';

    if( $deposits ) {
        foreach( $deposits as $id => $play ) {
        ?>
            <form method="post">
                <table class="timeapp-past-due-deposits-widget">
                    <thead>
                        <tr>
                            <td class="timeapp-play-title" colspan="3">
                                <?php echo $play['title']; ?>
                                <span>
                                    <a href="<?php echo admin_url( 'post.php?action=edit&post=' . $id ); ?>"><?php _e( 'Edit', 'timeapp' ); ?></a>
                                </span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if( isset( $play['deposit'][1] ) ) {
                            echo '<tr>';
                            echo '<td>' . sprintf( __( 'Deposit due %s', 'timeapp' ), date( 'm/d/Y', strtotime( $play['deposit'][1]['date'] ) ) ) . '</td>';
                            echo '<td>' . timeapp_format_price( $play['deposit'][1]['amt'] ) . '</td>';
                            echo '<td><a href="' . wp_nonce_url( add_query_arg( array( 'timeapp-action' => 'update_meta', 'type' => 'play', 'id' => $id, 'key' => '_timeapp_deposit1_paid', 'value' => '1' ) ), 'update-meta', 'update-nonce' ) . '#timeapp_past_due_deposits">' . __( 'Mark as paid', 'timeapp' ) . '</a></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
            </table>
        <?php
        }
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
    $now    = date( 'Ymd', time() );
    
    // Quick hack to handle upating notes from the dashboard
    if( isset( $_POST['timeapp_play_id'] ) ) {
        if( isset( $_POST['_timeapp_followup_notes'] ) && $_POST['_timeapp_followup_notes'] != '' ) {
            update_post_meta( $_POST['timeapp_play_id'], '_timeapp_followup_notes', $_POST['_timeapp_followup_notes'] );
        } elseif( isset( $_POST['_timeapp_followup_notes'] ) && $_POST['_timeapp_followup_notes'] == '' ) {
            delete_post_meta( $_POST['timeapp_play_id'], '_timeapp_followup_notes' );
        }
    }

    $plays = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_followed_up',
                'compare'   => 'NOT EXISTS'
            )
        )
    ) );

    foreach( $plays as $key => $play ) {
        $date = get_post_meta( $play->ID, '_timeapp_start_date', true );

        if( date( 'Ymd', strtotime( $date ) ) >= $now ) {
            unset( $plays[$key] );
        }
    }

    echo '<div class="timeapp-dashboard-widget">';
    
    if( $plays ) {
        foreach( $plays as $id => $play ) {
            $date           = get_post_meta( $play->ID, '_timeapp_start_date', true );

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


/**
 * Render Commissions Due widget
 *
 * @since       1.1.0
 * @return      void
 */
function timeapp_commissions_due_widget() {
    $now    = date( 'Ymd', time() );
    
    // Quick hack to handle upating commissions from the dashboard
    if( isset( $_POST['timeapp_play_id'] ) ) {
        if( isset( $_POST['_timeapp_date_paid'] ) && $_POST['_timeapp_date_paid'] != '' ) {
            update_post_meta( $_POST['timeapp_play_id'], '_timeapp_date_paid', $_POST['_timeapp_date_paid'] );
        }
    }

    $args = array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_date_paid',
                'value'     => '',
                'compare'   => '='
            )
        )
    );

    $has_filter = false;

    if( isset( $_POST['filter_month'] ) && ! empty( $_POST['filter_month'] ) ) {
        $date = explode( '-', $_POST['filter_month'] );

        $args['meta_query'][] = array(
            'key'       => '_timeapp_start_date',
            'value'     => $date[1] . '(.*)' . $date[0] . '(.*)',
            'compare'   => 'REGEXP'
        );
        
        $has_filter = true;
    }

    if( isset( $_POST['filter_artist'] ) && ! empty( $_POST['filter_artist'] ) ) {
        $args['meta_query'][] = array(
            'key'       => '_timeapp_artist',
            'value'     => $_POST['filter_artist'],
            'compare'   => '='
        );

        $has_filter = true;
    }

    $plays = get_posts( $args );

    foreach( $plays as $key => $play ) {
        $date = get_post_meta( $play->ID, '_timeapp_start_date', true );

        if( date( 'Ymd', strtotime( $date ) ) >= $now ) {
            unset( $plays[$key] );
        }
    }


    echo '<div class="timeapp-dashboard-widget">';
    
    if( $plays || $has_filter ) {
        $artists    = timeapp_get_artists();
        $months     = timeapp_get_months( true );
        ?>
        <div class="timeapp-dashboard-widget-filters">
            <form method="post">
                <?php
                    echo '<select name="filter_month">';
                    echo '<option value="">' . __( 'Show all months', 'timeapp' ) . '</option>';
                    foreach( $months as $id => $month ) {
                        $selected = isset( $_POST['filter_month'] ) && $_POST['filter_month'] == $id ? ' selected="selected"' : '';
                        echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $month ) . '</option>';
                    }
                    echo '</select>';

                    echo '<select name="filter_artist">';
                    echo '<option value="">' . __( 'Show all artists', 'timeapp' ) . '</option>';
                    foreach( $artists as $id => $artist ) {
                        $selected = isset( $_POST['filter_artist'] ) && $_POST['filter_artist'] == $id ? ' selected="selected"' : '';
                        echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $artist ) . '</option>';
                    }
                    echo '</select>';

                    submit_button( __( 'Filter', 'timeapp' ), 'secondary', 'timeapp_filter', false );
                ?>
            </form>
        </div>
        <?php
        if( $plays ) {
            foreach( $plays as $id => $play ) {
                $date           = get_post_meta( $play->ID, '_timeapp_start_date', true );

                $purchaser      = get_post_meta( $play->ID, '_timeapp_purchaser', true );
                $purchaser      = get_post( $purchaser );
                $artist         = get_post_meta( $play->ID, '_timeapp_artist', true );
                $artist         = get_post( $artist );
                $contact_fname  = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );
                $contact_lname  = get_post_meta( $purchaser->ID, '_timeapp_last_name', true );
                $contact_email  = get_post_meta( $purchaser->ID, '_timeapp_email', true );
                $contact_phone  = get_post_meta( $purchaser->ID, '_timeapp_phone_number', true );
                $guarantee      = get_post_meta( $play->ID, '_timeapp_guarantee', true );
                $production     = get_post_meta( $play->ID, '_timeapp_production', true );
                $production_cost= ( ! $production ? get_post_meta( $play->ID, '_timeapp_production_cost', true ) : false );
                $commission_rate= get_post_meta( $artist->ID, '_timeapp_commission', true );
                $split_comm     = get_post_meta( $play->ID, '_timeapp_split_comm', true );
                $split_rate     = ( $split_comm ? get_post_meta( $play->ID, '_timeapp_split_perc', true ) : false );
                $commission_rcvd= get_post_meta( $play->ID, '_timeapp_date_paid', true );
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

                $commission = timeapp_get_commission( $guarantee, $production_cost, $commission_rate, $split_rate );

                ?>
                <form method="post">
                    <table class="timeapp-commissions-due-widget">
                        <thead>
                            <tr>
                                <td class="timeapp-play-title" colspan="2">
                                    <?php echo $play->post_title; ?>
                                    <span>
                                        <a href="<?php echo admin_url( 'post.php?action=edit&post=' . $play->ID ); ?>"><? _e( 'Edit', 'timeapp' ); ?></a>
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
                                <td><?php _e( 'Commission', 'timeapp' ); ?></td>
                                <td><?php echo $commission; ?></td>
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
                            <tr class="timeapp-date-paid">
                                <td><?php _e( 'Date Paid', 'timeapp' ); ?></td>
                                <td><input type="text" id="_timeapp_date_paid_<?php echo $play->ID; ?>" name="_timeapp_date_paid" class="widefat timeapp-datetime" /></td>
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
            echo '<i class="dashicons dashicons-smiley"></i> ' . __( 'No results found for that filter!', 'timeapp' );
        }
    } else {
        echo '<i class="dashicons dashicons-smiley"></i> ' . __( 'Congratulations! You have reached eternal bliss... Nobody owes you any money!', 'timeapp' );
    }

    echo '</div>';
}


/**
 * Render Total Commissions widget
 *
 * @since       1.3.1
 * @return      void
 */
function timeapp_total_commissions_widget() {
    $args = array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
        )
    );

    if( isset( $_POST['filter_comm_month'] ) && ! empty( $_POST['filter_comm_month'] ) ) {
        $date = explode( '-', $_POST['filter_comm_month'] );

        $args['meta_query'][] = array(
            'key'       => '_timeapp_start_date',
            'value'     => $date[1] . '(.*)' . $date[0] . '(.*)',
            'compare'   => 'REGEXP'
        );
    }

    $plays      = get_posts( $args );
    $months     = timeapp_get_months();
    $total_comm = 0;
    $active     = __( 'all time', 'timeapp' );

    echo '<div class="timeapp-dashboard-widget">';
    ?>
    <div class="timeapp-dashboard-widget-filters">
        <form method="post">
            <?php
                echo '<select name="filter_comm_month">';
                echo '<option value="">' . __( 'Show all months', 'timeapp' ) . '</option>';
                foreach( $months as $id => $month ) {
                    $selected = isset( $_POST['filter_comm_month'] ) && $_POST['filter_comm_month'] == $id ? ' selected="selected"' && $active = $month : '';
                    echo '<option value="' . $id . '"' . $selected . '>' . esc_html( $month ) . '</option>';
                }
                echo '</select>';

                submit_button( __( 'Filter', 'timeapp' ), 'secondary', 'timeapp_filter', false );
            ?>
        </form>
    </div>
    <?php
    if( $plays ) {
        foreach( $plays as $id => $play ) {
            $artist         = get_post_meta( $play->ID, '_timeapp_artist', true );
            $artist         = get_post( $artist );
            $guarantee      = get_post_meta( $play->ID, '_timeapp_guarantee', true );
            $production     = get_post_meta( $play->ID, '_timeapp_production', true );
            $production_cost= ( ! $production ? get_post_meta( $play->ID, '_timeapp_production_cost', true ) : false );
            $commission_rate= get_post_meta( $artist->ID, '_timeapp_commission', true );
            $split_comm     = get_post_meta( $play->ID, '_timeapp_split_comm', true );
            $split_rate     = ( $split_comm ? get_post_meta( $play->ID, '_timeapp_split_perc', true ) : false );
                
            $commission = timeapp_get_commission( $guarantee, $production_cost, $commission_rate, $split_rate );
            $commission = str_replace( '$', '', $commission );
            $total_comm = $total_comm + (float) $commission;
        }
    }

    echo __( 'Total commissions for', 'timeapp' ) . ' ' . $active . ': ' . timeapp_format_price( $total_comm );
    echo '</div>';
}


/**
 * Render Split Commissions widget
 *
 * @since       1.1.1
 * @return      void
 */
function timeapp_split_commissions_widget() {
    $now        = date( 'Ymd', time() );
    $plays      = get_posts( array(
        'post_type'     => 'play',
        'numberposts'   => 999999,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'      => 'AND',
            array(
                'key'       => '_timeapp_split_comm',
                'compare'   => 'EXISTS'
            ),
            array(
                'key'       => '_timeapp_split_paid',
                'compare'   => 'NOT EXISTS'
            )
        )
    ) );
    
    foreach( $plays as $key => $play ) {
        $date = get_post_meta( $play->ID, '_timeapp_start_date', true );

        if( date( 'Ymd', strtotime( $date ) ) >= $now ) {
            unset( $plays[$key] );
        }
    }

    echo '<div class="timeapp-dashboard-widget">';

    if( $plays ) {
        foreach( $plays as $id => $play ) {
            $purchaser      = get_post_meta( $play->ID, '_timeapp_purchaser', true );
            $purchaser      = get_post( $purchaser );
            $artist         = get_post_meta( $play->ID, '_timeapp_artist', true );
            $artist         = get_post( $artist );
            $agent          = get_post_meta( $play->ID, '_timeapp_split_agent', true );
            $agent          = get_post( $agent );
            $guarantee      = get_post_meta( $play->ID, '_timeapp_guarantee', true );
            $production     = get_post_meta( $play->ID, '_timeapp_production', true );
            $production_cost= ( ! $production ? get_post_meta( $play->ID, '_timeapp_production_cost', true ) : false );
            $commission_rate= get_post_meta( $artist->ID, '_timeapp_commission', true );
            $split_comm     = get_post_meta( $play->ID, '_timeapp_split_comm', true );
            $split_rate     = ( $split_comm ? get_post_meta( $play->ID, '_timeapp_split_perc', true ) : false );
            ?>
            <form method="post">
                <table class="timeapp-split-commissions-widget">
                    <thead>
                        <tr>
                            <td class="timeapp-play-title" colspan="3">
                                <?php echo $play->post_title; ?>
                                <span>
                                    <a href="<?php echo admin_url( 'post.php?action=edit&post=' . $play->ID ); ?>"><?php _e( 'Edit', 'timeapp' ); ?></a>
                                </span>
                            </td>
                        </tr>
                    </thead>
                        <tr>
                            <td><?php echo $agent->post_title; ?></td>
                            <td><?php echo timeapp_get_commission( $guarantee, $production_cost, $commission_rate, $split_rate, true ); ?></td>
                            <td><a href="<?php echo wp_nonce_url( add_query_arg( array( 'timeapp-action' => 'update_meta', 'type' => 'play', 'id' => $play->ID, 'key' => '_timeapp_split_paid', 'value' => '1' ) ), 'update-meta', 'update-nonce' ); ?>#timeapp_split_commissions"><?php _e( 'Mark as paid', 'timeapp' ); ?></a></td>
                    <tbody>

                    </tbody>
            </table>
            <?php
        }
    } else {
        _e( 'No unpaid split commissions found!', 'timeapp' );
    }

    echo '</div>';
}
