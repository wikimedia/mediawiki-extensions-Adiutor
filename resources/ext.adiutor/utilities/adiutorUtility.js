( function () {
	'use strict';

	const api = new mw.Api();

	/**
	 * Replaces numbered placeholders in the input string with the corresponding
	 * replacements.
	 *
	 * @param {string} input - The input string with placeholders.
	 * @param {Object} replacements - An object containing the replacements for the placeholders.
	 * @return {string} - The input string with the placeholders replaced.
	 */
	function replacePlaceholders( input, replacements ) {
		return input.replace( /\$(\d+)/g, function ( match, group ) {
			const replacement = replacements[ '$' + group ];
			return replacement !== undefined ? replacement : match;
		} );
	}

	/**
	 * Replaces a specific placeholder in the input string with a new value.
	 *
	 * @param {string} input - The input string with placeholders.
	 * @param {string} parameterName - The name of the parameter to replace.
	 * @param {string} newValue - The new value to replace the parameter with.
	 * @return {string} - The input string with the specified parameter replaced.
	 */
	function replaceParameter( input, parameterName, newValue ) {
		// eslint-disable-next-line security/detect-non-literal-regexp
		const regex = new RegExp( '\\$' + parameterName, 'g' );
		if ( input.includes( '$' + parameterName ) ) {
			return input.replace( regex, newValue );
		} else {
			return input;
		}
	}

	/**
	 * Retrieves the deletion logs for a given page.
	 *
	 * @param {string} pageName - The name of the page.
	 * @return {Promise<Array>} - A promise that resolves to an array of deletion logs.
	 */
	const getDeletionLogs = async ( pageName ) => {
		const response = await api.get( {
			action: 'query',
			list: 'logevents',
			leprop: [ 'type', 'title', 'user', 'timestamp' ],
			letype: 'delete',
			lelimit: 500,
			letitle: pageName
		} );
		return response.query.logevents || [];
	};

	/**
	 * Retrieves the creator of a given page.
	 *
	 * @param {string} pageName - The name of the page.
	 * @return {Promise<string>} - The username of the page creator.
	 */
	const getCreator = async ( pageName ) => {
		const response = await api.get( {
			action: 'query',
			prop: 'revisions',
			rvlimit: 1,
			rvprop: [ 'user' ],
			rvdir: 'newer',
			titles: pageName
		} );
		const pageId = Object.keys( response.query.pages )[ 0 ];
		return response.query.pages[ pageId ].revisions[ 0 ].user;
	};

	/**
	 * Handles an error by displaying a notification with the error message.
	 * @param {string} error - The error message.
	 */
	function handleError( error ) {
		mw.notify( error, {
			title: mw.msg( 'adiutor-operation-failed' ),
			type: 'error'
		} );
	}

	/**
	 * Saves a configuration object to a specified MediaWiki page in JSON format.
	 *
	 * @param {string} title - The title of the page where the configuration should be saved.
	 * @param {Object} configuration - The configuration object to save.
	 */
	async function saveConfiguration( title, configuration ) {
		if ( !mw.config.get( 'wgUserGroups' ).includes( 'interface-admin' ) ) {
			mw.notify( mw.message( 'adiutor-interface-admin-required' ).text(), { type: 'error' } );
			return;
		}

		const editToken = mw.user.tokens.get( 'csrfToken' );
		const contentToSave = JSON.stringify( configuration, null, 2 );
		const summary = mw.message( 'adiutor-configurations-saving' ).text();

		try {
			await api.postWithToken( 'csrf', {
				action: 'edit',
				title: title,
				text: contentToSave,
				token: editToken,
				summary: summary,
				format: 'json'
			} );
			mw.notify( mw.message( 'adiutor-localization-settings-has-been-updated' ).text(), {
				title: mw.msg( 'adiutor-operation-completed' ),
				type: 'success'
			} );
		} catch ( error ) {
			mw.notify( error, { type: 'error' } );
		}
	}

	/**
	 * Retrieves the content of a page from the MediaWiki API.
	 * @param {string} title - The title of the page.
	 * @return {Promise<string>} - The content of the page.
	 */
	async function getPageContent( title ) {
		let response;
		try {
			response = await api.get( {
				action: 'query',
				titles: title,
				prop: 'revisions',
				rvprop: 'content',
				format: 'json'
			} );
			const pageId = Object.keys( response.query.pages )[ 0 ];
			return response.query.pages[ pageId ].revisions[ 0 ][ '*' ];
		} catch ( error ) {
			handleError( error );
		}
	}

	/**
	 * Extracts template names and parameters from the given content.
	 *
	 * @param {string} content - The content to extract templates from.
	 * @param {boolean} useMultipleIssuesTemplate - Indicates whether to use multiple issues' template.
	 * @param {string} multipleIssuesTemplate - The multiple issues template to use.
	 * @return {Array} - An array of objects containing template names and parameters.
	 */
	async function extractTemplateNamesAndParameters( content, useMultipleIssuesTemplate, multipleIssuesTemplate ) {
		const templatesAndParams = [];
		// eslint-disable-next-line es-x/no-regexp-s-flag,security/detect-unsafe-regex
		const regex = /{{((?:[^{}]|\{(?!\{).*})*)}}/gs;

		content.replace( regex, ( match, innerContent ) => {
			const parts = innerContent.split( '|' );
			const templateName = parts[ 0 ].trim();
			const parameters = {};

			if ( useMultipleIssuesTemplate && templateName === multipleIssuesTemplate ) {
				parts.slice( 1 ).forEach( ( part ) => {
					const nestedTemplate = extractTemplateNamesAndParameters( part, true, multipleIssuesTemplate );
					templatesAndParams.push( ...nestedTemplate );
				} );
			} else {
				parts.slice( 1 ).forEach( ( part ) => {
					const [ key, ...valueParts ] = part.split( '=' ).map( ( s ) => s.trim() );
					const value = valueParts.join( '=' ).trim();
					if ( key ) {
						parameters[ key ] = value || '';
					}
				} );
				templatesAndParams.push( { name: templateName, parameters } );
			}

			return match;
		} );

		return templatesAndParams;
	}

	module.exports = {
		getDeletionLogs,
		replacePlaceholders,
		replaceParameter,
		getCreator,
		handleError,
		saveConfiguration,
		getPageContent,
		extractTemplateNamesAndParameters
	};

}() );
