<template>
    <div>
          <!-- Filter by namespace -->
          <cdx-combobox v-model:selected="selectionNamespace" :menu-items="namespaceValues"
            placeholder="Filter by namespace"></cdx-combobox>
        <!-- Filter by reason -->
        <cdx-combobox v-model:selected="selectionReason" :menu-items="deletionReasonsValue"
            placeholder="Filter by reason"></cdx-combobox>
    </div>
<div class="speedy-deletion-requests">
    <cdx-card class="sdr-item" url="https://www.example.com" v-for="request in filteredRequests" :key="request.pageName">
        <template #title>
            {{ request.pageName }}
        </template>
        <template #description>
            {{ request.reason }}
        </template>
    </cdx-card>
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
  