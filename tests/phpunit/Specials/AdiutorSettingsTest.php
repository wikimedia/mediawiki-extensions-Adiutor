<?php
namespace MediaWiki\Extension\Adiutor\Tests\Specials;

use MediaWiki\Extension\Adiutor\Specials\AdiutorSettings;
use MediaWikiIntegrationTestCase;
use Message;
use OutputPage;

/**
 * PHPUnit test class for the AdiutorSettings special page.
 *
 * @group Adiutor
 * @group Specials
 * @covers \MediaWiki\Extension\Adiutor\Specials\AdiutorSettings
 */
class AdiutorSettingsTest extends MediaWikiIntegrationTestCase {

	public function testExecute() {
		// Create an instance of AdiutorSettings special page.
		$adiutorSettings = new AdiutorSettings();

		// Set up a mock OutputPage.
		$mockOutputPage = $this->createMock( OutputPage::class );

		// Set expectations for setPageTitle method using a callback to check the message key.
		$mockOutputPage->expects( $this->once() )
			->method( 'setPageTitle' )
			->with( $this->callback( static function ( $message ) {
				// Check if the message key matches 'adiutor-settings'.
				return $message instanceof Message && $message->getKey() === 'adiutor-settings';
			} ) );

		// Set up expectations for the addHTML method.
		$mockOutputPage->expects( $this->once() )
			->method( 'addHTML' )
			->with( $this->isType( 'string' ) );

		// Inject the mock OutputPage into the AdiutorSettings object.
		$adiutorSettings->setOutput( $mockOutputPage );

		// Call the execute method.
		$adiutorSettings->execute( 'subPage' );
	}
}
