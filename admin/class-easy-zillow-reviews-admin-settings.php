<?php

/**
 * The admin settings of the plugin.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 *
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/admin
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_Admin_Settings' ) ) {
    
    class Easy_Zillow_Reviews_Admin_Settings {

        /**
         * The title of this plugin.
         *
         * @since    1.1.0
         * @access   private
         * @var      string    $plugin_name    The title of this plugin.
         */
        private $plugin_name;
        
        /**
         * The ID of this plugin.
         *
         * @since    1.1.0
         * @access   private
         * @var      string    $plugin_slug    The ID of this plugin.
         */
        private $plugin_slug;

        /**
         * The version of this plugin.
         *
         * @since    1.1.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /**
         *  The object that contains methods used to build the plugin settings.
         *
         * @since    1.1.0
         * @access   private
         * @var      array    $settings    The current version of this plugin.
         */
        private $settings;
        

        /**
         * The tabs that separate plugin options in the admin interface.
         *
         * @since    1.1.0
         * @access   private
         * @var      array    $tabs 
         */
        private $tabs;

        /**
         * The section organize fields in each tab.
         *
         * @since    1.1.0
         * @access   private
         * @var      array    $sections 
         */
        private $sections;
        
        public function __construct( $plugin_name, $plugin_slug, $version ) {

            $this->plugin_name = $plugin_name;
            $this->plugin_slug = $plugin_slug;
            $this->version = $version;
            $this->tabs = $this->create_tabs();
            $this->sections = $this->create_sections();
            $this->settings = $this->create_settings();
        }
        
        /**
         * Create the tabs for EasyZillowReviews
         * @return array
         */
        public function create_tabs() {

            $tabs[] = array(
                'General', //display name
                'ezrwp_general', //option group
                'ezrwp_general_options' // option name
            );
            $tabs[] = array(
                'Professional Reviews', //display name
                'ezrwp_professional_reviews', //option group
                'ezrwp_professional_reviews_options' // option name
            );
            return $tabs;
        }
        
        /**
         * Create the sections for EasyZillowReviews
         * @return array
         */
        public function create_sections() {
            // General
            $sections[] = array(
                'id'  => 'ezrwp_section_for_defaults', // id
                'title'  => __( 'Default Plugin Settings', 'ezrwp_general' ), // title
                'page'  => 'ezrwp_general' // page
            );
            $sections[] = array(
                'id'  => 'ezrwp_section_for_support', // id
                'title'  => __( 'Debugging Information', 'ezrwp_general' ), // title
                'page'  => 'ezrwp_general' // page
            );
            // Professional Reviews
            $sections[] = array(
                'id'  => 'ezrwp_section_for_zillow_professional_parameters', //id
                'title'  => __( 'Zillow Professional Profile', 'ezrwp_professional_reviews' ), //title
                'page'  => 'ezrwp_professional_reviews' //page
            );

            return $sections;
        }
        
        /**
         * Create the settings for EasyZillowReviews
         * @return array
         */
        public function create_settings() {

            // General Tab
            $settings[] = array(
                'id'        => 'ezrwp_count', // id. Used only internally
                'title'     => __( 'Review Count', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_count_number_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_layout', // id. Used only internally
                'title'     => __( 'Reviews Layout', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_layout_select_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_cols', // id. Used only internally
                'title'     => __( 'Grid Columns', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_cols_number_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_disclaimer', // id. Used only internally
                'title'     => __( 'Zillow Disclaimer', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_disclaimer_pill_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_hide_date', // id. Used only internally
                'title'     => __( 'Hide Review Date', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_hide_date_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_hide_stars', // id. Used only internally
                'title'     => __( 'Hide Review Stars', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_hide_stars_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_hide_reviewer_summary', // id. Used only internally
                'title'     => __( 'Hide Reviewer Description', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_hide_reviewer_summary_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_hide_profile_card', // id. Used only internally
                'title'     => __( 'Hide Reviews Summary', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_hide_profile_card_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_hide_view_all_link', // id. Used only internally
                'title'     => __( 'Hide "View All" Link', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_hide_view_all_link_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );
            $settings[] = array(
                'id'        => 'ezrwp_hide_zillow_logo', // id. Used only internally
                'title'     => __( 'Hide Zillow Logo', 'ezrwp_general' ), // title
                'callback'  => 'ezrwp_hide_zillow_logo_field_cb', // callback
                'tab'       => 'ezrwp_general', // page
                'section'   => 'ezrwp_section_for_defaults'
            );

            // Professional Reviews
            $settings[] = array(
                'id'        => 'ezrwp_bridge_token_1', // id. Used only internally
                'title'     => __( 'Bridge API Access Token', 'ezrwp_professional_reviews' ), // title
                'callback'  => 'ezrwp_bridge_token_text_field_cb', // callback
                'tab'       => 'ezrwp_professional_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_professional_parameters'
            );
            $settings[] = array(
                'id'        => 'ezrwp_zwsid', // id. Used only internally
                'title'     => __( 'Zillow Web Services ID (Deprecated)', 'ezrwp_professional_reviews' ), // title
                'callback'  => 'ezrwp_zwsid_text_field_cb', // callback
                'tab'       => 'ezrwp_professional_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_professional_parameters'
            );
            $settings[] = array(
                'id'        => 'ezrwp_screenname', // id. Used only internally
                'title'     => __( 'Zillow Screenname', 'ezrwp_professional_reviews' ), // title
                'callback'  => 'ezrwp_screenname_text_field_cb', // callback
                'tab'       => 'ezrwp_professional_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_professional_parameters'
            );
            return $settings;
        }

        /**
         * Set the value of tabs
         *
         * @return  self
         */ 
        public function set_tabs($tabs)
        {
            $this->tabs = $tabs;

            return $this;
        }

        /**
         * Get the value of tabs
         */ 
        public function get_tabs()
        {
            return $this->tabs;
        }

        /**
         * Get the value of settings
         */ 
        public function get_settings()
        {
            return $this->settings;
        }

        /**
         * Set the value of settings
         *
         * @return  self
         */ 
        public function set_settings(array $settings)
        {
            $this->settings = $settings;

            return $this;
        }

        /**
         * Get $sections
         *
         * @return  array
         */ 
        public function get_sections()
        {
            return $this->sections;
        }

        /**
         * Set $sections
         *
         * @param  array  $sections  $sections
         *
         * @return  self
         */ 
        public function set_sections(array $sections)
        {
            $this->sections = $sections;

            return $this;
        }
    }
}