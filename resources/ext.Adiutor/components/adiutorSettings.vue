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
    <cdx-tabs v-model:active="currentTab" :framed="framed">
        <cdx-tab v-for="( tab, index ) in tabsData" :key="index" :name="tab.name" :label="tab.label">
            <template v-if="tab.name === 'csd'">
                <createSpeedyDeletionOptions></createSpeedyDeletionOptions>
            </template>
            <template v-if="tab.name === 'rpp'">
            </template>
        </cdx-tab>
    </cdx-tabs>
</template>
<script>
const { defineComponent } = require('vue');
const { CdxTabs, CdxTab } = require('@wikimedia/codex');
const createSpeedyDeletionOptions = require('./createSpeedyDeletionOptions.vue');
module.exports = defineComponent({
    name: 'adiutorSettings',
    components: {
        CdxTabs,
        CdxTab,
        createSpeedyDeletionOptions
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
            }, {
                name: 'rpp',
                label: 'Request Page Protection',
            }],

            currentTab: 'csd'
        };
    }
});
</script>
<style lang="less">
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