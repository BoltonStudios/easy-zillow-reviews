import { registerBlockType } from '@wordpress/blocks';
import { TextControl } from '@wordpress/components';
import { SelectControl } from '@wordpress/components';
import { RangeControl } from '@wordpress/components';
import { BlockControls } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { Panel, PanelBody, PanelRow } from '@wordpress/components';
import { __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const generalOptions = zillow_data[0].general_options;
const apis = zillow_data[0].available_apis;

console.log( zillow_data[0] );

registerBlockType( 'boltonstudios/easy-zillow-reviews', {

    title: __( 'Zillow Reviews', 'easy-zillow-reviews' ),
    description: __( 'Display reviews from Zillow on your site.', 'easy-zillow-reviews' ),
    icon: 'star-filled',
    category: 'widgets',
    attributes: {
        screenname: {
            type: 'string',
            default: "test"
        },
        reviewsType: {
            type: 'string',
            default: generalOptions.available_apis
        },
        reviewsLayout: {
            type: 'string',
            default: generalOptions.ezrwp_layout
        },
        gridColumns: {
            type: 'number',
            default: generalOptions.ezrwp_cols,
        },
        reviewsCount: {
            type: 'number',
            default: generalOptions.ezrwp_count,
        }
    },
	example: {
		attributes: {
            reviewsType: 'professional',
            reviewsLayout: 'grid',
            gridColumns: '2',
            reviewCount: '2'
		},
	},
    edit: function( props ) {

        const { InspectorControls } = wp.editor;
        const screenname = props.attributes.screenname;
        const layout = props.attributes.reviewsLayout;
        const columns = props.attributes.gridColumns;
        const count = props.attributes.reviewsCount;
        const reviewsType = props.attributes.reviewsType;

        //
        const ScreennameControl = ( reviewsType ) => {

            // Initialize variables.
            var control = null

            // Only display the Screenname TextControl if the 'professional' type is selected.
            if( reviewsType == 'professional' ){

                control = <TextControl
                    label= 'Screenname'
                    value={ screenname }
                    onChange={ screenname => props.setAttributes( { screenname } ) }
                />
            }
            return control;
        }

        //
        const ReviewsControl = ( apis ) => {

            var apiOptions = []; // dictionary

            apis.forEach( element => { 
                apiOptions.push({
                    value: element[0], label: element[1] 
                }); 
            });

            const control = <SelectControl
                label='Select Review Type'
                value={ reviewsType }
                options={ apiOptions }
                onChange={ reviewsType => props.setAttributes( { reviewsType } ) }
            />
            return control;
        }

        //
        const LayoutControl =  <SelectControl
                                    label='Select Layout'
                                    value={ layout }
                                    options={[
                                        { value: 'list', label: 'List' },
                                        { value: 'grid', label: 'Grid' },
                                    ]}
                                    onChange={ reviewsLayout => props.setAttributes( { reviewsLayout } ) }
                                />

        //
        const GridControl = ( reviewsLayout ) => {

            var control = null

            // Only display the Grid Columns range control if the 'grid' layout is selected
            if( reviewsLayout == 'grid' ){
                control = <RangeControl
                    beforeIcon="arrow-left-alt2"
                    afterIcon="arrow-right-alt2"
                    label= 'Grid Columns'
                    value={ columns }
                    onChange={ gridColumns => props.setAttributes( { gridColumns } ) }
                    min={ 1 }
                    max={ 6 }
                />
            }
            return control;
        }

        //
        const ReviewsCountControl = <RangeControl
                                        beforeIcon="arrow-left-alt2"
                                        afterIcon="arrow-right-alt2"
                                        label= 'Reviews Count'
                                        value={ count }
                                        onChange={ reviewsCount => props.setAttributes( { reviewsCount } ) }
                                        min={ 1 }
                                        max={ 10 }
                                    />

        // Append the grid class name to the reviews wrapper if the user selected the grid layout.
        function getWrapperLayoutClass( reviewsLayout, gridColumns ){

            var className = '';

            if( reviewsLayout == 'grid' ){

                className ='ezrwp-grid ezrwp-grid-' + gridColumns;
            }

            return className;
        }

        // Assemble the review placeholders.
        function getReviewPlaceholders( reviewsLayout, gridColumns, reviewsCount ){

            var reviews = [];
            var layout = reviewsLayout;
            var columns = gridColumns;
            var count = reviewsCount;

            for( var i = 1; i <= count; i++){
                
                reviews.push(
                    <div className="col ezrwp-col">
                        <ul className="ezrwp-placeholder-text blockquote-placeholder">
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                        <div className="ezrwp-stars ezrwp-stars-5"></div>
                        <div className="ezrwp-date">Zillow Review</div>
                        <div className="ezrwp-reviewer-placeholder">
                            <ul className="ezrwp-placeholder-text">
                                <li class="attribution"><span class="link"></span><span class="text"></span></li>
                                <li></li>
                                <li></li>
                            </ul>
                        </div>
                    </div>
                );

                // Add spacer between rows of columns
                if( (i % columns) == 0 ){

                    reviews.push(

                        <div class="clear"></div>
                    );
                }

                // Add spacer between rows in List layout
                if( layout == 'list' ){

                    reviews.push(

                        <div class="clear"></div>
                    )
                }
            }
            return reviews;
        }
        return([
            
            <InspectorControls>
                <PanelBody>
                    { ScreennameControl( reviewsType ) }
                    { ReviewsControl( apis ) }
                    { LayoutControl }
                    { GridControl( layout ) }
                    { ReviewsCountControl }
                </PanelBody>
            </InspectorControls>,

            <div className={ props.className }>
                <div className={ "ezrwp-wrapper " + getWrapperLayoutClass( layout, columns ) }>
                    <div className="ezrwp-content">
                        { getReviewPlaceholders( layout, columns, count ) }
                    </div>
                </div>
            </div>
            
        ]);
    }
});