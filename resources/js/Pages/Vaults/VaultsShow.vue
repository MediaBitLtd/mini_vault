<template>
    <PageLayout>
        <template #title>{{ vault.name }}</template>
        <template #header>
            <div class="flex flex-col sm:flex-row gap-2">
                <SearchBar class="flex-1"/>
                <Button class="!px-5" severity="contrast" variant="outlined" @click="menu.toggle($event)">
                    Options <i class="pi pi-cog"></i>
                </Button>
                <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
            </div>
        </template>
        <template #content>
            <ul v-if="records.length" class="space-y-4">
                <li v-for="(record, index) in records">
                    <VaultRecordListItem :vault :record :key="record.id" @delete="records.splice(index, 1)" />
                </li>
            </ul>
        </template>
        <template #footer>
            <div class="flex gap-3 px-5 py-4 sm:p-0 border-t-2 border-neutral-200 dark:border-stone-700 dark:bg-stone-950">
                <Button fluid size="large" class="sm:!rounded-none" @click="addRecord">Create Record</Button>
            </div>
        </template>
    </PageLayout>
    <RecordCreateModal
        :vault
        :categories
        v-model:visible="createVisible"
        @submitted="records.push($event)"
    />
</template>
<script setup lang="ts">
import { CategoryResource, VaultResource } from '~/types/resources'
import { useConfirm, Button, Menu } from 'primevue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useVaults } from '~/Composables/vaults'
import SearchBar from '~/Components/SearchBar.vue'
import VaultRecordListItem from '~/Components/VaultRecords/VaultRecordListItem.vue'
import PageLayout from '~/Layouts/PageLayout.vue'
import { MenuItem } from 'primevue/menuitem'
import RecordCreateModal from '~/Components/VaultRecords/RecordCreateModal.vue'

const props = defineProps<{
    vault: VaultResource;
    categories: CategoryResource[];
}>()

const confirm = useConfirm()
const { loadVaults, records, loadRecords } = useVaults()

const createVisible = ref(false)

const menu = ref()
const items = ref<MenuItem[]>([
    {
        label: 'Create record',
        icon: 'pi pi-plus',
        command: () => addRecord(),
    },
    {
        label: 'Delete vault',
        icon: 'pi pi-trash',
        command: () => {
            confirm.require({
                message: `All records will be lost for ever. Are you sure you want to delete "${ props.vault.name }"?`,
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
                        },
                    })
                },
            })
        },
    },
])

const addRecord = () => {
    createVisible.value = true
}

loadRecords(props.vault);
</script>
