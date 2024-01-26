<?php

namespace MediaWiki\Extension\Adiutor\Test\Integration\HookHandler;

use Exception;
use MediaWiki\Permissions\PermissionManager;
use MediaWikiIntegrationTestCase;
use User;

class PageDisplayHandlerTest extends MediaWikiIntegrationTestCase {
	/**
	 * Test the behavior of onPageContentSaveComplete hook.
	 *
	 * @covers \MediaWiki\Extension\Adiutor\HookHandler\PageDisplayHandler::onBeforePageDisplay
	 * @throws Exception
	 */
	public function testOnBeforePageDisplay() {
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
