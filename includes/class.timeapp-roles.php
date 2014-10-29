<?php
/**
 * Roles and capabilities
 *
 * @package     TimeApp\Roles
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'TimeApp_Roles' ) ) {


    /**
     * TimeApp_Roles class
     *
     * @since       1.0.0
     */
    class TimeApp_Roles {


        /**
         * Add new roles
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function add_roles() {
            add_role( 'staff', __( 'Time Staff', 'timeapp' ), array(
                'read'      => true
            ) );
        }


        /**
         * Add new staff-specific capabilities
         *
         * @access      public
         * @since       1.0.0
         * @global      object $wp_roles The WordPress roles object
         * @return      void
         */
        public function add_caps() {
            global $wp_roles;

            if( class_exists( 'WP_Roles' ) ) {
                if( ! isset( $wp_roles ) ) {
                    $wp_roles = new WP_Roles();
                }
            }

            if( is_object( $wp_roles ) ) {
                $capabilities = $this->get_core_caps();

                foreach( $capabilities as $cap_group ) {
                    foreach( $cap_group as $cap ) {
                        $wp_roles->add_cap( 'staff', $cap );
                        $wp_roles->add_cap( 'administrator', $cap );
                    }
                }
            }
        }


        /**
         * Get the core post type capabilities
         *
         * @access      public
         * @since       1.0.0
         * @return      array $capabilities The core capabilities
         */
        public function get_core_caps() {
            $capabilities       = array();
            $capability_types   = array( 'play', 'purchaser', 'artist', 'agent' );

            foreach( $capability_types as $capability_type ) {
                $capabilities[$capability_type] = array(
                    "edit_{$capability_type}",
                    "read_{$capability_type}",
                    "delete_{$capability_type}",
                    "edit_{$capability_type}s",
                    "edit_private_{$capability_type}s",
                    "edit_published_{$capability_type}s",
                    "edit_others_{$capability_type}s",
                    "publish_{$capability_type}s",
                    "read_private_{$capability_type}s",
                    "delete{$capability_type}s",
                    "delete_private_{$capability_type}s",
                    "delete_published_{$capability_type}s",
                    "delete_others_{$capability_type}s"
                );
            }

            return $capabilities;
        }
    }
}
