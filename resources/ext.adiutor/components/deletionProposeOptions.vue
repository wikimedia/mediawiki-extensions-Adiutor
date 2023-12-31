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
      {{ $i18n( 'adiutor-proposed-deletion-configuration' ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( 'adiutor-proposed-deletion-description' ) }}
    </p>
  </cdx-field>
  <cdx-message dismiss-button-label="{{ $i18n('adiutor-close') }}" type="warning">
    <h3>{{ $i18n( 'adiutor-parameters' ) }}</h3>
    <p>{{ $i18n( 'adiutor-parameters-description' ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( 'adiutor-parameters-page-name' ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( 'adiutor-parameters-rationale' ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( 'adiutor-parameters-day' ) }}</li>
      <li><strong>$4</strong>: {{ $i18n( 'adiutor-parameters-month' ) }}</li>
      <li><strong>$5</strong>: {{ $i18n( 'adiutor-parameters-year' ) }}</li>
      <li><strong>$6</strong>: {{ $i18n( 'adiutor-parameters-requester' ) }}</li>
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
    <cdx-field>
      <cdx-label input-id="proposedDeletionPolicy">
        {{ $i18n( 'adiutor-proposed-deletion-policy' ) }}
      </cdx-label>
      <cdx-text-input
          id="proposedDeletionPolicy"
          v-model="proposedDeletionPolicy"
          aria-label="{{ $i18n('adiutor-proposed-deletion-policy') }}"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="proposedDeletionPolicyShortcut">
        {{ $i18n( 'adiutor-proposed-deletion-policy-shortcut' ) }}
      </cdx-label>
      <cdx-text-input
          id="proposedDeletionPolicyShortcut"
          v-model="proposedDeletionPolicyShortcut"
          aria-label="{{ $i18n('adiutor-proposed-deletion-policy-shortcut') }}"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="proposedDeletionOfBiographiesOfLivingPeoplePolicy">
        {{ $i18n( 'adiutor-proposed-deletion-blpprod-policy' ) }}
      </cdx-label>
      <cdx-text-input
          id="proposedDeletionOfBiographiesOfLivingPeoplePolicy"
          v-model="proposedDeletionOfBiographiesOfLivingPeoplePolicy"
          aria-label="{{ $i18n('adiutor-proposed-deletion-blpprod-policy') }}"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut">
        {{ $i18n( 'adiutor-proposed-deletion-blpprod-policy-shortcut' ) }}
      </cdx-label>
      <cdx-text-input
          id="proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut"
          v-model="proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut"
          aria-label="{{ $i18n('adiutor-proposed-deletion-blpprod-policy-shortcut') }}"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <strong>{{ $i18n( 'adiutor-in-page-request-templating-configuration' ) }}</strong>
      <cdx-label input-id="standardProposeTemplate">
        {{ $i18n( 'adiutor-proposed-deletion-template' ) }}
      </cdx-label>
      <cdx-text-input
          id="standardProposeTemplate"
          v-model="standardProposeTemplate"
          aria-label="{{ $i18n('adiutor-proposed-deletion-template') }}"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-proposed-deletion-template-description' ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="livingPersonProposeTemplate">
        {{ $i18n( 'adiutor-proposed-deletion-blps-template' ) }}
      </cdx-label>
      <cdx-text-input
          id="livingPersonProposeTemplate"
          v-model="livingPersonProposeTemplate"
          aria-label="{{ $i18n('adiutor-proposed-deletion-blps-template') }}"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-proposed-deletion-blps-template-description' ) }}
      </template>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-label input-id="prodNotificationTemplate">
      {{ $i18n( 'adiutor-prod-notification-template' ) }}
    </cdx-label>
    <cdx-text-input
        id="prodNotificationTemplate"
        v-model="prodNotificationTemplate"
        aria-label="{{ $i18n('adiutor-prod-notification-template') }}"></cdx-text-input>
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
          aria-label="{{ $i18n('adiutor-api-post-summary-for-talk-page') }}"></cdx-text-input>
      <cdx-label input-id="apiPostSummaryForCreator">
        {{ $i18n( 'adiutor-api-post-summary-for-creator' ) }}
      </cdx-label>
      <cdx-text-input
          id="apiPostSummaryForCreator"
          v-model="apiPostSummaryForCreator"
          aria-label="{{ $i18n('adiutor-api-post-summary-for-creator') }}"></cdx-text-input>
    </cdx-field>
  </cdx-field>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxMessage, CdxTextInput, CdxChipInput, CdxToggleSwitch, CdxField, CdxButton } = require( '@wikimedia/codex' );
const dprConfiguration = mw.config.get( 'AdiutorDeletionPropose' );
module.exports = defineComponent( {
  name: 'DeletionProposeOptions',
  components: {
    CdxTextInput,
    CdxChipInput,
    CdxToggleSwitch,
    CdxMessage,
    CdxField,
    CdxButton
  },
  setup() {
    const standardProposeTemplate = ref( dprConfiguration.standardProposeTemplate );
    const livingPersonProposeTemplate = ref( dprConfiguration.livingPersonProposeTemplate );
    const apiPostSummary = ref( dprConfiguration.apiPostSummary );
    const apiPostSummaryForCreator = ref( dprConfiguration.apiPostSummaryForCreator );
    const prodNotificationTemplate = ref( dprConfiguration.prodNotificationTemplate );
    const moduleEnabled = ref( dprConfiguration.moduleEnabled );
    const testMode = ref( dprConfiguration.testMode );
    const namespaces = ref( dprConfiguration.namespaces );
    const proposedDeletionPolicy = ref( dprConfiguration.proposedDeletionPolicy );
    const proposedDeletionPolicyShortcut = ref( dprConfiguration.proposedDeletionPolicyShortcut );
    const proposedDeletionOfBiographiesOfLivingPeoplePolicy = ref( dprConfiguration.proposedDeletionOfBiographiesOfLivingPeoplePolicy );
    const proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut = ref( dprConfiguration.proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut );

    return {
      standardProposeTemplate,
      livingPersonProposeTemplate,
      apiPostSummary,
      apiPostSummaryForCreator,
      prodNotificationTemplate,
      moduleEnabled,
      testMode,
      namespaces,
      proposedDeletionPolicy,
      proposedDeletionPolicyShortcut,
      proposedDeletionOfBiographiesOfLivingPeoplePolicy,
      proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut
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
        standardProposeTemplate: this.standardProposeTemplate,
        livingPersonProposeTemplate: this.livingPersonProposeTemplate,
        apiPostSummary: this.apiPostSummary,
        apiPostSummaryForCreator: this.apiPostSummaryForCreator,
        prodNotificationTemplate: this.prodNotificationTemplate,
        moduleEnabled: this.moduleEnabled,
        testMode: this.testMode,
        namespaces: this.namespaces,
        proposedDeletionPolicy: this.proposedDeletionPolicy,
        proposedDeletionPolicyShortcut: this.proposedDeletionPolicyShortcut,
        proposedDeletionOfBiographiesOfLivingPeoplePolicy: this.proposedDeletionOfBiographiesOfLivingPeoplePolicy,
        proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut: this.proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut
      }, null, 2 );

      const data = {
        action: 'edit',
        title: 'MediaWiki:AdiutorDeletionPropose.json',
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
