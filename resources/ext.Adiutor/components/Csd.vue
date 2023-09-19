<template>
	<cdx-dialog v-model:open="openCsdDialog" title="Speedy Deletion Request" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" :default-action="defaultAction"
		@primary="createSpeedyDeletionRequest" @default="openCsdDialog = true">
		<h5>{{ $i18n('csd-header-title') }}</h5>
		<p>{{ $i18n('csd-header-description') }}</p>
		<div class="csd-reasons-body">
			<div class="csd-reason-field">
				<cdx-field :is-fieldset="true" v-for="reason in namespaceDeletionReasons">
					<cdx-label class="adt-label"><strong>{{ reason.name }}</strong></cdx-label>
					<cdx-checkbox v-for="reason in reason.reasons" :key="'checkbox-' + reason.value" v-model="checkboxValue"
						:input-value="reason.value">
						{{ reason.label }}
					</cdx-checkbox>
					<template #help-text>
						{{ reason.help }}
					</template>
					<cdx-label class="adt-label"><strong>{{ $i18n('other-options') }}</strong></cdx-label>
					<cdx-checkbox v-model="recreationProrection">{{
						$i18n('protect-against-rebuilding') }}</cdx-checkbox>
					<cdx-checkbox v-model="informCreator">{{ $i18n('afd-inform-creator')
					}}</cdx-checkbox>
				</cdx-field>
			</div>
			<div class="csd-reason-field">
				<cdx-field :is-fieldset="true" v-for="reason in generalDeletionReasons">
					<cdx-label class="adt-label"><strong>{{ reason.name }}</strong></cdx-label>
					<cdx-checkbox v-for="reason in reason.reasons" :key="'checkbox-' + reason.value" v-model="checkboxValue"
						:input-value="reason.value" @click="toggleCopyVioInputBasedOnCheckbox()">
						{{ reason.label }}
					</cdx-checkbox>
					<div v-if="showCopyVioInput">
						<cdx-label class="adt-label"><strong>{{ $i18n('copyright-infringing-page') }} {{ copyVioInput
						}}</strong></cdx-label>
						<cdx-text-input v-model="copyVioInput" aria-label="TextInput default demo"></cdx-text-input>
					</div>
				</cdx-field>
			</div>
		</div>
	</cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput } = require('@wikimedia/codex');
