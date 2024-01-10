/**
 * articleTagging.test.js
 * This file contains tests for the articleTagging Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
	config: {
		get: jest.fn( ( key ) => {
			// Mock configuration as needed for the tests
			const configMap = {
				wgAdiutorArticleTagging: {
					tagList: [
						// Mock tag list items as needed for the tests
						{
							label: 'Label1',
							tags: [
								{ tag: 'Tag1', description: 'Description1' }
							]
						}
					]
				},
				wgPageName: 'Mock_Page_Name'
			};
			return configMap[ key ];
		} )
	},
	Api: jest.fn( () => ( {
		postWithToken: jest.fn( () => Promise.resolve() )
	} ) ),
	notify: jest.fn(),
	msg: jest.fn( () => 'translated message' )
};

const ArticleTagging = require( '../../resources/ext.adiutor/components/articleTagging.vue' );

// Begin the test suite for the `ArticleTagging` Vue component.
describe( 'ArticleTagging', () => {
	it( 'initializes with default values', () => {
		const wrapper = mount( ArticleTagging );
		expect( wrapper.vm.openTagDialog ).toBe( true );
		expect( wrapper.vm.primaryAction.label ).toBe( 'translated message' );
	} );

	it( 'filters tag list based on search term', () => {
		// Mock data for the test
		const tagList = [
			{
				label: 'Label1',
				tags: [
					{
						tag: 'Tag1',
						description: 'Description1',
						items: []
					},
					{
						tag: 'Tag2',
						description: 'Description2',
						items: []
					}
				]
			},
			{
				label: 'Label2',
				tags: [
					{
						tag: 'Tag3',
						description: 'Description3',
						items: []
					},
					{
						tag: 'Tag4',
						description: 'Description4',
						items: []
					}
				]
			}
		];

		// Mock configuration with the tag list
		global.mw.config.get = jest.fn( () => ( {
			wgAdiutorArticleTagging: {
				tagList: tagList
			}
		} ) );

	} );

	it( 'notifies when tagArticle is called with no selected tags', async () => {
		const wrapper = mount( ArticleTagging );
		// Simulate calling tagArticle without selecting any tags
		await wrapper.vm.tagArticle();

		// Check if the mw.notify method was called with the expected parameters
		expect( mw.notify ).toHaveBeenCalledWith( 'translated message', {
			title: 'translated message',
			type: 'error'
		} );
	} );

	it( 'creates an API request when tagArticle is called', async () => {
		// Since createApiRequest is called within createApiRequest, we check the mw.Api().postWithToken call.
		const postWithTokenMock = jest.fn( () => Promise.resolve() );
		mw.Api = jest.fn( () => ( {
			postWithToken: postWithTokenMock
		} ) );

		const wrapper = mount( ArticleTagging );
		// Simulate selecting a tag
		wrapper.vm.selectedTags = [
			{
				tag: 'Tag1',
				description: 'Description1',
				items: []
			}
		];

		await wrapper.vm.tagArticle(); // Call the method that should internally call createApiRequest

	} );

} );
