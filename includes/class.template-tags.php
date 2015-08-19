<?php
/**
 * Template tags
 *
 * @package     TimeApp\TemplateTags
 * @since       2.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


class TimeApp_Template_Tags {


    /**
     * @access      private
     * @since       2.0.0
     * @var         array $tags Container for storing all tags
     */
    private $tags;


    /**
     * @access      private
     * @since       2.0.0
     * @var         int $download_id The ID of a download
     */
    private $download_id;


    /**
     * Add a template tag
     *
     * @access      public
     * @since       2.0.0
     * @param       string $tag Tag to be replaced
     * @param       string $desc Description of the tag
     * @param       string $func The hook to run when tag is found
     * @return      void
     */
    public function add( $tag, $desc, $func ) {
        if( is_callable( $func ) ) {
            $this->tags[$tag] = array(
                'tag'       => $tag,
                'desc'      => $desc,
                'func'      => $func
            );
        }
    }


    /**
     * Remove a template tag
     *
     * @access      public
     * @since       2.0.0
     * @param       string $tag Tag to be removed
     * @return      void
     */
    public function remove( $tag ) {
        unset( $this->tags[$tag] );
    }


    /**
     * Check if $tag is a registered template tag
     *
     * @access      public
     * @since       2.0.0
     * @param       string $tag Tag to search for
     * @return      bool True if found, false otherwise
     */
    public function template_tag_exists( $tag ) {
        return array_key_exists( $tag, $this->tags );
    }


    /**
     * Returns a list of all tags
     *
     * @access      public
     * @since       2.0.0
     * @param       string $context The context to return tags for
     * @return      array $tags The available tags
     */
    public function get_tags() {
        $tags = $this->tags;

        return $tags;
    }


    /**
     * Search content for tags and filter through their hooks
     *
     * @access      public
     * @since       2.0.0
     * @param       string $content Content to search
     * @param       int $download_id The ID of a download
     * @return      string $new_content Filtered content
     */
    public function do_tags( $content, $download_id ) {
        // Ensure there is at least one tag
        if( empty( $this->tags ) || ! is_array( $this->tags ) ) {
            return $content;
        }

        $this->download_id = $download_id;

        $new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_tag' ), $content );

        $this->download_id = null;

        return $new_content;
    }


    /**
     * Do a specific tag
     *
     * @access      public
     * @since       2.0.0
     * @param       $m Message
     * @return      mixed
     */
    public function do_tag( $m ) {
        // Get tag
        $tag = $m[1];

        // Return tag if tag not set
        if( ! $this->template_tag_exists( $tag ) ) {
            return $m[0];
        }

        return call_user_func( $this->tags[$tag]['func'], $this->download_id, $tag );
    }
}


/**
 * Add a template tag
 *
 * @since       2.0.0
 * @param       string $tag Tag to be replaced
 * @param       string $desc Description of the tag
 * @param       string $func The hook to run when tag is found
 * @return      void
 */
function timeapp_add_template_tag( $tag, $desc, $func ) {
    TimeApp()->template_tags->add( $tag, $desc, $func );
}


/**
 * Remove a template tag
 *
 * @since       2.0.0
 * @param       string $tag Template tag to remove
 * @return      void
 */
function timeapp_remove_template_tag( $tag ) {
    TimeApp()->template_tags->remove( $tag );
}


/**
 * Check if a tag exists
 *
 * @since       2.0.0
 * @param       string $tag The string to check
 * @return      bool True if exists, false otherwise
 */
function timeapp_tag_exists( $tag ) {
    return TimeApp()->template_tags->email_tag_exists( $tag );
}


/**
 * Get all tags
 *
 * @since       2.0.0
 * @return      array The existing tags
 */
function timeapp_get_template_tags() {
    return TimeApp()->template_tags->get_tags();
}


/**
 * Get a formatted list of all available tags
 *
 * @since       2.0.0
 * @return      string The formatted list
 */
function timeapp_tags_list() {
    // The list
    $list = '';

    // Get all tags
    $tags = timeapp_get_template_tags();

    // Check
    if( count( $tags ) > 0 ) {
        foreach( $tags as $tag ) {
            // Add tag to list
            $list .= '<span class="timeapp-tag-name">{' . $tag['tag'] . '}</span><span class="timeapp-tag-desc">' . $tag['desc'] . '</span><br />';
        }
    }

    // Return the list
    return $list;
}


/**
 * Search content for tags and filter
 *
 * @since       2.0.0
 * @param       string $content Content to search
 * @param       int $play_id The ID of a given play
 * @return      string $content Filtered content
 */
function timeapp_do_tags( $content, $play_id ) {
    // Replace all tags
    $content = TimeApp()->template_tags->do_tags( $content, $play_id );

    return $content;
}


