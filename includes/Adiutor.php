<?php
/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

 namespace MediaWiki\Extension\Adiutor;

 use MediaWiki\MediaWikiServices;
 use Title;
 
 class Adiutor
 {
     public static function onExtensionLoad()
     {
         $pageContent = [
             'Adiutor-CSD.json' => '{"key": "value1"}',
             'Adiutor-PMR.json' => '{"key": "value2"}',
             'Adiutor-AIV.json' => '{"key": "value3"}',
         ];
 
         $services = MediaWikiServices::getInstance();
 
         foreach ($pageContent as $pageTitle => $content) {
             $title = Title::newFromText($pageTitle);
             if (!$title->exists()) {
                 $user = $services->getUserFactory()->newAnonymous();
                 $updater = $services->getRevisionStore()->newPageUpdater($title, $user);
                 $updater->setContent(ContentHandler::makeContent($content, $title));
                 $summary = 'Initial content';
                 $updater->setEditSummary($summary);
                 $updater->saveRevision();
                 $services->getMainWANObjectCache()->delete($title->getPrefixedDBKey());
             }
         }
     }
 }