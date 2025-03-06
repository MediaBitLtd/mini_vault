<template>
    <div class="space-y-1 mb-6">
        <span class="block font-bold text-stone-500">{{ recordValue.field.label }}</span>
        <template v-if="editing">
            <div v-if="props.recordValue.field.type === 'password'" class="flex gap-2">
                <Password
                    ref="passwordField"
                    v-model="value"
                    class="flex-grow"
                    autocomplete="off"
                    medium-regex="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}"
                    strong-regex="^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}"
                    toggle-mask
                    :pt-options="{root: { state: { unmasked: true } } }"
                    fluid
                />
                <Button variant="text" severity="secondary" @click="value = generatePassword()">
                    <i class="pi pi-sync" />
                </Button>
            </div>
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
import { ContextMenu, Button, Textarea, InputText, Password } from 'primevue'
import { VaultRecordValueResource } from '~/types/resources'
import { computed, nextTick, ref, watch } from 'vue'
import { MenuItem } from 'primevue/menuitem'
import { useEncryption } from '~/Composables/encryption'

const props = defineProps<{
    recordValue: VaultRecordValueResource;
    editing?: boolean;
}>()

const emit = defineEmits(['update:recordValue'])

const { generatePassword } = useEncryption()

const menu = ref()
const passwordField = ref()

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

watch(
    () => props.editing,
    async () => {
        if (props.editing && props.recordValue.field.type === 'password' && !value.value?.trim()) {
            value.value = generatePassword()
            await nextTick()
            passwordField.value.unmasked = true
        }
    },
)
</script>
