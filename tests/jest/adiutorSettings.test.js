/**
 * adiutorSettings.test.js
 * This file contains tests for the adiutorSettings Vue component.
 */
const { mount } = require( '@vue/test-utils' );
const punycode = require( 'punycode/' );
global.punycode = punycode;
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
    msg: jest.fn( () => 'Mock message' ),
    config: {
        get: jest.fn().mockImplementation( ( key ) => {
            if ( key === 'wgAdiutorCreateSpeedyDeletion' ) {
                return {
                    speedyDeletionReasons: [],
                    csdTemplateStartSingleReason: '',
                    csdTemplateStartMultipleReason: '',
                    speedyDeletionPolicyLink: '',
                    speedyDeletionPolicyPageShortcut: '',
                    csdNotificationTemplate: '',
                    singleReasonSummary: '',
                    multipleReasonSummary: '',
                    copyVioReasonValue: '',
                    postfixReasonUsage: '',
                    moduleEnabled: false,
                    testMode: false,
                    namespaces: []
                };
            }
            if ( key === 'wgAdiutorRequestPageProtection' ) {
                return {
                    requestProtectionReasons: [],
                    requestProtectionTemplate: '',
                    requestProtectionPolicyLink: '',
                    requestProtectionPolicyPageShortcut: '',
                    requestProtectionNotificationTemplate: '',
                    requestProtectionSummary: '',
                    moduleEnabled: false,
                    testMode: false,
                    namespaces: []
                };
            }
            if ( key === 'wgAdiutorRequestPageMove' ) {
                return {
                    requestMoveReasons: [],
                    requestMoveTemplate: '',
                    requestMovePolicyLink: '',
                    requestMovePolicyPageShortcut: '',
                    requestMoveNotificationTemplate: '',
                    requestMoveSummary: '',
                    moduleEnabled: false,
                    testMode: false,
                    namespaces: []
                };
            }
            if ( key === 'wgAdiutorDeletionPropose' ) {
                return {
                    deletionProposeReasons: [],
                    deletionProposeTemplate: '',
                    deletionProposePolicyLink: '',
                    deletionProposePolicyPageShortcut: '',
                    deletionProposeNotificationTemplate: '',
                    deletionProposeSummary: '',
                    moduleEnabled: false,
                    testMode: false,
                    namespaces: []
                };
            }
            if ( key === 'wgAdiutorArticleTagging' ) {
                return {
                    articleTaggingReasons: [],
                    articleTaggingTemplate: '',
                    articleTaggingPolicyLink: '',
                    articleTaggingPolicyPageShortcut: '',
                    articleTaggingNotificationTemplate: '',
                    articleTaggingSummary: '',
                    moduleEnabled: false,
                    testMode: false,
                    namespaces: []
                };
            }
            return null;
        } )
    },
    Api: jest.fn( () => ( {
        postWithToken: jest.fn( () => Promise.resolve() )
    } ) ),
    user: {
        tokens: {
            get: jest.fn().mockReturnValue( 'mock-edit-token' )
        }
    },
    notify: jest.fn()
};

const AdiutorSettings = require( '../../resources/ext.adiutor/components/adiutorSettings.vue' );

describe( 'AdiutorSettings', () => {
    let wrapper;

    beforeEach( () => {

        wrapper = mount( AdiutorSettings, {
            global: {
                stubs: {
                    createSpeedyDeletionOptions: true,
                    requestPageProtectionOptions: true,
                    requestPageMoveOptions: true,
                    deletionProposeOptions: true,
                    articleTaggingOptions: true
                },
                provide: {}
            },
            props: {
                framed: true
            }
        } );
    } );

    it( 'sets the default active tab to "csd"', () => {
        expect( wrapper.vm.currentTab ).toBe( 'csd' );
    } );

    it( 'sets the default active tab to "rpp"', () => {
        wrapper.setData( { currentTab: 'rpp' } );
        expect( wrapper.vm.currentTab ).toBe( 'rpp' );
    } );

    it( 'sets the default active tab to "rpm"', () => {
        wrapper.setData( { currentTab: 'rpm' } );
        expect( wrapper.vm.currentTab ).toBe( 'rpm' );
    } );

    it( 'sets the default active tab to "dpr"', () => {
        wrapper.setData( { currentTab: 'dpr' } );
        expect( wrapper.vm.currentTab ).toBe( 'dpr' );
    } );

    it( 'sets the default active tab to "tag"', () => {
        wrapper.setData( { currentTab: 'tag' } );
        expect( wrapper.vm.currentTab ).toBe( 'tag' );
    } );

    afterEach( () => {
        wrapper.unmount();
    } );
} );
