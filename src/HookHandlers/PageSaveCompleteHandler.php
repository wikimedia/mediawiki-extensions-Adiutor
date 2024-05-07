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

use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Storage\Hook\PageSaveCompleteHook;

class PageSaveCompleteHandler implements PageSaveCompleteHook {

	private RevisionLookup $revisionLookup;
	private static array $configPages = [
		'MediaWiki:AdiutorRequestPageProtection.json',
		'MediaWiki:AdiutorCreateSpeedyDeletion.json',
		'MediaWiki:AdiutorDeletionPropose.json',
		'MediaWiki:AdiutorRequestPageMove.json',
		'MediaWiki:AdiutorArticleTagging.json',
		'MediaWiki:AdiutorReportRevision.json'
	];

	public function __construct( RevisionLookup $revisionLookup ) {
		$this->revisionLookup = $revisionLookup;
	}

	/**
	 * Handles page save completion events and updates configuration if necessary.
	 * @inheritDoc
	 */
	public function onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult ) {
		$titleText = $wikiPage->getTitle()->getPrefixedText();
		if ( in_array( $titleText, self::$configPages ) ) {
			$this->processPageSave();
		}
	}

	/**
	 * Processes the page save operation for configuration pages.
	 *
	 * Checks if the saved page is one of the designated configuration pages and, if so,
	 * updates the necessary cache settings and logs the action.
	 *
	 */
	private function processPageSave() {
		// Invalidate cache when the configuration page is updated
		$wanObjectCache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$key = $wanObjectCache->makeKey( 'Adiutor', 'config-data' );
		$wanObjectCache->delete( $key );
	}
}
