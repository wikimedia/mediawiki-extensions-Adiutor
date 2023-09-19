<template>
	<cdx-dialog v-model:open="openPrdDialog" title="Proposed Deletion" close-button-label="Close" :show-dividers="true"
		:primary-action="primaryAction" @primary="proposeForDeletion"
		@default="openPrdDialog = true">
		<h5>{{ $i18n('prd-header-title') }}</h5>
		<p>{{ $i18n('prd-header-description') }}</p>
		<cdx-label class="adt-label"><strong>{{ $i18n('prd-deletion-type') }}</strong></cdx-label>
		<cdx-checkbox v-model="standardPropose">{{
			$i18n('prd-deletion-type-1') }}</cdx-checkbox>
		<cdx-checkbox v-model="livingPersonPropose">{{
			$i18n('prd-deletion-type-2') }}</cdx-checkbox>
		<cdx-label class="adt-label"><strong>{{ $i18n('rationale') }}</strong></cdx-label>
		<cdx-text-input v-model="textareaValue" aria-label="TextInput default demo"></cdx-text-input>
		<cdx-toggle-switch v-model="switchValue">
			{{ $i18n('afd-inform-creator') }}
		</cdx-toggle-switch>
	</cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxToggleSwitch } = require('@wikimedia/codex');
const prdConfiguration = require('../localization/Prd.json');

module.exports = defineComponent({
	name: 'proposeDeletion',
	components: {
		CdxButton,
		CdxDialog,
		CdxCheckbox,
		CdxField,
		CdxLabel,
		CdxTextInput,
		CdxToggleSwitch
	},
	setup() {
		const textareaValue = ref( '' );
		const switchValue = ref(false);

		const standardPropose = false;
		const livingPersonPropose = false;
		const openPrdDialog = ref(true);

		const primaryAction = {
			label: mw.msg('propose'),
			actionType: 'progressive'
		};

		function proposeForDeletion() {
			openPrdDialog.value = false;
		}

		return {
			openPrdDialog,
			primaryAction,
			standardPropose,
			livingPersonPropose,
			textareaValue,
			switchValue,
			proposeForDeletion
		};
	}
});
</script>

<style>
.csd-reasons-body {
	display: flex;
	padding-top: 10px;
}

.csd-reason-field {
	display: flex;
	flex-direction: column;
	width: 50%;
}

cdx-label {
	margin-bottom: 10px;
	display: block;
}
</style>