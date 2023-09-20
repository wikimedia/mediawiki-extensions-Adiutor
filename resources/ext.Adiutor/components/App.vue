<template>
	<div>
		<component :is="activeComponent" :key="activeComponentKey" />
	</div>
</template>
<script>
const Rpp = require('./Rpp.vue');
const Csd = require('./Csd.vue');
const Prd = require('./Prd.vue');
mw.util.addPortletLink('p-cactions', '#', 'Request speedy deletion', 't-request-speedy-deletion', 'Request speedy deletion', 'rsd');
mw.util.addPortletLink('p-cactions', '#', 'Propose deletion', 't-propose-deletion', 'Propose deletion', 'pd');
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
		Rpp,
		Csd,
		Prd,
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
			const requestProtection = document.querySelector('#t-request-protection');
			if (speedyDeletionRequestLink) {
				speedyDeletionRequestLink.addEventListener('click', () => {
					this.showComponent('Csd');
				});
			}
			if (proposeDeletionLink) {
				proposeDeletionLink.addEventListener('click', () => {
					this.showComponent('Prd');
				});
			}
			if (requestProtection) {
				requestProtection.addEventListener('click', () => {
					this.showComponent('Rpp');
				});
			}
		});
	},
};
</script>