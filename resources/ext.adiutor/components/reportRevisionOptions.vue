<!-- eslint-disable vue/require-v-for-key -->
<!-- eslint-disable vue/no-undef-components -->
<!-- eslint-disable vue/no-static-inline-styles -->
<template>
  <cdx-field class="top-message">
    <cdx-button
        :action="saveButtonAction"
        :class="saveButtonClass"
        :disabled="saveButtonDisabled"
        :weight="saveButtonWeight"
        @click="saveConfiguration">
      {{ saveButtonLabel }}
    </cdx-button>
    <h3 style="display: initial;">
      {{ $i18n( "adiutor-report-revision-configuration" ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( "adiutor-report-revision-configuration-description" ) }}
    </p>
  </cdx-field>
  <cdx-message :dismiss-button-label="$i18n( 'adiutor-close' )" type="warning">
    <h3>{{ $i18n( "adiutor-parameters" ) }}</h3>
    <p>{{ $i18n( "adiutor-parameters-description" ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( "adiutor-parameters-page-name" ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( "adiutor-parameters-revision-number" ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( "adiutor-parameters-rationale" ) }}</li>
      <li><strong>$4</strong>: {{ $i18n( "adiutor-parameters-comment" ) }}</li>
    </ul>
  </cdx-message>
  <cdx-field>
    <cdx-toggle-switch v-model="moduleEnabled" :align-switch="true">
      {{ $i18n( "adiutor-module-enabled" ) }}
      <template #description>
        {{ $i18n( "adiutor-module-enabled-description" ) }}
      </template>
    </cdx-toggle-switch>
    <cdx-toggle-switch v-model="testMode" :align-switch="true">
      {{ $i18n( "adiutor-test-mode" ) }}
      <template #description>
        {{ $i18n( "adiutor-test-mode-description" ) }}
      </template>
    </cdx-toggle-switch>
    <cdx-field>
      <cdx-chip-input
          v-model:input-chips="namespaces"
          :remove-button-label="$i18n( 'adiutor-remove' )"></cdx-chip-input>
      <template #label>
        {{ $i18n( "adiutor-namespaces" ) }}
      </template>
      <template #description>
        {{ $i18n( "adiutor-namespaces-description" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-revision-deletion-policy-title" ) }}</strong>
      </template>
      <cdx-text-input
          id="policyLink"
          v-model="policyTitle"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-revision-deletion-policy-title-help" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-noticeboard" ) }}</strong>
      </template>
      <cdx-text-input
          id="noticeBoardTitle"
          v-model="noticeBoardTitle"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-content-pattern" ) }}</strong>
      </template>
      <cdx-text-area
          id="contentPattern"
          v-model="contentPattern"
          aria-label="{{ $i18n('adiutor-content-pattern') }}"></cdx-text-area>
      <template #help-text>
        {{ $i18n( "adiutor-content-pattern-description" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <cdx-toggle-switch v-model="addNewSection" :align-switch="true">
        {{ $i18n( "adiutor-create-new-section" ) }}
        <template #description>
          {{ $i18n( "adiutor-new-section-description" ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field v-if="addNewSection">
      <template #label>
        <strong>{{ $i18n( "adiutor-new-section-title" ) }}</strong>
      </template>
      <cdx-text-input
          id="sectionTitle"
          v-model="sectionTitle"></cdx-text-input>
    </cdx-field>
    <cdx-field v-if="!addNewSection">
      <cdx-toggle-switch v-model="useExistSection" :align-switch="true">
        {{ $i18n( "adiutor-use-exist-section" ) }}
        <template #description>
          {{ $i18n( "adiutor-use-exist-section-description" ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field v-if="useExistSection">
      <template #label>
        <strong>{{ $i18n( "adiutor-section-id" ) }}</strong>
      </template>
      <cdx-text-input
          id="sectionId"
          v-model="sectionId"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-section-id-description" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template v-if="!addNewSection && !useExistSection" #description>
        {{ $i18n( "adiutor-no-section-description" ) }}
      </template>
      <template #label>
        {{ $i18n( "adiutor-text-modification-direction" ) }}
      </template>
      <cdx-radio
          v-for="direction in textModificationDirectionRadios"
          :key="'rev-radio-' + direction.value"
          v-model="textModificationDirection"
          :input-value="direction.value"
          name="rev-radio-group-{{direction.value}}-descriptions">
        {{ direction.label }}
        <template #description>
          {{ direction.description }}
        </template>
      </cdx-radio>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-api-post-summary" ) }}</strong>
      </template>
      <cdx-text-input
          id="apiPostSummary"
          v-model="apiPostSummary"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <table id="adiutor-options-props">
      <caption>
        {{ $i18n( "adiutor-rationales" ) }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addRationale">
          {{ $i18n( "adiutor-add-new" ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( "adiutor-value" ) }}</th>
        <th>{{ $i18n( "adiutor-label" ) }}</th>
        <th>{{ $i18n( "adiutor-action" ) }}</th>
      </tr>
      <tr v-for="rationale in reportRationales">
        <td>
          <cdx-text-input
              v-model="rationale.value"
              aria-label="{{ $i18n( 'adiutor-rationale-value' ) }}"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input
              v-model="rationale.label"
              aria-label="{{ $i18n( 'adiutor-rationale-label' ) }}"></cdx-text-input>
        </td>
        <td>
          <cdx-button action="destructive" @click="deleteRationale( rationale )">
            {{ $i18n( "adiutor-delete" ) }}
          </cdx-button>
        </td>
      </tr>
    </table>
  </cdx-field>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const {
  CdxMessage,
  CdxTextInput,
  CdxChipInput,
  CdxToggleSwitch,
  CdxButton,
  CdxField,
  CdxRadio,
  CdxTextArea
} = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
module.exports = defineComponent( {
  name: 'ReportRevisionOptions',
  components: {
    CdxTextInput,
    CdxChipInput,
    CdxMessage,
    CdxToggleSwitch,
    CdxField, CdxButton,
    CdxRadio, CdxTextArea
  },
  setup() {
    const revConfiguration = mw.config.get( 'wgAdiutorReportRevision' );
    const reportRationales = ref( revConfiguration.reportRationales );
    const noticeBoardTitle = ref( revConfiguration.noticeBoardTitle );
    const addNewSection = ref( revConfiguration.addNewSection );
    const useExistSection = ref( revConfiguration.useExistSection );
    const sectionTitle = ref( revConfiguration.sectionTitle );
    const sectionId = ref( revConfiguration.sectionId );
    const contentPattern = ref( revConfiguration.contentPattern );
    const apiPostSummary = ref( revConfiguration.apiPostSummary );
    const textModificationDirection = ref( revConfiguration.textModificationDirection );
    const moduleEnabled = ref( revConfiguration.moduleEnabled );
    const testMode = ref( revConfiguration.testMode );
    const namespaces = ref( revConfiguration.namespaces );
    const policyTitle = ref( revConfiguration.policyTitle );
    const textModificationDirectionRadios = [
      {
        label: mw.message( 'adiutor-prepend-text-on-the-page' ).text(),
        description: mw.message( 'adiutor-prepend-text-on-the-page-description' ).text(),
        value: 'prependtext'
      }, {
        label: mw.message( 'adiutor-append-text-on-the-page' ).text(),
        description: mw.message( 'adiutor-append-text-on-the-page-description' ).text(),
        value: 'appendtext'
      }
    ];

    return {
      reportRationales,
      noticeBoardTitle,
      addNewSection,
      useExistSection,
      sectionTitle,
      sectionId,
      contentPattern,
      textModificationDirection,
      apiPostSummary,
      policyTitle,
      textModificationDirectionRadios,
      moduleEnabled,
      testMode,
      namespaces
    };
  },
  data() {
    return {
      saveButtonLabel: mw.message( 'adiutor-save-configurations' ).text(),
      saveButtonAction: 'progressive',
      saveButtonClass: 'save-button',
      saveButtonWeight: 'primary',
      saveButtonDisabled: false
    };
  },
  methods: {
    addRationale() {
      const newDuration = {
        value: '',
        data: '',
        label: ''
      };
      this.reportRationales.push( newDuration );
    },

    deleteRationale( rationale ) {
      const index = this.reportRationales.indexOf( rationale );
      if ( index !== -1 ) {
        this.reportRationales.splice( index, 1 );
      }
    },

    async saveConfiguration() {
      const configuration = {
        reportRationales: this.reportRationales,
        noticeBoardTitle: this.noticeBoardTitle,
        addNewSection: this.addNewSection,
        sectionTitle: this.sectionTitle,
        useExistSection: this.useExistSection,
        sectionId: this.sectionId,
        textModificationDirection: this.textModificationDirection,
        contentPattern: this.contentPattern,
        apiPostSummary: this.apiPostSummary,
        moduleEnabled: this.moduleEnabled,
        testMode: this.testMode,
        namespaces: this.namespaces,
        policyTitle: this.policyTitle
      };
      const title = 'MediaWiki:AdiutorReportRevision.json';

      // Set the button to the saving state
      this.saveButtonLabel = mw.message( 'adiutor-configurations-saving' ).text();
      this.saveButtonAction = 'default';
      this.saveButtonDisabled = true;

      try {
        // Call the centralized save function from AdiutorUtility
        await AdiutorUtility.saveConfiguration( title, configuration );

        // Update the button to show the save completed
        this.saveButtonLabel = mw.message( 'adiutor-save-configurations' ).text();
        this.saveButtonAction = 'progressive';
        this.saveButtonDisabled = false;
      } catch ( error ) {
        // If an error occurred, display it and update the button accordingly
        mw.notify( error, { type: 'error' } );
        this.saveButtonLabel = mw.message( 'adiutor-try-again' ).text();
        this.saveButtonAction = 'destructive';
        this.saveButtonDisabled = false;
      }
    }

  }
} );
</script>

<style lang="less">
#adiutor-options-props {
  border-collapse: collapse;
  width: 100%;

  caption {
    padding-top: 12px;
    padding-bottom: 12px;
    background-color: #eaecf0;
    color: #000f;
    text-align: center;
    border: 1px solid #ddd;
    font-weight: 900;
  }

  td, th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  tr:hover {
    background-color: #ddd;
  }

  th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #fff;
    color: #000f;
  }

  .add-new-button {
    float: right;
    margin-right: 10px;
  }
}

.ext-adiutor-options .cdx-field {
  margin-top: 10px;
  display: block;
}

.ext-adiutor-options .top-message {
  margin-top: 10px;
}

.ext-adiutor-options .save-button {
  float: right;
}</style>
