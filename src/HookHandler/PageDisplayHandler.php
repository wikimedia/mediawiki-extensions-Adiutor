<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use ExtensionRegistry;
use FormatJson;
use MediaWiki\Extension\Adiutor\Utils\Utils;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\User\UserOptionsLookup;
use TemplateParser;
use TextContent;
use Title;
use WANObjectCache;

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
	 * @var RevisionLookup
	 */
	private RevisionLookup $revisionLookup;

	/**
	 * @var TemplateParser
	 */
	private TemplateParser $templateParser;

	/**
	 * @var WANObjectCache
	 */
	private WANObjectCache $wanObjectCache;

	/**
	 * @param PermissionManager $permissionManager
	 * @param UserOptionsLookup $userOptionsLookup
	 * @param RevisionLookup $revisionLookup
	 * @param WANObjectCache $wanObjectCache
	 */
	public function __construct(
		PermissionManager $permissionManager, UserOptionsLookup $userOptionsLookup, RevisionLookup $revisionLookup,
		WANObjectCache $wanObjectCache
	) {
		$this->permissionManager = $permissionManager;
		$this->userOptionsLookup = $userOptionsLookup;
		$this->revisionLookup = $revisionLookup;
		$this->wanObjectCache = $wanObjectCache;
		$this->templateParser = new TemplateParser( __DIR__ . '/../Templates' );
	}

	/**
	 * @inheritDoc
	 */

	public function onBeforePageDisplay( $out, $skin ) : void {
		$extensionRegistry = ExtensionRegistry::getInstance();
		$user = $out->getUser();

		if ( !Utils::isEnabledForUser( $this->userOptionsLookup,
			$user,
			$extensionRegistry ) ) {
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
		$out->addModuleStyles( [ 'ext.adiutor.styles' ] );
		$out->addModules( 'ext.adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'Adiutor',
			[] ) );
		$configData =
			$this->wanObjectCache->getWithSetCallback( $this->wanObjectCache->makeKey( 'Adiutor',
				'config-data' ),
				$this->wanObjectCache::TTL_HOUR,
				function () {
					$configData = [];
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
						if ( !isset( $configPage['title'], $configPage['configuration'] ) ) {
							continue;
						}
						$title = Title::newFromText( $configPage['title'] );
						if ( !$title ) {
							continue;
						}
						$rev = $this->revisionLookup->getRevisionByTitle( $title );
						if ( !$rev ) {
							$configData[$configPage['configuration']] = [];
							continue;
						}
						$content =
							$rev->getContent( SlotRecord::MAIN,
								RevisionRecord::RAW );
						if ( $content instanceof TextContent ) {
							$configuration = FormatJson::decode( $content->getText() );
							$configData[$configPage['configuration']] = $configuration;
						} else {
							$configData[$configPage['configuration']] = [];
						}
					}

					return $configData;
				} );
		$out->addJsConfigVars( $configData );
	}
}
