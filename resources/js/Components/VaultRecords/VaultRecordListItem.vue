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
                        v-for="(recordValue, index) in record.values"
                        :key="recordValue.id"
                        :record-value="recordValue"
                        :editing
                        @delete="record.values.splice(index, 1)"
                        @updated="handle2FASetup"
                    />

                    <Button v-if="editing" @click="addingNewField = true">Add Field</Button>
                </div>
            </div>
        </template>
    </Card>
    <Dialog
        v-model:visible="addingNewField"
        modal
        header="Add new field"
        :style="{ width: '25rem' }"
    >
        <div class="flex flex-col gap-4">
            <InputText
                v-model="newName"
                placeholder="Name (optional)"
                fluid
            />
            <Select
                v-model="newField"
                :options="fields"
                option-label="label"
                option-value="id"
                fluid
            />
            <Button size="small" fluid :loading="savingNewField" @click="addField" >Add</Button>
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import { Dialog, Select, SplitButton, InputText, Button, Card, useConfirm } from 'primevue'
import { FieldResource, VaultRecordResource, VaultRecordValueResource, VaultResource } from '~/types/resources'
import { computed, ref, watch } from 'vue'
import RecordValue from '~/Components/VaultRecords/RecordValue.vue'
import { MenuItem } from 'primevue/menuitem'
import axios from 'axios'
import SpinnerLoader from '~/Components/SpinnerLoader.vue'
import { useErrorHandler } from '~/Composables/errors'
import { useToast } from 'vue-toastification'

const props = defineProps<{
    vault: VaultResource,
    record: VaultRecordResource,
    fields: FieldResource[],
}>()

const emit = defineEmits(['delete', 'editing'])

const confirm = useConfirm()
const toast = useToast();
const { handleAPIError } = useErrorHandler()

const panel = ref()
const opened = ref(false)
const editing = ref(false)
const intentsToClose = ref(false)
const existingValues = ref(undefined)
const addingNewField = ref(false)
const savingNewField = ref(false)
const newName = ref<string | undefined>(undefined)
const newField = ref(props.fields[0]?.id)

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
        addingNewField.value = false
    } catch (e) {
        handleAPIError(e)
    } finally {
        savingFavourite.value = false
    }
}

const handle2FASetup = ({ issuer, accountName }: {issuer: string, accountName?: string}) => {
    if (issuer) {
        const appNameValue = props.record.values.find(value => value.field.slug === 'appname')
        if (appNameValue) {
            appNameValue.value = btoa(issuer)
        }
    }
    if (accountName) {
        const userNameValue = props.record.values.find(value => value.field.slug === 'username')
        if (userNameValue) {
            userNameValue.value = btoa(accountName)
        }
    }
}

const addField = async () => {
    savingNewField.value = true

    try {
        const { data } = await axios.post<VaultRecordValueResource>(`/vaults/${ props.vault.id }/records/${props.record.id}/values`, {
            field_id: newField.value,
            name: newName.value,
        })

        newField.value = props.fields[0]?.id
        newName.value = undefined
        props.record.values.push(data)
        existingValues.value?.push(data)
        intentsToClose.value = false
        addingNewField.value = false
    } catch (e) {
        handleAPIError(e)
    } finally {
        savingNewField.value = false
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

watch(
    () => editing.value,
    () => emit('editing', editing.value)
)
watch(
    () => addingNewField.value,
    () => {
        if (!addingNewField.value) {
            newName.value = undefined
        }
    }
)
</script>
