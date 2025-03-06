<template>
    <Dialog :visible="visible" @update:visible="cancel" modal header="Create Record" :style="{ width: '25rem' }">
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
        <div class="mb-4">
            <Select
                v-model="categoryId"
                :options="props.categories"
                option-label="name"
                option-value="id"
                placeholder="Category"
                :invalid="!categoryId && submitted"
                filter
                fluid
            >
                <template #option="slotProps">
                    <div class="flex items-center">
                        <i :class="`mr-3 pi ${slotProps.option.icon}`" />
                        <div>{{ slotProps.option.name }}</div>
                    </div>
                </template>
            </Select>
            <Message v-if="!categoryId && submitted" severity="error" size="small" variant="simple">
                Please select a category
            </Message>
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancel" severity="secondary" @click="cancel"></Button>
            <Button type="button" label="Save" @click="save" :loading="saving"></Button>
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { Message, Dialog, Select, InputText, Button } from 'primevue'
import { CategoryResource, VaultResource } from '~/types/resources'
import { useErrorHandler } from '~/Composables/errors'
import axios from 'axios'

const props = defineProps<{
    vault: VaultResource,
    visible: boolean;
    categories: CategoryResource[];
}>();

const emit = defineEmits(['update:visible', 'submitted'])

const { handleAPIError } = useErrorHandler()

const saving = ref(false)
const submitted = ref(false)
const name = ref<string>(undefined)
const categoryId = ref<number>(undefined);

const reset = () => {
    name.value = undefined
    categoryId.value = undefined
    submitted.value = false;
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

    if (! categoryId.value || ! name.value) {
        return
    }

    saving.value = true

    try {
        const { data } = await axios.post(`/vaults/${props.vault.id}/records`, {
            name: name.value,
            category_id: categoryId.value,
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
</script>
