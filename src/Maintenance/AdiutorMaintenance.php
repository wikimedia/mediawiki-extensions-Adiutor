<?php
/**
 * Class AdiutorMaintenance
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
use MediaWiki\Revision\SlotRecord;
use TextContent;
use TitleFactory;
use User;

class AdiutorMaintenance extends Maintenance {

	private array $configurationPages = [
		'MediaWiki:AdiutorRequestPageProtection.json' => AdiutorDummyConfig::PAGE_PROTECTION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorCreateSpeedyDeletion.json' => AdiutorDummyConfig::SPEEDY_DELETION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorDeletionPropose.json' => AdiutorDummyConfig::DELETION_PROPOSE_CONFIGURATION,
		'MediaWiki:AdiutorRequestPageMove.json' => AdiutorDummyConfig::PAGE_MOVE_CONFIGURATION,
		'MediaWiki:AdiutorArticleTagging.json' => AdiutorDummyConfig::ARTICLE_TAGGING_CONFIGURATION,
		'MediaWiki:AdiutorReportRevision.json' => AdiutorDummyConfig::REPORT_REVISION_CONFIGURATION,
	];

	public function getConfigurationPages(): array {
		return $this->configurationPages;
	}

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Creates and saves configuration pages for Adiutor if they do not already exist.' );
	}

	public function execute() {
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$systemUserName = 'AdiutorBot';
		$user =
			User::newSystemUser( $systemUserName,
				[ 'steal' => true ] );
		if ( !$user ) {
			return;
		}

		global $wgReservedUsernames;
		if ( !in_array( $systemUserName,
			(array)$wgReservedUsernames ) ) {
			$wgReservedUsernames[] = $systemUserName;
		}

		foreach ( $this->configurationPages as $pageTitle => $content ) {
			$this->createPage( $titleFactory,
				$pageTitle,
				$content,
				$user,
				$services );
		}
	}

	/**
	 * Creates a new page with the given title, content, user, and services.
	 *
	 * @param TitleFactory $titleFactory The factory for creating page titles.
	 * @param string $pageTitle The title of the new page.
	 * @param array $content The content of the new page.
	 * @param User $user The user creating the page.
	 * @param MediaWikiServices $services The services required for creating the page.
	 *
	 * @return void
	 */
	public function createPage( TitleFactory $titleFactory, string $pageTitle, array $content, User $user,
		MediaWikiServices $services ) {
		$title = $titleFactory->newFromText( $pageTitle );
		if ( !$title->exists() ) {
			$pageContent =
				json_encode( $content, JSON_PRETTY_PRINT );
			$pageUpdater = $services->getWikiPageFactory()->newFromTitle( $title )->newPageUpdater( $user );
			$pageUpdater->setContent( SlotRecord::MAIN, new TextContent( $pageContent ) );
			$pageUpdater->saveRevision( CommentStoreComment::newUnsavedComment(
				'Initial content for Adiutor localization file' ),
				EDIT_INTERNAL | EDIT_AUTOSUMMARY );
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
