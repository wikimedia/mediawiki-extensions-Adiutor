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
        $deletionProposeConfiguration = '{
            "standardProposeTemplate": "{{subst:Proposed deletion|concern=$2}}",
            "livingPersonProposeTemplate": "{{Prod blp/dated|concern=$2|user=$6|timestamp=$3 $4 $5|help=off}}",
            "apiPostSummary": "Proposed deletion of the page per [[WP:PROD]]",
            "apiPostSummaryforCreator": "Proposed deletion of [[$1|$1]] per [[WP:PROD]]",
            "apiPostSummaryforLog": "Logging the page [[$1|$1]] as a pending deletion candidate per [[WP:PROD]].",
            "prodNotificationTemplate": "{{subst:Proposed deletion notify|$1}}"
        }';
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
        $requestPageMoveConfiguration = '{
            "noticeBoardTitle": "Wikipedia:Page move requests",
            "addNewSection": false,
            "sectionTitle": "Page move request for [[$1]]",
            "useExistSection": false,
            "sectionId": 1,
            "textModificationDirection": "appendtext",
            "contentPattern": "Move [[$1]] to [[$2]]\n\nRationale: $3 ~~~~",
            "apiPostSummary": "Page move request created for [[$1|$1]]."
        }';
        $requestRevisionDeletionConfiguration = '{
            "noticeBoardTitle": "Wikipedia:Revision deletion/Noticeboard",
            "addNewSection": false,
            "sectionTitle": "Revision deletion request for [[$1]]",
            "useExistSection": false,
            "sectionId": 1,
            "textModificationDirection": "appendtext",
            "contentPattern": "*\'\'\'Revision:\'\'\' [[Special:Diff/$1]]\n*\'\'\'Rationale:\'\'\' $2\n*\'\'\'Comment:\'\'\' $3 ~~~~\n*\'\'\'Outcome:\'\'\'",
            "apiPostSummary": "Created a revision deletion request for [[$1]] page."
        }';
        $articleTaggingConfiguration = '{
            "tagList": [
                {
                    "label": "Cleanup and maintenance tags",
                    "tags": [
                        {
                            "tag": "Cleanup ",
                            "description": "Article needs cleanup",
                            "items": [
                                {
                                    "name": "cleanup",
                                    "parameter": "reason",
                                    "type": "input",
                                    "label": "Reason for cleanup:",
                                    "required": false
                                }
                            ]
                        },
                        {
                            "tag": "Cleanup rewrite",
                            "description": "Article needs to be rewritten to comply with Wikipedia\'s style guidelines"
                        },
                        {
                            "tag": "External links",
                            "description": "Some external links used in the article do not comply with policies"
                        },
                        {
                            "tag": "Copypaste",
                            "description": "Article may have been copied from another source that potentially violates copyright policy",
                            "items": [
                                {
                                    "name": "copypaste",
                                    "parameter": "url",
                                    "type": "input",
                                    "label": "Source URL:"
                                }
                            ]
                        }
                    ]
                },
                {
                    "label": "General content issues",
                    "tags": [
                        {
                            "tag": "Notability",
                            "description": "Subject of the article may not be notable",
                            "items": [
                                {
                                    "name": "notability",
                                    "parameter": "1",
                                    "type": "select",
                                    "items": [
                                        {
                                            "label": "Notability: subject does not meet the following criteria",
                                            "value": "none",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|biography: notability for biography articles",
                                            "value": "biography",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|book: notability for book articles",
                                            "value": "book",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|company: notability for company articles",
                                            "value": "company",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|music: notability for music articles",
                                            "value": "Music",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|internet: notability for internet articles",
                                            "value": "internet",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|film: notability for film articles",
                                            "value": "Movie",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|theatre: notability for theatre articles",
                                            "value": "theatre",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|school: notability for school articles",
                                            "value": "school",
                                            "type": "checkbox"
                                        },
                                        {
                                            "label": "Notability|television: notability for television articles",
                                            "value": "television",
                                            "type": "checkbox"
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "tag": "Advert",
                            "description": "Article written like an advertisement"
                        },
                        {
                            "tag": "Cleanup tense",
                            "description": "Article does not follow guidelines on use of different tenses."
                        },
                        {
                            "tag": "Essay-like",
                            "description": "Article is written like a personal opinion or essay"
                        },
                        {
                            "tag": "Fanpov",
                            "description": "Article is written from a fan\'s point of view"
                        },
                        {
                            "tag": "Guide-like",
                            "description": "Article is written like a handbook or guidebook"
                        },
                        {
                            "tag": "Inappropriate person",
                            "description": "uses first-person or second-person inappropiately"
                        },
                        {
                            "tag": "Manual",
                            "description": "Article is written like a manual or guidebook"
                        },
                        {
                            "tag": "Technical",
                            "description": "Article is too technical for most readers to understand"
                        },
                        {
                            "tag": "Tone",
                            "description": "Article\'s tone does not adhere to the formal and serious tone expected in an encyclopedia"
                        },
                        {
                            "tag": "Context",
                            "description": "Article insufficient context for those unfamiliar with the subject"
                        },
                        {
                            "tag": "Confusing",
                            "description": "Article contains content that is confusing to readers or has unclear expressions"
                        },
                        {
                            "tag": "Fiction",
                            "description": "Article\'s style and content do not clearly distinguish between fact and fiction"
                        },
                        {
                            "tag": "Cleanup-PR",
                            "description": "Article is incomplete in a specific area",
                            "items": [
                                {
                                    "name": "MissingTopic",
                                    "parameter": "1",
                                    "type": "input",
                                    "label": "Missing topic:",
                                    "help": "Specify the missing topic. Required."
                                }
                            ]
                        },
                        {
                            "tag": "Expert",
                            "description": "Article requires input from experts to improve",
                            "items": [
                                {
                                    "name": "expertNeeded",
                                    "parameter": "wikiproject",
                                    "type": "input",
                                    "label": "Relevant WikiProject:",
                                    "help": "Optionally enter the name of a WikiProject. Do not include the \'WikiProject\' prefix."
                                },
                                {
                                    "name": "expertNeededReason",
                                    "parameter": "reason",
                                    "type": "input",
                                    "label": "Reason:",
                                    "help": "A brief explanation of the issue that requires expert input. A reason or discussion link is required."
                                },
                                {
                                    "name": "expertNeededTalk",
                                    "parameter": "discussion",
                                    "type": "input",
                                    "label": "Discussion:",
                                    "help": "The section name on the discussion page of the article where the issue is being discussed. Do not enter a link, just the section name. A reason or discussion link is required."
                                }
                            ]
                        },
                        {
                            "tag": "Undue",
                            "description": "Article gives undue weight to various opinions, events, or controversies"
                        },
                        {
                            "tag": "Prediction",
                            "description": "Article makes predictions about future events that may not have happened yet"
                        },
                        {
                            "tag": "Current",
                            "description": "Article is about a current event"
                        },
                        {
                            "tag": "Current person",
                            "description": "This person is associated with a current event"
                        },
                        {
                            "tag": "Update",
                            "description": "Article needs to be updated for accuracy and reliability"
                        },
                        {
                            "tag": "Hoax",
                            "description": "Article may be entirely fraudulent"
                        },
                        {
                            "tag": "Globalize",
                            "description": "Article does not reflect a global perspective on the subject",
                            "items": [
                                {
                                    "name": "globalizeRegion",
                                    "parameter": "1",
                                    "type": "input",
                                    "label": "Region reflected in bias"
                                }
                            ]
                        },
                        {
                            "tag": "Autobiography",
                            "description": "Article is an autobiography or has been extensively edited by someone closely connected to the subject"
                        },
                        {
                            "tag": "POV",
                            "description": "This article may be biased"
                        },
                        {
                            "tag": "Controversial",
                            "description": "Article is currently the subject of ongoing controversies"
                        },
                        {
                            "tag": "Paid",
                            "description": "Article may have been created or edited for payment, such as conflicts of interest"
                        },
                        {
                            "tag": "De-localize",
                            "description": "Information in the article has been viewed from the perspective of a specific region"
                        },
                        {
                            "tag": "Primary sources",
                            "description": "Article lacks reliable sources from reputable publications"
                        },
                        {
                            "tag": "Additional sources",
                            "description": "Article needs additional sources for verification"
                        },
                        {
                            "tag": "Unsourced",
                            "description": "Article lacks any references or sources"
                        },
                        {
                            "tag": "Original research",
                            "description": "Article contains original research, unverifiable claims, or editorial opinions"
                        },
                        {
                            "tag": "Self-published",
                            "description": "Article contains inappropriate references to self-published sources"
                        },
                        {
                            "tag": "Single source",
                            "description": "Article relies entirely or predominantly on a single source"
                        },
                        {
                            "tag": "Third-party sources",
                            "description": "Article relies too much on third-party sources closely associated with the subject"
                        }
                    ]
                },
                {
                    "label": "Specific content issues",
                    "tags": [
                        {
                            "tag": "Merge",
                            "description": "Article closely resembles another article and should be merged"
                        },
                        {
                            "tag": "History merge",
                            "description": "The histories of this article and another article need to be merged",
                            "items": [
                                {
                                    "name": "histmergeOriginalPage",
                                    "parameter": "1",
                                    "type": "input",
                                    "label": "Other page:",
                                    "help": "Name of the page that needs to be merged (required)."
                                },
                                {
                                    "name": "histmergeReason",
                                    "parameter": "reason",
                                    "type": "input",
                                    "label": "Reason:",
                                    "help": "A brief explanation of why the merger of histories is necessary."
                                }
                            ]
                        },
                        {
                            "tag": "Work",
                            "description": "There is ongoing work on this page"
                        }
                    ]
                }
            ],
            "useMultipleIssuesTemplate": true,
            "multipleIssuesTemplate": "Multiple issues",
            "uncategorizedTemplate": "Uncategorized",
            "apiPostSummary": "Page has been tagged"
        }';
        $pageContent = [
            'MediaWiki:AdiutorRequestPageProtection.json' => $requestPageProtectionConfiguration,
            'MediaWiki:AdiutorCreateSpeedyDeletion.json' => $createSpeedyDeletionRequestConfgiuration,
            'MediaWiki:AdiutorDeletionPropose.json' => $deletionProposeConfiguration,
            'MediaWiki:AdiutorRequestPageMove.json' => $requestPageMoveConfiguration,
            'MediaWiki:AdiutorRequestRevisionDeletion.json' => $requestRevisionDeletionConfiguration,
            'MediaWiki:AdiutorArticleTagging.json' => $articleTaggingConfiguration,
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