const csdConfiguration = require('../localization/Csd.json');
var speedyDeletionReasons = csdConfiguration.speedyDeletionReasons;
var csdTemplateStartSingleReason = csdConfiguration.csdTemplateStartSingleReason;
var csdTemplateStartMultipleReason = csdConfiguration.csdTemplateStartMultipleReason;
var reasonAndSeperator = csdConfiguration.reasonAndSeperator;
var speedyDeletionPolicyLink = csdConfiguration.speedyDeletionPolicyLink;
var speedyDeletionPolicyPageShorcut = csdConfiguration.speedyDeletionPolicyPageShorcut;
var apiPostSummaryforLog = csdConfiguration.apiPostSummaryforLog;
var apiPostSummary = csdConfiguration.apiPostSummary;
var csdNotificationTemplate = csdConfiguration.csdNotificationTemplate;
var userPagePrefix = csdConfiguration.userPagePrefix;
var userTalkPagePrefix = csdConfiguration.userTalkPagePrefix;
var localLangCode = csdConfiguration.localLangCode;
var singleReasonSummary = csdConfiguration.singleReasonSummary;
var multipleReasonSummary = csdConfiguration.multipleReasonSummary;
var copyVioReasonValue = csdConfiguration.copyVioReasonValue;
var csdTemplatePostfixReasonData = csdConfiguration.csdTemplatePostfixReasonData;
var csdTemplatePostfixReasonValue = csdConfiguration.csdTemplatePostfixReasonValue;
var useVerticalVarForSeparatingMultipleReasons = csdConfiguration.useVerticalVarForSeparatingMultipleReasons;
const namespaceDeletionReasons = [];
for (const reason of speedyDeletionReasons) {
	if (reason.namespace === mw.config.get('wgNamespaceNumber')) {
		namespaceDeletionReasons.push(reason);
	}
}
const generalDeletionReasons = [];
for (const reason of speedyDeletionReasons) {
	if (reason.namespace === 'general') {
		generalDeletionReasons.push(reason);
	}
}
// @vue/component
module.exports = defineComponent({
	name: 'createSpeedyDeletion',
	components: {
		CdxButton,
		CdxDialog,
		CdxCheckbox,
		CdxField,
		CdxLabel,
		CdxTextInput
	},
	setup() {
		const checkboxValue = ref(['quickReply', 'quickTopic']);
		const copyVioInput = ref('');
		const recreationProrection = false;
		const informCreator = false;
		const openCsdDialog = ref(true);

		const showCopyVioInput = ref(false);

		const toggleCopyVioInputBasedOnCheckbox = () => {
			const selectedReasons = speedyDeletionReasons.reduce((selected, category) => {
				const selectedInCategory = category.reasons.filter((reason) => {
					return checkboxValue.value.includes(reason.value);
				});
				selected.push(...selectedInCategory);
				return selected;
			}, []);

			if (selectedReasons.some((reason) => reason.value === copyVioReasonValue)) {
				showCopyVioInput.value = true;
			} else {
				showCopyVioInput.value = false;
			}
		};

		const primaryAction = {
			label: mw.msg('tag-page'),
			actionType: 'progressive'
		};

		const defaultAction = {
			label: mw.msg('speedy-deletion-policy'),
		};

		function createSpeedyDeletionRequest() {
			openCsdDialog.value = false;
			let csdReason, csdSummary;
			const selectedReasons = speedyDeletionReasons.reduce((selected, category) => {
				const selectedInCategory = category.reasons.filter((reason) => {
					return checkboxValue.value.includes(reason.value);
				});
				selected.push(...selectedInCategory);
				return selected;
			}, []);
			console.log(selectedReasons);

			if (selectedReasons.length > 0) {
				let saltCSDSummary = '';
				let copyVioURL = '';

				if (copyVioInput.value !== '') {
					copyVioURL = '|url=' + copyVioInput.value;
				}

				if (selectedReasons.length > 1) {
					let saltCSDReason = csdTemplateStartMultipleReason;
					for (let i = 0; i < selectedReasons.length; i++) {
						saltCSDReason += '|' + selectedReasons[i].value;

						if (i > 0) {
							saltCSDSummary += (i < selectedReasons.length - 1) ? ', ' : ' ' + reasonAndSeperator + ' ';
						}
						saltCSDSummary += '[[' + speedyDeletionPolicyPageShorcut + '#' + selectedReasons[i].value + ']]';
					}
					csdReason = saltCSDReason + '}}';
					csdSummary = replaceParameter(multipleReasonSummary, '2', saltCSDSummary);
					console.log(csdReason);
					console.log(csdSummary);
				} else {
					const reasonPlaceholder = csdTemplateStartSingleReason + copyVioURL + '}}';
					if (csdTemplatePostfixReasonData) {
						csdReason = replaceParameter(reasonPlaceholder, '3', selectedReasons[0].data);
					} else if (csdTemplatePostfixReasonValue) {
						csdReason = replaceParameter(reasonPlaceholder, '3', selectedReasons[0].value);
					}
					csdSummary = replaceParameter(singleReasonSummary, '2', selectedReasons[0].data);
					saltCSDSummary = replaceParameter(singleReasonSummary, '2', selectedReasons[0].data);
				}
				//putCSDTemplate(csdReason, csdSummary);
				console.log(csdReason);
				console.log(csdSummary);
			} else {
				// Uyarı göster
				mw.notify(mw.message('select-speedy-deletion-reason').text(), {
					title: mw.msg('warning'),
					type: 'error'
				});
			}
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
			checkboxValue,
			speedyDeletionReasons,
			namespaceDeletionReasons,
			generalDeletionReasons,
			openCsdDialog,
			primaryAction,
			defaultAction,
			copyVioInput,
			recreationProrection,
			informCreator,
			showCopyVioInput,
			toggleCopyVioInputBasedOnCheckbox,
			createSpeedyDeletionRequest
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