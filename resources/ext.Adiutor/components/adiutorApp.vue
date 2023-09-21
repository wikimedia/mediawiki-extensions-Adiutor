<template>
	<div>
		<component :is="activeComponent" :key="activeComponentKey" />
	</div>
</template>
<script>
const pageProtectionRequest = require('./pageProtectionRequest.vue');
const createSpeedyDeletion = require('./createSpeedyDeletion.vue');
const deletionPropose = require('./deletionPropose.vue');
const articleForDeletion = require('./articleForDeletion.vue');
mw.util.addPortletLink('p-cactions', '#', 'Request speedy deletion', 't-request-speedy-deletion', 'Request speedy deletion', 'rsd');
mw.util.addPortletLink('p-cactions', '#', 'Propose deletion', 't-propose-deletion', 'Propose deletion', 'pd');
mw.util.addPortletLink('p-cactions', '#', 'Article for deletion', 't-article-for-deletion', 'Article for deletion', 'afd');
mw.util.addPortletLink('p-cactions', '#', 'Request protection', 't-request-protection', 'Request protection', 'pr');
module.exports = {
	name: 'adiutorInterfaceLoader',
	data() {
		return {
			activeComponent: null,
			activeComponentKey: 0,
		};
	},
	components: {
		pageProtectionRequest,
		createSpeedyDeletion,
		deletionPropose,
		articleForDeletion,
	},
	methods: {
		showComponent(componentName) {
			if (componentName === this.activeComponent) {
				this.activeComponentKey++;
			} else {
				this.activeComponent = componentName;
			}
		},
	},
	created() {
		this.$nextTick(() => {
			const speedyDeletionRequestLink = document.querySelector('#t-request-speedy-deletion');
			const proposeDeletionLink = document.querySelector('#t-propose-deletion');
			const articleForDeletionLink = document.querySelector('#t-article-for-deletion');
			const requestProtection = document.querySelector('#t-request-protection');
			if (speedyDeletionRequestLink) {
				speedyDeletionRequestLink.addEventListener('click', () => {
					this.showComponent('createSpeedyDeletion');
				});
			}
			if (proposeDeletionLink) {
				proposeDeletionLink.addEventListener('click', () => {
					this.showComponent('deletionPropose');
				});
			}
			if (articleForDeletionLink) {
				articleForDeletionLink.addEventListener('click', () => {
					this.showComponent('articleForDeletion');
				});
			}
			if (requestProtection) {
				requestProtection.addEventListener('click', () => {
					this.showComponent('pageProtectionRequest');
				});
			}
		});
	},
};
</script>