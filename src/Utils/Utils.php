<?php

namespace MediaWiki\Extension\Adiutor\Utils;

use ExtensionRegistry;
use MediaWiki\User\UserOptionsLookup;

class Utils {
	public static function isEnabledForUser(
		UserOptionsLookup $userOptionsLookup, $user, ExtensionRegistry $extensionRegistry
	) : bool {
		$isBetaFeatureLoaded = $extensionRegistry->isLoaded( 'BetaFeatures' );

		if ( $isBetaFeatureLoaded ) {
			// Cast the return value to bool to ensure a boolean is returned
			return (bool) $userOptionsLookup->getOption( $user,
				'adiutor-beta-feature-enable' );
		} else {
			// If 'adiutor-enable' option is not set, default to false
			return (bool) $userOptionsLookup->getOption( $user,
				'adiutor-enable',
				false );
		}
	}
}
