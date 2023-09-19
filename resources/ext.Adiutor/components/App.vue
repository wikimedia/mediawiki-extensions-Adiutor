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
			console.log('Before Update: activeComponent:', this.activeComponent);
			console.log('Before Update: activeComponentKey:', this.activeComponentKey);

			if (componentName === this.activeComponent) {
				this.activeComponentKey++;
			} else {
				this.activeComponent = componentName;
			}

			console.log('After Update: activeComponent:', this.activeComponent);
			console.log('After Update: activeComponentKey:', this.activeComponentKey);
		},
	},
	created() {
		this.$nextTick(() => {
			const speedyDeletionRequestLink = document.querySelector('#t-deletion-request');
			const proposeDeletionLink = document.querySelector('#t-propose-deletion');
			const requestProtection = document.querySelector('#t-request-protection');

			if (speedyDeletionRequestLink) {
				speedyDeletionRequestLink.addEventListener('click', () => {
					this.showComponent('Csd');
				});
			}

			if (proposeDeletionLink) {
				proposeDeletionLink.addEventListener('click', () => {
					console.log('Propose Deletion Link Clicked');
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