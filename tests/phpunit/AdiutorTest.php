<?php

namespace MediaWiki\Extension\Adiutor\Test;

use MediaWiki\Extension\Adiutor\Adiutor;
use MediaWiki\Extension\Adiutor\Maintenance\AdiutorMaintenance;
use MediaWikiIntegrationTestCase;

/**
 * PHPUnit test class for the Adiutor.
 *
 * @group Adiutor
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\Adiutor
 */
class AdiutorTest extends MediaWikiIntegrationTestCase {
	/**
	 * Test the behavior of onExtensionLoad method.
	 *
	 * @covers \MediaWiki\Extension\Adiutor\Adiutor::onExtensionLoad
	 */
	public function testOnExtensionLoad() {
		// Create a mock AdiutorMaintenance object
		$adiutorMaintenanceMock = $this->createMock( AdiutorMaintenance::class );

		// Expect the execute method to be called once
		$adiutorMaintenanceMock->expects( $this->once() )->method( 'execute' );

		// Set the mock AdiutorMaintenance object
		Adiutor::setMaintenance( $adiutorMaintenanceMock );

		// Call the onExtensionLoad method
		Adiutor::onExtensionLoad();
	}

	protected function tearDown(): void {
		// Reset the Adiutor maintenance object after each test
		Adiutor::setMaintenance();

		parent::tearDown();
	}
}
