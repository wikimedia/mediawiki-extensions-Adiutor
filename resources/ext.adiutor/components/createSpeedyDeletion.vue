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
              v-model="checkboxValue"
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
              v-model="checkboxValue"
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
const csdConfiguration = mw.config.get( 'wgAdiutorCreateSpeedyDeletion' );
const speedyDeletionReasons = csdConfiguration.speedyDeletionReasons;
const copyVioReasonValue = csdConfiguration.copyVioReasonValue;
const postfixReasonUsage = csdConfiguration.postfixReasonUsage;
const multipleReasonSeparation = csdConfiguration.multipleReasonSeparation;
const csdTemplateStartSingleReason = csdConfiguration.csdTemplateStartSingleReason;
const csdTemplateStartMultipleReason = csdConfiguration.csdTemplateStartMultipleReason;
const singleReasonSummary = csdConfiguration.singleReasonSummary;
const multipleReasonSummary = csdConfiguration.multipleReasonSummary;
// const speedyDeletionPolicyLink = csdConfiguration.speedyDeletionPolicyLink;
const speedyDeletionPolicyPageShortcut = csdConfiguration.speedyDeletionPolicyPageShortcut;
const csdNotificationTemplate = csdConfiguration.csdNotificationTemplate;
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
    const api = new mw.Api();
    const checkboxValue = ref( [] );
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
        showCopyVioInput.value = checkboxValue.value.includes( copyVioReasonValue );
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
     * Creates an API request to tag an article for speedy deletion.
     *
     * @param {string} csdReason - The reason for the speedy deletion.
     * @param {string} csdSummary - The summary of the speedy deletion.
     */
    const createApiRequest = async ( csdReason, csdSummary ) => {
      // API request to tag the article for speedy deletion
      api.postWithToken( 'csrf', {
        action: 'edit',
        title: mw.config.get( 'wgPageName' ),
        prependtext: csdReason + '\n',
        summary: csdSummary,
        format: 'json'
      } ).done( function () {
        openCsdDialog.value = false;
        mw.notify( 'Article tagged for speedy deletion', {
          title: mw.msg( 'adiutor-operation-completed' ),
          type: 'success'
        } );
        window.location.reload();
      } ).fail( function ( error ) {
        mw.notify( 'Failed to tag article for speedy deletion: ' + error, {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      } );
    };

    /**
     * Sends a notification message to the author of an article.
     *
     * @param {string} articleAuthor - The username of the article author.
     * @param {string} notificationMessage - The message to be sent as a notification.
     * @param {string} summary
     * @return {Promise<void>} - A promise that resolves when the message is sent successfully.
     */
    const notifyAuthor = async ( articleAuthor, notificationMessage, summary ) => {
      const data = {
        title: mw.config.get( 'wgPageName' ),
        content: {
          author: articleAuthor,
          reason: summary,
          title: mw.config.get( 'wgPageName' )
        }
      };
      const apiUrl = mw.config.get( 'wgServer' ) + mw.config.get( 'wgScriptPath' ) + '/rest.php/adiutor/v0/notifier';
      try {
        await fetch( apiUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify( data )
        } );
      } catch ( error ) {
        mw.notify( error.message, {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      }
    };

    /**
     * Creates a speedy deletion request based on the selected reasons.
     * If there are selected reasons, it constructs the csdReason and csdSummary based on the selectedReasons array.
     * It then calls the createApiRequest function with the csdReason and csdSummary.
     * If informCreator is true, it fetches the creator of the article and sends a notification message to the creator.
     * If there are no selected reasons, it displays an error notification.
     */
    const createSpeedyDeletionRequest = async () => {
      const selectedReasons = speedyDeletionReasons.reduce( ( selected, category ) => {
        const selectedInCategory = category.reasons.filter( ( reason ) => {
          return checkboxValue.value.includes( reason.value );
        } );
        selected.push( ...selectedInCategory );
        return selected;
      }, [] );

      if ( selectedReasons.length ) {
        let csdReason;
        let csdSummary;
        let saltCSDSummary = '';
        let copyVioURL = '';
        if ( copyVioInput.value !== '' ) {
          copyVioURL = '|url=' + copyVioInput.value;
        }
        if ( selectedReasons.length > 1 ) {
          let saltCSDReason = csdTemplateStartMultipleReason;
          if ( multipleReasonSeparation === 'vertical_bar' ) {
            for ( let i = 0; i < selectedReasons.length; i++ ) {
              saltCSDReason += '|' + selectedReasons[ i ].value;
            }
            csdReason = saltCSDReason + '}}';
          } else if ( multipleReasonSeparation === 'default' ) {
            for ( let i = 0; i < selectedReasons.length; i++ ) {
              if ( i > 0 ) {
                saltCSDReason += ( i < selectedReasons.length - 1 ) ? ', ' : ' ' + mw.msg( 'adiutor-and' ) + ' ';
              }
              saltCSDReason += '|[[' + speedyDeletionPolicyPageShortcut + '#' + selectedReasons[ i ].value + ']]';
            }
            csdReason = saltCSDReason + '}}';
          }
          for ( let i = 0; i < selectedReasons.length; i++ ) {
            if ( i > 0 ) {
              saltCSDSummary += ( i < selectedReasons.length - 1 ) ? ', ' : ' ' + mw.msg( 'adiutor-and' ) + ' ';
            }
            saltCSDSummary += '[[' + speedyDeletionPolicyPageShortcut + '#' + selectedReasons[ i ].value + ']]';
          }
          csdSummary = AdiutorUtility.replaceParameter( multipleReasonSummary, '2', saltCSDSummary );
        } else {
          if ( postfixReasonUsage === 'data' ) {
            csdReason = csdTemplateStartSingleReason + selectedReasons[ 0 ].data + ( copyVioInput.value ? copyVioURL : '' ) + '}}';
          } else if ( postfixReasonUsage === 'value' ) {
            csdReason = csdTemplateStartSingleReason + selectedReasons[ 0 ].value + ( copyVioInput.value ? copyVioURL : '' ) + '}}';
          }
          csdSummary = AdiutorUtility.replaceParameter( singleReasonSummary, '2', selectedReasons[ 0 ].data );
        }

        await createApiRequest( csdReason, csdSummary );
        if ( informCreator.value ) {
          try {
            const creatorData = await getCreator();
            const articleAuthor = creatorData.query.pages[ mw.config.get( 'wgArticleId' ) ].revisions[ 0 ].user;
            if ( !mw.util.isIPAddress( articleAuthor ) ) {
              const placeholdersForNotification = {
                $1: mw.config.get( 'wgPageName' ),
                $2: csdSummary,
                $3: csdReason
              };
              const message = AdiutorUtility.replacePlaceholders( csdNotificationTemplate, placeholdersForNotification );
              await notifyAuthor( articleAuthor, message, csdSummary );
            }
          } catch ( error ) {
            handleError( error );
          }
        }
      } else {
        mw.notify( mw.message( 'adiutor-select-speedy-deletion-reason' ).text(), {
          title: mw.msg( 'adiutor-warning' ),
          type: 'error'
        } );
      }
    };

    /**
     * Retrieves the creator of the page.
     * @return {jQuery.Promise} A promise that resolves with the creator's information.
     */
    function getCreator() {
      return api.get( {
        action: 'query',
        prop: 'revisions',
        rvlimit: 1,
        rvprop: [ 'user' ],
        rvdir: 'newer',
        titles: mw.config.get( 'wgPageName' )
      } );
    }

    /**
     * Retrieves deletion logs for a specific page.
     * @async
     * @method getDeletionLogs
     * @return {Promise<void>}
     */
    async function getDeletionLogs() {
      try {
        const response = await api.get( {
          action: 'query',
          list: 'logevents',
          leprop: [ 'type', 'title', 'user', 'timestamp' ],
          letype: 'delete',
          lelimit: 500,
          letitle: pageName
        } );
        deletionLogs.value = response.query.logevents || [];
      } catch ( error ) {
        handleError( error );
      }
    }

    function handleError( error ) {
      mw.notify( error, {
        title: mw.msg( 'adiutor-operation-failed' ),
        type: 'error'
      } );
    }

    onMounted( () => {
      getDeletionLogs();
    } );

    return {
      checkboxValue,
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
