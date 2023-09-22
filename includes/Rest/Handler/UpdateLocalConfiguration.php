<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use MediaWiki\Rest\SimpleHandler;

class UpdateLocalConfiguration extends SimpleHandler {
    public function run() {
        $rawJson = file_get_contents('php://input');
        $jsonData = json_decode($rawJson);
        $configurationData = $jsonData->configuration;
        $jsonFilePath = __DIR__ . "../../../../resources/ext.Adiutor/localization/Rpp.json";
        $updatedJson = json_encode($configurationData, JSON_PRETTY_PRINT);
        $result = file_put_contents($jsonFilePath, $updatedJson);
        
        if ($result !== false) {
            // Successfully wrote the JSON file
            return ['status' => 'success'];
        } else {
            // Failed to write the JSON file
            return ['status' => 'error'];
        }
    }
}
