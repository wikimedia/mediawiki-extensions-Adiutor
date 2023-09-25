<template>
    <cdx-message class="top-message">
        <cdx-button :action="saveButtonAction" :class="saveButtonClass" :weight="saveButtonWeight"
            @click="saveConfiguration" :disabled="saveButtonDisabled">
            {{ saveButtonLabel }}
        </cdx-button>
        <h3 style="display: initial;">Article tagging configuration</h3>
        <p style="margin-top: 22px;">This page provides you with the ability to customize the settings of Adiutor's article
            tagging module. When you make adjustments here, it directly updates the configuration stored in the <a
                href="/MediaWiki:AdiutorArticleTagging.json">MediaWiki:AdiutorArticleTagging.json</a> page on your behalf.
            You can easily track and review the history of your modifications on that page.</p>
    </cdx-message>
    <cdx-message>
        <strong>Settings</strong>
        <cdx-field :is-fieldset="true">
            <cdx-toggle-switch v-model="useMultipleIssuesTemplate">
                <cdx-label input-id="useMultipleIssuesTemplate">Use Multiple Issues Template:</cdx-label>
                <template #description>
                    When activated, if more than one tag is added to an article, it puts them in a multi-issue template.
                </template>
            </cdx-toggle-switch>
        </cdx-field>
        <cdx-field v-if="useMultipleIssuesTemplate">
            <cdx-label input-id="multipleIssuesTemplate">Multiple Issues Template:</cdx-label>
            <cdx-text-input v-model="multipleIssuesTemplate" id="multipleIssuesTemplate"
                aria-label="Multiple issues template"></cdx-text-input>
            <template #help-text>
                This is a speedy deletion request template to be used for a single reason.
                <h5>Output:</h5>
                <pre>{{ generateTemplateOutput() }}</pre>
            </template>
        </cdx-field>
        <cdx-field>
            <cdx-label input-id="uncategorizedTemplate">Uncategorized Template:</cdx-label>
            <cdx-text-input v-model="uncategorizedTemplate" id="uncategorizedTemplate"
                aria-label="Uncategorized template"></cdx-text-input>
            <template #help-text>
                This is a speedy deletion request template to be used for a single reason.
            </template>
        </cdx-field>
    </cdx-message>
    <cdx-message>
        <cdx-field>
            <strong>Summaries</strong>
            <cdx-label input-id="apiPostSummary">API Post Summary:</cdx-label>
            <cdx-text-input v-model="apiPostSummary" id="apiPostSummary"
                aria-label="API Post Summary for Talk Page"></cdx-text-input>
        </cdx-field>
    </cdx-message>
    <cdx-field>
        <table width="100%" id="adiutor-options-props">
            <caption>
                Tag labels <cdx-button class="add-new-button" weight="quiet" @click="addNewTagLabel">Add New</cdx-button>
            </caption>
            <tr>
                <th>Label</th>
                <th style="text-align: right;">Action</th>
            </tr>
            <tr v-for="(label, index) in tagList" :key="index">
                <td><cdx-text-input v-model="label.label" aria-label="Namespace value"></cdx-text-input></td>
                <td style="text-align: right;"><cdx-button action="destructive" @click="deleteLabel(label)">Delete</cdx-button></td>
            </tr>
        </table>
    </cdx-field>
    <cdx-field v-for="(label, labelIndex) in tagList" :key="'nestedTable-' + labelIndex">
        <table width="100%" id="adiutor-options-props">
            <caption>
                Tags for {{ label.label }}
                <cdx-button class="add-new-button" weight="quiet" @click="addNewTag(labelIndex)">Add New</cdx-button>
            </caption>
            <tr>
                <th>Template</th>
                <th>Description</th>
                <th style="text-align: right;">Action</th>
            </tr>
            <tr v-for="(tag, tagIndex) in label.tags" :key="'tag-' + tagIndex">
                <td><cdx-text-input style="min-width: 50px;" v-model="tag.tag" aria-label="Value"></cdx-text-input></td>
                <td><cdx-text-input v-model="tag.description" aria-label="Data"></cdx-text-input></td>
                <td style="text-align: right;">
                    <cdx-button action="destructive" @click="deleteTag(labelIndex, tagIndex)">Delete</cdx-button>
                    <cdx-button @click="addNewSubitem(labelIndex, tagIndex)">Add Sub Item</cdx-button>
                </td>
            </tr>
            <tr v-for="(tag, tagIndex) in label.tags" :key="'nestedTable-' + tagIndex">
                <td colspan="3" v-if="tag.items && tag.items.length > 0">
                    <cdx-field>
                        <table width="100%" id="adiutor-options-props">
                            <caption>
                                Subitems for {{ tag.description }}
                                <cdx-button class="add-new-button" weight="quiet"
                                    @click="addNewSubitem(labelIndex, tagIndex)">Add New</cdx-button>
                            </caption>
                            <tr>
                                <th>Name</th>
                                <th>Required</th>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Label</th>
                                <th>Help</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                            <tr v-for="(subitem, subitemIndex) in tag.items" :key="'subitem-' + subitemIndex">
                                <td><cdx-text-input style="min-width: 80px;" v-model="subitem.name"
                                        aria-label="Subitem Value"></cdx-text-input></td>
                                <td><cdx-toggle-switch v-model="subitem.required"></cdx-toggle-switch></td>
                                <td><cdx-text-input style="min-width: 80px;" v-model="subitem.parameter"
                                        aria-label="Subitem parameter"></cdx-text-input></td>
                                <td><cdx-text-input style="min-width: 50px;" v-model="subitem.type"
                                        aria-label="Subitem type"></cdx-text-input></td>
                                <td><cdx-text-input style="min-width: 50px;" v-model="subitem.value"
                                        aria-label="Subitem value"></cdx-text-input></td>
                                <td><cdx-text-input v-model="subitem.label" aria-label="Subitem label"></cdx-text-input>
                                </td>
                                <td><cdx-text-input style="min-width: 150px;" v-model="subitem.help" aria-label="Subitem help"></cdx-text-input></td>
                                <td style="text-align: right;"><cdx-button action="destructive"
                                        @click="deleteSubitem(labelIndex, tagIndex, subitemIndex)">Delete</cdx-button></td>
                            </tr>
                        </table>
                    </cdx-field>
                </td>
            </tr>
        </table>
    </cdx-field>
