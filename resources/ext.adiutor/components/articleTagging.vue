<template>
  <cdx-dialog
      v-model:open="openTagDialog"
      :close-button-label="$i18n( 'adiutor-close' )"
      :default-action="defaultAction"
      :primary-action="primaryAction"
      :show-dividers="true"
      :title="$i18n( 'adiutor-tag-header-title' )"
      class="adiutor-article-tagging-dialog"
      @default="openTagDialog = false"
      @primary="tagArticle">
    <div class="adiutor-dialog-header">
      <p>{{ $i18n( "adiutor-tag-header-description" ) }}</p>
      <cdx-text-input
          v-model="searchTag"
          :aria-label="$i18n( 'adiutor-search' )"
          :clearable="true"
          :end-icon="cdxIconInfoFilled"
          :placeholder="$i18n( 'adiutor-search-tag-placeholder' )"
          :start-icon="cdxIconSearch"
          class="tag-search"
          input-type="search"></cdx-text-input>
    </div>
    <cdx-field
        v-for="label in filteredTagList"
        :key="'fieldset-' + label.label"
        :is-fieldset="true"
        style="margin-bottom: 10px">
      <template v-if="label.tags.length > 0" #label>
        <strong>{{ label.label }}</strong>
      </template>
      <cdx-field
          v-for="tag in label.tags"
          :key="'fieldset-' + tag.tag"
          :is-fieldset="true">
        <cdx-checkbox
            :key="'checkbox-' + tag.tag"
            v-model="checkboxValue"
            :input-value="tag.tag"
            @change="toggleTag( tag )">
          {{ tag.description }}
        </cdx-checkbox>
        <template v-if="tag.items && tag.items.length > 0 && checkboxValue.includes( tag.tag )">
          <template v-for="subItem in tag.items">
            <template v-if="subItem.type === 'input'">
              <cdx-text-input
                  :key="'text-input-' + subItem.name"
                  v-model="subItem.value"
                  :aria-label="subItem.label"
                  :name="subItem.name"
                  :placeholder="subItem.label"
                  class="sub-item-text-input">
              </cdx-text-input>
            </template>
            <template v-else-if="subItem.type === 'checkbox'">
              <cdx-checkbox
                  :key="'checkbox-' + subItem.name"
                  v-model="subItem.value"
                  class="sub-item-checkbox">
                {{ subItem.label }}
              </cdx-checkbox>
            </template>
          </template>
        </template>
      </cdx-field>
    </cdx-field>
    <cdx-message
        v-if="!filteredTagList.length"
        inline
        type="warning">
      <p>{{ $i18n( "adiutor-no-tag-found" ) }}</p>
    </cdx-message>
  </cdx-dialog>
</template>

