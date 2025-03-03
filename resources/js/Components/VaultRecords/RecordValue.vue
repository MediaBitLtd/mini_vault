<template>
    <div class="space-y-1 mb-6">
        <span class="block font-bold text-stone-500">{{ recordValue.field.label }}</span>
        <template v-if="editing">
            <Password
                v-if="props.recordValue.field.type === 'password'"
                v-model="value"
                autocomplete="off"
                toggle-mask
                fluid
            />
            <Textarea
                v-else-if="props.recordValue.field.type === 'textarea'"
                v-model="value"
                variant="filled"
                rows="5"
                autocomplete="off"
                style="resize: none"
                fluid
            />
            <InputText
                v-else
                v-model="value"
                autocomplete="off"
                fluid
            />
        </template>
        <span v-else @click="menu.show" @contextmenu.prevent="menu.show" style="user-select:none;">
            {{ recordValue.field.sensitive ? censured : value || '-' }}
        </span>

        <ContextMenu ref="menu" :model="items" />
    </div>
</template>
<script setup lang="ts">
import { ContextMenu, Textarea, InputText, Password } from 'primevue'
import { VaultRecordValueResource } from '~/types/resources'
import { computed, ref, watch } from 'vue'
import { MenuItem } from 'primevue/menuitem'

const props = defineProps<{
    recordValue: VaultRecordValueResource;
    editing?: boolean;
}>()

const emit = defineEmits(['update:recordValue'])

const menu = ref()

const value = ref<VaultRecordValueResource['value']>(props.recordValue.value ? atob(props.recordValue.value) : null)
const items = computed<MenuItem[]>(() => ([
    {
        label: 'Copy',
        icon: 'pi pi-copy',
        disabled: !props.recordValue.value,
        command: () => copyValue(),
    },
    props.recordValue.field.type === 'url' ? {
        label: 'Open URL',
        icon: 'pi pi-globe',
        disabled: !props.recordValue.value,
        command: () => openUrl(),
    } : null
] as MenuItem[]).filter(i => i))

const censured = computed(() => !value.value ? '-' : value.value?.split('').map(() => '*').join(''))

const copyValue = async () => {
    const result = await navigator.permissions.query({ name: 'clipboard-write' })

    if (result.state === "granted" || result.state === "prompt") {
        await navigator.clipboard.writeText(value.value)
    }
}
const openUrl = () => {
    // todo this is not working on pwa
    window.open(value.value)
}

watch(
    () => value.value,
    () => {
        props.recordValue.value = value.value ? btoa(value.value) : null
        emit('update:recordValue', props.recordValue)
    }
)

watch(
    () => props.recordValue,
    () => {
        value.value = props.recordValue.value ? atob(props.recordValue.value) : null
    }
)
</script>
