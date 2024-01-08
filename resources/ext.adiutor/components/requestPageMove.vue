<template>
  <cdx-dialog
      v-model:open="openRpmDialog"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-pmr-header-title' )"
      class="rpm-dialog"
      close-button-label="Close"
      @default="openRpmDialog = true"
      @primary="requestPageMove">
    <div class="header">
      <p>{{ $i18n( 'adiutor-pmr-header-description' ) }}</p>
    </div>
    <cdx-field class="rpm-dialog-body">
      <cdx-label class="rpm-label">
        <strong>{{ $i18n( 'adiutor-new-name' ) }}</strong>
      </cdx-label>
      <cdx-text-input
          v-model="newPageName"
          :placeholder="$i18n( 'adiutor-new-name-placeholder' )"
          aria-label="New page name"></cdx-text-input>
      <cdx-label class="rpm-label">
        <strong>{{ $i18n( 'adiutor-rationale' ) }}</strong>
      </cdx-label>
      <cdx-text-area
          v-model="rationaleInput"
          :placeholder="$i18n( 'adiutor-rpm-rationale-placeholder' )"></cdx-text-area>
    </cdx-field>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxField, CdxDialog, CdxLabel, CdxTextInput, CdxTextArea } = require( '@wikimedia/codex' );
const rpmConfiguration = mw.config.get( 'wgAdiutorRequestPageMove' );
const noticeBoardTitle = rpmConfiguration.noticeBoardTitle;
const noticeBoardLink = noticeBoardTitle.replace( / /g, '_' );
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
    CdxLabel,
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

    /**
     * Replaces placeholders in the input string with the corresponding replacements.
     *
     * @param {string} input - The input string with placeholders.
     * @param {Object} replacements - An object containing the replacements for the placeholders.
     * @return {string} - The input string with the placeholders replaced.
     */
    const replacePlaceholders = ( input, replacements ) => {
      return input.replace( /\$(\d+)/g, function ( match, group ) {
        const replacement = replacements[ '$' + group ];
        return replacement !== undefined ? replacement : match;
      } );
    };

    function replaceParameter( input, parameterName, newValue ) {
      // eslint-disable-next-line security/detect-non-literal-regexp
      const regex = new RegExp( '\\$' + parameterName, 'g' );
      if ( input.includes( '$' + parameterName ) ) {
        return input.replace( regex, newValue );
      } else {
        return input;
      }
    }

    const createApiRequest = async ( preparedContent ) => {
      const api = new mw.Api();
      const apiParams = {
        action: 'edit',
        title: noticeBoardTitle,
        summary: replaceParameter( apiPostSummary, '1', pageTitle ),
        tags: 'adiutor',
        format: 'json'
      };

      if ( addNewSection ) {
        apiParams.section = 'new';
        apiParams.sectiontitle = replaceParameter( sectionTitle, '1', pageTitle );
        apiParams.text = preparedContent;
      } else {
        if ( useExistSection ) {
          apiParams.section = sectionId;
        }
        apiParams[ textModificationDirection === 'appendtext' ? 'appendtext' : textModificationDirection === 'prependtext' ? 'prependtext' : 'text' ] = preparedContent + '\n';
      }

      api.postWithToken( 'csrf', apiParams ).done( function () {
        window.location = '/wiki/' + noticeBoardLink;
        openRpmDialog.value = false;
      } );
    };

    const requestPageMove = async () => {

      const placeholders = {
        $1: pageTitle,
        $2: newPageName.value,
        $3: rationaleInput.value
      };
      const preparedContent = replacePlaceholders( contentPattern, placeholders );

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
      rationaleInput,
      newPageName,
      requestPageMove
    };
  }
} );
</script>

<style lang="css">
.rpm-dialog {

  max-width: 29.571429em;

}

.rpm-dialog .cdx-dialog {
  max-width: 448px;
  padding-top: 10px;
  padding-bottom: 0;
}

.rpm-dialog .cdx-field__control {
  display: grid;
}

.rpm-dialog-body {
  padding: 20px;
  display: grid;
  width: inherit;
}

.rpm-dialog .cdx-dialog__body {
  flex-grow: 1;
  margin-top: 0;
  overflow-y: auto;
  padding: 0 0 20px;
}

.rpm-dialog .cdx-dialog--dividers .cdx-dialog__body {
  padding-top: 0;
}

.rpm-dialog .rpm-reason-field {
  display: flex;
  flex-direction: column;
  width: 50%;
}

.rpm-dialog .cdx-dialog__header {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-sizing: border-box;
  width: 100%;
  padding: 10px 20px 10px;
  font-weight: 700;
}

.rpm-label {
  margin-top: 10px;
}

.rpm-dialog .header {
  background-color: #eaf3ff;
  display: block;
  align-items: baseline;
  justify-content: space-between;
  height: 9em;
  padding: 20px;
  background-image: url(../../ext.adiutor.images/rpm-background.png);
  background-position: right -30px;
  background-repeat: no-repeat;
  background-size: 200px;
}

.rpm-dialog .cdx-dialog__footer {
  padding: 20px !important;
  border-top: 1px solid #a2a9b1;
}

.rpm-dialog .header p {
  width: 60%;
}

.rpm-dialog h2 {
  margin: 0;
  padding: 0;
  font-size: 1.125em !important
}

.rpm-dialog .cdx-select-vue {
  margin-bottom: 10px !important;
}</style>
