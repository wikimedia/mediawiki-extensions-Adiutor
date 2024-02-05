<template>
  <cdx-dialog
      v-model:open="openPrdDialog"
      :close-button-label="$i18n( 'adiutor-close' )"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-prd-header-title' )"
      class="prd-dialog"
      @default="openRppDialog = false"
      @primary="proposeForDeletion">
    <div class="adiutor-dialog-header">
      <p>{{ $i18n( "adiutor-prd-header-description" ) }}</p>
    </div>
    <cdx-field :is-fieldset="true">
      <template #label>
        <strong>{{ $i18n( "adiutor-prd-deletion-type" ) }}</strong>
      </template>
      <cdx-radio
          v-for="radio in radios"
          :key="'radio-' + radio.value"
          v-model="radioValue"
          :input-value="radio.value"
          name="radio-group-descriptions">
        {{ radio.label }}
        <template #description>
          <!-- eslint-disable vue/no-v-html -->
          <span v-html="radio.description"></span>
        </template>
      </cdx-radio>
    </cdx-field>
    <cdx-field style="margin-top:20px">
      <template #label>
        <strong>{{ $i18n( "adiutor-rationale" ) }}</strong>
      </template>
      <cdx-text-area
          v-model="textareaValue"
          :placeholder="$i18n( 'adiutor-prd-rationale-placeholder' )"></cdx-text-area>
    </cdx-field>
    <template #footer-text>
      <cdx-checkbox v-model="informCreator" :inline="true">
        {{ $i18n( "adiutor-inform-creator" ) }}
      </cdx-checkbox>
    </template>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxCheckbox, CdxField, CdxDialog, CdxTextArea, CdxRadio } = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
const prdConfiguration = mw.config.get( 'wgAdiutorDeletionPropose' );
const {
  standardProposeTemplate,
  livingPersonProposeTemplate,
  apiPostSummaryForCreator,
  prodNotificationTemplate,
  proposedDeletionPolicy,
  proposedDeletionPolicyShortcut,
  proposedDeletionOfBiographiesOfLivingPeoplePolicy,
  proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut,
  apiPostSummary
} = prdConfiguration;

function processDeletionHelpMessage( message, link, shortcut ) {
  const linkHTML = '<a href="' + mw.config.get( 'wgServer' ) + mw.config.get( 'wgArticlePath' ).replace( '$1', '' ) + link + '" target="_blank">' + shortcut + '</a>';
  return message.replace( '$1', linkHTML );
}

