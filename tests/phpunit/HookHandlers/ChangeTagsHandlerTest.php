<?php

namespace MediaWiki\Extension\Adiutor\Test\Unit\HookHandlers;

use MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler;
use MediaWikiIntegrationTestCase;
use User;

class ChangeTagsHandlerTest extends MediaWikiIntegrationTestCase {

	private ChangeTagsHandler $handler;

	protected function setUp(): void {
		parent::setUp();
		$this->handler = new ChangeTagsHandler();
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler::onListDefinedTags
	 */
	public function testOnListDefinedTags() {
		$tags = [];
		$this->handler->onListDefinedTags( $tags );
		$this->assertContains( ChangeTagsHandler::TAG_NAME, $tags,
			'The adiutor tag should be defined in the tags list' );
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler::onChangeTagsListActive
	 */
	public function testOnChangeTagsListActive() {
		$tags = [];
		$this->handler->onChangeTagsListActive( $tags );
		$this->assertContains( ChangeTagsHandler::TAG_NAME, $tags,
			'The adiutor tag should be marked as active in the tags list' );
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler::onChangeTagsAllowedAdd
	 */
	public function testOnChangeTagsAllowedAdd_UserAllowedToAddTag() {
		$allowedTags = [];
		$addTags = [ ChangeTagsHandler::TAG_NAME ];
		$user = $this->createMock( User::class );
		$this->handler->onChangeTagsAllowedAdd( $allowedTags, $addTags, $user );
		$this->assertContains( ChangeTagsHandler::TAG_NAME, $allowedTags,
			'The adiutor tag should be allowed for addition by the user' );
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler::onChangeTagsAllowedAdd
	 */
	public function testOnChangeTagsAllowedAdd_TagNotInAddTags() {
		$allowedTags = [];
		$addTags = [ 'some-other-tag' ];
		$user = $this->createMock( User::class );
		$this->handler->onChangeTagsAllowedAdd( $allowedTags, $addTags, $user );
		$this->assertNotContains( ChangeTagsHandler::TAG_NAME, $allowedTags,
			'The adiutor tag should not be allowed for addition if it is not in addTags' );
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\ChangeTagsHandler::onChangeTagsAllowedAdd
	 */
	public function testOnChangeTagsAllowedAdd_TagAlreadyAllowed() {
		$allowedTags = [ ChangeTagsHandler::TAG_NAME ];
		$addTags = [ ChangeTagsHandler::TAG_NAME ];
		$user = $this->createMock( User::class );
		$this->handler->onChangeTagsAllowedAdd( $allowedTags, $addTags, $user );
		$this->assertCount( 1, array_keys( $allowedTags, ChangeTagsHandler::TAG_NAME ),
			'The adiutor tag should be present only once in the allowedTags' );
	}
}
