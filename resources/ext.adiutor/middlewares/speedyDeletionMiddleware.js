( function () {
	'use strict';

	const api = new mw.Api();

	const AdiutorUtility = require( '../utilities/adiutorUtility.js' );

	/**
	 * Filters the selected speedy deletion reasons based on the checkbox values.
	 *
	 * @param {Array} speedyDeletionReasons - The array of speedy deletion reasons.
	 * @param {Object} selectedDeletionReasons - The array of checkbox values.
	 * @return {Array} - The filtered array of selected speedy deletion reasons.
	 */
	const filterSelectedReasons = ( speedyDeletionReasons, selectedDeletionReasons ) => speedyDeletionReasons.reduce( ( selected, category ) => {
		const selectedInCategory = category.reasons.filter( ( reason ) => selectedDeletionReasons.includes( reason.value ) );
		selected.push( ...selectedInCategory );
		return selected;
	}, [] );

	/**
	 * Constructs the deletion text based on the selected reasons, copyVioInput, and config.
	 *
	 * @param {Array} selectedReasons - The selected reasons for deletion.
	 * @param {Object} copyVioInput - The copy violation input.
	 * @param {Object} config - The configuration object containing various templates and shortcuts.
	 * @return {Object} - The deletion reason and summary.
	 */
	const constructDeletionText = ( selectedReasons, copyVioInput, config ) => {
		const {
			csdTemplateStartMultipleReason,
			multipleReasonSeparation,
			csdTemplateStartSingleReason,
			postfixReasonUsage,
			multipleReasonSummary,
			singleReasonSummary,
			speedyDeletionPolicyPageShortcut
		} = config;
		let csdReason, csdSummary, copyVioURL = '';

		if ( copyVioInput.value ) {
			copyVioURL = '|url=' + copyVioInput.value;
		}

		if ( selectedReasons.length > 1 ) {
			let saltCSDReason = csdTemplateStartMultipleReason;
			if ( multipleReasonSeparation === 'vertical_bar' ) {
				for ( let i = 0; i < selectedReasons.length; i++ ) {
					saltCSDReason += '|' + selectedReasons[ i ].value;
				}
				csdReason = saltCSDReason + '}}';
			} else if ( multipleReasonSeparation === 'default' ) {
				saltCSDReason += '|';
				for ( let i = 0; i < selectedReasons.length; i++ ) {
					if ( i > 0 ) {
						saltCSDReason += ( i < selectedReasons.length - 1 ) ? ', ' : ' ' + mw.msg( 'adiutor-and' ) + ' ';
					}
					saltCSDReason += '[[' + speedyDeletionPolicyPageShortcut + '#' + selectedReasons[ i ].value + ']]';
				}
				csdReason = saltCSDReason + '}}';
			}
			let saltCSDSummary = '';
			for ( let i = 0; i < selectedReasons.length; i++ ) {
				if ( i > 0 ) {
					saltCSDSummary += ( i < selectedReasons.length - 1 ) ? ', ' : ' ' + mw.msg( 'adiutor-and' ) + ' ';
				}
				saltCSDSummary += '[[WP:CSD#' + selectedReasons[ i ].value + ']]';
			}
			csdSummary = AdiutorUtility.replaceParameter( multipleReasonSummary, '2', saltCSDSummary );
		} else {
			const selectedReason = selectedReasons[ 0 ];
			const postfix = postfixReasonUsage === 'data' ? selectedReason.data : selectedReason.value;
			csdReason = `${ csdTemplateStartSingleReason }${ postfix }${ copyVioURL }}}`;
			csdSummary = AdiutorUtility.replaceParameter( singleReasonSummary, '2', selectedReason.data );
		}

		return { csdReason, csdSummary };
	};

	/**
	 * Sends a notification message to the author of an article.
	 *
	 * @param {string} title
	 * @param {string} author
	 * @param {string} summary
	 * @return {Promise<void>} - A promise that resolves when the message is sent successfully.
	 */
	const notifyAuthor = async ( title, author, summary ) => {
		const data = {
			title: title,
			content: {
				author: author,
				reason: summary,
				title: title
			}
		};
		const apiUrl = mw.config.get( 'wgServer' ) + mw.config.get( 'wgScriptPath' ) + '/rest.php/adiutor/v0/notifier';
		try {
			await fetch( apiUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify( data )
			} );
		} catch ( error ) {
			mw.notify( error.message, {
				title: mw.msg( 'adiutor-operation-failed' ),
				type: 'error'
			} );
		}
	};

	/**
	 * Sends a notification to the creator of a page.
	 *
	 * @param {string} pageName - The name of the page.
	 * @param {string} csdReason - The reason for speedy deletion.
	 * @param {string} csdSummary - The summary of the speedy deletion.
	 * @param {string} csdNotificationTemplate - The notification template for speedy deletion.
	 * @param {boolean} informCreator - Flag indicating whether to inform the creator or not.
	 * @return {Promise<void>} - A promise that resolves when the notification is sent.
	 */
	const sendNotificationToCreator = async ( pageName, csdReason, csdSummary, csdNotificationTemplate, informCreator ) => {
		if ( informCreator ) {
			try {
				const articleAuthor = await AdiutorUtility.getCreator( pageName );
				if ( !mw.util.isIPAddress( articleAuthor ) ) {
					await notifyAuthor( pageName, articleAuthor, csdSummary );
				}
			} catch ( error ) {
				AdiutorUtility.handleError( error );
			}
		}
	};

	/**
	 * Creates an API request to tag an article for speedy deletion.
	 *
	 * @param {string} pageName - The name of the page.
	 * @param {string} csdReason - The reason for the speedy deletion.
	 * @param {string} csdSummary - The summary of the speedy deletion.
	 */
	const createApiRequest = async ( pageName, csdReason, csdSummary ) => {
		try {
			await api.postWithToken( 'csrf', {
				action: 'edit',
				title: pageName,
				prependtext: csdReason + '\n',
				summary: csdSummary,
				tags: 'adiutor',
				format: 'json'
			} );
			mw.notify( mw.message( 'adiutor-speedy-deletion-request-completed' ).text(), {
				title: mw.msg( 'adiutor-operation-completed' ),
				type: 'success'
			} );
			window.location.reload();
		} catch ( error ) {
			mw.notify( error, {
				title: mw.msg( 'adiutor-operation-failed' ),
				type: 'error'
			} );
		}
	};

	/**
	 * Creates a speedy deletion request for a given page.
	 *
	 * @param {string} pageName - The name of the page to be deleted.
	 * @param {Object} selectedDeletionReasons - The value of the checkbox indicating the selected reasons for deletion.
	 * @param {string} copyVioInput - The input value for the copyvio reason, if applicable.
	 * @param {boolean} informCreator - Indicates whether to inform the page creator about the deletion request.
	 * @param {Object} csdConfiguration - The configuration object containing the available speedy deletion reasons.
	 * @return {Promise<void>} - A promise that resolves when the deletion request is created and notifications are sent.
	 */
	const createSpeedyDeletionRequest = async ( pageName,
		selectedDeletionReasons,
		copyVioInput,
		informCreator,
		csdConfiguration ) => {

		const selectedReasons = filterSelectedReasons( csdConfiguration.speedyDeletionReasons, selectedDeletionReasons );

		if ( selectedReasons.length === 0 ) {
			mw.notify( mw.message( 'adiutor-select-speedy-deletion-reason' ).text(), {
				title: mw.msg( 'adiutor-warning' ),
				type: 'error'
			} );
			return;
		}

		const {
			csdReason,
			csdSummary
		} = constructDeletionText( selectedReasons, copyVioInput, csdConfiguration );

		await createApiRequest( pageName, csdReason, csdSummary );
		await sendNotificationToCreator( pageName, csdReason, csdSummary, csdConfiguration.csdNotificationTemplate, informCreator );
	};

	module.exports = {
		createSpeedyDeletionRequest
	};

}() );
