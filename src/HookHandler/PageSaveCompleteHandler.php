<?php

namespace MediaWiki\Extension\Adiutor\HookHandler;

use MediaWiki\MediaWikiServices;

class PageSaveCompleteHandler {
	public static function onPageSaveComplete(
		$wikiPage, $user, $summary, $flags, $revisionRecord, $editResult
	) {
		$wanObjectCache = MediaWikiServices::getInstance()->getService( 'WANObjectCache' );
		$key =
			$wanObjectCache->makeKey( 'Adiutor',
				'config-data' );
		$wanObjectCache->delete( $key );
	}
}
