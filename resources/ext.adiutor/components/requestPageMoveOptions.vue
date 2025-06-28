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
      {{ $i18n( "adiutor-request-page-move-configuration" ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( "adiutor-request-page-move-description" ) }}
    </p>
  </cdx-field>
  <cdx-message :dismiss-button-label="$i18n( 'adiutor-close' )" type="warning">
    <h3>{{ $i18n( "adiutor-parameters" ) }}</h3>
    <p>{{ $i18n( "adiutor-parameters-description" ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( "adiutor-parameters-page-name" ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( "adiutor-parameters-new-page-name" ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( "adiutor-parameters-rationale" ) }}</li>
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
          v-model="contentPattern"></cdx-text-area>
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
      <cdx-toggle-switch v-model="useExistSection">
        <template #label>
          {{ $i18n( "adiutor-use-exist-section" ) }}
        </template>
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
          :key="'rpm-radio-' + direction.value"
          v-model="textModificationDirection"
          :input-value="direction.value"
          name="rpm-radio-group-{{direction.value}}-descriptions">
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
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const {
  CdxMessage,
  CdxTextInput,
  CdxToggleSwitch,
  CdxChipInput,
  CdxButton,
  CdxField,
  CdxRadio,
  CdxTextArea
} = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
module.exports = defineComponent( {
  name: 'RequestPageMove',
  components: {
    CdxTextInput,
    CdxChipInput,
    CdxMessage,
    CdxToggleSwitch,
    CdxField, CdxButton,
    CdxRadio, CdxTextArea
  },
  setup() {
    const rpmConfiguration = mw.config.get( 'wgAdiutorRequestPageMove' );
    const noticeBoardTitle = ref( rpmConfiguration.noticeBoardTitle );
    const addNewSection = ref( rpmConfiguration.addNewSection );
    const useExistSection = ref( rpmConfiguration.useExistSection );
    const sectionTitle = ref( rpmConfiguration.sectionTitle );
    const sectionId = ref( rpmConfiguration.sectionId );
    const contentPattern = ref( rpmConfiguration.contentPattern );
    const apiPostSummary = ref( rpmConfiguration.apiPostSummary );
    const textModificationDirection = ref( rpmConfiguration.textModificationDirection );
    const moduleEnabled = ref( rpmConfiguration.moduleEnabled );
    const testMode = ref( rpmConfiguration.testMode );
    const namespaces = ref( rpmConfiguration.namespaces );
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
      noticeBoardTitle,
      addNewSection,
      useExistSection,
      sectionTitle,
      sectionId,
      contentPattern,
      textModificationDirection,
      apiPostSummary,
      textModificationDirectionRadios,
      moduleEnabled,
      testMode,
      namespaces
    };
  },
  data() {
    return {
      // Add properties for controlling button appearance
      saveButtonLabel: mw.message( 'adiutor-save-configurations' ).text(),
      saveButtonAction: 'progressive',
      saveButtonClass: 'save-button',
      saveButtonWeight: 'primary',
      saveButtonDisabled: false
    };
  },
  methods: {

    async saveConfiguration() {
      const configuration = {
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
        namespaces: this.namespaces
      };
      const title = 'MediaWiki:AdiutorRequestPageMove.json';

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
.ext-adiutor-options .cdx-field {
	margin-top: 10px;
	display: block;
}

.ext-adiutor-options .top-message {
	margin-top: 10px;
}

.ext-adiutor-options .save-button {
	float: right;
}
</style>
