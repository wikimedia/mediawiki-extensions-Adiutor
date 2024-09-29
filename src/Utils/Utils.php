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
			return (bool)$userOptionsLookup->getOption(
				$user,
				'adiutor-beta-feature-enable'
			);
		} else {
			// If 'adiutor-enable' option is not set, default to false
			return (bool)$userOptionsLookup->getOption(
				$user,
				'adiutor-enable',
				false
			);
		}
	}
}
