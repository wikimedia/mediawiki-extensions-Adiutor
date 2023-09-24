<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Content\Content;
use MediaWiki\Rest\Validator\JsonBodyValidator;
use MediaWiki\Rest\Validator\UnsupportedContentTypeBodyValidator;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Storage\PageUpdater;
use MediaWiki\MediaWikiServices;
use Wikimedia\ParamValidator\ParamValidator;
use TextContent;
use User;

/**
 * Example class to update local adiutor module configurations
 */
class UpdateLocalConfigurationHandler extends SimpleHandler
{
	public function run()
	{
		$jsonData = $this->getValidatedBody();
		$pageTitle = $jsonData['title'];
		$pageContent = json_encode($jsonData['content'], JSON_PRETTY_PRINT);
		$user = $this->getAuthority()->getUser();
		$pageUpdater = MediaWikiServices::getInstance();
		$titleFactory = $pageUpdater->getTitleFactory();
		$pageUpdater = MediaWikiServices::getInstance()
			->getWikiPageFactory()
			->newFromTitle($titleFactory->newFromText($pageTitle))
			->newPageUpdater($user);
		$pageUpdater->setContent(SlotRecord::MAIN, new TextContent($pageContent));
		$pageUpdater->saveRevision(
			CommentStoreComment::newUnsavedComment('Configuration file updated'),
			EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY
		);
		$saveStatus = $pageUpdater->getStatus();
		if ($saveStatus !== false) {
			// If successful, return a success status
			return ['status' => 'success', 'message' => $jsonData['content']];
		} else {
			// If not successful, return an error status
			return ['status' => 'error'];
		}
	}

	public function getBodyValidator($contentType)
	{
		if ($contentType === 'application/json') {
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
		return new UnsupportedContentTypeBodyValidator($contentType);
	}
}
