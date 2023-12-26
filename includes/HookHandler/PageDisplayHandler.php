<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use ExtensionRegistry;
use FormatJson;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\User\UserOptionsLookup;
use Title;

class PageDisplayHandler implements BeforePageDisplayHook {
	/**
	 * @var PermissionManager
	 */
	private PermissionManager $permissionManager;

	/**
	 * @var UserOptionsLookup
	 */
	private UserOptionsLookup $userOptionsLookup;

	/**
	 * @param PermissionManager $permissionManager
	 * @param UserOptionsLookup $userOptionsLookup
	 */
	public function __construct( PermissionManager $permissionManager, UserOptionsLookup $userOptionsLookup ) {
		$this->permissionManager = $permissionManager;
		$this->userOptionsLookup = $userOptionsLookup;
	}

	/**
	 * @inheritDoc
	 */

	public function onBeforePageDisplay( $out, $skin ) : void {
		$services = MediaWikiServices::getInstance();
		$extensionRegistry = ExtensionRegistry::getInstance();
		$user = $out->getUser();
		$isBetaFeatureLoaded = $extensionRegistry->isLoaded( 'BetaFeatures' );
		if ( ( $isBetaFeatureLoaded && !$this->userOptionsLookup->getOption( $user,
				'adiutor-beta-feature-enable' ) ) ) {
			return;
		}
		if ( !$this->userOptionsLookup->getOption( $user,
			'adiutor-switch' ) ) {
			return;
		}
		if ( !$this->permissionManager->userHasRight( $user,
			'edit' ) ) {
			return;
		}
		$out->addHtml( '<div id="adiutor-container"></div>' );
		$out->addModules( 'ext.Adiutor' );
		$configPages = [
			[
				'title' => 'MediaWiki:AdiutorRequestPageProtection.json',
				'configuration' => 'AdiutorRequestPageProtection',
			],
			[
				'title' => 'MediaWiki:AdiutorCreateSpeedyDeletion.json',
				'configuration' => 'AdiutorCreateSpeedyDeletion',
			],
			[
				'title' => 'MediaWiki:AdiutorDeletionPropose.json',
				'configuration' => 'AdiutorDeletionPropose',
			],
			[
				'title' => 'MediaWiki:AdiutorRequestPageMove.json',
				'configuration' => 'AdiutorRequestPageMove',
			],
			[
				'title' => 'MediaWiki:AdiutorArticleTagging.json',
				'configuration' => 'AdiutorArticleTagging',
			],
		];

		foreach ( $configPages as $configPage ) {
			$title = Title::newFromText( $configPage['title'] );
			$rev = $services->getRevisionLookup()->getRevisionByTitle( $title );
			$content =
				$rev->getContent( SlotRecord::MAIN,
					RevisionRecord::FOR_PUBLIC );
			$configuration =
				FormatJson::decode( $content->getText(),
					FormatJson::FORCE_ASSOC );
			$out->addJsConfigVars( [ $configPage['configuration'] => $configuration ] );
		}
	}
}
