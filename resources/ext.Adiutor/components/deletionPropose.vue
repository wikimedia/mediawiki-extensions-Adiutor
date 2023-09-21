<template>
	<cdx-dialog class="prd-dialog" v-model:open="openPrdDialog" title="Proposed Deletion" close-button-label="Close"
		:show-dividers="true" @default="openPrdDialog = true">
		<div class="header">
			<h5>{{ $i18n('adiutor-prd-header-title') }}</h5>
			<p>{{ $i18n('adiutor-prd-header-description') }}</p>
		</div>
		<cdx-field class="prd-dialog-body">
			<cdx-label class="adt-label"><strong>{{ $i18n('adiutor-prd-deletion-type') }}</strong></cdx-label>
			<cdx-radio v-for="radio in radios" :key="'radio-' + radio.value" v-model="radioValue"
				name="radio-group-descriptions" :input-value="radio.value">
				{{ radio.label }}
				<template #description>
					{{ radio.description }}
				</template>
			</cdx-radio>
			<cdx-label class="adt-label"><strong>{{ $i18n('adiutor-rationale') }}</strong></cdx-label>
			<cdx-text-area v-model="textareaValue" :placeholder="rationalePlaceholder"></cdx-text-area>
		</cdx-field>
		<div class="footer">
			<cdx-checkbox v-model="checkboxValue" :inline="true">
				{{ $i18n('adiutor-afd-inform-creator') }}
			</cdx-checkbox>
			<cdx-button action="progressive" weight="primary" aria-label="Next" @click="proposeForDeletion">
				{{ $i18n('adiutor-propose') }}
			</cdx-button>
		</div>
	</cdx-dialog>
</template>
<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextArea, CdxToggleSwitch, CdxRadio } = require('@wikimedia/codex');
const prdConfiguration = require('../localization/Prd.json');

module.exports = defineComponent({
	name: 'proposeDeletion',
	components: {
		CdxRadio,
		CdxButton,
		CdxDialog,
		CdxCheckbox,
		CdxField,
		CdxLabel,
		CdxTextArea,
		CdxToggleSwitch
	},
	data() {
		return {
			rationalePlaceholder: mw.msg('adiutor-prd-deletion-rationale')
		};
	},
	setup() {
		const textareaValue = ref('');
		const switchValue = ref(false);

		const standardPropose = false;
		const livingPersonPropose = false;
		const openPrdDialog = ref(true);

		const radioValue = ref('default');
		const radios = [
			{
				label: mw.msg('adiutor-prd-deletion-type-1'),
				description: mw.msg('adiutor-prd-deletion-type-1-help'),
				value: 'standardPropose'
			},
			{
				label: mw.msg('adiutor-prd-deletion-type-2'),
				description: mw.msg('adiutor-prd-deletion-type-2-help'),
				value: 'livingPersonPropose'
			}
		];

		function proposeForDeletion() {
			openPrdDialog.value = false;
		}

		return {
			openPrdDialog,
			standardPropose,
			livingPersonPropose,
			textareaValue,
			switchValue,
			radioValue,
			radios,
			proposeForDeletion
		};
	}
});
</script>
<style lang="css">
.prd-dialog .cdx-dialog {
	max-width: 548px;
	padding-top: 10px;
	padding-bottom: 0;
}

.prd-dialog-body {
	padding: 20px;
	display: grid;
	width: inherit;
}

.prd-dialog .cdx-dialog__body {
	flex-grow: 1;
	margin-top: 0;
	padding: 0;
	overflow-y: auto;
}


.prd-dialog .cdx-dialog--dividers .cdx-dialog__body {
	padding-top: 0;
}

.prd-dialog .prd-reason-field {
	display: flex;
	flex-direction: column;
	width: 50%;
}

.prd-dialog .cdx-dialog__header {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	box-sizing: border-box;
	width: 100%;
	padding: 10px 20px 10px;
	font-weight: 700;
}

.prd-dialog cdx-label {
	margin-bottom: 10px;
	display: block;
	margin-top: 10px;
}

.prd-dialog .header {
	background-color: #eaf3ff;
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 10em;
	padding: 20px;
	background-image: url(../../ext.Adiutor.images/prd-background.png);
	background-position: right 10px;
	background-repeat: no-repeat;
	background-size: 215px;
}

.prd-dialog .cdx-dialog__footer {
	padding: 10px !important;
}

.prd-dialog .header p {
	width: 60%;
}

.prd-dialog h2 {
	margin: 0;
	padding: 0;
	font-size: 1.125em !important
}

.prd-dialog .cdx-select-vue {
	margin-bottom: 10px !important;
}

.prd-dialog .footer {
	display: flex;
	align-items: baseline;
	justify-content: space-between;
	border-top: solid 1px #c8ccd1;
	padding: 20px;
}
</style>