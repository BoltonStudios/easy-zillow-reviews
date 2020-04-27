<?php

/**
 * The Easy_Zillow_Reviews_Gutenberg class
 *
 * Enables the Gutenberg blocks
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.8
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
    
if ( ! class_exists( 'Easy_Zillow_Reviews_Gutenberg' ) ) {

    class Easy_Zillow_Reviews_Gutenberg{

        /**
         *
         *
         * @since    1.2.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Data    $zillow_data    
         */
        private $zillow_data;

        function __construct(){
            
            $this->init();
        }

        function init(){
            add_action( 'init', array( $this, 'register_ezrwp_block') );
        }
        
        /**
         * Render the Easy Zillow Reviews block on the front end
         *
         * @since    1.2.0  $attributes and $content passed from corresponding properties in gutenberg/src/index.js
         */
        function render_ezrwp_block( $attributes, $content ) {
           //var_dump($this->get_zillow_data()[0]);
            $output = null;
            $output = apply_filters( 'ezrwp_render_block', $output, $attributes );
            return $output;
        }
        
        /**
         * Setup and register the Easy Zillow Reviews block with WordPress
         *
         * @since    1.2.0
         */
        function register_ezrwp_block(){

            if ( ! function_exists( 'register_block_type' ) ) {
                // Gutenberg is not active.
                return;
            }

            // Include dependencies
            $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
            
            // Register the main JS file
            wp_register_script(
                'boltonstudios/easy-zillow-reviews',
                plugins_url( 'build/index.js', __FILE__ ),
                $asset_file['dependencies'],
                $asset_file['version']
            );
            
            // Register styles affecting the block in the WordPress editor only.
            wp_register_style(
                'easy-zillow-reviews-block-editor',
                plugins_url( 'assets/css/editor.css', __FILE__ ),
                array( 'wp-edit-blocks' ),
                filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/editor.css' )
            );
         
            // Register default styles afecting the block on the front-end and in the WP editor.
            wp_register_style(
                'easy-zillow-reviews-block',
                plugins_url( 'assets/css/style.css', __FILE__ ),
                array( ),
                filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/style.css' )
            );

            // Pass Easy_Zillow_Reviews_Professional object to main JS file
            wp_localize_script(
                'boltonstudios/easy-zillow-reviews',
                'zillow_data',
                array( apply_filters( 'ezrwp_block_options', $this->get_zillow_data() ) )
            );

            // Register the block type
            register_block_type(
                'boltonstudios/easy-zillow-reviews',
                array(
                    'style' => 'easy-zillow-reviews-block',
                    'editor_style' => 'easy-zillow-reviews-block-editor',
                    'editor_script' => 'boltonstudios/easy-zillow-reviews',
                    'render_callback' => array( $this, 'render_ezrwp_block' )
                )
            );
        }
        
        /**
         * Get the value of zillow_data
         *
         * @since    1.2.0
         */
        public function get_zillow_data()
        {
                return $this->zillow_data;
        }

        /**
         * Set the value of zillow_data
         *
         * @return  self
         */ 
        public function set_zillow_data($zillow_data)
        {
                $this->zillow_data = $zillow_data;

                return $this;
        }
    }
}