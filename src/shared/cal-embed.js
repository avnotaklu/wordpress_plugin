export function buildCalLink( eventPath, config, prefill = undefined ) {
	const searchParams = new URLSearchParams();
	const params = {
		utm_source: config.utmSource,
		utm_medium: config.utmMedium,
		utm_campaign: config.utmCampaign,
		utm_content: config.utmContent,
		utm_term: config.utmTerm,
	};

	Object.entries( params ).forEach( ( [ key, value ] ) => {
		if ( value ) {
			searchParams.set( key, value );
		}
	} );

	if ( prefill?.name ) {
		searchParams.set( 'name', prefill.name );
	}

	if ( prefill?.email ) {
		searchParams.set( 'email', prefill.email );
	}

	const queryString = searchParams.toString();
	return queryString ? `${ eventPath }?${ queryString }` : eventPath;
}

export function injectCalStub( targetWindow = window ) {
	if ( targetWindow.Cal ) {
		return targetWindow.Cal;
	}

	( function ( C, A, L ) {
		const p = function ( a, ar ) {
			a.q.push( ar );
		};
		const d = C.document;
		C.Cal =
			C.Cal ||
			function () {
				const cal = C.Cal;
				const ar = arguments;
				if ( ! cal.loaded ) {
					cal.ns = {};
					cal.q = cal.q || [];
					d.head.appendChild( d.createElement( 'script' ) ).src = A;
					cal.loaded = true;
				}
				if ( ar[ 0 ] === L ) {
					const api = function () {
						p( api, arguments );
					};
					const namespace = ar[ 1 ];
					api.q = api.q || [];
					if ( typeof namespace === 'string' ) {
						cal.ns[ namespace ] = cal.ns[ namespace ] || api;
						p( cal.ns[ namespace ], ar );
						p( cal, [ 'initNamespace', namespace ] );
					} else {
						p( cal, ar );
					}
					return;
				}
				p( cal, ar );
			};
	} )( targetWindow, 'https://cal.id/embed-link/embed.js', 'init' );

	return targetWindow.Cal;
}

export function buildRuntimeConfig( config ) {
	const brandColor = normalizeBrandColor( config.brandColor );
	const cssVarsPerTheme = {};

	if ( brandColor ) {
		cssVarsPerTheme.light = { 'cal-brand': brandColor };
		cssVarsPerTheme.dark = { 'cal-brand': brandColor };
	}

	return {
		calLink: buildCalLink( config.eventPath, config ),
		buttonText: config.buttonText || 'Book now',
		buttonColor: brandColor,
		buttonTextColor: '',
		hideEventDetails: !! config.hideEventDetails,
		prefillEnabled: !! config.prefillEnabled,
		prefillEndpoint: config.prefillEndpoint || '',
		layout: 'month_view',
		cssVarsPerTheme,
	};
}

export function normalizeBrandColor( brandColor ) {
	const value = String( brandColor || '' ).trim();
	return value ? value : undefined;
}
