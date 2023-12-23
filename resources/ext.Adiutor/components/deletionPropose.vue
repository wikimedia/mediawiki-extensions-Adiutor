<template>
	<!-- This component is a dialog for proposing the deletion of an article. -->
	<cdx-dialog class="prd-dialog" v-model:open="openPrdDialog" :title="$i18n('adiutor-prd-header-title')" close-button-label="Close"
		:show-dividers="true" @default="openPrdDialog = true">
		<!-- Header section with title and description. -->
		<div class="header">
			<p>{{ $i18n('adiutor-prd-header-description') }}</p>
		</div>

		<!-- Main body section for deletion type selection and rationale. -->
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

		<!-- Footer section with notification option and submit button. -->
		<div class="footer">
			<cdx-checkbox v-model="informCreator" :inline="true">
				{{ $i18n('adiutor-inform-creator') }}
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

// Configurations for the propose deletion functionality.
const prdConfiguration = mw.config.get('AdiutorDeletionPropose');
const {
	standardProposeTemplate,
	livingPersonProposeTemplate,
	apiPostSummary,
	apiPostSummaryforCreator,
	prodNotificationTemplate,
	apiPostSummaryforLog
} = prdConfiguration;

module.exports = defineComponent({
	name: 'proposeDeletion',
	components: { CdxRadio, CdxButton, CdxDialog, CdxCheckbox, CdxField, CdxLabel, CdxTextArea, CdxToggleSwitch },
	data() {
		return {
			rationalePlaceholder: mw.msg('adiutor-prd-deletion-rationale')
		};
	},
	setup() {
		const api = new mw.Api();
		const informCreator = ref(true);
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

		/**
		 * Function to propose deletion.
		 * 
		 * @async
		 * @function proposeForDeletion
		 * @returns {Promise<void>} - A promise that resolves when the deletion request is sent.
		 */
		const proposeForDeletion = async () => {
			const selectedRadioOption = radioValue.value;
			const rationaleText = textareaValue.value;
			await sendDeletionRequest(selectedRadioOption, rationaleText);

			if (informCreator.value) {
				try {
					const creatorData = await getCreator();
					const author = creatorData.query.pages[mw.config.get('wgArticleId')].revisions[0].user;

					if (!mw.util.isIPAddress(author)) {
						const pageTitle = mw.config.get('wgPageName').replace(/_/g, " ");
						const notificationMessage = replaceParameter(prodNotificationTemplate, '1', pageTitle);
						await sendMessageToAuthor(author, notificationMessage);
					}
				} catch (error) {
					console.error('Error in fetching creator or sending message:', error);
				}
			}

			openPrdDialog.value = false;
		};

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
		 * Sends a deletion request for the specified option and rationale.
		 * @param {string} option - The deletion option ('standardPropose' or 'livingPersonPropose').
		 * @param {string} rationale - The rationale for the deletion request.
		 * @returns {Promise<void>} - A promise that resolves when the deletion request is sent successfully.
		 */
		const sendDeletionRequest = async (option, rationale) => {
			const mwConfig = mw.config.get(["wgArticleId", "wgPageName", "wgUserGroups", "wgUserName", "wgWikiID", "wgMonthNames"]);
			const date = new Date();
			const localMonthsNames = mwConfig.wgMonthNames;
			let PRDText = '';
			const placeholders = {
				$1: mwConfig.wgPageName.replace(/_/g, " "),
				$2: rationale,
				$3: date.getDate(),
				$4: localMonthsNames[date.getMonth()],
				$5: date.getFullYear(),
				$6: mwConfig.wgUserName,
			};
			if (option === 'standardPropose') {
				PRDText = replacePlaceholders(standardProposeTemplate, placeholders);
			} else {
				PRDText = replacePlaceholders(livingPersonProposeTemplate, placeholders);
			}
			try {
				const response = await api.postWithToken('csrf', {
					action: 'edit',
					title: mwConfig.wgPageName,
					prependtext: PRDText + "\n",
					summary: replaceParameter(apiPostSummary, '1', mwConfig.wgPageName.replace(/_/g, " ")),
					tags: 'Adiutor',
					format: 'json'
				});
				mw.notify("Article proposed for deletion", {
					title: mw.msg('adiutor-operation-completed'),
					type: 'success'
				});
				location.reload();
			} catch (error) {
				console.error('Error during API request:', error);
			}
		};

		/**
		 * Sends a message to the author of a page.
		 * @param {string} Author - The username of the author.
		 * @param {string} message - The message to be sent.
		 * @returns {Promise<void>} - A promise that resolves when the message is sent successfully.
		 */
		const sendMessageToAuthor = async (Author, message) => {
			try {
				await api.postWithToken('csrf', {
					action: 'edit',
					title: 'User_talk:' + Author,
					appendtext: '\n' + message,
					summary: replaceParameter(apiPostSummaryforCreator, '1', mw.config.get('wgPageName').replace(/_/g, " ")),
					tags: 'Adiutor',
					format: 'json'
				});
			} catch (error) {
				console.error('Error sending message to author:', error);
			}
		};

		/**
		 * Replaces placeholders in the input string with the corresponding replacements.
		 *
		 * @param {string} input - The input string with placeholders.
		 * @param {object} replacements - An object containing the replacements for the placeholders.
		 * @returns {string} - The input string with the placeholders replaced.
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
		
		return {
			openPrdDialog,
			standardPropose,
			livingPersonPropose,
			textareaValue,
			informCreator,
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