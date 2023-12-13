<template>
    <cdx-tabs v-model:active="currentTab" :framed="framed">
        <cdx-tab v-for="(tab, index) in tabsData" :key="index" :name="tab.name" :label="tab.label">
            <component :is="tab.component"></component>
        </cdx-tab>
    </cdx-tabs>
</template>
<script>
const { defineComponent } = require('vue');
const { CdxTabs, CdxTab } = require('@wikimedia/codex');
const createSpeedyDeletionOptions = require('./createSpeedyDeletionOptions.vue');
const requestPageProtectionOptions = require('./requestPageProtectionOptions.vue');
const requestPageMoveOptions = require('./requestPageMoveOptions.vue');
const deletionProposeOptions = require('./deletionProposeOptions.vue');
const articleTaggingOptions = require('./articleTaggingOptions.vue');
module.exports = defineComponent({
    name: 'adiutorSettings',
    components: {
        CdxTabs,
        CdxTab,
        createSpeedyDeletionOptions,
        requestPageProtectionOptions
    },
    props: {
        framed: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            tabsData: [{
                name: 'csd',
                label: 'Create Speedy Deletion',
                component: createSpeedyDeletionOptions
            }, {
                name: 'rpp',
                label: 'Request Page Protection',
                component: requestPageProtectionOptions
            }, {
                name: 'rpm',
                label: 'Request Page Move',
                component: requestPageMoveOptions
            }, {
                name: 'dpr',
                label: 'Deletion Propose',
                component: deletionProposeOptions
            }, {
                name: 'tag',
                label: 'Article Tagging',
                component: articleTaggingOptions
            }],
            currentTab: 'csd'
        };
    }
});
</script>