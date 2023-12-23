<template>
	<cdx-dialog class="csd-dialog" v-model:open="openCsdDialog" :title="$i18n('adiutor-csd-header-title')" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" :default-action="defaultAction"
		@primary="createSpeedyDeletionRequest" @default="openCsdDialog = true">
		<div class="header">
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
					<cdx-checkbox v-model="informCreator">{{ $i18n('adiutor-inform-creator')
					}}</cdx-checkbox>
				</cdx-field>
				<cdx-field :is-fieldset="true" v-if="!namespaceDeletionReasons.length">
					<cdx-message inline type="warning">
						<p>{{ $i18n('adiutor-warning') }}</p>
						<small>{{ $i18n('adiutor-no-namespace-reason-for-csd-title') }}</small>
					</cdx-message>
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
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxMessage } = require('@wikimedia/codex');
const csdConfiguration = mw.config.get('AdiutorCreateSpeedyDeletion');
const speedyDeletionReasons = csdConfiguration.speedyDeletionReasons;
const copyVioReasonValue = csdConfiguration.copyVioReasonValue;
const postfixReasonUsage = csdConfiguration.postfixReasonUsage;
const multipleReasonSeparation = csdConfiguration.multipleReasonSeparation;
const csdTemplateStartSingleReason = csdConfiguration.csdTemplateStartSingleReason;
const csdTemplateStartMultipleReason = csdConfiguration.csdTemplateStartMultipleReason;
const singleReasonSummary = csdConfiguration.singleReasonSummary;
const multipleReasonSummary = csdConfiguration.multipleReasonSummary;
const speedyDeletionPolicyPageShortcut = csdConfiguration.speedyDeletionPolicyPageShortcut;
const csdNotificationTemplate = csdConfiguration.csdNotificationTemplate;
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

module.exports = defineComponent({
	name: 'createSpeedyDeletion',
	components: {
		CdxButton,
		CdxDialog,
		CdxCheckbox,
		CdxField,
		CdxLabel,
		CdxTextInput,
		CdxMessage
	},
	setup() {
		const api = new mw.Api();
		const checkboxValue = ref(['']);
		const copyVioInput = ref('');
		const recreationProrection = ref(false);
		const informCreator = ref(true);
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

		/**
		 * Creates a speedy deletion request based on the selected reasons.
		 * If there are selected reasons, it constructs the csdReason and csdSummary based on the selectedReasons array.
		 * It then calls the createApiRequest function with the csdReason and csdSummary.
		 * If informCreator is true, it fetches the creator of the article and sends a notification message to the creator.
		 * If there are no selected reasons, it displays an error notification.
		 */
		const createSpeedyDeletionRequest = async () => {
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
				createApiRequest(csdReason, csdSummary);
				if (informCreator.value) {
					try {
						const creatorData = await getCreator();
						var articleAuthor = creatorData.query.pages[mw.config.get('wgArticleId')].revisions[0].user;
						if (!mw.util.isIPAddress(articleAuthor)) {
							const placeholdersForNotification = {
								$1: mw.config.get("wgPageName"),
								$2: csdSummary,
								$3: csdReason,
							};
							var message = replacePlaceholders(csdNotificationTemplate, placeholdersForNotification);
							await sendMessageToAuthor(articleAuthor, message);
						}
					} catch (error) {
						console.error('Error in fetching creator or sending message:', error);
					}
				}
			} else {
				mw.notify(mw.message('adiutor-select-speedy-deletion-reason').text(), {
					title: mw.msg('adiutor-warning'),
					type: 'error'
				});
			}
		}

		/**
		 * Creates an API request to tag an article for speedy deletion.
		 * 
		 * @param {string} csdReason - The reason for the speedy deletion.
		 * @param {string} csdSummary - The summary of the speedy deletion.
		 */
		const createApiRequest = async (csdReason, csdSummary) => {
			// API request to tag the article for speedy deletion
			api.postWithToken('csrf', {
				action: 'edit',
				title: mw.config.get("wgPageName"),
				prependtext: csdReason + "\n",
				summary: csdSummary,
				tags: 'Adiutor',
				format: 'json'
			}).done(function () {
				openCsdDialog.value = false;
				mw.notify("Article tagged for speedy deletion", {
					title: mw.msg('adiutor-operation-completed'),
					type: 'success'
				});
			}).fail(function (error) {
				mw.notify("Failed to tag article for speedy deletion: " + error, {
					title: mw.msg('adiutor-operation-failed'),
					type: 'error'
				});
			});
		}

		/**
		 * Replaces placeholders in the input string with the corresponding replacements.
		 * 
		 * @param {string} input - The input string with placeholders.
		 * @param {object} replacements - An object containing the replacements for the placeholders.
		 * @returns {string} - The input string with placeholders replaced by their corresponding replacements.
		 */
		const replacePlaceholders = (input, replacements) => {
			return input.replace(/\$(\d+)/g, function (match, group) {
				var replacement = replacements['$' + group];
				return replacement !== undefined ? replacement : match;
			});
		};

		/**
		 * Replaces a parameter in the input string with a new value.
		 * 
		 * @param {string} input - The input string.
		 * @param {string} parameterName - The name of the parameter to replace.
		 * @param {string} newValue - The new value to replace the parameter with.
		 * @returns {string} - The modified input string with the parameter replaced.
		 */
		function replaceParameter(input, parameterName, newValue) {
			const regex = new RegExp('\\$' + parameterName, 'g');
			if (input.includes('$' + parameterName)) {
				return input.replace(regex, newValue);
			} else {
				return input;
			}
		}

		/**
		 * Retrieves the creator of the page.
		 * @returns {Promise} A promise that resolves with the creator's information.
		 */
		function getCreator() {
			return api.get({
				action: 'query',
				prop: 'revisions',
				rvlimit: 1,
				rvprop: ['user'],
				rvdir: 'newer',
				titles: mw.config.get("wgPageName")
			});
		}

		/**
		 * Sends a notification message to the author of an article.
		 * 
		 * @param {string} articleAuthor - The username of the article author.
		 * @param {string} notificationMessage - The message to be sent as a notification.
		 * @param {string} csdSummary - The summary of the notification.
		 * @returns {Promise<void>} - A promise that resolves when the message is sent successfully.
		 */
		const sendMessageToAuthor = async (articleAuthor, notificationMessage, csdSummary) => {
			try {
				await api.postWithToken('csrf', {
					action: 'edit',
					title: 'User_talk:' + articleAuthor,
					appendtext: '\n' + notificationMessage,
					summary: csdSummary,
					tags: 'Adiutor',
					format: 'json'
				});
			} catch (error) {
				console.error('Error sending message to author:', error);
			}
		};

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
			api,
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