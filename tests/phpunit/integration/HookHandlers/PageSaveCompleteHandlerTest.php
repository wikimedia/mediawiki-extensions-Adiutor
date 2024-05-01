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

use HashBagOStuff;
use MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\User\UserIdentity;
use MediaWikiIntegrationTestCase;
use WANObjectCache;
use WikiPage;

/**
 * @group Adiutor
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler
 */
class PageSaveCompleteHandlerTest extends MediaWikiIntegrationTestCase {

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
	}

}
