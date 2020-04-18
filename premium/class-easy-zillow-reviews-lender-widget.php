<?php

/**
 * The Easy_Zillow_Reviews_Lender_Widget class
 *
 * Adds the Easy Zillow Reviews Lender widget to WordPress
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */
    
if ( ! class_exists( 'Easy_Zillow_Reviews_Lender_Widget_Init' ) ) {

    class Easy_Zillow_Reviews_Lender_Widget_Init extends Easy_Zillow_Reviews_Lender{
            
        /**	
         *	The reviews fetched from the Zillow API Network, and relevant user options.
        *	
        * @since    1.1.4
        * @access   private	
        * @var      Easy_Zillow_Reviews_Lender    $zillow_lender_reviews   	
        */	
        private $zillow_lender_reviews;

        function __construct( $zillow_lender_reviews ){

            // The constructor accepts an object containing reviews data, and stores the object.
            $this->set_zillow_lender_reviews( $zillow_lender_reviews );

            // Initialize the widget
            add_action('widgets_init', array($this, 'init'));
        }
        function init(){

            $lender_widget = new Easy_Zillow_Reviews_Lender_Widget();

            // Pass this Easy_Zillow_Reviews_Lender_Widget_Init class instance to the Easy_Zillow_Reviews_Lender_Widget class instance
            $lender_widget->set_lender_reviews($this);
            
            // Pass the reviews data to the Easy_Zillow_Reviews_Lender_Widget instance
            $lender_widget->set_lender_reviews($this->get_zillow_lender_reviews());

            // Register widget
            register_widget($lender_widget);
        }
        
        /**
         * Get the value of zillow_lender_reviews
         *
         * @since    1.1.4
         */
        public function get_zillow_lender_reviews()
        {
                return $this->zillow_lender_reviews;
        }

        /**
         * Set the value of zillow_lender_reviews
         *
         * @return  self
         */ 
        public function set_zillow_lender_reviews($zillow_lender_reviews)
        {
                $this->zillow_lender_reviews = $zillow_lender_reviews;

                return $this;
        }
    }
}
    
