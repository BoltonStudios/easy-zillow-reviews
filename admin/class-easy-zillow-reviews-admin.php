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


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
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
         * @var      object  
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

            add_filter( 'ezrwp_sanitize_ezrwp_general_options', array( $this, 'sanitize_general_options' ) );
            add_filter( 'ezrwp_sanitize_ezrwp_professional_reviews_options', array( $this, 'sanitize_professional_reviews_options' ) );
            add_action( 'wp_ajax_ezrwp_test_connection', array( $this, 'ajax_test_connection' ) );
            add_action( 'admin_notices', array( $this, 'setup_notice' ) );
            add_action( 'admin_post_ezrwp_dismiss_setup_notice', array( $this, 'dismiss_setup_notice' ) );
        }

        /**
         * Sanitize the General options before saving.
         *
         * @since    1.7.0
         * @param    array    $input    The submitted options.
         * @return   array
         */
        public function sanitize_general_options( $input ) {

            $layouts = apply_filters( 'ezrwp_available_layouts', array( 'list', 'grid' ) );
            $output = array();

            $output['ezrwp_count'] = isset( $input['ezrwp_count'] ) ? min( max( absint( $input['ezrwp_count'] ), 1 ), 10 ) : 3;
            $output['ezrwp_layout'] = ( isset( $input['ezrwp_layout'] ) && in_array( $input['ezrwp_layout'], $layouts, true ) ) ? $input['ezrwp_layout'] : 'list';
            $output['ezrwp_cols'] = isset( $input['ezrwp_cols'] ) ? min( max( absint( $input['ezrwp_cols'] ), 1 ), 6 ) : 3;

            foreach( array( 'ezrwp_disclaimer', 'ezrwp_hide_date', 'ezrwp_hide_stars', 'ezrwp_hide_reviewer_summary', 'ezrwp_hide_profile_card', 'ezrwp_hide_view_all_link', 'ezrwp_hide_zillow_logo' ) as $key ){
                $output[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
            }

            foreach( array( 'ezrwp_quote_font_size' => 18, 'ezrwp_review_description_font_size' => 16 ) as $key => $default ){
                $output[ $key ] = ( isset( $input[ $key ] ) && absint( $input[ $key ] ) > 0 ) ? min( absint( $input[ $key ] ), 72 ) : $default;
            }

            /**
             * Filters the sanitized General options, so extensions can save their own options.
             *
             * @since 1.7.0
             * @param array $output The sanitized options.
             * @param array $input  The submitted options.
             */
            return apply_filters( 'ezrwp_sanitize_general_extra', $output, $input );
        }

        /**
         * Sanitize the Zillow Professional options before saving.
         *
         * @since    1.7.0
         * @param    array    $input    The submitted options.
         * @return   array
         */
        public function sanitize_professional_reviews_options( $input ) {

            $saved = get_option( 'ezrwp_professional_reviews_options' );
            $output = array(
                'ezrwp_bridge_token_1' => isset( $input['ezrwp_bridge_token_1'] ) ? preg_replace( '/[^A-Za-z0-9_\-]/', '', $input['ezrwp_bridge_token_1'] ) : '',
                'ezrwp_screenname'     => isset( $input['ezrwp_screenname'] ) ? sanitize_text_field( Easy_Zillow_Reviews_Professional::normalize_screenname( $input['ezrwp_screenname'] ) ) : '',
            );

            // Keep a retired ZWSID so the plugin can explain that it is no longer supported.
            if( is_array( $saved ) && ! empty( $saved['ezrwp_zwsid'] ) && $output['ezrwp_bridge_token_1'] === '' ){
                $output['ezrwp_zwsid'] = sanitize_text_field( $saved['ezrwp_zwsid'] );
            }

            /**
             * Filters the sanitized Zillow Professional options, so extensions can save their own options.
             *
             * @since 1.7.0
             * @param array $output The sanitized options.
             * @param array $input  The submitted options.
             */
            return apply_filters( 'ezrwp_sanitize_professional_reviews_extra', $output, $input );
        }

        /**
         * Check the Bridge token and screenname entered on the settings page.
         *
         * @since    1.7.0
         */
        public function ajax_test_connection() {

            check_ajax_referer( 'ezrwp_test_connection' );

            if( ! current_user_can( 'manage_options' ) ){
                wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'easy-zillow-reviews' ) ), 403 );
            }

            $type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : 'professional';
            $fields = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['fields'] ) ) : array();

            /**
             * Filters the result of a connection test.
             *
             * @since 1.7.0
             * @param array|null $result The result: array( 'success' => bool, 'message' => string ), or null if the type is not handled.
             * @param string     $type   The connection type, e.g. 'professional'.
             * @param array      $fields The values entered on the settings page.
             */
            $result = apply_filters( 'ezrwp_test_connection', null, $type, $fields );

            if( ! is_array( $result ) ){
                wp_send_json_error( array( 'message' => __( 'Unknown connection type.', 'easy-zillow-reviews' ) ) );
            }

            if( $result['success'] ){
                wp_send_json_success( array( 'message' => $result['message'] ) );
            }

            wp_send_json_error( array( 'message' => $result['message'] ) );
        }

        /**
         * Print a "Test connection" button for a settings section.
         *
         * @since    1.7.0
         * @param    string    $type      The connection type, e.g. 'professional'.
         * @param    array     $fields    Field name => input ID pairs to send.
         */
        public static function test_connection_button( $type, $fields ) {
            ?>
            <p class="ezrwp-test-connection" data-type="<?php echo esc_attr( $type ); ?>" data-fields="<?php echo esc_attr( wp_json_encode( $fields ) ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ezrwp_test_connection' ) ); ?>">
                <button type="button" class="button button-secondary"><?php esc_html_e( 'Test connection', 'easy-zillow-reviews' ); ?></button>
                <span class="ezrwp-test-connection-result" role="status" aria-live="polite"></span>
            </p>
            <?php
        }

        /**
         * Ask site administrators to finish setting up the plugin.
         *
         * @since    1.7.0
         */
        public function setup_notice() {

            if( ! current_user_can( 'manage_options' ) ){
                return;
            }

            $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
            if( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'plugins' ), true ) ){
                return;
            }

            if( get_user_meta( get_current_user_id(), 'ezrwp_dismissed_setup_notice', true ) ){
                return;
            }

            $options = get_option( 'ezrwp_professional_reviews_options' );
            $has_token = is_array( $options ) && ! empty( $options['ezrwp_bridge_token_1'] );
            $has_zwsid = is_array( $options ) && ! empty( $options['ezrwp_zwsid'] );

            /**
             * Filters whether the plugin is configured, e.g. Premium can report configured lender reviews.
             *
             * @since 1.7.0
             * @param bool $configured Whether reviews can be displayed.
             */
            if( apply_filters( 'ezrwp_is_configured', $has_token ) ){
                return;
            }

            $settings_url = admin_url( 'admin.php?page=' . $this->plugin_slug . '&tab=ezrwp_professional_reviews' );
            $dismiss_url = wp_nonce_url( admin_url( 'admin-post.php?action=ezrwp_dismiss_setup_notice' ), 'ezrwp_dismiss_setup_notice' );
            $message = $has_zwsid
                ? __( 'Zillow retired the Zillow Web Services ID (ZWSID), so Easy Zillow Reviews cannot show your reviews until you add a free Bridge Server Token. Getting one takes about 5 minutes.', 'easy-zillow-reviews' )
                : __( 'Easy Zillow Reviews needs a free Bridge Server Token and your Zillow screenname to show your reviews. Getting the token takes about 5 minutes.', 'easy-zillow-reviews' );
            ?>
            <div class="notice notice-warning">
                <p>
                    <?php echo esc_html( $message ); ?>
                    <a href="<?php echo esc_url( $settings_url ); ?>" class="button button-primary" style="margin-left: 6px;"><?php esc_html_e( 'Set up now', 'easy-zillow-reviews' ); ?></a>
                    <a href="<?php echo esc_url( $dismiss_url ); ?>" style="margin-left: 6px;"><?php esc_html_e( 'Dismiss', 'easy-zillow-reviews' ); ?></a>
                </p>
            </div>
            <?php
        }

        /**
         * Dismiss the setup notice for the current user.
         *
         * @since    1.7.0
         */
        public function dismiss_setup_notice() {

            check_admin_referer( 'ezrwp_dismiss_setup_notice' );
            update_user_meta( get_current_user_id(), 'ezrwp_dismissed_setup_notice', 1 );
            wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
            exit;
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
                'settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'easy-zillow-reviews' ) . '</a>',
            );
            return array_merge( $action_links, $links );
        }

        /**
         * Register the plugin settings
         *
         * @since    1.1.0
         */
        public function init_settings(){

            // Settings for each tab, including tabs added by Premium.
            foreach( $this->settings->get_tabs() as $tab ){
                $option_name = $tab[2];
                register_setting(
                    $tab[1], // option group
                    $option_name, // option name
                    array(
                        'sanitize_callback' => function( $value ) use ( $option_name ) {

                            /**
                             * Filters the submitted value of an Easy Zillow Reviews option before it is saved.
                             *
                             * @since 1.7.0
                             * @param array $value The submitted value.
                             */
                            return apply_filters( 'ezrwp_sanitize_' . $option_name, is_array( $value ) ? $value : array() );
                        },
                    )
                );
            }
            // Sections
            foreach($this->settings->get_sections() as $section){
                add_settings_section(
                    $section['id'], // id
                    $section['title'], // title
                    isset( $section['callback'] ) ? $section['callback'] : $section['id'] . '_cb', // callback
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
                <div class="ezrwp-setup-guide">
                    <p><strong><?php esc_html_e( 'How to connect your Zillow reviews', 'easy-zillow-reviews' ); ?></strong> <?php esc_html_e( '(about 5 minutes)', 'easy-zillow-reviews' ); ?></p>
                    <ol>
                        <li><?php
                            printf(
                                /* translators: %s: link to Bridge Interactive's Zillow data registration form */
                                esc_html__( 'Fill out Bridge\'s free registration form at %s. Bridge is the company Zillow uses to share its data. On the form:', 'easy-zillow-reviews' ),
                                '<a href="https://bridgedataoutput.com/zgdata" target="_blank" rel="noopener noreferrer">bridgedataoutput.com/zgdata</a>'
                            );
                            ?>
                            <ul>
                                <li><?php
                                    printf(
                                        /* translators: 1: the form field name "Website", 2: this site's address */
                                        esc_html__( '%1$s: the site where your reviews will appear, e.g. %2$s', 'easy-zillow-reviews' ),
                                        '<strong>Website</strong>',
                                        '<code>' . esc_html( home_url() ) . '</code>'
                                    );
                                ?></li>
                                <li><?php
                                    printf(
                                        /* translators: 1: the form field name "Product Name", 2: the form field name "Product Type", 3: the value "Website" */
                                        esc_html__( '%1$s: your business name. %2$s: %3$s', 'easy-zillow-reviews' ),
                                        '<strong>Product Name</strong>',
                                        '<strong>Product Type</strong>',
                                        'Website'
                                    );
                                ?></li>
                                <li><?php
                                    printf(
                                        /* translators: %s: the dataset name, "Zillow Agent Reviews" */
                                        esc_html__( 'Datasets: check %s', 'easy-zillow-reviews' ),
                                        '<strong>Zillow Agent Reviews</strong>'
                                    );
                                ?></li>
                            </ul>
                            <?php esc_html_e( 'The form says requests can take 10+ days, but Zillow Agent Reviews is usually approved right away.', 'easy-zillow-reviews' ); ?>
                        </li>
                        <li><?php esc_html_e( 'Click the link in Bridge\'s verification email (check your spam folder), then log in.', 'easy-zillow-reviews' ); ?></li>
                        <li><?php
                            printf(
                                /* translators: 1: "API Access", 2: "Show Token", 3: "Server Token" (labels in the Bridge dashboard) */
                                esc_html__( 'Open %1$s and click %2$s next to %3$s. Paste it below. (Not the Client ID, Client Secret, or Browser Token.)', 'easy-zillow-reviews' ),
                                '<strong>API Access</strong>',
                                '<strong>Show Token</strong>',
                                '<strong>Server Token</strong>'
                            );
                        ?></li>
                        <li><?php esc_html_e( 'Enter your Zillow screenname (the part after "zillow.com/profile/"), or paste your Zillow profile address.', 'easy-zillow-reviews' ); ?></li>
                        <li><?php esc_html_e( 'Click "Test connection", then "Save Settings".', 'easy-zillow-reviews' ); ?></li>
                    </ol>
                    <p><?php
                        printf(
                            /* translators: 1: "Log in" link to Bridge, 2: "Create New Application", 3: "Zillow Agent Reviews", 4: "Data Access" (labels in the Bridge dashboard) */
                            esc_html__( 'Already have a Bridge account? %1$s, choose %2$s from the account menu, then request %3$s under %4$s.', 'easy-zillow-reviews' ),
                            '<a href="https://bridgedataoutput.com/login" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Log in', 'easy-zillow-reviews' ) . '</a>',
                            '<strong>Create New Application</strong>',
                            '<strong>Zillow Agent Reviews</strong>',
                            '<strong>Data Access</strong>'
                        );
                    ?></p>
                </div>
                <hr />
                <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
                    <strong style="font-size: 14px">Shortcode</strong><br/>[ez-zillow-reviews]
                </p>
                <p id="<?php echo esc_attr( $args['id'] ); ?>-3">
                    Example shortcode with overrides:<br />[ez-zillow-reviews columns="2" count="4" excerpt="30" layout="grid" screenname="jsmith"]
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
                
                ?>
                <hr />
                <p>
                    <?php
                    printf(
                        /* translators: 1: PHP version, 2: WordPress version */
                        esc_html__( 'Your PHP version is %1$s. Your WordPress version is %2$s.', 'easy-zillow-reviews' ),
                        esc_html( PHP_VERSION ),
                        esc_html( get_bloginfo( 'version' ) )
                    );
                    ?>
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

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text"><?php esc_html_e( 'Bridge Server Token', 'easy-zillow-reviews' ); ?></label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_professional_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" />

                <p><?php
                    printf(
                        /* translators: 1: "API Access", 2: "Show Token", 3: "Server Token" (labels in the Bridge dashboard) */
                        esc_html__( 'In your Bridge dashboard, open %1$s and click %2$s next to %3$s. No Bridge account yet? Follow the steps above.', 'easy-zillow-reviews' ),
                        '<strong>API Access</strong>',
                        '<strong>Show Token</strong>',
                        '<strong>Server Token</strong>'
                    );
                ?></p>
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

                <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text"><?php esc_html_e( 'Zillow Screenname', 'easy-zillow-reviews' ); ?></label>
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_professional_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" />

                <p><?php esc_html_e( 'The screenname of the user whose reviews you want to display. You can also paste their Zillow profile address. Use the shortcode options to show reviews for multiple profiles.', 'easy-zillow-reviews' ); ?></p>

                <?php
                Easy_Zillow_Reviews_Admin::test_connection_button(
                    'professional',
                    array(
                        'token'      => 'ezrwp_bridge_token_1',
                        'screenname' => 'ezrwp_screenname',
                    )
                );
                ?>

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
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" min="1" max="10" required />

                <p>The amount of reviews you want to show. Choose a number from 1 to 10.</p>
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
                    <?php
                    $ezrwp_layout_labels = array( 'list' => __( 'List', 'easy-zillow-reviews' ), 'grid' => __( 'Grid', 'easy-zillow-reviews' ), 'carousel' => __( 'Slider', 'easy-zillow-reviews' ) );
                    foreach( apply_filters( 'ezrwp_available_layouts', array( 'list', 'grid' ) ) as $ezrwp_layout_option ){
                        printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $ezrwp_layout_option ), selected( isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '', $ezrwp_layout_option, false ), esc_html( isset( $ezrwp_layout_labels[ $ezrwp_layout_option ] ) ? $ezrwp_layout_labels[ $ezrwp_layout_option ] : ucfirst( $ezrwp_layout_option ) ) );
                    }
                    ?>
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
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" min="2" max="6" required />

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
                    <?php esc_html_e( 'On', 'easy-zillow-reviews' ); ?>
                    </option>
                    <option value="1" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '1', false ) ) : ( '' ); ?>>
                    <?php esc_html_e( 'Off', 'easy-zillow-reviews' ); ?>
                    </option>
                </select>

                <div id="disclaimer-warning">
                    <p>Recommended: Consult the <a href="https://bridgedataoutput.com/zillowterms" target="_blank">Zillow Data Terms of Use</a> <span class="dashicons dashicons-external" style="font-size: 14px;"></span> before you hide the Zillow Disclaimer.
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
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The time elapsed since the review. Example: "6 days ago".
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
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> A short description of the reviewer. Example: "Listed and sold a home in Ellicott City, MD 21043".
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
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The overall profile star rating.
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
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The link to your Zillow profile.
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
                <input name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> Recommended: Consult the <a href="https://www.zillow.com/howto/api/BrandingRequirements.htm" target="_blank">Zillow Branding Requirements</a> <span class="dashicons dashicons-external" style="font-size: 14px;"></span> before you hide the Zillow logo.
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
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" min="1" />

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
                <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" min="1" />

                <p>The font size for the reviewer description text in pixels. Default is 16px.</p>

                <?php
            }
            function ezrwp_section_for_premium_callout_cb( $args ) {
                ?>
                <p id="<?php echo esc_attr( $args['id'] ); ?>-0">The Premium License unlocks the following features:</p>
                <ul id="<?php echo esc_attr( $args['id'] ); ?>-1">
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Team Reviews: show your team members' reviews alongside your own</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Slider Layout</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Lender Reviews</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Individual Loan Officer Reviews</li>
                    <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Company Profile Reviews</li>
                </ul>
                <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
                    <a href="<?php echo esc_url( ezrwp_fs()->get_upgrade_url() ); ?>" class="button">Upgrade to Premium</a>
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
        public function enqueue_styles( $hook_suffix = '' ) {

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

            if( $hook_suffix !== 'toplevel_page_' . $this->plugin_slug ){
                return;
            }

            wp_enqueue_style( $this->plugin_slug . '-admin', plugin_dir_url( __FILE__ ) . 'css/easy-zillow-reviews-admin.css', array(), $this->version, 'all' );

        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.1.0
         */
        public function enqueue_scripts( $hook_suffix = '' ) {

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

            if( $hook_suffix !== 'toplevel_page_' . $this->plugin_slug ){
                return;
            }

            wp_enqueue_script( $this->plugin_slug . '-admin', plugin_dir_url( __FILE__ ) . 'js/easy-zillow-reviews-admin.js', array( 'jquery' ), $this->version, true );
        }
    }
}