<template>
  <cdx-dialog
      v-model:open="openRpmDialog"
      :close-button-label="$i18n( 'adiutor-close' )"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-pmr-header-title' )"
      class="rpm-dialog"
      @default="openRpmDialog = false"
      @primary="requestPageMove">
    <div class="adiutor-dialog-header">
      <p>{{ $i18n( "adiutor-pmr-header-description" ) }}</p>
    </div>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-new-name" ) }}</strong>
      </template>
      <cdx-text-input
          v-model="newPageName"
          :placeholder="$i18n( 'adiutor-new-name-placeholder' )"
      ></cdx-text-input>
    </cdx-field>
    <cdx-field style="margin-top: 10px;">
      <template #label>
        <strong>{{ $i18n( "adiutor-rationale" ) }}</strong>
      </template>
      <cdx-text-area
          v-model="rationaleInput"
          :placeholder="$i18n( 'adiutor-rpm-rationale-placeholder' )"></cdx-text-area>
    </cdx-field>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxField, CdxDialog, CdxTextInput, CdxTextArea } = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
const rpmConfiguration = mw.config.get( 'wgAdiutorRequestPageMove' );
const noticeBoardTitle = rpmConfiguration.noticeBoardTitle;
const addNewSection = rpmConfiguration.addNewSection;
const sectionTitle = rpmConfiguration.sectionTitle;
const useExistSection = rpmConfiguration.useExistSection;
const sectionId = rpmConfiguration.sectionId;
const textModificationDirection = rpmConfiguration.textModificationDirection;
const contentPattern = rpmConfiguration.contentPattern;
const apiPostSummary = rpmConfiguration.apiPostSummary;
const pageTitle = mw.config.get( 'wgPageName' ).replace( /_/g, ' ' );
module.exports = defineComponent( {
  name: 'RequestPageMove',
  components: {
    CdxDialog,
    CdxField,
    CdxTextInput,
    CdxTextArea
  },

  setup() {
    const rationaleInput = ref( '' );
    const newPageName = ref( '' );
    const openRpmDialog = ref( true );

    const primaryAction = {
      label: mw.msg( 'adiutor-create-request' ),
      actionType: 'progressive'
    };

    const defaultAction = {
      label: mw.msg( 'adiutor-cancel' )
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
        const placeholders = {
          $1: pageTitle,
          $2: newPageName.value,
          $3: rationaleInput.value
        };
        apiParams.sectiontitle = AdiutorUtility.replacePlaceholders( sectionTitle, placeholders );
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
        openRpmDialog.value = false;
      } catch ( error ) {
        mw.notify( 'adiutor-rpm-failed', {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      }
    };

    const requestPageMove = async () => {

      const placeholders = {
        $1: pageTitle,
        $2: newPageName.value,
        $3: rationaleInput.value
      };
      const preparedContent = AdiutorUtility.replacePlaceholders( contentPattern, placeholders );

      if ( !newPageName.value || !rationaleInput.value ) {
        mw.notify( 'adiutor-rpm-incomplete-request-details', {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      } else {
        await createApiRequest( preparedContent );
      }
    };

    return {
      openRpmDialog,
      primaryAction,
      defaultAction,
      rationaleInput,
      newPageName,
      requestPageMove
    };
  }
} );
</script>

<style lang="css">
.rpm-dialog .adiutor-dialog-header {
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 10em;
	background-image: url( ../../ext.adiutor.images/move-icon-ltr.svg );
	background-position: 100% -4px;
	background-repeat: no-repeat;
	background-size: 140px;
}

.rpm-dialog .adiutor-dialog-header p {
	width: 60%;
}

/* Media query for mobile view */
@media only screen and ( max-width: 767px ) {
	.rpm-dialog .adiutor-dialog-header {
		background: none; /* Hide background */
		margin-bottom: 10px !important;
	}

	.rpm-dialog .adiutor-dialog-header p {
		width: 100% !important;
	}
}
</style>
