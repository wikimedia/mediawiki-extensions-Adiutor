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
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\Title\Title;
use MediaWiki\User\UserIdentity;
use MediaWikiIntegrationTestCase;
use Psr\Log\NullLogger;
use WANObjectCache;
use WikiPage;

/**
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler
 */
class PageSaveCompleteHandlerTest extends MediaWikiIntegrationTestCase {

	public function testPageSaveComplete() {
		$revisionLookup = $this->createMock( RevisionLookup::class );
		$logger = new NullLogger();
		$handler = new PageSaveCompleteHandler( $revisionLookup, $logger );

		$wikiPage = $this->createMock( WikiPage::class );
		$wikiPage->method( 'getTitle' )->willReturn( Title::newFromText( 'MediaWiki:AdiutorRequestPageProtection.json' ) );

		$user = $this->createMock( UserIdentity::class );

		$summary = 'some summary';

		$flags = 0;

		$revisionRecord = $this->createMock( RevisionRecord::class );

		$editResult = $this->createMock( EditResult::class );

		$wanObjectCache = new WANObjectCache( [ 'cache' => new HashBagOStuff() ] );
		$key = $wanObjectCache->makeKey( 'Adiutor', 'config-data' );
		$this->assertFalse( $wanObjectCache->get( $key ) );
		$this->setService( 'MainWANObjectCache', $wanObjectCache );

		$handler->onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult );

		$this->assertFalse( $wanObjectCache->get( $key ) );
	}
}
