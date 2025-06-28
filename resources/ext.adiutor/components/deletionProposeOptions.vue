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
      {{ $i18n( "adiutor-proposed-deletion-configuration" ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( "adiutor-proposed-deletion-description" ) }}
    </p>
  </cdx-field>
  <cdx-message :dismiss-button-label="$i18n( 'adiutor-close' )" type="warning">
    <h3>{{ $i18n( "adiutor-parameters" ) }}</h3>
    <p>{{ $i18n( "adiutor-parameters-description" ) }}</p>
    <ul>
      <li><strong>$1</strong>: {{ $i18n( "adiutor-parameters-page-name" ) }}</li>
      <li><strong>$2</strong>: {{ $i18n( "adiutor-parameters-rationale" ) }}</li>
      <li><strong>$3</strong>: {{ $i18n( "adiutor-parameters-day" ) }}</li>
      <li><strong>$4</strong>: {{ $i18n( "adiutor-parameters-month" ) }}</li>
      <li><strong>$5</strong>: {{ $i18n( "adiutor-parameters-year" ) }}</li>
      <li><strong>$6</strong>: {{ $i18n( "adiutor-parameters-requester" ) }}</li>
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
        <strong>{{ $i18n( "adiutor-proposed-deletion-policy" ) }}</strong>
      </template>
      <cdx-text-input
          id="proposedDeletionPolicy"
          v-model="proposedDeletionPolicy"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-proposed-deletion-policy-shortcut" ) }}</strong>
      </template>
      <cdx-text-input
          id="proposedDeletionPolicyShortcut"
          v-model="proposedDeletionPolicyShortcut"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-proposed-deletion-blpprod-policy" ) }}</strong>
      </template>
      <cdx-text-input
          id="proposedDeletionOfBiographiesOfLivingPeoplePolicy"
          v-model="proposedDeletionOfBiographiesOfLivingPeoplePolicy"></cdx-text-input>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-proposed-deletion-blpprod-policy-shortcut" ) }}</strong>
      </template>
      <cdx-text-input
          id="proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut"
          v-model="proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-proposed-deletion-template" ) }}</strong>
      </template>
      <cdx-text-input
          id="standardProposeTemplate"
          v-model="standardProposeTemplate"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-proposed-deletion-template-description" ) }}
      </template>
    </cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-proposed-deletion-blps-template" ) }}</strong>
      </template>
      <cdx-text-input
          id="livingPersonProposeTemplate"
          v-model="livingPersonProposeTemplate"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-proposed-deletion-blps-template-description" ) }}
      </template>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <template #label>
      <strong>{{ $i18n( "adiutor-prod-notification-template" ) }}</strong>
    </template>
    <cdx-text-input
        id="prodNotificationTemplate"
        v-model="prodNotificationTemplate"></cdx-text-input>
  </cdx-field>
  <cdx-field>
    <template #label>
      <strong>{{ $i18n( "adiutor-api-post-summary" ) }}</strong>
    </template>
    <cdx-text-input
        id="apiPostSummary"
        v-model="apiPostSummary"></cdx-text-input>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-api-post-summary-for-creator" ) }}</strong>
      </template>
      <cdx-text-input
          id="apiPostSummaryForCreator"
          v-model="apiPostSummaryForCreator"></cdx-text-input>
    </cdx-field>
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
  CdxButton
} = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
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
    const dprConfiguration = mw.config.get( 'wgAdiutorDeletionPropose' );
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
      const configuration = {
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
      };
      const title = 'MediaWiki:AdiutorDeletionPropose.json';

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
.ext-adiutor-options .top-message {
	margin-top: 10px;
}
</style>
