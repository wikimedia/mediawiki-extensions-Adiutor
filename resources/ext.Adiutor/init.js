( function () {
	const Vue = require( 'vue' );
	const adiutorApp = require( './components/adiutorApp.vue' );
	const adiutorSettings = require( './components/adiutorSettings.vue' );

	Vue.configureCompat( {
		MODE: 3
	} );

	Vue.createMwApp( adiutorApp ).mount( '#adiutor-teleport-target' );
	Vue.createMwApp( adiutorSettings ).mount( '#adiutor-settings-teleport-target' );
}() );
