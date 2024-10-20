<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.1.0
 */

namespace MediaWiki\Extension\Adiutor\Specials;

use Message;
use OutputPage;
use SpecialPage;
use TemplateParser;

class AdiutorSettings extends SpecialPage {

	private TemplateParser $templateParser;

	private ?OutputPage $output = null;

	public function __construct() {
		$this->templateParser = new TemplateParser( __DIR__ . '/../Templates' );
		parent::__construct( 'AdiutorSettings' );
	}

	/**
	 * Set the OutputPage object for testing purposes.
	 *
	 * @since 0.1.0
	 * @param OutputPage $outputPage The mocked OutputPage object.
	 */
	public function setOutput( OutputPage $outputPage ) {
		$this->output = $outputPage;
	}

	/**
	 * Returns the description of the AdiutorSettings object.
	 *
	 * @since 0.1.0
	 * @return Message The description of the AdiutorSettings object.
	 */
	public function getDescription(): Message {
		return $this->msg( 'adiutor-settings' );
	}

	/**
	 * Shows the page to the user.
	 *
	 * @since 0.1.0
	 * @param string $subPage The subpage string argument (if any).
	 */
	public function execute( $subPage ) {
		$out = $this->output ?? $this->getOutput();
		$out->setPageTitleMsg( $this->msg( 'adiutor-settings' ) );
		$out->addModules( 'ext.adiutor' );
		$out->addHTML( $this->templateParser->processTemplate( 'AdiutorSettings', [] ) );
	}
}