if ( ! class_exists( 'Easy_Zillow_Reviews_Lender_Widget' ) ) {
    
    class Easy_Zillow_Reviews_Lender_Widget extends WP_Widget{

        /**
         * The Easy_Zillow_Reviews_Lender class instance
         *
         * @since    1.1.0
         * @access   protected
         * @var      Easy_Zillow_Reviews_Lender  $lender_reviews  
         */
        private $lender_reviews;
        
        /**
         * Setup the widget
         *
         * @since    1.1.0
         */
        public function __construct() {
            $widget_ops = array( 
                'classname' => 'ezrwp_lender_widget',
                'description' => 'Display Zillow Lender Reviews',
            );
            parent::__construct( 'ezrwp_lender_widget', 'Zillow Lender Reviews', $widget_ops );
        }
        
        /**
         * Render the widget content on the public-facing website.
         *
         * @since    1.1.0
         */
        public function widget( $args, $instance ) {
            
            // Defaults
            $lender_reviews = $this->get_lender_reviews();
            $layout = $lender_reviews->get_layout();
            $cols = $lender_reviews->get_grid_columns();
            $count = $lender_reviews->get_count();
            
            // Get widget instance settings 
            if( ! empty( $instance['count'] ) ){
                $count = $instance['count'];
            }
            if( ! empty( $instance['layout'] ) ){
                $layout = $instance['layout'];
            }
            // Get widget review layout
            if( ! empty( $instance['cols'] ) ){
                $cols = $instance['cols'];
            }
            
            // Get widget title
            if ( ! empty( $instance['title'] ) ){
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }

            // Fetch reviews from Zillow
            $lender_reviews->fetch_reviews_from_zillow( $count );

            // Render output
            if( $lender_reviews->get_has_reviews() ){

                // Success
                $output = $lender_reviews->layout_lender_reviews( $layout, $cols );
            } else {

                // Error
                $output = '<p>Unable to load reviews. Zillow says: <strong>'. $lender_reviews -> get_message() .'</strong>.</p>';
            }
            // Output content
            echo $args['before_widget'];
            echo $output;
            echo $args['after_widget'];
        }
        
        /**
         * Render the widget options form on the admin Widgets page.
         *
         * @since    1.1.0
         */
        public function form( $instance ) {
            // outputs the options form on admin
            $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
            $count = ! empty( $instance['count'] ) ? $instance['count'] : esc_html__( '', 'text_domain' );
            $layout = ! empty( $instance['layout'] ) ? $instance['layout'] : esc_html__( '', 'text_domain' );
            $cols = ! empty( $instance['cols'] ) ? $instance['cols'] : esc_html__( '', 'text_domain' );
            ?>
            <p>Add your Zillow Mortgages Partner ID, NMLS#, and Company Name if applicable in Settings -> Zillow Reviews -> Lender Reviews.</p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <?php esc_attr_e( 'Title:', 'text_domain' ); ?>
                </label> 
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
                    <?php esc_attr_e( 'Number of reviews to show:', 'text_domain' ); ?>
                </label> 
                <input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" style="width:45px;" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="1" max="<?php echo $GLOBALS['ezrwpReviewLimit'] ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>">
                    <?php esc_attr_e( 'Reviews Layout:', 'text_domain' ); ?>
                </label> 
                
                <select id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"
                class="widefat ezrwp_layout"
                name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>]"
                        onchange="ezrwpToggleGridCols(this)">
                    <option value="list" <?php echo (esc_attr( $layout ) == 'list') ? 'selected' : '' ; ?>>
                    <?php esc_html_e( 'List', 'ezrwp' ); ?>
                    </option>
                    <option value="grid" <?php echo (esc_attr( $layout ) == 'grid') ? 'selected' : '' ; ?>>
                    <?php esc_html_e( 'Grid', 'ezrwp' ); ?>
                    </option>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'cols' ) ); ?>">
                    <?php esc_attr_e( 'Review Grid Columns:', 'text_domain' ); ?>
                </label> 
                <input id="<?php echo esc_attr( $this->get_field_id( 'cols' ) ); ?>" class="ezrwp_cols" style="width:45px;" name="<?php echo esc_attr( $this->get_field_name( 'cols' ) ); ?>" type="number" value="<?php echo esc_attr( $cols ); ?>" min="2" max="6">
            </p>
            <script>
                function ezrwpToggleGridCols(elem){
                    // Toggle grid columns field based on layout selected
                    var ezrwpCols = jQuery(elem).closest('.widget-content').find('.ezrwp_cols');
                    ezrwpCols.prop('disabled', (elem.value == 'list' ? true : false));
                }
                
                jQuery('.ezrwp_layout').each(function(){ezrwpToggleGridCols(this)});
            </script>
            <?php 
        }
        
        /**
         * Process widget options on save
         *
         * @since    1.1.0
         */
        public function update( $new_instance, $old_instance ) {
            // processes widget options to be saved
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
            $instance['count'] = ( ! empty( $new_instance['count'] ) ) ? sanitize_text_field( $new_instance['count'] ) : '';
            $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? sanitize_text_field( $new_instance['layout'] ) : '';
            $instance['cols'] = ( ! empty( $new_instance['cols'] ) ) ? sanitize_text_field( $new_instance['cols'] ) : '';

            return $instance;
        }
        
        /**
         * Get the value of lender_reviews
         *
         * @since    1.1.0
         */
        public function get_lender_reviews()
        {
                return $this->lender_reviews;
        }

        /**
         * Set the value of lender_reviews
         *
         * @return  self
         */ 
        public function set_lender_reviews($lender_reviews)
        {
                $this->lender_reviews = $lender_reviews;

                return $this;
        }
    }
}