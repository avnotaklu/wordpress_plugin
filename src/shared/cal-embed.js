export function buildCalLink( eventPath, config ) {
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

	const queryString = searchParams.toString();
	return queryString ? `${ eventPath }?${ queryString }` : eventPath;
}

export function injectCalStub() {
	if ( window.Cal ) {
		return window.Cal;
	}

	( function ( C, A, L ) {
		let p = function ( a, ar ) {
			a.q.push( ar );
		};
		let d = C.document;
		C.Cal =
			C.Cal ||
			function () {
				let cal = C.Cal;
				let ar = arguments;
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
	} )( window, 'https://cal.id/embed-link/embed.js', 'init' );

	return window.Cal;
}

export function buildRuntimeConfig( config ) {
	return {
		calLink: buildCalLink( config.eventPath, config ),
		buttonText: config.buttonText || 'Book now',
		buttonColor: config.brandColor || '',
		buttonTextColor: '',
		hideEventDetails: !! config.hideEventDetails,
		prefillEnabled: !! config.prefillEnabled,
		layout: 'month_view',
	};
}
