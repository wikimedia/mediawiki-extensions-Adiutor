( function () {
	const Vue = require( 'vue' );
	const components = {
		adiutorSettings: require( './components/adiutorSettings.vue' ),
		requestPageProtection: require( './components/requestPageProtection.vue' ),
		requestPageMove: require( './components/requestPageMove.vue' ),
		articleTagging: require( './components/articleTagging.vue' ),
		createSpeedyDeletion: require( './components/createSpeedyDeletion.vue' ),
		deletionPropose: require( './components/deletionPropose.vue' ),
		reportRevision: require( './components/reportRevision.vue' )
	};

	const configurations = {
		csd: mw.config.get( 'wgAdiutorCreateSpeedyDeletion' ),
		rpp: mw.config.get( 'wgAdiutorRequestPageProtection' ),
		rpm: mw.config.get( 'wgAdiutorRequestPageMove' ),
		dpr: mw.config.get( 'wgAdiutorDeletionPropose' ),
		tag: mw.config.get( 'wgAdiutorArticleTagging' ),
		rev: mw.config.get( 'wgAdiutorReportRevision' )
	};

	const userGroups = mw.config.get( 'wgUserGroups' );
	const isSpecialPage = mw.config.get( 'wgCanonicalSpecialPageName' );
	const isMainPage = mw.config.get( 'wgIsMainPage' );
	const portletLinks = [];

	/**
	 * Adds a portlet link.
	 *
	 * @param {Object} config - The configuration object.
	 * @param {string} linkId - The ID of the link.
	 * @param {string} linkLabel - The label of the link.
	 * @param {string} accessKey - The key of the link.
	 */

	function addPortletLink( config, linkId, linkLabel, accessKey ) {
		if ( config && config.moduleEnabled ) {
			if ( !config.testMode || ( userGroups && userGroups.includes( 'interface-admin' ) ) ) {
				// Messages that can be used here:
				// * adiutor-request-speedy-deletion
				// * adiutor-request-protection
				// * adiutor-request-page-move
				// * adiutor-propose-deletion
				// * adiutor-tag-article
				portletLinks.push( {
					id: linkId,
					label: mw.msg( linkLabel ),
					action: mw.msg( linkLabel ),
					key: accessKey,
					namespaces: config.namespaces || []
				} );
			}
		}
	}

	if ( !isSpecialPage && !isMainPage ) {
		if ( mw.user.options.get( 'adiutor-csd-enable' ) ) {
			addPortletLink( configurations.csd, 't-request-speedy-deletion', 'adiutor-request-speedy-deletion', '1' );
		}
		if ( mw.user.options.get( 'adiutor-rpp-enable' ) ) {
			addPortletLink( configurations.rpp, 't-request-protection', 'adiutor-request-protection', '2' );
		}
		if ( mw.user.options.get( 'adiutor-rpm-enable' ) ) {
			addPortletLink( configurations.rpm, 't-request-page-move', 'adiutor-request-page-move', '3' );
		}
		if ( mw.user.options.get( 'adiutor-prod-enable' ) ) {
			addPortletLink( configurations.dpr, 't-propose-deletion', 'adiutor-propose-deletion', '4' );
		}
		if ( mw.user.options.get( 'adiutor-tag-enable' ) ) {
			addPortletLink( configurations.tag, 't-tag-article', 'adiutor-tag-article', '5' );
		}
		if ( mw.user.options.get( 'adiutor-rev-enable' ) ) {
			if ( /[?&](?:|diff)=/.test( window.location.href ) ) {
				addPortletLink( configurations.rev, 't-report-revision', 'adiutor-report-revision', '6' );
			}
		}
	}

	const currentNamespace = mw.config.get( 'wgNamespaceNumber' );

	portletLinks.forEach( ( link ) => {
		const linkNamespaces = link.namespaces.map( ( nsObj ) => parseInt( nsObj.value, 10 ) );
		if ( linkNamespaces.indexOf( currentNamespace ) !== -1 ) {
			mw.util.addPortletLink( 'p-cactions', '#', link.label, link.id, link.action, link.key );
		}
	} );

	function attachEventListener( link, component ) {
		const element = document.querySelector( link );
		if ( element ) {
			element.addEventListener( 'click', () => {
				Vue.createMwApp( components[ component ] ).mount( '#mw-teleport-target' );
			} );
		}
	}

	attachEventListener( '#t-request-speedy-deletion', 'createSpeedyDeletion' );
	attachEventListener( '#t-propose-deletion', 'deletionPropose' );
	attachEventListener( '#t-request-protection', 'requestPageProtection' );
	attachEventListener( '#t-request-page-move', 'requestPageMove' );
	attachEventListener( '#t-tag-article', 'articleTagging' );
	attachEventListener( '#t-report-revision', 'reportRevision' );

	Vue.createMwApp( components.adiutorSettings ).mount( '#adiutor-settings-teleport-target' );
}() );
