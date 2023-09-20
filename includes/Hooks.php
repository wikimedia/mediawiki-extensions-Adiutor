<?php

namespace MediaWiki\Extension\Adiutor;

class Hooks {
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
        }
    }
}
