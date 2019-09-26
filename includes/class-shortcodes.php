<?php
/*
* Easy Zillow Reviews Shortcodes
*/
if(!class_exists('EasyZillowReviewsProfessionalShortcodes')){
    
    class EasyZillowReviewsProfessionalShortcodes extends EasyZillowReviewsProfessional{
    
        function __construct(){
            
            add_action('plugins_loaded', array($this, 'init_shortcodes'));
        }
        function init_shortcodes($atts){
            
            add_shortcode('ez-zillow-reviews', array($this, 'display_reviews'));
        }
        function display_reviews($atts){
    
            // Shortcode defaults
            $output = '';
            $options = $this->options;
            var_dump($this);
            $layout = isset($options['ezrwp_layout']) ? $options['ezrwp_layout'] : 'list';
            $cols = isset($options['ezrwp_cols']) ? $options['ezrwp_cols'] : 3;
            $count = isset($options['ezrwp_count']) ? $options['ezrwp_count'] : 3;
            $zwsid = isset($options['ezrwp_zwsid']) ? $options['ezrwp_zwsid'] : '';
            $screenname = isset($options['ezrwp_screenname']) ? $options['ezrwp_screenname'] : '';

            if( isset( $atts ) ){
                $atts = shortcode_atts( array(
                    // Defaults
                    'layout' => 'list',
                    'columns' => 3,
                    'count' => $count
                ), $atts );
                $layout = $atts[ 'layout' ];
                $cols = $atts[ 'columns' ];
                $count = $atts[ 'count' ];
            }

            // Review count cannot exceed the limit of 10.
            $count = ($count > 10 ) ? 10 : $count;

            // Number of reviews to display.
            $count = floor($count) > 0 ? floor($count) : '';

            // Contstruct the URL for a Zillow Professional.
            $url = 'https://www.zillow.com/webservice/ProReviews.htm?zws-id='. $zwsid .'&screenname='. $screenname .'&count='. $count;

            // Fetch data from Zillow.
            $xml = simplexml_load_file($url) or die("Error: Cannot create object");

            // Pass data to object.
            $this->message = $xml->message->text;
            $this->has_reviews = ( $xml->message->code > 0 ) ? false : true;
            if($this->has_reviews){

                // Success
                $this->info = $xml->response->result->proInfo;
                $this->url = $xml->response->result->proInfo->profileURL;
                $this->review_count = $xml->response->result->proInfo->review_count;
                $this->reviews = $xml->response->result->proReviews;
            }

            // Render output
            if( $this -> has_reviews ){
                $output .= $this -> get_reviews($layout, $cols);
            } else {
                $output .= '<p>Unable to load reviews. Zillow says: <strong>'. $this -> message .'</strong>.</p>';
            }
            return $output;
        }
    }
}