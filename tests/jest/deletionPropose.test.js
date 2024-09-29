/**
 * proposeDeletion.test.js
 * This file contains tests for the proposeDeletion Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
    config: {
        get: jest.fn( ( key ) => {
            if ( key === 'wgAdiutorDeletionPropose' ) {
                return {
                    standardProposeTemplate: 'mock_template_1',
                    livingPersonProposeTemplate: 'mock_template_2',
                    apiPostSummaryForCreator: 'mock_summary_1',
                    prodNotificationTemplate: 'mock_notification_template',
                    proposedDeletionPolicy: 'mock_policy_url',
                    proposedDeletionPolicyShortcut: 'mock_policy_shortcut',
                    proposedDeletionOfBiographiesOfLivingPeoplePolicy: 'mock_policy_url_2',
                    proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut: 'mock_policy_shortcut_2',
                    apiPostSummary: 'mock_summary_2'
                };
            }
            if ( key === 'wgArticleId' ) {
                return 123;
            }
            if ( key === 'wgPageName' ) {
                return 'Mock page name';
            }
            if ( key === 'wgUserGroups' ) {
                return [ 'mock_group_1', 'mock_group_2' ];
            }
            if ( key === 'wgUserName' ) {
                return 'Mock user name';
            }
            if ( key === 'wgWikiID' ) {
                return 'Mock wiki ID';
            }
            if ( key === 'wgMonthNames' ) {
                return [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
            }
            if ( key === 'wgServer' ) {
                return 'http://localhost';
            }
            if ( key === 'wgArticlePath' ) {
                return '/wiki/$1';
            }
            return null;
        } )
    },
    Api: jest.fn( () => ( {
        postWithToken: jest.fn( () => Promise.resolve() )
    } ) ),
    notify: jest.fn(),
    msg: jest.fn( () => 'translated message' )
};

const deletionPropose = require( '../../resources/ext.adiutor/components/deletionPropose.vue' );

describe( 'ProposeDeletion', () => {
    const wrapper = mount( deletionPropose );
    it( 'initializes with default values', () => {
        expect( wrapper.vm.openPrdDialog ).toBe( true );
    } );
} );
