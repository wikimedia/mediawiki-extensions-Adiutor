<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use ExtensionRegistry;
use HttpError;
use MediaWiki\Extension\Notifications\Model\Event;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Validator\JsonBodyValidator;
use MediaWiki\Rest\Validator\UnsupportedContentTypeBodyValidator;
use MediaWiki\Title\Title;
use MediaWiki\User\UserFactory;
use RequestContext;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * Example class to update local adiutor module configurations
 */
class NotifierHandler extends SimpleHandler {

	private UserFactory $userFactory;

	public function __construct( UserFactory $userFactory ) {
		$this->userFactory = $userFactory;
	}

	/**
	 * @throws HttpError
	 */
	protected function run(): Response {
		$jsonData = $this->getValidatedBody();
		$content = $jsonData['content'];
		$agent = RequestContext::getMain()->getUser();
		$author = $this->userFactory->newFromName( $content['author'] );
		if ( ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			$event = Event::create( [
				'type' => 'adiutor-csd-notification',
				'title' => Title::newFromText( $content['title'] ),
				'extra' => [
					'author' => $author,
					'reason' => $content['reason'],
					'title' => $content['title'],
				],
				'agent' => $agent,
			] );

			if ( $event === false ) {
				throw new HttpError( 500, 'Failed to create notification event' );
			}

			return $this->getResponseFactory()->createJson( [ 'result' => 'success', 'event' => $event ] );
		} else {
			throw new HttpError( 404, 'Echo extension is not available' );
		}
	}

	/**
	 * Returns the appropriate body validator based on the content type.
	 *
	 * @param string $contentType The content type of the request body.
	 *
	 * @return JsonBodyValidator|UnsupportedContentTypeBodyValidator
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
		} else {
			return new UnsupportedContentTypeBodyValidator( $contentType );
		}
	}
}
