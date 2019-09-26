<?php
/*
* Easy Zillow Reviews Professional
*/
if(!class_exists('EasyZillowReviewsProfessional')){
    
class EasyZillowReviewsProfessional{
        
        // Public vars
        private $url;
        private $info;
        private $review_count;
        private $reviews;
        private $has_reviews;
        private $message;
        private $options;
        
        // Private vars
        private $layout;
        private $grid_columns;

        // Constructor
        public function __construct($layout, $cols){

            // Defaults
            $this->options = get_option('ezrwp_options');
            $this->layout = $layout;
            $this->grid_columns = $cols;
        }
        private function render($reviews, $as_layout, $number_cols){

            // User Options
            $hide_disclaimer = isset($options['ezrwp_disclaimer']) == 1 ? true : false;
            $hide_view_all_link = isset($options['ezrwp_hide_view_all_link']) == 1 ? true : false;
            $hide_zillow_logo = isset($options['ezrwp_hide_zillow_logo']) == 1 ? true : false;

            // Layout Options
            // Get Layout
            $layout = $this->layout;
            if( isset($as_layout) && $as_layout != null ){
                $layout = $as_layout;
            }

            // Get Grid Columns
            $grid_columns = $this->grid_columns;
            if( $layout == "grid" ){
                // Grid
                if(isset($number_cols) && $number_cols != null){
                    $grid_columns = $number_cols;
                }
            }
            $grid_columns_class = ($layout == "grid" && $grid_columns > 0) ? 'ezrwp-grid-'. $grid_columns : '';

            // Other options
            $view_all_link = '';
            if( !$hide_view_all_link ){
                $view_all_link = '<p class="ezrwp-call-to-action"><a href="'. $this->url . '#reviews" class="z-profile-link" target="_blank" rel="nofollow">View all '. $this->info->reviewCount .' reviews.</a></p>';
            }

            $zillow_logo = '';
            if( !$hide_zillow_logo ){
                $zillow_logo = '<p class="ezrwp-attribution"><a href="'. $this->url . '" class="z-profile-link" target="_blank" rel="nofollow"><img src="https://www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/zillow_logo_200x50.gif" width="150" height="38" alt="Real Estate on Zillow"></a></p>';
            }

            $mandatory_disclaimer = '';
            if( !$hide_disclaimer ){
                $mandatory_disclaimer = '
                    <p class="ezrwp-mandatory-disclaimer">© Zillow, Inc., 2006-2016. Use is subject to <a href="https://www.zillow.com/corp/Terms.htm" target="_blank">Terms of Use</a><br />
                        <a href="https://www.zillow.com/wikipages/What-is-a-Zestimate/" target="_blank">What\'s a Zestimate?</a>
                    </p>';
            }

            $wrapper_start = '<div class="ezrwp-wrapper ezrwp-'. $layout .' '. $grid_columns_class .'"><div class="ezrwp-content">';
            $wrapper_end = '</div></div>';

            $output = $wrapper_start;
            $output .= $reviews;
            $output .= $view_all_link;
            $output .= $zillow_logo;
            $output .= $mandatory_disclaimer;
            $output .= $wrapper_end;

            return $output;

        }
        // Convert date to time elapsed
        private function convert_date_to_time_elapsed($datetime, $full = false) {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) $string = array_slice($string, 0, 1);
            return $string ? implode(', ', $string) . ' ago' : 'just now';
        }
        public function get_reviews($as_layout, $number_cols){

            // User Options
            $hide_date = isset($options['ezrwp_hide_date']) == 1 ? true : false;
            $hide_stars = isset($options['ezrwp_hide_stars']) == 1 ? true : false;
            $hide_reviewer_summary = isset($options['ezrwp_hide_reviewer_summary']) == 1 ? true : false;
            $layout = ($as_layout == '') ? $this->layout : $as_layout;
            $number_cols = ($number_cols == '') ? $this->grid_columns : $number_cols;

            // Output
            $i = 0;
            $reviews = '';

            // Professional Reviews
            foreach($this->reviews->review as $review) :
                $description = $review->description;
                $summary = lcfirst($review->reviewSummary);
                $url = $review->reviewUrl;
                if( !$hide_date ){
                    $date = 
                        '<div class="ezrwp-date">
                            '. $this->convert_date_to_time_elapsed(date( "Y-m-d", strtotime($review->reviewDate))) .'
                        </div>';
                }
                if( !$hide_stars ){
                    $stars = $review->rating;
                    $star_count = floor($stars); // count whole stars
                    $half_star_toggle = '';
                    if( $stars - floor($stars) > 0 ){
                        // add half star if required
                        $half_star_toggle = "ezrwp-plus-half-star";
                    } 

                    $stars = '
                        <div class="ezrwp-stars ezrwp-stars-'. $star_count .' '. $half_star_toggle .'"></div>
                    ';
                }
                if( !$hide_reviewer_summary ){
                    $reviewer_summary = '<span class="review-summary">who '. $summary .'</span>';
                }
                $reviewer = '
                    <div class="ezrwp-reviewer">
                        <p> &mdash; <a href="'. $url .'" target="_blank" rel="nofollow">Zillow Reviewer</a> '. $reviewer_summary .'
                        </p>
                    </div>
                ';
                $review_quote = '<blockquote>'. $description .'</blockquote>';
                $before_review = '<div class="col ezrwp-col">';
                $after_review = '</div>';

                $reviews .= $before_review;
                $reviews .= $review_quote;
                $reviews .= $stars;
                $reviews .= $date;
                $reviews .= $reviewer;
                $reviews .= $after_review;

                $i++;
                if( $i % $number_cols == 0 ){
                    $reviews .= '<div style="clear:both"></div>';
                }
            endforeach;

            return $this->render($reviews, $as_layout, $number_cols);
        }
    }
}
?>