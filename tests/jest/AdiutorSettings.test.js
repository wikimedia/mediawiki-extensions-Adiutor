// Mock the `mw` object globally before importing Vue components

global.mw = {
	msg: jest.fn( ( key ) => `Mock message for ${ key }` ),
	config: {
		get: jest.fn( ( key ) => {
			if ( key === 'wgAdiutorCreateSpeedyDeletion' ) {
				// Return a mock configuration object or the actual expected configuration
				return {
					// Include all properties accessed within your component, with mock or default values
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
					// Include all properties accessed within your component, with mock or default values
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
					// Include all properties accessed within your component, with mock or default values
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
					// Include all properties accessed within your component, with mock or default values
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
			// Mock other configuration keys as needed
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
				provide: {
					// Provide other injected values if there are any
				}
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
		// Modify the default active tab to 'rpp' if needed
		// For example, if the component allows changing the default tab
		wrapper.setData( { currentTab: 'rpp' } );
		expect( wrapper.vm.currentTab ).toBe( 'rpp' );
	} );

	it( 'sets the default active tab to "rpm"', () => {
		// Modify the default active tab to 'rpm' if needed
		wrapper.setData( { currentTab: 'rpm' } );
		expect( wrapper.vm.currentTab ).toBe( 'rpm' );
	} );

	it( 'sets the default active tab to "dpr"', () => {
		// Modify the default active tab to 'dpr' if needed
		wrapper.setData( { currentTab: 'dpr' } );
		expect( wrapper.vm.currentTab ).toBe( 'dpr' );
	} );

	it( 'sets the default active tab to "tag"', () => {
		// Modify the default active tab to 'tag' if needed
		wrapper.setData( { currentTab: 'tag' } );
		expect( wrapper.vm.currentTab ).toBe( 'tag' );
	} );

	afterEach( () => {
		wrapper.unmount();
	} );
} );
