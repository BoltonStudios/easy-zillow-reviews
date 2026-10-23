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


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
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

            // Carousel layout.
            add_filter( 'ezrwp_available_layouts', array( $this, 'add_carousel_layout' ) );
            add_filter( 'ezrwp_reviews_output', array( $this, 'carousel_markup' ), 10, 5 );
            add_action( 'init', array( $this, 'register_assets' ) );
            add_action( 'enqueue_block_assets', array( $this, 'enqueue_editor_assets' ) );

            // Team reviews.
            add_filter( 'ezrwp_bridge_reviews_filter', array( $this, 'team_reviews_filter' ), 10, 3 );
            add_filter( 'ezrwp_bridge_reviews', array( $this, 'attribute_team_reviews' ), 10, 5 );

            // Settings.
            add_filter( 'ezrwp_sanitize_ezrwp_lender_reviews_options', array( $this, 'sanitize_lender_options' ) );
            add_filter( 'ezrwp_sanitize_professional_reviews_extra', array( $this, 'sanitize_professional_extra' ), 10, 2 );
            add_filter( 'ezrwp_sanitize_general_extra', array( $this, 'sanitize_general_extra' ), 10, 2 );
            add_filter( 'ezrwp_test_connection', array( $this, 'test_lender_connection' ), 10, 3 );
            add_filter( 'ezrwp_is_configured', array( $this, 'is_configured' ) );
            add_filter( 'ezrwp_block_editor_settings', array( $this, 'block_editor_settings' ) );
        }

        /**
         * Enable Premium options in the block editor.
         *
         * @since    1.7.0
         * @param    array    $settings    The block editor settings.
         * @return   array
         */
        public function block_editor_settings( $settings ){

            $settings['isPremium'] = true;

            return $settings;
        }

        /**
         * Add the carousel to the available layouts.
         *
         * @since    1.7.0
         * @param    string[]    $layouts    Layout slugs.
         * @return   string[]
         */
        public function add_carousel_layout( $layouts ){

            if( ! in_array( 'carousel', $layouts, true ) ){
                $layouts[] = 'carousel';
            }

            return $layouts;
        }

        /**
         * Register the carousel assets.
         *
         * @since    1.7.0
         */
        public function register_assets(){

            $version = defined( 'EASY_ZILLOW_REVIEWS_VERSION' ) ? EASY_ZILLOW_REVIEWS_VERSION : false;

            wp_register_style( 'easy-zillow-reviews-carousel', plugin_dir_url( __FILE__ ) . 'css/easy-zillow-reviews-carousel.css', array( 'easy-zillow-reviews' ), $version );
            wp_register_script(
                'easy-zillow-reviews-carousel',
                plugin_dir_url( __FILE__ ) . 'js/easy-zillow-reviews-carousel.js',
                array(),
                $version,
                array(
                    'in_footer' => true,
                    'strategy'  => 'defer',
                )
            );
        }

        /**
         * Style carousel previews in the block editor.
         *
         * @since    1.7.0
         */
        public function enqueue_editor_assets(){

            if( is_admin() ){
                wp_enqueue_style( 'easy-zillow-reviews-carousel' );
            }
        }

        /**
         * Display reviews in a carousel.
         *
         * @since    1.7.0
         * @param    string    $html            The reviews HTML.
         * @param    string    $layout          The layout slug.
         * @param    int       $columns         The number of reviews visible at once.
         * @param    string    $wrapper_id      The unique ID of this set of reviews.
         * @param    string    $reviews_html    The reviews without a wrapper.
         * @return   string
         */
        public function carousel_markup( $html, $layout, $columns, $wrapper_id, $reviews_html = '' ){

            if( $layout !== 'carousel' ){
                return $html;
            }

            wp_enqueue_style( 'easy-zillow-reviews-carousel' );
            wp_enqueue_script( 'easy-zillow-reviews-carousel' );

            $general = get_option( 'ezrwp_general_options' );
            $autoplay = is_array( $general ) && isset( $general['ezrwp_carousel_autoplay'] ) ? absint( $general['ezrwp_carousel_autoplay'] ) : 0;
            $columns = min( max( absint( $columns ), 1 ), 6 );
            $track_id = 'ezrwp-carousel-' . sanitize_html_class( $wrapper_id );

            $pause = $autoplay > 0
                ? '<button type="button" class="ezrwp-carousel-pause" aria-pressed="false" data-label-pause="' . esc_attr__( 'Pause', 'easy-zillow-reviews' ) . '" data-label-play="' . esc_attr__( 'Play', 'easy-zillow-reviews' ) . '">' . esc_html__( 'Pause', 'easy-zillow-reviews' ) . '</button>'
                : '';

            return sprintf(
                '<div class="ezrwp-carousel-container" data-autoplay="%1$d" style="--ezrwp-carousel-columns: %2$d">
                    <div class="ezrwp-reviews ezrwp-carousel-track" id="%3$s" tabindex="0" role="region" aria-roledescription="%4$s" aria-label="%5$s">%6$s</div>
                    <div class="ezrwp-carousel-controls" hidden>
                        <button type="button" class="ezrwp-carousel-prev" aria-controls="%3$s" aria-label="%7$s"><span aria-hidden="true">&lsaquo;</span></button>
                        <span class="ezrwp-carousel-status"></span>
                        %8$s
                        <button type="button" class="ezrwp-carousel-next" aria-controls="%3$s" aria-label="%9$s"><span aria-hidden="true">&rsaquo;</span></button>
                    </div>
                </div>',
                $autoplay,
                $columns,
                esc_attr( $track_id ),
                esc_attr__( 'carousel', 'easy-zillow-reviews' ),
                esc_attr__( 'Reviews on Zillow', 'easy-zillow-reviews' ),
                $reviews_html,
                esc_attr__( 'Previous reviews', 'easy-zillow-reviews' ),
                $pause,
                esc_attr__( 'Next reviews', 'easy-zillow-reviews' )
            );
        }

        /**
         * Whether to include team members' reviews.
         *
         * @since    1.7.0
         * @param    array    $args    Request options, e.g. shortcode attributes.
         * @return   bool
         */
        private function include_team_reviews( $args ){

            if( is_array( $args ) && isset( $args['team'] ) ){
                return in_array( strtolower( trim( (string) $args['team'] ) ), array( '1', 'true', 'yes', 'on' ), true );
            }

            $options = get_option( 'ezrwp_professional_reviews_options' );

            return is_array( $options ) && ! empty( $options['ezrwp_include_team'] );
        }

        /**
         * Request a team lead's reviews together with their team members' reviews.
         *
         * @since    1.7.0
         * @param    string    $filter     The OData filter.
         * @param    object    $account    The reviewee account.
         * @param    array     $args       Request options.
         * @return   string
         */
        public function team_reviews_filter( $filter, $account, $args ){

            if( ! $this->include_team_reviews( $args ) ){
                return $filter;
            }

            $id = preg_replace( '/[^A-Za-z0-9]/', '', (string) $account->AccountIdReviewee );

            return "AccountIdReviewee eq '" . $id . "' or TeamLeadAccountId eq '" . $id . "'";
        }

        /**
         * Show which team member each review is about, linked to their Zillow profile.
         *
         * @since    1.7.0
         * @param    Easy_Zillow_Reviews_Review[]        $reviews    The reviews.
         * @param    object[]                            $items      The review records.
         * @param    object                              $account    The team lead's account.
         * @param    array                               $headers    Request headers.
         * @param    Easy_Zillow_Reviews_Professional    $data       The reviews data object.
         * @return   Easy_Zillow_Reviews_Review[]
         */
        public function attribute_team_reviews( $reviews, $items, $account, $headers, $data ){

            if( ! $this->include_team_reviews( $data->get_request_args() ) ){
                return $reviews;
            }

            $people = array(
                (string) $account->AccountIdReviewee => array(
                    'name' => isset( $account->RevieweeFullName ) ? $account->RevieweeFullName : '',
                    'url'  => isset( $account->RevieweeProfileURL ) ? $account->RevieweeProfileURL : '',
                ),
            );

            $member_ids = array();
            foreach( $items as $item ){
                $id = preg_replace( '/[^A-Za-z0-9]/', '', (string) $item->AccountIdReviewee );
                if( $id !== '' && ! isset( $people[ $id ] ) ){
                    $member_ids[ $id ] = "AccountIdReviewee eq '" . $id . "'";
                }
            }

            // Only attribute reviews when the team actually has other members' reviews.
            if( empty( $member_ids ) ){
                return $reviews;
            }

            $result = $data->remote_get_json(
                add_query_arg(
                    array(
                        '$filter' => rawurlencode( implode( ' or ', $member_ids ) ),
                        '$select' => 'AccountIdReviewee,RevieweeFullName,RevieweeProfileURL',
                    ),
                    'https://api.bridgedataoutput.com/api/v2/OData/reviews/Reviewee'
                ),
                $headers
            );

            if( $result['code'] === 200 && isset( $result['data']->value ) && is_array( $result['data']->value ) ){
                foreach( $result['data']->value as $person ){
                    $people[ (string) $person->AccountIdReviewee ] = array(
                        'name' => isset( $person->RevieweeFullName ) ? $person->RevieweeFullName : '',
                        'url'  => isset( $person->RevieweeProfileURL ) ? $person->RevieweeProfileURL : '',
                    );
                }
            }

            foreach( $items as $index => $item ){
                $id = (string) $item->AccountIdReviewee;
                if( isset( $reviews[ $index ], $people[ $id ] ) && $people[ $id ]['name'] !== '' && $people[ $id ]['url'] !== '' ){
                    $reviews[ $index ]->set_reviewee( $people[ $id ]['name'], $people[ $id ]['url'] );
                }
            }

            return $reviews;
        }

        /**
         * Sanitize the lender options before saving.
         *
         * @since    1.7.0
         * @param    array    $input    The submitted options.
         * @return   array
         */
        public function sanitize_lender_options( $input ){

            return array(
                'ezrwp_zmpid'        => isset( $input['ezrwp_zmpid'] ) ? preg_replace( '/[^A-Za-z0-9_\-]/', '', $input['ezrwp_zmpid'] ) : '',
                'ezrwp_nmlsid'       => isset( $input['ezrwp_nmlsid'] ) ? preg_replace( '/[^0-9]/', '', $input['ezrwp_nmlsid'] ) : '',
                'ezrwp_company_name' => isset( $input['ezrwp_company_name'] ) ? sanitize_text_field( $input['ezrwp_company_name'] ) : '',
            );
        }

        /**
         * Save the team reviews option.
         *
         * @since    1.7.0
         * @param    array    $output    The sanitized options.
         * @param    array    $input     The submitted options.
         * @return   array
         */
        public function sanitize_professional_extra( $output, $input ){

            $output['ezrwp_include_team'] = empty( $input['ezrwp_include_team'] ) ? 0 : 1;

            return $output;
        }

        /**
         * Save the carousel autoplay option.
         *
         * @since    1.7.0
         * @param    array    $output    The sanitized options.
         * @param    array    $input     The submitted options.
         * @return   array
         */
        public function sanitize_general_extra( $output, $input ){

            $output['ezrwp_carousel_autoplay'] = isset( $input['ezrwp_carousel_autoplay'] ) ? min( absint( $input['ezrwp_carousel_autoplay'] ), 60 ) : 0;

            return $output;
        }

        /**
         * Test the lender settings entered on the settings page.
         *
         * @since    1.7.0
         * @param    array|null    $result    The result from other handlers.
         * @param    string        $type      The connection type.
         * @param    array         $fields    The values entered on the settings page.
         * @return   array|null
         */
        public function test_lender_connection( $result, $type, $fields ){

            if( $type !== 'lender' ){
                return $result;
            }

            if( empty( $fields['zmpid'] ) || empty( $fields['nmlsid'] ) ){
                return array( 'success' => false, 'message' => __( 'Enter your Zillow Mortgages Partner ID and NMLS# first.', 'easy-zillow-reviews' ) );
            }

            $lender = new Easy_Zillow_Reviews_Lender();
            $lender->set_zmpid( $fields['zmpid'] );
            $lender->set_nmlsid( $fields['nmlsid'] );
            $lender->set_company_name( isset( $fields['company'] ) ? $fields['company'] : '' );
            $lender->fetch_reviews_from_zillow( 1 );

            if( $lender->get_has_reviews() ){
                return array(
                    'success' => true,
                    'message' => sprintf(
                        /* translators: %s: number of reviews */
                        _n( 'Connected! Found %s review on Zillow. Remember to save your settings.', 'Connected! Found %s reviews on Zillow. Remember to save your settings.', absint( $lender->get_review_count() ), 'easy-zillow-reviews' ),
                        number_format_i18n( absint( $lender->get_review_count() ) )
                    ),
                );
            }

            $message = (string) $lender->get_message();

            if( $message === 'InvalidPartnerId' ){
                $message = __( 'Zillow did not recognize this Partner ID. Check that you copied it from your Zillow Mortgages API account.', 'easy-zillow-reviews' );
            }

            return array( 'success' => false, 'message' => $message );
        }

        /**
         * Treat the plugin as configured when lender reviews are set up.
         *
         * @since    1.7.0
         * @param    bool    $configured    Whether reviews can be displayed.
         * @return   bool
         */
        public function is_configured( $configured ){

            $options = get_option( 'ezrwp_lender_reviews_options' );

            return $configured || ( is_array( $options ) && ! empty( $options['ezrwp_zmpid'] ) && ! empty( $options['ezrwp_nmlsid'] ) );
        }

        /**
         * Print the team reviews setting.
         *
         * @since    1.7.0
         * @param    array    $args    The field arguments.
         */
        public function include_team_field_cb( $args ){

            $options = get_option( 'ezrwp_professional_reviews_options' );
            $setting = is_array( $options ) && ! empty( $options['ezrwp_include_team'] );
            ?>
            <label>
                <input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="ezrwp_professional_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="1" <?php checked( $setting ); ?> />
                <?php esc_html_e( 'Include reviews of my team members (for team lead profiles).', 'easy-zillow-reviews' ); ?>
            </label>
            <p class="description"><?php esc_html_e( 'Each review shows and links to the team member it is about. Shortcode override: team="true" or team="false".', 'easy-zillow-reviews' ); ?></p>
            <?php
        }

        /**
         * Print the carousel autoplay setting.
         *
         * @since    1.7.0
         * @param    array    $args    The field arguments.
         */
        public function carousel_autoplay_field_cb( $args ){

            $options = get_option( 'ezrwp_general_options' );
            $setting = is_array( $options ) && isset( $options['ezrwp_carousel_autoplay'] ) ? absint( $options['ezrwp_carousel_autoplay'] ) : 0;
            ?>
            <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="small-text" name="ezrwp_general_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" min="0" max="60" />
            <?php esc_html_e( 'seconds', 'easy-zillow-reviews' ); ?>
            <p class="description"><?php esc_html_e( 'For the slider layout: advance automatically every few seconds. 0 turns autoplay off. Visitors can pause it, and it never moves for visitors who prefer reduced motion.', 'easy-zillow-reviews' ); ?></p>
            <?php
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
                
                ?>
                <hr />

                <p id="<?php echo esc_attr( $id['id'] ); ?>-2">
                    <strong style="font-size: 14px">Shortcode</strong><br/>[ez-zillow-lender-reviews]
                </p>
                <p id="<?php echo esc_attr( $id['id'] ); ?>-3">
                    Example shortcode with overrides:<br />[ez-zillow-lender-reviews columns="2" count="4" excerpt="30" layout="grid"]
                </p>
                <?php
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
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_lender_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" />

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
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_lender_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" />

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
                <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_lender_reviews_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $setting ); ?>" />

                <p><strong>For institutional lenders only.</strong> Leave blank for individual loan officers. The Company Name of the lender whose reviews you want to display, exactly as it appears on Zillow. Example "Great  Loans, LLC".</p>

                <?php
                Easy_Zillow_Reviews_Admin::test_connection_button(
                    'lender',
                    array(
                        'zmpid'   => 'ezrwp_zmpid',
                        'nmlsid'  => 'ezrwp_nmlsid',
                        'company' => 'ezrwp_company_name',
                    )
                );
                ?>

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
                'title' => __( 'Upgrade to Easy Zillow Reviews Premium', 'easy-zillow-reviews' ), // title
                'page'  => 'ezrwp_lender_reviews' // page
            );*/

            $premium_settings_sections[] = array(
                'id'    => 'ezrwp_section_for_zillow_lender_parameters', // id
                'title' => __( 'Zillow Lender Profile', 'easy-zillow-reviews' ), // title
                'page'  => 'ezrwp_lender_reviews' // page
            );
            $settings->set_sections($premium_settings_sections);

            // Override settings fields
            $premium_settings_fields = $settings->get_settings();
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_zmpid', // id. Used only internally
                'title'     => __( 'Zillow Mortgages Partner ID', 'easy-zillow-reviews' ), // title
                'callback'  => 'ezrwp_zmpid_text_field_cb', // callback
                'tab'       => 'ezrwp_lender_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_lender_parameters'
            );
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_nmlsid', // id. Used only internally
                'title'     => __( 'NMLS#', 'easy-zillow-reviews' ), // title
                'callback'  => 'ezrwp_nmlsid_text_field_cb', // callback
                'tab'       => 'ezrwp_lender_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_lender_parameters'
            );
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_company_name', // id. Used only internally
                'title'     => __( 'Company Name', 'easy-zillow-reviews' ), // title
                'callback'  => 'ezrwp_company_name_text_field_cb', // callback
                'tab'       => 'ezrwp_lender_reviews', // page
                'section'   => 'ezrwp_section_for_zillow_lender_parameters'
            );
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_include_team',
                'title'     => __( 'Team Reviews', 'easy-zillow-reviews' ),
                'callback'  => array( $this, 'include_team_field_cb' ),
                'tab'       => 'ezrwp_professional_reviews',
                'section'   => 'ezrwp_section_for_zillow_professional_parameters'
            );
            $premium_settings_fields[] = array(
                'id'        => 'ezrwp_carousel_autoplay',
                'title'     => __( 'Slider Autoplay', 'easy-zillow-reviews' ),
                'callback'  => array( $this, 'carousel_autoplay_field_cb' ),
                'tab'       => 'ezrwp_general',
                'section'   => 'ezrwp_section_for_defaults'
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