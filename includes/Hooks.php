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
            $configFiles = [ 'afd', 'aiv', 'cmr', 'csd', 'das', 'pmr', 'prd', 'rdr', 'rpp', 'sum', 'tag', 'ubm', 'wrn' ];
            foreach ($configFiles as $configFile) {
                $jsonFilePath = __DIR__ . "../../resources/localization/$configFile.json";
                $jsonData = file_get_contents($jsonFilePath);
                $out->addJsConfigVars("{$configFile}Configuration",[json_decode($jsonData)]);
            }
        }
    }
}
