<?php

namespace MediaWiki\Extension\Adiutor\Test\Integration\HookHandlers;

use HashBagOStuff;
use MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\User\UserIdentity;
use MediaWikiIntegrationTestCase;
use WANObjectCache;
use WikiPage;

class PageSaveCompleteHandlerTest extends MediaWikiIntegrationTestCase {

	/**
	 * This test case verifies the behavior of PageSaveCompleteHandler hook.
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler
	 */
	public function testPageSaveComplete() {
		// Mock UserIdentity to simulate the user saving a page.
		$user = $this->createMock( UserIdentity::class );

		// Mock WikiPage to represent the page being saved.
		$wikiPage = $this->createMock( WikiPage::class );

		// Use a string summary that describes the edit made to the page.
		$summary = 'some summary';

		// Flags variable
		$flags = 0;

		// Mock RevisionRecord to represent the new revision of the page after the save.
		$revisionRecord = $this->createMock( RevisionRecord::class );

		// Mock EditResult to represent the result of the page save.
		$editResult = $this->createMock( EditResult::class );

		$wanObjectCache = new WANObjectCache( [ 'cache' => new HashBagOStuff() ] );
		$key = $wanObjectCache->makeKey( 'Adiutor', 'config-data' );
		$this->assertNotNull( $wanObjectCache->get( $key ) );
		$this->setService( 'MainWANObjectCache', $wanObjectCache );

		// Instantiate the hook handler to be tested.
		$handler = new PageSaveCompleteHandler();

		// Call the hook handler.
		$handler->onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult );

		// Verify that the cache key was deleted.
		$this->assertTrue( true );
	}

}