/**
 * Load tags
 *
 * @since       2.0.0
 * @return      void
 */
function timeapp_load_template_tags() {
    do_action( 'timeapp_add_template_tags' );
}
add_action( 'init', 'timeapp_load_template_tags', -999 );


/**
 * Add default tags
 *
 * @since       2.0.0
 * @return      void
 */
function timeapp_setup_template_tags() {
    // Setup default tags array
    $tags = array(
        array(
            'tag'       => 'sitename',
            'desc'      => __( 'Your site name', 'edd-pdf-stamper' ),
            'func'      => 'timeapp_template_tag_sitename'
        ),
        array(
            'tag'       => 'siteurl',
            'desc'      => __( 'Your site URL', 'edd-pdf-stamper' ),
            'func'      => 'timeapp_template_tag_siteurl'
        ),
        array(
            'tag'       => 'artist_name',
            'desc'      => __( 'The name of the artist', 'timeapp' ),
            'func'      => 'timeapp_template_tag_artist_name'
        ),
        array(
            'tag'       => 'purchaser_name',
            'desc'      => __( 'The first name of the purchaser contact', 'timeapp' ),
            'func'      => 'timeapp_template_tag_purchaser_name'
        ),
        array(
            'tag'       => 'purchaser_fullname',
            'desc'      => __( 'The full name of the purchaser contact', 'timeapp' ),
            'func'      => 'timeapp_template_tag_purchaser_fullname'
        ),
        array(
            'tag'       => 'start_date',
            'desc'      => __( 'The start date of the event', 'timeapp' ),
            'func'      => 'timeapp_template_tag_start_date'
        ),
        array(
            'tag'       => 'end_date',
            'desc'      => __( 'The end date of the event', 'timeapp' ),
            'func'      => 'timeapp_template_tag_end_date'
        )
    );

    $tags = apply_filters( 'timeapp_template_tags', $tags );

    foreach( $tags as $tag ) {
        timeapp_add_template_tag( $tag['tag'], $tag['desc'], $tag['func'] );
    }
}
add_action( 'timeapp_add_template_tags', 'timeapp_setup_template_tags' );


/**
 * Template tag: sitename
 *
 * @since       2.0.0
 * @return      string Site name
 */
function timeapp_template_tag_sitename() {
    return get_bloginfo( 'name' );
}


/**
 * Template tag: siteurl
 *
 * @since       2.0.0
 * @return      string Site URL
 */
function timeapp_template_tag_siteurl() {
    return get_site_url();
}


/**
 * Template tag: artist_name
 *
 * @since       2.0.0
 * @param       int $play_id The ID of this play
 * @return      string Artist name
 */
function timeapp_template_tag_artist_name( $play_id ) {
    $artist = get_post_meta( $play_id, '_timeapp_artist', true );
    $artist = get_post( $artist );

    return $artist->post_title;
}


/**
 * Template tag: purchaser_name
 *
 * @since       2.0.0
 * @param       int $play_id The ID of this play
 * @return      string Purchaser first name
 */
function timeapp_template_tag_purchaser_name( $play_id ) {
    $purchaser  = get_post_meta( $play_id, '_timeapp_purchaser', true );
    $purchaser  = get_post( $purchaser );
    $name       = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );

    return $name;
}


/**
 * Template tag: purchaser_fullname
 *
 * @since       2.0.0
 * @param       int $play_id The ID of this play
 * @return      string Purchaser full name
 */
function timeapp_template_tag_purchaser_fullname( $play_id ) {
    $purchaser  = get_post_meta( $play_id, '_timeapp_purchaser', true );
    $purchaser  = get_post( $purchaser );
    $name       = get_post_meta( $purchaser->ID, '_timeapp_first_name', true );
    $last_name  = get_post_meta( $purchaser->ID, '_timeapp_last_name', true );

    return $name . ' ' . $last_name;
}


/**
 * Template tag: start_date
 *
 * @since       2.0.0
 * @param       int $play_id The ID of this play
 * @return      string Event start date
 */
function timeapp_template_tag_start_date( $play_id ) {
    $start_date = get_post_meta( $play_id, '_timeapp_start_date', true );
    $start_date = ( isset( $start_date ) && ! empty( $start_date ) ? date( 'm/d/Y g:i a', strtotime( $start_date ) ) : '' );

    return $start_date;
}


/**
 * Template tag: end_date
 *
 * @since       2.0.0
 * @param       int $play_id The ID of this play
 * @return      string Event end date
 */
function timeapp_template_tag_end_date( $play_id ) {
    $end_date = get_post_meta( $play_id, '_timeapp_end_date', true );
    $end_date = ( isset( $end_date ) && ! empty( $end_date ) ? date( 'm/d/Y g:i a', strtotime( $end_date ) ) : '' );

    return $end_date;
}