<script>
const { defineComponent, ref, computed, onMounted } = require( 'vue' );
const { CdxCheckbox, CdxField, CdxDialog, CdxTextInput, CdxMessage } = require( '../../codex.js' );
const { cdxIconSearch, cdxIconInfoFilled } = require( '../icons.json' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
module.exports = defineComponent( {
  name: 'ArticleTagging',
  components: {
    CdxDialog,
    CdxCheckbox,
    CdxField,
    CdxTextInput,
    CdxMessage
  },
  setup() {
    const checkboxValue = ref( [ '' ] );
    const openTagDialog = ref( true );
    const tagConfiguration = mw.config.get( 'wgAdiutorArticleTagging' );
    const tagList = tagConfiguration.tagList;
    const useMultipleIssuesTemplate = tagConfiguration.useMultipleIssuesTemplate;
    const multipleIssuesTemplate = tagConfiguration.multipleIssuesTemplate;
    const uncategorizedTemplate = tagConfiguration.uncategorizedTemplate;
    const apiPostSummary = tagConfiguration.apiPostSummary;
    const searchTag = ref( '' );
    const pageHasTags = ref( false );
    const pageName = mw.config.get( 'wgPageName' );

    const defaultAction = {
      label: mw.msg( 'adiutor-cancel' )
    };

    const primaryAction = computed( () => {
      return {
        icon: 'cdxIconTag',
        label: pageHasTags.value ? mw.msg( 'adiutor-update' ) : mw.msg( 'adiutor-tag-page' ),
        actionType: 'progressive'
      };
    } );

    const filteredTagList = computed( () => {
      const searchTerm = searchTag.value.toLowerCase();
      return tagList.map( ( label ) => {
        const filteredTags = label.tags
            .filter( ( tag ) =>
                tag.tag.toLowerCase().includes( searchTerm ) ||
                tag.description.toLowerCase().includes( searchTerm )
            )
            .map( ( tag ) => Object.assign( {}, tag ) );
        return Object.assign( {}, label, { tags: filteredTags } );
      } ).filter( ( label ) => label.tags.length > 0 );
    } );

    const selectedTags = [];

    function toggleTag( tag ) {
      const tagIndex = selectedTags.findIndex( ( selectedTag ) => selectedTag.tag === tag.tag );
      if ( tagIndex === -1 ) {
        selectedTags.push( tag );
      } else {
        selectedTags.splice( tagIndex, 1 );
      }
    }

    const createApiRequest = async ( preparedTagsString ) => {
      const api = new mw.Api();
      const editParams = {
        action: 'edit',
        title: mw.config.get( 'wgPageName' ),
        summary: apiPostSummary,
        tags: 'adiutor',
        format: 'json'
      };
      let removedContent = '';
      const modifiedTags = preparedTagsString.replace( '{{' + uncategorizedTemplate + '}}', function ( match ) {
        removedContent = match;
        return '';
      } );
      if ( removedContent ) {
        editParams.prependtext = modifiedTags.split( ',' ).join( '\n' ) + '\n';
        editParams.appendtext = '\n' + removedContent;
      } else {
        editParams.prependtext = modifiedTags.split( ',' ).join( '\n' ) + '\n';
      }
      try {
        await api.postWithToken( 'csrf', editParams );
        openTagDialog.value = false;
        location.reload();
      } catch ( error ) {
        mw.notify( mw.msg( 'adiutor-tag-failed' ), {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      }
    };

    const updateApiRequest = async ( updatedPageContent ) => {
      const api = new mw.Api();
      const editParams = {
        action: 'edit',
        title: mw.config.get( 'wgPageName' ),
        summary: apiPostSummary,
        tags: 'adiutor',
        format: 'json'
      };
      editParams.text = updatedPageContent;
      try {
        await api.postWithToken( 'csrf', editParams );
        openTagDialog.value = false;
        location.reload();
      } catch ( error ) {
        mw.notify( mw.msg( 'adiutor-tag-failed' ), {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      }
    };

    const tagArticle = async () => {
      const preparedTemplates = [];
      const templateInfo = {};
      let preparedTagsString;
      let updatedPageContent;
      selectedTags.forEach( function ( tag ) {
        if ( tag.items && tag.items.length > 0 ) {
          tag.items.forEach( function ( subItem ) {
            if ( tag.tag ) {
              let template = `{{${ tag.tag }`;
              if ( subItem.parameter ) {
                if ( !templateInfo[ tag.tag ] ) {
                  templateInfo[ tag.tag ] = {};
                }
                if ( !templateInfo[ tag.tag ][ subItem.parameter ] ) {
                  templateInfo[ tag.tag ][ subItem.parameter ] = getInputValue( subItem.name );
                }
                template += `|${ subItem.parameter }=${ templateInfo[ tag.tag ][ subItem.parameter ] }`;
              }
              template += '}}';
              preparedTemplates.push( template );
            }
          } );
        } else {
          if ( tag.tag ) {
            preparedTemplates.push( `{{${ tag.tag }}}` );
          }
        }
      } );

      if ( useMultipleIssuesTemplate && preparedTemplates.length > 0 ) {
        const pageContent = await AdiutorUtility.getPageContent( pageName );
        // eslint-disable-next-line es-x/no-regexp-s-flag,security/detect-non-literal-regexp
        const matches = pageContent.match( new RegExp( `{{${ multipleIssuesTemplate }\\s*\\|\\s*((?:{{[^{}]*}}(?:\\n|\\s*))*?)}}`, 's' ) );
        let currentTagsContent = matches ? matches[ 1 ] : '';
        let currentTags = currentTagsContent.match( /{{[\s\S]*?}}/g ) || [];
        currentTags = currentTags.map( ( tag ) => tag.trim() );
        const preparedTagNames = preparedTemplates.map( ( tag ) => {
          const match = tag.match( /{{([\s\S]*?)}}/ );
          return match ? match[ 1 ].trim() : '';
        } );

        // Add new tags
        preparedTagNames.forEach( ( tagName ) => {
          if ( !currentTags.includes( `{{${ tagName }}}` ) ) {
            currentTagsContent += `{{${ tagName }}}\n`;
          }
        } );

        currentTags.forEach( ( tag ) => {
          if ( !preparedTagNames.map( ( tn ) => `{{${ tn }}}` ).includes( tag ) ) {
            currentTagsContent = currentTagsContent.replace( tag + '\n', '' );
          }
        } );

        currentTagsContent = currentTagsContent.trim();

        preparedTagsString = `{{${ multipleIssuesTemplate }|\n${ currentTagsContent }\n}}`;
        updatedPageContent = matches ? pageContent.replace( matches[ 0 ], preparedTagsString ) : `${ preparedTagsString }\n${ pageContent.trim() }`;
      } else {
        const pageContent = await AdiutorUtility.getPageContent( pageName );
        if ( typeof pageContent === 'string' ) {
          const contentWithoutTags = pageContent.replace( /{{[^{}]*}}/g, '' );
          updatedPageContent = preparedTemplates.join( '\n' ) + '\n\n' + contentWithoutTags.trim();
        } else {
          AdiutorUtility.handleError( 'The content of the page is not a string.' );
        }
      }

      if ( !selectedTags.length > 0 ) {
        mw.notify( mw.msg( 'adiutor-please-select-a-tag' ), {
          title: mw.msg( 'adiutor-operation-failed' ),
          type: 'error'
        } );
      } else {
        if ( !pageHasTags.value ) {
          await createApiRequest( preparedTagsString );
        } else {
          await updateApiRequest( updatedPageContent );
        }
      }
    };

    function getInputValue( inputName ) {
      const inputElement = document.querySelector( 'input[name="' + inputName + '"]' );
      if ( inputElement ) {
        return inputElement.value;
      } else {
        return '';
      }
    }

    onMounted( async () => {
      try {
        const parsedContent = await AdiutorUtility.getParseTree( pageName );
        const templatesAndParams = AdiutorUtility.extractTemplatesFromParsedContent( parsedContent );
        const templateNamesOnPage = [];
        templatesAndParams.forEach( ( templateObj ) => {
          tagList.forEach( ( labelObj ) => {
            labelObj.tags.forEach( ( tagObj ) => {
              if ( templateObj.name.toLowerCase() === tagObj.tag.toLowerCase() ) {
                toggleTag( tagObj );
                templateNamesOnPage.push( templateObj.name );
                if ( tagObj.items ) {
                  tagObj.items.forEach( ( item ) => {
                    if ( item.parameter && templateObj.parameters[ item.parameter ] ) {
                      item.value = templateObj.parameters[ item.parameter ];
                    }
                  } );
                }
              }
            } );
          } );
        } );
        checkboxValue.value = templateNamesOnPage;
      } catch ( error ) {
        AdiutorUtility.handleError( error );
      }
      if ( checkboxValue.value.length > 0 ) {
        pageHasTags.value = true;
      }
    } );

    return {
      checkboxValue,
      openTagDialog,
      defaultAction,
      primaryAction,
      searchTag,
      toggleTag,
      filteredTagList,
      cdxIconSearch,
      cdxIconInfoFilled,
      tagArticle
    };
  }
} );
</script>

<style lang="css">
.adiutor-article-tagging-dialog {
  max-width: 720px !important;
  flex-grow: 1;
  margin-top: 0;
  padding: 0;
  overflow-y: auto;
}

.adiutor-article-tagging-dialog .tag-search {
  width: 200px
}

.adiutor-article-tagging-dialog .adiutor-dialog-header {
  display: block;
  align-items: baseline;
  justify-content: space-between;
  height: 10em;
  background-image: url(../../ext.adiutor.images/tag-background.png);
  background-position: 100% 0;
  background-repeat: no-repeat;
  background-size: 170px;
  border-bottom: 1px solid #dedede;
  margin-bottom: 20px;
}

.adiutor-article-tagging-dialog .adiutor-dialog-header p {
  width: 70%;
}

.adiutor-article-tagging-dialog .sub-item-text-input {
  margin-left: 20px;
  margin-bottom: 10px;
}

.adiutor-article-tagging-dialog .sub-item-checkbox {
  margin-left: 20px;
}
</style>
