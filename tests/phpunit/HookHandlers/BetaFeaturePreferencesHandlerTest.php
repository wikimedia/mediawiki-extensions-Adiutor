<?php

namespace MediaWiki\Extension\Adiutor\Test\Integration\HookHandlers;

use Exception;
use MediaWiki\Permissions\PermissionManager;
use MediaWikiIntegrationTestCase;
use User;

class BetaFeaturePreferencesHandlerTest extends MediaWikiIntegrationTestCase {
	/**
	 * Test the behavior of onGetBetaFeaturePreferences hook.
	 *
	 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\BetaFeaturePreferencesHandler::onGetBetaFeaturePreferences
	 * @throws Exception
	 */
	public function testOnGetBetaFeaturePreferences() {
		// Override PermissionManager service to return true for userHasRight
		$this->overrideMwServices(
			null,
			[
				'PermissionManager' => function () {
					$permissionManager = $this->createMock( PermissionManager::class );
					$permissionManager->method( 'userHasRight' )->willReturn( true );
					return $permissionManager;
				}
			]
		);

		// Create a mock User object
		$user = $this->createMock( User::class );

		// Array to store preferences modified by the hook
		$preferences = [
			'adiutor-beta-feature-enable' => false,
		];

		// Run the onGetBetaFeaturePreferences hook
		$this->getServiceContainer()->getHookContainer()->run( 'GetBetaFeaturePreferences', [ $user, $preferences ] );

		// Assert that the 'adiutor-beta-feature-enable' key exists in the preferences array
		$this->assertArrayHasKey( 'adiutor-beta-feature-enable', $preferences );
	}
}
