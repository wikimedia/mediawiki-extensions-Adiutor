<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use ExtensionRegistry;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Preferences\Hook\GetPreferencesHook;
use MediaWiki\User\UserGroupManager;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;

class PreferencesHandler implements GetPreferencesHook {
	/**
	 * @var PermissionManager
	 */
	private PermissionManager $permissionManager;

	/**
	 * @var UserOptionsLookup
	 */
	private UserOptionsLookup $userOptionsLookup;

	/**
	 * @var UserGroupManager
	 */
	private UserGroupManager $userGroupManager;

	/**
	 * @param PermissionManager $permissionManager
	 * @param UserOptionsLookup $userOptionsLookup
	 * @param UserGroupManager $userGroupManager
	 */
	public function __construct(
		PermissionManager $permissionManager, UserOptionsLookup $userOptionsLookup, UserGroupManager $userGroupManager
	) {
		$this->permissionManager = $permissionManager;
		$this->userOptionsLookup = $userOptionsLookup;
		$this->userGroupManager = $userGroupManager;
	}

	/**
	 * @inheritDoc
	 */
	public function onGetPreferences( $user, &$preferences ) : void {
		if ( !$this->permissionManager->userHasRight( $user,
			'edit' ) ) {
			return;
		}

		$isBetaFeatureLoaded = ExtensionRegistry::getInstance()->isLoaded( 'BetaFeatures' );
		if ( $isBetaFeatureLoaded && !$this->userOptionsLookup->getOption( $user,
				'adiutor-beta-feature-enable' ) ) {
			return;
		}

		$preferences['adiutor-switch'] = [
			'type' => 'toggle',
			'label-message' => 'adiutor-toggle-adiutor',
			'section' => 'moderate/adiutor',
		];
	}

	/**
	 * @param UserIdentity $user
	 * @param array &$modifiedOptions
	 * @param array $originalOptions
	 */
	public function onSaveUserOptions( UserIdentity $user, array &$modifiedOptions, array $originalOptions ) {
		$betaFeatureIsEnabled =
			$this->isTruthy( $originalOptions,
				'adiutor-beta-feature-enable' );
		$betaFeatureIsDisabled = !$betaFeatureIsEnabled;

		$betaFeatureWillEnable =
			$this->isTruthy( $modifiedOptions,
				'adiutor-beta-feature-enable' );
		$betaFeatureWillDisable = $this->isFalsey( $modifiedOptions );

		$autoEnrollIsEnabled =
			$this->isTruthy( $originalOptions,
				'betafeatures-auto-enroll' );
		$autoEnrollIsDisabled = !$autoEnrollIsEnabled;
		$autoEnrollWillEnable =
			$this->isTruthy( $modifiedOptions,
				'betafeatures-auto-enroll' );

		if ( ( $betaFeatureIsEnabled && $betaFeatureWillDisable ) ||
			( $betaFeatureIsDisabled && $betaFeatureWillEnable ) ||
			( $betaFeatureIsDisabled && $autoEnrollIsDisabled && $autoEnrollWillEnable ) ) {
			$modifiedOptions['adiutor-switch'] = false;
		}
	}

	/**
	 * @param array $options
	 * @param string $option
	 *
	 * @return bool The option is set and truthy
	 */
	private function isTruthy( array $options, string $option ) : bool {
		return !empty( $options[$option] );
	}

	/**
	 * @param array $options
	 *
	 * @return bool The option is set and falsey
	 */
	private function isFalsey( array $options ) : bool {
		return isset( $options['adiutor-beta-feature-enable'] ) && !$options['adiutor-beta-feature-enable'];
	}
}
