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
 * @since 0.1.0
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

/**
 * This class is responsible for creating and saving configuration pages for the Adiutor extension
 * if they do not already exist. It extends the Maintenance class.
 */
class UpdateConfiguration extends Maintenance {

	private const CONFIGURATION_PAGES = [
		'MediaWiki:AdiutorRequestPageProtection.json' => AdiutorDummyConfig::PAGE_PROTECTION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorCreateSpeedyDeletion.json' => AdiutorDummyConfig::SPEEDY_DELETION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorDeletionPropose.json' => AdiutorDummyConfig::DELETION_PROPOSE_CONFIGURATION,
		'MediaWiki:AdiutorRequestPageMove.json' => AdiutorDummyConfig::PAGE_MOVE_CONFIGURATION,
		'MediaWiki:AdiutorArticleTagging.json' => AdiutorDummyConfig::ARTICLE_TAGGING_CONFIGURATION,
		'MediaWiki:AdiutorReportRevision.json' => AdiutorDummyConfig::REPORT_REVISION_CONFIGURATION,
	];

	public function __construct() {
		parent::__construct();
		$this->requireExtension( 'Adiutor' );
	}

	public function execute() {
		foreach ( self::CONFIGURATION_PAGES as $pageTitle => $content ) {
			$this->createPage( $pageTitle, $content );
		}
	}

	/**
	 * Creates a new configuration page if it does not already exist.
	 *
	 * This method handles the creation of a MediaWiki page with the specified title and JSON content.
	 * It uses the AdiutorBot system user to perform the creation. If the page already exists,
	 * the method outputs a message indicating that no action was taken.
	 *
	 * Steps:
	 * - Check if the page already exists.
	 * - If not, create the page using the specified content.
	 * - Log any failures in creation.
	 *
	 * @since 0.1.0
	 * @param string $pageTitle The title of the page to be created.
	 * @param array $content The content of the page in the form of a JSON-encoded array.
	 * @return void Outputs messages regarding the success or failure of the page creation.
	 */
	public function createPage( string $pageTitle, array $content ) {
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$systemUserName = 'AdiutorBot';
		$user = User::newSystemUser( $systemUserName, [ 'steal' => true ] );
		if ( !$user ) {
			$this->error( "Could not create or obtain system user: $systemUserName.\n" );

			return;
		}

		global $wgReservedUsernames;
		if ( !in_array( $systemUserName, (array)$wgReservedUsernames ) ) {
			$wgReservedUsernames[] = $systemUserName;
		}

		$title = $titleFactory->newFromText( $pageTitle );
		if ( $title->exists() ) {
			$this->output( "Page '$pageTitle' already exists and was not created.\n" );

			return;
		}

		$pageContent = json_encode( $content, JSON_PRETTY_PRINT );
		$pageUpdater = $services->getWikiPageFactory()->newFromTitle( $title )->newPageUpdater( $user );
		$pageUpdater->setContent( SlotRecord::MAIN, new TextContent( $pageContent ) );
		$pageUpdater->saveRevision(
			CommentStoreComment::newUnsavedComment(
				Message::newFromKey( 'adiutor-create-localization-initial-content' )
			),
			EDIT_INTERNAL
		);

		$saveStatus = $pageUpdater->getStatus();
		if ( !$saveStatus->isGood() ) {
			$logger = LoggerFactory::getInstance( 'Adiutor' );
			$logger->warning( 'Failed to create configuration page', [
				'pageTitle' => $pageTitle,
				'pageContent' => $pageContent,
				'saveStatus' => $saveStatus,
			] );
			$this->error( "Failed to create page '$pageTitle'.\n" );
		} else {
			$this->output( "Page '$pageTitle' created successfully.\n" );
		}
	}
}
