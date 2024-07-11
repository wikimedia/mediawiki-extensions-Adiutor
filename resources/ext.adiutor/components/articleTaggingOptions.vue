<template>
  <cdx-field class="top-message">
    <cdx-button
        :action="saveButtonAction"
        :class="saveButtonClass"
        :disabled="saveButtonDisabled"
        :weight="saveButtonWeight"
        @click="saveConfiguration">
      {{ saveButtonLabel }}
    </cdx-button>
    <h3 style="display: initial;">
      {{ $i18n( "adiutor-article-tagging-configuration-title" ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( "adiutor-article-tagging-description" ) }}
    </p>
  </cdx-field>
  <cdx-field>
    <cdx-toggle-switch v-model="moduleEnabled" :align-switch="true">
      {{ $i18n( "adiutor-module-enabled" ) }}
      <template #description>
        {{ $i18n( "adiutor-module-enabled-description" ) }}
      </template>
    </cdx-toggle-switch>
    <cdx-toggle-switch v-model="testMode" :align-switch="true">
      {{ $i18n( "adiutor-test-mode" ) }}
      <template #description>
        {{ $i18n( "adiutor-test-mode-description" ) }}
      </template>
    </cdx-toggle-switch>
    <cdx-field>
      <cdx-chip-input
          v-model:input-chips="namespaces"
          :remove-button-label="$i18n( 'adiutor-remove' )"></cdx-chip-input>
      <template #label>
        {{ $i18n( "adiutor-namespaces" ) }}
      </template>
      <template #description>
        {{ $i18n( "adiutor-namespaces-description" ) }}
      </template>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-toggle-switch v-model="useMultipleIssuesTemplate" :align-switch="true">
        {{ $i18n( "adiutor-use-multiple-issues-template-label" ) }}
        <template #description>
          {{ $i18n( "adiutor-multiple-issues-template-description" ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field v-if="useMultipleIssuesTemplate">
      <template #label>
        <strong>{{ $i18n( "adiutor-multiple-issues-template-label" ) }}</strong>
      </template>
      <cdx-text-input
          id="multipleIssuesTemplate"
          v-model="multipleIssuesTemplate"></cdx-text-input>
      <template #help-text>
        {{ $i18n( "adiutor-multiple-issues-template-help-text" ) }}
        <h5>{{ $i18n( "adiutor-output" ) }}</h5>
        <pre>{{ generateTemplateOutput() }}</pre>
      </template>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <template #label>
        <strong>{{ $i18n( "adiutor-api-post-summary-label" ) }}</strong>
      </template>
      <cdx-text-input
          id="apiPostSummary"
          v-model="apiPostSummary">
      </cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <table id="adiutor-options-props" class="adiutor-article-tagging-options">
      <caption>
        {{ $i18n( "adiutor-tag-labels" ) }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewTagLabel">
          {{ $i18n( "adiutor-add-new-tag-label" ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( "adiutor-label-column" ) }}</th>
        <th style="text-align: right;">
          {{ $i18n( "adiutor-action" ) }}
        </th>
      </tr>
      <tr v-for="( label, index ) in tagList" :key="index">
        <td>
          <cdx-text-input v-model="label.label" :aria-label="$i18n( 'adiutor-namespace-value' )"></cdx-text-input>
        </td>
        <td style="text-align: right;">
          <cdx-button action="destructive" @click="deleteLabel( label )">
            {{ $i18n( "adiutor-delete-button" ) }}
          </cdx-button>
        </td>
      </tr>
    </table>
  </cdx-field>
  <cdx-field v-for="( label, labelIndex ) in tagList" :key="'nestedTable-' + labelIndex">
    <table id="adiutor-options-props">
      <caption>
        {{ $i18n( "adiutor-tags-for" ) }} {{ label.label }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewTag( labelIndex )">
          {{ $i18n( "adiutor-add-new-tag-label" ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( "adiutor-tag-template" ) }}</th>
        <th>{{ $i18n( "adiutor-tag-description" ) }}</th>
        <th style="text-align: right;">
          {{ $i18n( "adiutor-action" ) }}
        </th>
      </tr>
      <tr v-for="( tag, tagIndex ) in label.tags" :key="'tag-' + tagIndex">
        <td>
          <cdx-text-input
              v-model="tag.tag"
              :aria-label="$i18n( 'adiutor-value' )"
              style="min-width: 50px;"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="tag.description" :aria-label="$i18n( 'adiutor-data' )"></cdx-text-input>
        </td>
        <td style="text-align: right;">
          <cdx-button action="destructive" @click="deleteTag( labelIndex, tagIndex )">
            {{ $i18n( "adiutor-delete" ) }}
          </cdx-button>
          <cdx-button @click="addNewSubitem( labelIndex, tagIndex )">
            {{ $i18n( "adiutor-add-sub-item" ) }}
          </cdx-button>
        </td>
      </tr>
      <tr v-for="( tag, tagIndex ) in label.tags" :key="'nestedTable-' + tagIndex">
        <td v-if="tag.items && tag.items.length > 0" colspan="3">
          <cdx-field>
            <table id="adiutor-options-props">
              <caption>
                {{ $i18n( "adiutor-sub-items-for" ) }} {{ tag.tag }}
                <cdx-button
                    class="add-new-button"
                    weight="quiet"
                    @click="addNewSubitem( labelIndex, tagIndex )">
                  {{ $i18n( "adiutor-add-new-tag-label" ) }}
                </cdx-button>
              </caption>
              <tr>
                <th>{{ $i18n( "adiutor-name" ) }}</th>
                <th>{{ $i18n( "adiutor-required" ) }}</th>
                <th>{{ $i18n( "adiutor-parameter" ) }}</th>
                <th>{{ $i18n( "adiutor-type" ) }}</th>
                <th>{{ $i18n( "adiutor-value" ) }}</th>
                <th>{{ $i18n( "adiutor-label" ) }}</th>
                <th>{{ $i18n( "adiutor-help" ) }}</th>
                <th style="text-align: right;">
                  {{ $i18n( "adiutor-action" ) }}
                </th>
              </tr>
              <tr v-for="( subitem, subitemIndex ) in tag.items" :key="'subitem-' + subitemIndex">
                <td>
                  <cdx-text-input
                      v-model="subitem.name"
                      :aria-label="$i18n( 'adiutor-subitem-value' )"
                      style="min-width: 80px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-toggle-switch
                      v-model="subitem.required"
                      :aria-label="$i18n( 'adiutor-required' )"></cdx-toggle-switch>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.parameter"
                      :aria-label="$i18n( 'adiutor-subitem-parameter' )"
                      style="min-width: 80px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.type"
                      :aria-label="$i18n( 'adiutor-subitem-type' )"
                      style="min-width: 50px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.value"
                      :aria-label="$i18n( 'adiutor-subitem-value' )"
                      style="min-width: 50px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.label"
                      :aria-label="$i18n( 'adiutor-subitem-label' )"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.help"
                      :aria-label="$i18n( 'adiutor-subitem-help' )"
                      style="min-width: 150px;"></cdx-text-input>
                </td>
                <td style="text-align: right;">
                  <cdx-button
                      action="destructive"
                      @click="deleteSubitem( labelIndex, tagIndex, subitemIndex )">
                    {{ $i18n( "adiutor-delete-button" ) }}
                  </cdx-button>
                </td>
              </tr>
            </table>
          </cdx-field>
        </td>
      </tr>
    </table>
  </cdx-field>
</template>

<script>
const { defineComponent, ref } = require( 'vue' );
const { CdxTextInput, CdxChipInput, CdxField, CdxToggleSwitch, CdxButton } = require( '../../codex.js' );
const AdiutorUtility = require( '../utilities/adiutorUtility.js' );
module.exports = defineComponent( {
  name: 'ArticleTaggingOptions',
  components: {
    CdxTextInput,
    CdxChipInput,
    CdxToggleSwitch,
    CdxField,
    CdxButton
  },
  setup() {
    const tagConfiguration = mw.config.get( 'wgAdiutorArticleTagging' );
    const tagList = ref( tagConfiguration.tagList );
    const useMultipleIssuesTemplate = ref( tagConfiguration.useMultipleIssuesTemplate );
    const multipleIssuesTemplate = ref( tagConfiguration.multipleIssuesTemplate );
    const apiPostSummary = ref( tagConfiguration.apiPostSummary );
    const moduleEnabled = ref( tagConfiguration.moduleEnabled );
    const testMode = ref( tagConfiguration.testMode );
    const namespaces = ref( tagConfiguration.namespaces );
    return {
      tagList,
      useMultipleIssuesTemplate,
      multipleIssuesTemplate,
      apiPostSummary,
      moduleEnabled,
      testMode,
      namespaces
    };
  },
  data() {
    return {
      saveButtonLabel: mw.message( 'adiutor-save-configurations' ).text(),
      saveButtonAction: 'progressive',
      saveButtonClass: 'save-button',
      saveButtonWeight: 'primary',
      saveButtonDisabled: false
    };
  },
  methods: {

    addNewTagLabel() {
      const newLabel = {
        label: '',
        tags: []
      };
      this.tagList.push( newLabel );
    },

    deleteLabel( label ) {
      const index = this.tagList.indexOf( label );
      if ( index !== -1 ) {
        this.tagList.splice( index, 1 );
      }
    },

    addNewTag( labelIndex ) {
      const label = this.tagList[ labelIndex ];
      if ( label ) {
        const newTag = {
          tag: '',
          description: '',
          items: []
        };
        label.tags.push( newTag );
      }
    },

    deleteTag( labelIndex, tagIndex ) {
      const label = this.tagList[ labelIndex ];
      if ( label && label.tags[ tagIndex ] ) {
        label.tags.splice( tagIndex, 1 );
      }
    },

    // Function to add a new subitem for a specific tag
    addNewSubitem( labelIndex, tagIndex ) {
      const label = this.tagList[ labelIndex ];
      if ( label && label.tags[ tagIndex ] ) {
        const newSubitem = {
          name: '',
          required: false,
          parameter: '',
          type: '',
          value: '',
          label: '',
          help: ''
        };
        label.tags[ tagIndex ].items.push( newSubitem );
      }
    },

    // Function to delete a subitem for a specific tag
    deleteSubitem( labelIndex, tagIndex, subitemIndex ) {
      const label = this.tagList[ labelIndex ];
      if ( label && label.tags[ tagIndex ] && label.tags[ tagIndex ].items[ subitemIndex ] ) {
        label.tags[ tagIndex ].items.splice( subitemIndex, 1 );
      }
    },

    generateTemplateOutput() {
      return this.useMultipleIssuesTemplate ? `{{${ this.multipleIssuesTemplate }|\n{{Cleanup|reason=Test}}\n{{Another tag}}\n}}` : '';
    },

    async saveConfiguration() {
      const configuration = {
        tagList: this.tagList,
        useMultipleIssuesTemplate: this.useMultipleIssuesTemplate,
        multipleIssuesTemplate: this.multipleIssuesTemplate,
        apiPostSummary: this.apiPostSummary,
        moduleEnabled: this.moduleEnabled,
        testMode: this.testMode,
        namespaces: this.namespaces
      };
      const title = 'MediaWiki:AdiutorArticleTagging.json';

      // Set the button to the saving state
      this.saveButtonLabel = mw.message( 'adiutor-configurations-saving' ).text();
      this.saveButtonAction = 'default';
      this.saveButtonDisabled = true;

      try {
        // Call the centralized save function from AdiutorUtility
        await AdiutorUtility.saveConfiguration( title, configuration );

        // Update the button to show the save completed
        this.saveButtonLabel = mw.message( 'adiutor-save-configurations' ).text();
        this.saveButtonAction = 'progressive';
        this.saveButtonDisabled = false;
      } catch ( error ) {
        // If an error occurred, display it and update the button accordingly
        mw.notify( error, { type: 'error' } );
        this.saveButtonLabel = mw.message( 'adiutor-try-again' ).text();
        this.saveButtonAction = 'destructive';
        this.saveButtonDisabled = false;
      }
    }

  }
} );
</script>

<style lang="less">
@import 'mediawiki.skin.variables.less';
#adiutor-options-props.adiutor-article-tagging-options {
  border-collapse: collapse;
  width: 100%;

  caption {
    padding-top: 12px;
    padding-bottom: 12px;
    background-color: @background-color-interactive;
    color: @color-emphasized;
    text-align: center;
    border: 1px solid #ddd;
    font-weight: 900;
  }

  td, th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  tr:nth-child(even) {
    background-color: @background-color-interactive-subtle;
  }

  tr:hover {
    background-color: @background-color-interactive;
  }

  th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: @background-color-base;
    color: @color-base;
  }

  .add-new-button {
    float: right;
    margin-right: 10px;
  }
}

.ext-adiutor-options .cdx-field {
  margin-top: 10px;
  display: block;
}

.ext-adiutor-options .top-message {
  margin-top: 10px;
}</style>
