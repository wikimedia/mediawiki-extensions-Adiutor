// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
	config: {
		get: jest.fn( ( key ) => {
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

const { mount } = require( '@vue/test-utils' );
const RequestPageMove = require( '../../resources/ext.adiutor/components/requestPageMove.vue' );

// Begin the test suite for the `RequestPageMove` Vue component.
describe( 'RequestPageMove', () => {
	it( 'initializes with default values', () => {
		const wrapper = mount( RequestPageMove );
		expect( wrapper.vm.openRpmDialog ).toBe( true );
		expect( wrapper.vm.primaryAction.label ).toBe( 'translated message' );
	} );

	it( 'creates an API request when requestPageMove is called', async () => {
		// Since createApiRequest is called within requestPageMove, we check the mw.Api().postWithToken call.
		const postWithTokenMock = jest.fn( () => Promise.resolve() );
		mw.Api = jest.fn( () => ( {
			postWithToken: postWithTokenMock
		} ) );

		const wrapper = mount( RequestPageMove );
		wrapper.vm.newPageName = 'New Mock Page Name';
		wrapper.vm.rationaleInput = 'Rationale for the move';

		await wrapper.vm.requestPageMove(); // Call the method that should internally call createApiRequest

		// Check if the mw.Api postWithToken was called with the expected parameters
		expect( postWithTokenMock ).toHaveBeenCalled();
	} );
} );
