<template>
  <cdx-dialog
      v-model:open="openDialog"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-revision-reporting-header-title' )"
      class="revision-reporting-dialog"
      close-button-label="Close"
      @default="openDialog = false"
      @primary="reportRevision">
    <div class="header">
      <p>{{ $i18n( "adiutor-revision-reporting-warning" ) }}</p>
    </div>
    <cdx-field class="revision-reporting-dialog-body">
      <cdx-field class="flex-box">
        <cdx-label class="adt-label">
          {{ $i18n( "adiutor-rationale" ) }}
        </cdx-label>
        <cdx-field :is-fieldset="true">
          <cdx-radio
              v-for="rationale in reportRationales"
              :key="'rationale-' + rationale.value"
              v-model="rationaleSelection"
              name="rationale-group-descriptions"
              :input-value="rationale.value"
          >
            {{ rationale.label }}
          </cdx-radio>
        </cdx-field>
        <cdx-field :is-fieldset="true">
          <cdx-label>{{ $i18n( "adiutor-comment" ) }}</cdx-label>
          <cdx-text-area
              v-model="commentInput"
              :placeholder="$i18n( 'adiutor-revision-reporting-comment-placeholder' )">
          </cdx-text-area>
        </cdx-field>
      </cdx-field>
    </cdx-field>
    <template #footer-text>
      <span v-html="getModulePolicy( $i18n( 'please-read-the-x-policy' ) )"></span>
    </template>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxField, CdxDialog, CdxLabel, CdxTextArea, CdxRadio } = require( '@wikimedia/codex' );
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
    CdxLabel,
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
  max-width: 40em;
}

.revision-reporting-dialog .cdx-dialog {
  max-width: 40em;
  padding-top: 10px;
  padding-bottom: 0;
}

.revision-reporting-dialog .cdx-field__control {
  display: grid;
}

.revision-reporting-dialog-body {
  padding: 20px;
  display: grid;
  width: inherit;
}

.revision-reporting-dialog .cdx-dialog__body {
  flex-grow: 1;
  margin-top: 0;
  overflow-y: auto;
  padding: 0 0 20px;
}

.revision-reporting-dialog .cdx-dialog--dividers .cdx-dialog__body {
  padding-top: 0;
}

.revision-reporting-dialog .rpp-reason-field {
  display: flex;
  flex-direction: column;
  width: 50%;
}

.revision-reporting-dialog .cdx-dialog__header {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-sizing: border-box;
  width: 100%;
  padding: 10px 20px 10px;
  font-weight: 700;
}

.revision-reporting-dialog cdx-label {
  margin-bottom: 10px;
  display: block;
  margin-top: 10px;
}

.revision-reporting-dialog .header {
  background-color: #eaf3ff;
  display: block;
  align-items: baseline;
  justify-content: space-between;
  height: 9em;
  padding: 20px;
  background-image: url(../../ext.adiutor.images/revision-reporting-background.svg);
  background-position: right 15px;
  background-repeat: no-repeat;
  background-size: 170px;
}

.revision-reporting-dialog .header p {
  width: 70%;
}

.revision-reporting-dialog h2 {
  margin: 0;
  padding: 0;
  font-size: 1.125em !important
}

.revision-reporting-dialog .cdx-select-vue {
  margin-bottom: 10px !important;
}
</style>
