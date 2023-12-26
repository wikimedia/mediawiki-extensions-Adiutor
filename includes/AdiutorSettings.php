<?php

/**
 * Adiutor Special page.
 *
 * @file
 */

namespace MediaWiki\Extension\Adiutor;

use Message;
use PermissionsError;
use SpecialPage;

class AdiutorSettings extends SpecialPage {
	/**
	 * Initialize the special page.
	 */
	public function __construct() {
		parent::__construct( 'AdiutorSettings' );
	}

	/**
	 * Returns the description of the AdiutorSettings object.
	 *
	 * @return Message The description of the AdiutorSettings object.
	 */
	public function getDescription() : Message {
		return Message::newFromKey( 'adiutor-settings' );
	}

	/**
	 * Shows the page to the user.
	 *
	 * @param string $subPage The subpage string argument (if any).
	 *
	 * @throws PermissionsError
	 */
	public function execute( $subPage ) {
		$user = $this->getUser();
		if ( !$user->isAllowed( 'editinterface' ) ) {
			throw new PermissionsError( 'editinterface' );
		}
		$out = $this->getOutput();
		$out->setPageTitle( Message::newFromKey( 'adiutor-settings' ) );
		$out->addHtml( '<div id="adiutor-settings"></div>' );
	}

	/**
	 * @inheritDoc
	 */
	protected function getGroupName() : string {
		return 'other';
	}
}
