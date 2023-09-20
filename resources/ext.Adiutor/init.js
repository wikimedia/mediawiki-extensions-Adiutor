(function () {
	const Vue = require('vue');
	const adiutorApp = require('./components/adiutorApp.vue');
	const speedyDeletionRequests = require('./components/speedyDeletionRequests.vue');
	const afdList = require('./components/afdList.vue');

	Vue.configureCompat({
		MODE: 3
	});

	Vue.createMwApp(adiutorApp).mount('#adiutor-container');
	Vue.createMwApp(speedyDeletionRequests).mount('#adiutor-speedy-deletion-requests');
	Vue.createMwApp(afdList).mount('#adiutor-article-for-deletion');
})();
