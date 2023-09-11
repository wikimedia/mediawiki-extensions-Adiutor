<?php
/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\Adiutor;

use MediaWiki\MediaWikiServices;
use MediaWiki\Page\WikiPage;
use MediaWiki\Slot\SlotRecord;
use MediaWiki\Title\Title;
use MediaWiki\Content\ContentHandlerFactory;
use MediaWiki\Permissions\PermissionManager;
use User;

class Adiutor
{
    public static function onExtensionLoad()
    {
        $pageContent = [
            'Adiutor-CSD.json' => '{"key": "value1"}',
            'Adiutor-PMR.json' => '{"key": "value2"}',
            'Adiutor-AIV.json' => '{"key": "value3"}',
        ];
        $user = User::newFromId(0); 
        foreach ($pageContent as $pageTitle => $content) {
            $pageUpdater = MediaWikiServices::getInstance()
                ->getWikiPageFactory()
                ->newFromTitle( $pageTitle )
                ->newPageUpdater( $user );
            $pageUpdater->setContent( SlotRecord::MAIN, $content );
            $pageUpdater->saveRevision(
                CommentStoreComment::newUnsavedComment( '' ),
                EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY
            );
        }
    }
}