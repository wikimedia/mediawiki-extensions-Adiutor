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
 * @since 0.1.0
 */

namespace MediaWiki\Extension\Adiutor\Tests\Integration\Rest\Handler;

use Exception;
use HttpError;
use MediaWiki\Block\BlockManager;
use MediaWiki\Extension\Adiutor\Rest\Handler\NotifierHandler;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWiki\User\UserFactory;
use MediaWiki\User\UserIdentity;
use MediaWikiIntegrationTestCase;
use RequestContext;
use User;

/**
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\Rest\Handler\NotifierHandler
 */
class NotifierHandlerTest extends MediaWikiIntegrationTestCase {

	private UserFactory $userFactory;
	private RevisionLookup $revisionLookup;

	public function provideRunTestData(): array {
		return [
			'valid notification' => [
				'TestUser',
				'Test page',
				'some_reason',
				false,
				false,
				true,
				true,
			],
		];
	}

	/**
	 * @dataProvider provideRunTestData
	 * @throws HttpError
	 * @throws Exception
	 */
	public function testRun(
		string $authorName,
		string $title,
		string $reason,
		bool $isBlocked,
		bool $hasPageChanged,
		bool $expectsException,
		bool $echoLoaded
	) {
		$this->overrideConfigValue( 'EnableEcho', $echoLoaded );
		$mockUser = $this->createMock( User::class );
		$mockUser->method( 'getName' )
			->willReturn( $authorName );
		$this->userFactory->method( 'newFromName' )
			->willReturn( $mockUser );
		$latestRevisionId = 12345;
		$userIdentity = $this->createMock( UserIdentity::class );
		$userIdentity->method( 'getId' )
			->willReturn( $hasPageChanged ? RequestContext::getMain()->getUser()->getId() : 99999 );
		$revision = $this->createMock( RevisionRecord::class );
		$revision->method( 'getUser' )
			->willReturn( $userIdentity );
		$mockTitle = $this->createMock( Title::class );
		$mockTitle->method( 'getLatestRevID' )
			->willReturn( $latestRevisionId );
		$mockTitle->method( 'exists' )
			->willReturn( true );
		Title::clearCaches();
		$titleFactory = $this->createMock( TitleFactory::class );
		$titleFactory->method( 'newFromText' )
			->willReturn( $mockTitle );
		$this->setService( 'TitleFactory', $titleFactory );
		$this->revisionLookup->method( 'getRevisionById' )
			->willReturn( $revision );
		$handler = $this->getHandler();
		if ( $expectsException ) {
			$this->expectException( HttpError::class );
			$this->expectExceptionMessage( 'No changes made to the page' );
		}
		$response = $handler->run();
		$jsonBody = $response->getBody()->getContents();
		$data = json_decode( $jsonBody, true );
		if ( $data === null ) {
			$jsonError = json_last_error_msg();
			throw new Exception( "JSON error: $jsonError" );
		}
		$this->assertEquals( 'success', $data[ 'result' ] );
		$this->assertArrayHasKey( 'event', $data );
	}

	private function getHandler(): NotifierHandler {
		$handler = $this->getMockBuilder( NotifierHandler::class )
			->setConstructorArgs( [ $this->userFactory, $this->revisionLookup ] )
			->onlyMethods( [ 'getValidatedBody' ] )
			->getMock();

		$handler->method( 'getValidatedBody' )
			->willReturn( [
				'content' => [
					'author' => 'TestUser',
					'title' => 'Test page',
					'reason' => 'some_reason',
				],
			] );

		return $handler;
	}

	protected function setUp(): void {
		parent::setUp();
		$this->userFactory = $this->createMock( UserFactory::class );
		$this->revisionLookup = $this->createMock( RevisionLookup::class );
		$mockBlockManager = $this->getMockBuilder( BlockManager::class )->disableOriginalConstructor()->getMock();
		$mockBlockManager->method( 'getUserBlock' )->willReturn( null );
		$this->setService( 'BlockManager', $mockBlockManager );
	}
}
