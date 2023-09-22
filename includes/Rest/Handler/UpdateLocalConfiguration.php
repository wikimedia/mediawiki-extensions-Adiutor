<?php

namespace MediaWiki\Extension\Adiutor\Rest\Handler;
use MediaWiki\Rest\SimpleHandler;

class UpdateLocalConfiguration extends SimpleHandler {
	public function run() {
		$params = $this->getValidatedParams();
		return [
			'params' => $params
		];
	}
}