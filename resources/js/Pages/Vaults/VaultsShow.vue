<template>
    <PageLayout>
        <template #title>{{ vault.name }}</template>
        <template #header>
            <Button severity="danger" @click="deleteVault">Delete vault</Button>
            <SearchBar />
        </template>
        <template #content>
            <ul v-if="records.length" class="space-y-4">
                <li v-for="record in records">
                    <VaultRecordListItem :vault :record />
                </li>
            </ul>
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import { VaultResource } from '~/types/resources'
import { useConfirm, Button } from 'primevue'
import { computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useVaults } from '~/Composables/vaults'
import SearchBar from '~/Components/SearchBar.vue'
import VaultRecordListItem from '~/Components/VaultRecords/VaultRecordListItem.vue'
import PageLayout from '~/Layouts/PageLayout.vue'

const props = defineProps<{
    vault: VaultResource;
}>()

const confirm = useConfirm()
const { loadVaults, records, loadRecords } = useVaults()

const deleteVault = () => {
    confirm.require({
        message: `All records will be lost for ever. Are you sure you want to delete ${ props.vault.name }?`,
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
        },
        accept: async () => {
            router.delete(`/vault/${props.vault.id}`, {
                onSuccess() {
                    loadVaults()
                }
            })
        },
    })
}

loadRecords(props.vault);
</script>
