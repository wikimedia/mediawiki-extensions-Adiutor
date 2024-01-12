<template>
  <cdx-dialog
      v-model:open="openCsdDialog"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-csd-header-title' )"
      class="csd-dialog"
      close-button-label="Close"
      @default="openCsdDialog = true"
      @primary="createSpeedyDeletionRequest">
    <div class="header">
      <p>{{ $i18n( 'adiutor-csd-header-description' ) }}</p>
    </div>
    <cdx-message
        v-if="deletionLogs.length"
        class="csd-deletion-log-message"
        dismiss-button-label="Close"
        inline
        type="warning">
      {{ $i18n( 'adiutor-this-page-deleted-before', deletionLogs.length ) }}
      <small>(<a
          :href="'/wiki/Special:Log?type=delete&user=&page=' + encodeURIComponent( pageName )">{{
          $i18n( 'adiutor-deletion-log' )
        }}</a>)</small>
    </cdx-message>
    <div class="csd-reasons-body">
      <div class="csd-reason-field">
        <cdx-field v-for="namespaceReason in namespaceDeletionReasons" :is-fieldset="true">
          <cdx-label class="adt-label">
            <strong>{{ namespaceReason.name }}</strong>
          </cdx-label>
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
          <cdx-label class="adt-label">
            <strong>{{ $i18n( 'adiutor-other-options' ) }}</strong>
          </cdx-label>
          <cdx-checkbox v-model="recreationProrection">
            {{ $i18n( 'adiutor-protect-against-rebuilding' ) }}
            <template #description>
              <small>{{ $i18n( 'adiutor-protect-against-rebuilding-help' ) }}</small>
            </template>
          </cdx-checkbox>
          <cdx-checkbox v-model="informCreator">
            {{ $i18n( 'adiutor-inform-creator' ) }}
            <template #description>
              <small>{{ $i18n( 'adiutor-inform-creator-help' ) }}</small>
            </template>
          </cdx-checkbox>
        </cdx-field>
        <cdx-field v-if="!namespaceDeletionReasons.length" :is-fieldset="true">
          <cdx-message inline type="warning">
            <p>{{ $i18n( 'adiutor-warning' ) }}</p>
            <small>{{ $i18n( 'adiutor-no-namespace-reason-for-csd-title' ) }}</small>
          </cdx-message>
        </cdx-field>
      </div>
      <div class="csd-reason-field">
        <cdx-field v-for="generalReason in generalDeletionReasons" :is-fieldset="true">
          <cdx-label class="adt-label">
            <strong>{{ generalReason.name }}</strong>
          </cdx-label>
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
          <div v-if="showCopyVioInput">
            <cdx-label class="adt-label">
              <strong>{{ $i18n( 'adiutor-copyright-infringing-page' ) }}</strong>
            </cdx-label>
            <cdx-text-input v-model="copyVioInput" aria-label="TextInput default demo"></cdx-text-input>
          </div>
        </cdx-field>
      </div>
    </div>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref, nextTick, onMounted } = require( 'vue' );
const { CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxMessage } = require( '@wikimedia/codex' );
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
    CdxLabel,
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
      label: mw.msg( 'adiutor-speedy-deletion-policy' )
    };

    /**
     * Creates a speedy deletion request.
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
      toggleCopyVioInputBasedOnCheckbox,
      createSpeedyDeletionRequest
    };
  }
} );
</script>

<style lang="css">
.csd-dialog {
  max-width: 45em;
}

.csd-dialog .csd-reasons-body {
  display: flex;
  padding: 20px;
}

.csd-dialog .cdx-dialog {
  max-width: 720px;
  padding-top: 10px;
  padding-bottom: 0;
}

.csd-dialog .cdx-dialog__body {
  flex-grow: 1;
  margin-top: 0;
  padding: 0;
  overflow-y: auto;
}

.csd-dialog .cdx-dialog--dividers .cdx-dialog__body {
  padding-top: 0;
}

.csd-dialog .csd-reason-field {
  display: flex;
  flex-direction: column;
  width: 50%;
}

.csd-dialog .cdx-dialog__header {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-sizing: border-box;
  width: 100%;
  padding: 10px 20px 10px;
  font-weight: 700;
}

.csd-dialog cdx-label {
  margin-bottom: 10px;
  display: block;
}

.csd-dialog .header {
  background-color: #eaf3ff;
  display: block;
  align-items: baseline;
  justify-content: space-between;
  height: 10em;
  padding: 20px;
  background-image: url(../../ext.adiutor.images/csd-background.png);
  background-position: right 10px;
  background-repeat: no-repeat;
  background-size: 205px;
}

.csd-dialog .cdx-dialog__footer {
  padding: 20px !important;
  border-top: 1px solid #a2a9b1;
}

.csd-dialog .header p {
  width: 70%;
}

.csd-deletion-log-message {
  margin-left: 20px;
  margin-top: 20px;
}

.csd-dialog h2 {
  margin: 0;
  padding: 0;
  font-size: 1.125em !important
}
</style>
