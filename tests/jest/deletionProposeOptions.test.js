/**
 * deletionProposeOptions.test.js
 * This file contains tests for the DeletionProposeOptions Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object for accessing MediaWiki's global variables and services.
global.mw = {
	config: {
		get: jest.fn( ( key ) => {
			switch ( key ) {
			case 'wgAdiutorDeletionPropose':
				return {
					standardProposeTemplate: 'MockStandardProposeTemplate',
					livingPersonProposeTemplate: 'MockLivingPersonProposeTemplate',
					apiPostSummary: 'MockApiPostSummary',
					apiPostSummaryForCreator: 'MockApiPostSummaryForCreator',
					prodNotificationTemplate: 'MockProdNotificationTemplate',
					proposedDeletionPolicy: 'MockProposedDeletionPolicy',
					proposedDeletionPolicyShortcut: 'MockProposedDeletionPolicyShortcut',
					proposedDeletionOfBiographiesOfLivingPeoplePolicy: 'MockProposedDeletionOfBiographiesOfLivingPeoplePolicy',
					proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut: 'MockProposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut',
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
const DeletionProposeOptions = require( '../../resources/ext.adiutor/components/deletionProposeOptions.vue' );
describe( 'DeletionProposeOptions Component Tests', () => {

	const wrapper = mount( DeletionProposeOptions );

	it( 'Should exist DeletionProposeOptions', () => {
		expect( wrapper.exists() ).toBe( true );
	} );

	// Test to verify initial data properties
	it( 'Initialize with the correct elements', () => {
		expect( wrapper.vm.standardProposeTemplate ).toBe( 'MockStandardProposeTemplate' );
		expect( wrapper.vm.livingPersonProposeTemplate ).toBe( 'MockLivingPersonProposeTemplate' );
		expect( wrapper.vm.apiPostSummary ).toBe( 'MockApiPostSummary' );
		expect( wrapper.vm.apiPostSummaryForCreator ).toBe( 'MockApiPostSummaryForCreator' );
		expect( wrapper.vm.prodNotificationTemplate ).toBe( 'MockProdNotificationTemplate' );
		expect( wrapper.vm.proposedDeletionPolicy ).toBe( 'MockProposedDeletionPolicy' );
		expect( wrapper.vm.proposedDeletionPolicyShortcut ).toBe( 'MockProposedDeletionPolicyShortcut' );
		expect( wrapper.vm.proposedDeletionOfBiographiesOfLivingPeoplePolicy ).toBe( 'MockProposedDeletionOfBiographiesOfLivingPeoplePolicy' );
		expect( wrapper.vm.proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut ).toBe( 'MockProposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut' );
		expect( wrapper.vm.moduleEnabled ).toBe( true );
		expect( wrapper.vm.testMode ).toBe( false );
		expect( wrapper.vm.namespaces ).toEqual( [ 0, 1 ] );
	} );

	it( 'Should correctly save when saveConfiguration is called', async () => {
		await wrapper.vm.saveConfiguration();
		expect( mw.message ).toHaveBeenCalledWith( 'adiutor-save-configurations' );
	} );

} );
