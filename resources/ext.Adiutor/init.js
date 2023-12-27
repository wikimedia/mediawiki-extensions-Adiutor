( function () {
	const Vue = require( 'vue' );
	const adiutorSettings = require( './components/adiutorSettings.vue' );
	const requestPageProtection = require( './components/requestPageProtection.vue' );
	const requestPageMove = require( './components/requestPageMove.vue' );
	const articleTagging = require( './components/articleTagging.vue' );
	const createSpeedyDeletion = require( './components/createSpeedyDeletion.vue' );
	const deletionPropose = require( './components/deletionPropose.vue' );

	Vue.configureCompat( {
		MODE: 3
	} );

const csdConfiguration = mw.config.get( 'AdiutorCreateSpeedyDeletion' );
const rppConfiguration = mw.config.get( 'AdiutorRequestPageProtection' );
const rpmConfiguration = mw.config.get( 'AdiutorRequestPageMove' );
const dprConfiguration = mw.config.get( 'AdiutorDeletionPropose' );
const tagConfiguration = mw.config.get( 'AdiutorArticleTagging' );
const userGroups = mw.config.get( 'wgUserGroups' );
const isSpecialPage = mw.config.get( 'wgCanonicalSpecialPageName' );
const isMainPage = mw.config.get( 'wgIsMainPage' );

const portletLinks = [];

/**
 * Adds a portlet link based on the provided configuration and permission.
 *
 * @param {Object} config - The configuration object.
 * @param {string} permission - The permission required to display the link.
 * @param {string} linkId - The ID of the link.
 * @param {string} linkLabel - The label of the link.
 * @param {string} linkKey - The key of the link.
 */

function addPortletLink( config, permission, linkId, linkLabel, linkKey ) {
	if ( config && config.moduleEnabled ) {
		if ( !config.testMode || ( userGroups && userGroups.indexOf( permission ) !== -1 ) ) {
			// Messages that can be used here:
			// * adiutor-csd-header-title
			// * adiutor-csd-header-title-action-label
			portletLinks.push( {
				id: linkId,
				label: mw.msg( linkLabel ),
				action: mw.msg( linkLabel ),
				key: linkKey,
				namespaces: config.namespaces || []
			} );
		}
	}
}

if ( !isSpecialPage && !isMainPage ) {
	addPortletLink( csdConfiguration, 'interface-admin', 't-request-speedy-deletion', 'adiutor-request-speedy-deletion', 'createSpeedyDeletion' );
	addPortletLink( rppConfiguration, 'interface-admin', 't-request-protection', 'adiutor-request-protection', 'requestPageProtection' );
	addPortletLink( rpmConfiguration, 'interface-admin', 't-request-page-move', 'adiutor-request-page-move', 'requestPageMove' );
	addPortletLink( dprConfiguration, 'interface-admin', 't-propose-deletion', 'adiutor-propose-deletion', 'deletionPropose' );
	addPortletLink( tagConfiguration, 'interface-admin', 't-tag-article', 'adiutor-tag-article', 'articleTagging' );
}

const currentNamespace = mw.config.get( 'wgNamespaceNumber' );

portletLinks.forEach( ( link ) => {
	const linkNamespaces = link.namespaces.map( ( nsObj ) => parseInt( nsObj.value, 10 ) );
	if ( linkNamespaces.indexOf( currentNamespace ) !== -1 ) {
		mw.util.addPortletLink( 'p-cactions', '#', link.label, link.id, link.action, link.key );
	}
} );

const speedyDeletionRequestLink = document.querySelector( '#t-request-speedy-deletion' );
		const proposeDeletionLink = document.querySelector( '#t-propose-deletion' );
		const requestProtectionLink = document.querySelector( '#t-request-protection' );
		const requestPageMoveLink = document.querySelector( '#t-request-page-move' );
		const tagArticleLink = document.querySelector( '#t-tag-article' );
		if ( speedyDeletionRequestLink ) {
			speedyDeletionRequestLink.addEventListener( 'click', () => {
				Vue.createMwApp( createSpeedyDeletion ).mount( '#mw-teleport-target' );
			} );
		}
		if ( proposeDeletionLink ) {
			proposeDeletionLink.addEventListener( 'click', () => {
				Vue.createMwApp( deletionPropose ).mount( '#mw-teleport-target' );
			} );
		}
		if ( requestProtectionLink ) {
			requestProtectionLink.addEventListener( 'click', () => {
				Vue.createMwApp( requestPageProtection ).mount( '#mw-teleport-target' );
			} );
		}
		if ( requestPageMoveLink ) {
			requestPageMoveLink.addEventListener( 'click', () => {

				Vue.createMwApp( requestPageMove ).mount( '#mw-teleport-target' );
			} );
		}
		if ( tagArticleLink ) {
			tagArticleLink.addEventListener( 'click', () => {

				Vue.createMwApp( articleTagging ).mount( '#mw-teleport-target' );
			} );
		}
	Vue.createMwApp( adiutorSettings ).mount( '#adiutor-settings-teleport-target' );
}() );
