<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use MediaWiki\MediaWikiServices;
use MediaWiki\Storage\Hook\PageSaveCompleteHook;

class PageSaveCompleteHandler implements PageSaveCompleteHook {

	/**
	 * @inheritDoc
	 */
	public function onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult ) {
		$wanObjectCache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$key = $wanObjectCache->makeKey( 'Adiutor', 'config-data' );
		$wanObjectCache->delete( $key );
	}
}
