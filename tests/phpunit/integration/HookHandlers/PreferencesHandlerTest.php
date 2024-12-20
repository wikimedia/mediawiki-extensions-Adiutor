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

use ExtensionRegistry;
use MediaWiki\Extension\Adiutor\HookHandlers\PreferencesHandler;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\UserGroupManager;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;
use MediaWikiIntegrationTestCase;
use User;

/**
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\PreferencesHandler
 */
class PreferencesHandlerTest extends MediaWikiIntegrationTestCase {

	public static function provideOnSaveUserOptionsNoAccessChange(): array {
		return [
			'Enabled to begin with, then not set' => [
				[ 'adiutor-enable' => true ],
				[],
				[ 'adiutor-enable' => true ],
			],
			'Enabled to begin with, then both option set to truthy' => [
				[ 'adiutor-enable' => true ],
				[ 'adiutor-enable' => '1' ],
				[ 'adiutor-enable' => '1' ],
			],
			'Disabled to begin with, then not set' => [
				[ 'adiutor-enable' => false ],
				[],
				[ 'adiutor-enable' => false ],
			],
			'Disabled to begin with, then set to falsey' => [
				[ 'adiutor-enable' => 0 ],
				[ 'adiutor-enable' => false ],
				[ 'adiutor-enable' => false ],
			],
			'No options set to begin with, then no options set' => [
				[],
				[],
				[],
			],
		];
	}

	public static function provideOnSaveUserOptionsRestoreDefaultPreferences(): array {
		return [
			'Enabled to begin with, then not set' => [
				[
					'adiutor-enable' => true,
				],
				[],
				[
					'adiutor-enable' => true,
				],
			],
			'Enabled to begin with, then both option set to truthy' => [
				[
					'adiutor-enable' => true,
				],
				[
					'adiutor-enable' => '1',
				],
				[
					'adiutor-enable' => '1',
				],
			],
			'Disabled to begin with, then not set' => [
				[
					'adiutor-enable' => false,
				],
				[],
				[
					'adiutor-enable' => false,
				],
			],
			'Disabled to begin with, then set to falsey' => [
				[
					'adiutor-enable' => 0,
				],
				[
					'adiutor-enable' => false,
				],
				[
					'adiutor-enable' => false,
				],
			],
			'No options set to begin with, then no options set' => [
				[],
				[],
				[],
			],
		];
	}

	/**
	 * @dataProvider provideOnSaveUserOptionsNoAccessChange
	 */
	public function testOnSaveUserOptionsNoAccessChange( $originalOptions, $modifiedOptions, $expectedOptions ) {
		$user = $this->createMock( UserIdentity::class );
		$handler = $this->getPreferencesHandler();
		$handler->onSaveUserOptions( $user, $modifiedOptions, $originalOptions );
		$this->assertSame( $expectedOptions, $modifiedOptions );
	}

	private function getPreferencesHandler( array $options = [] ): PreferencesHandler {
		return new PreferencesHandler(
			...array_values(
				array_merge(
					[
						'permissionManager' => $this->createMock( PermissionManager::class ),
						'userOptionsLookup' => $this->createMock( UserOptionsLookup::class ),
					],
					$options
				)
			)
		);
	}

	public function testOnGetPreferences() {
		$user = $this->createMock( User::class );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userHasRight' )->willReturnCallback(
			static function ( $user, $right ) {
				return $right === 'edit';
			}
		);

		$userOptionsLookup = $this->createMock( UserOptionsLookup::class );
		$userOptionsLookup->method( 'getOption' )->willReturnMap( [
			[ $user, 'adiutor-beta-feature-enable', null, false ],
		] );

		$extensionRegistry = $this->createMock( ExtensionRegistry::class );
		$extensionRegistry->method( 'isLoaded' )->willReturnCallback(
			static function ( $extension ) {
				// Simulate that BetaFeatures extension is not loaded
				return $extension !== 'BetaFeatures';
			}
		);

		$handler = $this->GetPreferencesHandler( [
			'permissionManager' => $permissionManager,
			'userOptionsLookup' => $userOptionsLookup,
			'userGroupManager' => $this->createMock( UserGroupManager::class ),
		] );

		$preferences = [
			'adiutor-enable' => [
				'type' => 'toggle',
				'label-message' => 'foo',
			],
		];

		$handler->onGetPreferences( $user, $preferences );

		$this->assertArrayHasKey( 'adiutor-enable', $preferences );
	}
}
