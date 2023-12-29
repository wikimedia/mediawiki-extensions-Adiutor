<?php

namespace MediaWiki\Extension\Adiutor;

use Maintenance;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use TextContent;
use User;

class AdiutorMaintenance extends Maintenance {

	private const FLAG_PAGE_TITLE = 'MediaWiki:AdiutorSetupComplete.json';
	private array $configurationPages = [
		'MediaWiki:AdiutorRequestPageProtection.json' => LocalizationConfiguration::REQUEST_PAGE_PROTECTION_CONFIGURATION,
		'MediaWiki:AdiutorCreateSpeedyDeletion.json' => LocalizationConfiguration::CREATE_SPEEDY_DELETION_REQUEST_CONFIGURATION,
		'MediaWiki:AdiutorDeletionPropose.json' => LocalizationConfiguration::DELETION_PROPOSE_CONFIGURATION,
		'MediaWiki:AdiutorRequestPageMove.json' => LocalizationConfiguration::PAGE_MOVE_CONFIGURATION,
		'MediaWiki:AdiutorArticleTagging.json' => LocalizationConfiguration::ARTICLE_TAGGING_CONFIGURATION,
	];

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Creates and saves configuration pages for the Adiutor extension if they do not already exist.' );
	}

	public function execute() {
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$flagTitle = $titleFactory->newFromText( self::FLAG_PAGE_TITLE );
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

		if ( $flagTitle->exists() ) {
			return;
		}

		foreach ( $this->configurationPages as $pageTitle => $content ) {
			$this->createPage( $titleFactory,
				$pageTitle,
				$content,
				$user,
				$services );
		}

		// Create the flag page to indicate that setup has been completed
		$this->createPage( $titleFactory,
			self::FLAG_PAGE_TITLE,
			[ "SetupCompleted" => true ],
			$user,
			$services );
	}

	private function createPage( $titleFactory, $pageTitle, $content, $user, $services ) {
		$title = $titleFactory->newFromText( $pageTitle );
		if ( !$title->exists() ) {
			$pageContent =
				json_encode( $content,
					JSON_PRETTY_PRINT );
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
