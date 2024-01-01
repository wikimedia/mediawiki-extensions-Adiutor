<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use ExtensionRegistry;
use FormatJson;
use MediaWiki\Extension\Adiutor\Utils\Utils;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\User\UserOptionsLookup;
use Psr\Log\LoggerInterface;
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

	private LoggerInterface $logger;

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
		$this->logger = LoggerFactory::getInstance( 'Adiutor' );
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
			'adiutor-enable' ) ) {
			return;
		}
		if ( !$this->permissionManager->userHasRight( $user,
			'edit' ) ) {
			return;
		}
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
							'configuration' => 'adiutorRequestPageProtection',
						],
						[
							'title' => 'MediaWiki:AdiutorCreateSpeedyDeletion.json',
							'configuration' => 'adiutorCreateSpeedyDeletion',
						],
						[
							'title' => 'MediaWiki:AdiutorDeletionPropose.json',
							'configuration' => 'adiutorDeletionPropose',
						],
						[
							'title' => 'MediaWiki:AdiutorRequestPageMove.json',
							'configuration' => 'adiutorRequestPageMove',
						],
						[
							'title' => 'MediaWiki:AdiutorArticleTagging.json',
							'configuration' => 'adiutorArticleTagging',
						],
					];
					foreach ( $configPages as $configPage ) {
						if ( !isset( $configPage['title'], $configPage['configuration'] ) ) {
							$this->logger->warning( 'Configuration page data is incomplete',
								[ 'configPage' => $configPage ] );
							continue;
						}
						$title = Title::newFromText( $configPage['title'] );
						if ( !$title ) {
							$this->logger->warning( 'Configuration page title is invalid',
								[ 'configPageTitle' => $configPage['title'] ] );
							continue;
						}
						$rev = $this->revisionLookup->getRevisionByTitle( $title );
						if ( !$rev ) {
							$this->logger->warning( 'Configuration page not found',
								[ 'configPageTitle' => $configPage['title'] ] );
							$configData[$configPage['configuration']] = [];
							continue;
						}
						$content =
							$rev->getContent( SlotRecord::MAIN,
								RevisionRecord::RAW );
						if ( !( $content instanceof TextContent ) ) {
							$this->logger->warning( 'Configuration page content is not TextContent',
								[
									'configPageTitle' => $configPage['title'],
									'contentType' => get_class( $content ),
								] );
							$configData[$configPage['configuration']] = [];
							continue;
						}
						$jsonContent =
							FormatJson::decode( $content->getText(),
								true );
						if ( !is_array( $jsonContent ) ) {
							$this->logger->warning( 'Configuration page content is not valid JSON',
								[ 'configPageTitle' => $configPage['title'] ] );
							$configData[$configPage['configuration']] = [];
							continue;
						}
						$configData[$configPage['configuration']] = $jsonContent;
					}

					return $configData;
				} );
		$out->addJsConfigVars( $configData );
	}
}
