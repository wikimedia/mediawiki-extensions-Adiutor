/**
 * articleTaggingOptions.test.js
 * This file contains tests for the ArticleTaggingOptions Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object for accessing MediaWiki's global variables and services.
global.mw = {
	config: {
		get: jest.fn().mockImplementation( ( key ) => {
			switch ( key ) {
			case 'wgAdiutorArticleTagging':
				return {
					tagList: [ {
						name: 'MockMessage',
						value: 'MockMessage'
					} ],
					useMultipleIssuesTemplate: 'MockUseMultipleIssuesTemplate',
					multipleIssuesTemplate: 'MockMultipleIssuesTemplate',
					uncategorizedTemplate: 'MockSectionTemplate',
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
const ArticleTaggingOptions = require( '../../resources/ext.adiutor/components/articleTaggingOptions.vue' );
describe( 'ArticleTaggingOptions Component Tests', () => {

	const wrapper = mount( ArticleTaggingOptions );

	it( 'Should exist ArticleTaggingOptions', () => {
		expect( wrapper.exists() ).toBe( true );
	} );

	// Test to verify initial data properties
	it( 'Initialize with the correct elements', () => {
		expect( wrapper.vm.tagList ).toEqual( [
			{
				name: 'MockMessage',
				value: 'MockMessage'
			}
		] );
		expect( wrapper.vm.useMultipleIssuesTemplate ).toBe( 'MockUseMultipleIssuesTemplate' );
		expect( wrapper.vm.multipleIssuesTemplate ).toBe( 'MockMultipleIssuesTemplate' );
		expect( wrapper.vm.uncategorizedTemplate ).toBe( 'MockSectionTemplate' );
		expect( wrapper.vm.apiPostSummary ).toBe( 'MockAPISummary' );
		expect( wrapper.vm.moduleEnabled ).toBe( false );
		expect( wrapper.vm.testMode ).toBe( false );
		expect( wrapper.vm.namespaces ).toEqual( [ 0, 1 ] );
	} );

	it( 'Should correctly save when saveConfiguration is called', async () => {
		await wrapper.vm.saveConfiguration();
		expect( mw.message ).toHaveBeenCalledWith( 'adiutor-save-configurations' );
	} );

} );
