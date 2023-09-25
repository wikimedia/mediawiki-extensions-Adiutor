<template>
    <cdx-message class="top-message">
        <cdx-button :action="saveButtonAction" :class="saveButtonClass" :weight="saveButtonWeight"
            @click="saveConfiguration" :disabled="saveButtonDisabled">
            {{ saveButtonLabel }}
        </cdx-button>
        <h3 style="display: initial;">Create speedy deletion configuration</h3>
        <p style="margin-top: 22px;">This page provides you with the ability to customize the settings of Adiutor's request speedy deletion module. When you make adjustments here, it directly updates the configuration stored in the <a href="/MediaWiki:AdiutorCreateSpeedyDeletion.json">MediaWiki:AdiutorCreateSpeedyDeletion.json</a> page on your behalf. You can easily track and review the history of your modifications on that page.</p>
    </cdx-message>
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
    <cdx-field>
        <table width="100%" id="adiutor-options-props">
            <caption>
                Speedy deletion reasons <cdx-button class="add-new-button" weight="quiet" @click="addNewNameSpace">Add
                    New</cdx-button>
            </caption>
            <tr>
                <th>Name Space</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
            <tr v-for="(reasonNamespace, index) in speedyDeletionReasons" :key="index">
                <td><cdx-text-input v-model="reasonNamespace.namespace" aria-label="Namespace value"
                        :disabled="isNamespaceDisabled(reasonNamespace)"></cdx-text-input></td>
                <td><cdx-text-input v-model="reasonNamespace.name" aria-label="Namespace data"></cdx-text-input></td>
                <td><cdx-button action="destructive" @click="deleteNameSpace(reasonNamespace)" :disabled="isNamespaceDisabled(reasonNamespace)">Delete</cdx-button></td>
            </tr>
        </table>
    </cdx-field>
    <cdx-field v-for="(reasonNamespace, namespaceIndex) in speedyDeletionReasons" :key="'nestedTable-' + namespaceIndex">
        <table width="100%" id="adiutor-options-props">
            <caption>
                Deletion reasons for {{ reasonNamespace.name }}
                <cdx-button class="add-new-button" weight="quiet" @click="addNewReason(namespaceIndex)">Add New</cdx-button>
            </caption>
            <tr>
                <th>Value</th>
                <th>Data</th>
                <th>Label</th>
                <th>Help</th>
                <th>Action</th>
            </tr>
            <tr v-for="(reason, reasonIndex) in reasonNamespace.reasons" :key="'reason-' + reasonIndex">
                <td><cdx-text-input style="min-width: 50px;" v-model="reason.value" aria-label="Value"></cdx-text-input>
                </td>
                <td><cdx-text-input v-model="reason.data" aria-label="Data"></cdx-text-input></td>
                <td><cdx-text-input v-model="reason.label" aria-label="Label"></cdx-text-input></td>
                <td><cdx-text-input v-model="reason.help" aria-label="Help"></cdx-text-input></td>
                <td><cdx-button action="destructive" @click="deleteReason(namespaceIndex, reasonIndex)">Delete</cdx-button>
                </td>
            </tr>
        </table>
    </cdx-field>
