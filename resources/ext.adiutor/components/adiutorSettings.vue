<template>
  <cdx-tabs v-model:active="currentTab" :framed="framed">
    <cdx-tab
        v-for="( tab, index ) in tabsData"
        :key="index"
        :label="tab.label"
        :name="tab.name">
      <component :is="tab.component"></component>
    </cdx-tab>
  </cdx-tabs>
</template>

<script>
const { defineComponent } = require( 'vue' );
const { CdxTabs, CdxTab } = require( '../../codex.js' );
const createSpeedyDeletionOptions = require( './createSpeedyDeletionOptions.vue' );
const requestPageProtectionOptions = require( './requestPageProtectionOptions.vue' );
const requestPageMoveOptions = require( './requestPageMoveOptions.vue' );
const deletionProposeOptions = require( './deletionProposeOptions.vue' );
const articleTaggingOptions = require( './articleTaggingOptions.vue' );
const reportRevisionOptions = require( './reportRevisionOptions.vue' );
module.exports = defineComponent( {
  name: 'AdiutorSettings',
  components: {
    CdxTabs,
    CdxTab,
    createSpeedyDeletionOptions,
    requestPageProtectionOptions,
    requestPageMoveOptions,
    deletionProposeOptions,
    articleTaggingOptions,
    reportRevisionOptions
  },
  props: {
    framed: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      tabsData: [ {
        name: 'csd',
        label: mw.msg( 'adiutor-create-speedy-deletion' ),
        component: createSpeedyDeletionOptions
      }, {
        name: 'rpp',
        label: mw.msg( 'adiutor-request-page-protection' ),
        component: requestPageProtectionOptions
      }, {
        name: 'rpm',
        label: mw.msg( 'adiutor-request-page-move' ),
        component: requestPageMoveOptions
      }, {
        name: 'dpr',
        label: mw.msg( 'adiutor-proposed-deletion' ),
        component: deletionProposeOptions
      }, {
        name: 'tag',
        label: mw.msg( 'adiutor-article-tagging' ),
        component: articleTaggingOptions
      }, {
        name: 'rev',
        label: mw.msg( 'adiutor-report-revision' ),
        component: reportRevisionOptions
      } ],
      currentTab: 'csd'
    };
  }
} );
</script>
