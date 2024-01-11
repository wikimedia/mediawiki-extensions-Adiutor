/**
 * createSpeedyDeletionOptions.test.js
 * This file contains tests for the CreateSpeedyDeletionOptions Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object for accessing MediaWiki's global variables and services.
global.mw = {
	config: {
		get: jest.fn( ( key ) => {
			switch ( key ) {
			case 'wgAdiutorCreateSpeedyDeletion':
				return {
					speedyDeletionReasons: [
						{
							label: 'MockLabel',
							reasons: [
								{ reason: 'MockReason', description: 'MockDescription' }
							]
						}
					],
					csdTemplateStartSingleReason: 'MockCSDTemplateStartSingleReason',
					csdTemplateStartMultipleReason: 'MockCSDTemplateStartMultipleReason',
					postfixReasonUsage: 'MockPostfixReasonUsage',
					multipleReasonSeparation: 'MockMultipleReasonSeparation',
					speedyDeletionPolicyLink: 'MockSpeedyDeletionPolicyLink',
					speedyDeletionPolicyPageShortcut: 'MockSpeedyDeletionPolicyPageShortcut',
					csdNotificationTemplate: 'MockCSDNotificationTemplate',
					singleReasonSummary: 'MockSingleReasonSummary',
					multipleReasonSummary: 'MockMultipleReasonSummary',
					copyVioReasonValue: 'MockCopyVioReasonValue',
					moduleEnabled: true,
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
	message: jest.fn().mockImplementation( () => {
		return {
			text: jest.fn().mockReturnValue( 'MockMessage' )
		};
	} ),
	Api: jest.fn( () => ( {
		postWithToken: jest.fn( () => Promise.resolve() )
	} ) ),
	msg: jest.fn( () => 'translated message' ),
	notify: jest.fn( () => {
		return {
			title: undefined,
			type: 'success'
		};
	} )
};
const CreateSpeedyDeletionOptions = require( '../../resources/ext.adiutor/components/createSpeedyDeletionOptions.vue' );
describe( 'CreateSpeedyDeletionOptions Component Tests', () => {

	const wrapper = mount( CreateSpeedyDeletionOptions );

	it( 'Should exist CreateSpeedyDeletionOptions', () => {
		expect( wrapper.exists() ).toBe( true );
	} );

	// Test to verify initial data properties
	it( 'Initialize with the correct elements', () => {
		expect( wrapper.vm.speedyDeletionReasons ).toEqual( [
			{
				label: 'MockLabel',
				reasons: [
					{ reason: 'MockReason', description: 'MockDescription' }
				]
			}
		] );
		expect( wrapper.vm.csdTemplateStartSingleReason ).toBe( 'MockCSDTemplateStartSingleReason' );
		expect( wrapper.vm.csdTemplateStartMultipleReason ).toBe( 'MockCSDTemplateStartMultipleReason' );
		expect( wrapper.vm.postfixReasonUsage ).toBe( 'MockPostfixReasonUsage' );
		expect( wrapper.vm.multipleReasonSeparation ).toBe( 'MockMultipleReasonSeparation' );
		expect( wrapper.vm.speedyDeletionPolicyLink ).toBe( 'MockSpeedyDeletionPolicyLink' );
		expect( wrapper.vm.speedyDeletionPolicyPageShortcut ).toBe( 'MockSpeedyDeletionPolicyPageShortcut' );
		expect( wrapper.vm.csdNotificationTemplate ).toBe( 'MockCSDNotificationTemplate' );
		expect( wrapper.vm.singleReasonSummary ).toBe( 'MockSingleReasonSummary' );
		expect( wrapper.vm.multipleReasonSummary ).toBe( 'MockMultipleReasonSummary' );
		expect( wrapper.vm.copyVioReasonValue ).toBe( 'MockCopyVioReasonValue' );
		expect( wrapper.vm.moduleEnabled ).toBe( true );
		expect( wrapper.vm.testMode ).toBe( false );
		expect( wrapper.vm.namespaces ).toEqual( [ 0, 1 ] );
	} );

	it( 'Should correctly save when saveConfiguration is called', async () => {
		await wrapper.vm.saveConfiguration();
		expect( mw.message ).toHaveBeenCalledWith( 'adiutor-save-configurations' );
	} );

} );
