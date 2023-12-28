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
use TemplateParser;
use TextContent;
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
	 * @var TemplateParser
	 */
	private TemplateParser $templateParser;

	/**
	 * @param PermissionManager $permissionManager
	 * @param UserOptionsLookup $userOptionsLookup
	 */
	public function __construct(
		PermissionManager $permissionManager, UserOptionsLookup $userOptionsLookup
	) {
		$this->permissionManager = $permissionManager;
		$this->userOptionsLookup = $userOptionsLookup;
		$this->templateParser = new TemplateParser( __DIR__ . '/../Templates' );
	}

	/**
	 * @inheritDoc
	 */

	public function onBeforePageDisplay( $out, $skin ) : void {
		$services = MediaWikiServices::getInstance();
		$extensionRegistry = ExtensionRegistry::getInstance();
		$user = $out->getUser();
		$isBetaFeatureLoaded = $extensionRegistry->isLoaded( 'BetaFeatures' );
		if ( !$isBetaFeatureLoaded || !$this->userOptionsLookup->getOption( $user,
				'adiutor-beta-feature-enable' ) ) {
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
		$templateVars = [];
		$out->addModuleStyles( [ 'ext.Adiutor.styles' ] );
		$out->addModules( 'ext.Adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'Adiutor',
			$templateVars ) );

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

		// Define an empty array to collect configuration data
		$configData = [];

		foreach ( $configPages as $configPage ) {
			if ( !isset( $configPage['title'], $configPage['configuration'] ) ) {
				// Skip this iteration if required keys are not set in $configPage
				continue;
			}

			$title = Title::newFromText( $configPage['title'] );
			if ( !$title ) {
				// Skip this title if it is invalid
				continue;
			}

			$rev = $services->getRevisionLookup()->getRevisionByTitle( $title );

			if ( !$rev ) {
				// Handle the case where the revision is not found
				$configData[$configPage['configuration']] = [];
				continue;
			}

			$content =
				$rev->getContent( SlotRecord::MAIN,
					RevisionRecord::RAW );

			if ( $content ) {
				// Use instanceof to check the content's type instead of method_exists
				if ( $content instanceof TextContent ) {
					// Decode the content if it's instanceof TextContent
					$configuration = FormatJson::decode( $content->getText() );
					$configData[$configPage['configuration']] = $configuration;
				} else {
					// If content is not TextContent, use an empty array
					$configData[$configPage['configuration']] = [];
				}
			} else {
				// If content is not retrieved, use an empty array
				$configData[$configPage['configuration']] = [];
			}
		}
		// Add all the configuration data at once to avoid multiple calls to addJsConfigVars
		$out->addJsConfigVars( $configData );
	}
}
