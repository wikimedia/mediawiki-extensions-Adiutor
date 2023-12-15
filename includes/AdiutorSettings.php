<?php

/**
 * Adiutor Special page.
 *
 * @file
 */

namespace MediaWiki\Extension\Adiutor;

class AdiutorSettings extends \SpecialPage
{
    /**
     * Initialize the special page.
     */
<<<<<<< HEAD   (4c6e3d Add .gitreview)
    public function __construct() {
        // A special page should at least have a name.
        // We do this by calling the parent class (the SpecialPage class)
        // constructor method with the name as first and only parameter.
        parent::__construct( 'AdiutorSettings' );
=======
    public function __construct()
    {
        parent::__construct('AdiutorSettings');
>>>>>>> CHANGE (02af75 Merge branch 'bugfix/vikipolimer')
    }

<<<<<<< HEAD   (4c6e3d Add .gitreview)
=======
    public function getDescription()
    {
        // return $this->msg( 'adiutor-settings' );
        return 'Adiutor settings';
    }

>>>>>>> CHANGE (02af75 Merge branch 'bugfix/vikipolimer')
    /**
     * Shows the page to the user.
     * @param string $sub The subpage string argument (if any).
     *  [[Special:Adiutor/subpage]].
     */
<<<<<<< HEAD   (4c6e3d Add .gitreview)
    public function execute( $sub ) {
        // Get the current user
=======
    public function execute($sub)
    {
>>>>>>> CHANGE (02af75 Merge branch 'bugfix/vikipolimer')
        $user = $this->getUser();
        if (!$user->isAllowed('editinterface')) {
            throw new \PermissionsError('editinterface');
        }

        $out = $this->getOutput();
<<<<<<< HEAD   (4c6e3d Add .gitreview)

        $out->setPageTitle( $this->msg( 'adiutor-settings' ) );

        // Parses message from .i18n.php as wikitext and adds it to the
        // page output.
=======
        $out->setPageTitle($this->msg('adiutor-settings'));
>>>>>>> CHANGE (02af75 Merge branch 'bugfix/vikipolimer')
        $out->addHtml('<div id="adiutor-settings"></div>');
    }

<<<<<<< HEAD   (4c6e3d Add .gitreview)
    /** @inheritDoc */
    protected function getGroupName() {
=======
    protected function getGroupName()
    {
>>>>>>> CHANGE (02af75 Merge branch 'bugfix/vikipolimer')
        return 'other';
    }
}
