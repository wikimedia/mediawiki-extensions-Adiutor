(function () {
	const Vue = require('vue');
	const App = require('./components/App.vue');
	const Sdr = require('./components/Sdr.vue');
	const Afd = require('./components/Afd.vue');
	
	Vue.configureCompat({
		MODE: 3
	});

	Vue.createMwApp(App).mount('#adiutor-container');
	Vue.createMwApp(Sdr).mount('#adiutor-speedy-deletion-requests');
	Vue.createMwApp(Afd).mount('#adiutor-article-for-deletion');
})();
