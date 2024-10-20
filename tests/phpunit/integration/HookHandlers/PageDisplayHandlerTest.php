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

use MediaWiki\Permissions\PermissionManager;
use MediaWikiIntegrationTestCase;
use User;

/**
 * @covers \MediaWiki\Extension\Adiutor\HookHandlers\PageDisplayHandler
 */
class PageDisplayHandlerTest extends MediaWikiIntegrationTestCase {

	public function testOnBeforePageDisplay() {
		$this->overrideMwServices(
			null,
			[
				'PermissionManager' => function () {
					$permissionManager = $this->createMock( PermissionManager::class );
					$permissionManager->method( 'userHasRight' )->willReturn( true );
					$this->setService( 'PermissionManager', $permissionManager );

					return $permissionManager;
				},
			]
		);

		$user = $this->createMock( User::class );

		$preferences = [
			'adiutor-beta-feature-enable' => false,
		];

		$this->getServiceContainer()->getHookContainer()->run( 'GetBetaFeaturePreferences', [ $user, $preferences ] );

		$this->assertArrayHasKey( 'adiutor-beta-feature-enable', $preferences );
	}
}
