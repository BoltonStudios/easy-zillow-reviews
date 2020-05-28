<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * 
 * Special thanks to Brad Vincent <bradvin@gmail.comm> and contributors. 
 * Selections of the following code were derived from the code from FooGallery, which is licensed GPLv2.
 * Used with permission.
 *
 * @link              https://www.boltonstudios.com
 * @since             1.1.0
 * @package           Easy_Zillow_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Zillow Reviews
 * Plugin URI:        https://wordpress.org/plugins/easy-zillow-reviews/
 * Description:       Display reviews from Zillow on your site.
 * Version:           1.2.1-beta.1
 * Author:            Aaron Bolton
 * Author URI:        https://www.boltonstudios.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy-zillow-reviews
 * Domain Path:       /languages
 *
 * @fs_premium_only /premium/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( function_exists( 'ezrwp_fs' ) ) {

    ezrwp_fs()->set_basename( true, __FILE__ );
} else {

	if ( ! class_exists( 'Easy_Zillow_Reviews' ) ) {

        /**
         * Current plugin version.
         * Start at version 1.0.0 and use SemVer - https://semver.org
         * Rename this for your plugin and update it as you release new versions.
         */
        define( 'EASY_ZILLOW_REVIEWS_VERSION', '1.2.1-beta.1' );
        define( 'EASY_ZILLOW_REVIEWS_BASENAME', plugin_basename( __FILE__ ) );

        /**
         * The code that runs during plugin activation.
         * This action is documented in includes/class-easy-zillow-reviews-activator.php
         */
        function activate_easy_zillow_reviews() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-zillow-reviews-activator.php';
            Easy_Zillow_Reviews_Activator::activate();
        }

        /**
         * The code that runs during plugin deactivation.
         * This action is documented in includes/class-easy-zillow-reviews-deactivator.php
         */
        function deactivate_easy_zillow_reviews() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-zillow-reviews-deactivator.php';
            Easy_Zillow_Reviews_Deactivator::deactivate();
        }

        register_activation_hook( __FILE__, 'activate_easy_zillow_reviews' );
        register_deactivation_hook( __FILE__, 'deactivate_easy_zillow_reviews' );

        /**
         * The core plugin class that is used to define internationalization,
         * admin-specific hooks, and public-facing site hooks.
         */
        require plugin_dir_path( __FILE__ ) . 'includes/class-easy-zillow-reviews-base.php';

        // Create a helper function for easy SDK access.
        function ezrwp_fs()
        {

            global $ezrwp_fs;

            if (!isset($ezrwp_fs)) {

                // Include Freemius SDK.
                require_once dirname(__FILE__) . '/freemius/start.php';

                $ezrwp_fs = fs_dynamic_init(array(
                    'id'                  => '4652',
                    'slug'                => 'easy-zillow-reviews',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_5fa54ca20b9465c9db23a010b43f5',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Premium',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'menu'                => array(
                        'slug'           => 'easy-zillow-reviews'
                    ),
                ));
            }

            return $ezrwp_fs;
        }

        // Init Freemius.
        ezrwp_fs();

        // Signal that SDK was initiated.
        do_action('ezrwp_fs_loaded');

        /**
         * Easy_Zillow_Reviews Class
         *
         * @since      1.1.6
         * @package    Easy_Zillow_Reviews
         * @subpackage Easy_Zillow_Reviews/includes
         * @author     Aaron Bolton <aaron@boltonstudios.com>
         */
        class Easy_Zillow_Reviews extends Easy_Zillow_Reviews_Base{
            
            /**
             * Initialize the plugin by setting localization, filters, and administration functions.
             */
            public function __construct() {
                    
                // Load the essential plugin features including settings and admin
                $plugin = new Easy_Zillow_Reviews_Base( EASY_ZILLOW_REVIEWS_BASENAME );
                
                /*
                 * Run the upgrader to migrate values from old option name
                 * to new option name(s) introduced in v1.1.0
                 */
                new Easy_Zillow_Reviews_Upgrader();

                // Get the Zillow Professional data
                $zillow_professional_reviews = new Easy_Zillow_Reviews_Professional();
                
                // Define plugin shortcodes
                new Easy_Zillow_Reviews_Professional_Shortcodes( $zillow_professional_reviews );

                // Define plugin widget
                new Easy_Zillow_Reviews_Professional_Widget_Init( $zillow_professional_reviews );

                // Define Gutenberg block
                $gutenberg = new Easy_Zillow_Reviews_Gutenberg();
                $gutenberg->set_zillow_data( $zillow_professional_reviews );

                // Add Zillow Professional reviews to Gutenberg block
                $plugin->loader->add_filter( 'ezrwp_render_block', $zillow_professional_reviews, 'update_reviews_in_block', 10, 2 );
                
                // This IF block will be auto removed from the Free version.
                if ( ezrwp_fs()->is__premium_only() ) {

                    // This IF will be executed only if the user in a trial mode or have a valid license.
                    if ( ezrwp_fs()->can_use_premium_code() ) {

                        // ... premium only logic ...
                        require_once plugin_dir_path( __FILE__ ) . 'premium/class-easy-zillow-reviews-premium.php';

                        // Load Premium Features and pass the plugin object to be modified
                        new Easy_Zillow_Reviews_Premium( $plugin );

                        // Get the Zillow Professional data
                        $zillow_lender_reviews = new Easy_Zillow_Reviews_Lender();

                        // Define Premium shortcodes
                        new Easy_Zillow_Reviews_Lender_Shortcodes( $zillow_lender_reviews );
                        
                        // Define Premium widget
                        new Easy_Zillow_Reviews_Lender_Widget_Init( $zillow_lender_reviews );

                        // Add Premium options to Gutenberg block
                        $plugin->loader->add_filter( 'ezrwp_block_options', $zillow_lender_reviews, 'update_options_in_block', 10, 1 );
                        
                        // Add Zillow Lender reviews to Gutenberg block
                        $plugin->loader->add_filter( 'ezrwp_render_block', $zillow_lender_reviews, 'update_reviews_in_block', 10, 2 );
                    }
                }
                $plugin->run();
            }
        }
    }
    // Run Plugin
    new Easy_Zillow_Reviews();
}