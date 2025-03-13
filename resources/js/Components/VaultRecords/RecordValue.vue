<template>
    <div class="space-y-1 mb-6">
        <span class="block font-bold text-stone-500">{{ recordValue.name || recordValue.field.label }}</span>
        <div v-if="editing" class="flex gap-2">
            <div
                v-if="props.recordValue.field.type === '2fa'"
                class="flex items-center w-full gap-2"
            >
                <div
                    class="flex gap-2"
                    v-if="recordValue.value"
                    @contextmenu.prevent="menu.show"
                    @click="copyValue"
                >
                    <CircularProgress v-model="timerProgression" />
                    <span class="text-md font-bold whitespace-nowrap" style="user-select:none;">{{ tfaNumber }}</span>
                </div>
                <TFASetup
                    v-model="value"
                    @updated="emit('updated', $event)"
                />
            </div>
            <template v-else-if="props.recordValue.field.type === 'password'">
                <Password
                    ref="inputField"
                    v-model="value"
                    class="flex-grow"
                    autocomplete="off"
                    medium-regex="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}"
                    strong-regex="^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}"
                    toggle-mask
                    fluid
                />
                <Button variant="text" severity="secondary" @click="value = generatePassword()" size="small">
                    <i class="pi pi-sync" />
                </Button>
            </template>
            <InputNumber
                v-else-if="props.recordValue.field.type === 'pin'"
                v-model="value"
                autocomplete="off"
                :use-grouping="false"
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
            <Button severity="danger" variant="text" size="small" @click="deleteField">
                <i class="pi pi-trash" />
            </Button>
        </div>
        <div
            v-else-if="props.recordValue.field.type === '2fa' && recordValue.value"
            class="flex gap-2"
            @contextmenu.prevent="menu.show"
            @click="handleClick"
        >
            <CircularProgress v-model="timerProgression" />
            <span class="text-md font-bold" style="user-select:none;">{{ tfaNumber }}</span>
        </div>
        <span
            v-else
            @click="handleClick"
            @contextmenu.prevent="menu.show"
            style="user-select:none;"
        >
            {{ recordValue.field.sensitive && !overrideCensor ? censured : value || '-' }}
        </span>

        <ContextMenu ref="menu" :model="items" />
    </div>
</template>
<script setup lang="ts">
import { ContextMenu, Button, Textarea, InputText, InputNumber, Password } from 'primevue'
import { VaultRecordValueResource } from '~/types/resources'
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { MenuItem } from 'primevue/menuitem'
import { useEncryption } from '~/Composables/encryption'
import CircularProgress from '~/Components/CircularProgress.vue'
import TFASetup from '~/Components/VaultRecords/TFASetup.vue'

const props = defineProps<{
    recordValue: VaultRecordValueResource;
    editing?: boolean;
}>()

const emit = defineEmits(['update:recordValue', 'delete', 'updated'])

const { generatePassword, getOTPFromSecret } = useEncryption()

const menu = ref()
const inputField = ref()

const value = ref<VaultRecordValueResource['value']>(props.recordValue.value ? atob(props.recordValue.value) : null)
const overrideCensor = ref(false)

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

const currentTime = ref(0)
const tfaNumber = ref<string>(null)

const censured = computed(() => !value.value
    ? '-'
    : value.value?.split('').map(() => '*').splice(0, 30).join('')
)
const timerProgression = computed(() => currentTime.value / JSON.parse(value.value)?.period * 100)
const usableValue = computed(() => {
    switch (props.recordValue.field.type) {
        case '2fa':
            return tfaNumber.value?.replaceAll('-', '')
        default:
            return value.value
    }
})

const handleClick = () => {
    switch (props.recordValue.field.type) {
        case 'textarea':
            if (props.recordValue.field.slug === 'secret_note') {
                overrideCensor.value = !overrideCensor.value
            }

            // Don't copy
            return
        case '2fa':
        default:
            switch(props.recordValue.field.slug) {
                case 'appname':
                case 'website':
                    return
            }
            return copyValue()
    }
}

const copyValue = async () => {
    const result = await navigator.permissions.query({ name: 'clipboard-write' })

    if (result.state === "granted" || result.state === "prompt") {
        await navigator.clipboard.writeText(usableValue.value)
    }
}
const openUrl = () => {
    // todo this is not working on pwa
    window.open(value.value)
}
const deleteField = () => {
    emit('delete', props.recordValue)
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
    },
    {
        deep: true,
    }
)

watch(
    () => props.editing,
    async () => {
        if (props.editing && props.recordValue.field.type === 'password' && !value.value?.trim()) {
            value.value = generatePassword()
            await nextTick()
            inputField.value.unmasked = true
        }
    },
)

if (props.recordValue.field.type === '2fa') {
    const interval = setInterval(() => {
        const data = JSON.parse(value.value)

        currentTime.value = Math.abs(Math.floor(((new Date).getTime() / 1000) % data?.period) - data?.period)
        tfaNumber.value = getOTPFromSecret(data?.secret || '', data?.period)
        tfaNumber.value = tfaNumber.value.substring(0, 3) + '-' + tfaNumber.value.substring(3, 6)
    }, 500)

    onBeforeUnmount(() => {
        clearInterval(interval)
    })
}

</script>
