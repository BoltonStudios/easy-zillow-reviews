<?php
/*
* Easy Zillow Lender Reviews
*/
if(!class_exists('EasyZillowLenderReviews')){
    class EasyZillowLenderReviews{

        public $profile;
        public $profileURL;
        public $reviews;
        public $reviewCount;
        public $hasReviews;
        public $message;

        private $options;
        private $layout;
        private $gridColumns;

        public function __construct(){

            // Defaults
            $this->options = $GLOBALS['ezrwpOptions'];
            $this->layout = isset($this->options['ezrwp_layout']) ? $this->options['ezrwp_layout'] : 'list';
            $this->gridColumns = isset($this->options['ezrwp_cols']) ? $this->options['ezrwp_cols'] : 3;
        }
        private function layout($reviews, $asLayout, $numberCols){

            // User Options
            $hideDisclaimer = isset($options['ezrwp_disclaimer']) == 1 ? true : false;
            $hideViewAllLink = isset($options['ezrwp_hide_view_all_link']) == 1 ? true : false;
            $hideZillowLogo = isset($options['ezrwp_hide_zillow_logo']) == 1 ? true : false;

            // Layout Options
            // Get Layout
            $layout = $this->layout;
            if( isset($asLayout) && $asLayout != null ){
                $layout = $asLayout;
            }

            // Get Grid Columns
            $gridColumns = $this->gridColumns;
            if( $layout == "grid" ){
                // Grid
                if(isset($numberCols) && $numberCols != null){
                    $gridColumns = $numberCols;
                }
            }
            $gridColumnsClass = ($layout == "grid" && $gridColumns > 0) ? 'ezrwp-grid-'. $gridColumns : '';

            // Other options
            $viewAllLink = '';
            if( !$hideViewAllLink ){
                $viewAllLink = '<p class="ezrwp-call-to-action"><a href="'. $this->profileURL . '#reviews" class="z-profile-link" target="_blank" rel="nofollow">View all '. $this->reviewCount .' reviews.</a></p>';
            }

            $zillowLogo = '';
            if( !$hideZillowLogo ){
                $zillowLogo = '<p class="ezrwp-attribution"><a href="'. $this->profileURL . '" class="z-profile-link" target="_blank" rel="nofollow"><img src="https://www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/Zillowlogo_200x50.gif" width="150" height="38" alt="Real Estate on Zillow"></a></p>';
            }

            $mandatoryDisclaimer = '';
            if( !$hideDisclaimer ){
                $mandatoryDisclaimer = '
                    <p class="ezrwp-mandatory-disclaimer">© Zillow, Inc., 2006-2016. Use is subject to <a href="https://www.zillow.com/corp/Terms.htm" target="_blank">Terms of Use</a><br />
                        <a href="https://www.zillow.com/wikipages/What-is-a-Zestimate/" target="_blank">What\'s a Zestimate?</a>
                    </p>';
            }

            $wrapperStart = '<div class="ezrwp-wrapper ezrwp-'. $layout .' '. $gridColumnsClass .'"><div class="ezrwp-content">';
            $wrapperEnd = '</div></div>';

            $output = $wrapperStart;
            $output .= $reviews;
            $output .= $viewAllLink;
            $output .= $zillowLogo;
            $output .= $mandatoryDisclaimer;
            $output .= $wrapperEnd;

            return $output;

        }
        function getReviews($asLayout, $numberCols){

            // User Options
            $hideDate = isset($options['ezrwp_hide_date']) == 1 ? true : false;
            $hideStars = isset($options['ezrwp_hide_stars']) == 1 ? true : false;
            $hideReviewerSummary = isset($options['ezrwp_hide_reviewer_summary']) == 1 ? true : false;
            $layout = ($asLayout == '') ? $this->layout : $asLayout;
            $numberCols = ($numberCols == '') ? $this->gridColumns : $numberCols;

            // Output
            $i = 0;
            $reviews = '';

            // Professional Reviews
            foreach($this->reviews as $review) :

                // $summary = lcfirst($review->reviewSummary);
                // $url = $review->reviewURL;

                $title = '<h4><strong>' . $review->title . '</strong></h4>';
                $description = $review->content;
                $loanPurpose = $review->loanPurpose;
                if( !$hideDate ){
                    $date = 
                        '<div class="ezrwp-date">
                            '. ezrwpConvertDateToTimeElapsed(date( "Y-m-d", strtotime($review->created))) .'
                        </div>';
                }
                if(!$hideStars){
                    $stars = $review->rating;
                    $starCount = floor($stars); // count whole stars
                    $halfStarToggle = '';
                    if( $stars - floor($stars) > 0 ){
                        // add half star if required
                        $halfStarToggle = "ezrwp-plus-half-star";
                    } 

                    $stars = '
                        <div class="ezrwp-stars ezrwp-stars-'. $starCount .' '. $halfStarToggle .'"></div>
                    ';
                }
                if( !$hideReviewerSummary ){
                    $reviewerSummary = '<span class="review-summary">who received a '. strtolower($loanPurpose) .' loan</span>';
                }
                $reviewer = '
                    <div class="ezrwp-reviewer">
                        <p> &mdash; Zillow Reviewer '. $reviewerSummary .'.</p>
                    </div>
                ';
                $reviewQuote = '<blockquote>'. $title . $description .'</blockquote>';
                $beforeReview = '<div class="col ezrwp-col">';
                $afterReview = '</div>';

                $reviews .= $beforeReview;
                $reviews .= $reviewQuote;
                $reviews .= $stars;
                $reviews .= $date;
                $reviews .= $reviewer;
                $reviews .= $afterReview;

                $i++;
                if( $i % $numberCols == 0 ){
                    $reviews .= '<div style="clear:both"></div>';
                }
            endforeach;

            return $this->layout($reviews, $asLayout, $numberCols);
        }
    }
}
?>