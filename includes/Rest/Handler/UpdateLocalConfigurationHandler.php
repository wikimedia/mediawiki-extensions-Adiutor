<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\MediaWikiServices;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Validator\JsonBodyValidator;
use MediaWiki\Rest\Validator\UnsupportedContentTypeBodyValidator;
use MediaWiki\Revision\SlotRecord;
use TextContent;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * Example class to update local adiutor module configurations
 */
class UpdateLocalConfigurationHandler extends SimpleHandler {
	/**
	 * Updates the local configuration file with the provided JSON data.
	 *
	 * @return array An array containing the status and message.
	 * - If the update is successful, the status will be 'success' and the message will be the updated content.
	 * - If the update fails, the status will be 'error'.
	 */
	public function run() {
		$jsonData = $this->getValidatedBody();
		$pageTitle = $jsonData['title'];
		$pageContent = json_encode( $jsonData['content'], JSON_PRETTY_PRINT );
		$user = $this->getAuthority()->getUser();
		$pageUpdater = MediaWikiServices::getInstance();
		$titleFactory = $pageUpdater->getTitleFactory();
		$pageUpdater = MediaWikiServices::getInstance()
			->getWikiPageFactory()
			->newFromTitle( $titleFactory->newFromText( $pageTitle ) )
			->newPageUpdater( $user );
		$pageUpdater->setContent( SlotRecord::MAIN, new TextContent( $pageContent ) );
		$pageUpdater->saveRevision(
			CommentStoreComment::newUnsavedComment( 'Configuration file updated' ),
			EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY
		);
		$saveStatus = $pageUpdater->getStatus();
		if ( $saveStatus !== false ) {
			// If successful, return a success status
			return [ 'status' => 'success', 'message' => $jsonData['content'] ];
		} else {
			// If not successful, return an error status
			return [ 'status' => 'error' ];
		}
	}

	/**
	 * Returns the appropriate body validator based on the content type.
	 *
	 * @param string $contentType The content type of the request body.
	 * @return BodyValidatorInterface The body validator instance.
	 */
	public function getBodyValidator( $contentType ) {
		if ( $contentType === 'application/json' ) {
			return new JsonBodyValidator(
				[
					'title' => [
						ParamValidator::PARAM_TYPE => 'string',
						ParamValidator::PARAM_REQUIRED => true,
					],
					'content' => [
						ParamValidator::PARAM_TYPE => 'string',
						ParamValidator::PARAM_REQUIRED => true,
					],
				]
			);
		}
		return new UnsupportedContentTypeBodyValidator( $contentType );
	}
}
