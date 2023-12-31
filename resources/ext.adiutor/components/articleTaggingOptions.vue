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
      {{ $i18n( 'adiutor-article-tagging-configuration-title' ) }}
    </h3>
    <p style="margin-top: 22px;">
      {{ $i18n( 'adiutor-article-tagging-description' ) }}
    </p>
  </cdx-field>
  <cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-toggle-switch v-model="moduleEnabled">
        <cdx-label input-id="moduleEnabled">
          {{ $i18n( 'adiutor-module-enabled' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-module-enabled-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-toggle-switch v-model="testMode">
        <cdx-label input-id="testMode">
          {{ $i18n( 'adiutor-test-mode' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-test-mode-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-chip-input v-model:input-chips="namespaces" remove-button-label="remove"></cdx-chip-input>
      <template #label>
        {{ $i18n( 'adiutor-namespaces' ) }}
      </template>
      <template #description>
        {{ $i18n( 'adiutor-namespaces-description' ) }}
      </template>
    </cdx-field>
    <cdx-field :is-fieldset="true">
      <cdx-toggle-switch v-model="useMultipleIssuesTemplate">
        <cdx-label input-id="useMultipleIssuesTemplate">
          {{ $i18n( 'adiutor-use-multiple-issues-template-label' ) }}
        </cdx-label>
        <template #description>
          {{ $i18n( 'adiutor-multiple-issues-template-description' ) }}
        </template>
      </cdx-toggle-switch>
    </cdx-field>
    <cdx-field v-if="useMultipleIssuesTemplate">
      <cdx-label input-id="multipleIssuesTemplate">
        {{ $i18n( 'adiutor-multiple-issues-template-label' ) }}
      </cdx-label>
      <cdx-text-input
          id="multipleIssuesTemplate"
          v-model="multipleIssuesTemplate"
          aria-label="Multiple issues template"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-multiple-issues-template-help-text' ) }}
        <h5>{{ $i18n( 'adiutor-output' ) }}</h5>
        <pre>{{ generateTemplateOutput() }}</pre>
      </template>
    </cdx-field>
    <cdx-field>
      <cdx-label input-id="uncategorizedTemplate">
        {{ $i18n( 'adiutor-uncategorized-template-label' ) }}
      </cdx-label>
      <cdx-text-input
          id="uncategorizedTemplate"
          v-model="uncategorizedTemplate"
          aria-label="Uncategorized template"></cdx-text-input>
      <template #help-text>
        {{ $i18n( 'adiutor-uncategorized-template-help-text' ) }}
      </template>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <cdx-field>
      <strong> {{ $i18n( 'adiutor-summaries-label' ) }}</strong>
      <cdx-label input-id="apiPostSummary">
        {{ $i18n( 'adiutor-api-post-summary-label' ) }}
      </cdx-label>
      <cdx-text-input
          id="apiPostSummary"
          v-model="apiPostSummary"
          aria-label="API Post Summary for Talk Page"></cdx-text-input>
    </cdx-field>
  </cdx-field>
  <cdx-field>
    <table id="adiutor-options-props" width="100%">
      <caption>
        {{ $i18n( 'adiutor-tag-labels' ) }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewTagLabel">
          {{ $i18n( 'adiutor-add-new-tag-label' ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( 'adiutor-label-column' ) }}</th>
        <th style="text-align: right;">
          {{ $i18n( 'adiutor-action' ) }}
        </th>
      </tr>
      <tr v-for="( label, index ) in tagList" :key="index">
        <td>
          <cdx-text-input v-model="label.label" aria-label="Namespace value"></cdx-text-input>
        </td>
        <td style="text-align: right;">
          <cdx-button action="destructive" @click="deleteLabel( label )">
            {{ $i18n( 'adiutor-delete-button' ) }}
          </cdx-button>
        </td>
      </tr>
    </table>
  </cdx-field>
  <cdx-field v-for="( label, labelIndex ) in tagList" :key="'nestedTable-' + labelIndex">
    <table id="adiutor-options-props" width="100%">
      <caption>
        {{ $i18n( 'adiutor-tags-for' ) }} {{ label.label }}
        <cdx-button
            class="add-new-button"
            weight="quiet"
            @click="addNewTag( labelIndex )">
          {{ $i18n( 'adiutor-add-new-tag-label' ) }}
        </cdx-button>
      </caption>
      <tr>
        <th>{{ $i18n( 'adiutor-tag-template' ) }}</th>
        <th>{{ $i18n( 'adiutor-tag-description' ) }}</th>
        <th style="text-align: right;">
          {{ $i18n( 'adiutor-action' ) }}
        </th>
      </tr>
      <tr v-for="( tag, tagIndex ) in label.tags" :key="'tag-' + tagIndex">
        <td>
          <cdx-text-input
              v-model="tag.tag"
              aria-label="Value"
              style="min-width: 50px;"></cdx-text-input>
        </td>
        <td>
          <cdx-text-input v-model="tag.description" aria-label="Data"></cdx-text-input>
        </td>
        <td style="text-align: right;">
          <cdx-button action="destructive" @click="deleteTag( labelIndex, tagIndex )">
            {{ $i18n( 'adiutor-delete' ) }}
          </cdx-button>
          <cdx-button @click="addNewSubitem( labelIndex, tagIndex )">
            {{ $i18n( 'adiutor-add-sub-item' ) }}
          </cdx-button>
        </td>
      </tr>
      <tr v-for="( tag, tagIndex ) in label.tags" :key="'nestedTable-' + tagIndex">
        <td v-if="tag.items && tag.items.length > 0" colspan="3">
          <cdx-field>
            <table id="adiutor-options-props" width="100%">
              <caption>
                {{ $i18n( 'adiutor-sub-items-for' ) }} {{ tag.description }}
                <cdx-button
                    class="add-new-button"
                    weight="quiet"
                    @click="addNewSubitem( labelIndex, tagIndex )">
                  {{ $i18n( 'adiutor-add-new-tag-label' ) }}
                </cdx-button>
              </caption>
              <tr>
                <th>{{ $i18n( 'adiutor-name' ) }}</th>
                <th>{{ $i18n( 'adiutor-required' ) }}</th>
                <th>{{ $i18n( 'adiutor-parameter' ) }}</th>
                <th>{{ $i18n( 'adiutor-type' ) }}</th>
                <th>{{ $i18n( 'adiutor-value' ) }}</th>
                <th>{{ $i18n( 'adiutor-label' ) }}</th>
                <th>{{ $i18n( 'adiutor-help' ) }}</th>
                <th style="text-align: right;">
                  {{ $i18n( 'adiutor-action' ) }}
                </th>
              </tr>
              <tr v-for="( subitem, subitemIndex ) in tag.items" :key="'subitem-' + subitemIndex">
                <td>
                  <cdx-text-input
                      v-model="subitem.name"
                      aria-label="Subitem Value"
                      style="min-width: 80px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-toggle-switch v-model="subitem.required" aria-label="Required"></cdx-toggle-switch>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.parameter"
                      aria-label="Subitem parameter"
                      style="min-width: 80px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.type"
                      aria-label="Subitem type"
                      style="min-width: 50px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.value"
                      aria-label="Subitem value"
                      style="min-width: 50px;"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input v-model="subitem.label" aria-label="Subitem label"></cdx-text-input>
                </td>
                <td>
                  <cdx-text-input
                      v-model="subitem.help"
                      aria-label="Subitem help"
                      style="min-width: 150px;"></cdx-text-input>
                </td>
                <td style="text-align: right;">
                  <cdx-button
                      action="destructive"
                      @click="deleteSubitem( labelIndex, tagIndex, subitemIndex )">
                    {{ $i18n( 'adiutor-delete-button' ) }}
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
const { CdxTextInput, CdxChipInput, CdxField, CdxToggleSwitch, CdxButton } = require( '@wikimedia/codex' );
const tagConfiguration = mw.config.get( 'AdiutorArticleTagging' );
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
    const tagList = ref( tagConfiguration.tagList );
    const useMultipleIssuesTemplate = ref( tagConfiguration.useMultipleIssuesTemplate );
    const multipleIssuesTemplate = ref( tagConfiguration.multipleIssuesTemplate );
    const uncategorizedTemplate = ref( tagConfiguration.uncategorizedTemplate );
    const apiPostSummary = ref( tagConfiguration.apiPostSummary );
    const moduleEnabled = ref( tagConfiguration.moduleEnabled );
    const testMode = ref( tagConfiguration.testMode );
    const namespaces = ref( tagConfiguration.namespaces );
    return {
      tagList,
      useMultipleIssuesTemplate,
      multipleIssuesTemplate,
      uncategorizedTemplate,
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
      if ( !mw.config.get( 'wgUserGroups' ).includes( 'interface-admin' ) ) {
        mw.notify( mw.message( 'adiutor-interface-admin-required' ).text(), { type: 'error' } );
        return;
      }
      this.saveButtonLabel = mw.message( 'adiutor-configurations-saving' ).text();
      this.saveButtonAction = 'default';
      this.saveButtonDisabled = true;

      const api = new mw.Api();
      const editToken = mw.user.tokens.get( 'csrfToken' );
      const contentToSave = JSON.stringify( {
        tagList: this.tagList,
        useMultipleIssuesTemplate: this.useMultipleIssuesTemplate,
        multipleIssuesTemplate: this.multipleIssuesTemplate,
        uncategorizedTemplate: this.uncategorizedTemplate,
        apiPostSummary: this.apiPostSummary,
        moduleEnabled: this.moduleEnabled,
        testMode: this.testMode,
        namespaces: this.namespaces
      }, null, 2 );

      const data = {
        action: 'edit',
        title: 'MediaWiki:AdiutorArticleTagging.json',
        text: contentToSave,
        token: editToken,
        format: 'json'
      };

      api.post( data ).done( ( response ) => {
        if ( response.edit && response.edit.result === 'Success' ) {
          mw.notify( mw.message( 'adiutor-localization-settings-has-been-updated' ).text(), {
            title: mw.msg( 'adiutor-operation-completed' ),
            type: 'success'
          } );
          this.saveButtonLabel = mw.message( 'adiutor-save-configurations' ).text();
          this.saveButtonAction = 'progressive';
          this.saveButtonDisabled = false;
        } else {
          throw new Error( mw.msg( 'adiutor-operation-failed' ) );
        }
      } ).fail( ( error ) => {
        mw.notify( error, { type: 'error' } );
        this.saveButtonLabel = mw.message( 'adiutor-try-again' ).text();
        this.saveButtonAction = 'destructive';
        this.saveButtonDisabled = false;
      } );
    }

  }
} );
</script>

<style lang="less">
#adiutor-options-props {
  border-collapse: collapse;
  width: 100%;

  caption {
    padding-top: 12px;
    padding-bottom: 12px;
    background-color: #eaecf0;
    color: #000f;
    text-align: center;
    border: 1px solid #ddd;
    font-weight: 900;
  }

  td, th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  tr:hover {
    background-color: #ddd;
  }

  th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #fff;
    color: #000f;
  }

  .add-new-button {
    float: right;
    margin-right: 10px;
  }
}

cdx-label {
  font-weight: 900;
  margin-top: 10px;
  display: block;
}

.cdx-field {
  margin-top: 10px;
  display: block;
}

.top-message {
  margin-top: 10px;
}</style>
