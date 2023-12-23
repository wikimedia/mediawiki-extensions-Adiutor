<template>
    <cdx-dialog class="tag-dialog" v-model:open="openTagDialog" :title="$i18n('adiutor-tag-header-title')" close-button-label="Close"
        :show-dividers="true" :primary-action="primaryAction" @primary="tagArticle" @default="openTagDialog = true">
        <div class="header">
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
        const api = new mw.Api();
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
            label: mw.msg('adiutor-tag-page'),
            actionType: 'progressive'
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
                checkboxValue.value = checkboxValue.value.filter(selectedTag => selectedTag !== tag);
            } else {
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
                        if (tag.tag) {
                            let template = `{{${tag.tag}`;
                            if (subItem.parameter) {
                                if (!templateInfo[tag.tag]) {
                                    templateInfo[tag.tag] = {};
                                }
                                if (!templateInfo[tag.tag][subItem.parameter]) {
                                    const inputValue = getInputValue(subItem.name);
                                    templateInfo[tag.tag][subItem.parameter] = inputValue;
                                }
                                template += `|${subItem.parameter}=${templateInfo[tag.tag][subItem.parameter]}`;
                            }
                            template += '}}';
                            preparedTemplates.push(template);
                        }
                    });
                } else {
                    if (tag.tag) {
                        preparedTemplates.push(`{{${tag.tag}}}`);
                    }
                }
            });
            console.log(preparedTemplates);

            if (useMultipleIssuesTemplate && preparedTemplates.length > 1) {
                preparedTagsString = `{{${multipleIssuesTemplate}|\n${preparedTemplates.join('\n')}\n}}`;
            } else {
                preparedTagsString = preparedTemplates.join('\n');
            }

            console.log(preparedTagsString);

            if (selectedTags.length > 0) {
                tagPage(preparedTagsString);
                openTagDialog.value = false;

            } else {
                mw.notify(mw.msg('adiutor-select-a-tag'), {
                    title: mw.msg('adiutor-operation-failed'),
                    type: 'error'
                });
            }
        }

        function getInputValue(inputName) {
            var inputElement = document.querySelector('input[name="' + inputName + '"]');
            if (inputElement) {
                return inputElement.value;
            } else {
                return "";
            }
        }

        function tagPage(preparedTagsString) {
            var editParams = {
                action: 'edit',
                title: mw.config.get("wgPageName"),
                summary: apiPostSummary,
                tags: 'Adiutor',
                format: 'json'
            };
            var removedContent = "";
            var modifiedTags = preparedTagsString.replace('{{' + uncategorizedTemplate + '}}', function (match) {
                removedContent = match;
                return "";
            });
            if (removedContent) {
                editParams.prependtext = modifiedTags.split(',').join('\n') + '\n';
                editParams.appendtext = '\n' + removedContent;
            } else {
                editParams.prependtext = modifiedTags.split(',').join('\n') + '\n';
            }
            api.postWithToken('csrf', editParams).done(function () {
                location.reload();
            });
        }

        return {
            checkboxValue,
            tagList,
            openTagDialog,
            primaryAction,
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