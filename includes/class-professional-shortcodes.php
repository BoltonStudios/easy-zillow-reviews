<?php
/*
* Easy Zillow Reviews Professional Shortcodes
*/
if(!class_exists('EasyZillowReviewsProfessionalShortcodes')){
    
    class EasyZillowReviewsProfessionalShortcodes extends EasyZillowReviewsProfessional{
    
        function __construct(){
            
            add_action('plugins_loaded', array($this, 'init_shortcodes'));
        }
        function init_shortcodes($atts){
            
            add_shortcode('ez-zillow-reviews', array($this, 'display_professional_reviews'));
        }
        function display_professional_reviews($atts){
    
            // Get Settings and defaults
            $general_options = get_option('ezrwp_general_options');
            $professional_reviews_options = get_option('ezrwp_professional_reviews_options');
            $layout = isset($general_options['ezrwp_layout']) ? $general_options['ezrwp_layout'] : 'list';
            $grid_columns = isset($general_options['ezrwp_cols']) ? $general_options['ezrwp_cols'] : 3;
            $count = isset($general_options['ezrwp_count']) ? $general_options['ezrwp_count'] : 3;
            
            // Pass settings to object
            $this->set_general_options($general_options);
            $this->set_professional_reviews_options($professional_reviews_options);
            $this->set_layout($layout);
            $this->set_grid_columns($grid_columns);
            $this->set_count($count);

            // Get shortcode attributes
            if( isset( $atts ) ){
                $atts = shortcode_atts( array(
                    // Defaults
                    'layout' => $this->get_layout(),
                    'columns' => $this->get_grid_columns(),
                    'count' => $this->get_count()
                ), $atts );
                $layout = $atts[ 'layout' ];
                $cols = $atts[ 'columns' ];
                $count = $atts[ 'count' ];
            }

            // Review count cannot be more than 10 or less than 0.
            $count = ($count > 10 ) ? 10 : $count;
            $count = floor($count) > 0 ? floor($count) : 1;

            // Fetch reviews from Zillow
            $this->fetch_reviews_from_zillow($count);

            // Render output
            if( $this -> get_has_reviews() ){

                // Success
                $output = $this -> layout_reviews($layout, $cols);
            } else {

                // Error
                $output = '<p>Unable to load reviews. Zillow says: <strong>'. $this -> get_message() .'</strong>.</p>';
            }
            return $output;
        }
    }
}