</template>
<script>
const { defineComponent, watch, ref, computed } = require('vue');
const { CdxCard, CdxCombobox, CdxTabs, CdxTab, CdxMessage, CdxTextInput, CdxCheckbox, CdxField, CdxRadio, CdxTextArea, CdxButton } = require('@wikimedia/codex');
const csdConfiguration = mw.config.get('AdiutorCreateSpeedyDeletion');
module.exports = defineComponent({
    name: '',
    components: {
        CdxCard,
        CdxCombobox,
        CdxTextInput,
        CdxTabs,
        CdxTab,
        CdxMessage,
        CdxCheckbox,
        CdxField,
        CdxRadio, CdxTextArea, CdxButton
    },
    props: {
        framed: {
            type: Boolean,
            default: false
        }
    },
    setup() {
        const speedyDeletionReasons = ref(csdConfiguration.speedyDeletionReasons);
        const csdTemplateStartSingleReason = ref(csdConfiguration.csdTemplateStartSingleReason);
        const csdTemplateStartMultipleReason = ref(csdConfiguration.csdTemplateStartMultipleReason);
        const speedyDeletionPolicyLink = ref(csdConfiguration.speedyDeletionPolicyLink);
        const speedyDeletionPolicyPageShortcut = ref(csdConfiguration.speedyDeletionPolicyPageShortcut);
        const apiPostSummaryforTalkPage = ref(csdConfiguration.apiPostSummaryforTalkPage);
        const apiPostSummaryforLog = ref(csdConfiguration.apiPostSummaryforLog);
        const csdNotificationTemplate = ref(csdConfiguration.csdNotificationTemplate);
        const singleReasonSummary = ref(csdConfiguration.singleReasonSummary);
        const multipleReasonSummary = ref(csdConfiguration.multipleReasonSummary);
        const copyVioReasonValue = ref(csdConfiguration.copyVioReasonValue);
        const postfixReasonUsage = ref(csdConfiguration.postfixReasonUsage);
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
        const multipleReasonSeparation = ref(csdConfiguration.multipleReasonSeparation);
        const multipleReasonSeparationRadios = [
            {
                label: 'Use vertical bar',
                description: 'When activated, reasons within the speedy deletion template are separated by "|".',
                value: 'vertical_bar',
                parameter: '$3',
                output: '|A1|G1}}',
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
            speedyDeletionReasons,
            csdTemplateStartSingleReason,
            csdTemplateStartMultipleReason,
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
            saveButtonLabel: 'Save configurations',
            saveButtonAction: 'progressive',
            saveButtonClass: 'save-button',
            saveButtonWeight: 'primary',
            saveButtonDisabled: false,
        };
    },
    methods: {

        isNamespaceDisabled(reasonNamespace) {
            const disabledNamespaces = ["general", "redirect", "other"];
            return disabledNamespaces.includes(reasonNamespace.namespace);
        },

        addNewNameSpace() {
            const newNameSpace = {
                name: '',
                namespace: '',
                items: []
            };
            this.speedyDeletionReasons.push(newNameSpace);
        },

        deleteNameSpace(namespace) {
            const index = this.speedyDeletionReasons.indexOf(namespace);
            if (index !== -1) {
                this.speedyDeletionReasons.splice(index, 1);
            }
        },

        addNewReason(namespaceIndex) {
            // Find the namespace based on the provided namespaceIndex
            const reasonNamespace = this.speedyDeletionReasons[namespaceIndex];

            if (reasonNamespace) {
                const newReason = {
                    namespace: reasonNamespace.namespace,
                    value: '',
                    data: '',
                    selected: false,
                    label: '',
                    help: '',
                };
                reasonNamespace.reasons.push(newReason);
            }
        },

        deleteReason(namespaceIndex, reasonIndex) {
            // Find the namespace based on the provided namespaceIndex
            const reasonNamespace = this.speedyDeletionReasons[namespaceIndex];

            // Check if the reasonNamespace and the reason exist (a safety check)
            if (reasonNamespace && reasonNamespace.reasons[reasonIndex]) {
                // Use splice to remove the reason at the specified reasonIndex
                reasonNamespace.reasons.splice(reasonIndex, 1);
            }
        },

        async saveConfiguration() {
            // Change the button label and disable it during the save operation
            this.saveButtonLabel = 'Saving...';
            this.saveButtonAction = 'default';
            this.saveButtonDisabled = true;

            // Prepare the data to be sent in the HTTP request
            const data = {
                title: 'MediaWiki:AdiutorCreateSpeedyDeletion.json',
                content: {
                    "speedyDeletionReasons": this.speedyDeletionReasons,
                    "csdTemplateStartSingleReason": this.csdTemplateStartSingleReason,
                    "csdTemplateStartMultipleReason": this.csdTemplateStartMultipleReason,
                    "postfixReasonUsage": this.postfixReasonUsage,
                    "multipleReasonSeparation": this.multipleReasonSeparation,
                    "speedyDeletionPolicyLink": this.speedyDeletionPolicyLink,
                    "speedyDeletionPolicyPageShortcut": this.speedyDeletionPolicyPageShortcut,
                    "apiPostSummaryforTalkPage": this.apiPostSummaryforTalkPage,
                    "apiPostSummaryforLog": this.apiPostSummaryforLog,
                    "csdNotificationTemplate": this.csdNotificationTemplate,
                    "singleReasonSummary": this.singleReasonSummary,
                    "multipleReasonSummary": this.multipleReasonSummary,
                    "copyVioReasonValue": this.copyVioReasonValue,
                }
            };

            // Define the API endpoint URL
            const apiUrl = '/mediawiki/rest.php/adiutor/v0/updatelocalconfiguration';

            try {
                // Send a POST request to the API with the data
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                if (!response.ok) {
                    // Handle HTTP error if the response status is not OK
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                // Parse the response data as JSON
                const responseData = await response.json();

                if (responseData.status === 'success') {
                    // Display a success notification if the update was successful
                    mw.notify(mw.message('adiutor-localization-settings-has-been-updated').text(), {
                        title: mw.msg('adiutor-operation-completed'),
                        type: 'success'
                    });
                    // Revert the button label and enable it after a delay (2 seconds)
                    this.saveButtonLabel = 'Save configurations';
                    this.saveButtonAction = 'progressive';
                    this.saveButtonDisabled = false;
                } else {
                    // Log an error message if the update was not successful
                    console.error('Error updating configuration');
                    this.saveButtonLabel = 'Try again';
                    this.saveButtonAction = 'destructive';
                    this.saveButtonDisabled = false;
                }
            } catch (error) {
                // Handle any fetch-related errors and log them
                console.error('Fetch error:', error);
            }
        }

    },
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
</style>