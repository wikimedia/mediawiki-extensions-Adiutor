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

namespace MediaWiki\Extension\Adiutor\Tests\Integration\Maintenance;

use MediaWiki\Extension\Adiutor\Maintenance\UpdateConfiguration;
use MediaWikiIntegrationTestCase;
use ReflectionClass;
use Title;
use TitleFactory;
use User;

/**
 * @group Maintenance
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\Maintenance\UpdateConfiguration
 */
class UpdateConfigurationTest extends MediaWikiIntegrationTestCase {

	public function testExecute() {
		// Create a new UpdateConfiguration object
		$maintenance = new UpdateConfiguration();
		$maintenance->execute();
		// Set getConfigurationPages to public for testing purposes
		$reflection = new ReflectionClass( $maintenance );
		$property = $reflection->getProperty( 'configurationPages' );
		$property->setAccessible( true );
		// Now get getConfigurationPages value and check if the pages exist
		$configurationPages = $property->getValue( $maintenance );
		// Assert that the configuration pages exist
		foreach ( $configurationPages as $pageTitle => $content ) {
			$title = Title::newFromText( $pageTitle );
			$this->assertTrue( $title->exists(), "Configuration page $pageTitle should exist after execution." );
		}
	}

	public function testCreatePage() {
		$maintenance = new UpdateConfiguration();
		$titleFactory = $this->createMock( TitleFactory::class );
		$services = $this->getServiceContainer();
		$user = User::newSystemUser( 'Adiutor bot', [ 'steal' => true ] );
		$this->setMwGlobals( 'wgReservedUsernames', [ 'Adiutor bot' ] );
		// Now check username is in the reserved usernames
		$this->assertContains( $user->getName(), $GLOBALS['wgReservedUsernames'],
			'Adiutor bot should be in the reserved usernames.' );
		// Create a new title and check if it exists
		$titleText = 'MediaWiki:AdiutorTestPage' . mt_rand( 10000, 99999 ) . '.json';
		$content = [ 'key' => 'value' ];
		$title = Title::newFromText( $titleText );
		$this->assertFalse( $title->exists(), 'Title should not exist for testing purposes.' );
		$titleFactory->method( 'newFromText' )->willReturn( $title );
		$maintenance->createPage(
			$titleText,
			$content,
		);
		$this->assertTrue( $title->exists(), 'Title should have been created.' );
		$wikiPage = $services->getWikiPageFactory()->newFromTitle( $title );
		$pageText = $wikiPage->getContent()->serialize();
		$expectedContent = json_encode( $content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		$this->assertEquals( $expectedContent, $pageText, 'Page should contain the expected JSON content.' );
		$maintenance->createPage(
			$titleText,
			[ 'new' => 'content' ],
		);
		$wikiPageAfter = $services->getWikiPageFactory()->newFromTitle( $title );
		$pageTextAfter = $wikiPageAfter->getContent()->serialize();
		$this->assertEquals( $expectedContent, $pageTextAfter,
			'Page content should not change when the title already exists.' );
	}
}
