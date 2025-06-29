<template>
  <cdx-dialog
      v-model:open="openCsdDialog"
      :close-button-label="$i18n( 'adiutor-close' )"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-csd-header-title' )"
      class="csd-dialog"
      @default="openCsdDialog = false"
      @primary="createSpeedyDeletionRequest">
    <div class="adiutor-dialog-header">
      <p>{{ $i18n( "adiutor-csd-header-description" ) }}</p>
      <cdx-message
          v-if="deletionLogs.length"
          :dismiss-button-label="$i18n( 'adiutor-close' )"
          class="csd-deletion-log-message"
          inline
          type="warning"
          style="margin: 10px 0 0 0;">
        {{ $i18n( "adiutor-this-page-deleted-before", deletionLogs.length ) }}
        <small>(<a
            :href="'/wiki/Special:Log?type=delete&user=&page=' + encodeURIComponent( pageName )">
          {{ $i18n( "adiutor-deletion-log" ) }}</a>)</small>
      </cdx-message>
    </div>
    <div class="csd-reasons-body">
      <div class="csd-reason-field">
        <cdx-field v-for="namespaceReason in namespaceDeletionReasons" :is-fieldset="true">
          <template #label>
            {{ namespaceReason.name }}
          </template>
          <cdx-checkbox
              v-for="reason in namespaceReason.reasons"
              :key="reason.value"
              v-model="selectedDeletionReasons"
              :input-value="reason.value"
          >
            {{ reason.label }}
            <template #description>
              {{ reason.help }}
            </template>
          </cdx-checkbox>
        </cdx-field>
        <cdx-field v-if="!namespaceDeletionReasons.length" :is-fieldset="true">
          <cdx-message inline type="warning">
            <p>{{ $i18n( "adiutor-warning" ) }}</p>
            <small>{{ $i18n( "adiutor-no-namespace-reason-for-csd-title" ) }}</small>
          </cdx-message>
        </cdx-field>
      </div>
      <div class="csd-reason-field">
        <cdx-field v-for="generalReason in generalDeletionReasons" :is-fieldset="true">
          <template #label>
            {{ generalReason.name }}
          </template>
          <cdx-checkbox
              v-for="reasonItem in generalReason.reasons"
              :key="'checkbox-' + reasonItem.value"
              v-model="selectedDeletionReasons"
              :input-value="reasonItem.value"
              @change="toggleCopyVioInputBasedOnCheckbox"
          >
            {{ reasonItem.label }}
            <template #description>
              {{ reasonItem.help }}
            </template>
          </cdx-checkbox>
          <cdx-field v-if="showCopyVioInput">
            <template #label>
              <strong>{{ $i18n( "adiutor-copyright-infringing-page" ) }}</strong>
            </template>
            <cdx-text-input id="copyVioInputId" v-model="copyVioInput"></cdx-text-input>
          </cdx-field>
        </cdx-field>
      </div>
    </div>
    <template #footer-text>
      <cdx-checkbox v-model="recreationProrection">
        {{ $i18n( "adiutor-protect-against-rebuilding" ) }}
        <template #description>
          <small>{{ $i18n( "adiutor-protect-against-rebuilding-help" ) }}</small>
        </template>
      </cdx-checkbox>
      <cdx-checkbox v-model="informCreator">
        {{ $i18n( "adiutor-inform-creator" ) }}
        <template #description>
          <small>{{ $i18n( "adiutor-inform-creator-help" ) }}</small>
        </template>
      </cdx-checkbox>
      <!-- eslint-disable vue/no-v-html -->
      <span v-html="getModulePolicy()"></span>
    </template>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref, nextTick, onMounted } = require( 'vue' );
const { CdxCheckbox, CdxField, CdxDialog, CdxTextInput, CdxMessage } = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
const createSpeedyDeletionMiddleware = require( '../middlewares/speedyDeletionMiddleware.js' );
const csdConfiguration = mw.config.get( 'wgAdiutorCreateSpeedyDeletion' );
const speedyDeletionReasons = csdConfiguration.speedyDeletionReasons;
const copyVioReasonValue = csdConfiguration.copyVioReasonValue;
const namespaceDeletionReasons = [];
for ( const reason of speedyDeletionReasons ) {
  if ( reason.namespace === mw.config.get( 'wgNamespaceNumber' ) ) {
    if ( mw.config.get( 'wgIsRedirect' ) ) {
      namespaceDeletionReasons.push( speedyDeletionReasons.find( ( redirectReason ) => redirectReason.namespace === 'redirect' ) );
    } else {
      namespaceDeletionReasons.push( reason );
    }
  }
}
const generalDeletionReasons = [];
for ( const reason of speedyDeletionReasons ) {
  if ( reason.namespace === 'general' ) {
    generalDeletionReasons.push( reason );
  }
}

