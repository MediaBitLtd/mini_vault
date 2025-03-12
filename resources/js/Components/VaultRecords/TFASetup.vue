<template>
    <Button v-if="!modelValue" severity="contrast" size="small" @click="scan" icon="pi pi-qrcode" label="Setup" />
    <div v-else class="w-full text-gray-400 dark:text-stone-400">
        (2FA successfuly setup)
    </div>
    <Dialog
        :visible
        @update:visible="cancel"
        modal
        header="Two factor auth setup"
        :style="{ width: '25rem' }"
    >
        <div class="space-y-4">
            <template v-if="scanning">
                <div id="scanner" />
                <Button severity="contrast" @click="enterManual" label="Enter manually" fluid />
            </template>
            <div v-else class="space-y-4">
                <div>
                    <span class="block font-bold text-stone-500">Secret</span>
                    <InputText v-model="secret" fluid />
                </div>
                <div>
                    <span class="block font-bold text-stone-500">Period</span>
                    <InputText v-model="period" fluid />
                </div>
                <Button severity="contrast" @click="save" label="Save" fluid />
            </div>
            <Button severity="danger" @click="cancel" label="Cancel" fluid />
        </div>
    </Dialog>
</template>
<script setup lang="ts">
import { nextTick, ref, watch } from 'vue'
import { Button, Dialog, InputText } from 'primevue'
import { Html5Qrcode } from 'html5-qrcode'

const props = defineProps<{
   modelValue?: string,
}>()

const emit = defineEmits(['update:modelValue', 'updated'])

const period = ref(30)
const secret = ref('')

const scanning = ref(false)
const visible = ref(false)
const scanner = ref()

const scan = async () => {
    visible.value = true
    scanning.value = true

    await nextTick()

    try {
        const cameras = await Html5Qrcode.getCameras()

        if (!cameras.length) {
            // TODO change this
            alert('No cameras found')
            return cancel()
        }

        scanner.value = new Html5Qrcode('scanner')
        scanner.value.start(
            { facingMode: 'environment' },
            {},
            processQR
        )
    } catch (e) {
        if (e.name === 'NotAllowedError') {
            // TODO change this
            alert('Permission denied')
            scanner.value?.stop()
            scanner.value = undefined
            scanning.value = false
            return
        }

        console.error(e)
    }
}

const processQR = decodedText => {
    if (decodedText.indexOf('otpauth://') !== 0) {
        // TODO error
        alert('Not valid qr')
        return enterManual()
    }

    decodedText = decodeURI(decodedText.substring(10))
    const proto = decodedText.substring(0, 4)

    if (proto !== 'totp') {
        // TODO error
        alert('Protocol not supported')
        return enterManual()
    }

    decodedText = decodedText.substring(5)
    const account = decodedText.split('?')[0]
    const parts = {} as { [k: string]: string }

    decodedText.split('?')[1]
        .split('&')
        .forEach(item => parts[item.split('=')[0]] = item.split('=')[1])

    period.value = parts.period ? parseInt(parts.period) : 30
    secret.value = parts.secret
    const accountName = account.split(':')[1] || ''
    const issuer = parts.issuer || account.split(':')[0]

    emit('updated', {
        accountName,
        issuer,
        period: period.value,
        secret: secret.value,
    })

    save()
}

const enterManual = () => {
    scanner.value?.stop()
    scanner.value = undefined
    scanning.value = false
}

const save = () => {
    emit('update:modelValue', JSON.stringify({
        secret: secret.value,
        period: period.value,
    }))

    scanner.value?.stop()
    scanner.value = undefined

    scanning.value = false
    visible.value = false
}

const cancel = () => {
    scanner.value?.stop()
    scanner.value = undefined
    visible.value = false
}

watch(
    () => props.modelValue,
    () => {
        const data = JSON.parse(props.modelValue || '{}')

        period.value = data?.period || 30
        secret.value = data?.secret || ''
    },
    {
        immediate: true,
    }
)
</script>
