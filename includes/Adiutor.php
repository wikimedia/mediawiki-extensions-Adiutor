<?php
/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\Adiutor;

use MediaWiki\MediaWikiServices;
use Title;
use WikiPage;

class AdiutorExtension
{
    /**
     * Initialize the extension.
     */
    public static function onExtensionLoad()
    {
        // Define an associative array where the keys are page titles and the values are the default content
        $pageContent = [
            'Adiutor-CSD.json' => '{"key": "value1"}',
            'Adiutor-PMR.json' => '{"key": "value2"}',
            'Adiutor-AIV.json' => '{"key": "value3"}',
            // Add other pages with their respective content
        ];

        $services = MediaWikiServices::getInstance();

        foreach ($pageContent as $pageTitle => $content) {
            // Check if the page already exists
            $title = Title::newFromText($pageTitle);
            if (!$title->exists()) {
                $page = WikiPage::factory($title);
                $page->doEdit($content, 'Initial content', EDIT_NEW);
            }
        }

        error_log('AdiutorExtension.php executed');
    }
}