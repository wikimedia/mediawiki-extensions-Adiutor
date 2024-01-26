<?php
namespace MediaWiki\Extension\Adiutor\Tests;

use MediaWiki\Extension\Adiutor\Maintenance\AdiutorMaintenance;
use MediaWikiIntegrationTestCase;
use Title;
use TitleFactory;
use User;

/**
 * PHPUnit test class for the AdiutorMaintenance.
 *
 * @group Adiutor
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\Maintenance\AdiutorMaintenance
 */
class AdiutorMaintenanceTest extends MediaWikiIntegrationTestCase {

	public function testExecute() {
		$mockTitleFactory = $this->createMock( TitleFactory::class );
		$mockUser = $this->createMock( User::class );
		$mockTitle = $this->createMock( Title::class );
		$mockTitleFactory->method( 'newFromText' )->willReturn( $mockTitle );
		$mockTitle->method( 'exists' )->willReturn( false );
		$mockUser->method( 'isAllowed' )->willReturn( true );

		$maintenance = new AdiutorMaintenance();
		$maintenance->execute();

		foreach ( $maintenance->getConfigurationPages() as $pageTitle => $content ) {
			$title = Title::newFromText( $pageTitle );
			$this->assertTrue( $title->exists(), "Configuration page $pageTitle should exist after execution." );
		}
	}

	public function testCreatePage() {
		$titleFactory = $this->createMock( TitleFactory::class );
		$services = $this->getServiceContainer();
		$user = User::newSystemUser( 'Adiutor bot', [ 'steal' => true ] );

		$titleText = 'MediaWiki:AdiutorTestPage' . mt_rand( 10000, 99999 ) . '.json';
		$content = [ 'key' => 'value' ];

		$title = Title::newFromText( $titleText );
		$this->assertFalse( $title->exists(), 'Title should not exist for testing purposes.' );

		$titleFactory->method( 'newFromText' )->willReturn( $title );

		$maintenance = new AdiutorMaintenance();
		$maintenance->createPage(
			$titleFactory,
			$titleText,
			$content,
			$user,
			$services
		);

		$this->assertTrue( $title->exists(), 'Title should have been created.' );

		$wikiPage = $services->getWikiPageFactory()->newFromTitle( $title );
		$pageText = $wikiPage->getContent()->serialize();
		$expectedContent = json_encode( $content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		$this->assertEquals( $expectedContent, $pageText, 'Page should contain the expected JSON content.' );

		$sameMaintenance = new AdiutorMaintenance();
		$sameMaintenance->createPage(
			$titleFactory,
			$titleText,
			[ 'new' => 'content' ],
			$user,
			$services
		);

		$wikiPageAfter = $services->getWikiPageFactory()->newFromTitle( $title );
		$pageTextAfter = $wikiPageAfter->getContent()->serialize();
		$this->assertEquals( $expectedContent, $pageTextAfter, 'Page content should not change when the title already exists.' );
	}

}
