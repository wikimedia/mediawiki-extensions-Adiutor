<template>
    <div class="afd">
        <div class="header">
            <h5>{{ $i18n('afd-special-page-title') }}</h5>
            <p>Articles for deletion (AfD) is where Wikipedians discuss whether an article should be deleted. Articles
                listed are normally discussed for at least seven days, after which the deletion process proceeds based on
                community consensus. Common outcomes are that the article is kept, merged, redirected, incubated,
                renamed/moved to another title, userfied to a user subpage, or deleted per the deletion policy.
                Disambiguation pages are also nominated for deletion at AfD.</p>
        </div>
        <div>
            <!-- Filter by namespace -->
            <cdx-combobox v-model:selected="selectionNamespace" :menu-items="namespaceValues"
                placeholder="Filter by namespace"></cdx-combobox>
            <!-- Filter by reason -->
            <cdx-combobox v-model:selected="selectionReason" :menu-items="deletionReasonsValue"
                placeholder="Filter by reason"></cdx-combobox>
        </div>
        <div class="speedy-deletion-requests">
            <cdx-card class="sdr-item" url="https://www.example.com" v-for="request in filteredRequests"
                :key="request.pageName">
                <template #title>
                    {{ request.pageName }}
                </template>
                <template #description>
                    {{ request.reason }}
                </template>
            </cdx-card>
        </div>
    </div>
</template>
  
<script>
const { defineComponent, ref, computed } = require('vue');
const { CdxCard, CdxCombobox } = require('@wikimedia/codex');

module.exports = defineComponent({
    name: 'speedyDeletionRequests',
    components: { CdxCard, CdxCombobox },
    setup() {
        const speedyDeletionRequests = [
            {
                "pageName": "Test2",
                "reason": "A1",
                "requester": 1212,
                "creator": 122,
                "namespace": "Article"
            },
            {
                "pageName": "File:Test.jpg",
                "reason": "F1",
                "requester": 1212,
                "creator": 122,
                "namespace": "File"
            },
            {
                "pageName": "Template:Test",
                "reason": "T1",
                "requester": 1212,
                "creator": 122,
                "namespace": "Template"
            },
            {
                "pageName": "User:Test User",
                "reason": "U1",
                "requester": 1212,
                "creator": 122,
                "namespace": "User page"
            },
            {
                "pageName": "Test9",
                "reason": "A1",
                "requester": 1212,
                "creator": 122,
                "namespace": "Article"
            }
        ];

        const deletionReasonsValue = [
            ...new Set(speedyDeletionRequests.map(request => request.reason))
        ].map(reason => ({ label: reason, value: reason }));

        // Use ref to create a reactive property for the selected value
        const selectionReason = ref('');

        const namespaceValues = [
            ...new Set(speedyDeletionRequests.map(request => request.namespace))
        ].map(namespace => ({ label: namespace, value: namespace }));

        // Use ref to create a reactive property for the selected value
        const selectionNamespace = ref('');

        // Create a computed property to filter speedyDeletionRequests
        const filteredRequests = computed(() => {
            if (!selectionReason.value && !selectionNamespace.value) {
                return speedyDeletionRequests;
            }

            return speedyDeletionRequests.filter(request => {
                const matchReason = !selectionReason.value || request.reason === selectionReason.value;
                const matchNamespace = !selectionNamespace.value || request.namespace === selectionNamespace.value;

                return matchReason && matchNamespace;
            });
        });

        return {
            deletionReasonsValue,
            namespaceValues,
            selectionReason,
            selectionNamespace,
            filteredRequests,
        };
    },
});
</script>
  
<style lang="css">
.afd cdx-label {
    margin-bottom: 10px;
    display: block;
}

.afd .header {
    background-color: #eaf3ff;
    display: block;
    align-items: baseline;
    justify-content: space-between;
    height: 17em;
    padding: 20px;
    background-image: url(https://upload.wikimedia.org/wikipedia/commons/6/6c/Illustration_of_Reading_Wikipedia_in_the_Classroom_%28blue%29_02.png);
    background-position: right 10px;
    background-repeat: no-repeat;
    background-size: 364px;
}

.afd .header p {
    width: 66%;
}

.afd h5 {
    margin: 0;
    padding: 0;
    font-size: 24px;
}
</style>