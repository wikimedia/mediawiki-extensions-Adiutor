<template>
	<cdx-dialog class="rpp-dialog" v-model:open="openRppDialog" title="Page Protection Request" close-button-label="Close"
		:show-dividers="true" :primary-action="primaryAction" @primary="requestPageProtection"
		@default="openRppDialog = true" :default-action="defaultAction">
		<div class="header">
			<h5>{{ $i18n('adiutor-rpp-header-title') }}</h5>
			<p>{{ $i18n('adiutor-rpp-header-description') }}</p>
		</div>
		<cdx-field class="rpp-dialog-body">
			<cdx-label class="adt-label">
				<strong>{{ $i18n('adiutor-protection-type') }}</strong>
			</cdx-label>
			<cdx-select v-model:selected="durationSelection" :menu-items="protectionDurations"
				default-label="Choose duration"></cdx-select>
			<cdx-select v-model:selected="typeSelection" :menu-items="protectionTypes"
				default-label="Select protection type"></cdx-select>
			<cdx-label><strong>{{ $i18n('adiutor-rationale') }}</strong></cdx-label>
			<cdx-text-area v-model="rationaleInput" :placeholder="rationalePlaceholder"></cdx-text-area>
		</cdx-field>
	</cdx-dialog>
</template>
<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxTextArea, CdxSelect } = require('@wikimedia/codex');
const rppConfiguration = mw.config.get('AdiutorRequestPageProtection');
var noticeBoardTitle = rppConfiguration.noticeBoardTitle;
var noticeBoardLink = noticeBoardTitle.replace(/ /g, '_');
var addNewSection = rppConfiguration.addNewSection;
var sectionTitle = rppConfiguration.sectionTitle;
var useExistSection = rppConfiguration.useExistSection;
var sectionId = rppConfiguration.sectionId;
var textModificationDirection = rppConfiguration.textModificationDirection;
var contentPattern = rppConfiguration.contentPattern;
var apiPostSummary = rppConfiguration.apiPostSummary;
var pageTitle = mw.config.get("wgPageName").replace(/_/g, " ");
module.exports = defineComponent({
	name: 'requestPageProtection',
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
			protectionDurations: ref(rppConfiguration.protectionDurations),
			protectionTypes: ref(rppConfiguration.protectionTypes),
			durationSelection: null,
			typeSelection: null,
			rationalePlaceholder: mw.msg('adiutor-rpp-rationale-placeholder')
		};
	},

	setup() {
		const durationSelection = ref('');
		const typeSelection = ref('');
		const rationaleInput = ref('');
		const openRppDialog = ref(true);
		const primaryAction = {
			label: mw.msg('adiutor-create-request'),
			actionType: 'progressive'
		};

		const defaultAction = {
			label: mw.msg('adiutor-protection-policy'),
		};

		const requestPageProtection = async () => {

			const placeholders = {
				$1: pageTitle,
				$2: durationSelection.value,
				$3: typeSelection.value,
				$4: rationaleInput.value,
			};
			const preparedContent = replacePlaceholders(contentPattern, placeholders);

			if (!durationSelection.value || !typeSelection.value || !rationaleInput.value) {
				mw.notify('adiutor-rpp-incomplete-request-details', {
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
				window.location = 'http://localhost:8888/mediawiki/index.php/' + noticeBoardLink;
				openRppDialog.value = false;
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
			openRppDialog,
			primaryAction,
			defaultAction,
			rationaleInput,
			durationSelection,
			typeSelection,
			requestPageProtection
		};
	},
});
</script>

<style lang="css">
.rpp-dialog {

	max-width: 29.571429em;

}

.rpp-dialog .cdx-dialog {
	max-width: 448px;
	padding-top: 10px;
	padding-bottom: 0;
}

.rpp-dialog .cdx-field__control {
	display: grid;
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
	padding-bottom: 20px;
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
	padding: 10px 20px 10px;
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
	height: 9em;
	padding: 20px;
	background-image: url(../../ext.Adiutor.images/rpp-background.png);
	background-position: right 22px;
	background-repeat: no-repeat;
	background-size: 200px;
}

.rpp-dialog .cdx-dialog__footer {
	padding: 20px !important;
	border-top: 1px solid #a2a9b1;
}

.rpp-dialog .header p {
	width: 60%;
}

.rpp-dialog h2 {
	margin: 0;
	padding: 0;
	font-size: 1.125em !important
}

.rpp-dialog .cdx-select-vue {
	margin-bottom: 10px !important;
}
</style>