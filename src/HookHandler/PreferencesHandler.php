<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use ExtensionRegistry;
use MediaWiki\Extension\Adiutor\Utils\Utils;
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
		$extensionRegistry = ExtensionRegistry::getInstance();

		if ( !$this->permissionManager->userHasRight( $user,
			'edit' ) ) {
			return;
		}

		if ( !Utils::isEnabledForUser( $this->userOptionsLookup,
			$user,
			$extensionRegistry ) ) {
			return;
		}

		$preferences['adiutor-enable'] = [
			'type' => 'toggle',
			'label-message' => 'adiutor-toggle-adiutor',
			'section' => 'moderate/adiutor',
		];

		$preferences['adiutor-csd-enable'] = [
			'type' => 'check',
			'label-message' => 'adiutor-csd-enable-label',
			'section' => 'moderate/adiutor',
			'help-message' => 'adiutor-csd-enable-help',
		];

		// Request protection
		$preferences['adiutor-rpp-enable'] = [
			'type' => 'check',
			'label-message' => 'adiutor-rpp-enable-label',
			'section' => 'moderate/adiutor',
			'help-message' => 'adiutor-rpp-enable-help',
		];

		$preferences['adiutor-rpm-enable'] = [
			'type' => 'check',
			'label-message' => 'adiutor-rpm-enable-label',
			'section' => 'moderate/adiutor',
			'help-message' => 'adiutor-rpm-enable-help',
		];

		$preferences['adiutor-prod-enable'] = [
			'type' => 'check',
			'label-message' => 'adiutor-prod-enable-label',
			'section' => 'moderate/adiutor',
			'help-message' => 'adiutor-prod-enable-help',
		];

		$preferences['adiutor-tag-enable'] = [
			'type' => 'check',
			'label-message' => 'adiutor-tag-enable-label',
			'section' => 'moderate/adiutor',
			'help-message' => 'adiutor-tag-enable-help',
		];
	}

	/**
	 * @param UserIdentity $user
	 * @param array &$modifiedOptions
	 * @param array $originalOptions
	 */
	public function onSaveUserOptions( UserIdentity $user, array &$modifiedOptions, array $originalOptions ) {
		$betaFeatureIsEnabled =
			$this->isTrue( $originalOptions,
				'adiutor-beta-feature-enable' );
		$betaFeatureIsDisabled = !$betaFeatureIsEnabled;

		$betaFeatureWillEnable =
			$this->isTrue( $modifiedOptions,
				'adiutor-beta-feature-enable' );
		$betaFeatureWillDisable = $this->isFalse( $modifiedOptions );

		$autoEnrollIsEnabled =
			$this->isTrue( $originalOptions,
				'betafeatures-auto-enroll' );
		$autoEnrollIsDisabled = !$autoEnrollIsEnabled;
		$autoEnrollWillEnable =
			$this->isTrue( $modifiedOptions,
				'betafeatures-auto-enroll' );

		if ( ( $betaFeatureIsEnabled && $betaFeatureWillDisable ) ||
			( $betaFeatureIsDisabled && $betaFeatureWillEnable ) ||
			( $betaFeatureIsDisabled && $autoEnrollIsDisabled && $autoEnrollWillEnable ) ) {
			$modifiedOptions['adiutor-enable'] = false;
		}
	}

	/**
	 * @param array $options
	 * @param string $option
	 *
	 * @return bool The option is set and true
	 */
	private function isTrue( array $options, string $option ) : bool {
		return !empty( $options[$option] );
	}

	/**
	 * @param array $options
	 *
	 * @return bool The option is set and false
	 */
	private function isFalse( array $options ) : bool {
		return isset( $options['adiutor-beta-feature-enable'] ) && !$options['adiutor-beta-feature-enable'];
	}
}
