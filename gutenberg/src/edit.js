import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	Disabled,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

const settings = window.ezrwpBlockSettings || {};
const defaults = settings.defaults || {};

const LAYOUT_LABELS = {
	list: __( 'List', 'easy-zillow-reviews' ),
	grid: __( 'Grid', 'easy-zillow-reviews' ),
	carousel: __( 'Carousel', 'easy-zillow-reviews' ),
};

export default function Edit( { attributes, setAttributes } ) {
	const {
		reviewsType,
		reviewsLayout,
		gridColumns,
		reviewsCount,
		wordLimit,
		screenname,
		includeTeam,
	} = attributes;

	const types = settings.types || [
		{ value: 'professional', label: __( 'Professional', 'easy-zillow-reviews' ) },
	];
	const type = reviewsType || 'professional';
	const layout = reviewsLayout || defaults.layout || 'list';
	const layouts = ( settings.layouts || [ 'list', 'grid' ] ).map(
		( value ) => ( { value, label: LAYOUT_LABELS[ value ] || value } )
	);
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Reviews', 'easy-zillow-reviews' ) }>
					{ types.length > 1 && (
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Review type', 'easy-zillow-reviews' ) }
							value={ type }
							options={ types }
							onChange={ ( value ) =>
								setAttributes( { reviewsType: value } )
							}
						/>
					) }
					{ type === 'professional' && (
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Zillow screenname', 'easy-zillow-reviews' ) }
							value={ screenname || '' }
							placeholder={ defaults.screenname }
							help={ __(
								'Leave empty to use the screenname from the plugin settings.',
								'easy-zillow-reviews'
							) }
							onChange={ ( value ) =>
								setAttributes( { screenname: value } )
							}
						/>
					) }
					{ type === 'professional' && settings.isPremium && (
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Include team members’ reviews', 'easy-zillow-reviews' ) }
							checked={
								typeof includeTeam === 'boolean'
									? includeTeam
									: !! defaults.includeTeam
							}
							onChange={ ( value ) =>
								setAttributes( { includeTeam: value } )
							}
						/>
					) }
					<RangeControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Number of reviews', 'easy-zillow-reviews' ) }
						value={ reviewsCount }
						initialPosition={ defaults.count || 3 }
						onChange={ ( value ) =>
							setAttributes( { reviewsCount: value } )
						}
						min={ 1 }
						max={ 10 }
						allowReset
					/>
					<RangeControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Excerpt length (words)', 'easy-zillow-reviews' ) }
						value={ wordLimit }
						initialPosition={ defaults.wordLimit || 750 }
						onChange={ ( value ) =>
							setAttributes( { wordLimit: value } )
						}
						min={ 20 }
						max={ 750 }
						allowReset
					/>
				</PanelBody>
				<PanelBody title={ __( 'Layout', 'easy-zillow-reviews' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Layout', 'easy-zillow-reviews' ) }
						value={ layout }
						options={ layouts }
						onChange={ ( value ) =>
							setAttributes( { reviewsLayout: value } )
						}
					/>
					{ layout !== 'list' && (
						<RangeControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={
								layout === 'carousel'
									? __( 'Reviews visible at once', 'easy-zillow-reviews' )
									: __( 'Columns', 'easy-zillow-reviews' )
							}
							value={ gridColumns }
							initialPosition={ defaults.columns || 3 }
							onChange={ ( value ) =>
								setAttributes( { gridColumns: value } )
							}
							min={ 1 }
							max={ 6 }
							allowReset
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block="boltonstudios/easy-zillow-reviews"
						attributes={ attributes }
						skipBlockSupportAttributes
					/>
				</Disabled>
			</div>
		</>
	);
}
