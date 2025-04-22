<template>
    <Popover ref="menu" :dismissable="!adding" @hide="activeTag = undefined">
        <div class="w-60">
            <span class="font-bold">Tags</span>
            <Divider />
            <div v-show="tags.length" class="flex flex-wrap gap-2 mb-4">
                <Badge
                    v-for="(tag, index) in tags"
                    class="cursor-pointer"
                    :severity="activeTag === index ? 'danger' : 'primary'"
                    @click="handleTagClick(index)"
                >
                    <span class="mr-1">{{ tag }}</span>
                    <span v-if="activeTag === index && loadingTag !== index">X</span>
                    <SpinnerLoader v-if="loadingTag === index" />
                </Badge>
            </div>
            <Button
                v-if="!adding"
                class="flex justify-start"
                variant="text"
                severity="contrast"
                fluid
                @click.prevent="startAdding"
            >
                <span class="pi pi-plus" />
                <span>New</span>
            </Button>
            <div v-else class="flex items-center gap-3">
                <InputText
                    ref="tagInput"
                    v-model="newTag"
                    fluid
                    @keydown.enter="addTag"
                />
                <Button
                    :icon="newTag.trim().length > 0 ? 'pi pi-check' : 'pi pi-times'"
                    size="small"
                    variant="text"
                    severity="contrast"
                    @click="addTag"
                />
            </div>
        </div>
    </Popover>
</template>
<script setup lang="ts">
import { Badge, Popover, Button, InputText, Divider } from 'primevue'
import { nextTick, ref } from 'vue'
import SpinnerLoader from '~/Components/SpinnerLoader.vue'
import { VaultRecordResource, VaultRecordTag, VaultResource } from '~/types/resources'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const props = defineProps<{
    vault: VaultResource;
    record: VaultRecordResource;
    tags: string[];
}>()

const toast = useToast()

const menu = ref()
const tagInput = ref()
const adding = ref(false)
const newTag = ref('')
const activeTag = ref<undefined|number>(undefined)
const loadingTag = ref<undefined|number>(undefined)

const toggle = event => {
    menu.value.toggle(event)
}

const startAdding = async () => {
    activeTag.value = undefined
    adding.value = true
    await nextTick()
    tagInput.value?.$el.focus()
}

const addTag = () => {
    if (loadingTag.value !== undefined) {
        return
    }

    activeTag.value = undefined
    const tag = newTag.value.trim().toLowerCase()

    if (tag.length && !props.tags.includes(tag)) {
        loadingTag.value = props.tags.push(tag) - 1

        axios.post<VaultRecordTag>(`/vaults/${props.vault.id}/records/${props.record.id}/tags`, {
            'name': tag,
        })
            .catch(() => {
                toast.error('Something went wrong')
            })
            .finally(() => {
                loadingTag.value = undefined
            })
    }

    newTag.value = ''
    adding.value = false
}

const deleteTag = async (index) => {
    if (loadingTag.value !== undefined) {
        return
    }

    loadingTag.value = index
    const tag = props.tags[index]

    try {
        await axios.delete(`/vaults/${props.vault.id}/records/${props.record.id}/tags/${tag}`)
        props.tags.splice(index, 1)
        activeTag.value = undefined
        loadingTag.value = undefined
    } catch (err) {
        toast.error('Something went wrong deleting the tag')
    }
}

const handleTagClick = index => {
    if (activeTag.value === index) {
        deleteTag(index)
        return
    }

    activeTag.value = index
}

defineExpose({
    toggle,
})
</script>
