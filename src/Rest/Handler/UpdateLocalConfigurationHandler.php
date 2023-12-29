<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use Exception;
use HttpError;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Validator\JsonBodyValidator;
use MediaWiki\Rest\Validator\UnsupportedContentTypeBodyValidator;
use MediaWiki\Revision\SlotRecord;
use TextContent;
use Wikimedia\ParamValidator\ParamValidator;

class UpdateLocalConfigurationHandler extends SimpleHandler {

	/**
	 * @var PermissionManager
	 */
	private PermissionManager $permissionManager;

	public function __construct( PermissionManager $permissionManager ) {
		$this->permissionManager = $permissionManager;
	}

	/**
	 * @throws HttpError
	 */
	public function run() : array {
		$user = $this->getAuthority()->getUser();
		if ( !$this->permissionManager->userHasRight( $user,
			'editinterface' ) ) {
			throw new HttpError( 403,
				'User does not have the required permissions.' );
		}

		$jsonData = $this->getValidatedBody();
		$pageTitleText = $jsonData['title'];
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$titleObject = $titleFactory->newFromText( $pageTitleText );

		if ( $titleObject === null || !in_array( $titleObject->getPrefixedText(),
				[
					'MediaWiki:AdiutorRequestPageProtection.json',
					'MediaWiki:AdiutorCreateSpeedyDeletion.json',
					'MediaWiki:AdiutorDeletionPropose.json',
					'MediaWiki:AdiutorRequestPageMove.json',
					'MediaWiki:AdiutorArticleTagging.json',
				] ) ) {
			throw new HttpError( 403,
				'Editing of this page is not allowed.' );
		}

		try {
			$pageTitle = $titleObject->getPrefixedText();
			$pageContent =
				json_encode( $jsonData['content'],
					JSON_PRETTY_PRINT );

			$services = MediaWikiServices::getInstance();
			$titleFactory = $services->getTitleFactory();
			$wikiPageFactory = $services->getWikiPageFactory();

			$pageUpdater =
				$wikiPageFactory->newFromTitle( $titleFactory->newFromText( $pageTitle ) )->newPageUpdater( $user );
			$pageUpdater->setContent( SlotRecord::MAIN,
				new TextContent( $pageContent ) );
			$pageUpdater->saveRevision( CommentStoreComment::newUnsavedComment( 'Configuration file updated' ),
				EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY );
			$saveStatus = $pageUpdater->getStatus();

			if ( $saveStatus->isOK() ) {
				return [
					'status' => 'success',
					'message' => $jsonData['content'],
				];
			} else {
				return [
					'status' => 'error',
				];
			}
		} catch ( Exception $e ) {
			return [
				'status' => 'error',
				'message' => $e->getMessage(),
			];
		}
	}

	public function getBodyValidator( $contentType ) {
		if ( $contentType === 'application/json' ) {
			return new JsonBodyValidator( [
				'title' => [
					ParamValidator::PARAM_TYPE => [
						'MediaWiki:AdiutorRequestPageProtection.json',
						'MediaWiki:AdiutorCreateSpeedyDeletion.json',
						'MediaWiki:AdiutorDeletionPropose.json',
						'MediaWiki:AdiutorRequestPageMove.json',
						'MediaWiki:AdiutorArticleTagging.json',
					],
					ParamValidator::PARAM_REQUIRED => true,
				],
				'content' => [
					ParamValidator::PARAM_TYPE => 'string',
					ParamValidator::PARAM_REQUIRED => true,
				],
			] );
		}

		return new UnsupportedContentTypeBodyValidator( $contentType );
	}
}
