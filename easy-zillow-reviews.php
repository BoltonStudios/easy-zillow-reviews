<?php
/*
Plugin Name: Easy Zillow Reviews
Description: Display reviews from Zillow on your site.
Version:     1.0.3
Author:      Aaron Bolton
Author URI:  https://www.boltonstudios.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: easy-zillow-reviews
Domain Path: /languages

Zillow Reviews is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Zillow Reviews is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Zillow Reviews. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*
*
* @fs_premium_only /premium/
*/

// Block direct access to plugin PHP files
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Freemius
if ( function_exists( 'ezrwp_fs' ) ) {
    
    ezrwp_fs()->set_basename( true, __FILE__ );
} else {
    
    if(!class_exists('EasyZillowReviews_Plugin')){
        
        define( 'EASYZILLOWREVIEWS_PATH', plugin_dir_path( __FILE__ ) );
        define( 'EASYZILLOWREVIEWS_URL', plugin_dir_url( __FILE__ ) );
        require_once EASYZILLOWREVIEWS_PATH . 'includes/constants.php';
        
        if ( ! function_exists( 'ezrwp_fs' ) ) {

            // Create a helper function for easy SDK access.
            function ezrwp_fs() {

                global $ezrwp_fs;

                if ( ! isset( $ezrwp_fs ) ) {

                    // Include Freemius SDK.
                    require_once dirname(__FILE__) . '/freemius/start.php';

                    $ezrwp_fs = fs_dynamic_init( array(
                        'id'                  => '4652',
                        'slug'                => 'easy-zillow-reviews',
                        'type'                => 'plugin',
                        'public_key'          => 'pk_5fa54ca20b9465c9db23a010b43f5',
                        'is_premium'          => true,
                        'premium_suffix'      => 'Premium',
                        // If your plugin is a serviceware, set this option to false.
                        'has_premium_version' => true,
                        'has_addons'          => false,
                        'has_paid_plans'      => true,
                        'menu'                => array(
                            'slug'           => 'easy-zillow-reviews',
                            'parent'         => array(
                                'slug' => 'options-general.php',
                            ),
                        ),
                        // Set the SDK to work in a sandbox mode (for development & testing).
                        // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                        'secret_key'          => 'sk_QG)ED88s[r1)5SF.NP8IvM@XjoD7S',
                    ) );
                }

                return $ezrwp_fs;
            }

            // Init Freemius.
            ezrwp_fs();

            // Signal that SDK was initiated.
            do_action( 'ezrwp_fs_loaded' );
        }

        /**
         * EasyZillowReviews_Plugin
         *
         * @package   EasyZillowReviews
         * @author    Aaron Bolton <aaron@boltonstudios.com>
         * @license   GPL-2.0+
         * @link      https://www.boltonstudios.com
         * @copyright 2019 Aaron Bolton
         */
        class EasyZillowReviews_Plugin
        {
            private static  $instance ;
            public static function get_instance()
            {
                if ( !isset( self::$instance ) && !self::$instance instanceof EasyZillowReviews_Plugin ) {
                    self::$instance = new EasyZillowReviews_Plugin();
                }
                return self::$instance;
            }
            
            /**
             * Initialize the plugin by setting localization, filters, and administration functions.
             */
            private function __construct()
            {

                // Includes
                require_once EASYZILLOWREVIEWS_PATH . 'includes/constants.php';
                require_once EASYZILLOWREVIEWS_PATH . 'includes/includes.php';
                
                // Admin
                if ( is_admin() ) {

                    // we are in admin mode
                    require_once( dirname( __FILE__ ) . '/admin/easy-zillow-reviews-admin.php' );

                    // Add action links to Plugins page
                    function ezrp_add_action_links( $actions, $plugin_file ){
                        static $plugin;

                        if (!isset($plugin))
                        $plugin = plugin_basename(__FILE__);
                        if ($plugin == $plugin_file) {

                            $settings = array('settings' => '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=easy-zillow-reviews') ) .'">' . __('Settings', 'General') . '</a>');

                            $actions = array_merge($settings, $actions);
                        }

                        return $actions;
                    }
                    add_filter( 'plugin_action_links', 'ezrp_add_action_links', 10, 5 );
                }
            
                // Public Scripts
                function enqueue_ezrwp_styles(){
                    wp_enqueue_style( 'ezrwp', EASYZILLOWREVIEWS_URL . 'public/css/ezrwp-styles.css', null, false, 'all' );
                }
                if ( !is_admin() ) {
                    add_action('wp_enqueue_scripts', 'enqueue_ezrwp_styles');
                }
                
                // Free Version
                new EasyZillowReviewsProfessionalShortcodes();
                
                //new EasyZillowReviewsWidget();
                
                if(ezrwp_fs()->is__premium_only()){
                    
                    // Premium Version
                    if ( ezrwp_fs()->is_plan( 'premium', true ) ) {
                        
                        require_once EASYZILLOWREVIEWS_PATH . 'premium/easy-zillow-reviews-premium.php';
                        new EasyZillowReviews_Premium();
                    }
                }
            }

            // Add Inline Styles to Footer
            function ezrwp_add_inline_styles(){

                // User Options
                $options = $GLOBALS['ezrwp_options'];
                $quote_font_size = $options['ezrwp_quote_font_size'];
                $reviewer_description_font_size = $options['ezrwp_reviewer_description_font_size'];
                $quote_styles = '';

                // Styles
                if( $quote_font_size != null && $quote_font_size != '' ){
                    $quote_styles = '
                    /* Review Quote Font Size */
                    body .ezrwp-wrapper .ezrwp-content blockquote,
                    body .entry-content .ezrwp-wrapper .ezrwp-content blockquote{
                        font-size: '.$quote_font_size.'px;
                    }
                    ';
                }
                if( $reviewer_description_font_size != null && $reviewer_description_font_size != '' ){
                    $reviewer_description_font_size = '
                    /* Reviewer Description Font Size */
                    body .ezrwp-wrapper .ezrwp-content .ezrwp-reviewer p,
                    body .ezrwp-wrapper .ezrwp-content .ezrwp-reviewer *{
                        font-size: '. $reviewer_description_font_size .'px;
                    }
                    ';
                }
                $before_inline_styles = '<!-- Easy Zillow Reviews Inline Styles --><style>';
                $after_inline_styles = '</style>';
                $inline_styles = $before_inline_styles;
                $inline_styles .= $quote_styles;
                $inline_styles .= $reviewer_description_font_size;
                $inline_styles .= $after_inline_styles;

                echo $inline_styles;

            }
            //add_action('wp_footer', 'ezrwpAddInlineStyles');
            

            /*
            if(ezrwp_fs()->is__premium_only()){
            /*
            * Premium Version
            *

                include( 'premium/functions.php' );
                include( 'premium/class-shortcodes.php' );

                new EasyZillowReviewsShortcodes;

                //
                add_action( 'admin_enqueue_scripts', 'load_ezrwp_premium_admin_style' );

                // This "if" block will be auto removed from the free version.
                if ( ezrwp_fs()->is_plan( 'premium', true ) ) {

                    // Init premium version.
                    add_action( 'admin_init', 'ezrwp_premium_settings_init' );
                } else{

                    // Upgrade callout
                    add_action( 'admin_init', 'ezrwp_premium_upgrade_callout_init' );
                }
            } */
        }
    }
    
    EasyZillowReviews_Plugin::get_instance();
}