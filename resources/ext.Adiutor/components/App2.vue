<template>
	<div>
		<button @click="showComponent('Rpp')">Rpp</button>
		<button @click="showComponent('Csd')">Csd</button>
		<button @click="showComponent('Prd')">Prd</button>

		<component :is="activeComponent" />
	</div>
</template>
  
<script>
// Import the required modules using CommonJS syntax
const Rpp = require('./Rpp.vue');
const Csd = require('./Csd.vue');
const Prd = require('./Prd.vue');

module.exports = {
	name: 'Test',
	data() {
		return {
			activeComponent: null, 
		};
	},
	components: {
		Rpp,
		Csd,
		Prd,
	},
	methods: {
		showComponent(componentName) {
			this.activeComponent = componentName;
		},
	},
};
</script>
  