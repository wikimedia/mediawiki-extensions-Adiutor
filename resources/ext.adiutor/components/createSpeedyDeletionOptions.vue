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
      {{ $i18n( 'adiutor-create-speedy-deletion-configuration-title' ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( 'adiutor-create-speedy-deletion-configuration-description' ) }}
    </p>
  </cdx-field>
  <cdx-message dismiss-button-label="Close" type="warning">
    <h3>{{ $i18n( 'adiutor-parameters' ) }}</h3>
    <p>{{ $i18n( 'adiutor-parameters-description' ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( 'adiutor-parameters-page-name' ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( 'adiutor-parameters-reason-data' ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( 'adiutor-parameters-reason-value' ) }}</li>
    </ul>
  </cdx-message>
  <cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-toggle-switch v-model="moduleEnabled">
        <cdx-label input-id="moduleEnabled">
          {{ $i18n( 'adiutor-module-enabled' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-module-enabled-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-toggle-switch v-model="testMode">
        <cdx-label input-id="testMode">
          {{ $i18n( 'adiutor-test-mode' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-test-mode-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-chip-input v-model:input-chips="namespaces" remove-button-label="remove"></cdx-chip-input>
      <template #label>
        {{ $i18n( 'adiutor-namespaces' ) }}
      </template>
      <template #description>
        {{ $i18n( 'adiutor-namespaces-description' ) }}
      </template>
    </cdx-field>
    <cdx-label input-id="speedyDeletionPolicyLink">
      {{ $i18n( 'adiutor-speedy-deletion-policy-link' ) }}
    </cdx-label>
    <cdx-text-input
        id="speedyDeletionPolicyLink"
        v-model="speedyDeletionPolicyLink"
        aria-label="{{ $i18n('adiutor-speedy-deletion-policy-link') }}"></cdx-text-input>
    <cdx-label input-id="speedyDeletionPolicyPageShortcut">
      {{ $i18n( 'adiutor-speedy-deletion-policy-page-shortcut' ) }}
    </cdx-label>
    <cdx-text-input
        id="speedyDeletionPolicyPageShortcut"
        v-model="speedyDeletionPolicyPageShortcut"
        aria-label="{{ $i18n('adiutor-speedy-deletion-policy-page-shortcut') }}"></cdx-text-input>
    <cdx-label input-id="csdNotificationTemplate">
      {{ $i18n( 'adiutor-csd-notification-template' ) }}
    </cdx-label>
    <cdx-text-input
        id="csdNotificationTemplate"
        v-model="csdNotificationTemplate"
        aria-label="{{ $i18n('adiutor-csd-notification-template') }}"></cdx-text-input>
    <cdx-field>
      <cdx-text-input v-model="copyVioReasonValue" :start-icon="cdxIconWikiText"></cdx-text-input>
      <template #label>
        {{ $i18n( 'adiutor-copy-vio-reason-value' ) }}
      </template>
      <template #description>
        {{ $i18n( 'adiutor-copy-vio-reason-value-description' ) }}
      </template>
      <template #help-text>
        {{ $i18n( 'adiutor-copy-vio-reason-value-help-text' ) }}
      </template>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <strong>{{ $i18n( 'adiutor-in-page-request-templating-configuration' ) }}</strong>
      <cdx-label input-id="csdTemplateStartSingleReason">
        {{ $i18n( 'adiutor-single-reason-template' ) }}
      </cdx-label>
      <cdx-text-input
          id="csdTemplateStartSingleReason"
          v-model="csdTemplateStartSingleReason"
          aria-label="{{ $i18n('adiutor-single-reason-template') }}"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-single-reason-template-description' ) }}
      </template>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <template #label>
        {{ $i18n( 'adiutor-postfix-reason-usage' ) }}
      </template>
      <cdx-radio
          v-for="postfix in postfixReasonUsageRadios"
          :key="'radio-' + postfix.value"
          v-model="postfixReasonUsage"
          :input-value="postfix.value"
          name="radio-group-{{postfix.value}}-descriptions">
        {{ postfix.label }}
        <template #description>
          {{ postfix.description }}
          <h5>{{ $i18n( 'adiutor-output' ) }}</h5>
          <pre> {{ csdTemplateStartSingleReason }}{{ postfix.output }}</pre>
        </template>
      </cdx-radio>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="csdTemplateStartMultipleReason">
        {{ $i18n( 'adiutor-multiple-reason-template' ) }}
      </cdx-label>
      <cdx-text-input
          id="csdTemplateStartMultipleReason"
          v-model="csdTemplateStartMultipleReason"
          aria-label="{{ $i18n('adiutor-multiple-reason-template') }}"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-multiple-reason-template-description' ) }}
      </template>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <template #label>
        {{ $i18n( 'adiutor-multiple-reason-separation' ) }}
      </template>
      <cdx-radio
          v-for="separator in multipleReasonSeparationRadios"
          :key="'radio-' + separator.value"
          v-model="multipleReasonSeparation"
          :input-value="separator.value"
          name="radio-group-{{separator.value}}-descriptions">
        {{ separator.label }}
        <template #description>
          {{ separator.description }}
          <h5>{{ $i18n( 'adiutor-output' ) }}</h5>
          <pre> {{ csdTemplateStartMultipleReason }}{{ separator.output }}</pre>
        </template>
      </cdx-radio>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <strong>{{ $i18n( 'adiutor-summaries' ) }}</strong>
      <cdx-label input-id="singleReasonSummary">
        {{ $i18n( 'adiutor-single-reason-summary' ) }}
      </cdx-label>
      <cdx-text-input
          id="singleReasonSummary"
          v-model="singleReasonSummary"
          aria-label="{{ $i18n('adiutor-single-reason-summary') }}"></cdx-text-input>
      <cdx-label input-id="multipleReasonSummary">
        {{ $i18n( 'adiutor-multiple-reason-summary' ) }}
      </cdx-label>
      <cdx-text-input
          id="multipleReasonSummary"
          v-model="multipleReasonSummary"
          aria-label="{{ $i18n('adiutor-multiple-reason-summary') }}"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <table id="adiutor-options-props" width="100%">
      <caption>
        {{ $i18n( 'adiutor-speedy-deletion-reasons' ) }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewNameSpace">
          {{ $i18n( 'adiutor-add-new' ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( 'adiutor-name-space' ) }}</th>
        <th>{{ $i18n( 'adiutor-name' ) }}</th>
        <th>{{ $i18n( 'adiutor-action' ) }}</th>
      </tr>
      <tr v-for="( reasonNamespace, index ) in speedyDeletionReasons" :key="index">
        <td>
          <cdx-text-input
              v-model="reasonNamespace.namespace"
              :disabled="isNamespaceDisabled( reasonNamespace )"
              aria-label="{{ $i18n('adiutor-name-space') }}"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input
              v-model="reasonNamespace.name"
              aria-label="{{ $i18n('adiutor-name') }}"></cdx-text-input>
        </td>
        <td>
          <cdx-button
              :disabled="isNamespaceDisabled( reasonNamespace )"
              action="destructive"
              @click="deleteNameSpace( reasonNamespace )">
            {{ $i18n( 'adiutor-delete' ) }}
          </cdx-button>
        </td>
      </tr>
    </table>
  </cdx-field>
  <cdx-field
      v-for="( reasonNamespace, namespaceIndex ) in speedyDeletionReasons"
      :key="'nestedTable-' + namespaceIndex">
    <table id="adiutor-options-props" width="100%">
      <caption>
        {{ $i18n( 'adiutor-deletion-reasons-for' ) }} {{ reasonNamespace.name }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewReason( namespaceIndex )">
          {{ $i18n( 'adiutor-add-new' ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( 'adiutor-value' ) }}</th>
        <th>{{ $i18n( 'adiutor-data' ) }}</th>
        <th>{{ $i18n( 'adiutor-label' ) }}</th>
        <th>{{ $i18n( 'adiutor-help' ) }}</th>
        <th>{{ $i18n( 'adiutor-action' ) }}</th>
      </tr>
      <tr v-for="( reason, reasonIndex ) in reasonNamespace.reasons" :key="'reason-' + reasonIndex">
        <td>
          <cdx-text-input
              v-model="reason.value"
              aria-label="{{ $i18n('adiutor-value') }}"
              style="min-width: 50px;"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="reason.data" aria-label="{{ $i18n('adiutor-data') }}"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="reason.label" aria-label="{{ $i18n('adiutor-label') }}"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="reason.help" aria-label="{{ $i18n('adiutor-help') }}"></cdx-text-input>
        </td>
        <td>
          <cdx-button action="destructive" @click="deleteReason( namespaceIndex, reasonIndex )">
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
  CdxMessage,
  CdxTextInput,
  CdxChipInput,
  CdxToggleSwitch,
  CdxField,
  CdxRadio,
  CdxButton
} = require( '@wikimedia/codex' );
const csdConfiguration = mw.config.get( 'wgAdiutorCreateSpeedyDeletion' );
module.exports = defineComponent( {
  name: 'CreateSpeedyDeletionOptions',
  components: {
    CdxChipInput,
    CdxTextInput,
    CdxMessage,
    CdxField,
    CdxRadio,
    CdxButton,
    CdxToggleSwitch
  },
  setup() {
    const speedyDeletionReasons = ref( csdConfiguration.speedyDeletionReasons );
    const csdTemplateStartSingleReason = ref( csdConfiguration.csdTemplateStartSingleReason );
    const csdTemplateStartMultipleReason = ref( csdConfiguration.csdTemplateStartMultipleReason );
    const speedyDeletionPolicyLink = ref( csdConfiguration.speedyDeletionPolicyLink );
    const speedyDeletionPolicyPageShortcut = ref( csdConfiguration.speedyDeletionPolicyPageShortcut );
    const csdNotificationTemplate = ref( csdConfiguration.csdNotificationTemplate );
    const singleReasonSummary = ref( csdConfiguration.singleReasonSummary );
    const multipleReasonSummary = ref( csdConfiguration.multipleReasonSummary );
    const copyVioReasonValue = ref( csdConfiguration.copyVioReasonValue );
    const postfixReasonUsage = ref( csdConfiguration.postfixReasonUsage );
    const moduleEnabled = ref( csdConfiguration.moduleEnabled );
    const testMode = ref( csdConfiguration.testMode );
    const namespaces = ref( csdConfiguration.namespaces );
    const postfixReasonUsageRadios = [
      {
        label: mw.message( 'adiutor-csd-use-reason-value' ).text(),
        description: mw.message( 'adiutor-csd-use-reason-value-description' ).text(),
        value: 'value',
        parameter: '$3',
        output: 'A1}}'
      },
      {
        label: mw.message( 'adiutor-csd-use-reason-data' ).text(),
        description: mw.message( 'adiutor-csd-use-reason-data-description' ).text(),
        value: 'data',
        parameter: '$2',
        output: '[[WP:CSD#A1|A1]]: Short article without enough context to identify the subject}}'
      }
    ];
    const multipleReasonSeparation = ref( csdConfiguration.multipleReasonSeparation );
    const multipleReasonSeparationRadios = [
      {
        label: mw.message( 'adiutor-csd-use-vertical-bar' ).text(),
        description: mw.message( 'adiutor-csd-use-vertical-bar-description' ).text(),
        value: 'vertical_bar',
        parameter: '$3',
        output: '|A1|G1}}'
      },
      {
        label: mw.message( 'adiutor-csd-default' ).text(),
        description: mw.message( 'adiutor-csd-default-description' ).text(),
        value: 'default',
        parameter: '$2',
        output: '[[WP:CSD#A1]] and [[WP:CSD#G1]]}}'
      }
    ];

    return {
      speedyDeletionReasons,
      csdTemplateStartSingleReason,
      csdTemplateStartMultipleReason,
      speedyDeletionPolicyLink,
      speedyDeletionPolicyPageShortcut,
      csdNotificationTemplate,
      singleReasonSummary,
      multipleReasonSummary,
      copyVioReasonValue,
      postfixReasonUsage,
      moduleEnabled,
      namespaces,
      testMode,
      postfixReasonUsageRadios,
      multipleReasonSeparation,
      multipleReasonSeparationRadios
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

    isNamespaceDisabled( reasonNamespace ) {
      const disabledNamespaces = [ 'general', 'redirect', 'other' ];
      return disabledNamespaces.includes( reasonNamespace.namespace );
    },

    addNewNameSpace() {
      const newNameSpace = {
        name: '',
        namespace: '',
        items: []
      };
      this.speedyDeletionReasons.push( newNameSpace );
    },

    deleteNameSpace( namespace ) {
      const index = this.speedyDeletionReasons.indexOf( namespace );
      if ( index !== -1 ) {
        this.speedyDeletionReasons.splice( index, 1 );
      }
    },

    addNewReason( namespaceIndex ) {
      // Find the namespace based on the provided namespaceIndex
      const reasonNamespace = this.speedyDeletionReasons[ namespaceIndex ];

      if ( reasonNamespace ) {
        const newReason = {
          namespace: reasonNamespace.namespace,
          value: '',
          data: '',
          selected: false,
          label: '',
          help: ''
        };
        reasonNamespace.reasons.push( newReason );
      }
    },

    deleteReason( namespaceIndex, reasonIndex ) {
      // Find the namespace based on the provided namespaceIndex
      const reasonNamespace = this.speedyDeletionReasons[ namespaceIndex ];

      // Check if the reasonNamespace and the reason exist (a safety check)
      if ( reasonNamespace && reasonNamespace.reasons[ reasonIndex ] ) {
        // Use splice to remove the reason at the specified reasonIndex
        reasonNamespace.reasons.splice( reasonIndex, 1 );
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
        speedyDeletionReasons: this.speedyDeletionReasons,
        csdTemplateStartSingleReason: this.csdTemplateStartSingleReason,
        csdTemplateStartMultipleReason: this.csdTemplateStartMultipleReason,
        postfixReasonUsage: this.postfixReasonUsage,
        multipleReasonSeparation: this.multipleReasonSeparation,
        speedyDeletionPolicyLink: this.speedyDeletionPolicyLink,
        speedyDeletionPolicyPageShortcut: this.speedyDeletionPolicyPageShortcut,
        csdNotificationTemplate: this.csdNotificationTemplate,
        singleReasonSummary: this.singleReasonSummary,
        multipleReasonSummary: this.multipleReasonSummary,
        copyVioReasonValue: this.copyVioReasonValue,
        moduleEnabled: this.moduleEnabled,
        testMode: this.testMode,
        namespaces: this.namespaces
      }, null, 2 );

      const data = {
        action: 'edit',
        title: 'MediaWiki:AdiutorCreateSpeedyDeletion.json',
        text: contentToSave,
        token: editToken,
        format: 'json'
      };

      api.post( data ).done( ( response ) => {
        if ( response.edit && response.edit.result === 'Success' ) {
          mw.notify( mw.message( 'adiutor-localization-settings-has-been-updated' ).text(), {
            title: mw.msg( 'adiutor-operation-completed' ),
            type: 'success'
          } );
          this.saveButtonLabel = mw.message( 'adiutor-save-configurations' ).text();
          this.saveButtonAction = 'progressive';
          this.saveButtonDisabled = false;
        } else {
          throw new Error( mw.msg( 'adiutor-operation-failed' ) );
        }
      } ).fail( ( error ) => {
        mw.notify( error, { type: 'error' } );
        this.saveButtonLabel = mw.message( 'adiutor-try-again' ).text();
        this.saveButtonAction = 'destructive';
        this.saveButtonDisabled = false;
      } );
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
