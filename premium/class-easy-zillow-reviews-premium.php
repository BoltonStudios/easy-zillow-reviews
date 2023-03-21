<?php

/**
 * The premium-version functionality of the plugin.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 *
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/premium
 */

/**
 * Easy Zillow Reviews Premium includes
 */
require_once plugin_dir_path( __FILE__ ) . 'class-easy-zillow-reviews-lender.php';
require_once plugin_dir_path( __FILE__ ) . 'class-easy-zillow-reviews-lender-shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'class-easy-zillow-reviews-lender-widget.php';

if ( ! class_exists( 'Easy_Zillow_Reviews_Premium' ) ) {

    /**
     * Easy Zillow Reviews Premium Main Class
     */
    class Easy_Zillow_Reviews_Premium{

        /**
         *
         *
         * @since    1.1.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Base    $base_plugin
         */
        private $base_plugin;
        
        /**
         *
         *
         * @since    1.1.0
         * @access   protected
         * @var      Easy_Zillow_Reviews_Admin    $base_plugin_admin
         */
        private $base_plugin_admin;
        
        /**
         *
         *
         * @since    1.1.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Admin_Settings   $base_plugin_settings
         */
        private $base_plugin_settings;
        
        /**
         *
         *
         * @since    1.1.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Admin_Settings    $premium_settings
         */
        private $premium_settings;

        public function __construct($base_plugin)
        {
            $this->base_plugin = $base_plugin;
            $this->base_plugin_settings = $base_plugin->get_plugin_settings();
            $this->base_plugin_admin = $base_plugin->get_plugin_admin();
            $this->premium_settings = $this->add_premium_settings();

            $this->init();
        }
        function init(){
            
            $this->get_base_plugin()->set_plugin_settings(
                $this->get_premium_settings()
            );
        }

        /**
         *  Premium Version Settings
         */
        function add_premium_settings(){

            // Get default settings
            $settings = $this->get_base_plugin_settings();

            // Define Premium Version Callbacks

            // Lender Reviews Section
            function ezrwp_section_for_zillow_lender_parameters_cb( $id ) {
                
                $output = '
                <hr />

                <p id="'. $id['id'] .'-2">
                    <strong style="font-size: 14px">Shortcode</strong><br/>[ez-zillow-lender-reviews]
                </p>
                <p id="'. $id['id'] .'-3">
                    Example shortcode with overrides:<br />[ez-zillow-lender-reviews columns="2" count="4" excerpt="30" layout="grid"]
                </p>
                ';
                echo $output;
            }

            // ZMPID callback
            function ezrwp_zmpid_text_field_cb( $args ) {
                        
                // Get the value of the setting we've registered with register_setting()
                $options = get_option('ezrwp_lender_reviews_options');
                
                $setting = ''; // Zillow Mortgages Partner ID (ZWPID)
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Zillow Mortgages Partner ID</label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_lender_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

                <p>Sign-up for a <strong>Zillow Mortgages Partner ID</strong> at <a href="https://www.zillow.com/mortgage/api/#/" target="_blank">https://www.zillow.com/mortgage/api/#/</a>. <span class="dashicons dashicons-external" style="font-size: 14px;"></span> Select <strong>Zillow Lender Reviews</strong> when asked to select your API.</p>

                <?php
            }

            // Zillow NMLSID callback
            function ezrwp_nmlsid_text_field_cb( $args ) {
                
                $options = get_option('ezrwp_lender_reviews_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">NMLS Number</label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_lender_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

                <p>The NMLS # of the lender whose reviews you want to display.</p>

                <?php
            }

            // Zillow Lender Company Name callback
            function ezrwp_company_name_text_field_cb( $args ) {
                
                $options = get_option('ezrwp_lender_reviews_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Lender Company Name</label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_lender_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

                <p><strong>For institutional lenders only.</strong> Leave blank for individual loan officers. The Company Name of the lender whose reviews you want to display, exactly as it appears on Zillow. Example "Great  Loans, LLC".</p>

                <?php
            }

            // Add premium settings
            // Override settings tabs
            $premium_settings_tabs = $settings->get_tabs();
            $premium_settings_tabs["premium"] = array(
                'Lender Reviews', //display name
                'ezrwp_lender_reviews', //option group
                'ezrwp_lender_reviews_options' // option name
            );
            $settings->set_tabs($premium_settings_tabs);

            // Override settings sections
            $premium_settings_sections = $settings->get_sections();
            /*$premium_settings_sections[] = array(
                'id'    => 'ezrwp_section_for_premium_callout', // id
                'title' => __( 'Upgrade to Easy Zillow Reviews Premium', 'ezrwp_lender_reviews' ), // title
                'page'  => 'ezrwp_lender_reviews' // page
            );*/

            $premium_settings_sections[] = array(
                'id'    => 'ezrwp_section_for_zillow_lender_parameters', // id
                'title' => __( 'Zillow Lender Profile', 'ezrwp_lender_reviews' ), // title
                'page'  => 'ezrwp_lender_reviews' // page
            );
            $settings->set_sections($premium_settings_sections);

            // Override settings fields
            $premium_settings_fields = $settings->get_settings();
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_zmpid', // id. Used only internally
                'title'     => __( 'Zillow Mortgages Partner ID', 'ezrwp_lender_reviews' ), // title
                'callback'  => 'ezrwp_zmpid_text_field_cb', // callback
                'tab'       => 'ezrwp_lender_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_lender_parameters'
            );
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_nmlsid', // id. Used only internally
                'title'     => __( 'NMLS#', 'ezrwp_lender_reviews' ), // title
                'callback'  => 'ezrwp_nmlsid_text_field_cb', // callback
                'tab'       => 'ezrwp_lender_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_lender_parameters'
            );
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_company_name', // id. Used only internally
                'title'     => __( 'Company Name', 'ezrwp_lender_reviews' ), // title
                'callback'  => 'ezrwp_company_name_text_field_cb', // callback
                'tab'       => 'ezrwp_lender_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_lender_parameters'
            );
            $settings->set_settings($premium_settings_fields);

            // Return premium settings
            return $settings;
        }

        // Getters & Setters
        /**
         * Get $base_plugin
         *
         * @return  Easy_Zillow_Reviews_Base
         */ 
        public function get_base_plugin()
        {
            return $this->base_plugin;
        }

        /**
         * Set $base_plugin
         *
         * @param  Easy_Zillow_Reviews_Base  $base_plugin  $base_plugin
         *
         * @return  self
         */ 
        public function set_base_plugin(Easy_Zillow_Reviews_Base $base_plugin)
        {
            $this->base_plugin = $base_plugin;

            return $this;
        }
        /**
         * Get $base_plugin_admin
         *
         * @return  Easy_Zillow_Reviews_Admin
         */ 
        public function get_base_plugin_admin()
        {
            return $this->base_plugin_admin;
        }

        /**
         * Set $base_plugin_admin
         *
         * @param  Easy_Zillow_Reviews_Admin  $base_plugin_admin  $base_plugin_admin
         *
         * @return  self
         */ 
        public function set_base_plugin_admin(Easy_Zillow_Reviews_Admin $base_plugin_admin)
        {
            $this->base_plugin_admin = $base_plugin_admin;

            return $this;
        }

        /**
         * Get $base_plugin_settings
         *
         * @return  Easy_Zillow_Reviews_Admin_Settings
         */ 
        public function get_base_plugin_settings()
        {
            return $this->base_plugin_settings;
        }

        /**
         * Set $base_plugin_settings
         *
         * @param  Easy_Zillow_Reviews_Admin_Settings  $base_plugin_settings  $base_plugin_settings
         *
         * @return  self
         */ 
        public function set_base_plugin_settings(Easy_Zillow_Reviews_Admin_Settings $base_plugin_settings)
        {
            $this->base_plugin_settings = $base_plugin_settings;

            return $this;
        }

        /**
         * Get $premium_settings
         *
         * @return  Easy_Zillow_Reviews_Admin_Settings
         */ 
        public function get_premium_settings()
        {
            return $this->premium_settings;
        }

        /**
         * Set $premium_settings
         *
         * @param  Easy_Zillow_Reviews_Admin_Settings  $premium_settings  $premium_settings
         *
         * @return  self
         */ 
        public function set_premium_settings(Easy_Zillow_Reviews_Admin_Settings $premium_settings)
        {
            $this->premium_settings = $premium_settings;

            return $this;
        }
    }
}