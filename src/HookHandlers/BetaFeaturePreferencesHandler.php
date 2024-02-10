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

use Config;
use MediaWiki\Permissions\PermissionManager;
use User;

class BetaFeaturePreferencesHandler {

	private Config $config;
	private PermissionManager $permissionManager;

	/**
	 * @param Config $config
	 * @param PermissionManager $permissionManager
	 */
	public function __construct( Config $config, PermissionManager $permissionManager ) {
		$this->config = $config;
		$this->permissionManager = $permissionManager;
	}

	/**
	 * @param User $user
	 * @param array[] &$betaPrefs
	 */
	public function onGetBetaFeaturePreferences( User $user, array &$betaPrefs ) {
		$extensionAssetsPath = $this->config->get( 'ExtensionAssetsPath' );

		if ( $this->permissionManager->userHasRight( $user,
			'edit' ) ) {
			$url = "https://meta.wikimedia.org/wiki/";
			$infoLink = $url . "Adiutor";
			$discussionLink = $url . "Talk:Adiutor";
			$betaPrefs['adiutor-beta-feature-enable'] = [
				'label-message' => 'adiutor-beta-feature-title',
				'desc-message' => 'adiutor-beta-feature-description',
				'screenshot' => [
					'ltr' => "$extensionAssetsPath/Adiutor/resources/ext.adiutor.images/adiutor-icon-ltr.svg",
					'rtl' => "$extensionAssetsPath/Adiutor/resources/ext.adiutor.images/adiutor-icon-rtl.svg",
				],
				'info-link' => $infoLink,
				'discussion-link' => $discussionLink,
				'requirements' => [
					'javascript' => true,
				],
			];
		}
	}
}
