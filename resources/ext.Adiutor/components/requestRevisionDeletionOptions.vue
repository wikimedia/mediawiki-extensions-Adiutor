<template>
    <cdx-message class="top-message">
        <cdx-button :action="saveButtonAction" :class="saveButtonClass" :weight="saveButtonWeight"
            @click="saveConfiguration" :disabled="saveButtonDisabled">
            {{ saveButtonLabel }}
        </cdx-button>
        <h3 style="display: initial;">Request revision deletion configuration</h3>
        <p style="margin-top: 22px;">This page provides you with the ability to customize the settings of Adiutor's request page protection module. When you make adjustments here, it directly updates the configuration stored in the <a href="/MediaWiki:AdiutorRequestRevisionDeletion.json">MediaWiki:AdiutorRequestRevisionDeletion.json</a> page on your behalf. You can easily track and review the history of your modifications on that page.</p>
    </cdx-message>
    <cdx-message type="warning" dismiss-button-label="Close">
        <h3>Parameters</h3>
        <p>You can utilize the parameters within this options, which include:</p>
        <ul>
            <li><strong>$1</strong>: revision number</li>
            <li><strong>$2</strong>: rationale</li>
            <li><strong>$3</strong>: comment</li>
        </ul>
    </cdx-message>
    <cdx-message>
        <strong>Settings</strong>
        <cdx-field :is-fieldset="true">
            <cdx-label input-id="noticeBoardTitle">Noticeboard:</cdx-label>
            <cdx-text-input v-model="noticeBoardTitle" id="noticeBoardTitle"
                aria-label="Speedy Deletion Policy Link"></cdx-text-input>
        </cdx-field>
        <cdx-field :is-fieldset="true">
            <cdx-label input-id="contentPattern">Content pattern:</cdx-label>
            <cdx-text-input v-model="contentPattern" id="contentPattern"
                aria-label="Speedy Deletion Policy Link"></cdx-text-input>
            <template #help-text>
                In this field you can create the basic pattern of the content to be added to the page using the parameters
                specified above, you can use a preload templates in this field or you can also add regular templates.
            </template>
        </cdx-field>
        <cdx-field :is-fieldset="true">
            <cdx-toggle-switch v-model="addNewSection">
                <cdx-label input-id="addNewSection"> Create a new section on a new request queue.</cdx-label>
                <template #description>
                    When this checkbox is enabled, a new section will be added to the notice board page.
                </template>
            </cdx-toggle-switch>
        </cdx-field>
        <cdx-field :is-fieldset="true" v-if="addNewSection">
            <cdx-label input-id="sectionTitle">New section title:</cdx-label>
            <cdx-text-input v-model="sectionTitle" id="sectionTitle"
                aria-label="Speedy Deletion Policy Link"></cdx-text-input>
        </cdx-field>
        <cdx-field :is-fieldset="true" v-if="!addNewSection">
            <cdx-toggle-switch v-model="useExistSection">
                <cdx-label input-id="useExistSection">Use exist section to append or prepend the content
                    pattern.</cdx-label>
                <template #description>
                    When this checkbox is enabled, a new section will be added to the notice board page.
                </template>
            </cdx-toggle-switch>
        </cdx-field>
        <cdx-field :is-fieldset="true" v-if="useExistSection">
            <cdx-label input-id="sectionId">Section ID:</cdx-label>
            <cdx-text-input v-model="sectionId" id="sectionId" aria-label="Speedy Deletion Policy Link"></cdx-text-input>
            <template #help-text>
                The content pattern will be prepended or appended in the section belonging to the ID specified here.
            </template>
        </cdx-field>
        <cdx-field :is-fieldset="true">
            <template #description v-if="!addNewSection && !useExistSection">
                If you do not create a new section or use an existing section, the content will be prepended or appended on
                the page.
            </template>
            <template #label>
                Text Modification Direction
            </template>
            <cdx-radio v-for="direction in textModificationDirectionRadios" :key="'radio-' + direction.value"
                v-model="textModificationDirection" name="radio-group-{{direction.value}}-descriptions"
                :input-value="direction.value">
                {{ direction.label }}
                <template #description> {{ direction.description }} </template>
            </cdx-radio>
        </cdx-field>
    </cdx-message>
    <cdx-message>
        <cdx-field>
            <strong>Summaries</strong>
            <cdx-label input-id="apiPostSummary">API Post Summary:</cdx-label>
            <cdx-text-input v-model="apiPostSummary" id="apiPostSummary" aria-label="API Post Summary"></cdx-text-input>
        </cdx-field>
    </cdx-message>
</template>
<script>
const { defineComponent, watch, ref, computed } = require('vue');
const { CdxCard, CdxCombobox, CdxTabs, CdxTab, CdxMessage, CdxTextInput, CdxToggleSwitch, CdxButton, CdxCheckbox, CdxField, CdxRadio, CdxTextArea } = require('@wikimedia/codex');
const rrdConfiguration = mw.config.get('AdiutorRequestRevisionDeletion');
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
        CdxToggleSwitch,
        CdxField, CdxButton,
        CdxRadio, CdxTextArea
    },
    props: {
        framed: {
            type: Boolean,
            default: false
        }
    },
    setup() {
        const noticeBoardTitle = ref(rrdConfiguration.noticeBoardTitle);
        const addNewSection = ref(rrdConfiguration.addNewSection);
        const useExistSection = ref(rrdConfiguration.useExistSection);
        const sectionTitle = ref(rrdConfiguration.sectionTitle);
        const sectionId = ref(rrdConfiguration.sectionId);
        const contentPattern = ref(rrdConfiguration.contentPattern);
        const apiPostSummary = ref(rrdConfiguration.apiPostSummary);
        const textModificationDirection = ref(rrdConfiguration.textModificationDirection);
        const textModificationDirectionRadios = [
            {
                label: 'Prepend text on the page.',
                description: 'When this checkbox is activated, the specified content in the content pattern input will be prepended on the page.',
                value: 'prependtext',
            }, {
                label: 'Append text to page.',
                description: 'When this checkbox is activated, the specified content in the content pattern input will be appended to the page.',
                value: 'appendtext',
            }
        ];

        return {
            noticeBoardTitle,
            addNewSection,
            useExistSection,
            sectionTitle,
            sectionId,
            contentPattern,
            textModificationDirection,
            apiPostSummary,
            textModificationDirectionRadios,
        };
    },
    data() {
        return {
            // Add properties for controlling button appearance
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
            //this.saveButtonDisabled = true;

            // Prepare the data to be sent in the HTTP request
            const data = {
                title: 'MediaWiki:AdiutorRequestRevisionDeletion.json',
                content: {
                    "noticeBoardTitle": this.noticeBoardTitle,
                    "addNewSection": this.addNewSection,
                    "sectionTitle": this.sectionTitle,
                    "useExistSection": this.useExistSection,
                    "sectionId": this.sectionId,
                    "textModificationDirection": this.textModificationDirection,
                    "contentPattern": this.contentPattern,
                    "apiPostSummary": this.apiPostSummary
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

.cdx-toggle-switch {
    justify-content: space-between;
    display: flex;
}

.cdx-field {
    margin-top: 10px;
    display: block;
}

.top-message {
    margin-top: 10px;
}

.save-button {
    float: right;
}
</style>