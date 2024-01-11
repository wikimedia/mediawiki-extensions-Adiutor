( function () {
	'use strict';

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

	exports.replacePlaceholders = replacePlaceholders;
	exports.replaceParameter = replaceParameter;

}() );
