<template>
	<cdx-dialog class="csd-dialog" v-model:open="openCsdDialog" title="Speedy Deletion Request" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" :default-action="defaultAction"
		@primary="createSpeedyDeletionRequest" @default="openCsdDialog = true">
		<div class="header">
			<h5>{{ $i18n('adiutor-csd-header-title') }}</h5>
			<p>{{ $i18n('adiutor-csd-header-description') }}</p>
		</div>
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
					<cdx-label class="adt-label"><strong>{{ $i18n('adiutor-other-options') }}</strong></cdx-label>
					<cdx-checkbox v-model="recreationProrection">{{
						$i18n('adiutor-protect-against-rebuilding') }}</cdx-checkbox>
					<cdx-checkbox v-model="informCreator">{{ $i18n('adiutor-afd-inform-creator')
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
						<cdx-label class="adt-label"><strong>{{ $i18n('adiutor-copyright-infringing-page') }} {{
							copyVioInput
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
const speedyDeletionReasons = csdConfiguration.speedyDeletionReasons;
const copyVioReasonValue = csdConfiguration.copyVioReasonValue;
const postfixReasonUsage = csdConfiguration.postfixReasonUsage;
const multipleReasonSeparation = csdConfiguration.multipleReasonSeparation;
const csdTemplateStartSingleReason = csdConfiguration.csdTemplateStartSingleReason;
const csdTemplateStartMultipleReason = csdConfiguration.csdTemplateStartMultipleReason;
const singleReasonSummary = csdConfiguration.singleReasonSummary;
const multipleReasonSummary = csdConfiguration.multipleReasonSummary;
const speedyDeletionPolicyPageShortcut = csdConfiguration.speedyDeletionPolicyPageShortcut;
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
			icon: 'cdxIconTag',
			label: mw.msg('adiutor-request'),
			actionType: 'progressive'
		};

		const defaultAction = {
			label: mw.msg('adiutor-speedy-deletion-policy'),
		};

		function createSpeedyDeletionRequest() {
			const selectedReasons = speedyDeletionReasons.reduce((selected, category) => {
				const selectedInCategory = category.reasons.filter((reason) => {
					return checkboxValue.value.includes(reason.value);
				});
				selected.push(...selectedInCategory);
				return selected;
			}, []);

			if (selectedReasons.length) {
				let csdReason;
				let csdSummary = '';
				let saltCSDSummary = '';
				let copyVioURL = '';
				if (copyVioInput.value !== '') {
					copyVioURL = '|url=' + copyVioInput.value;
				}
				if (selectedReasons.length > 1) {
					let saltCSDReason = csdTemplateStartMultipleReason;
					if (multipleReasonSeparation === 'vertical_bar') {
						for (let i = 0; i < selectedReasons.length; i++) {
							saltCSDReason += '|' + selectedReasons[i].value;
						}
						csdReason = saltCSDReason + '}}';
					} else if (multipleReasonSeparation === 'default') {
						for (let i = 0; i < selectedReasons.length; i++) {
							if (i > 0) {
								saltCSDReason += (i < selectedReasons.length - 1) ? ', ' : ' ' + mw.msg('adiutor-and') + ' ';
							}
							saltCSDReason += '|[[' + speedyDeletionPolicyPageShortcut + '#' + selectedReasons[i].value + ']]';
						}
						csdReason = saltCSDReason + '}}';
					}
					for (let i = 0; i < selectedReasons.length; i++) {
						if (i > 0) {
							saltCSDSummary += (i < selectedReasons.length - 1) ? ', ' : ' ' + mw.msg('adiutor-and') + ' ';
						}
						saltCSDSummary += '[[' + speedyDeletionPolicyPageShortcut + '#' + selectedReasons[i].value + ']]';
					}
					csdSummary = replaceParameter(multipleReasonSummary, '2', saltCSDSummary);
				} else {
					if (postfixReasonUsage === 'data') {
						csdReason = csdTemplateStartSingleReason + selectedReasons[0].data + '}}';
					} else if (postfixReasonUsage === 'value') {
						csdReason = csdTemplateStartSingleReason + selectedReasons[0].value + '}}';
					}
					csdSummary = replaceParameter(singleReasonSummary, '2', selectedReasons[0].data);
				}
				console.log(csdReason);
				console.log(csdSummary);
				openCsdDialog.value = false;
			} else {
				mw.notify(mw.message('select-speedy-deletion-reason').text(), {
					title: mw.msg('adiutor-warning'),
					type: 'error'
				});
			}
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
<style lang="css">
.csd-dialog {
	max-width: 45em;
}

.csd-dialog .csd-reasons-body {
	display: flex;
	padding: 20px;
}

.csd-dialog .cdx-dialog {
	max-width: 720px;
	padding-top: 10px;
	padding-bottom: 0;
}

.csd-dialog .cdx-dialog__body {
	flex-grow: 1;
	margin-top: 0;
	padding: 0;
	overflow-y: auto;
}


.csd-dialog .cdx-dialog--dividers .cdx-dialog__body {
	padding-top: 0;
}

.csd-dialog .csd-reason-field {
	display: flex;
	flex-direction: column;
	width: 50%;
}

.csd-dialog .cdx-dialog__header {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	box-sizing: border-box;
	width: 100%;
	padding: 10px 20px 10px;
	font-weight: 700;
}

.csd-dialog cdx-label {
	margin-bottom: 10px;
	display: block;
}

.csd-dialog .header {
	background-color: #eaf3ff;
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 10em;
	padding: 20px;
	background-image: url(../../ext.Adiutor.images/csd-background.png);
	background-position: right 10px;
	background-repeat: no-repeat;
	background-size: 205px;
}

.csd-dialog .cdx-dialog__footer {
	padding: 20px !important;
	border-top: 1px solid #a2a9b1;
}

.csd-dialog .header p {
	width: 70%;
}

.csd-dialog h2 {
	margin: 0;
	padding: 0;
	font-size: 1.125em !important
}
</style>