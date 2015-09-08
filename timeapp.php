<?php
/**
 * Plugin Name:     TimeApp
 * Plugin URI:      http://ingroupconsulting.com/
 * Description:     Time Management's internal workflow tool
 * Version:         2.0.3
 * Author:          Kiko Doran
 * Author URI:      http://ingroupconsulting.com/
 * Text Domain:     timeapp
 *
 * @package         TimeApp
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'TimeApp' ) ) {


    /**
     * Main TimeApp class
     *
     * @since       1.0.0
     */
    class TimeApp {


        /**
         * @var         TimeApp $instance The one true TimeApp
         * @since       1.0.0
         */
        private static $instance;


        /**
         * @var         object $template_tags The template tags object
         * @since       2.0.0
         */
        public $template_tags;


        /**
         * @var         object $roles The TimeApp roles object
         * @since       1.0.0
         */
        public $roles;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true TimeApp
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new TimeApp();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
                self::$instance->template_tags = new TimeApp_Template_Tags();
                self::$instance->roles = new TimeApp_Roles();
            }

            return self::$instance;
        }


        /**
         * Throw error on object clone
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'timeapp' ), TIMEAPP_VER );
        }


        /**
         * Disable unserializing of the class
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'timeapp' ), TIMEAPP_VER );
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'TIMEAPP_VER', '2.0.3' );

            // Plugin path
            define( 'TIMEAPP_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'TIMEAPP_URL', plugin_dir_url( __FILE__ ) );

            // Plugin file
            define( 'TIMEAPP_FILE', __FILE__ );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @global      array $timeapp_options The TimeApp options array
         * @return      void
         */
        private function includes() {
            global $timeapp_options;

            require_once TIMEAPP_DIR . 'includes/admin/settings/register.php';
            $timeapp_options = timeapp_get_settings();

            require_once TIMEAPP_DIR . 'includes/functions.php';
            require_once TIMEAPP_DIR . 'includes/scripts.php';
            require_once TIMEAPP_DIR . 'includes/class.template-tags.php';
            require_once TIMEAPP_DIR . 'includes/class.timeapp-roles.php';

            if( is_admin() ) {
                require_once TIMEAPP_DIR . 'includes/admin/settings/display.php';
                require_once TIMEAPP_DIR . 'includes/class.textualizer.php';
                require_once TIMEAPP_DIR . 'includes/post-types.php';
                require_once TIMEAPP_DIR . 'includes/admin/actions.php';
                require_once TIMEAPP_DIR . 'includes/admin/pages.php';
                require_once TIMEAPP_DIR . 'includes/admin/meta-boxes.php';
                require_once TIMEAPP_DIR . 'includes/admin/admin-bar.php';
                require_once TIMEAPP_DIR . 'includes/admin/dashboard.php';
                require_once TIMEAPP_DIR . 'includes/admin/bulk-actions.php';
                require_once TIMEAPP_DIR . 'includes/admin/dashboard-columns.php';
                require_once TIMEAPP_DIR . 'includes/admin/dashboard-widgets.php';
                require_once TIMEAPP_DIR . 'includes/admin/notices.php';
                require_once TIMEAPP_DIR . 'includes/install.php';
            }
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'timeapp_lang_dir', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'timeapp', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/timeapp/' . $locale;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/timeapp/ folder
                load_textdomain( 'timeapp', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/timeapp/languages/ folder
                load_textdomain( 'timeapp', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'timeapp', false, $lang_dir );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true TimeApp
 * instance to functions everywhere.
 *
 * @since       1.0.0
 * @return      TimeApp The one true TimeApp instance
 */
function TimeApp() {
    return TimeApp::instance();
}

// Off we go!
TimeApp();