module.exports = defineComponent( {
  name: 'CreateSpeedyDeletion',
  components: {
    CdxDialog,
    CdxCheckbox,
    CdxField,
    CdxTextInput,
    CdxMessage
  },
  setup() {
    const selectedDeletionReasons = ref( [] );
    const copyVioInput = ref( '' );
    const recreationProrection = ref( false );
    const informCreator = ref( true );
    const openCsdDialog = ref( true );
    const showCopyVioInput = ref( false );
    const deletionLogs = ref( [] );
    const pageName = mw.config.get( 'wgPageName' );

    /**
     * Returns the module policy message.
     *
     * @return {string} The module policy message.
     */
    const getModulePolicy = () => {
      const policyLink = '<a href="' + mw.util.getUrl( csdConfiguration.speedyDeletionPolicyLink ) + '" target="_blank">' + mw.msg( 'adiutor-speedy-deletion-policy-name-lowercase' ) + '</a>';
      return mw.msg( 'adiutor-please-read-the-x-policy' ).replace( '$1', policyLink );
    };

    /**
     * Toggles the visibility of the copy violation input based on the checkbox value.
     */
    const toggleCopyVioInputBasedOnCheckbox = () => {
      const wasChecked = showCopyVioInput.value;
      showCopyVioInput.value = !wasChecked;
      nextTick( () => {
        showCopyVioInput.value = selectedDeletionReasons.value.includes( copyVioReasonValue );
      } );
    };

    const primaryAction = {
      icon: 'cdxIconTag',
      label: mw.msg( 'adiutor-request' ),
      actionType: 'progressive'
    };

    const defaultAction = {
      label: mw.msg( 'adiutor-cancel' )
    };

    /**
     * Creates a speedy deletion request.
     *
     * @return {Promise<void>}
     */
    const createSpeedyDeletionRequest = async () => {
      await createSpeedyDeletionMiddleware.createSpeedyDeletionRequest( pageName,
          selectedDeletionReasons.value,
          copyVioInput,
          informCreator,
          csdConfiguration );
    };

    onMounted( async () => {
      try {
        deletionLogs.value = await AdiutorUtility.getDeletionLogs( pageName );
      } catch ( error ) {
        AdiutorUtility.handleError( error );
      }
    } );

    return {
      selectedDeletionReasons,
      namespaceDeletionReasons,
      generalDeletionReasons,
      pageName,
      openCsdDialog,
      deletionLogs,
      primaryAction,
      defaultAction,
      copyVioInput,
      recreationProrection,
      informCreator,
      showCopyVioInput,
      getModulePolicy,
      toggleCopyVioInputBasedOnCheckbox,
      createSpeedyDeletionRequest
    };
  }
} );
</script>

<style lang="css">
.csd-dialog {
	max-width: 50em !important;
}

.csd-dialog .csd-reason-field {
	display: flex;
	flex-direction: column;
	width: 50%;
}

.csd-dialog .adiutor-dialog-header {
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 10em;
	background-image: url( ../../ext.adiutor.images/csd-icon-ltr.svg );
	background-position: 100% -4px;
	background-repeat: no-repeat;
	background-size: 140px;
}

.csd-reasons-body {
	display: flex;
	justify-content: space-between;
	gap: 10px;
}

.csd-dialog .adiutor-dialog-header p {
	width: 70%;
}

.csd-deletion-log-message {
	margin-left: 20px;
	margin-top: 20px;
}

/* Media query for mobile view */
@media only screen and ( max-width: 767px ) {
	.csd-dialog .adiutor-dialog-header {
		background: none; /* Hide background */
		margin-bottom: 10px !important;
	}

	.csd-dialog .adiutor-dialog-header p {
		width: 100% !important;
	}

	.csd-dialog .csd-reasons-body {
		flex-direction: column;
	}

	.csd-dialog .csd-reason-field {
		display: contents;
		flex-direction: column;
		width: 100%;
	}
}
</style>
