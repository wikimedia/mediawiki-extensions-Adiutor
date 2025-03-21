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

namespace MediaWiki\Extension\Adiutor\Test\Integration\HookHandlers;

use MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use MediaWiki\Title\Title;
use MediaWiki\User\UserIdentity;
use MediaWikiIntegrationTestCase;
use Psr\Log\NullLogger;
use WikiPage;

/**
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\PageSaveCompleteHandler
 */
class PageSaveCompleteHandlerTest extends MediaWikiIntegrationTestCase {

	public function testPageSaveComplete() {
		$handler = new PageSaveCompleteHandler(
			$this->createMock( RevisionLookup::class ),
			new NullLogger()
		);

		$wikiPage = $this->createMock( WikiPage::class );
		$wikiPage->method( 'getTitle' )->willReturn( Title::newFromText( 'MediaWiki:AdiutorRequestPageProtection.json' ) );
		$user = $this->createMock( UserIdentity::class );
		$summary = 'some summary';
		$flags = 0;
		$revisionRecord = $this->createMock( RevisionRecord::class );
		$editResult = $this->createMock( EditResult::class );

		$cache = $this->getServiceContainer()->getMainWANObjectCache();
		$key = $cache->makeKey( 'Adiutor', 'config-data' );
		$this->assertFalse( $cache->get( $key ) );

		// FIXME: This isn't testing much, since it is already false and the hook has no ability
		// to change that, even if not called.
		//
		// TODO:
		// - Turn into hook-agnostic integration test that makes an actual edit (no mocking),
		// - Call onBeforePageDisplay() to assert PageDisplayHandler is functionally working,
		//   by checking the config data.
		// - Disable WANObjectCache, make another edit, forward ConvertibleTimestamp clock by 1min.
		//   Assert that the cache works by seeing onBeforePageDisplay continue to use the same data.
		// - Re-enable, make a third and final edit, forward clock by another min.
		//   Asseert that purge works by seeing onBeforePageDisplay now use the new data.
		$handler->onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult );
		$this->assertFalse( $cache->get( $key ) );
	}
}
