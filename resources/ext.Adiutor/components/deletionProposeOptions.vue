<template>
    <cdx-message class="top-message">
        <cdx-button :action="saveButtonAction" :class="saveButtonClass" :weight="saveButtonWeight"
            @click="saveConfiguration" :disabled="saveButtonDisabled">
            {{ saveButtonLabel }}
        </cdx-button>
        <h3 style="display: initial;">Create speedy deletion configuration</h3>
        <p style="margin-top: 22px;">This page provides you with the ability to customize the settings of Adiutor's request speedy deletion module. When you make adjustments here, it directly updates the configuration stored in the <a href="/MediaWiki:AdiutorDeletionPropose.json">MediaWiki:AdiutorDeletionPropose.json</a> page on your behalf. You can easily track and review the history of your modifications on that page.</p>
    </cdx-message>
    <cdx-message type="warning" dismiss-button-label="Close">
        <h3>Parameters</h3>
        <p>You can utilize the parameters within this options, which include:</p>
        <ul>
            <li><strong>$1</strong>: page name</li>
            <li><strong>$2</strong>: rationale</li>
            <li><strong>$3</strong>: day</li>
            <li><strong>$4</strong>: month</li>
            <li><strong>$5</strong>: year</li>
            <li><strong>$6</strong>: requester</li>
        </ul>
    </cdx-message>
    <cdx-message>
        <strong>Settings</strong>
        <cdx-label input-id="prodNotificationTemplate">Prod Notification Template:</cdx-label>
        <cdx-text-input v-model="prodNotificationTemplate" id="prodNotificationTemplate"
            aria-label="Prod Notification Template"></cdx-text-input>
    </cdx-message>
    <cdx-message>
        <cdx-field>
            <strong>In-page request templating configuration</strong>
            <cdx-label input-id="standardProposeTemplate">Proposed Deletion Template:</cdx-label>
            <cdx-text-input v-model="standardProposeTemplate" id="standardProposeTemplate"
                aria-label="Proposed Deletion Template"></cdx-text-input>
            <template #help-text>
                This is a speedy deletion request template to be used for a single reason.
            </template>
        </cdx-field>
        <cdx-field>
            <cdx-label input-id="livingPersonProposeTemplate">Proposed deletion of unsourced BLPs Template:</cdx-label>
            <cdx-text-input v-model="livingPersonProposeTemplate" id="livingPersonProposeTemplate"
                aria-label="Proposed deletion of unsourced BLPs Template"></cdx-text-input>
            <template #help-text>
                This is a speedy deletion request template to be used for a single reason.
            </template>
        </cdx-field>
    </cdx-message>
    <cdx-message>
        <cdx-field>
            <strong>Summaries</strong>
            <cdx-label input-id="apiPostSummaryforLog">API Post Summary for Log:</cdx-label>
            <cdx-text-input v-model="apiPostSummaryforLog" id="apiPostSummaryforLog"
                aria-label="API Post Summary for Log"></cdx-text-input>
            <cdx-label input-id="apiPostSummary">API Post Summary:</cdx-label>
            <cdx-text-input v-model="apiPostSummary" id="apiPostSummary"
                aria-label="API Post Summary for Talk Page"></cdx-text-input>
            <cdx-label input-id="apiPostSummaryforCreator">API Post Summary for Creator:</cdx-label>
            <cdx-text-input v-model="apiPostSummaryforCreator" id="apiPostSummaryforCreator"
                aria-label="API Post Summary for Creator"></cdx-text-input>
        </cdx-field>
    </cdx-message>
</template>
<script>
const { defineComponent, watch, ref, computed } = require('vue');
const { CdxCard, CdxCombobox, CdxTabs, CdxTab, CdxMessage, CdxTextInput, CdxCheckbox, CdxField, CdxRadio, CdxTextArea, CdxButton } = require('@wikimedia/codex');
const dprConfiguration = mw.config.get('AdiutorDeletionPropose');
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
        const standardProposeTemplate = ref(dprConfiguration.standardProposeTemplate);
        const livingPersonProposeTemplate = ref(dprConfiguration.livingPersonProposeTemplate);
        const apiPostSummary = ref(dprConfiguration.apiPostSummary);
        const apiPostSummaryforCreator = ref(dprConfiguration.apiPostSummaryforCreator);
        const prodNotificationTemplate = ref(dprConfiguration.prodNotificationTemplate);
        const apiPostSummaryforLog = ref(dprConfiguration.apiPostSummaryforLog);

        return {
            standardProposeTemplate,
            livingPersonProposeTemplate,
            apiPostSummary,
            apiPostSummaryforCreator,
            prodNotificationTemplate,
            apiPostSummaryforLog,

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

        async saveConfiguration() {
            // Change the button label and disable it during the save operation
            this.saveButtonLabel = 'Saving...';
            this.saveButtonAction = 'default';
            this.saveButtonDisabled = true;

            // Prepare the data to be sent in the HTTP request
            const data = {
                title: 'MediaWiki:AdiutorDeletionPropose.json',
                content: {
                    "standardProposeTemplate": this.standardProposeTemplate,
                    "livingPersonProposeTemplate": this.livingPersonProposeTemplate,
                    "apiPostSummary": this.apiPostSummary,
                    "apiPostSummaryforCreator": this.apiPostSummaryforCreator,
                    "prodNotificationTemplate": this.prodNotificationTemplate,
                    "apiPostSummaryforLog": this.apiPostSummaryforLog,
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
                        title: mw.msg('adiutor-warning'),
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