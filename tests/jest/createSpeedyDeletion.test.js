/**
 * createSpeedyDeletion.test.js
 * This file contains tests for the proposeDeletion Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
    config: {
        get: jest.fn().mockImplementation( ( key ) => {
            if ( key === 'wgAdiutorCreateSpeedyDeletion' ) {
                return {
                    speedyDeletionReasons: [],
                    copyVioReasonValue: ' ',
                    postfixReasonUsage: '',
                    multipleReasonSeparation: '',
                    csdTemplateStartSingleReason: '',
                    csdTemplateStartMultipleReason: '',
                    singleReasonSummary: '',
                    multipleReasonSummary: '',
                    speedyDeletionPolicyPageShortcut: '',
                    csdNotificationTemplate: '',
                    namespaceDeletionReasons: []
                };
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

const createSpeedyDeletion = require( '../../resources/ext.adiutor/components/createSpeedyDeletion.vue' );

describe( 'CreateSpeedyDeletion', () => {
    const wrapper = mount( createSpeedyDeletion );
    it( 'initializes with default values', () => {
        expect( wrapper.vm.openCsdDialog ).toBe( true );
    } );
} );
