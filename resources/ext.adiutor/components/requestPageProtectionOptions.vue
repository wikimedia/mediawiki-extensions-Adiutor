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
      {{ $i18n( 'adiutor-request-page-protection-configuration' ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( 'adiutor-request-page-protection-description' ) }}
    </p>
  </cdx-field>
  <cdx-message dismiss-button-label="Close" type="warning">
    <h3>{{ $i18n( 'adiutor-parameters' ) }}</h3>
    <p>{{ $i18n( 'adiutor-parameters-description' ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( 'adiutor-parameters-page-name' ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( 'adiutor-parameters-protection-duration' ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( 'adiutor-parameters-protection-type' ) }}</li>
      <li><strong>$4</strong>: {{ $i18n( 'adiutor-parameters-protection-rationale' ) }}</li>
    </ul>
  </cdx-message>
  <cdx-field>
    <cdx-field>
      <cdx-toggle-switch v-model="moduleEnabled">
        <cdx-label input-id="moduleEnabled">
          {{ $i18n( 'adiutor-module-enabled' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-module-enabled-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field>
      <cdx-toggle-switch v-model="testMode">
        <cdx-label input-id="testMode">
          {{ $i18n( 'adiutor-test-mode' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-test-mode-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field>
      <cdx-chip-input v-model:input-chips="namespaces" remove-button-label="remove"></cdx-chip-input>
      <template #label>
        {{ $i18n( 'adiutor-namespaces' ) }}
      </template>
      <template #description>
        {{ $i18n( 'adiutor-namespaces-description' ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="noticeBoardTitle">
        {{ $i18n( 'adiutor-noticeboard' ) }}
      </cdx-label>
      <cdx-text-input
          id="noticeBoardTitle"
          v-model="noticeBoardTitle"
          aria-label="{{ $i18n('adiutor-noticeboard') }}"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="contentPattern">
        {{ $i18n( 'adiutor-content-pattern' ) }}
      </cdx-label>
      <cdx-text-area
          id="contentPattern"
          v-model="contentPattern"
          aria-label="{{ $i18n('adiutor-content-pattern') }}"></cdx-text-area>
      <template #help-text>
        {{ $i18n( 'adiutor-content-pattern-description' ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <cdx-toggle-switch v-model="addNewSection">
        <cdx-label input-id="addNewSection">
          {{ $i18n( 'adiutor-create-new-section' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-new-section-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field v-if="addNewSection">
      <cdx-label input-id="sectionTitle">
        {{ $i18n( 'adiutor-new-section-title' ) }}
      </cdx-label>
      <cdx-text-input
          id="sectionTitle"
          v-model="sectionTitle"
          aria-label="Speedy Deletion Policy"></cdx-text-input>
    </cdx-field>
    <cdx-field v-if="!addNewSection">
      <cdx-toggle-switch v-model="useExistSection">
        <cdx-label input-id="useExistSection">
          {{ $i18n( 'adiutor-use-exist-section' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-use-exist-section-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field v-if="useExistSection">
      <cdx-label input-id="sectionId">
        {{ $i18n( 'adiutor-section-id' ) }}
      </cdx-label>
      <cdx-text-input
          id="sectionId"
          v-model="sectionId"
          aria-label="{{ $i18n('adiutor-section-id') }}"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-section-id-description' ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template v-if="!addNewSection && !useExistSection" #description>
        {{ $i18n( 'adiutor-no-section-description' ) }}
      </template>
      <template #label>
        {{ $i18n( 'adiutor-text-modification-direction' ) }}
      </template>
      <cdx-radio
          v-for="direction in textModificationDirectionRadios"
          :key="'rpp-radio-' + direction.value"
          v-model="textModificationDirection"
          :input-value="direction.value"
          name="rpp-radio-group-{{direction.value}}-descriptions">
        {{ direction.label }}
        <template #description>
          {{ direction.description }}
        </template>
      </cdx-radio>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <strong>{{ $i18n( 'adiutor-summaries' ) }}</strong>
      <cdx-label input-id="apiPostSummary">
        {{ $i18n( 'adiutor-api-post-summary' ) }}
      </cdx-label>
      <cdx-text-input
          id="apiPostSummary"
          v-model="apiPostSummary"
          aria-label="{{ $i18n('adiutor-api-post-summary') }}"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <table id="adiutor-options-props" width="100%">
      <caption>
        {{ $i18n( 'adiutor-protection-durations' ) }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewProtectionDuration">
          {{ $i18n( 'adiutor-add-new' ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( 'adiutor-value' ) }}</th>
        <th>{{ $i18n( 'adiutor-data' ) }}</th>
        <th>{{ $i18n( 'adiutor-label' ) }}</th>
        <th>{{ $i18n( 'adiutor-action' ) }}</th>
      </tr>
      <tr v-for="duration in protectionDurations">
        <td>
          <cdx-text-input v-model="duration.value" aria-label="Duration value"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="duration.data" aria-label="Duration data"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="duration.label" aria-label="Duration label"></cdx-text-input>
        </td>
        <td>
          <cdx-button action="destructive" @click="deleteProtectionDuration( duration )">
            {{ $i18n( 'adiutor-delete' ) }}
          </cdx-button>
        </td>
      </tr>
    </table>
  </cdx-field>
  <cdx-field>
    <table id="adiutor-options-props" width="100%">
      <caption>
        {{ $i18n( 'adiutor-protection-types' ) }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewProtectionType">
          {{ $i18n( 'adiutor-add-new' ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( 'adiutor-value' ) }}</th>
        <th>{{ $i18n( 'adiutor-data' ) }}</th>
        <th>{{ $i18n( 'adiutor-label' ) }}</th>
        <th>{{ $i18n( 'adiutor-action' ) }}</th>
      </tr>
      <tr v-for="protection_type in protectionTypes">
        <td>
          <cdx-text-input v-model="protection_type.value" aria-label="Type value"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="protection_type.data" aria-label="Type data"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="protection_type.label" aria-label="Type label"></cdx-text-input>
        </td>
        <td>
          <cdx-button action="destructive" @click="deleteProtectionType( protection_type )">
            {{ $i18n( 'adiutor-delete' ) }}
          </cdx-button>
        </td>
      </tr>
    </table>
  </cdx-field>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const {
  CdxLabel,
  CdxMessage,
  CdxTextInput,
  CdxChipInput,
  CdxToggleSwitch,
  CdxButton,
  CdxField,
  CdxRadio,
  CdxTextArea
} = require( '@wikimedia/codex' );
module.exports = defineComponent( {
  name: 'RequestPageProtectionOptions',
  components: {
    CdxLabel,
    CdxTextInput,
    CdxChipInput,
    CdxMessage,
    CdxToggleSwitch,
    CdxField, CdxButton,
    CdxRadio, CdxTextArea
  },
  setup() {
    const rppConfiguration = mw.config.get( 'wgAdiutorRequestPageProtection' );
    const protectionDurations = ref( rppConfiguration.protectionDurations );
    const protectionTypes = ref( rppConfiguration.protectionTypes );
    const noticeBoardTitle = ref( rppConfiguration.noticeBoardTitle );
    const addNewSection = ref( rppConfiguration.addNewSection );
    const useExistSection = ref( rppConfiguration.useExistSection );
    const sectionTitle = ref( rppConfiguration.sectionTitle );
    const sectionId = ref( rppConfiguration.sectionId );
    const contentPattern = ref( rppConfiguration.contentPattern );
    const apiPostSummary = ref( rppConfiguration.apiPostSummary );
    const textModificationDirection = ref( rppConfiguration.textModificationDirection );
    const moduleEnabled = ref( rppConfiguration.moduleEnabled );
    const testMode = ref( rppConfiguration.testMode );
    const namespaces = ref( rppConfiguration.namespaces );
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
    addNewProtectionDuration() {
      const newDuration = {
        value: '',
        data: '',
        label: ''
      };
      this.protectionDurations.push( newDuration );
    },

    addNewProtectionType() {
      const newProtectionType = {
        value: '',
        data: '',
        label: ''
      };
      this.protectionTypes.push( newProtectionType );
    },

    deleteProtectionDuration( duration ) {
      const index = this.protectionDurations.indexOf( duration );
      if ( index !== -1 ) {
        this.protectionDurations.splice( index, 1 );
      }
    },

    deleteProtectionType( type ) {
      const index = this.protectionTypes.indexOf( type );
      if ( index !== -1 ) {
        this.protectionTypes.splice( index, 1 );
      }
    },

    async saveConfiguration() {
      if ( !mw.config.get( 'wgUserGroups' ).includes( 'interface-admin' ) ) {
        mw.notify( mw.message( 'adiutor-interface-admin-required' ).text(), { type: 'error' } );
        return;
      }
      this.saveButtonLabel = mw.message( 'adiutor-configurations-saving' ).text();
      this.saveButtonAction = 'default';
      this.saveButtonDisabled = true;

      const api = new mw.Api();
      const editToken = mw.user.tokens.get( 'csrfToken' );
      const contentToSave = JSON.stringify( {
        protectionDurations: this.protectionDurations,
        protectionTypes: this.protectionTypes,
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
      }, null, 2 );

      try {
        await api.postWithToken( 'csrf', {
          action: 'edit',
          title: 'MediaWiki:AdiutorRequestPageProtection.json',
          text: contentToSave,
          token: editToken,
          format: 'json'
        } );
        mw.notify( mw.message( 'adiutor-localization-settings-has-been-updated' ).text(), {
          title: mw.msg( 'adiutor-operation-completed' ),
          type: 'success'
        } );
        this.saveButtonLabel = mw.message( 'adiutor-save-configurations' ).text();
        this.saveButtonAction = 'progressive';
        this.saveButtonDisabled = false;
      } catch ( error ) {
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
}</style>
