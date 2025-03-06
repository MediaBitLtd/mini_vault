<template>
    <Card ref="panel">
        <template #content>
            <div
                class="overflow-hidden"
                style="transition: 500ms ease-in-out;"
                :style="opened ? 'max-height: 800px' : 'max-height: 2.5rem'"
            >
                <div class="flex justify-between items-center w-full">
                    <button class="flex-grow flex items-center gap-3 h-10" @click="toggleOpen">
                        <i :class="`pi ${record.category.icon}`"></i>
                        <span class="text-ellipsis overflow-hidden">
                            {{ record.name || `(${ record.category.name })` }}
                        </span>
                    </button>

                    <div class="flex items-center">
                        <Button
                            v-if="!savingFavourite"
                            variant="text"
                            size="small"
                            @click="toggleFavourite"
                        >
                            <i class="pi py-2" :class="record.is_favourite ? 'pi-star-fill' : 'pi-star'" />
                        </Button>
                        <SpinnerLoader v-else class="m-3 mr-2" />

                        <Button
                            v-if="!editing"
                            severity="contrast"
                            variant="text"
                            size="small"
                            @click="startEditing"
                        >
                            <i class="pi pi-pen-to-square" />
                        </Button>
                        <SplitButton
                            v-else
                            severity="contrast"
                            size="small"
                            :disabled="saving"
                            :model="saveMenu"
                            @click="saveRecord"
                        >
                            <span v-if="!saving">Save</span>
                            <SpinnerLoader v-else />
                        </SplitButton>
                    </div>
                </div>
                <div class="min-h-[50dvh] relative">
                    <RecordValue
                        v-for="recordValue in record.values"
                        :record-value="recordValue"
                        :editing
                    />
                </div>
            </div>
        </template>
    </Card>
</template>
<script setup lang="ts">
import { SplitButton, Button, Card, useConfirm } from 'primevue'
import { VaultRecordResource, VaultResource } from '~/types/resources'
import { computed, ref } from 'vue'
import RecordValue from '~/Components/VaultRecords/RecordValue.vue'
import { MenuItem } from 'primevue/menuitem'
import axios from 'axios'
import SpinnerLoader from '~/Components/SpinnerLoader.vue'
import { useErrorHandler } from '~/Composables/errors'
import { useToast } from 'vue-toastification'

const props = defineProps<{
    vault: VaultResource,
    record: VaultRecordResource,
}>()

const emit = defineEmits(['delete'])

const confirm = useConfirm()
const toast = useToast();
const { handleAPIError } = useErrorHandler()

const panel = ref()
const opened = ref(false)
const editing = ref(false)
const intentsToClose = ref(false)
const existingValues = ref(undefined)

const saving = ref(false)
const savingFavourite = ref(false)

const title = computed(() => props.record.name || props.record.category.name)

const saveMenu = computed(() => [
    {
        label: 'Archive',
        icon: 'pi pi-tags',
    },
    {
        label: 'Delete',
        icon: 'pi pi-trash',
        command: () => {
            confirm.require({
                message: 'Are you sure you want to permanently delete this record?',
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
                    await axios.delete(`/vaults/${props.vault.id}/records/${props.record.id}`)

                    emit('delete')
                },
            })
        },
    },
    {
        separator: true,
    },
    {
        label: 'Cancel changes',
        command: () => revertChanges(),
    },
] as MenuItem[])

const toggleOpen = () => {
    if (editing.value) {
        intentsToClose.value = true
        return
    }

    opened.value = !opened.value
    intentsToClose.value = false

    if (opened.value) {
        setTimeout(() => {
            panel.value.$el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'end' })
        }, 300)
    }
}

const startEditing = () => {
    if (!opened.value) {
        toggleOpen()
        intentsToClose.value = true
    }

    editing.value = true
    existingValues.value = JSON.parse(JSON.stringify(props.record.values))
}

const toggleFavourite = async () => {
    savingFavourite.value = true

    try {
        const { data } = await axios.put<VaultRecordResource>(`/vaults/${ props.vault.id }/records/${ props.record.id }`, {
            is_favourite: !props.record.is_favourite
        })

        props.record.is_favourite = data?.is_favourite
    } catch (e) {
        handleAPIError(e)
    } finally {
        savingFavourite.value = false
    }
}

const saveRecord = async () => {
    saving.value = true;
    try {
        const { data } = await axios.put<VaultRecordResource>(`/vaults/${ props.vault.id }/records/${ props.record.id }?include_values=true`, {
            name: props.record.name,
            values: props.record.values,
        })

        props.record.name = data.name
        props.record.values = data.values

        editing.value = false
        intentsToClose.value = false
        toast.success('Record updated')
    } catch (e) {
        handleAPIError(e)
    } finally {
        saving.value = false
    }
}

const revertChanges = () => {
    props.record.values = JSON.parse(JSON.stringify(existingValues.value))
    editing.value = false
    if (intentsToClose.value) {
        opened.value = false
    }
}
</script>
