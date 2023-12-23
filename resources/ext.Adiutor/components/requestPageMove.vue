<template>
	<cdx-dialog class="rpm-dialog" v-model:open="openRpmDialog" :title="$i18n('adiutor-pmr-header-title')" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" @primary="requestPageMove" @default="openRpmDialog = true">
		<div class="header">
			<p>{{ $i18n('adiutor-pmr-header-description') }}</p>
		</div>
		<cdx-field class="rpm-dialog-body">
			<cdx-label class="rpm-label"><strong>{{ $i18n('adiutor-new-name') }}</strong></cdx-label>
			<cdx-text-input v-model="newPageName" aria-label="New page name"></cdx-text-input>
			<cdx-label class="rpm-label"><strong>{{ $i18n('adiutor-rationale') }}</strong></cdx-label>
			<cdx-text-area v-model="rationaleInput" :placeholder="rationalePlaceholder"></cdx-text-area>
		</cdx-field>
	</cdx-dialog>
</template>
<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxTextArea, CdxSelect } = require('@wikimedia/codex');
const rpmConfiguration = mw.config.get('AdiutorRequestPageMove');
var noticeBoardTitle = rpmConfiguration.noticeBoardTitle;
var noticeBoardLink = noticeBoardTitle.replace(/ /g, '_');
var addNewSection = rpmConfiguration.addNewSection;
var sectionTitle = rpmConfiguration.sectionTitle;
var useExistSection = rpmConfiguration.useExistSection;
var sectionId = rpmConfiguration.sectionId;
var textModificationDirection = rpmConfiguration.textModificationDirection;
var contentPattern = rpmConfiguration.contentPattern;
var apiPostSummary = rpmConfiguration.apiPostSummary;
var pageTitle = mw.config.get("wgPageName").replace(/_/g, " ");
module.exports = defineComponent({
	name: 'requestPageMove',
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
			protectionDurations: ref(rpmConfiguration.protectionDurations),
			protectionTypes: ref(rpmConfiguration.protectionTypes),
			durationSelection: null,
			typeSelection: null,
			rationalePlaceholder: mw.msg('adiutor-rpm-rationale-placeholder')
		};
	},

	setup() {
		const durationSelection = ref('');
		const typeSelection = ref('');
		const rationaleInput = ref('');
		const newPageName = ref('');
		const openRpmDialog = ref(true);
		const primaryAction = {
			label: mw.msg('adiutor-create-request'),
			actionType: 'progressive'
		};

		const requestPageMove = async () => {

			const placeholders = {
				$1: pageTitle,
				$2: newPageName.value,
				$3: rationaleInput.value,
			};
			const preparedContent = replacePlaceholders(contentPattern, placeholders);

			if (!newPageName.value || !rationaleInput.value) {
				mw.notify('adiutor-rpm-incomplete-request-details', {
					title: mw.msg('adiutor-operation-failed'),
					type: 'error'
				});
			} else {
				createApiRequest(preparedContent);
			}
		};

		const createApiRequest = async (preparedContent) => {
			var api = new mw.Api();
			var apiParams = {
				action: 'edit',
				title: noticeBoardTitle,
				summary: replaceParameter(apiPostSummary, '1', pageTitle),
				tags: 'Adiutor',
				format: 'json'
			};

			if (addNewSection) {
				apiParams.section = 'new';
				apiParams.sectiontitle = replaceParameter(sectionTitle, '1', pageTitle);
				apiParams.text = preparedContent;
			} else {
				if (useExistSection) {
					apiParams.section = sectionId;
				}
				apiParams[textModificationDirection === 'appendtext' ? 'appendtext' : textModificationDirection === 'prependtext' ? 'prependtext' : 'text'] = preparedContent + '\n';
			}

			api.postWithToken('csrf', apiParams).done(function () {
				window.location = '/' + noticeBoardLink;
				openRpmDialog.value = false;
			});
		}

		const replacePlaceholders = (input, replacements) => {
			return input.replace(/\$(\d+)/g, function (match, group) {
				var replacement = replacements['$' + group];
				return replacement !== undefined ? replacement : match;
			});
		};

		function replaceParameter(input, parameterName, newValue) {
			const regex = new RegExp('\\$' + parameterName, 'g');
			if (input.includes('$' + parameterName)) {
				return input.replace(regex, newValue);
			} else {
				return input;
			}
		}

		return {
			openRpmDialog,
			primaryAction,
			rationaleInput,
			newPageName,
			durationSelection,
			typeSelection,
			requestPageMove
		};
	},
});
</script>

<style lang="css">
.rpm-dialog {

	max-width: 29.571429em;

}

.rpm-dialog .cdx-dialog {
	max-width: 448px;
	padding-top: 10px;
	padding-bottom: 0;
}

.rpm-dialog .cdx-field__control {
	display: grid;
}

.rpm-dialog-body {
	padding: 20px;
	display: grid;
	width: inherit;
}

.rpm-dialog .cdx-dialog__body {
	flex-grow: 1;
	margin-top: 0;
	padding: 0;
	overflow-y: auto;
	padding-bottom: 20px;
}


.rpm-dialog .cdx-dialog--dividers .cdx-dialog__body {
	padding-top: 0;
}

.rpm-dialog .rpm-reason-field {
	display: flex;
	flex-direction: column;
	width: 50%;
}

.rpm-dialog .cdx-dialog__header {
	display: flex;
	align-items: center;
	justify-content: flex-end;
	box-sizing: border-box;
	width: 100%;
	padding: 10px 20px 10px;
	font-weight: 700;
}

.rpm-label {
	margin-top: 10px;
}

.rpm-dialog .header {
	background-color: #eaf3ff;
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 9em;
	padding: 20px;
	background-image: url(../../ext.Adiutor.images/rpm-background.png);
	background-position: right -30px;
	background-repeat: no-repeat;
	background-size: 200px;
}

.rpm-dialog .cdx-dialog__footer {
	padding: 20px !important;
	border-top: 1px solid #a2a9b1;
}

.rpm-dialog .header p {
	width: 60%;
}

.rpm-dialog h2 {
	margin: 0;
	padding: 0;
	font-size: 1.125em !important
}

.rpm-dialog .cdx-select-vue {
	margin-bottom: 10px !important;
}
</style>