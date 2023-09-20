<template>
	<div>
		<component :is="activeComponent" :key="activeComponentKey" />
	</div>
</template>
<script>
const Rpp = require('./Rpp.vue');
const Csd = require('./Csd.vue');
const Prd = require('./Prd.vue');
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