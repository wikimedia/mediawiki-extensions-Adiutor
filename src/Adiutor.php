<?php

/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\Adiutor;

class Adiutor {
	/**
	 * @see AdiutorMaintenance
	 */
	public static function onExtensionLoad() {
		$maintenance = new AdiutorMaintenance();
		// Run the configuration setup
		$maintenance->execute();
	}
}
