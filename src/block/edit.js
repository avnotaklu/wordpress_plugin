import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	useEffect,
	useLayoutEffect,
	useRef,
	useState,
} from '@wordpress/element';
import {
	PanelBody,
	TextControl,
	SelectControl,
	RangeControl,
	ToggleControl,
	Notice,
} from '@wordpress/components';
import {
	buildCalLink,
	buildRuntimeConfig,
	injectCalStub,
} from '../shared/cal-embed';

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

function normalizePreviewEventPath( eventPath ) {
	const value = String( eventPath || '' ).trim();
	const match = value.match( /^https:\/\/cal\.id\/([^?#]+)$/i );

	return ( match ? match[ 1 ] : value ).replace( /^\/+/, '' );
}

function LivePreview( { attributes } ) {
	const containerRef = useRef( null );
	const instanceIdRef = useRef(
		`cal-id-preview-${ Math.random().toString( 36 ).slice( 2 ) }`
	);
	const {
		hideEventDetails,
		theme,
		utmCampaign,
		utmContent,
		utmMedium,
		utmSource,
		utmTerm,
	} = attributes;
	const eventPath = normalizePreviewEventPath( attributes.eventPath );

	useLayoutEffect( () => {
		const container = containerRef.current;
		if ( ! container || ! eventPath ) {
			return;
		}

		container.innerHTML = '';
		container.id = container.id || `${ instanceIdRef.current }-container`;

		const targetWindow = container.ownerDocument?.defaultView || window;
		const Cal = injectCalStub( targetWindow );
		const initConfig = new targetWindow.Object();
		initConfig.origin = 'https://cal.id';
		Cal( 'init', instanceIdRef.current, initConfig );

		const uiConfig = new targetWindow.Object();
		uiConfig.theme = theme;
		uiConfig.hideEventTypeDetails = !! hideEventDetails;
		uiConfig.layout = 'month_view';
		if ( brandColor ) {
			const cssVarsPerTheme = new targetWindow.Object();
			const lightTheme = new targetWindow.Object();
			const darkTheme = new targetWindow.Object();
			lightTheme[ 'cal-brand' ] = brandColor;
			darkTheme[ 'cal-brand' ] = brandColor;
			cssVarsPerTheme.light = lightTheme;
			cssVarsPerTheme.dark = darkTheme;
			uiConfig.cssVarsPerTheme = cssVarsPerTheme;
		}
		Cal.ns[ instanceIdRef.current ]( 'ui', uiConfig );
		const mountTimer = targetWindow.setTimeout( () => {
			if ( ! container.isConnected ) {
				return;
			}

			const target = container.ownerDocument?.getElementById( container.id );
			if ( ! target ) {
				return;
			}

			const inlineConfig = new targetWindow.Object();
			inlineConfig.layout = 'month_view';

			const inlineArgs = new targetWindow.Object();
			inlineArgs.elementOrSelector = `#${ target.id }`;
			inlineArgs.calLink = buildCalLink( eventPath, {
				utmCampaign,
				utmContent,
				utmMedium,
				utmSource,
				utmTerm,
			} );
			inlineArgs.config = inlineConfig;

			Cal.ns[ instanceIdRef.current ]( 'inline', inlineArgs );
		}, 0 );

		return () => {
			targetWindow.clearTimeout( mountTimer );
			container.innerHTML = '';
		};
	}, [
		attributes.embedHeight,
		hideEventDetails,
		attributes.layout,
		theme,
		utmCampaign,
		utmContent,
		utmMedium,
		utmSource,
		utmTerm,
		eventPath,
	] );

	return (
		<div className="cal-id__preview-frame cal-id__preview-frame--live">
			<div
				ref={ containerRef }
				className="cal-id__preview-body"
				style={ { minHeight: `${ attributes.embedHeight || 600 }px` } }
			/>
			<div className="cal-id__preview-shield" aria-hidden="true" />
		</div>
	);
}

function StaticPreview( { attributes } ) {
	if ( attributes.layout === 'modal' ) {
		return (
			<button type="button" className="cal-id__trigger">
				{ attributes.buttonText || __( 'Book now', 'cal-id' ) }
			</button>
		);
	}

	if ( attributes.layout === 'floating' ) {
		return (
			<div className="cal-id__static-preview cal-id__static-preview--floating">
				<div className="cal-id__static-preview-content">
					{ __(
						'Floating button is displayed at bottom',
						'cal-id'
					) }
				</div>
			</div>
		);
	}

	return (
		<div className="cal-id__static-preview cal-id__static-preview--inline">
			<div className="cal-id__static-preview-content">
				{ __(
					'Cal ID booking embed — visible on the published page',
					'cal-id'
				) }
			</div>
		</div>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const [ showLivePreview, setShowLivePreview ] = useState( false );
	const blockProps = useBlockProps( {
		className: `layout-${ attributes.layout || 'inline' } theme-${
			attributes.theme || 'auto'
		}`,
	} );

	const hasValidPreviewPath =
		attributes.eventPath &&
		! attributes.eventPath.includes( 'javascript:' );
	const showPreview =
		attributes.layout === 'inline' &&
		showLivePreview &&
		hasValidPreviewPath;

	useEffect( () => {
		if ( attributes.layout !== 'inline' && showLivePreview ) {
			setShowLivePreview( false );
		}
	}, [ attributes.layout, showLivePreview ] );

	let preview;

	if ( showPreview ) {
		preview = <LivePreview attributes={ attributes } />;
	} else if ( hasValidPreviewPath ) {
		preview = <StaticPreview attributes={ attributes } />;
	} else {
		preview = (
			<div className="cal-id__placeholder">
				{ attributes.eventPath
					? __(
							'Block configured. Preview will be rendered on the frontend.',
							'cal-id'
					  )
					: __( 'Enter an event path to preview.', 'cal-id' ) }
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Event', 'cal-id' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Event Path', 'cal-id' ) }
						help={ __(
							'Use owner/event, team/owner/event, or a hosted https://cal.id URL.',
							'cal-id'
						) }
						value={ attributes.eventPath }
						onChange={ ( value ) =>
							setAttributes( { eventPath: value } )
						}
					/>
					{ attributes.eventPath &&
					attributes.eventPath.includes( 'javascript:' ) ? (
						<Notice status="error" isDismissible={ false }>
							{ __(
								'Unable to preview embed - check event path.',
								'cal-id'
							) }
						</Notice>
					) : null }
				</PanelBody>

				<PanelBody title={ __( 'Layout', 'cal-id' ) }>
					<SelectControl
						label={ __( 'Layout', 'cal-id' ) }
						value={ attributes.layout }
						options={ allowedLayouts }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
					<RangeControl
						label={ __( 'Embed Height', 'cal-id' ) }
						value={ attributes.embedHeight }
						min={ 320 }
						max={ 1600 }
						step={ 10 }
						onChange={ ( value ) =>
							setAttributes( { embedHeight: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Hide Event Details', 'cal-id' ) }
						checked={ !! attributes.hideEventDetails }
						onChange={ ( value ) =>
							setAttributes( { hideEventDetails: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Appearance', 'cal-id' ) }>
					<SelectControl
						label={ __( 'Theme', 'cal-id' ) }
						value={ attributes.theme }
						options={ allowedThemes }
						onChange={ ( value ) =>
							setAttributes( { theme: value } )
						}
					/>
					<TextControl
						label={ __( 'Brand Color', 'cal-id' ) }
						value={ attributes.brandColor }
						onChange={ ( value ) =>
							setAttributes( { brandColor: value } )
						}
					/>
					<TextControl
						label={ __( 'Button Text', 'cal-id' ) }
						value={ attributes.buttonText }
						onChange={ ( value ) =>
							setAttributes( { buttonText: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Prefill', 'cal-id' ) }>
					<ToggleControl
						label={ __(
							'Enable logged-in prefill',
							'cal-id'
						) }
						checked={ !! attributes.prefillEnabled }
						onChange={ ( value ) =>
							setAttributes( { prefillEnabled: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Tracking', 'cal-id' ) }>
					<TextControl
						label="UTM Source"
						value={ attributes.utmSource }
						onChange={ ( value ) =>
							setAttributes( { utmSource: value } )
						}
					/>
					<TextControl
						label="UTM Medium"
						value={ attributes.utmMedium }
						onChange={ ( value ) =>
							setAttributes( { utmMedium: value } )
						}
					/>
					<TextControl
						label="UTM Campaign"
						value={ attributes.utmCampaign }
						onChange={ ( value ) =>
							setAttributes( { utmCampaign: value } )
						}
					/>
					<TextControl
						label="UTM Content"
						value={ attributes.utmContent }
						onChange={ ( value ) =>
							setAttributes( { utmContent: value } )
						}
					/>
					<TextControl
						label="UTM Term"
						value={ attributes.utmTerm }
						onChange={ ( value ) =>
							setAttributes( { utmTerm: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Preview', 'cal-id' ) }>
					<ToggleControl
						label={ __( 'Live preview', 'cal-id' ) }
						checked={ showLivePreview }
						disabled={ attributes.layout !== 'inline' }
						onChange={ setShowLivePreview }
					/>
					{ attributes.layout !== 'inline' ? (
						<div className="cal-id__preview-help">
							{ __(
								'Live preview is only available for inline embeds.',
								'cal-id'
							) }
						</div>
					) : null }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>{ preview }</div>
		</>
	);
}