module.exports = defineComponent( {
  name: 'ProposeDeletion',
  components: { CdxRadio, CdxDialog, CdxCheckbox, CdxField, CdxTextArea },
  setup() {
    const api = new mw.Api();
    const informCreator = ref( true );
    const textareaValue = ref( '' );
    const openPrdDialog = ref( true );
    const primaryAction = {
      label: mw.msg( 'adiutor-propose' ),
      actionType: 'progressive'
    };
    const defaultAction = {
      label: mw.msg( 'adiutor-cancel' )
    };
    const radioValue = ref( 'default' );
    const radios = [
      {
        label: mw.msg( 'adiutor-prd-deletion-type-1' ),
        description: processDeletionHelpMessage( mw.msg( 'adiutor-prd-deletion-type-1-help' ), proposedDeletionPolicy, proposedDeletionPolicyShortcut ),
        value: 'standardPropose'
      },
      {
        label: mw.msg( 'adiutor-prd-deletion-type-2' ),
        description: processDeletionHelpMessage( mw.msg( 'adiutor-prd-deletion-type-2-help' ), proposedDeletionOfBiographiesOfLivingPeoplePolicy, proposedDeletionOfBiographiesOfLivingPeoplePolicyShortcut ),
        value: 'livingPersonPropose'
      }
    ];

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
     * Sends a deletion request for the specified option and rationale.
     * @param {string} option - The deletion option ('standardPropose' or 'livingPersonPropose').
     * @param {string} rationale - The rationale for the deletion request.
     * @return {Promise<void>} - A promise that resolves when the deletion request is sent successfully.
     */
    const sendDeletionRequest = async ( option, rationale ) => {
      const mwConfig = mw.config.get( [ 'wgArticleId', 'wgPageName', 'wgUserGroups', 'wgUserName', 'wgWikiID', 'wgMonthNames' ] );
      const date = new Date();
      const localMonthsNames = mwConfig.wgMonthNames;
      let PRDText;
      const placeholders = {
        $1: mwConfig.wgPageName.replace( /_/g, ' ' ),
        $2: rationale,
        $3: date.getDate(),
        $4: localMonthsNames[ date.getMonth() + 1 ],
        $5: date.getFullYear(),
        $6: mwConfig.wgUserName
      };
      if ( option === 'standardPropose' ) {
        PRDText = AdiutorUtility.replacePlaceholders( standardProposeTemplate, placeholders );
      } else {
        PRDText = AdiutorUtility.replacePlaceholders( livingPersonProposeTemplate, placeholders );
      }
      try {
        await api.postWithToken( 'csrf', {
          action: 'edit',
          title: mwConfig.wgPageName,
          prependtext: PRDText + '\n',
          summary: AdiutorUtility.replaceParameter( apiPostSummary, '1', mwConfig.wgPageName.replace( /_/g, ' ' ) ),
          format: 'json'
        } );
        mw.notify( mw.message( 'adiutor-page-proposed-for-deletion' ).text(), {
          title: mw.msg( 'adiutor-operation-completed' ),
          type: 'success'
        } );
        location.reload();
      } catch ( error ) {
        AdiutorUtility.handleError( error );
      }
    };

    /**
     * Sends a message to the author of a page.
     * @param {string} Author - The username of the author.
     * @param {string} message - The message to be sent.
     * @return {Promise<void>} - A promise that resolves when the message is sent successfully.
     */
    const sendMessageToAuthor = async ( Author, message ) => {
      try {
        await api.postWithToken( 'csrf', {
          action: 'edit',
          title: 'User_talk:' + Author,
          appendtext: '\n' + message,
          summary: AdiutorUtility.replaceParameter( apiPostSummaryForCreator, '1', mw.config.get( 'wgPageName' ).replace( /_/g, ' ' ) ),
          tags: 'adiutor',
          format: 'json'
        } );
      } catch ( error ) {
        AdiutorUtility.handleError( error );
      }
    };

    /**
     * Function to propose deletion.
     *
     * @async
     * @method proposeForDeletion
     * @return {Promise<void>} - A promise that resolves when the deletion request is sent.
     */
    const proposeForDeletion = async () => {
      const selectedRadioOption = radioValue.value;
      const rationaleText = textareaValue.value;
      await sendDeletionRequest( selectedRadioOption, rationaleText );

      if ( informCreator.value ) {
        try {
          const creatorData = await getCreator();
          const author = creatorData.query.pages[ mw.config.get( 'wgArticleId' ) ].revisions[ 0 ].user;

          if ( !mw.util.isIPAddress( author ) ) {
            const pageTitle = mw.config.get( 'wgPageName' ).replace( /_/g, ' ' );
            const notificationMessage = AdiutorUtility.replaceParameter( prodNotificationTemplate, '1', pageTitle );
            await sendMessageToAuthor( author, notificationMessage );
          }
        } catch ( error ) {

        }
      }

      openPrdDialog.value = false;
    };

    return {
      openPrdDialog,
      textareaValue,
      primaryAction,
      defaultAction,
      informCreator,
      radioValue,
      radios,
      proposeForDeletion
    };
  }
} );
</script>

<style lang="css">

.prd-dialog .adiutor-dialog-header {
  display: block;
  align-items: baseline;
  justify-content: space-between;
  height: 10em;
  background-image: url(../../ext.adiutor.images/prod-icon-ltr.svg);
  background-position: 100% -4px;
  background-repeat: no-repeat;
  background-size: 140px;
}

.prd-dialog .adiutor-dialog-header p {
  width: 70%;
}

</style>
