<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/admin
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_Admin' ) ) {

    class Easy_Zillow_Reviews_Admin {

        // Properties
        /**
         * The title of this plugin.
         *
         * @since    1.1.0
         * @access   private
         * @var      string    $plugin_name   The title of this plugin.
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
         * The tabs that separate plugin options in the admin interface.
         *
         * @since    1.1.0
         * @access   private
         * @var      string  
         */
        private $tabs;

        /**
         * The object that contains methods used to build the plugin settings.
         *
         * @since    1.1.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Admin_Settings 
         */
        private $settings;

        // Constructor
        /**
         * Initialize the class and set its properties.
         *
         * @since	1.1.0
         * @param	string								$plugin_name	The title of this plugin.
         * @param	string								$plugin_slug	The id of this plugin.
         * @param	string								$version		The version of this plugin.
         * @param	Easy_Zillow_Reviews_Admin_Settings	$settings		The default settings for this plugin.
         */
        public function __construct( $plugin_name, $plugin_slug, $version, $settings ) {

            $this->plugin_name = $plugin_name;
            $this->plugin_slug = $plugin_slug;
            $this->version = $version;
            $this->settings = $settings;
            $this->tabs = $settings->get_tabs();
        }

        // Methods
        /**
         * Add plugin admin options page
         *
         * @since    1.1.0
         */
        public function add_options_page(){
            
            add_menu_page(
                
                $this->plugin_name, // $page_title
                'Zillow Reviews', // $menu_title
                'manage_options', // $capability
                $this->plugin_slug, // $menu_slug
                array($this, 'render_settings_page'), // $function,
                'dashicons-star-filled' // string $icon_url
            );
        }

        /**
         * Add action links to the plugin in the Plugins list table
         *
         * @since    1.1.0
         */
        public function admin_plugin_listing_actions( $links ) {
            
            $action_links = [];
            $action_links = array(
                'settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'domain' ) . '</a>',
            );
            return array_merge( $action_links, $links );
        }

        /**
         * Register the plugin settings
         *
         * @since    1.1.0
         */
        public function init_settings(){

            // Register sections in the settings page
            register_setting(
                'ezrwp_lender_reviews', // option group
                'ezrwp_lender_reviews_options' // option name
            );
            // Tabs
            foreach($this->tabs as $tab){
                register_setting(
                    $tab[1], // option group
                    $tab[2] // option name
                );
            }
            // Sections
            foreach($this->settings->get_sections() as $section){
                add_settings_section(
                    $section['id'], // id
                    $section['title'], // title
                    $section['id'] . '_cb', // callback
                    $section['page'] // page
                );
            }
            // Fields
            foreach($this->settings->get_settings() as $setting){
                add_settings_field(
                    $setting['id'],
                    $setting['title'],
                    $setting['callback'],
                    $setting['tab'],
                    $setting['section'], [
                        'label_for' => $setting['id'],
                        'class' => 'ezrwp_row',
                        'ezrwp_custom_data' => 'custom',
                    ]
                );
            }

            /*
            *
            *  Free Plugin Version Callbacks
            * 
            */

            // Section Callbacks
            // Section callbacks accept an $args parameter, which is an array.
            // $args have the following keys defined: id, title, callback.
            // the values are defined at the add_settings_section() function.

            // Professional Reviews Section
            function ezrwp_section_for_zillow_professional_parameters_cb( $args ) {
                ?>
                <hr />
                <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
                    <strong style="font-size: 14px">Shortcode</strong><br/>[ez-zillow-reviews]
                </p>
                <p id="<?php echo esc_attr( $args['id'] ); ?>-3">
                    Example shortcode with overrides:<br />[ez-zillow-reviews layout="grid" columns="2" count="4"]
                </p>
                <?php
            }
            function ezrwp_section_for_defaults_cb( $args ) {
                ?>
                <hr />
                <p id="<?php echo esc_attr( $args['id'] ); ?>">
                    Please find the default plugin settings below. You may override the default settings using the widget, block, and shortcode options.
                </p>
                <?php
            }
            function ezrwp_section_for_appearance_cb( $args ) {
                ?>
                <hr />
                <?php
            }
            function ezrwp_section_for_support_cb( $args ) {
                
                $simpleXML_is_loaded = extension_loaded ("SimpleXML") 
                    ? 'SimpleXML is loaded.'
                    : 'SimpleXML is not loaded.';
                $allow_url = ini_get( 'allow_url_fopen' )
                    ? 'allow_url_fopen is enabled.'
                    : 'allow_url_fopen is disabled.';
                ?>
                <hr />
                <p>
                    Your PHP version is <?php echo PHP_VERSION; ?>. 
                    <?php echo $simpleXML_is_loaded; ?> 
                    <?php echo $allow_url; ?>
                </p>
                <hr />
                <?php
            }
            // Field Callbacks
            // Field callbacks can accept an $args parameter, which is an array.
            // $args is defined at the add_settings_field() function.
            // wordpress has magic interaction with the following keys: label_for, class.
            // the "label_for" key value is used for the "for" attribute of the <label>.
            // the "class" key value is used for the "class" attribute of the <tr> containing the field.
            // you can add custom key value pairs to be used inside your callbacks.

            // Bridge API Access Token callback
            function ezrwp_bridge_token_text_field_cb( $args ) {
                
                // Get the value of the setting we've registered with register_setting()
                $options = get_option('ezrwp_professional_reviews_options');
                
                $setting = ''; // Bridge API Access Token
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Bridge API Access Token</label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_professional_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

                <p><a href="https://bridgedataoutput.com/login" target="_blank">Login to Bridge</a> or <a href="https://bridgedataoutput.com/zgdata" target="_blank">create a new account <span class="dashicons dashicons-external" style="font-size: 14px;"></span></a> and request access to the API to get a <strong>Bridge API Access Token</strong>.
                <?php
            }

            // ZWSID callback
            function ezrwp_zwsid_text_field_cb( $args ) {
                
                // Get the value of the setting we've registered with register_setting()
                $options = get_option('ezrwp_professional_reviews_options');
                
                $setting = ''; // Zillow Web Services ID (ZWSID)
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Zillow Web Services ID</label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_professional_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

                <p>Sign-up for a <strong>Zillow Web Services ID (ZWSID)</strong> at <a href="https://www.zillow.com/howto/api/APIOverview.htm" target="_blank">https://www.zillow.com/howto/api/APIOverview.htm</a>. <span class="dashicons dashicons-external" style="font-size: 14px;"></span></p>

                <?php
            }
            
            // Zillow Screenname callback
            function ezrwp_screenname_text_field_cb( $args ) {

                $options = get_option('ezrwp_professional_reviews_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Zillow Screenname</label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_professional_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

                <p>The screenname of the user whose reviews you want to display.</p>

                <?php
            }
            
            // Zillow Review Count callback
            function ezrwp_count_number_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = 1;
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Review Count</label>
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="1" max="10" required />

                <p>The count of reviews you would like to return. Choose a number from 1 to 10.</p>
                <?php
            }

            // Zillow Review Layout callback
            function ezrwp_layout_select_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>
                    
                <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
                        class="ezrwp-setting ezrwp_layout"
                        data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>"
                        name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
                    <option value="list" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'list', false ) ) : ( '' ); ?>>
                    <?php esc_html_e( 'List', 'ezrwp' ); ?>
                    </option>
                    <option value="grid" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'grid', false ) ) : ( '' ); ?>>
                    <?php esc_html_e( 'Grid', 'ezrwp' ); ?>
                    </option>
                </select>

                <?php
            }

            // Zillow Reviews Grid Columns callback
            function ezrwp_cols_number_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = 3;
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Grid Columns</label>
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="2" max="6" required />

                <?php
            }

            // Zillow Mandatory Disclaimer callback
            function ezrwp_disclaimer_pill_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>
                    
                <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
                        class="ezrwp-setting"
                        data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>"
                        name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
                    <option value="0" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '0', false ) ) : ( '' ); ?>>
                    <?php esc_html_e( 'On', 'ezrwp' ); ?>
                    </option>
                    <option value="1" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '1', false ) ) : ( '' ); ?>>
                    <?php esc_html_e( 'Off', 'ezrwp' ); ?>
                    </option>
                </select>

                <div id="disclaimer-warning">
                    <p><strong>Notice</strong>: Please add the disclaimer below somewhere on your website if you turn off the Disclaimer Setting on this page. According to Zillow's <a href="https://www.zillow.com/howto/api/BrandingRequirements.htm" target="_blank">Branding Requirements</a> <span class="dashicons dashicons-external" style="font-size: 14px;"></span>, <em>All pages that contain Zillow Data or tools must include the following text, typically at the bottom of the page</em>:<p>

                    <blockquote>
                        © Zillow, Inc., 2006-2016. Use is subject to <a href="https://www.zillow.com/corp/Terms.htm">Terms of Use</a><br />
                        <a href="https://www.zillow.com/wikipages/What-is-a-Zestimate/">What's a Zestimate?</a>
                    </blockquote>
                </div>
            <?php
            }

            // Zillow Hide Date callback
            function ezrwp_hide_date_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Date</label>
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The time elapsed since the review was written. Example: 6 days ago.
            <?php
            }

            // Zillow Hide Stars callback
            function ezrwp_hide_stars_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Stars</label>
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The star rating for each review.
            <?php
            }

            // Zillow Hide Reviewer Description callback
            function ezrwp_hide_reviewer_summary_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Reviewer Description</label>
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> A short description of the reviewer. Example: "Sold a Single Family home in 2013 for approximately $500K in Roswell, GA."
            <?php
            }

            // Zillow Hide Profile Card callback
            function ezrwp_hide_profile_card_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Reviews Summary</label>
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The reviews summary badge with overall star rating for the profile.
            <?php
            }

            // Zillow Hide View All Link callback
            function ezrwp_hide_view_all_link_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide View All Link</label>
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The link to your Zillow profile labeled "View All Reviews".
            <?php
            }

            // Zillow Hide View All Reviews Link callback
            function ezrwp_hide_zillow_logo_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Zillow Logo</label>
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> Important: Consult the <a href="https://www.zillow.com/howto/api/BrandingRequirements.htm" target="_blank">Zillow Branding Requirements</a> <span class="dashicons dashicons-external" style="font-size: 14px;"></span> before you hide the Zillow logo.
            <?php
            }

            // Zillow Review Quote Font Size callback
            function ezrwp_quote_font_size_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Review Text Font Size</label>
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="1" />

                <p>The font size for the review quote text in pixels. Default is 18px.</p>

                <?php
            }

            // Zillow Reviewer Description Font Size callback
            function ezrwp_reviewer_description_font_size_field_cb( $args ) {
                
                $options = get_option('ezrwp_general_options');
                
                $setting = '';
                if( isset( $options[$args['label_for']] ) ){
                    $setting = $options[$args['label_for']];
                };
                ?>

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Review Text Font Size</label>
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="1" />

                <p>The font size for the reviewer description text in pixels. Default is 16px.</p>

                <?php
            }
            function ezrwp_section_for_premium_callout_cb( $args ) {
                ?>
                <p id="<?php echo esc_attr( $args['id'] ); ?>-0">The Premium License unlocks the following features:</p>
                <ul id="<?php echo esc_attr( $args['id'] ); ?>-1">
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Team Reviews</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Lender Reviews</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Individual Loan Officer Reviews</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Company Profile Reviews</li>
                </ul>
                <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
                    <a href="<?php echo ezrwp_fs()->get_upgrade_url(); ?>" class="button">Upgrade to Premium</a>
                </p>
                <?php
            }
        }
        
        /**
         * Include the HTML code to display the settings page tabs and more.
         *
         * @since    1.1.0
         */
        public function render_settings_page() {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/easy-zillow-reviews-admin-display.php';
        }

        /**
         * Register the stylesheets for the admin area.
         *
         * @since    1.1.0
         */
        public function enqueue_styles() {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Easy_Zillow_Reviews_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Easy_Zillow_Reviews_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/easy-zillow-reviews-admin.css', array(), $this->version, 'all' );

        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.1.0
         */
        public function enqueue_scripts() {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Easy_Zillow_Reviews_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Easy_Zillow_Reviews_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'js/easy-zillow-reviews-admin.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( $this->plugin_slug, plugins_url( $this->plugin_slug ) . '/gutenberg/build/index.js', null, $this->version, false );
        }
    }
}