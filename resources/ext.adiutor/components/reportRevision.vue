<template>
  <cdx-dialog
      v-model:open="openDialog"
      :close-button-label="$i18n( 'adiutor-close' )"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-revision-reporting-header-title' )"
      class="revision-reporting-dialog"
      @default="openDialog = false"
      @primary="reportRevision">
    <div class="adiutor-dialog-header">
      <p>{{ $i18n( "adiutor-revision-reporting-warning" ) }}</p>
    </div>
      <cdx-field class="flex-box">
        <template #label>
          {{ $i18n( "adiutor-rationale" ) }}
        </template>
        <cdx-field :is-fieldset="true">
          <cdx-radio
              v-for="rationale in reportRationales"
              :key="'rationale-' + rationale.value"
              v-model="rationaleSelection"
              :input-value="rationale.value"
              name="rationale-group-descriptions"
          >
            {{ rationale.label }}
          </cdx-radio>
        </cdx-field>
        <cdx-field style="margin-top: 10px;">
          <template #label>
            {{ $i18n( "adiutor-comment" ) }}
          </template>
          <cdx-text-area
              v-model="commentInput"
              :placeholder="$i18n( 'adiutor-revision-reporting-comment-placeholder' )">
          </cdx-text-area>
        </cdx-field>
      </cdx-field>
    <template #footer-text>
      <!-- eslint-disable vue/no-v-html -->
      <span v-html="getModulePolicy( $i18n( 'please-read-the-x-policy' ) )"></span>
    </template>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxField, CdxDialog, CdxTextArea, CdxRadio } = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
const revConfiguration = mw.config.get( 'wgAdiutorReportRevision' );
const noticeBoardTitle = revConfiguration.noticeBoardTitle;
const addNewSection = revConfiguration.addNewSection;
const sectionTitle = revConfiguration.sectionTitle;
const useExistSection = revConfiguration.useExistSection;
const sectionId = revConfiguration.sectionId;
const textModificationDirection = revConfiguration.textModificationDirection;
const contentPattern = revConfiguration.contentPattern;
const apiPostSummary = revConfiguration.apiPostSummary;
const pageTitle = mw.config.get( 'wgPageName' ).replace( /_/g, ' ' );
module.exports = defineComponent( {
  name: 'ReportRevision',
  components: {
    CdxDialog,
    CdxField,
    CdxTextArea,
    CdxRadio
  },

  setup() {
    const rationaleSelection = ref( [ '' ] );
    const commentInput = ref( '' );
    const openDialog = ref( true );
    const primaryAction = {
      label: mw.msg( 'adiutor-report' ),
      actionType: 'progressive'
    };

    const defaultAction = {
      label: mw.msg( 'adiutor-cancel' )
    };

    const getModulePolicy = () => {
      const policyLink = '<a href="' + mw.util.getUrl( revConfiguration.policyTitle ) + '" target="_blank">' + mw.msg( 'adiutor-revision-deletion-policy-name-lowercase' ) + '</a>';
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
        const placeholders = {
          $1: pageTitle,
          $2: mw.config.get( 'wgRevisionId' ),
          $3: rationaleSelection.value,
          $4: commentInput.value
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
        openDialog.value = false;
      } catch ( error ) {
        mw.notify( error, {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      }
    };

    const reportRevision = async () => {

      const placeholders = {
        $1: pageTitle,
        $2: mw.config.get( 'wgRevisionId' ),
        $3: rationaleSelection.value,
        $4: commentInput.value
      };
      const preparedContent = AdiutorUtility.replacePlaceholders( contentPattern, placeholders );

      if ( !rationaleSelection.value || !commentInput.value ) {
        mw.notify( 'adiutor-rev-incomplete-request-details', {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      } else {
        await createApiRequest( preparedContent );
      }
    };

    return {
      getModulePolicy,
      openDialog,
      primaryAction,
      defaultAction,
      commentInput,
      rationaleSelection,
      reportRevision
    };
  },
  data() {
    return {
      reportRationales: ref( revConfiguration.reportRationales )
    };
  }
} );
</script>

<style lang="css">
.revision-reporting-dialog {
	max-width: 40em !important;
}

.revision-reporting-dialog .adiutor-dialog-header {
	display: block;
	align-items: baseline;
	justify-content: space-between;
	height: 10em;
	background-image: url( ../../ext.adiutor.images/revision-icon-ltr.svg );
	background-position: 100% -4px;
	background-repeat: no-repeat;
	background-size: 140px;
}

.revision-reporting-dialog .adiutor-dialog-header p {
	width: 70%;
}

/* Media query for mobile view */
@media only screen and ( max-width: 767px ) {
	.revision-reporting-dialog .adiutor-dialog-header {
		background: none; /* Hide background */
		margin-bottom: 10px !important;
	}

	.revision-reporting-dialog .adiutor-dialog-header p {
		width: 100% !important;
	}
}
</style>
