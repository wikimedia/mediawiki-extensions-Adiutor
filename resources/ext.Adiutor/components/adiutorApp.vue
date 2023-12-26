<template>
	<div>
		<component :is="activeComponent" :key="activeComponentKey"></component>
	</div>
</template>

<script>
// Import components
const requestPageProtection = require( './requestPageProtection.vue' );
const requestPageMove = require( './requestPageMove.vue' );
const articleTagging = require( './articleTagging.vue' );
const createSpeedyDeletion = require( './createSpeedyDeletion.vue' );
const deletionPropose = require( './deletionPropose.vue' );

// Configuration objects
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

// @vue/component
module.exports = {
	name: 'AdiutorInterfaceLoader',
	components: {
		requestPageProtection,
		requestPageMove,
		createSpeedyDeletion,
		deletionPropose,
		articleTagging
	},
	data() {
		return {
			activeComponent: null,
			activeComponentKey: 0
		};
	},
	methods: {
		showComponent( componentName ) {
			if ( componentName === this.activeComponent ) {
				this.activeComponentKey++;
			} else {
				this.activeComponent = componentName;
			}
		}
	},
	created() {
		const speedyDeletionRequestLink = document.querySelector( '#t-request-speedy-deletion' );
		const proposeDeletionLink = document.querySelector( '#t-propose-deletion' );
		const requestProtectionLink = document.querySelector( '#t-request-protection' );
		const requestPageMoveLink = document.querySelector( '#t-request-page-move' );
		const tagArticleLink = document.querySelector( '#t-tag-article' );
		if ( speedyDeletionRequestLink ) {
			speedyDeletionRequestLink.addEventListener( 'click', () => {
				this.showComponent( 'createSpeedyDeletion' );
			} );
		}
		if ( proposeDeletionLink ) {
			proposeDeletionLink.addEventListener( 'click', () => {
				this.showComponent( 'deletionPropose' );
			} );
		}
		if ( requestProtectionLink ) {
			requestProtectionLink.addEventListener( 'click', () => {
				this.showComponent( 'requestPageProtection' );
			} );
		}
		if ( requestPageMoveLink ) {
			requestPageMoveLink.addEventListener( 'click', () => {
				this.showComponent( 'requestPageMove' );
			} );
		}
		if ( tagArticleLink ) {
			tagArticleLink.addEventListener( 'click', () => {
				this.showComponent( 'articleTagging' );
			} );
		}
	}
};
</script>
