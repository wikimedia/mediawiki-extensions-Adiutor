<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use Config;
use MediaWiki\Permissions\PermissionManager;
use User;

class BetaFeaturePreferencesHandler {
	/**
	 * @var Config
	 */
	private Config $config;

	/**
	 * @var PermissionManager
	 */
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
