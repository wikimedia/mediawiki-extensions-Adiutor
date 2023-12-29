<?php
/**
 * This method is called when the Adiutor extension is loaded.
 * It creates and saves configuration pages for the extension if they do not already exist.
 * The configuration pages are defined in the $configurationPages array.
 * Each page is checked if it already exists, and if not, a new page is created with the specified content.
 * The content is encoded as JSON and saved as the main slot content of the page.
 * The method uses MediaWiki's PageUpdater to create and save the pages.
 * The user used for creating the pages is $userFactory::newAnonymous(0), which is the default anonymous user.
 * After saving each page, the save status is stored in the $saveStatus variable.
 * The LocalizationConfiguration class contains constants for deletion propose configuration,
 * page protection configuration, speedy deletion request configuration, page move configuration,
 * revision deletion configuration, and article tagging configuration.
 */

namespace MediaWiki\Extension\Adiutor;

use Maintenance;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use TextContent;
use User;

class AdiutorMaintenance extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Creates and saves configuration pages for the Adiutor extension if they do not already exist.' );
	}

	public function execute() {
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$systemUserName = 'Adiutor bot';
		$user =
			User::newSystemUser( $systemUserName,
				[ 'steal' => true ] );
		if ( !$user ) {
			return;
		}

		global $wgReservedUsernames;
		if ( !in_array( $systemUserName,
			(array) $wgReservedUsernames ) ) {
			$wgReservedUsernames[] = $systemUserName;
		}

		$configurationPages = [
			'MediaWiki:AdiutorRequestPageProtection.json' => LocalizationConfiguration::REQUEST_PAGE_PROTECTION_CONFIGURATION,
			'MediaWiki:AdiutorCreateSpeedyDeletion.json' => LocalizationConfiguration::CREATE_SPEEDY_DELETION_REQUEST_CONFIGURATION,
			'MediaWiki:AdiutorDeletionPropose.json' => LocalizationConfiguration::DELETION_PROPOSE_CONFIGURATION,
			'MediaWiki:AdiutorRequestPageMove.json' => LocalizationConfiguration::PAGE_MOVE_CONFIGURATION,
			'MediaWiki:AdiutorArticleTagging.json' => LocalizationConfiguration::ARTICLE_TAGGING_CONFIGURATION,
		];

		foreach ( $configurationPages as $pageTitle => $content ) {
			$title = $titleFactory->newFromText( $pageTitle );
			$pageContent =
				json_encode( $content,
					JSON_PRETTY_PRINT );

			// Check if the page already exists
			if ( !$title->exists() ) {
				$pageUpdater = $services->getWikiPageFactory()->newFromTitle( $title )->newPageUpdater( $user );
				$pageUpdater->setContent( SlotRecord::MAIN,
					new TextContent( $pageContent ) );
				$pageUpdater->saveRevision( CommentStoreComment::newUnsavedComment( 'Initial content for Adiutor localization file' ),
					EDIT_INTERNAL | EDIT_AUTOSUMMARY );
				$saveStatus = $pageUpdater->getStatus();
				if ( !$saveStatus->isGood() ) {
					wfLogWarning( 'Adiutor: Failed to create configuration page',
						[
							'pageTitle' => $pageTitle,
							'pageContent' => $pageContent,
							'saveStatus' => $saveStatus,
						] );
				}
			}
		}
	}
}
