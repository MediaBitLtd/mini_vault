<template>
    <PageLayout>
        <template #title>Favourites</template>
        <template #content>
            <ul class="space-y-4">
                <li v-for="(record, index) in records">
                    <VaultRecordListItem
                        :vault="record.vault"
                        :record
                        :key="record.id"
                        :fields
                        @delete="records.splice(index, 1)"
                    />
                </li>
                <li v-if="!records.length && !loadingRecords">
                    <span class="text-sm text-gray-400">No records</span>
                </li>
            </ul>
            <InfiniteLoader v-model:loading="loadingRecords" :last-page="lastPage" @load="nextPage" />
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import PageLayout from '~/Layouts/PageLayout.vue'
import { useVaults } from '~/Composables/vaults'
import VaultRecordListItem from '~/Components/VaultRecords/VaultRecordListItem.vue'
import { FieldResource } from '~/types/resources'
import InfiniteLoader from '~/Components/InfiniteLoader.vue'

const props = defineProps<{
    fields: FieldResource[];
}>()

const { records, loadFavourites, loadingRecords, lastPage, lastLoadedPage } = useVaults()

const nextPage = () => {
    if (lastPage.value) {
        return
    }

    loadFavourites({ page: lastLoadedPage.value + 1, q: undefined })
}

loadFavourites()
</script>
