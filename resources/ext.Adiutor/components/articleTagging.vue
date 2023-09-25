<template>
    <cdx-dialog class="tag-dialog" v-model:open="openTagDialog" title="Article Tagging" close-button-label="Close"
        :show-dividers="true" :primary-action="primaryAction" :default-action="defaultAction" @primary="tagArticle"
        @default="openTagDialog = true">
        <div class="header">
            <h5>{{ $i18n('adiutor-choose-appropriate-tags') }}</h5>
            <p>{{ $i18n('adiutor-tag-header-description') }}</p>
            <cdx-text-input style="width: 200px;" v-model="searchTag" aria-label="New page name"
                placeholder="Search tag..."></cdx-text-input>
        </div>
        <div class="tag-reasons-body">
            <cdx-field :is-fieldset="true" v-for="label in filteredTagList" :key="'fieldset-' + label.label">
                <template v-if="label.tags.length > 0">
                    <cdx-label class="adt-label"><strong>{{ label.label }}</strong></cdx-label>
                </template>
                <cdx-field :is-fieldset="true" v-for="tag in label.tags" :key="'fieldset-' + tag.tag">
                    <cdx-checkbox :key="'checkbox-' + tag.tag" v-model="checkboxValue" :input-value="tag.tag"
                        @change="toggleTag(tag)">
                        {{ tag.description }}
                    </cdx-checkbox>
                    <template v-if="tag.items && tag.items.length > 0 && checkboxValue.includes(tag.tag)">
                        <template v-for="subItem in tag.items">
                            <template v-if="subItem.type === 'input'">
                                <cdx-text-input :key="'text-input-' + subItem.name" :placeholder="subItem.label"
                                    v-model="subItem.value" :name="subItem.name"></cdx-text-input>
                            </template>
                            <template v-else-if="subItem.type === 'checkbox'">
                                <cdx-checkbox :key="'checkbox-' + subItem.name" v-model="subItem.value">
                                    {{ subItem.label }}
                                </cdx-checkbox>
                            </template>
                        </template>
                    </template>
                </cdx-field>
            </cdx-field>
        </div>
    </cdx-dialog>
