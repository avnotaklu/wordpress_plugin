import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import {
	PanelBody,
	TextControl,
	SelectControl,
	RangeControl,
	ToggleControl,
	Notice,
} from '@wordpress/components';

const allowedLayouts = [
	{ label: 'Inline', value: 'inline' },
	{ label: 'Modal', value: 'modal' },
	{ label: 'Floating', value: 'floating' },
];

const allowedThemes = [
	{ label: 'Auto', value: 'auto' },
	{ label: 'Light', value: 'light' },
	{ label: 'Dark', value: 'dark' },
];

export default function Edit( { attributes, setAttributes } ) {
	const [ showLivePreview, setShowLivePreview ] = useState( false );
	const blockProps = useBlockProps( {
		className: `layout-${ attributes.layout || 'inline' } theme-${ attributes.theme || 'auto' }`,
	} );

	const showPreview = attributes.layout === 'inline' && showLivePreview;

	const preview = showPreview ? (
		<div className="cal-id-event-embed__preview-frame">
			<div className="cal-id-event-embed__preview-header">
				{ __( 'Live preview placeholder', 'cal-id-event-embed' ) }
			</div>
			<div className="cal-id-event-embed__preview-body">
				{ attributes.eventPath ? attributes.eventPath : __( 'Enter an event path to preview.', 'cal-id-event-embed' ) }
			</div>
		</div>
	) : (
		<div className="cal-id-event-embed__placeholder">
			{ attributes.eventPath ? __( 'Block configured. Preview will be rendered on the frontend.', 'cal-id-event-embed' ) : __( 'Enter an event path to preview.', 'cal-id-event-embed' ) }
		</div>
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Event', 'cal-id-event-embed' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Event Path', 'cal-id-event-embed' ) }
						help={ __( 'Use owner/event, team/owner/event, or a hosted https://cal.id URL.', 'cal-id-event-embed' ) }
						value={ attributes.eventPath }
						onChange={ ( value ) => setAttributes( { eventPath: value } ) }
					/>
					{ attributes.eventPath && attributes.eventPath.includes( 'javascript:' ) ? (
						<Notice status="error" isDismissible={ false }>
							{ __( 'Unable to preview embed - check event path.', 'cal-id-event-embed' ) }
						</Notice>
					) : null }
				</PanelBody>

				<PanelBody title={ __( 'Layout', 'cal-id-event-embed' ) }>
					<SelectControl
						label={ __( 'Layout', 'cal-id-event-embed' ) }
						value={ attributes.layout }
						options={ allowedLayouts }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
					<RangeControl
						label={ __( 'Embed Height', 'cal-id-event-embed' ) }
						value={ attributes.embedHeight }
						min={ 320 }
						max={ 1600 }
						step={ 10 }
						onChange={ ( value ) => setAttributes( { embedHeight: value } ) }
					/>
					<ToggleControl
						label={ __( 'Hide Event Details', 'cal-id-event-embed' ) }
						checked={ !! attributes.hideEventDetails }
						onChange={ ( value ) => setAttributes( { hideEventDetails: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Appearance', 'cal-id-event-embed' ) }>
					<SelectControl
						label={ __( 'Theme', 'cal-id-event-embed' ) }
						value={ attributes.theme }
						options={ allowedThemes }
						onChange={ ( value ) => setAttributes( { theme: value } ) }
					/>
					<TextControl
						label={ __( 'Brand Color', 'cal-id-event-embed' ) }
						value={ attributes.brandColor }
						onChange={ ( value ) => setAttributes( { brandColor: value } ) }
					/>
					<TextControl
						label={ __( 'Button Text', 'cal-id-event-embed' ) }
						value={ attributes.buttonText }
						onChange={ ( value ) => setAttributes( { buttonText: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Prefill', 'cal-id-event-embed' ) }>
					<ToggleControl
						label={ __( 'Enable logged-in prefill', 'cal-id-event-embed' ) }
						checked={ !! attributes.prefillEnabled }
						onChange={ ( value ) => setAttributes( { prefillEnabled: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Tracking', 'cal-id-event-embed' ) }>
					<TextControl label="UTM Source" value={ attributes.utmSource } onChange={ ( value ) => setAttributes( { utmSource: value } ) } />
					<TextControl label="UTM Medium" value={ attributes.utmMedium } onChange={ ( value ) => setAttributes( { utmMedium: value } ) } />
					<TextControl label="UTM Campaign" value={ attributes.utmCampaign } onChange={ ( value ) => setAttributes( { utmCampaign: value } ) } />
					<TextControl label="UTM Content" value={ attributes.utmContent } onChange={ ( value ) => setAttributes( { utmContent: value } ) } />
					<TextControl label="UTM Term" value={ attributes.utmTerm } onChange={ ( value ) => setAttributes( { utmTerm: value } ) } />
				</PanelBody>

				<PanelBody title={ __( 'Preview', 'cal-id-event-embed' ) }>
					<ToggleControl
						label={ __( 'Live preview', 'cal-id-event-embed' ) }
						checked={ showLivePreview }
						disabled={ attributes.layout !== 'inline' }
						onChange={ setShowLivePreview }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ preview }
			</div>
		</>
	);
}
