<?php

namespace MediaWiki\Extension\Adiutor\Test\Integration\HookHandler;

use MediaWiki\Permissions\PermissionManager;
use MediaWikiIntegrationTestCase;
use User;

class BetaFeaturePreferencesHandlerTest extends MediaWikiIntegrationTestCase {
    public function testOnGetBetaFeaturePreferences() {
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

        $user = $this->createMock( User::class );
        $preferences = [];
        $this->getServiceContainer()->getHookContainer()->run( 'GetBetaFeaturePreferences', [ $user, $preferences ] );
        $this->assertArrayHasKey( 'adiutor-beta-feature-enable', $preferences );
    }
}