</template>
<script>
const { defineComponent, ref, computed } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxMessage } = require('@wikimedia/codex');
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
        const checkboxValue = ref(['']);
        const openTagDialog = ref(true);
        const tagConfiguration = mw.config.get('AdiutorArticleTagging');
        const tagList = tagConfiguration.tagList;
        const useMultipleIssuesTemplate = tagConfiguration.useMultipleIssuesTemplate;
        const multipleIssuesTemplate = tagConfiguration.multipleIssuesTemplate;
        const uncategorizedTemplate = tagConfiguration.uncategorizedTemplate;
        const apiPostSummary = tagConfiguration.apiPostSummary;
        const searchTag = ref('');
        const primaryAction = {
            icon: 'cdxIconTag',
            label: mw.msg('adiutor-request'),
            actionType: 'progressive'
        };

        const defaultAction = {
            label: mw.msg('adiutor-speedy-deletion-policy'),
        };
        const filteredTagList = computed(() => {
            const searchTerm = searchTag.value.toLowerCase();
            return tagList.map(label => {
                const filteredTags = label.tags.filter(tag =>
                    tag.tag.toLowerCase().includes(searchTerm) ||
                    tag.description.toLowerCase().includes(searchTerm)
                );
                return { ...label, tags: filteredTags };
            });
        });

        function toggleTag(tag) {
            if (checkboxValue.value.includes(tag)) {
                // If the tag is already in selectedTags and the checkbox is unchecked, remove it
                checkboxValue.value = checkboxValue.value.filter(selectedTag => selectedTag !== tag);
            } else {
                // If the tag is not in selectedTags and the checkbox is checked, add it
                checkboxValue.value.push(tag);
            }
            console.log(checkboxValue.value);
        }

        function tagArticle() {
            const selectedTags = checkboxValue.value;
            const preparedTemplates = [];
            const templateInfo = {};
            let preparedTagsString = '';

            selectedTags.forEach(function (tag) {
                if (tag.items && tag.items.length > 0) {
                    tag.items.forEach(function (subItem) {
                        if (tag.tag) { // Check if tag.tag is defined
                            let template = `{{${tag.tag}`;
                            if (subItem.parameter) {
                                // If information for this template has not been provided before, get it from the user
                                if (!templateInfo[tag.tag]) {
                                    templateInfo[tag.tag] = {};
                                }
                                if (!templateInfo[tag.tag][subItem.parameter]) {
                                    const inputValue = getInputValue(subItem.name); // Get information from the user
                                    templateInfo[tag.tag][subItem.parameter] = inputValue;
                                }
                                // Create the template using the previously entered information
                                template += `|${subItem.parameter}=${templateInfo[tag.tag][subItem.parameter]}`;
                            }
                            template += '}}';
                            preparedTemplates.push(template);
                        }
                    });
                } else {
                    if (tag.tag) { // Check if tag.tag is defined
                        // If there are no tag.items, just add {{Template}}
                        preparedTemplates.push(`{{${tag.tag}}}`);
                    }
                }
            });
            console.log(preparedTemplates);

            if (useMultipleIssuesTemplate && preparedTemplates.length > 1) {
                // Join the formatted tags with newline characters and wrap in {{Multiple issues|...}}
                preparedTagsString = `{{${multipleIssuesTemplate}|\n${preparedTemplates.join('\n')}\n}}`;
            } else {
                // If there's only one tag or useMultipleIssuesTemplate is false, just use it as-is
                preparedTagsString = preparedTemplates.join('\n');
            }

            console.log(preparedTagsString);

            if (selectedTags.length > 0) {
                //tagPage(); // You may want to define the tagPage function if it's not already defined
                //openTagDialog.value = false;

            } else {
                mw.notify(mw.msg('adiutor-select-a-tag'), {
                    title: mw.msg('adiutor-operation-failed'),
                    type: 'error'
                });
            }
        }


        function getInputValue(inputName) {
            // Find the input element that matches inputName
            var inputElement = document.querySelector('input[name="' + inputName + '"]');
            if (inputElement) {
                // If the input element is found, get its value
                return inputElement.value;
            } else {
                // If the input element is not found, return a default value or handle the error
                return ""; // You can return a default value or perform other actions
            }
        }


        return {
            checkboxValue,
            tagList,
            openTagDialog,
            primaryAction,
            defaultAction,
            searchTag,
            toggleTag,
            filteredTagList,
            tagArticle,
        };
    }
});
</script>
<style lang="css">
.tag-dialog {
    max-width: 40em;
}

.tag-dialog .tag-reasons-body {
    padding: 20px;
}

.tag-dialog .cdx-dialog {
    max-width: 720px;
    padding-top: 10px;
    padding-bottom: 0;
}

.tag-dialog .cdx-dialog__body {
    flex-grow: 1;
    margin-top: 0;
    padding: 0;
    overflow-y: auto;
}


.tag-dialog .cdx-dialog--dividers .cdx-dialog__body {
    padding-top: 0;
}

.tag-dialog .tag-reason-field {
    display: flex;
    flex-direction: column;
    width: 50%;
}

.tag-dialog .cdx-dialog__header {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    box-sizing: border-box;
    width: 100%;
    padding: 10px 20px 10px;
    font-weight: 700;
}

.tag-dialog cdx-label {
    margin-bottom: 10px;
    display: block;
}

.tag-dialog .header {
    background-color: #eaf3ff;
    display: block;
    align-items: baseline;
    justify-content: space-between;
    height: 10em;
    padding: 20px;
    background-image: url(../../ext.Adiutor.images/tag-background.png);
    background-position: right 25px;
    background-repeat: no-repeat;
    background-size: 205px;
}

.tag-dialog .cdx-dialog__footer {
    padding: 20px !important;
    border-top: 1px solid #a2a9b1;
}

.tag-dialog .header p {
    width: 70%;
}

.tag-dialog h2 {
    margin: 0;
    padding: 0;
    font-size: 1.125em !important
}
</style>