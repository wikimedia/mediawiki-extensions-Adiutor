<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use ExtensionRegistry;
use HttpError;
use MediaWiki\Extension\Notifications\Model\Event;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Validator\JsonBodyValidator;
use MediaWiki\Rest\Validator\UnsupportedContentTypeBodyValidator;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Title\Title;
use MediaWiki\User\UserFactory;
use RequestContext;
use User;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * Example class to update local adiutor module configurations
 */
class NotifierHandler extends SimpleHandler {

	private UserFactory $userFactory;

	private RevisionLookup $revisionLookup;

	public function __construct( UserFactory $userFactory, RevisionLookup $revisionLookup ) {
		$this->userFactory = $userFactory;
		$this->revisionLookup = $revisionLookup;
	}

	/**
	 * @throws HttpError
	 */
	protected function run(): Response {
		$jsonData = $this->getValidatedBody();
		$content = $jsonData['content'];

		$agent = RequestContext::getMain()->getUser();
		$author = $this->userFactory->newFromName( $content['author'] );

		$this->preconditionsCheck( $agent, $content['title'] );

		$event = $this->createNotificationEvent( $content, $author, $agent );

		return $this->getResponseFactory()->createJson( [ 'result' => 'success', 'event' => $event ] );
	}

	/**
	 * Check if any preconditions are violated before sending a notification
	 *
	 * @param User $agent The author user object.
	 * @param string $pageTitle Title of the page that the notification is about.
	 * @throws HttpError
	 */
	private function preconditionsCheck( User $agent, string $pageTitle ): void {
		if ( $agent->getBlock() ) {
			throw new HttpError( 403, 'Author is blocked' );
		}

		if ( !$this->hasPageChanged( $pageTitle ) ) {
			throw new HttpError( 400, 'No changes made to the page' );
		}
	}

	/**
	 * Check if a page has changed recently
	 *
	 * @param string $pageTitle Title of the page to check.
	 * @return bool True if the page has changed; otherwise, false.
	 */
	private function hasPageChanged( string $pageTitle ): bool {
		$title = Title::newFromText( $pageTitle );

		if ( !$title->exists() ) {
			// If the title does not exist, no changes can have been made
			return false;
		}

		// Get the latest revision of the page
		$latestRevision = $title->getLatestRevID();

		if ( !$latestRevision ) {
			// If there's no revision ID, for whatever reason, we can't tell if the page has changed
			return false;
		}

		// Check if the agent is the one who made the latest change
		$latestRevisionUser = $this->revisionLookup->getRevisionById( $latestRevision )->getUser();

		// Compare the user IDs to determine if the page has changed recently
		return $latestRevisionUser && $latestRevisionUser->getId() === RequestContext::getMain()->getUser()->getId();
	}

	/**
	 * Create a notification event if Echo extension is loaded
	 *
	 * @param array $content The content array containing 'title' and 'reason'.
	 * @param User $author The author user object.
	 * @param User $agent The user object representing the agent.
	 *
	 * @return Event The notification event.
	 * @throws HttpError
	 */
	private function createNotificationEvent( array $content, User $author, User $agent ): Event {
		if ( ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			$event = Event::create( [
				'type' => 'adiutor-csd-notification',
				'title' => Title::newFromText( $content['title'] ),
				'extra' => [
					'author' => $author,
					'reason' => $content['reason'],
					'title' => $content['title'],
				],
				'agent' => $agent
			] );

			if ( $event === false ) {
				throw new HttpError( 500, 'Failed to create notification event' );
			}

			return $event;
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
