/**
 * requestPageProtection.test.js
 * This file contains tests for the RequestPageProtection Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
    config: {
        get: jest.fn().mockImplementation( ( key ) => {
            const configMap = {
                wgAdiutorRequestPageProtection: {
                    noticeBoardTitle: 'Mock Notice Board',
                    protectionDurations: [
                        { text: 'Temporary', value: 'temp' },
                        { text: 'Permanent', value: 'perm' }
                    ],
                    protectionTypes: [
                        { text: 'Full', value: 'full' },
                        { text: 'Semi', value: 'semi' }
                    ],
                    sectionTitle: 'Section Title',
                    contentPattern: 'Content pattern with $1, $2, $3, $4',
                    apiPostSummary: 'API post summary with $1'
                },
                wgPageName: 'Mock_Page_Name'
            };
            return configMap[ key ];
        } ),
        set: jest.fn()
    },
    Api: jest.fn( () => ( {
        postWithToken: jest.fn( () => Promise.resolve() )
    } ) ),
    util: {
        addPortletLink: jest.fn(),
        getUrl: jest.fn()
    },
    msg: jest.fn( () => 'translated message' )
};

const RequestPageProtection = require( '../../resources/ext.adiutor/components/requestPageProtection.vue' );

describe( 'RequestPageProtection', () => {
    it( 'initializes with default values', () => {
        const wrapper = mount( RequestPageProtection );
        expect( wrapper.vm.openRppDialog ).toBe( true );
        expect( wrapper.vm.primaryAction.label ).toBe( 'translated message' );
    } );

    it( 'creates an API request when requestPageProtection is called', async () => {

        const postWithTokenMock = jest.fn( () => Promise.resolve() );
        mw.Api = jest.fn( () => ( {
            postWithToken: postWithTokenMock
        } ) );

        const wrapper = mount( RequestPageProtection );
        wrapper.vm.durationSelection = 'temp';
        wrapper.vm.typeSelection = 'full';
        wrapper.vm.rationaleInput = 'Rationale for the protection';

        await wrapper.vm.requestPageProtection();

        expect( postWithTokenMock ).toHaveBeenCalled();
    } );
} );
