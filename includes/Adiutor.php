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
        $folderPath = __DIR__ . '/resources/localization/';

        // Initialize an empty array to store page content
        $pageContent = [];

        // Get a list of JSON files in the folder
        $jsonFiles = glob($folderPath . '*.json');

        $user = User::newFromId(0);
        $services = MediaWikiServices::getInstance();
        $titleFactory = $services->getTitleFactory();
        foreach ($jsonFiles as $jsonFile) {
            $pageTitle = basename($jsonFile);
            $content = file_get_contents($jsonFile);
            $pageContent[$pageTitle] = $content;
            $pageUpdater = MediaWikiServices::getInstance()
                ->getWikiPageFactory()
                ->newFromTitle($titleFactory->newFromText($pageTitle))
                ->newPageUpdater($user);
            
            // Pass the content as a string and set the content model to JSON
            $textContent = new TextContent($content);
            
            $pageUpdater->setContent(SlotRecord::MAIN, $textContent);
            $pageUpdater->saveRevision(
                CommentStoreComment::newUnsavedComment('Initial content for Adiutor localization file'),
                EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY
            );
            $saveStatus = $pageUpdater->getStatus();
        }
    }
}