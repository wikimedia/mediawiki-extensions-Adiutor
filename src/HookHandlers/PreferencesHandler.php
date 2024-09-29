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

namespace MediaWiki\Extension\Adiutor\HookHandlers;

use ExtensionRegistry;
use MediaWiki\Extension\Adiutor\Utils\Utils;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Preferences\Hook\GetPreferencesHook;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;

class PreferencesHandler implements GetPreferencesHook {

	private const PREF_ADIUTOR_ENABLE = 'adiutor-enable';
	private PermissionManager $permissionManager;
	private UserOptionsLookup $userOptionsLookup;

	/**
	 * @param PermissionManager $permissionManager
	 * @param UserOptionsLookup $userOptionsLookup
	 */
	public function __construct(
		PermissionManager $permissionManager,
		UserOptionsLookup $userOptionsLookup
	) {
		$this->permissionManager = $permissionManager;
		$this->userOptionsLookup = $userOptionsLookup;
	}

	/**
	 * @inheritDoc
	 */
	public function onGetPreferences( $user, &$preferences ): void {
		$extensionRegistry = ExtensionRegistry::getInstance();

		if ( !$this->permissionManager->userHasRight( $user, 'edit' ) ) {
			return;
		}

		// Check if BetaFeatures is installed and if the user has activated Adiutor Beta Feature
		$betaFeaturesInstalled = $extensionRegistry->isLoaded( 'BetaFeatures' );
		$adiutorBetaFeatureEnable = $this->userOptionsLookup->getOption( $user, 'adiutor-beta-feature-enable' );

		// If BetaFeatures is not installed or the Adiutor Beta Feature is enabled, show adiutor-enable preference
		if ( !$betaFeaturesInstalled || $adiutorBetaFeatureEnable ) {
			$preferences[ self::PREF_ADIUTOR_ENABLE ] = [
				'type' => 'toggle',
				'label-message' => 'adiutor-toggle-adiutor',
				'section' => 'moderation/adiutor',
			];
		}

		$adiutorEnabled = Utils::isEnabledForUser(
			$this->userOptionsLookup,
			$user,
			$extensionRegistry
		);

		if ( $adiutorEnabled ) {
			// Define other Adiutor's preferences
			$prefDefinitions = [
				'adiutor-csd-enable' => [
					'type' => 'check',
					'label-message' => 'adiutor-csd-enable-label',
					'section' => 'moderation/adiutor',
					'help-message' => 'adiutor-csd-enable-help',
				],
				'adiutor-rpp-enable' => [
					'type' => 'check',
					'label-message' => 'adiutor-rpp-enable-label',
					'section' => 'moderation/adiutor',
					'help-message' => 'adiutor-rpp-enable-help',
				],
				'adiutor-rpm-enable' => [
					'type' => 'check',
					'label-message' => 'adiutor-rpm-enable-label',
					'section' => 'moderation/adiutor',
					'help-message' => 'adiutor-rpm-enable-help',
				],
				'adiutor-prod-enable' => [
					'type' => 'toggle',
					'label-message' => 'adiutor-prod-enable-label',
					'section' => 'moderation/adiutor',
					'help-message' => 'adiutor-prod-enable-help',
				],
				'adiutor-tag-enable' => [
					'type' => 'toggle',
					'label-message' => 'adiutor-tag-enable-label',
					'section' => 'moderation/adiutor',
					'help-message' => 'adiutor-tag-enable-help',
				],
				'adiutor-rev-enable' => [
					'type' => 'toggle',
					'label-message' => 'adiutor-rev-enable-label',
					'section' => 'moderation/adiutor',
					'help-message' => 'adiutor-rev-enable-help',
				],
			];

			foreach ( $prefDefinitions as $key => $definition ) {
				$preferences[ $key ] = $definition;
			}

			$adiutorEnabled =
				$this->userOptionsLookup->getOption(
					$user,
					'adiutor-enable'
				);

			if ( !$adiutorEnabled ) {
				$preferences[ 'adiutor-csd-enable' ][ 'disabled' ] = true;
				$preferences[ 'adiutor-rpp-enable' ][ 'disabled' ] = true;
				$preferences[ 'adiutor-rpm-enable' ][ 'disabled' ] = true;
				$preferences[ 'adiutor-prod-enable' ][ 'disabled' ] = true;
				$preferences[ 'adiutor-tag-enable' ][ 'disabled' ] = true;
			}
		}
	}

	/**
	 * @param UserIdentity $user
	 * @param array &$modifiedOptions
	 * @param array $originalOptions
	 */
	public function onSaveUserOptions( UserIdentity $user, array &$modifiedOptions, array $originalOptions ) {
		$betaFeatureIsEnabled = $this->isTrue( $originalOptions, 'adiutor-beta-feature-enable' );
		$betaFeatureIsDisabled = !$betaFeatureIsEnabled;

		$betaFeatureWillEnable = $this->isTrue( $modifiedOptions, 'adiutor-beta-feature-enable' );
		$betaFeatureWillDisable = $this->isFalse( $modifiedOptions );

		$autoEnrollIsEnabled = $this->isTrue( $originalOptions, 'betafeatures-auto-enroll' );
		$autoEnrollIsDisabled = !$autoEnrollIsEnabled;
		$autoEnrollWillEnable = $this->isTrue( $modifiedOptions, 'betafeatures-auto-enroll' );

		if ( !isset( $modifiedOptions[ self::PREF_ADIUTOR_ENABLE ] ) &&
			isset( $originalOptions[ self::PREF_ADIUTOR_ENABLE ] ) ) {
			$modifiedOptions[ self::PREF_ADIUTOR_ENABLE ] = $originalOptions[ self::PREF_ADIUTOR_ENABLE ];
		}

		if ( ( $betaFeatureIsEnabled && $betaFeatureWillDisable ) ||
			( $betaFeatureIsDisabled && $betaFeatureWillEnable ) ||
			( $betaFeatureIsDisabled && $autoEnrollIsDisabled && $autoEnrollWillEnable ) ) {
			$modifiedOptions[ 'adiutor-enable' ] = false;
		}
	}

	/**
	 * @param array $options
	 * @param string $option
	 *
	 * @return bool The option is set and true
	 */
	private function isTrue( array $options, string $option ): bool {
		return !empty( $options[ $option ] );
	}

	/**
	 * @param array $options
	 *
	 * @return bool The option is set and false
	 */
	private function isFalse( array $options ): bool {
		return isset( $options[ 'adiutor-beta-feature-enable' ] ) && !$options[ 'adiutor-beta-feature-enable' ];
	}
}
