<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;

use MediaWiki\Rest\Validator\JsonBodyValidator;
use MediaWiki\Rest\Validator\UnsupportedContentTypeBodyValidator;
use MediaWiki\Rest\SimpleHandler;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * Example class to update local adiutor module configurations
 */
class UpdateLocalConfigurationHandler extends SimpleHandler
{
	public function run()
	{
		$jsonData = $this->getValidatedBody();

		// Extract the 'configuration' property from the JSON data
		$configurationData = $jsonData['configuration'];
		$moduleName = $jsonData['module'];
		// Define the path to the JSON file to be updated
		$jsonFilePath = __DIR__ . "../../../../resources/ext.Adiutor/localization/$moduleName.json";

		// Convert the updated configuration data back to JSON with pretty-printing
		$updatedJson = json_encode($configurationData, JSON_PRETTY_PRINT);

		// Write the updated JSON data back to the specified file
		$result = file_put_contents($jsonFilePath, $updatedJson);

		// Check if the write operation was successful
		if ($result !== false) {
			// If successful, return a success status
			return ['status' => 'success', 'message' => $jsonData['configuration']];
		} else {
			// If not successful, return an error status
			return ['status' => 'error'];
		}
	}

	public function getBodyValidator($contentType)
	{
		if ($contentType === 'application/json') {
			return new JsonBodyValidator(
				[
					'module' => [
						ParamValidator::PARAM_TYPE => 'string',
						ParamValidator::PARAM_REQUIRED => true,
					],
					'configuration' => [
						ParamValidator::PARAM_TYPE => 'string',
						ParamValidator::PARAM_REQUIRED => true,
					],
				]
			);
		}
		return new UnsupportedContentTypeBodyValidator($contentType);
	}
}
