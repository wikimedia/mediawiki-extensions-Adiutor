<template>
	<cdx-dialog v-model:open="openRppDialog" title="Page Protection Request" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" @primary="requestPageProtection"
		@default="openRppDialog = false">
		<h5>{{ $i18n('rpp-header-title') }}</h5>
		<p>{{ $i18n('rpp-header-description') }}</p>
		<cdx-label class="adt-label"><strong>{{ $i18n('protection-type') }}</strong></cdx-label>
		<cdx-field>
			<cdx-select v-model:selected="durationSelection" :menu-items="protectionDurations"
				default-label="Choose duration"></cdx-select>
			<cdx-select v-model:selected="typeSelection" :menu-items="protectionTypes"
				default-label="Select protection type"></cdx-select>
			<cdx-label class="label-second"><strong>{{ $i18n('rationale') }}</strong></cdx-label>
			<cdx-field
		class="cdx-demo-field-with-counter"
		:status="status"
		:messages="errorMessage"
	>
		<template #label>
			Enter your message
		</template>

		<cdx-text-area v-model="userMessageText"></cdx-text-area>

		<template #help-text>
			<div
				class="cdx-demo-field-with-counter__help-text"
				:class="dynamicClasses"
			>
				<!-- Display help text or error message depending on error status. -->
				<div class="cdx-demo-field-with-counter__help-text__message">
					<p>{{ helpText }}</p>
				</div>

				<!-- Display the remaining character count. -->
				<div class="cdx-demo-field-with-counter__help-text__counter">
					{{ charsRemaining }}
				</div>
			</div>
		</template>
	</cdx-field>
		</cdx-field>

	</cdx-dialog>
</template>
<script>
const { defineComponent, ref, computed } = require( 'vue' );
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput,CdxTextArea, CdxSelect } = require('@wikimedia/codex');
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
	name: 'csdDialog',
	components: {
		CdxButton,
		CdxDialog,
		CdxCheckbox,
		CdxField,
		CdxLabel,
		CdxTextInput,
		CdxSelect,
		CdxTextArea
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


		const MAX_MESSAGE_LENGTH = 100;
		const helpText = `Please enter a message of ${MAX_MESSAGE_LENGTH} characters or less`;

		const errorMessage = { error: 'Message is too long' };
		const userMessageText = ref( '' );

		// This is a simplified example; support for other languages/scripts may
		// require more complex code.
		const charsRemaining = computed( () => MAX_MESSAGE_LENGTH - userMessageText.value.length );
		const status = computed( () => charsRemaining.value < 0 ? 'error' : 'default' );

		const dynamicClasses = computed( () => {
			return {
				'cdx-demo-field-with-counter__help-text--error': status.value === 'error'
			};
		} );


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
			requestPageProtection,
			userMessageText,
			status,
			charsRemaining,
			errorMessage,
			helpText,
			dynamicClasses
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