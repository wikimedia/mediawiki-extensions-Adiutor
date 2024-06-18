<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.0.1
 */

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use ExtensionRegistry;
use HttpError;
use MediaWiki\Extension\Notifications\Model\Event;
use MediaWiki\Rest\Response;
use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Title\Title;
use MediaWiki\User\UserFactory;
use RequestContext;
use User;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * This is a rest handler for echo notifications.
 */
class NotifierHandler extends SimpleHandler {

	private UserFactory $userFactory;
	private RevisionLookup $revisionLookup;

	/**
	 * @param UserFactory $userFactory
	 * @param RevisionLookup $revisionLookup
	 */
	public function __construct( UserFactory $userFactory, RevisionLookup $revisionLookup ) {
		$this->userFactory = $userFactory;
		$this->revisionLookup = $revisionLookup;
	}

	/**
	 * @throws HttpError
	 */
	public function run(): Response {
		$jsonData = $this->getValidatedBody();

		if ( !is_array( $jsonData ) || !isset( $jsonData['content'] ) ) {
			// Handle the error appropriately, for example:
			throw new HttpError( 400, 'Invalid or missing content in the request body' );
		}

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
	 * Check whether the page has been changed since the user calling the API has last edited it.
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

	public function getBodyParamSettings(): array {
		return [
			'title' => [
				self::PARAM_SOURCE => 'body',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
			],
			'content' => [
				self::PARAM_SOURCE => 'body',
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true,
			],
		];
	}
}
