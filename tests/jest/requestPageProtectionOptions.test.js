/**
 * requestPageProtectionOptions.test.js
 * This file contains tests for the RequestPageProtectionOptions Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object for accessing MediaWiki's global variables and services.
global.mw = {
    config: {
        get: jest.fn().mockImplementation( ( key ) => {
            switch ( key ) {
            case 'wgAdiutorRequestPageProtection':
                return {
                    protectionDurations: 'MockProtectionDurations',
                    protectionTypes: 'MockProtectionTypes',
                    noticeBoardTitle: 'MockNoticeBoardTitle',
                    addNewSection: 'MockAddNewSection',
                    sectionTitle: 'MockSectionTitle',
                    useExistSection: 'MockUseExistingSection',
                    sectionId: 123,
                    textModificationDirection: 'prependtext',
                    contentPattern: 'MockPattern',
                    apiPostSummary: 'MockAPISummary',
                    moduleEnabled: false,
                    testMode: false,
                    namespaces: [ 0, 1 ]
                };
            case 'wgUserGroups':
                return [ 'interface-admin' ];
            default:
                return null;
            }
        } )
    },
    user: {
        tokens: {
            get: jest.fn().mockReturnValue( 'mock-csrf-token' )
        }
    },
    message: jest.fn().mockImplementation( () => ( {
        text: jest.fn().mockReturnValue( 'MockMessage' )
    } ) ),
    Api: jest.fn( () => ( {
        postWithToken: jest.fn( () => Promise.resolve() )
    } ) ),
    msg: jest.fn( () => 'translated message' ),
    notify: jest.fn( () => ( {
        title: undefined,
        type: 'success'
    } ) )
};
const RequestPageProtectionOptions = require( '../../resources/ext.adiutor/components/requestPageProtectionOptions.vue' );

describe( 'RequestPageProtectionOptions Component Tests', () => {

    const wrapper = mount( RequestPageProtectionOptions );

    it( 'Should exist RequestPageProtectionOptions', () => {
        expect( wrapper.exists() ).toBe( true );
    } );

    it( 'Initialize with the correct elements', async () => {
        expect( wrapper.vm.noticeBoardTitle ).toBe( 'MockNoticeBoardTitle' );
        expect( wrapper.vm.addNewSection ).toBe( 'MockAddNewSection' );
    } );

    it( 'Should correctly save when saveConfiguration is called', async () => {
        await wrapper.vm.saveConfiguration();
        expect( mw.message ).toHaveBeenCalledWith( 'adiutor-save-configurations' );
    } );

} );
