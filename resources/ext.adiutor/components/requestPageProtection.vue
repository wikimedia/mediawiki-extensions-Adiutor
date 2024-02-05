<template>
  <cdx-dialog
      v-model:open="openRppDialog"
      :close-button-label="$i18n( 'adiutor-close' )"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-rpp-header-title' )"
      class="rpp-dialog"
      @default="openRppDialog = false"
      @primary="requestPageProtection">
    <div class="adiutor-dialog-header">
      <p>{{ $i18n( "adiutor-rpp-header-description" ) }}</p>
    </div>
      <cdx-field class="flex-box" is-fieldset="true">
        <template #label>
          <strong>{{ $i18n( "adiutor-protection-type" ) }}</strong>
        </template>
        <cdx-select
            v-model:selected="durationSelection"
            :menu-items="protectionDurations"
            default-label="Choose duration"></cdx-select>
        <cdx-select
            v-model:selected="typeSelection"
            :menu-items="protectionTypes"
            default-label="Select protection type"></cdx-select>
      </cdx-field>
      <cdx-field>
        <template #label>
          <strong>{{ $i18n( "adiutor-rationale" ) }}</strong>
        </template>
        <cdx-text-area
            v-model="rationaleInput"
            :placeholder="$i18n( 'adiutor-rpp-rationale-placeholder' )"></cdx-text-area>
      </cdx-field>
    <template #footer-text>
      <!-- eslint-disable vue/no-v-html -->
      <span v-html="getModulePolicy( $i18n( 'please-read-the-x-policy' ) )"></span>
    </template>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxField, CdxDialog, CdxTextArea, CdxSelect } = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
const rppConfiguration = mw.config.get( 'wgAdiutorRequestPageProtection' );
const noticeBoardTitle = rppConfiguration.noticeBoardTitle;
const addNewSection = rppConfiguration.addNewSection;
const sectionTitle = rppConfiguration.sectionTitle;
const useExistSection = rppConfiguration.useExistSection;
const sectionId = rppConfiguration.sectionId;
const textModificationDirection = rppConfiguration.textModificationDirection;
const contentPattern = rppConfiguration.contentPattern;
const apiPostSummary = rppConfiguration.apiPostSummary;
const pageTitle = mw.config.get( 'wgPageName' ).replace( /_/g, ' ' );
module.exports = defineComponent( {
  name: 'RequestPageProtection',
  components: {
    CdxDialog,
    CdxField,
    CdxTextArea,
    CdxSelect
  },

  setup() {
    const durationSelection = ref( '' );
    const typeSelection = ref( '' );
    const rationaleInput = ref( '' );
    const openRppDialog = ref( true );
    const primaryAction = {
      label: mw.msg( 'adiutor-request' ),
      actionType: 'progressive'
    };

    const defaultAction = {
      label: mw.msg( 'adiutor-cancel' )
    };

    const getModulePolicy = () => {
      const policyLink = '<a href="' + mw.util.getUrl( rppConfiguration.policyTitle ) + '" target="_blank">' + mw.msg( 'adiutor-protection-policy-name-lowercase' ) + '</a>';
      return mw.msg( 'adiutor-please-read-the-x-policy' ).replace( '$1', policyLink );
    };

    const createApiRequest = async ( preparedContent ) => {
      const api = new mw.Api();
      const apiParams = {
        action: 'edit',
        title: noticeBoardTitle,
        summary: AdiutorUtility.replaceParameter( apiPostSummary, '1', pageTitle ),
        tags: 'adiutor',
        format: 'json'
      };

      if ( addNewSection ) {
        apiParams.section = 'new';
        apiParams.sectiontitle = AdiutorUtility.replaceParameter( sectionTitle, '1', pageTitle );
        apiParams.text = preparedContent;
      } else {
        if ( useExistSection ) {
          apiParams.section = sectionId;
        }
        apiParams[ textModificationDirection === 'appendtext' ? 'appendtext' : textModificationDirection === 'prependtext' ? 'prependtext' : 'text' ] = preparedContent + '\n';
      }
      try {
        await api.postWithToken( 'csrf', apiParams );
        window.location = mw.util.getUrl( noticeBoardTitle );
        openRppDialog.value = false;
      } catch ( error ) {
        mw.notify( error, {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      }
    };

    const requestPageProtection = async () => {

      const placeholders = {
        $1: pageTitle,
        $2: durationSelection.value,
        $3: typeSelection.value,
        $4: rationaleInput.value
      };
      const preparedContent = AdiutorUtility.replacePlaceholders( contentPattern, placeholders );

      if ( !durationSelection.value || !typeSelection.value || !rationaleInput.value ) {
        mw.notify( 'adiutor-rpp-incomplete-request-details', {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      } else {
        await createApiRequest( preparedContent );
      }
    };

    return {
      getModulePolicy,
      openRppDialog,
      primaryAction,
      defaultAction,
      rationaleInput,
      durationSelection,
      typeSelection,
      requestPageProtection
    };
  },
  data() {
    return {
      protectionDurations: ref( rppConfiguration.protectionDurations ),
      protectionTypes: ref( rppConfiguration.protectionTypes )
    };
  }
} );
</script>

<style lang="css">
.rpp-dialog {
  max-width: 32em;
}

.rpp-dialog .cdx-field__control {
  display: grid;
}

.rpp-dialog cdx-label {
  margin-bottom: 10px;
  display: block;
  margin-top: 10px;
}

.rpp-dialog .adiutor-dialog-header {
  display: block;
  align-items: baseline;
  justify-content: space-between;
  height: 10em;
  background-image: url(../../ext.adiutor.images/rpp-background.png);
  background-position: 100% 0;
  background-repeat: no-repeat;
  background-size: 165px;
  border-bottom: 1px solid #dedede;
  margin-bottom: 20px;
}

.rpp-dialog .adiutor-dialog-header p {
  width: 60%;
}

.rpp-dialog .cdx-select-vue {
  margin-bottom: 10px !important;
}
</style>
