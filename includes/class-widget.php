<?php

// Widget
class EasyZillowReviewsWidget extends WP_Widget{
    
    // Setup widget
    public function __construct() {
		$widget_ops = array( 
			'classname' => 'ezrwp_widget',
			'description' => 'Display Zillow Reviews',
		);
		parent::__construct( 'ezrwp_widget', 'Easy Zillow Reviews', $widget_ops );
	}
    
    // Output content
    public function widget( $args, $instance ) {
        
        // Vars
        $options = $GLOBALS['ezrwpOptions'];
        
        // Get widget review count 
        if( ! empty( $instance['count'] ) ){
            $count = $instance['count']; // widget count ettings
        } elseif( $options['ezrwp_count'] ){
            $count = $options['ezrwp_count']; // fallback to review count in Settings
        } else{
            $count = 3; // default
        }
        // Get widget review layout
        if( ! empty( $instance['layout'] ) ){
            $layout = $instance['layout']; // widget count ettings
        } elseif( $options['ezrwp_layout'] ){
            $layout = $options['ezrwp_layout']; // fallback to review count in Settings
        } else{
            $layout = null;
        }
        // Get widget review layout
        if( ! empty( $instance['cols'] ) ){
            $cols = $instance['cols']; // widget count ettings
        } elseif( $options['ezrwp_cols'] ){
            $cols = $options['ezrwp_cols']; // fallback to review count in Settings
        } else{
            $cols = null;
        }
        // Get widget title
		if ( ! empty( $instance['title'] ) ){
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
        // Get reviews
        $zillowData = ezrwpFetchProDataFromZillow( $options['ezrwp_zwsid'], $options['ezrwp_screenname'], $count );
        if( $zillowData -> hasReviews ){
            $output = $zillowData -> getReviews($layout, $cols);
        } else {
            $output = '<p>Unable to load reviews. Zillow says: <strong>'. $zillowData -> message .'</strong>.</p>';
        }
        // Output content
        echo $args['before_widget'];
        echo $output;
        echo $args['after_widget'];
	}
    
    // Widget Admin
    public function form( $instance ) {
		// outputs the options form on admin
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
                <option value="list" <?php echo (esc_attr( $layout ) == 'list') ? selected : '' ; ?>>
                <?php esc_html_e( 'List', 'ezrwp' ); ?>
                </option>
                <option value="grid" <?php echo (esc_attr( $layout ) == 'grid') ? selected : '' ; ?>>
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
    
    // Process widget options on save
    public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
        $instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? sanitize_text_field( $new_instance['count'] ) : '';
		$instance['layout'] = ( ! empty( $new_instance['layout'] ) ) ? sanitize_text_field( $new_instance['layout'] ) : '';
		$instance['cols'] = ( ! empty( $new_instance['cols'] ) ) ? sanitize_text_field( $new_instance['cols'] ) : '';

		return $instance;
	}
}
add_action( 'widgets_init', function(){
	register_widget( 'EasyZillowReviewsWidget' ); // Requires PHP 5.3+
});