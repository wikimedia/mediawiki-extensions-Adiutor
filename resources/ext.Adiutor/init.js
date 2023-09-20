(function () {
	const Vue = require('vue');
	const App = require('./components/App.vue');
	const Sdr = require('./components/Sdr.vue');
	const Afd = require('./components/Afd.vue');
  
	// Add portlet links
	mw.util.addPortletLink('p-cactions', '#', 'Request speedy deletion', 't-request-speedy-deletion', 'Request speedy deletion', 'rsd');
	mw.util.addPortletLink('p-cactions', '#', 'Propose deletion', 't-propose-deletion', 'Propose deletion', 'pd');
	mw.util.addPortletLink('p-cactions', '#', 'Request protection', 't-request-protection', 'Request protection', 'pr');
  
	Vue.configureCompat({
	  MODE: 3
	});
  
	Vue.createMwApp(App).mount('#adiutor-container');
	Vue.createMwApp(Sdr).mount('#adiutor-speedy-deletion-requests');
	Vue.createMwApp(Afd).mount('#adiutor-article-for-deletion');
  })();
  