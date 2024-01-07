<?php

namespace MediaWiki\Extension\Adiutor\Utils;

use ExtensionRegistry;
use MediaWiki\User\UserIdentity;
use MediaWiki\User\UserOptionsLookup;

class Utils {

	/**
	 * Checks if a feature is enabled for a specific user.
	 *
	 * @param UserOptionsLookup $userOptionsLookup The UserOptionsLookup instance.
	 * @param UserIdentity $user The ID of the user to check.
	 * @param ExtensionRegistry $extensionRegistry The ExtensionRegistry instance.
	 *
	 * @return bool Returns true if the feature is enabled for the user, false otherwise.
	 */
	public static function isEnabledForUser(
		UserOptionsLookup $userOptionsLookup,
		UserIdentity $user,
		ExtensionRegistry $extensionRegistry
	): bool {
		$isBetaFeatureLoaded = $extensionRegistry->isLoaded( 'BetaFeatures' );

		if ( $isBetaFeatureLoaded ) {
			// Cast the return value to bool to ensure a boolean is returned
			return (bool)$userOptionsLookup->getOption( $user,
				'adiutor-beta-feature-enable' );
		} else {
			// If 'adiutor-enable' option is not set, default to false
			return (bool)$userOptionsLookup->getOption( $user,
				'adiutor-enable',
				false );
		}
	}
}
