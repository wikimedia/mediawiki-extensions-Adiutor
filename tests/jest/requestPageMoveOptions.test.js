/**
 * requestPageMoveOptions.test.js
 * This file contains tests for the RequestPageMoveOptions Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object for accessing MediaWiki's global variables and services.
global.mw = {
	config: {
		get: jest.fn().mockImplementation( ( key ) => {
			switch ( key ) {
			case 'wgAdiutorRequestPageMove':
				return {
					noticeBoardTitle: 'MockNoticeBoardTitle',
					addNewSection: 'MockAddNewSection',
					useExistSection: 'MockUseExistingSection',
					sectionTitle: 'MockSectionTitle',
					sectionId: 123,
					contentPattern: 'MockPattern',
					apiPostSummary: 'MockAPISummary',
					textModificationDirection: 'prependtext',
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
const RequestPageMoveOptions = require( '../../resources/ext.adiutor/components/requestPageMoveOptions.vue' );
describe( 'RequestPageMoveOptions Component Tests', () => {

	const wrapper = mount( RequestPageMoveOptions );

	it( 'Should exist RequestPageMoveOptions', () => {
		expect( wrapper.exists() ).toBe( true );
	} );

	// Test to verify initial data properties
	it( 'Initialize with the correct elements', () => {
		expect( wrapper.vm.noticeBoardTitle ).toBe( 'MockNoticeBoardTitle' );
		expect( wrapper.vm.addNewSection ).toBe( 'MockAddNewSection' );
	} );

	it( 'Should correctly save when saveConfiguration is called', async () => {
		await wrapper.vm.saveConfiguration();
		expect( mw.message ).toHaveBeenCalledWith( 'adiutor-save-configurations' );
	} );

} );
