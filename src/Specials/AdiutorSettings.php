<?php
namespace MediaWiki\Extension\Adiutor\Specials;

use Message;
use OutputPage;
use SpecialPage;
use TemplateParser;

class AdiutorSettings extends SpecialPage {

	/**
	 * @var TemplateParser
	 */
	private TemplateParser $templateParser;

	/**
	 * @var OutputPage|null
	 */
	private ?OutputPage $output = null;

	/**
	 * Initialize the special page.
	 */
	public function __construct() {
		$this->templateParser = new TemplateParser( __DIR__ . '/../Templates' );
		parent::__construct( 'AdiutorSettings' );
	}

	/**
	 * Set the OutputPage object for testing purposes.
	 *
	 * @param OutputPage $outputPage The mocked OutputPage object.
	 */
	public function setOutput( OutputPage $outputPage ) {
		$this->output = $outputPage;
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
		$out = $this->output ?? $this->getOutput();
		$out->setPageTitle( $this->msg( 'adiutor-settings' ) );
		$out->addModules( 'ext.adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'AdiutorSettings', [] ) );
	}
}
