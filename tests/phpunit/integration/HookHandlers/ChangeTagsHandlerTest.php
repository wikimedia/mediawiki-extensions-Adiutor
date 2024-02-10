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

namespace MediaWiki\Extension\Adiutor\Test\Integration\HookHandlers;

use MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler;
use MediaWikiIntegrationTestCase;
use User;

/**
 * @group Adiutor
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler
 */
class ChangeTagsHandlerTest extends MediaWikiIntegrationTestCase {

	private ChangeTagsHandler $handler;

	protected function setUp(): void {
		parent::setUp();
		$this->handler = new ChangeTagsHandler();
	}

	public function testOnListDefinedTags() {
		$tags = [];
		$this->handler->onListDefinedTags( $tags );
		$this->assertContains( ChangeTagsHandler::TAG_NAME, $tags,
			'The adiutor tag should be defined in the tags list' );
	}

	public function testOnChangeTagsListActive() {
		$tags = [];
		$this->handler->onChangeTagsListActive( $tags );
		$this->assertContains( ChangeTagsHandler::TAG_NAME, $tags,
			'The adiutor tag should be marked as active in the tags list' );
	}

	public function testOnChangeTagsAllowedAdd_UserAllowedToAddTag() {
		$allowedTags = [];
		$addTags = [ ChangeTagsHandler::TAG_NAME ];
		$user = $this->createMock( User::class );
		$this->handler->onChangeTagsAllowedAdd( $allowedTags, $addTags, $user );
		$this->assertContains( ChangeTagsHandler::TAG_NAME, $allowedTags,
			'The adiutor tag should be allowed for addition by the user' );
	}

	public function testOnChangeTagsAllowedAdd_TagNotInAddTags() {
		$allowedTags = [];
		$addTags = [ 'some-other-tag' ];
		$user = $this->createMock( User::class );
		$this->handler->onChangeTagsAllowedAdd( $allowedTags, $addTags, $user );
		$this->assertNotContains( ChangeTagsHandler::TAG_NAME, $allowedTags,
			'The adiutor tag should not be allowed for addition if it is not in addTags' );
	}

	public function testOnChangeTagsAllowedAdd_TagAlreadyAllowed() {
		$allowedTags = [ ChangeTagsHandler::TAG_NAME ];
		$addTags = [ ChangeTagsHandler::TAG_NAME ];
		$user = $this->createMock( User::class );
		$this->handler->onChangeTagsAllowedAdd( $allowedTags, $addTags, $user );
		$this->assertCount( 1, array_keys( $allowedTags, ChangeTagsHandler::TAG_NAME ),
			'The adiutor tag should be present only once in the allowedTags' );
	}
}
