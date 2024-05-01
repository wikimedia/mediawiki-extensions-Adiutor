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

use MediaWiki\Extension\Adiutor\HookHandlers\BetaFeaturePreferencesHandler;
use MediaWiki\Permissions\PermissionManager;
use MediaWikiIntegrationTestCase;
use User;

/**
 * @group Adiutor
 * @group Database
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\BetaFeaturePreferencesHandler
 */
class BetaFeaturePreferencesHandlerTest extends MediaWikiIntegrationTestCase {

	public function providePreferences(): array {
		return [
			[ 'adiutor-beta-feature-enable', false, true ],
			[ 'adiutor-beta-feature-enable', true, false ],
		];
	}

	/**
	 * @dataProvider providePreferences
	 */
	public function testOnGetBetaFeaturePreferences( $key, $value, $expected ) {
		$handler = $this->createMock( BetaFeaturePreferencesHandler::class );
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

		// Run the onGetBetaFeaturePreferences hook
		$preferences = [
			$key => $value,
		];

		$this->getServiceContainer()->getHookContainer()->run( 'GetBetaFeaturePreferences', [ $user, $preferences ] );

		// Assert that the 'adiutor-beta-feature-enable' key exists in the preferences array
		$this->assertArrayHasKey( $expected ? 'adiutor-beta-feature-enable' : $key, $preferences );

		// Assert that the 'adiutor-beta-feature-enable' key exists in the preferences array
		$this->assertArrayHasKey( 'adiutor-beta-feature-enable', $preferences );

		$handler->onGetBetaFeaturePreferences( $user, $preferences );
	}
}
