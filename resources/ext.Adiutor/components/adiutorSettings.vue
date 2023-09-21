<template>
    <div class="adiutor-settings">
        <div class="header">
            <h5>Customization and Localization</h5>
            <p>This dedicated page allows you to not only customize the Adiutor extension for your local wiki but also
                ensures localization. Here, you will find an extensive repository of information and resources,
                offering a thorough exploration of configuring procedures tailored specifically to your wiki. Our aim is to
                provide you with comprehensive, step-by-step guidance that simplifies the process of setting up and
                customizing these procedures to precisely match your wiki's needs, language preferences, and regional
                settings.</p>
        </div>
    </div>
    <cdx-message class="top-message" type="info" dismiss-button-label="Close">This page contains configuration data for the
        Adiutor tool's speedy deletion reasons for various namespaces on your wiki. These settings allow you to customize
        the reasons for which pages, files, categories, and other elements can be speedy deleted on your wiki.</cdx-message>
    <cdx-message type="warning" dismiss-button-label="Close">
        <h3>Parameters</h3>
        <p>You can utilize the parameters within this options, which include:</p>
        <ul>
            <li><strong>$1</strong>: page name</li>
            <li><strong>$2</strong>: reason data</li>
            <li><strong>$3</strong>: reason value</li>
        </ul>
    </cdx-message>
    <cdx-message>
        <strong>Settings</strong>
        <cdx-label input-id="speedyDeletionPolicyLink">Speedy Deletion Policy Link:</cdx-label>
        <cdx-text-input v-model="speedyDeletionPolicyLink" id="speedyDeletionPolicyLink"
            aria-label="Speedy Deletion Policy Link"></cdx-text-input>
        <cdx-label input-id="speedyDeletionPolicyPageShortcut">Speedy Deletion Policy Page Shortcut:</cdx-label>
        <cdx-text-input v-model="speedyDeletionPolicyPageShortcut" id="speedyDeletionPolicyPageShortcut"
            aria-label="Speedy Deletion Policy Page Shortcut"></cdx-text-input>
        <cdx-label input-id="csdNotificationTemplate">CSD Notification Template:</cdx-label>
        <cdx-text-input v-model="csdNotificationTemplate" id="csdNotificationTemplate"
            aria-label="CSD Notification Template"></cdx-text-input>
        <cdx-label input-id="copyVioReasonValue">Copy Vio Reason Value:</cdx-label>
        <cdx-text-input v-model="copyVioReasonValue" id="copyVioReasonValue"
            aria-label="Copy Vio Reason Value"></cdx-text-input>
    </cdx-message>
    <cdx-message>
        <cdx-field>
            <strong>In-page request templating configuration</strong>
            <cdx-label input-id="csdTemplateStartSingleReason">Single reason template:</cdx-label>
            <cdx-text-input v-model="csdTemplateStartSingleReason" id="csdTemplateStartSingleReason"
                aria-label="CSD Template Start Single Reason"></cdx-text-input>
            <template #help-text>
                This is a speedy deletion request template to be used for a single reason.
            </template>
        </cdx-field>
        <cdx-field :is-fieldset="true">
            <template #label>
                Postfix Reason Usage
            </template>
            <cdx-radio v-for="postfix in postfixReasonUsageRadios" :key="'radio-' + postfix.value"
                v-model="postfixReasonUsage" name="radio-group-{{postfix.value}}-descriptions" :input-value="postfix.value">
                {{ postfix.label }}
                <template #description> {{ postfix.description }}
                    <h5>Output:</h5>
                    <pre> {{ csdTemplateStartSingleReason }}{{ postfix.output }}</pre>
                </template>
            </cdx-radio>
        </cdx-field>
        <cdx-field>
            <cdx-label input-id="csdTemplateStartMultipleReason">Multiple reason template:</cdx-label>
            <cdx-text-input v-model="csdTemplateStartMultipleReason" id="csdTemplateStartMultipleReason"
                aria-label="CSD Template Start Multiple Reason"></cdx-text-input>
            <template #help-text>
                This is a speedy deletion request template to be used for a multiple reason.
            </template>
        </cdx-field>
        <cdx-field :is-fieldset="true">
            <template #label>
                Multiple reason separation
            </template>
            <cdx-radio v-for="separator in multipleReasonSeparationRadios" :key="'radio-' + separator.value"
                v-model="multipleReasonSeparation" name="radio-group-{{separator.value}}-descriptions"
                :input-value="separator.value">
                {{ separator.label }}
                <template #description> {{ separator.description }}
                    <h5>Output:</h5>
                    <pre> {{ csdTemplateStartMultipleReason }}{{ separator.output }}</pre>
                </template>
            </cdx-radio>
        </cdx-field>
    </cdx-message>
    <cdx-message>
        <cdx-field>
            <strong>Summaries</strong>
            <cdx-label input-id="singleReasonSummary">Single Reason Summary:</cdx-label>
            <cdx-text-input v-model="singleReasonSummary" id="singleReasonSummary"
                aria-label="Single Reason Summary"></cdx-text-input>
            <cdx-label input-id="multipleReasonSummary">Multiple Reason Summary:</cdx-label>
            <cdx-text-input v-model="multipleReasonSummary" id="multipleReasonSummary"
                aria-label="Multiple Reason Summary"></cdx-text-input>
            <cdx-label input-id="apiPostSummaryforTalkPage">API Post Summary for Talk Page:</cdx-label>
            <cdx-text-input v-model="apiPostSummaryforTalkPage" id="apiPostSummaryforTalkPage"
                aria-label="API Post Summary for Talk Page"></cdx-text-input>
            <cdx-label input-id="apiPostSummaryforLog">API Post Summary for Log:</cdx-label>
            <cdx-text-input v-model="apiPostSummaryforLog" id="apiPostSummaryforLog"
                aria-label="API Post Summary for Log"></cdx-text-input>
        </cdx-field>
    </cdx-message>
