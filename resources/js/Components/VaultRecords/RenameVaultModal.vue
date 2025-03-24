<template>
    <Dialog :visible="visible" @update:visible="cancel" modal header="Rename vault" :style="{ width: '25rem' }">
        <div class="mb-4">
            <InputText
                v-model="name"
                class="flex-auto"
                autocomplete="off"
                placeholder="Name"
                fluid
                :invalid="!name && submitted"
            />
            <Message v-if="!name && submitted" severity="error" size="small" variant="simple">
                Please provide a name for the record
            </Message>
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancel" severity="secondary" @click="cancel"></Button>
            <Button type="button" label="Save" @click="save" :loading="saving"></Button>
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import { Dialog, Button, Message, InputText } from 'primevue'
import { VaultResource } from '~/types/resources'
import { useErrorHandler } from '~/Composables/errors'
import { nextTick, ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps<{
    vault: VaultResource;
    visible: boolean;
}>()

const emit = defineEmits(['update:visible', 'submitted'])

const { handleAPIError } = useErrorHandler()

const saving = ref(false)
const submitted = ref(false)
const name = ref<string>(undefined)

const reset = async () => {
    await nextTick()
    name.value = props.vault.name
    submitted.value = false
}

const cancel = () => {
    if (saving.value) {
        return
    }

    reset()
    emit('update:visible', false)
}

const save = async () => {
    submitted.value = true

    if (!name.value) {
        return
    }

    saving.value = true

    try {
        const { data } = await axios.put(`/vaults/${props.vault.id}`, {
            name: name.value,
        })

        reset()
        emit('submitted', data)
        emit('update:visible', false)
    } catch (e) {
        handleAPIError(e)
    } finally {
        saving.value = false
    }
}

watch(
    () => props.vault,
    () => name.value = props.vault.name,
    {
        immediate: true,
    }
)
</script>
