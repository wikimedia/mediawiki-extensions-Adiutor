<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Storage\EditResult;
use User;
use WikiPage;

class PageSaveCompleteHandler {

	/**
	 * This method is called after a page is saved in MediaWiki.
	 * It deletes a specific cache key from the main WAN object cache.
	 *
	 * @param WikiPage $wikiPage The WikiPage object representing the saved page.
	 * @param User $user The User object representing the user who saved the page.
	 * @param string $summary The summary of the page edit.
	 * @param int $flags Flags indicating additional information about the save operation.
	 * @param RevisionRecord $revisionRecord The RevisionRecord object representing the saved revision.
	 * @param EditResult $editResult The EditResult object representing the result of the save operation.
	 *
	 * @return void
	 */
	public static function onPageSaveComplete(
		WikiPage $wikiPage, User $user, string $summary, int $flags, RevisionRecord $revisionRecord, EditResult $editResult
	) {
		$wanObjectCache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$key = $wanObjectCache->makeKey( 'Adiutor', 'config-data' );
		$wanObjectCache->delete( $key );
	}
}
