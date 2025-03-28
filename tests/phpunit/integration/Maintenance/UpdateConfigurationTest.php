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

namespace MediaWiki\Extension\Adiutor\Tests\Integration\Maintenance;

use MediaWiki\Extension\Adiutor\Maintenance\UpdateConfiguration;
use MediaWiki\MainConfigNames;
use MediaWiki\Title\Title;
use MediaWikiIntegrationTestCase;
use ReflectionClass;
use TitleFactory;

/**
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\Maintenance\UpdateConfiguration
 */
class UpdateConfigurationTest extends MediaWikiIntegrationTestCase {

	public function testExecute() {
		$maintenance = new UpdateConfiguration();
		ob_start();
		$maintenance->execute();
		ob_end_clean();
		$reflection = new ReflectionClass( $maintenance );
		$configurationPages = $reflection->getConstant( 'CONFIGURATION_PAGES' );

		foreach ( $configurationPages as $pageTitle => $content ) {
			$title = Title::newFromText( $pageTitle );
			$this->assertTrue( $title->exists(), "Configuration page $pageTitle should exist after execution." );
		}
	}

	public function testCreatePage() {
		$maintenance = new UpdateConfiguration();
		$titleFactory = $this->createMock( TitleFactory::class );
		$services = $this->getServiceContainer();
		$this->overrideConfigValue( MainConfigNames::ReservedUsernames, [ 'AdiutorBot' ] );

		$titleText = 'MediaWiki:AdiutorTestPage' . mt_rand( 10000, 99999 ) . '.json';
		$content = [ 'key' => 'value' ];
		$title = Title::newFromText( $titleText );
		$titleFactory->method( 'newFromText' )->willReturn( $title );

		ob_start();
		$maintenance->createPage( $titleText, $content );
		ob_end_clean();

		$wikiPage = $services->getWikiPageFactory()->newFromTitle( $title );
		$pageText = $wikiPage->getContent()->serialize();
		$expectedContent = json_encode( $content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );

		$this->assertEquals( $expectedContent, $pageText, 'Page should contain the expected JSON content.' );

		ob_start();
		$maintenance->createPage( $titleText, [ 'new' => 'content' ] );
		ob_end_clean();

		$wikiPageAfter = $services->getWikiPageFactory()->newFromTitle( $title );
		$pageTextAfter = $wikiPageAfter->getContent()->serialize();
		$this->assertEquals(
			$expectedContent,
			$pageTextAfter,
			'Page content should not change when the title already exists.'
		);
	}
}
