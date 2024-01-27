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
	 * This function retrieves the parse tree of a given page title from the MediaWiki API.
	 * The parse tree is a representation of the parsed content of the page.
	 *
	 * @async
	 * @param {string} title - The title of the page for which to retrieve the parse tree.
	 * @return {Promise<string>} - A promise that resolves to the parse tree of the page.
	 * @throws {Error} - If an error occurs during the API request, it is caught and passed to the handleError function.
	 */
	async function getParseTree( title ) {
		let response;
		try {
			response = await api.get( {
				action: 'parse',
				prop: 'parsetree',
				page: title,
				format: 'json',
				formatversion: 2
			} );
			return response.parse.parsetree;
		} catch ( error ) {
			handleError( error );
		}
	}

	/**
	 * This function extracts templates and their parameters from parsed content.
	 * It traverses the parsed content, identifies templates and their parameters, and returns an array of objects.
	 * Each object represents a template and contains the template name and its parameters.
	 *
	 * @param {string} parsedContent - The parsed content from which to extract templates and parameters.
	 * @return {Array} - An array of objects, each representing a template and its parameters.
	 */
	function extractTemplatesFromParsedContent( parsedContent ) {
		const templatesAndParams = [];
		const processedTemplates = new Set();

		function traverseNode( node ) {
			const templates = node.querySelectorAll( 'template' );

			templates.forEach( ( template ) => {
				if ( processedTemplates.has( template ) ) {
					return;
				}

				const templateNameNode = template.querySelector( 'title' );
				const templateName = templateNameNode ? templateNameNode.textContent.trim() : '';

				const parametersNode = template.querySelector( 'part' );
				const parametersString = parametersNode ? parametersNode.textContent.trim() : '';

				const parameters = {};

				parametersString.split( '\n' ).forEach( ( param ) => {
					const [ paramName, paramValue ] = param.split( '=' ).map( ( item ) => item.trim() );
					if ( paramName && paramValue ) {
						parameters[ paramName ] = paramValue;
					}
				} );

				templatesAndParams.push( { name: templateName, parameters } );
				processedTemplates.add( template );
			} );

			Array.from( node.children ).forEach( traverseNode );
		}

		const parsedXml = new DOMParser().parseFromString( parsedContent, 'application/xml' );
		traverseNode( parsedXml.querySelector( 'root' ) );

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
		getParseTree,
		extractTemplatesFromParsedContent
	};

}() );
