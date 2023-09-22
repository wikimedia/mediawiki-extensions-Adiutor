<template>
    <cdx-message class="top-message">
        <strong>Request page protection configuration</strong>
        <cdx-button :action="saveButtonAction" :class="saveButtonClass" :weight="saveButtonWeight"
            @click="saveConfiguration" :disabled="saveButtonDisabled">
            {{ saveButtonLabel }}
        </cdx-button>
    </cdx-message>
    <cdx-message type="warning" dismiss-button-label="Close">
        <h3>Parameters</h3>
        <p>You can utilize the parameters within this options, which include:</p>
        <ul>
            <li><strong>$1</strong>: page name</li>
            <li><strong>$2</strong>: protection duration</li>
            <li><strong>$3</strong>: protection type</li>
            <li><strong>$4</strong>: protection rationale</li>
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
    <cdx-field>
        <table width="100%" id="adiutor-options-props">
            <caption>
                Protection durations <cdx-button class="add-new-button" weight="quiet" @click="addNewProtectionDuration">Add
                    New</cdx-button>
            </caption>
            <tr>
                <th>Value</th>
                <th>Data</th>
                <th>Label</th>
                <th>Action</th>
            </tr>
            <tr v-for="duration in protectionDurations">
                <td><cdx-text-input v-model="duration.value" aria-label="Duration value"></cdx-text-input></td>
                <td><cdx-text-input v-model="duration.data" aria-label="Duration data"></cdx-text-input></td>
                <td><cdx-text-input v-model="duration.label" aria-label="Duration label"></cdx-text-input></td>
                <td><cdx-button action="destructive" @click="deleteProtectionDuration(duration)">Delete</cdx-button></td>
            </tr>
        </table>
    </cdx-field>
    <cdx-field>
        <table width="100%" id="adiutor-options-props">
            <caption>
                Protection types <cdx-button class="add-new-button" weight="quiet" @click="addNewProtectionType">Add
                    New</cdx-button>
            </caption>
            <tr>
                <th>Value</th>
                <th>Data</th>
                <th>Label</th>
                <th>Action</th>
            </tr>
            <tr v-for="protection_type in protectionTypes">
                <td><cdx-text-input v-model="protection_type.value" aria-label="Type value"></cdx-text-input></td>
                <td><cdx-text-input v-model="protection_type.data" aria-label="Type data"></cdx-text-input></td>
                <td><cdx-text-input v-model="protection_type.label" aria-label="Type label"></cdx-text-input></td>
                <td><cdx-button action="destructive" @click="deleteProtectionType(protection_type)">Delete</cdx-button></td>
            </tr>
        </table>
    </cdx-field>
</template>
<script>
const { defineComponent, watch, ref, computed } = require('vue');
const { CdxCard, CdxCombobox, CdxTabs, CdxTab, CdxMessage, CdxTextInput, CdxToggleSwitch, CdxButton, CdxCheckbox, CdxField, CdxRadio, CdxTextArea } = require('@wikimedia/codex');
const rppConfiguration = require('../localization/Rpp.json');
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
        const protectionDurations = ref(rppConfiguration.protectionDurations);
        const protectionTypes = ref(rppConfiguration.protectionTypes);
        const noticeBoardTitle = ref(rppConfiguration.noticeBoardTitle);
        const addNewSection = ref(rppConfiguration.addNewSection);
        const useExistSection = ref(rppConfiguration.useExistSection);
        const sectionTitle = ref(rppConfiguration.sectionTitle);
        const sectionId = ref(rppConfiguration.sectionId);
        const contentPattern = ref(rppConfiguration.contentPattern);
        const apiPostSummary = ref(rppConfiguration.apiPostSummary);
        const textModificationDirection = ref(rppConfiguration.textModificationDirection);
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
            protectionDurations,
            protectionTypes,
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
        addNewProtectionDuration() {
            const newDuration = {
                value: '',
                data: '',
                label: ''
            };
            this.protectionDurations.push(newDuration);
        },

        addNewProtectionType() {
            const newProtectionType = {
                value: '',
                data: '',
                label: ''
            };
            this.protectionTypes.push(newProtectionType);
        },

        deleteProtectionDuration(duration) {
            const index = this.protectionDurations.indexOf(duration);
            if (index !== -1) {
                this.protectionDurations.splice(index, 1);
            }
        },

        deleteProtectionType(type) {
            const index = this.protectionTypes.indexOf(type);
            if (index !== -1) {
                this.protectionTypes.splice(index, 1);
            }
        },

        async saveConfiguration() {
            this.saveButtonLabel = 'Saving...';
            this.saveButtonAction = 'default';
            this.saveButtonDisabled = true;

            var hookName = 'PageContentSaveComplete';

            $.ajax({
                type: 'POST',
                url: mw.util.wikiScript('api'),
                data: {
                    action: 'post', 
                    rs: hookName, 
                    rsargs: JSON.stringify(rppConfiguration)
                },
                success: function (data) {
                    console.log(data);
                },
                error: function (error) {
                    console.error(error);
                }
            });

            setTimeout(() => {
                this.saveButtonLabel = 'Save configurations';
                this.saveButtonAction = 'progressive';
                this.saveButtonDisabled = false;
            }, 2000);
        },

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

#adiutor-options-props {
    border-collapse: collapse;
    width: 100%;
}

#adiutor-options-props caption {
    padding-top: 12px;
    padding-bottom: 12px;
    background-color: #fef6e8;
    color: black;
    text-align: center;
    border: 1px solid #ddd;
    font-weight: 900;
}

#adiutor-options-props td,
#adiutor-options-props th {
    border: 1px solid #ddd;
    padding: 8px;
}

#adiutor-options-props tr:nth-child(even) {
    background-color: #f2f2f2;
}

#adiutor-options-props tr:hover {
    background-color: #ddd;
}

#adiutor-options-props th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #eaecf0;
    color: black;
}

#adiutor-options-props .add-new-button {
    float: right;
    margin-right: 10px;
}

.save-button {
    float: right;
}
</style>