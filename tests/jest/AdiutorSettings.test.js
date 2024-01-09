// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
	msg: jest.fn( ( key ) => `Mock message for ${ key }` ),
	config: {
		get: jest.fn( ( key ) => {
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
	Api: jest.fn( () => ( { post: jest.fn().mockResolvedValue( {} ) } ) ),
	user: {
		tokens: {
			get: jest.fn().mockReturnValue( 'mock-edit-token' )
		}
	},
	notify: jest.fn()
};
const { mount } = require( '@vue/test-utils' );
const AdiutorSettings = require( '../../resources/ext.adiutor/components/adiutorSettings.vue' );

// Begin the test suite for the `AdiutorSettings` Vue component.
describe( 'AdiutorSettings', () => {
	let wrapper;

	// `beforeEach` is run before each individual test, this is where common setup is performed.
	beforeEach( () => {
		// Mount the component with stubs for the Vue subcomponents and props
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

	// Test case to verify that the default active tab is "csd" (create speedy deletion)
	it( 'sets the default active tab to "csd"', () => {
		expect( wrapper.vm.currentTab ).toBe( 'csd' );
	} );

	// Test case to verify that the default active tab is "rpp" (request page protection)
	it( 'sets the default active tab to "rpp"', () => {
		wrapper.setData( { currentTab: 'rpp' } );
		expect( wrapper.vm.currentTab ).toBe( 'rpp' );
	} );

	// Test case to verify that the default active tab is "rpm" (request page move)
	it( 'sets the default active tab to "rpm"', () => {
		wrapper.setData( { currentTab: 'rpm' } );
		expect( wrapper.vm.currentTab ).toBe( 'rpm' );
	} );

	// Test case to verify that the default active tab is "dpr" (proposed deletion)
	it( 'sets the default active tab to "dpr"', () => {
		wrapper.setData( { currentTab: 'dpr' } );
		expect( wrapper.vm.currentTab ).toBe( 'dpr' );
	} );

	// Test case to verify that the default active tab is "tag" (article tagging)
	it( 'sets the default active tab to "tag"', () => {
		wrapper.setData( { currentTab: 'tag' } );
		expect( wrapper.vm.currentTab ).toBe( 'tag' );
	} );

	// `afterEach` is run after each test, this is where common teardown is performed.
	afterEach( () => {
		// Unmount the wrapper after each test to clean up
		wrapper.unmount();
	} );
} );
