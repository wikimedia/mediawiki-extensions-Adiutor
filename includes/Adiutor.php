<?php
/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\Adiutor;
use MediaWiki\Content\Content;
use TextContent;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Storage\PageUpdater;
use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\MediaWikiServices;
use User;

class Adiutor
{
    public static function onExtensionLoad()
    {
        $pageContent = [
            'MediaWiki:Adiutor-CSD.json' => '1',
            'MediaWiki:Adiutor-PMR.json' => '2',
            'MediaWiki:Adiutor-AIV.json' => '3',
        ];
        $user = User::newFromId(0);
        $services = MediaWikiServices::getInstance();
        $titleFactory = $services->getTitleFactory();
        foreach ($pageContent as $pageTitle => $content) {
            $pageUpdater = MediaWikiServices::getInstance()
                ->getWikiPageFactory()
                ->newFromTitle($titleFactory->newFromText($pageTitle) )
                ->newPageUpdater( $user );
            $pageUpdater->setContent( SlotRecord::MAIN, new TextContent($content) );
            $pageUpdater->saveRevision(
                CommentStoreComment::newUnsavedComment( 'Initial content for Adiutor localization file' ),
                EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY
            );
            $saveStatus = $pageUpdater->getStatus();
        }
    }
}