</template>
<script>
const { defineComponent, watch, ref, computed } = require('vue');
const { CdxCard, CdxCombobox, CdxTabs, CdxTab, CdxMessage, CdxTextInput, CdxCheckbox, CdxField, CdxRadio, CdxToggleSwitch, CdxTextArea, CdxButton } = require('@wikimedia/codex');
const tagConfiguration = mw.config.get('AdiutorArticleTagging');
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
        const tagList = ref(tagConfiguration.tagList);
        const useMultipleIssuesTemplate = ref(tagConfiguration.useMultipleIssuesTemplate);
        const multipleIssuesTemplate = ref(tagConfiguration.multipleIssuesTemplate);
        const uncategorizedTemplate = ref(tagConfiguration.uncategorizedTemplate);
        const apiPostSummary = ref(tagConfiguration.apiPostSummary);
        return {
            tagList,
            useMultipleIssuesTemplate,
            multipleIssuesTemplate,
            uncategorizedTemplate,
            apiPostSummary,
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

        addNewTagLabel() {
            const newLabel = {
                label: '',
                tags: []
            };
            this.tagList.push(newLabel);
        },

        deleteLabel(label) {
            const index = this.tagList.indexOf(label);
            if (index !== -1) {
                this.tagList.splice(index, 1);
            }
        },

        addNewTag(labelIndex) {
            const label = this.tagList[labelIndex];
            if (label) {
                const newTag = {
                    tag: '',
                    description: '',
                    items: []
                };
                label.tags.push(newTag);
            }
        },

        deleteTag(labelIndex, tagIndex) {
            const label = this.tagList[labelIndex];
            if (label && label.tags[tagIndex]) {
                label.tags.splice(tagIndex, 1);
            }
        },

        // Function to add a new subitem for a specific tag
        addNewSubitem(labelIndex, tagIndex) {
            const label = this.tagList[labelIndex];
            if (label && label.tags[tagIndex]) {
                const newSubitem = {
                    name: '',
                    required: false,
                    parameter: '',
                    type: '',
                    value: '',
                    label: '',
                    help: ''
                };
                label.tags[tagIndex].items.push(newSubitem);
            }
        },

        // Function to delete a subitem for a specific tag
        deleteSubitem(labelIndex, tagIndex, subitemIndex) {
            const label = this.tagList[labelIndex];
            if (label && label.tags[tagIndex] && label.tags[tagIndex].items[subitemIndex]) {
                label.tags[tagIndex].items.splice(subitemIndex, 1);
            }
        },

        generateTemplateOutput() {
            let output = this.useMultipleIssuesTemplate ? `{{${this.multipleIssuesTemplate}|\n{{Cleanup|reason=Test}}\n{{Another tag}}\n}}` : '';
            return output;
        },

        async saveConfiguration() {
            // Change the button label and disable it during the save operation
            this.saveButtonLabel = 'Saving...';
            this.saveButtonAction = 'default';
            this.saveButtonDisabled = true;

            // Prepare the data to be sent in the HTTP request
            const data = {
                title: 'MediaWiki:AdiutorArticleTagging.json',
                content: {
                    "tagList": this.tagList,
                    "useMultipleIssuesTemplate": this.useMultipleIssuesTemplate,
                    "multipleIssuesTemplate": this.multipleIssuesTemplate,
                    "uncategorizedTemplate": this.uncategorizedTemplate,
                    "apiPostSummary": this.apiPostSummary,
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