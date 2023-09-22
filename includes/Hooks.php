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
            $out->addHtml('<div id="adiutor-container"></div>');
            $out->addModules('ext.Adiutor');
        }
    }

    public static function onPageContentSaveComplete() {
        return true;
    }
}