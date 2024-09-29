/**
 * requestPageMove.test.js
 * This file contains tests for the RequestPageProtection Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
    config: {
        get: jest.fn().mockImplementation( ( key ) => {
            const configMap = {
                wgAdiutorRequestPageMove: {
                    noticeBoardTitle: 'Mock Notice Board',
                    addNewSection: true,
                    sectionTitle: 'Section Title',
                    useExistSection: false,
                    sectionId: '',
                    textModificationDirection: 'appendtext',
                    contentPattern: 'Content pattern with $1, $2, $3',
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

const RequestPageMove = require( '../../resources/ext.adiutor/components/requestPageMove.vue' );

describe( 'RequestPageMove', () => {
    it( 'initializes with default values', () => {
        const wrapper = mount( RequestPageMove );
        expect( wrapper.vm.openRpmDialog ).toBe( true );
        expect( wrapper.vm.primaryAction.label ).toBe( 'translated message' );
    } );

    it( 'creates an API request when requestPageMove is called', async () => {

        const postWithTokenMock = jest.fn( () => Promise.resolve() );
        mw.Api = jest.fn( () => ( {
            postWithToken: postWithTokenMock
        } ) );

        const wrapper = mount( RequestPageMove );
        wrapper.vm.newPageName = 'New Mock Page Name';
        wrapper.vm.rationaleInput = 'Rationale for the move';

        await wrapper.vm.requestPageMove();

        expect( postWithTokenMock ).toHaveBeenCalled();
    } );
} );
