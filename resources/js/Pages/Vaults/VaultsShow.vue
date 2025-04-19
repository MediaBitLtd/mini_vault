<template>
    <PageLayout>
        <template #title>{{ vault.name }}</template>
        <template #header>
            <div class="flex gap-2">
                <SearchBar v-model="search" class="flex-1" :disabled="!!editing.length" />
                <Button class="!px-5" severity="contrast" variant="outlined" @click="menu.toggle($event)">
                    Options <i class="pi pi-cog"></i>
                </Button>
                <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
            </div>
        </template>
        <template #content>
            <ul class="space-y-4">
                <li v-for="(record, index) in records">
                    <VaultRecordListItem
                        :vault
                        :record
                        :key="record.id"
                        :fields
                        @editing="$event ? editing.push(true) : editing.pop()"
                        @delete="records.splice(index, 1)"
                    />
                </li>
                <li v-if="!records.length && !loadingRecords">
                    <span class="text-sm text-gray-400">No records</span>
                </li>
                <InfiniteLoader v-model:loading="loadingRecords" :last-page="lastPage" @load="nextPage" />
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
        @submitted="records.unshift({...$event, _new: true})"
    />
    <RenameVaultModal
        :vault
        v-model:visible="renameVisible"
        @submitted="updateVault"
    />
</template>
<script setup lang="ts">
import { CategoryResource, FieldResource, VaultResource } from '~/types/resources'
import { useConfirm, Button, Menu } from 'primevue'
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useVaults } from '~/Composables/vaults'
import SearchBar from '~/Components/SearchBar.vue'
import VaultRecordListItem from '~/Components/VaultRecords/VaultRecordListItem.vue'
import PageLayout from '~/Layouts/PageLayout.vue'
import { MenuItem } from 'primevue/menuitem'
import RecordCreateModal from '~/Components/VaultRecords/RecordCreateModal.vue'
import { useDebounce } from '~/Composables/debouce'
import InfiniteLoader from '~/Components/InfiniteLoader.vue'
import RenameVaultModal from '~/Components/VaultRecords/RenameVaultModal.vue'

const props = defineProps<{
    vault: VaultResource;
    categories: CategoryResource[];
    fields: FieldResource[];
}>()

const confirm = useConfirm()
const { loadVaults, lastLoadedPage, loadingRecords, lastPage, records, loadRecords } = useVaults()
const debounce = useDebounce()

const search = ref('')
const createVisible = ref(false)
const renameVisible = ref(false)
const editing = ref([])

const menu = ref()
const items = ref<MenuItem[]>([
    {
        label: 'Create record',
        icon: 'pi pi-plus',
        command: () => addRecord(),
    },
    {
        label: 'Rename vault',
        icon: 'pi pi-pencil',
        command: () => renameVisible.value = true,
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

const applySearch = () => {
    loadRecords(props.vault, {
        q: search.value?.trim() ? search.value : undefined,
        page: 1,
    });
}

const updateVault = ({ name }) => {
    props.vault.name = name
    loadVaults()
}

const nextPage = () => {
    if (lastPage.value) {
        return
    }

    loadRecords(props.vault, {
        q: search.value?.trim() ? search.value : undefined,
        page: lastLoadedPage.value + 1,
    });
}

loadRecords(props.vault);

watch(
    () => search.value,
    () => {
        debounce(() => applySearch())
    }
)
</script>
