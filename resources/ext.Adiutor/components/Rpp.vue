<template>
	<cdx-dialog class="rpp-dialog" v-model:open="openRppDialog" title="Page Protection Request" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" @primary="requestPageProtection "
		@default="openRppDialog = true" :default-action="defaultAction">
		<div class="header">
		<h5>{{ $i18n('rpp-header-title') }}</h5>
		<p>{{ $i18n('rpp-header-description') }}</p>
	</div>
		
		<cdx-field class="rpp-dialog-body">
			<cdx-label class="adt-label"><strong>{{ $i18n('protection-type') }}</strong></cdx-label>
			<cdx-select v-model:selected="durationSelection" :menu-items="protectionDurations"
				default-label="Choose duration"></cdx-select>
			<cdx-select v-model:selected="typeSelection" :menu-items="protectionTypes"
				default-label="Select protection type"></cdx-select>
			<cdx-label><strong>{{ $i18n('rationale') }}</strong></cdx-label>
			<cdx-text-input v-model="rationaleInput" aria-label="TextInput default demo"></cdx-text-input>
			<div>
				<cdx-text-area v-model="rationaleInput" placeholder="Describe what you changed"></cdx-text-area>
			</div>
		</cdx-field>
	</cdx-dialog>
</template>
<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxTextArea, CdxSelect } = require('@wikimedia/codex');
const rppConfiguration = require('../localization/Rpp.json');
var noticeBoardTitle = rppConfiguration.noticeBoardTitle;
var noticeBoardLink = noticeBoardTitle.replace(/ /g, '_');
var protectionDurations = rppConfiguration.protectionDurations;
var protectionTypes = rppConfiguration.protectionTypes;
var addNewSection = rppConfiguration.addNewSection;
var appendText = rppConfiguration.appendText;
var prependText = rppConfiguration.prependText;
var sectionId = rppConfiguration.sectionId;
var contentPattern = rppConfiguration.contentPattern;
var apiPostSummary = rppConfiguration.apiPostSummary;
var sectionTitle = rppConfiguration.sectionTitle;
var pageTitle = mw.config.get("wgPageName").replace(/_/g, " ");
module.exports = defineComponent({
	name: 'requestProtection',
	components: {
		CdxButton,
		CdxDialog,
		CdxCheckbox,
		CdxField,
		CdxLabel,
		CdxTextInput,
		CdxTextArea,
		CdxSelect
	},
	data() {
		return {
			protectionDurations: rppConfiguration.protectionDurations,
			protectionTypes: rppConfiguration.protectionTypes,
			durationSelection: null,
			typeSelection: null
		};
	},
	setup() {
		const rationaleInput = ref('');
		const switchValue = ref(false);
		const durationSelection = null;
		const typeSelection = null;
		const standardPropose = false;
		const livingPersonPropose = false;
		const openRppDialog = ref(true);

		const primaryAction = {
			label: mw.msg('create-request'),
			actionType: 'progressive'
		};

		const defaultAction = {
			label: mw.msg('protection-policy'),
		};

		function requestPageProtection() {
			openRppDialog.value = false;
			var placeholders = {
				$1: pageTitle,
				$2: durationSelection,
				$3: typeSelection,
				$4: rationaleInput,
			};
			const preparedContent = replacePlaceholders(contentPattern, placeholders);
			console.log(preparedContent);
		}

		function replacePlaceholders(input, replacements) {
			return input.replace(/\$(\d+)/g, function (match, group) {
				var replacement = replacements['$' + group];
				return replacement !== undefined ? replacement : match;
			});
		}

		function replaceParameter(input, parameterName, newValue) {
			const regex = new RegExp('\\$' + parameterName, 'g');
			if (input.includes('$' + parameterName)) {
				return input.replace(regex, newValue);
			} else {
				return input;
			}
		}

		return {
			openRppDialog,
			primaryAction,
			defaultAction,
			standardPropose,
			livingPersonPropose,
			rationaleInput,
			switchValue,
			requestPageProtection
		};
	}
});
</script>

<style lang="css">

.rpp-dialog .cdx-dialog {
	max-width: 448px;
	padding-top: 10px;
	padding-bottom: 0;
}

.rpp-dialog-body {
	padding: 20px;
    display: grid;
    width: inherit;
}

.rpp-dialog .cdx-dialog__body {
	flex-grow: 1;
	margin-top: 0;
	padding: 0;
	overflow-y: auto;
}


.rpp-dialog .cdx-dialog--dividers .cdx-dialog__body {
	padding-top: 0;
}

.rpp-dialog .rpp-reason-field {
	display: flex;
	flex-direction: column;
	width: 50%;
}

.rpp-dialog .cdx-dialog__header {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	box-sizing: border-box;
	width: 100%;
	padding: 0 20px 8px;
	font-weight: 700;
}

.rpp-dialog cdx-label {
    margin-bottom: 10px;
    display: block;
    margin-top: 10px;
}

.rpp-dialog .header {
	background-color: #eaf3ff;
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 8em;
	padding: 20px;
	background-image: url(../../ext.Adiutor.images/rpp-background.png);
    background-position: right 5px;
    background-repeat: no-repeat;
    background-size: 200px;
}

.rpp-dialog .cdx-dialog__footer {
	padding: 10px !important;
}

.rpp-dialog .header p {
	width: 60%;
}

.rpp-dialog h2 {
	margin: 0;
	padding: 0;
	font-size: 1.125em;
}
.rpp-dialog .cdx-select-vue {
    margin-bottom: 10px !important;
}
</style>