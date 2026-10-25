<?php

/**
 * The Easy_Zillow_Reviews_Elementor class
 *
 * Adds the Zillow Reviews widget to Elementor. Does nothing unless Elementor is active.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.8.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/elementor
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Easy_Zillow_Reviews_Elementor' ) ) {

    class Easy_Zillow_Reviews_Elementor{

        /**
         * The oldest Elementor version with the APIs the widget uses.
         *
         * @since    1.8.0
         */
        const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

        /**
         * The reviews data used for the widget's defaults and options.
         *
         * @since    1.8.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Data    $zillow_data
         */
        private $zillow_data;

        function __construct( $zillow_data ){

            $this->zillow_data = $zillow_data;

            // These actions only fire when Elementor is active.
            add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
            add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
        }

        /**
         * Whether the installed Elementor supports the widget.
         *
         * @since    1.8.0
         * @return   bool
         */
        private function is_compatible(){

            return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' );
        }

        /**
         * Register the Zillow Reviews widget.
         *
         * @since    1.8.0
         * @param    \Elementor\Widgets_Manager    $widgets_manager
         */
        public function register_widgets( $widgets_manager ){

            if( ! $this->is_compatible() ){
                return;
            }

            // Loaded here because the widget extends an Elementor class.
            require_once plugin_dir_path( __FILE__ ) . 'class-easy-zillow-reviews-elementor-widget.php';

            // Elementor creates its own widget instances, so the data is shared through the class.
            Easy_Zillow_Reviews_Elementor_Widget::set_zillow_data( $this->zillow_data );

            $widgets_manager->register( new Easy_Zillow_Reviews_Elementor_Widget() );
        }

        /**
         * Register the script that starts reviews added or changed in the Elementor editor.
         *
         * @since    1.8.0
         */
        public function register_scripts(){

            wp_register_script(
                'easy-zillow-reviews-elementor',
                plugin_dir_url( __FILE__ ) . 'js/easy-zillow-reviews-elementor.js',
                array( 'elementor-frontend' ),
                defined( 'EASY_ZILLOW_REVIEWS_VERSION' ) ? EASY_ZILLOW_REVIEWS_VERSION : false,
                true
            );
        }
    }
}
