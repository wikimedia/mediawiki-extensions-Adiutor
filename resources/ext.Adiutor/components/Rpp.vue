<template>
	<cdx-dialog v-model:open="openRppDialog" title="Page Protection Request" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" @primary="requestPageProtection"
		@default="openRppDialog = true">
		<h5>{{ $i18n('rpp-header-title') }}</h5>
		<p>{{ $i18n('rpp-header-description') }}</p>
		<cdx-label class="adt-label"><strong>{{ $i18n('protection-type') }}</strong></cdx-label>
		<cdx-field>
			<cdx-select v-model:selected="durationSelection" :menu-items="protectionDurations"
				default-label="Choose duration"></cdx-select>
			<cdx-select v-model:selected="typeSelection" :menu-items="protectionTypes"
				default-label="Select protection type"></cdx-select>
			<cdx-label class="label-second"><strong>{{ $i18n('rationale') }}</strong></cdx-label>
			<cdx-text-input v-model="rationaleInput" aria-label="TextInput default demo"></cdx-text-input>
			<cdx-text-area v-model="rationaleInput" placeholder="Describe what you changed"></cdx-text-area>
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
			standardPropose,
			livingPersonPropose,
			rationaleInput,
			switchValue,
			requestPageProtection
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

.label-second {
	margin-top: 10px;
	margin-bottom: 10px;
	display: block;
}
</style>