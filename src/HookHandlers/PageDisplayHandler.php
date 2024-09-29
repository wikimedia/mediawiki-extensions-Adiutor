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
use FormatJson;
use MediaWiki\Extension\Adiutor\Utils\Utils;
use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\Output\OutputPage;
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

	private PermissionManager $permissionManager;
	private UserOptionsLookup $userOptionsLookup;
	private RevisionLookup $revisionLookup;
	private TemplateParser $templateParser;
	private WANObjectCache $wanObjectCache;
	private LoggerInterface $logger;

	/**
	 * @param PermissionManager $permissionManager
	 * @param UserOptionsLookup $userOptionsLookup
	 * @param RevisionLookup $revisionLookup
	 * @param WANObjectCache $wanObjectCache
	 */
	public function __construct(
		PermissionManager $permissionManager,
		UserOptionsLookup $userOptionsLookup,
		RevisionLookup $revisionLookup,
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
	public function onBeforePageDisplay( $out, $skin ): void {
		$extensionRegistry = ExtensionRegistry::getInstance();
		$user = $out->getUser();

		if ( !Utils::isEnabledForUser( $this->userOptionsLookup, $user, $extensionRegistry ) ) {
			return;
		}
		if ( !$this->userOptionsLookup->getOption( $user, 'adiutor-enable' ) ) {
			return;
		}
		if ( !$this->permissionManager->userHasRight( $user, 'edit' ) ) {
			return;
		}

		$this->loadModulesAndHTML( $out );
	}

	/**
	 * Loads modules and HTML content for the page display.
	 *
	 * @param OutputPage $out The OutputPage object.
	 *
	 * @return void
	 */
	private function loadModulesAndHTML( OutputPage $out ): void {
		$out->addModules( 'ext.adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'Adiutor', [] ) );
		$configData = $this->getConfigData();
		$out->addJsConfigVars( $configData );
	}

	/**
	 * Retrieves the configuration data from the cache or generates it if not available.
	 *
	 * @return array The configuration data.
	 */
	private function getConfigData(): array {
		return $this->wanObjectCache->getWithSetCallback(
			$this->wanObjectCache->makeKey( 'Adiutor', 'config-data' ),
			$this->wanObjectCache::TTL_HOUR,
			function () {
				$configData = [];
				$configPages = [
					[
						'title' => 'MediaWiki:AdiutorRequestPageProtection.json',
						'configuration' => 'wgAdiutorRequestPageProtection',
					],
					[
						'title' => 'MediaWiki:AdiutorCreateSpeedyDeletion.json',
						'configuration' => 'wgAdiutorCreateSpeedyDeletion',
					],
					[
						'title' => 'MediaWiki:AdiutorDeletionPropose.json',
						'configuration' => 'wgAdiutorDeletionPropose',
					],
					[
						'title' => 'MediaWiki:AdiutorRequestPageMove.json',
						'configuration' => 'wgAdiutorRequestPageMove',
					],
					[
						'title' => 'MediaWiki:AdiutorArticleTagging.json',
						'configuration' => 'wgAdiutorArticleTagging',
					],
					[
						'title' => 'MediaWiki:AdiutorReportRevision.json',
						'configuration' => 'wgAdiutorReportRevision',
					],
				];
				foreach ( $configPages as $configPage ) {
					$title = Title::newFromText( $configPage[ 'title' ] );
					if ( !$title ) {
						$this->logger->warning(
							'Configuration page title is invalid',
							[ 'configPageTitle' => $configPage[ 'title' ] ]
						);
						continue;
					}
					$rev = $this->revisionLookup->getRevisionByTitle( $title );
					if ( !$rev ) {
						$this->logger->warning(
							'Configuration page not found',
							[ 'configPageTitle' => $configPage[ 'title' ] ]
						);
						$configData[ $configPage[ 'configuration' ] ] = [];
						continue;
					}
					$content = $rev->getContent( SlotRecord::MAIN, RevisionRecord::RAW );
					if ( !( $content instanceof TextContent ) ) {
						$this->logger->warning( 'Configuration page content is not TextContent', [
							'configPageTitle' => $configPage[ 'title' ],
							'contentType' => get_class( $content ),
						] );
						$configData[ $configPage[ 'configuration' ] ] = [];
						continue;
					}
					$jsonContent = FormatJson::decode( $content->getText(), true );
					if ( !is_array( $jsonContent ) ) {
						$this->logger->warning(
							'Configuration page content is not valid JSON',
							[ 'configPageTitle' => $configPage[ 'title' ] ]
						);
						$configData[ $configPage[ 'configuration' ] ] = [];
						continue;
					}
					$configData[ $configPage[ 'configuration' ] ] = $jsonContent;
				}

				return $configData;
			}
		);
	}
}
