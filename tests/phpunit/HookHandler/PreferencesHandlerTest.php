<?php

namespace MediaWiki\Extension\Adiutor\Test\Unit\HookHandler;

use MediaWiki\Extension\Adiutor\HookHandler\PreferencesHandler;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\UserGroupManager;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;
use MediaWikiIntegrationTestCase;
use User;

class PreferencesHandlerTest extends MediaWikiIntegrationTestCase {

	public static function provideOnSaveUserOptionsNoAccessChange() {
		return [ 'Enabled to begin with, then not set' => [ [ 'adiutor-switch' => true, ],
			[], ],
			'Enabled to begin with, then both option set to truthy' => [ [ 'adiutor-switch' => true, ],
				[ 'adiutor-switch' => '1', ], ],
			'Disabled to begin with, then not set' => [ [ 'adiutor-switch' => false, ],
				[], ],
			'Disabled to begin with, then set to falsey' => [ [ 'adiutor-switch' => 0, ],
				[ 'adiutor-switch' => false, ], ],
			'No options set to begin with, then no options set' => [ [],
				[], ], ];
	}

	public static function provideOnSaveUserOptionsRestoreDefaultPreferences() {
		return [ 'Disable beta feature' => [ [ 'adiutor-beta-feature-enable' => true ],
			[ 'adiutor-beta-feature-enable' => false ], ],
			'Enable beta feature' => [ [ 'adiutor-beta-feature-enable' => false ],
				[ 'adiutor-beta-feature-enable' => true ], ],
			'Enable auto enroll' => [ [ 'adiutor-beta-feature-enable' => false,
				'betafeatures-auto-enroll' => false ],
				[ 'betafeatures-auto-enroll' => true ], ], ];
	}

	/**
	 * @covers \MediaWiki\Extension\Adiutor\HookHandler\PreferencesHandler::onSaveUserOptions
	 */
	public function testOnSaveUserOptionsNoAccessChange( $originalOptions, $modifiedOptions ) {
		$user = $this->createMock( UserIdentity::class );

		$handler = $this->getPreferencesHandler( [] );
		$handler->onSaveUserOptions( $user,
			$modifiedOptions,
			$originalOptions );
	}

	private function getPreferencesHandler( array $options = [] ) : PreferencesHandler {
		return new PreferencesHandler( ...
			array_values( array_merge( [ 'permissionManager' => $this->createMock( PermissionManager::class ),
				'userOptionsLookup' => $this->createMock( UserOptionsLookup::class ),
				'userGroupManager' => $this->createMock( UserGroupManager::class ), ],
				$options ) ) );
	}

	public function testOnSaveUserOptionsRestoreDefaultPreferences( $originalOptions, $modifiedOptions ) {
		$user = $this->createMock( UserIdentity::class );

		$handler = $this->getPreferencesHandler( [] );
		$handler->onSaveUserOptions( $user,
			$modifiedOptions,
			$originalOptions );

		$this->assertFalse( $modifiedOptions['adiutor-switch'] );
	}

	public function testOnGetPreferences() {
		$user = $this->createMock( User::class );

		$permissionManager = $this->createMock( PermissionManager::class );
		$permissionManager->method( 'userHasRight' )->willReturn( true );

		$handler = $this->getPreferencesHandler( [ 'permissionManager' => $permissionManager ] );

		$preferences = [];
		$handler->onGetPreferences( $user,
			$preferences );
		$this->assertArrayHasKey( 'adiutor-switch',
			$preferences );
	}
}
