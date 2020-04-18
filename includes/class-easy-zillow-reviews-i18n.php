<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.boltonstudios.com
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_i18n' ) ) {

    class Easy_Zillow_Reviews_i18n {

        /**
         * Load the plugin text domain for translation.
         *
         * @since    1.0.0
         */
        public function load_plugin_textdomain() {

            load_plugin_textdomain(
                'easy-zillow-reviews',
                false,
                dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
            );

        }
    }
}
