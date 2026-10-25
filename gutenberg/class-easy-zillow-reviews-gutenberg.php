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
    

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
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

            $output = apply_filters( 'ezrwp_render_block', null, is_array( $attributes ) ? $attributes : array() );

            if( $output === null || $output === '' ){
                return '';
            }

            $wrapper_attributes = function_exists( 'get_block_wrapper_attributes' ) ? get_block_wrapper_attributes() : '';

            return '<div ' . $wrapper_attributes . '>' . $output . '</div>';
        }

        function register_ezrwp_block(){

            $block_dir = plugin_dir_path( __FILE__ ) . 'build';

            if ( ! file_exists( $block_dir . '/block.json' ) ) {
                return;
            }

            register_block_type( $block_dir, array( 'render_callback' => array( $this, 'render_ezrwp_block' ) ) );

            wp_add_inline_script(
                generate_block_asset_handle( 'boltonstudios/easy-zillow-reviews', 'editorScript' ),
                'window.ezrwpBlockSettings = ' . wp_json_encode( $this->get_editor_settings() ) . ';',
                'before'
            );
        }

        /**
         * The defaults and options the block editor needs.
         *
         * @since    1.7.0
         * @return   array
         */
        private function get_editor_settings(){

            $data = $this->get_zillow_data();

            // The block falls back to its own defaults when settings are missing.
            return $data ? $data->get_editor_settings( 'block' ) : array();
        }

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