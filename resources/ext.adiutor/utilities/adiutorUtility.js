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
		return response.query["logevents"] || [];
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

	module.exports = {
		getDeletionLogs,
		replacePlaceholders,
		replaceParameter,
		getCreator,
		handleError
	};

}() );
