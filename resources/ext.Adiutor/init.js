(function () {
	const Vue = require('vue');
	const App = require('./components/App.vue');
  
	// Create a new div element with the id "adiutor-container"
	const adiutorContainer = document.createElement('div');
	adiutorContainer.id = 'adiutor-container';
	document.body.appendChild(adiutorContainer);
  
	// Add portlet links
	mw.util.addPortletLink('p-cactions', '#', 'Speedy deletion request', 't-deletion-request', 'Speedy deletion request', 'sdr');
	mw.util.addPortletLink('p-cactions', '#', 'Propose deletion', 't-propose-deletion', 'Propose deletion', 'pd');
	mw.util.addPortletLink('p-cactions', '#', 'Request protection', 't-request-protection', 'Request protection', 'pr');
  
	Vue.configureCompat({
	  MODE: 3
	});
  
	Vue.createMwApp(App)
	  .mount('#adiutor-container'); // Mount the app to the adiutor-container element
  })();
  