</template>
<script>
const { defineComponent, watch, ref, computed } = require('vue');
const { CdxCard, CdxCombobox, CdxTabs, CdxTab, CdxMessage, CdxTextInput, CdxCheckbox, CdxField, CdxRadio } = require('@wikimedia/codex');
const csdConfiguration = require('../localization/Csd.json');
module.exports = defineComponent({
    name: 'adiutorSettings',
    components: {
        CdxCard,
        CdxCombobox,
        CdxTextInput,
        CdxTabs,
        CdxTab,
        CdxMessage,
        CdxCheckbox,
        CdxField,
        CdxRadio
    },
    props: {
        framed: {
            type: Boolean,
            default: false
        }
    },
    setup() {
        const csdTemplateStartSingleReason = csdConfiguration.csdTemplateStartSingleReason;
        const csdTemplateStartMultipleReason = csdConfiguration.csdTemplateStartMultipleReason;
        const reasonAndSeparator = csdConfiguration.reasonAndSeparator;
        const speedyDeletionPolicyLink = csdConfiguration.speedyDeletionPolicyLink;
        const speedyDeletionPolicyPageShortcut = csdConfiguration.speedyDeletionPolicyPageShortcut;
        const apiPostSummaryforTalkPage = csdConfiguration.apiPostSummaryforTalkPage;
        const apiPostSummaryforLog = csdConfiguration.apiPostSummaryforLog;
        const csdNotificationTemplate = csdConfiguration.csdNotificationTemplate;
        const singleReasonSummary = csdConfiguration.singleReasonSummary;
        const multipleReasonSummary = csdConfiguration.multipleReasonSummary;
        const copyVioReasonValue = csdConfiguration.copyVioReasonValue;
        const postfixReasonUsage = csdConfiguration.postfixReasonUsage;
        const postfixReasonUsageRadios = [
            {
                label: 'Use reason value',
                description: 'When activated, only the reason value is inserted. It is mandatory to use the $3 parameter in the request template.',
                value: 'value',
                parameter: '$3',
                output: 'A1}}',
            },
            {
                label: 'Use reason data',
                description: 'When activated, only the reason data is inserted. It is mandatory to use the $2 parameter in the request template.',
                value: 'data',
                parameter: '$2',
                output: '[[WP:CSD#A1|A1]]: Short article without enough context to identify the subject}}',
            }
        ];
        const multipleReasonSeparation = csdConfiguration.multipleReasonSeparation;
        const multipleReasonSeparationRadios = [
            {
                label: 'Use vertical bar',
                description: 'When activated, reasons within the speedy deletion template are separated by "|".',
                value: 'vertical_bar',
                parameter: '$3',
                output: 'A1|G1}}',
            },
            {
                label: 'Default',
                description: 'When activated, only the reason data is inserted. It is mandatory to use the $2 parameter in the request template.',
                value: 'default',
                parameter: '$2',
                output: '[[WP:CSD#A1]] and [[WP:CSD#G1]]}}',
            }
        ];

        return {
            csdTemplateStartSingleReason,
            csdTemplateStartMultipleReason,
            reasonAndSeparator,
            speedyDeletionPolicyLink,
            speedyDeletionPolicyPageShortcut,
            apiPostSummaryforTalkPage,
            apiPostSummaryforLog,
            csdNotificationTemplate,
            singleReasonSummary,
            multipleReasonSummary,
            copyVioReasonValue,
            postfixReasonUsage,
            postfixReasonUsageRadios,
            multipleReasonSeparation,
            multipleReasonSeparationRadios
        };
    },
    data() {
        return {

        };
    }
});
</script>
  
<style lang="less">
cdx-label {
    font-weight: 900;
    margin-top: 10px;
    display: block;
}

.cdx-field {
    margin-top: 10px;
    display: block;
}

.top-message {
    margin-top: 10px;
}

.adiutor-settings .header {
    background-color: #eaf3ff;
    display: block;
    align-items: baseline;
    justify-content: space-between;
    height: 17em;
    padding: 20px;
    background-image: url(../../ext.Adiutor.images/opt-background.png);
    background-position: right 10px;
    background-repeat: no-repeat;
    background-size: 437px;
    margin-bottom: 10px;
}

.adiutor-settings .header p {
    width: 66%;
}

.adiutor-settings h5 {
    margin: 0;
    padding: 0;
    font-size: 24px;
}
</style>