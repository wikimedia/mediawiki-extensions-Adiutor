<?php
/**
 * Hooks for Adiutor extension.
 *
 * @file
 */

namespace MediaWiki\Extension\Adiutor;

use MediaWiki\MediaWikiServices;
use Title;
use WikiPage;

class Hooks implements \MediaWiki\Hook\BeforePageDisplayHook
{
    /**
     * Customizations to OutputPage right before page display.
     *
     * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
     * @param OutputPage $out
     * @param Skin $skin
     */
    public function onBeforePageDisplay($out, $skin): void
    {
        global $wgAdiutorEnable;

        if ($wgAdiutorEnable) {
            // Load our module on all pages
            $out->addModules('ext.Adiutor');
        }
    }
}
