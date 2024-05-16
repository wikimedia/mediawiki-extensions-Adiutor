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
      {{ $i18n( "adiutor-create-speedy-deletion-configuration-title" ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( "adiutor-create-speedy-deletion-configuration-description" ) }}
    </p>
  </cdx-field>
  <cdx-message :dismiss-button-label="$i18n( 'adiutor-close' )" type="warning">
    <h3>{{ $i18n( "adiutor-parameters" ) }}</h3>
    <p>{{ $i18n( "adiutor-parameters-description" ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( "adiutor-parameters-page-name" ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( "adiutor-parameters-reason-data" ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( "adiutor-parameters-reason-value" ) }}</li>
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
        <strong>{{ $i18n( "adiutor-speedy-deletion-policy-link" ) }}</strong>
      </template>
      <cdx-text-input
          id="speedyDeletionPolicyLink"
          v-model="speedyDeletionPolicyLink"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-speedy-deletion-policy-page-shortcut" ) }}</strong>
      </template>
      <cdx-text-input
          id="speedyDeletionPolicyPageShortcut"
          v-model="speedyDeletionPolicyPageShortcut"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-csd-notification-template" ) }}</strong>
      </template>
      <cdx-text-input
          id="csdNotificationTemplate"
          v-model="csdNotificationTemplate"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <cdx-text-input v-model="copyVioReasonValue"></cdx-text-input>
      <template #label>
        {{ $i18n( "adiutor-copy-vio-reason-value" ) }}
      </template>
      <template #description>
        {{ $i18n( "adiutor-copy-vio-reason-value-description" ) }}
      </template>
      <template #help-text>
        {{ $i18n( "adiutor-copy-vio-reason-value-help-text" ) }}
      </template>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-single-reason-template" ) }}</strong>
      </template>
      <cdx-text-input
          id="csdTemplateStartSingleReason"
          v-model="csdTemplateStartSingleReason"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-single-reason-template-description" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template #label>
        {{ $i18n( "adiutor-postfix-reason-usage" ) }}
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
          <h5>{{ $i18n( "adiutor-output" ) }}</h5>
          <pre> {{ csdTemplateStartSingleReason }}{{ postfix.output }}</pre>
        </template>
      </cdx-radio>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-multiple-reason-template" ) }}</strong>
      </template>
      <cdx-text-input
          id="csdTemplateStartMultipleReason"
          v-model="csdTemplateStartMultipleReason"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-multiple-reason-template-description" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template #label>
        {{ $i18n( "adiutor-multiple-reason-separation" ) }}
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
          <h5>{{ $i18n( "adiutor-output" ) }}</h5>
          <pre> {{ csdTemplateStartMultipleReason }}{{ separator.output }}</pre>
        </template>
      </cdx-radio>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-single-reason-summary" ) }}</strong>
      </template>
      <cdx-text-input
          id="singleReasonSummary"
          v-model="singleReasonSummary"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-multiple-reason-summary" ) }}</strong>
      </template>
      <cdx-text-input
          id="multipleReasonSummary"
          v-model="multipleReasonSummary"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    {{ speedyDeletionReasons }}
    <cdx-table
        class="cdx-docs-table-custom-cells"
        :caption="$i18n( 'adiutor-speedy-deletion-namespaces' )"
        :columns="speedyDeletionNamespacesColumns"
        :data="speedyDeletionReasons"
    >
      <template #header>
        <div class="cdx-docs-table-with-selection__header">
          <cdx-button :aria-label="$i18n( 'adiutor-add-new' )" @click="addNewNameSpace">
            <cdx-icon :icon="cdxIconAdd"></cdx-icon>
          </cdx-button>
        </div>
      </template>
      <template #item-namespace="{ row, item }">
        <cdx-text-input
            v-model="row.namespace"
            :aria-label="$i18n( 'adiutor-name-space' )"
            :disabled="isNamespaceDisabled( item )"></cdx-text-input>
      </template>
      <template #item-name="{ row }">
        <cdx-text-input
            v-model="row.name"
            :aria-label="$i18n( 'adiutor-name' )"></cdx-text-input>
      </template>
      <template #item-actions="{ row }">
        <div class="cdx-docs-table-custom-cells__actions">
          <cdx-button
              weight="quiet"
              action="destructive"
              :aria-label="$i18n( 'adiutor-delete' )"
              :disabled="isNamespaceDisabled( row.namespace )"
              @click="deleteNameSpace( row )"
          >
            <cdx-icon :icon="cdxIconTrash"></cdx-icon>
          </cdx-button>
        </div>
      </template>
    </cdx-table>
  </cdx-field>
  <cdx-field v-for="( reasonNamespace, namespaceIndex ) in speedyDeletionReasons" :key="namespaceIndex">
    <cdx-table
        class="cdx-docs-table-custom-cells"
        :caption="$i18n( 'adiutor-deletion-reasons-for' ) + ' ' + reasonNamespace.name"
        :columns="speedyDeletionReasonsColumns"
        :data="reasonNamespace.reasons"
    >
      <template #header>
        <div class="cdx-docs-table-with-selection__header">
          <cdx-button :aria-label="$i18n( 'adiutor-add-new' )" @click="addNewReason( namespaceIndex )">
            <cdx-icon :icon="cdxIconAdd"></cdx-icon>
          </cdx-button>
        </div>
      </template>
      <template #item-value="{ row, item }">
        <cdx-text-input
            v-model="row.value"
            :aria-label="$i18n( 'adiutor-value' )"
            :disabled="isNamespaceDisabled( item )"></cdx-text-input>
      </template>
      <template #item-data="{ row }">
        <cdx-text-input
            v-model="row.data"
            :aria-label="$i18n( 'adiutor-data' )"></cdx-text-input>
      </template>
      <template #item-label="{ row }">
        <cdx-text-input
            v-model="row.label"
            :aria-label="$i18n( 'adiutor-label' )"></cdx-text-input>
      </template>
      <template #item-help="{ row }">
        <cdx-text-input
            v-model="row.help"
            :aria-label="$i18n( 'adiutor-help' )"></cdx-text-input>
      </template>
      <template #item-actions="{ row }">
        <div class="cdx-docs-table-custom-cells__actions">
          <cdx-button
              weight="quiet"
              action="destructive"
              :aria-label="$i18n( 'adiutor-delete' )"
              @click="deleteReason( row )">
            <cdx-icon :icon="cdxIconTrash"></cdx-icon>
          </cdx-button>
        </div>
      </template>
    </cdx-table>
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
  CdxButton,
  CdxTable,
  CdxIcon
} = require( '../../codex.js' );
const { cdxIconAdd, cdxIconTrash } = require( '../icons.json' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
module.exports = defineComponent( {
  name: 'CreateSpeedyDeletionOptions',
  components: {
    CdxChipInput,
    CdxTextInput,
    CdxMessage,
    CdxField,
    CdxRadio,
    CdxButton,
    CdxToggleSwitch,
    CdxTable,
    CdxIcon
  },
  setup() {
    const csdConfiguration = mw.config.get( 'wgAdiutorCreateSpeedyDeletion' );
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
        output: '|[[WP:CSD#A1]] and [[WP:CSD#G1]]}}'
      }
    ];
    const speedyDeletionNamespacesColumns = [
      { id: 'namespace', label: mw.msg( 'adiutor-name-space' ) },
      { id: 'name', label: mw.msg( 'adiutor-name' ) },
      { id: 'actions', label: mw.msg( 'adiutor-action' ), textAlign: 'end' }
    ];
    const speedyDeletionReasonsColumns = [
      { id: 'value', label: mw.msg( 'adiutor-value' ), maxWidth: '50px' },
      { id: 'data', label: mw.msg( 'adiutor-data' ) },
      { id: 'label', label: mw.msg( 'adiutor-label' ) },
      { id: 'help', label: mw.msg( 'adiutor-help' ) },
      { id: 'actions', label: mw.msg( 'adiutor-action' ), textAlign: 'end' }
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
      multipleReasonSeparationRadios,
      speedyDeletionNamespacesColumns,
      speedyDeletionReasonsColumns,
      cdxIconTrash,
      cdxIconAdd
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
      return disabledNamespaces.includes( reasonNamespace );
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

    deleteReason( reason ) {
      // Iterate over each namespace to find the one containing the reason
      for ( let namespaceIndex = 0; namespaceIndex < this.speedyDeletionReasons.length; namespaceIndex++ ) {
        const reasonNamespace = this.speedyDeletionReasons[ namespaceIndex ];

        // Find the index of the reason within the namespace
        const reasonIndex = reasonNamespace.reasons.indexOf( reason );

        // Check if the reason exists within the namespace
        if ( reasonIndex !== -1 ) {
          // Use splice to remove the reason at the specified reasonIndex
          reasonNamespace.reasons.splice( reasonIndex, 1 );
          break; // Exit the loop once the reason is found and removed
        }
      }
    },

    async saveConfiguration() {
      const configuration = {
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
      };
      const title = 'MediaWiki:AdiutorCreateSpeedyDeletion.json';

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

.ext-adiutor-options .top-message {
  margin-top: 10px;
}
</style>
