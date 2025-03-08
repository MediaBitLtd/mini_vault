<template>
    <PageLayout>
        <template #title>Favourites</template>
        <template #content>
            <ul v-if="records.length" class="space-y-4">
                <li v-for="(record, index) in records">
                    <VaultRecordListItem
                        :vault="record.vault"
                        :record
                        :key="record.id"
                        :fields
                        @delete="records.splice(index, 1)"
                    />
                </li>
            </ul>
            <!--TODO pagination-->
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import PageLayout from '~/Layouts/PageLayout.vue'
import { useVaults } from '~/Composables/vaults'
import VaultRecordListItem from '~/Components/VaultRecords/VaultRecordListItem.vue'
import { FieldResource } from '~/types/resources'

const props = defineProps<{
    fields: FieldResource[];
}>()

const { records, loadFavourites } = useVaults()

loadFavourites()
</script>
