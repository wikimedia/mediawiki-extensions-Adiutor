<?php
namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use MediaWiki\Rest\SimpleHandler;
use MediaWiki\Rest\Validator\JsonBodyValidator;
use Wikimedia\ParamValidator\ParamValidator;
class UpdateLocalConfigurationHandler extends SimpleHandler
{
    public function run()
    {
        // Read the raw JSON data from the incoming request body
        $rawJson = file_get_contents('php://input');

        $jsonBodyValidator = new JsonBodyValidator([
            'configuration' => [
                ParamValidator::PARAM_TYPE => 'string', 
            ],
        ]);

        // Parse the JSON data into a PHP object
        $jsonData = json_decode($rawJson);

        // Extract the 'configuration' property from the JSON data
        $configurationData = $jsonData->configuration;

        // Define the path to the JSON file to be updated
        $jsonFilePath = __DIR__ . "../../../../resources/ext.Adiutor/localization/$jsonData->module.json";

        // Convert the updated configuration data back to JSON with pretty-printing
        $updatedJson = json_encode($configurationData, JSON_PRETTY_PRINT);

        // Write the updated JSON data back to the specified file
        $result = file_put_contents($jsonFilePath, $updatedJson);

        // Check if the write operation was successful
        if ($result !== false)
        {
            // If successful, return a success status
            return ['status' => 'success', 'message' => $jsonBodyValidator->configuration];
        }
        else
        {
            // If not successful, return an error status
            return ['status' => 'error'];
        }
    }
}