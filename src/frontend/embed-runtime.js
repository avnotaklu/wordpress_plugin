import {
	buildCalLink,
	buildRuntimeConfig,
	injectCalStub,
} from '../shared/cal-embed';

async function initInstance( root ) {
	const configScript = root.querySelector( '.cal-id-embed__config' );
	if ( ! configScript ) {
		return;
	}

	let config;
	try {
		config = JSON.parse( configScript.textContent || '{}' );
	} catch ( error ) {
		root.classList.add( 'cal-id-embed--error' );
		return;
	}

	const instanceId = root.dataset.instanceId;

	if ( ! instanceId || ! config.eventPath ) {
		return;
	}

	const Cal = injectCalStub();
	Cal( 'init', instanceId, { origin: 'https://cal.id' } );

	Cal.ns[ instanceId ]( 'ui', {
		cssVarsPerTheme: {
			light: { 'cal-brand': config.brandColor || '' },
			dark: { 'cal-brand': config.brandColor || '' },
		},
		hideEventTypeDetails: !! config.hideEventDetails,
		theme: config.theme,
		layout: 'month_view',
	} );

	const calLink = buildCalLink( config.eventPath, config );

	const container = root.querySelector( '.cal-id-embed__container' );
	if ( config.layout === 'inline' && container ) {
		container.id = container.id || `${ instanceId }-container`;

		Cal.ns[ instanceId ]( 'inline', {
			elementOrSelector: `#${ container.id }`,
			calLink,
			config: { layout: 'month_view' },
		} );
		return;
	}

	const trigger = root.querySelector( '.cal-id-embed__trigger' );
	if ( config.layout === 'modal' && trigger ) {
		trigger.dataset.calLink = calLink;
		trigger.dataset.calNamespace = instanceId;
		trigger.dataset.calConfig = JSON.stringify(
			buildRuntimeConfig( config )
		);
		return;
	}

	if ( config.layout === 'floating' ) {
		Cal.ns[ instanceId ]( 'floatingButton', {
			calLink,
			calOrigin: 'https://cal.id',
			config: { layout: 'month_view' },
			buttonText: config.buttonText || 'Book now',
			hideButtonIcon: false,
			buttonPosition: 'bottom-right',
			buttonColor: config.brandColor || '',
			buttonTextColor: '',
		} );
	}
}

document.addEventListener( 'DOMContentLoaded', () => {
	document
		.querySelectorAll( '.cal-id-embed[data-instance-id]' )
		.forEach( ( root ) => {
			initInstance( root ).catch( () => {
				root.classList.add( 'cal-id-embed--error' );
			} );
		} );
} );
