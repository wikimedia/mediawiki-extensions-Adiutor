<?php

namespace MediaWiki\Extension\Adiutor;

use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\MediaWikiServices;
use FormatJson;
use OutputPage;
use Skin;
use StatusValue;
use Title;

class Hooks
{
    /**
     * Hook handler for BeforePageDisplay hook.
     *
     * @param OutputPage $out
     * @param Skin $skin
     */
    public static function onBeforePageDisplay($out, $skin): void
    {
        global $wgAdiutorEnable;

        if ($wgAdiutorEnable) {
            // Load our module on all pages
            $out->addHtml('<div id="adiutor-container"></div>');
            $out->addModules('ext.Adiutor');
            $configPages = [
                [
                    'title' => 'MediaWiki:AdiutorCreateSpeedyDeletion.json',
                    'configuration' => 'AdiutorCreateSpeedyDeletion'
                ],
                [
                    'title' => 'MediaWiki:AdiutorRequestPageProtection.json',
                    'configuration' => 'AdiutorRequestPageProtection'
                ]
            ];

            $services = MediaWikiServices::getInstance();

            foreach ($configPages as $configPage) {
                $title = Title::newFromText($configPage['title']);
                $rev = $services->getRevisionLookup()->getRevisionByTitle($title);
                $content = $rev->getContent(SlotRecord::MAIN, RevisionRecord::FOR_PUBLIC);
                $configuration = FormatJson::decode($content->getText(), FormatJson::FORCE_ASSOC);
                $out->addJsConfigVars([$configPage['configuration'] => $configuration]);
            }
        }
    }
}
