import {
	buildCalLink,
	buildRuntimeConfig,
	injectCalStub,
	normalizeBrandColor,
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
	const brandColor = normalizeBrandColor( config.brandColor );
	const prefill = await getPrefillData( config );
	const uiConfig = {
		hideEventTypeDetails: !! config.hideEventDetails,
		theme: config.theme,
		layout: 'month_view',
	};

	if ( brandColor ) {
		uiConfig.cssVarsPerTheme = {
			light: { 'cal-brand': brandColor },
			dark: { 'cal-brand': brandColor },
		};
	}

	Cal( 'init', instanceId, { origin: 'https://cal.id' } );

	Cal.ns[ instanceId ]( 'ui', uiConfig );

	const runtimeConfig = buildRuntimeConfig( config );
	const calLink = buildCalLink( config.eventPath, config, prefill );

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
		trigger.dataset.calConfig = JSON.stringify( runtimeConfig );
		return;
	}

	if ( config.layout === 'floating' ) {
		Cal.ns[ instanceId ]( 'floatingButton', {
			calLink,
			calOrigin: 'https://cal.id',
			config: {
				layout: 'month_view',
			},
			buttonText: config.buttonText || 'Book now',
			hideButtonIcon: false,
			buttonPosition: 'bottom-right',
			buttonColor: brandColor,
			buttonTextColor: '',
		} );
	}
}

async function getPrefillData( config ) {
	if ( ! config.prefillEnabled || ! config.prefillEndpoint ) {
		return undefined;
	}

	try {
		const response = await fetch( config.prefillEndpoint, {
			credentials: 'same-origin',
			headers: {
				Accept: 'application/json',
				...( config.restNonce ? { 'X-WP-Nonce': config.restNonce } : {} ),
			},
		} );

		if ( ! response.ok ) {
			return undefined;
		}

		const payload = await response.json();
		const name = typeof payload?.name === 'string' ? payload.name.trim() : '';
		const email = typeof payload?.email === 'string' ? payload.email.trim() : '';

		if ( ! name && ! email ) {
			return undefined;
		}

		return {
			name: name || undefined,
			email: email || undefined,
		};
	} catch ( error ) {
		return undefined;
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
