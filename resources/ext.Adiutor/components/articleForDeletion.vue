<template>
    <cdx-dialog class="afd-dialog" v-model:open="openAfdDialog" :title="$i18n('adiutor-afd-header-title')" close-button-label="Close"
        :show-dividers="true" @default="openAfdDialog = true">
        <div class="header">
            <p>{{ $i18n('adiutor-afd-header-description') }}</p>
        </div>
        <cdx-field class="afd-dialog-body">
            <cdx-label class="adt-label"><strong>{{ $i18n('adiutor-rationale') }}</strong></cdx-label>
            <cdx-text-area v-model="rationaleValue" :placeholder="rationalePlaceholder"></cdx-text-area>
        </cdx-field>
        <div class="footer">
            <cdx-checkbox v-model="informCreator" :inline="true">
                {{ $i18n('adiutor-inform-creator') }}
            </cdx-checkbox>
            <cdx-button action="progressive" weight="primary" aria-label="Next" @click="nominateForDeletion">
                {{ $i18n('adiutor-continue') }}
            </cdx-button>
        </div>
    </cdx-dialog>
</template>
<script>
const { defineComponent, ref } = require('vue');
const { CdxButton, CdxCheckbox, CdxField, CdxDialog, CdxLabel, CdxTextArea } = require('@wikimedia/codex');
const afdConfiguration = mw.config.get('AdiutorArticleForDeletion');
module.exports = defineComponent({
    name: 'articleForDeletion',
    components: {
        CdxButton,
        CdxDialog,
        CdxCheckbox,
        CdxField,
        CdxLabel,
        CdxTextArea,
    },
    data() {
        return {
            rationalePlaceholder: mw.msg('adiutor-rationale-placeholder')
        };
    },
    setup() {
        const rationaleValue = ref('');
        const openAfdDialog = ref(true);
        const informCreator = ref(true);
        function nominateForDeletion() {
            openAfdDialog.value = false;
        }
        return {
            openAfdDialog,
            rationaleValue,
            informCreator,
            nominateForDeletion
        };
    }
});
</script>
<style lang="css">
.afd-dialog .cdx-dialog {
    max-width: 548px;
    padding-top: 10px;
    padding-bottom: 0;
}

.afd-dialog-body {
    padding: 20px;
    display: grid;
    width: inherit;
}

.afd-dialog .cdx-dialog__body {
    flex-grow: 1;
    margin-top: 0;
    padding: 0;
    overflow-y: auto;
}


.afd-dialog .cdx-dialog--dividers .cdx-dialog__body {
    padding-top: 0;
}

.afd-dialog .prd-reason-field {
    display: flex;
    flex-direction: column;
    width: 50%;
}

.afd-dialog .cdx-dialog__header {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    box-sizing: border-box;
    width: 100%;
    padding: 10px 20px 10px;
    font-weight: 700;
}

.afd-dialog cdx-label {
    margin-bottom: 10px;
    display: block;
    margin-top: 10px;
}

.afd-dialog .header {
    background-color: #eaf3ff;
    display: block;
    align-items: baseline;
    justify-content: space-between;
    height: 9em;
    padding: 20px;
    background-image: url(../../ext.Adiutor.images/afd-background.png);
    background-position: right 0px;
    background-repeat: no-repeat;
    background-size: 245px;
}

.afd-dialog .cdx-dialog__footer {
    padding: 10px !important;
}

.afd-dialog .header p {
    width: 60%;
}

.afd-dialog h2 {
    margin: 0;
    padding: 0;
    font-size: 1.125em !important
}

.afd-dialog .cdx-select-vue {
    margin-bottom: 10px !important;
}

.afd-dialog .footer {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    border-top: solid 1px #c8ccd1;
    padding: 20px;
}
</style>