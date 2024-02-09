<?php

use MediaWiki\Extension\Adiutor\Maintenance\UpdateConfiguration;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}

require_once "$IP/maintenance/Maintenance.php";
require_once __DIR__ . "/../src/Maintenance/UpdateConfiguration.php";

$maintClass = UpdateConfiguration::class;
require_once RUN_MAINTENANCE_IF_MAIN;
