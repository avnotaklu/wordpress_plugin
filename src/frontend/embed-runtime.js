import { buildCalLink, buildRuntimeConfig } from '../shared/cal-embed';

const CAL_STUB_SRC = 'https://cal.id/embed-link/embed.js';

function loadCalStub() {
	return new Promise( ( resolve, reject ) => {
		if ( window.Cal ) {
			resolve( window.Cal );
			return;
		}

		const existing = document.querySelector( 'script[data-cal-embed-stub]' );
		if ( existing ) {
			existing.addEventListener( 'load', () => resolve( window.Cal ) );
			existing.addEventListener( 'error', reject );
			return;
		}

		const script = document.createElement( 'script' );
		script.src = CAL_STUB_SRC;
		script.async = true;
		script.dataset.calEmbedStub = '1';
		script.addEventListener( 'load', () => resolve( window.Cal ) );
		script.addEventListener( 'error', reject );
		document.head.appendChild( script );
	} );
}

async function initInstance( root ) {
	const configScript = root.querySelector( '.cal-id-event-embed__config' );
	if ( ! configScript ) {
		return;
	}

	let config;
	try {
		config = JSON.parse( configScript.textContent || '{}' );
	} catch ( error ) {
		root.classList.add( 'cal-id-event-embed--error' );
		return;
	}

	const instanceId = root.dataset.instanceId;
	const container = root.querySelector( '.cal-id-event-embed__container' );
	const trigger = root.querySelector( '.cal-id-event-embed__trigger' );

	if ( ! instanceId || ! config.eventPath ) {
		return;
	}

	const Cal = await loadCalStub();
	Cal( 'init', instanceId, { origin: 'https://cal.id' } );

	const runtimeConfig = buildRuntimeConfig( config );
	const calLink = buildCalLink( config.eventPath, config );

	Cal.ns[ instanceId ]( 'ui', {
		cssVarsPerTheme: {
			light: { 'cal-brand': config.brandColor || '' },
			dark: { 'cal-brand': config.brandColor || '' },
		},
		hideEventTypeDetails: !! config.hideEventDetails,
		layout: 'month_view',
	} );

	if ( config.layout === 'inline' && container ) {
		Cal.ns[ instanceId ]( 'inline', {
			elementOrSelector: `#${ container.id || root.id }`,
			calLink,
			config: { layout: 'month_view' },
		} );
		return;
	}

	if ( config.layout === 'modal' && trigger ) {
		trigger.dataset.calLink = calLink;
		trigger.dataset.calNamespace = instanceId;
		trigger.dataset.calConfig = JSON.stringify( runtimeConfig );
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
	document.querySelectorAll( '.cal-id-event-embed[data-instance-id]' ).forEach( ( root ) => {
		initInstance( root ).catch( () => {
			root.classList.add( 'cal-id-event-embed--error' );
		} );
	} );
} );
