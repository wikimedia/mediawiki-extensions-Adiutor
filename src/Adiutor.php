<?php

/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\Adiutor;

use MediaWiki\Extension\Adiutor\Maintenance\AdiutorMaintenance;

class Adiutor {

	/**
	 * @var AdiutorMaintenance|null
	 */
	private static ?AdiutorMaintenance $maintenance = null;

	/**
	 * Entry point for the extension's "on load" hook
	 */
	public static function onExtensionLoad() {
		if ( self::$maintenance === null ) {
			self::$maintenance = new AdiutorMaintenance();
		}
		// Run the configuration setup or other tasks
		self::$maintenance->execute();
	}

	/**
	 * Used for injecting dependencies e.g. during testing
	 *
	 * @param AdiutorMaintenance|null $maintenance Maintenance object to set
	 */
	public static function setMaintenance( AdiutorMaintenance $maintenance = null ) {
		self::$maintenance = $maintenance;
	}
}
