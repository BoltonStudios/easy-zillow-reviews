<?php
/*
* Easy Zillow Reviews Premium Shortcodes
*/
if(!class_exists('EasyZillowReviewsPremiumShortcodes')){
    
    class EasyZillowReviewsPremiumShortcodes{
    
        function __construct(){
            
            add_action('plugins_loaded', array($this, 'init_shortcodes'));
        }
        function init_shortcodes($atts){
            
            add_shortcode('ez-zillow-lender-reviews', array($this, 'display_lender_reviews'));
        }
        function display_lender_reviews($atts){
    
            // Shortcode defaults
            $options = $GLOBALS['ezrwp_options'];
            $layout = isset($options['ezrwp_layout']) ? $options['ezrwp_layout'] : 'list';
            $cols = isset($options['ezrwp_cols']) ? $options['ezrwp_cols'] : 3;
            $count = isset($options['ezrwp_count']) ? $options['ezrwp_count'] : 3;
            $zmpid = isset($options['ezrwp_zmpid']) ? $options['ezrwp_zmpid'] : 0;
            $nmlsid = isset($options['ezrwp_nmlsid']) ? $options['ezrwp_nmlsid'] : 0;
            $companyName = isset($options['ezrwp_company_name']) ? $options['ezrwp_company_name'] : '';

            // Shortcode attributes
            if( isset( $atts ) ){
                $atts = shortcode_atts( array(
                    // Defaults
                    'layout' => 'list',
                    'columns' => 0,
                    'count' => $count
                ), $atts );
                $layout = $atts[ 'layout' ];
                $cols = $atts[ 'columns' ];
                $count = $atts[ 'count' ];
            }

            // Review count cannot exceed the limit of 10.
            $count = ($count > 10 ) ? 10 : $count;

            // Fetch Zillow Data
            $zillowData = ezrwpFetchLenderDataFromZillow(
                $zmpid,
                $nmlsid,
                $companyName,
                $count
            );

            // Render output
            if( $zillowData -> hasReviews ){
                $output = $zillowData -> getLenderReviews($layout, $cols);
            } else {
                $output = '<p>Unable to load reviews. Zillow says: <strong>'. $zillowData -> message .'</strong>.</p>';
            }
            return $output;
        }
            
            // Fetch Professionals data from Zillow
            private function fetch_zillow_data($zwsid, $screenname, $count){

                // Number of reviews to display.
                $count = floor($count) > 0 ? floor($count) : '';

                // Contstruct the URL for a Zillow Professional.
                $url = 'https://www.zillow.com/webservice/ProReviews.htm?zws-id='. $zwsid .'&screenname='. $screenname .'&count='. $count;

                // Fetch data from Zillow.
                $xml = simplexml_load_file($url) or die("Error: Cannot create object");

                // Pass data to EasyZillowReviewsData object.
                $this->reviews->professional = $this;
                $this->reviews->message = $xml->message->text;
                $this->reviews->hasReviews = ( $xml->message->code > 0 ) ? false : true;
                if($this->reviews->hasReviews){

                    // Success
                    $this->info = $xml->response->result->proInfo;
                    $this->url = $xml->response->result->proInfo->profileURL;
                    $this->reviews->reviews = $xml->response->result->proReviews;
                    $this->reviewCount = $xml->response->result->proInfo->reviewCount;
                }

                // Return EasyZillowReviewsData object.
                //return $zillowData;
            }
    }
}