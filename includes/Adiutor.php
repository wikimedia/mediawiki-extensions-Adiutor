<?php
/**
 * Entry point for Adiutor extension.
 *
 * @file
 * @ingroup Extensions
 */

namespace MediaWiki\Extension\Adiutor;

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Content\Content;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Storage\PageUpdater;
use MediaWiki\MediaWikiServices;
use TextContent;
use User;

class Adiutor
{
    public static function onExtensionLoad()
    {
        $requestPageProtectionConfiguration = '{
            "protectionDurations": [
                {
                    "value": "temporary",
                    "data": "Temporary",
                    "label": "Temporary"
                },
                {
                    "value": "indefinite",
                    "data": "Indefinite",
                    "label": "Indefinite"
                }
            ],
            "protectionTypes": [
                {
                    "value": "semi-protection",
                    "data": "Semi-protection",
                    "label": "Semi-protection"
                },
                {
                    "value": "full-protection",
                    "data": "Full protection",
                    "label": "Full protection"
                }
            ],
            "noticeBoardTitle": "Wikipedia:Requests for page protection",
            "addNewSection": false,
            "sectionTitle": "Protection request for [[$1|$1]]",
            "useExistSection": false,
            "sectionId": 1,
            "textModificationDirection": "appendtext",
            "contentPattern": "Protection Type: $2 $3 Rationale: $4 ~~~~",
            "apiPostSummary": "Protection requested for [[$1|$1]]"
        }';
        $createSpeedyDeletionRequestConfgiuration = '{
            "speedyDeletionReasons": [
                {
                    "name": "Articles",
                    "namespace": 0,
                    "reasons": [
                        {
                            "namespace": 0,
                            "value": "A1",
                            "data": "[[WP:CSD#A1|A1]]: Short article without enough context to identify the subject",
                            "selected": false,
                            "label": "A1 - No context.",
                            "help": "Short article without enough context to identify the subject"
                        },
                        {
                            "namespace": 0,
                            "value": "A2",
                            "data": "[[WP:CSD#A2|A2]]: Article in a foreign language that exists on another project",
                            "selected": false,
                            "label": "A2 - Article in a foreign language",
                            "help": "Article in a foreign language that exists on another project"
                        },
                        {
                            "namespace": 0,
                            "value": "A3",
                            "data": "[[WP:CSD#A3|A3]]: No content",
                            "selected": false,
                            "label": "A3 - No content",
                            "help": "Article in a foreign language that exists on another project"
                        }
                    ]
                },
                {
                    "name": "Files",
                    "namespace": 6,
                    "reasons": [
                        {
                            "namespace": 6,
                            "value": "F1",
                            "data": "[[WP:CSD#F1|CSD:F1]]: Test file",
                            "selected": false,
                            "label": "F1 - File uploaded for test",
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "Categories",
                    "namespace": 14,
                    "reasons": [
                        {
                            "data": "[[WP:CSD#C1|CSD:C1]]: Test category",
                            "value": "C1",
                            "selected": false,
                            "label": "C1 - Category created for test",
                            "namespace": 14,
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "User pages",
                    "namespace": 2,
                    "reasons": [
                        {
                            "data": "[[WP:CSD#U1|CSD:U1]]: Test user page",
                            "value": "U1",
                            "selected": false,
                            "label": "U1 - User page created for test",
                            "namespace": 2,
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "Templates",
                    "namespace": 10,
                    "reasons": [
                        {
                            "data": "[[WP:CSD#T1|CSD:T1]]: Test template",
                            "value": "T1",
                            "selected": false,
                            "label": "T1 - Template created for test",
                            "namespace": 10,
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "Portals",
                    "namespace": 100,
                    "reasons": [
                        {
                            "data": "[[WP:CSD#T1|CSD:P1]]: Test portal",
                            "value": "P1",
                            "selected": false,
                            "label": "P1 - Portal created for test",
                            "namespace": 100,
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "General",
                    "namespace": "general",
                    "reasons": [
                        {
                            "data": "[[WP:CSD#G1|G1]]: [[WP:PN|Patent nonsense]], meaningless, or incomprehensible",
                            "value": "G1",
                            "selected": false,
                            "label": "G1 -  Patent nonsense",
                            "namespace": "general",
                            "help": "This is a help text about the criteria"
                        },
                        {
                            "data": "[[WP:CSD#G2|G2]]: Test page",
                            "value": "G2",
                            "selected": false,
                            "label": "G2 - Test page",
                            "namespace": "general",
                            "help": "This is a help text about the criteria"
                        },
                        {
                            "data": "[[WP:CSD#G3|G3]]: [[WP:Vandalism|Vandalism]]",
                            "value": "G3",
                            "selected": false,
                            "label": "G3 - Pure vandalism",
                            "namespace": "general",
                            "help": "This is a help text about the criteria"
                        },
                        {
                            "data": "[[WP:CSD#G4|G4]]: Unambiguous [[WP:CV|copyright infringement]]",
                            "value": "G4",
                            "selected": false,
                            "label": "G4 - Unambiguous copyright infringement",
                            "namespace": "general",
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "Redirects",
                    "namespace": "redirect",
                    "reasons": [
                        {
                            "data": "[[WP:CSD#R1|CSD:R1]]: A test redirect",
                            "value": "R1",
                            "selected": false,
                            "label": "R1 - A test redirect",
                            "namespace": "redirect",
                            "help": "This is a help text about the criteria"
                        }
                    ]
                },
                {
                    "name": "Other",
                    "namespace": "other",
                    "reasons": [
                        {
                            "data": "[[Other|other]]: reason",
                            "value": "01",
                            "selected": false,
                            "label": "Other reason",
                            "namespace": "other",
                            "help": "This is a help text about the criteria"
                        }
                    ]
                }
            ],
            "csdTemplateStartSingleReason": "{{Db-",
            "csdTemplateStartMultipleReason": "{{Db-multiple",
            "postfixReasonUsage": "value",
            "multipleReasonSeparation": "vertical_bar",
            "speedyDeletionPolicyLink": "Wikipedia:Criteria_for_speedy_deletion",
            "speedyDeletionPolicyPageShortcut": "WP:CSD",
            "apiPostSummaryforTalkPage": "[[WP:CSD#G1]]: Discussion page of the deleted article",
            "apiPostSummaryforLog": "Log record of the speedy deletion request of [[$1]] is being logged.",
            "csdNotificationTemplate": "{{subst:CSD-Notification|1=$1|2=$2}}",
            "singleReasonSummary": "Requesting speedy deletion for the page per $2.",
            "multipleReasonSummary": "Requesting speedy deletion for the page per $2.",
            "copyVioReasonValue": "G4"
        }';
        $pageContent = [
            'MediaWiki:AdiutorRequestPageProtection.json' => $requestPageProtectionConfiguration,
            'MediaWiki:AdiutorCreateSpeedyDeletion.json' => $createSpeedyDeletionRequestConfgiuration,
        ];
        $user = User::newFromId(0);
        $services = MediaWikiServices::getInstance();
        $titleFactory = $services->getTitleFactory();
        foreach ($pageContent as $pageTitle => $content) {
            $title = $titleFactory->newFromText($pageTitle);
            // Check if the page already exists
            if (!$title->exists()) {
                $pageUpdater = MediaWikiServices::getInstance()
                    ->getWikiPageFactory()
                    ->newFromTitle($title)
                    ->newPageUpdater($user);
                $pageUpdater->setContent(SlotRecord::MAIN, new TextContent($content));
                $pageUpdater->saveRevision(
                    CommentStoreComment::newUnsavedComment('Initial content for Adiutor localization file'),
                    EDIT_INTERNAL | EDIT_MINOR | EDIT_AUTOSUMMARY
                );
                $saveStatus = $pageUpdater->getStatus();
            }
        }
    }
}
