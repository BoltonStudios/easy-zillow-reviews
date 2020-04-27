<?php

/**
 * The Easy_Zillow_Reviews_Professional_Widget class
 *
 * Adds the Easy Zillow Reviews Professional widget to WordPress
 *
 *
 * @link       https://www.boltonstudios.com
 * @since      1.1.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/includes
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

if ( ! class_exists( 'Easy_Zillow_Reviews_Professional_Widget_Init' ) ) {

    class Easy_Zillow_Reviews_Professional_Widget_Init{
            
        /**	
         *	The reviews fetched from the Zillow API Network, and relevant user options.
        *	
        * @since    1.1.0	
        * @access   private	
        * @var      Easy_Zillow_Reviews_Professional    $zillow_professional_reviews   	
        */	
        private $zillow_professional_reviews;

        function __construct( $zillow_professional_reviews ){

            // The constructor accepts an object containing reviews data, and stores the object.
            $this->set_zillow_professional_reviews( $zillow_professional_reviews );

            // Initialize the widget
            add_action('widgets_init', array($this, 'init'));
        }
        function init(){

            // Create a new widget instance
            $professional_widget = new Easy_Zillow_Reviews_Professional_Widget();
            
            // Pass the reviews data to the Easy_Zillow_Reviews_Professional_Widget instance
            $professional_widget->set_professional_reviews($this->get_zillow_professional_reviews());

            // Register widget with WordPress
            register_widget($professional_widget);
        }
        
        /**
         * Get the value of zillow_professional_reviews
         *
         * @since    1.1.4
         */
        public function get_zillow_professional_reviews()
        {
                return $this->zillow_professional_reviews;
        }

        /**
         * Set the value of zillow_professional_reviews
         *
         * @return  self
         */ 
        public function set_zillow_professional_reviews($zillow_professional_reviews)
        {
                $this->zillow_professional_reviews = $zillow_professional_reviews;

                return $this;
        }
    }
}

if ( ! class_exists( 'Easy_Zillow_Reviews_Professional_Widget' ) ) {

    class Easy_Zillow_Reviews_Professional_Widget extends WP_Widget{

        /**
         * The reviews fetched from the Zillow API Network, and relevant user options.
         *
         * @since    1.1.0
         * @access   protected
         * @var      Easy_Zillow_Reviews_Professional  $professional_reviews  
         */
        private $professional_reviews;
        
        /**
         * Setup the widget
         *
         * @since    1.1.0
         */
        public function __construct() {
            $widget_ops = array( 
                'classname' => 'ezrwp_widget',
                'description' => 'Display Zillow Professional Reviews',
            );
            parent::__construct( 'ezrwp_widget', 'Zillow Professional Reviews', $widget_ops );
        }
        
        /**
         * Render the widget content on the public-facing website.
         *
         * @since    1.1.0
         */
        public function widget( $args, $instance ) {
            
            // Default vars
            $reviews = $this->get_professional_reviews();
            $layout = $reviews->get_layout();
            $cols = $reviews->get_grid_columns();
            $count = $reviews->get_count();
            
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

            $output = $reviews->get_reviews_output( $reviews, $layout, $cols, $count );

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
            
            $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
            $count = ! empty( $instance['count'] ) ? $instance['count'] : esc_html__( '', 'text_domain' );
            $layout = ! empty( $instance['layout'] ) ? $instance['layout'] : esc_html__( '', 'text_domain' );
            $cols = ! empty( $instance['cols'] ) ? $instance['cols'] : esc_html__( '', 'text_domain' );
            ?>
            <p>Add your Zillow Web Services ID and Screenname in Settings -> Zillow Reviews.</p>
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
            
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
            $instance['count'] = ( ! empty( $new_instance['count'] ) ) ? sanitize_text_field( $new_instance['count'] ) : '';
            $instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? sanitize_text_field( $new_instance['layout'] ) : '';
            $instance['cols'] = ( ! empty( $new_instance['cols'] ) ) ? sanitize_text_field( $new_instance['cols'] ) : '';

            return $instance;
        }
        
        /**
         * Get the value of professional_reviews
         *
         * @since    1.1.0
         */
        public function get_professional_reviews()
        {
                return $this->professional_reviews;
        }

        /**
         * Set the value of professional_reviews
         *
         * @return  self
         */ 
        public function set_professional_reviews($professional_reviews)
        {
                $this->professional_reviews = $professional_reviews;

                return $this;
        }
    }
}