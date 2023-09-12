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
            $out->addModules('ext.Adiutor');

            Hooks::getCsdConfiguration();
        }
    }

    /**
     * Fire the "Adiutor::getCsdConfiguration" hook.
     *
     */
    public static function getCsdConfiguration(): void
    {
        $jsonFilePath = __DIR__ . '../../resources/localization/csd.json';
        $jsonData = file_get_contents($jsonFilePath);
        \Hooks::run('Adiutor::getCsdConfiguration', [$jsonData]);
    }
}
