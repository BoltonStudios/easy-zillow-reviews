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
    
class Easy_Zillow_Reviews_Professional_Widget_Init extends Easy_Zillow_Reviews_Professional{

    function __construct(){
        
        add_action('widgets_init', array($this, 'init'));
    }
    function init(){

        $professional_widget = new Easy_Zillow_Reviews_Professional_Widget();

        // Get saved admin settings and defaults
        $general_options = get_option('ezrwp_general_options'); // General admin tab settings
        $professional_reviews_options = get_option('ezrwp_professional_reviews_options'); // Professionals Reviews admin tab settings
        $layout = isset($general_options['ezrwp_layout']) ? $general_options['ezrwp_layout'] : 'list';
        $grid_columns = isset($general_options['ezrwp_cols']) ? $general_options['ezrwp_cols'] : 3;
        $count = isset($general_options['ezrwp_count']) ? $general_options['ezrwp_count'] : 3;
        
        // Pass saved admin settings to this Easy_Zillow_Reviews_Professional_Widget_Init class instance
        $this->set_general_options($general_options);
        $this->set_professional_reviews_options($professional_reviews_options);
        $this->set_layout($layout);
        $this->set_grid_columns($grid_columns);
        $this->set_count($count);

        // Pass this Easy_Zillow_Reviews_Professional_Widget_Init class instance to the Easy_Zillow_Reviews_Professional_Widget class instance
        $professional_widget->set_professional_reviews($this);

        // Register widget
        register_widget($professional_widget);
    }
}
class Easy_Zillow_Reviews_Professional_Widget extends WP_Widget{

	/**
	 * The Easy_Zillow_Reviews_Professional class instance
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
			'classname' => 'ezrwp_profesional_widget',
			'description' => 'Display Zillow Professional Reviews',
		);
		parent::__construct( 'ezrwp_professional_widget', 'Zillow Professional Reviews', $widget_ops );
	}
    
    /**
     * Render the widget content on the public-facing website.
     *
     * @since    1.1.0
     */
    public function widget( $args, $instance ) {
        
        // Default vars
        $professional_reviews = $this->get_professional_reviews();
        $layout = $professional_reviews->get_layout();
        $cols = $professional_reviews->get_grid_columns();
        $count = $professional_reviews->get_count();
        
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
        $professional_reviews->fetch_reviews_from_zillow( $count );

        // Render output
        if( $professional_reviews->get_has_reviews() ){

            // Success
            $output = $professional_reviews->layout_reviews( $layout, $cols );
        } else {

            // Error
            $output = '<p>Unable to load reviews. Zillow says: <strong>'. $professional_reviews -> get_message() .'</strong>.</p>';
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