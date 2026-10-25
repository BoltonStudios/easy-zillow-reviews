<?php

/**
 * The Easy_Zillow_Reviews_Elementor_Widget class
 *
 * The Zillow Reviews widget for Elementor. Renders through the same handlers as the block,
 * so Premium options (slider, team reviews, lender reviews) appear when they're available.
 *
 * @link       https://www.boltonstudios.com
 * @since      1.8.0
 * @package    Easy_Zillow_Reviews
 * @subpackage Easy_Zillow_Reviews/elementor
 * @author     Aaron Bolton <aaron@boltonstudios.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Easy_Zillow_Reviews_Elementor_Widget' ) ) {

    class Easy_Zillow_Reviews_Elementor_Widget extends \Elementor\Widget_Base{

        /**
         * The reviews data used for the widget's defaults and options.
         *
         * @since    1.8.0
         * @access   private
         * @var      Easy_Zillow_Reviews_Data|null    $zillow_data
         */
        private static $zillow_data = null;

        public static function set_zillow_data( $zillow_data ){

            self::$zillow_data = $zillow_data;
        }

        public function get_name(){

            return 'easy-zillow-reviews';
        }

        public function get_title(){

            return esc_html__( 'Zillow Reviews', 'easy-zillow-reviews' );
        }

        public function get_icon(){

            return 'eicon-review';
        }

        public function get_categories(){

            return array( 'general' );
        }

        public function get_keywords(){

            return array( 'zillow', 'reviews', 'real estate', 'testimonials', 'lender', 'agent' );
        }

        public function get_custom_help_url(){

            return 'https://wordpress.org/plugins/easy-zillow-reviews/';
        }

        /**
         * Zillow's terms don't allow storing review data, so Elementor must never cache the output.
         *
         * @since    1.8.0
         * @return   bool
         */
        protected function is_dynamic_content(): bool {

            return true;
        }

        /**
         * While Elementor builds its element cache, elements that return true are rendered fresh on each view instead
         * of being stored. Always render fresh, whatever the widget's "Cache Settings" say.
         *
         * @since    1.8.0
         * @return   bool
         */
        protected function should_render_shortcode() {

            return (bool) apply_filters( 'elementor/element/should_render_shortcode', false );
        }

        public function has_widget_inner_wrapper(): bool {

            return false;
        }

        /**
         * On the site, the reviews load their own styles. In the editor, widgets render over AJAX, so
         * the preview needs the styles up front.
         *
         * @since    1.8.0
         * @return   string[]
         */
        public function get_style_depends(): array {

            $handles = $this->is_preview()
                ? array( 'easy-zillow-reviews', 'easy-zillow-reviews-carousel' )
                : array( 'easy-zillow-reviews' );

            return $this->registered( $handles, 'wp_style_is' );
        }

        /**
         * @since    1.8.0
         * @return   string[]
         */
        public function get_script_depends(): array {

            $handles = $this->is_preview()
                ? array( 'easy-zillow-reviews', 'easy-zillow-reviews-carousel', 'easy-zillow-reviews-elementor' )
                : array( 'easy-zillow-reviews' );

            return $this->registered( $handles, 'wp_script_is' );
        }

        /**
         * Skip assets that aren't registered, e.g. the carousel in the free version.
         *
         * @since    1.8.0
         * @param    string[]    $handles
         * @param    callable    $is_registered    wp_style_is or wp_script_is.
         * @return   string[]
         */
        private function registered( $handles, $is_registered ){

            return array_values( array_filter( $handles, function( $handle ) use ( $is_registered ){
                return call_user_func( $is_registered, $handle, 'registered' );
            } ) );
        }

        /**
         * Whether this is the editor's preview of the page.
         *
         * @since    1.8.0
         * @return   bool
         */
        private function is_preview(){

            $elementor = \Elementor\Plugin::$instance;

            return isset( $elementor->preview ) && $elementor->preview->is_preview_mode();
        }

        /**
         * Whether the widget is being shown in the Elementor editor.
         *
         * @since    1.8.0
         * @return   bool
         */
        private function is_editor(){

            $elementor = \Elementor\Plugin::$instance;

            return ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) || $this->is_preview();
        }

        /**
         * The review types, layouts, and defaults shared with the block.
         *
         * @since    1.8.0
         * @return   array
         */
        private function get_editor_settings(){

            $settings = self::$zillow_data ? self::$zillow_data->get_editor_settings( 'elementor' ) : array();

            return wp_parse_args(
                is_array( $settings ) ? $settings : array(),
                array(
                    'types'     => array( array( 'value' => 'professional', 'label' => 'Professional' ) ),
                    'layouts'   => array( 'list', 'grid' ),
                    'isPremium' => false,
                    'defaults'  => array(),
                )
            );
        }

        private function get_layout_label( $layout ){

            $labels = array(
                'list'     => __( 'List', 'easy-zillow-reviews' ),
                'grid'     => __( 'Grid', 'easy-zillow-reviews' ),
                'carousel' => __( 'Slider', 'easy-zillow-reviews' ),
            );

            return isset( $labels[ $layout ] ) ? $labels[ $layout ] : ucfirst( $layout );
        }

        private function get_type_label( $type ){

            $labels = array(
                'professional' => __( 'Professional', 'easy-zillow-reviews' ),
                'lender'       => __( 'Lender & Loan Officer', 'easy-zillow-reviews' ),
            );

            return isset( $labels[ $type['value'] ] ) ? $labels[ $type['value'] ] : $type['label'];
        }

        protected function register_controls(){

            $settings = $this->get_editor_settings();
            $defaults = wp_parse_args(
                (array) $settings['defaults'],
                array(
                    'layout'      => 'list',
                    'columns'     => 3,
                    'count'       => 3,
                    'wordLimit'   => 750,
                    'screenname'  => '',
                    'includeTeam' => false,
                )
            );
            $layouts = array_values( (array) $settings['layouts'] );
            $has_carousel = in_array( 'carousel', $layouts, true );

            // An empty layout uses the default from the plugin settings, so these conditions include it when it applies.
            $columns_layouts = array_values( array_diff( $layouts, array( 'list' ) ) );
            if( $defaults['layout'] !== 'list' ){
                $columns_layouts[] = '';
            }
            $carousel_layouts = $defaults['layout'] === 'carousel' ? array( 'carousel', '' ) : array( 'carousel' );

            $this->register_content_controls( $settings, $defaults, $layouts, $has_carousel, $columns_layouts );
            $this->register_style_controls( $has_carousel, $carousel_layouts );
        }

        private function register_content_controls( $settings, $defaults, $layouts, $has_carousel, $columns_layouts ){

            $this->start_controls_section(
                'section_reviews',
                array( 'label' => esc_html__( 'Reviews', 'easy-zillow-reviews' ) )
            );

            $types = array();
            foreach( (array) $settings['types'] as $type ){
                $types[ $type['value'] ] = $this->get_type_label( $type );
            }

            if( count( $types ) > 1 ){
                $this->add_control(
                    'reviewsType',
                    array(
                        'label'   => esc_html__( 'Review type', 'easy-zillow-reviews' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'options' => $types,
                        'default' => 'professional',
                    )
                );
            } else{
                // Hidden, so the conditions below still work when there's only one type.
                $this->add_control(
                    'reviewsType',
                    array(
                        'type'    => \Elementor\Controls_Manager::HIDDEN,
                        'default' => 'professional',
                    )
                );
            }

            $this->add_control(
                'screenname',
                array(
                    'label'       => esc_html__( 'Zillow screenname', 'easy-zillow-reviews' ),
                    'type'        => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => $defaults['screenname'],
                    'description' => esc_html__( 'Leave empty to use the screenname from the plugin settings.', 'easy-zillow-reviews' ),
                    'dynamic'     => array( 'active' => true ),
                    'ai'          => array( 'active' => false ),
                    'condition'   => array( 'reviewsType' => 'professional' ),
                )
            );

            if( ! empty( $settings['isPremium'] ) ){
                $this->add_control(
                    'includeTeam',
                    array(
                        'label'     => esc_html__( 'Include team members’ reviews', 'easy-zillow-reviews' ),
                        'type'      => \Elementor\Controls_Manager::SWITCHER,
                        'default'   => $defaults['includeTeam'] ? 'yes' : '',
                        'condition' => array( 'reviewsType' => 'professional' ),
                    )
                );
            }

            $this->add_control(
                'reviewsCount',
                array(
                    'label'       => esc_html__( 'Number of reviews', 'easy-zillow-reviews' ),
                    'type'        => \Elementor\Controls_Manager::NUMBER,
                    'min'         => 1,
                    'max'         => 10,
                    'step'        => 1,
                    'placeholder' => absint( $defaults['count'] ),
                )
            );

            $this->add_control(
                'wordLimit',
                array(
                    'label'       => esc_html__( 'Excerpt length (words)', 'easy-zillow-reviews' ),
                    'type'        => \Elementor\Controls_Manager::NUMBER,
                    'min'         => 20,
                    'max'         => 750,
                    'step'        => 1,
                    'placeholder' => absint( $defaults['wordLimit'] ),
                )
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'section_layout',
                array( 'label' => esc_html__( 'Layout', 'easy-zillow-reviews' ) )
            );

            $layout_options = array(
                /* translators: %s: the default layout from the plugin settings, e.g. "List" */
                '' => sprintf( esc_html__( 'Default (%s)', 'easy-zillow-reviews' ), $this->get_layout_label( $defaults['layout'] ) ),
            );
            foreach( $layouts as $layout ){
                $layout_options[ $layout ] = $this->get_layout_label( $layout );
            }

            $this->add_control(
                'reviewsLayout',
                array(
                    'label'   => esc_html__( 'Layout', 'easy-zillow-reviews' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'options' => $layout_options,
                    'default' => '',
                )
            );

            $this->add_control(
                'gridColumns',
                array(
                    'label'       => esc_html__( 'Columns', 'easy-zillow-reviews' ),
                    'type'        => \Elementor\Controls_Manager::NUMBER,
                    'min'         => 1,
                    'max'         => 6,
                    'step'        => 1,
                    'placeholder' => absint( $defaults['columns'] ),
                    'description' => $has_carousel ? esc_html__( 'In the slider, the number of reviews visible at once.', 'easy-zillow-reviews' ) : '',
                    'condition'   => array( 'reviewsLayout' => $columns_layouts ),
                )
            );

            $this->add_control(
                'defaults_note',
                array(
                    'type'            => \Elementor\Controls_Manager::RAW_HTML,
                    'raw'             => esc_html__( 'Empty fields use the defaults from Zillow Reviews › Settings.', 'easy-zillow-reviews' ),
                    'content_classes' => 'elementor-descriptor',
                    'separator'       => 'before',
                )
            );

            $this->end_controls_section();
        }

        private function register_style_controls( $has_carousel, $carousel_layouts ){

            $tab = \Elementor\Controls_Manager::TAB_STYLE;

            // Review text.
            $this->start_controls_section(
                'section_style_text',
                array(
                    'label' => esc_html__( 'Review Text', 'easy-zillow-reviews' ),
                    'tab'   => $tab,
                )
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'     => 'text_typography',
                    'selector' => '{{WRAPPER}} .ezrwp-wrapper blockquote',
                )
            );

            $this->add_control(
                'text_color',
                array(
                    'label'     => esc_html__( 'Text color', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-wrapper blockquote' => 'color: {{VALUE}};' ),
                )
            );

            $this->add_control(
                'quote_color',
                array(
                    'label'     => esc_html__( 'Quote mark color', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-wrapper blockquote::before' => 'color: {{VALUE}};' ),
                )
            );

            $this->end_controls_section();

            // Stars.
            $this->start_controls_section(
                'section_style_stars',
                array(
                    'label' => esc_html__( 'Stars', 'easy-zillow-reviews' ),
                    'tab'   => $tab,
                )
            );

            $this->add_control(
                'star_color',
                array(
                    'label'     => esc_html__( 'Color', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-stars::before, {{WRAPPER}} .ezrwp-star-average' => 'color: {{VALUE}};' ),
                )
            );

            $this->add_responsive_control(
                'star_size',
                array(
                    'label'      => esc_html__( 'Size', 'easy-zillow-reviews' ),
                    'type'       => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => array( 'px', 'em' ),
                    'range'      => array(
                        'px' => array( 'min' => 10, 'max' => 48 ),
                        'em' => array( 'min' => 0.5, 'max' => 3, 'step' => 0.1 ),
                    ),
                    'selectors'  => array(
                        // The stars have a fixed height for the default size.
                        '{{WRAPPER}} .ezrwp-col .ezrwp-stars'         => 'height: {{SIZE}}{{UNIT}}; line-height: 1;',
                        '{{WRAPPER}} .ezrwp-col .ezrwp-stars::before' => 'font-size: {{SIZE}}{{UNIT}};',
                    ),
                )
            );

            $this->end_controls_section();

            // Reviewer and date.
            $this->start_controls_section(
                'section_style_reviewer',
                array(
                    'label' => esc_html__( 'Reviewer and Date', 'easy-zillow-reviews' ),
                    'tab'   => $tab,
                )
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'     => 'reviewer_typography',
                    'selector' => '{{WRAPPER}} .ezrwp-reviewer p, {{WRAPPER}} .ezrwp-reviewer p *',
                )
            );

            $this->add_control(
                'reviewer_color',
                array(
                    'label'     => esc_html__( 'Reviewer color', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-reviewer p' => 'color: {{VALUE}};' ),
                )
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                array(
                    'name'      => 'date_typography',
                    'label'     => esc_html__( 'Date typography', 'easy-zillow-reviews' ),
                    'selector'  => '{{WRAPPER}} .ezrwp-date',
                    'separator' => 'before',
                )
            );

            $this->add_control(
                'date_color',
                array(
                    'label'     => esc_html__( 'Date color', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-date' => 'color: {{VALUE}};' ),
                )
            );

            $this->end_controls_section();

            if( ! $has_carousel ){
                return;
            }

            // Slider buttons.
            $this->start_controls_section(
                'section_style_slider',
                array(
                    'label'     => esc_html__( 'Slider Controls', 'easy-zillow-reviews' ),
                    'tab'       => $tab,
                    'condition' => array( 'reviewsLayout' => $carousel_layouts ),
                )
            );

            $this->add_control(
                'slider_color',
                array(
                    'label'     => esc_html__( 'Color', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-carousel-controls' => 'color: {{VALUE}};' ),
                )
            );

            $this->add_control(
                'slider_background',
                array(
                    'label'     => esc_html__( 'Button background', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-carousel-controls button' => 'background: {{VALUE}};' ),
                )
            );

            $this->add_control(
                'slider_background_hover',
                array(
                    'label'     => esc_html__( 'Button background on hover', 'easy-zillow-reviews' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => array( '{{WRAPPER}} .ezrwp-carousel-controls button:hover:not(:disabled)' => 'background: {{VALUE}};' ),
                )
            );

            $this->end_controls_section();
        }

        protected function render(){

            $settings = $this->get_settings_for_display();

            // Same attribute names as the block, so both render through the same handlers.
            $attributes = array(
                'reviewsType'   => isset( $settings['reviewsType'] ) ? sanitize_key( $settings['reviewsType'] ) : '',
                'reviewsLayout' => isset( $settings['reviewsLayout'] ) ? sanitize_key( $settings['reviewsLayout'] ) : '',
                'gridColumns'   => isset( $settings['gridColumns'] ) ? absint( $settings['gridColumns'] ) : 0,
                'reviewsCount'  => isset( $settings['reviewsCount'] ) ? absint( $settings['reviewsCount'] ) : 0,
                'wordLimit'     => isset( $settings['wordLimit'] ) ? absint( $settings['wordLimit'] ) : 0,
                'screenname'    => isset( $settings['screenname'] ) ? sanitize_text_field( (string) $settings['screenname'] ) : '',
            );

            if( isset( $settings['includeTeam'] ) ){
                $attributes['includeTeam'] = $settings['includeTeam'] === 'yes';
            }

            $output = apply_filters( 'ezrwp_render_block', null, $attributes );

            if( $output === null || trim( (string) $output ) === '' ){

                // Visitors see nothing; editors get a hint instead of an empty box.
                if( $this->is_editor() ){
                    echo '<p class="ezrwp-editor-notice" style="padding:1em;border:1px dashed currentColor;opacity:.7;">'
                        . esc_html__( 'No Zillow reviews to show. Check the connection in Zillow Reviews › Settings.', 'easy-zillow-reviews' )
                        . '</p>';
                }

                return;
            }

            // The reviews HTML is escaped when it's built.
            echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
