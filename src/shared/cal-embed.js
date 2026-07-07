export function buildCalLink( eventPath, config ) {
	const url = new URL( `https://cal.id/${ eventPath }` );
	const params = {
		utm_source: config.utmSource,
		utm_medium: config.utmMedium,
		utm_campaign: config.utmCampaign,
		utm_content: config.utmContent,
		utm_term: config.utmTerm,
	};

	Object.entries( params ).forEach( ( [ key, value ] ) => {
		if ( value ) {
			url.searchParams.set( key, value );
		}
	} );

	return url.toString();
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
