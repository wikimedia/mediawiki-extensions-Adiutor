<?php

/**
 * Adiutor Special page.
 *
 * @file
 */

namespace MediaWiki\Extension\Adiutor;

use Message;
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
		$this->templateParser = new TemplateParser( __DIR__ . '/./Templates' );
		parent::__construct( 'AdiutorSettings' );
	}

	/**
	 * Returns the description of the AdiutorSettings object.
	 *
	 * @return Message The description of the AdiutorSettings object.
	 */
	public function getDescription(): Message {
		return $this->msg( 'adiutor-settings' );
	}

	/**
	 * Shows the page to the user.
	 *
	 * @param string $subPage The subpage string argument (if any).
	 */
	public function execute( $subPage ) {
		$out = $this->getOutput();
		$out->setPageTitle( $this->msg( 'adiutor-settings' ) );
		$out->addModules( 'ext.adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'AdiutorSettings', [] ) );
	}
}
