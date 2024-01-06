<?php

namespace MediaWiki\Extension\Adiutor\Test\Unit\HookHandler;

use MediaWiki\Extension\Adiutor\HookHandler\PreferencesHandler;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\UserGroupManager;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;
use MediaWikiIntegrationTestCase;
use PHPUnit\Framework\MockObject\Exception;
use User;

class PreferencesHandlerTest extends MediaWikiIntegrationTestCase {

	private function GetPreferencesHandler( array $options = [] ): PreferencesHandler {
		return new PreferencesHandler( ...array_values( array_merge(
			[
				'permissionManager' => $this->createMock( PermissionManager::class ),
				'userOptionsLookup' => $this->createMock( UserOptionsLookup::class ),
				'userGroupManager' => $this->createMock( UserGroupManager::class ),
			],
			$options
		) ) );
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandler\PreferencesHandler::onSaveUserOptions
	 * @dataProvider provideOnSaveUserOptionsNoAccessChange
	 */
	public function testOnSaveUserOptionsNoAccessChange( $originalOptions, $modifiedOptions, $expectedOptions ) {
		$user = $this->createMock( UserIdentity::class );
		$handler = $this->GetPreferencesHandler( [] );
		$handler->onSaveUserOptions( $user, $modifiedOptions, $originalOptions );
		$this->assertSame( $expectedOptions, $modifiedOptions );
	}

	public static function provideOnSaveUserOptionsNoAccessChange() {
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

	public static function provideOnSaveUserOptionsRestoreDefaultPreferences() {
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
	 * @covers \MediaWiki\Extension\Adiutor\HookHandler\PreferencesHandler::onGetPreferences
	 * @throws Exception
	 */
	public function testOnGetPreferences() {
		$user = $this->createMock( User::class );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userHasRight' )->willReturn( true );

		$handler = $this->getPreferencesHandler( [
			'permissionManager' => $permissionManager,
		] );

		$preferences = [];
		$handler->onGetPreferences( $user, $preferences );

		$this->assertArrayHasKey( 'adiutor-enable', $preferences );
	}
}
