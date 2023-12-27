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
use TemplateParser;

class AdiutorSettings extends SpecialPage {

	/**
	 * @var TemplateParser
	 */
	private TemplateParser $templateParser;

	/**
	 * Initialize the special page.
	 */
	public function __construct() {
		$this->templateParser = new TemplateParser( __DIR__ . '/./templates' );
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
		$templateVars = [];
		$out->addModuleStyles( [ 'ext.Adiutor.styles' ] );
		$out->addModules( 'ext.Adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'Adiutor',
			$templateVars ) );
	}

	/**
	 * @inheritDoc
	 */
	protected function getGroupName() : string {
		return 'other';
	}
}
