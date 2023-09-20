<?php
/**
 * Adiutor Special page.
 *
 * @file
 */

namespace MediaWiki\Extension\Adiutor;

class Afd extends \SpecialPage {

    /**
     * Initialize the special page.
     */
    public function __construct() {
        // A special page should at least have a name.
        // We do this by calling the parent class (the SpecialPage class)
        // constructor method with the name as first and only parameter.
        parent::__construct( 'ArticlesForDeletion' );
    }

    /**
     * Shows the page to the user.
     * @param string $sub The subpage string argument (if any).
     *  [[Special:Adiutor/subpage]].
     */
    public function execute( $sub ) {
        $out = $this->getOutput();

        $out->setPageTitle( $this->msg( 'afd-module-title' ) );

        // Parses message from .i18n.php as wikitext and adds it to the
        // page output.
        $out->addHtml('<div id="adiutor-article-for-deletion">This is the content of the element.</div>');
    }

    /** @inheritDoc */
    protected function getGroupName() {
        return 'other';
    }
}