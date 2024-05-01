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

/**
 * Class UpdateConfiguration
 *
 * This class is responsible for creating and saving configuration pages for the Adiutor extension
 * if they do not already exist. It extends the Maintenance class.
 */

namespace MediaWiki\Extension\Adiutor\Maintenance;

use Maintenance;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Extension\Adiutor\Configs\AdiutorDummyConfig;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MediaWiki\Revision\SlotRecord;
use TextContent;
use User;

class UpdateConfiguration extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->requireExtension( 'Adiutor' );
	}

	private array $configurationPages = [
		'MediaWiki:AdiutorRequestPageProtection.json' => AdiutorDummyConfig::PAGE_PROTECTION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorCreateSpeedyDeletion.json' => AdiutorDummyConfig::SPEEDY_DELETION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorDeletionPropose.json' => AdiutorDummyConfig::DELETION_PROPOSE_CONFIGURATION,
		'MediaWiki:AdiutorRequestPageMove.json' => AdiutorDummyConfig::PAGE_MOVE_CONFIGURATION,
		'MediaWiki:AdiutorArticleTagging.json' => AdiutorDummyConfig::ARTICLE_TAGGING_CONFIGURATION,
		'MediaWiki:AdiutorReportRevision.json' => AdiutorDummyConfig::REPORT_REVISION_CONFIGURATION,
	];

	public function execute() {
		foreach ( $this->configurationPages as $pageTitle => $content ) {
			$this->createPage( $pageTitle, $content );
		}
	}

	/**
	 * Creates a new page with the given title and content.
	 *
	 * @param string $pageTitle The title of the new page.
	 * @param array $content The content of the new page.
	 *
	 * @return void
	 */
	public function createPage( string $pageTitle, array $content ) {
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$systemUserName = 'AdiutorBot';
		$user = User::newSystemUser( $systemUserName, [ 'steal' => true ] );
		if ( !$user ) {
			return;
		}
		global $wgReservedUsernames;
		if ( !in_array( $systemUserName, (array)$wgReservedUsernames ) ) {
			$wgReservedUsernames[] = $systemUserName;
		}
		$title = $titleFactory->newFromText( $pageTitle );
		if ( !$title->exists() ) {
			$pageContent = json_encode( $content, JSON_PRETTY_PRINT );
			$pageUpdater = $services->getWikiPageFactory()->newFromTitle( $title )->newPageUpdater( $user );
			$pageUpdater->setContent( SlotRecord::MAIN, new TextContent( $pageContent ) );
			$pageUpdater->saveRevision( CommentStoreComment::newUnsavedComment(
				Message::newFromKey( 'adiutor-create-localization-initial-content' ) ), EDIT_INTERNAL |
				EDIT_AUTOSUMMARY );
			$saveStatus = $pageUpdater->getStatus();
			if ( !$saveStatus->isGood() ) {
				$logger = LoggerFactory::getInstance( 'Adiutor' );
				$logger->warning( 'Failed to create configuration page', [
					'pageTitle' => $pageTitle,
					'pageContent' => $pageContent,
					'saveStatus' => $saveStatus,
				] );
			}
		}
	}
}
