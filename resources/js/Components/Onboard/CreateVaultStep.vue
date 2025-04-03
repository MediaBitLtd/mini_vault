<template>
    <div class="flex flex-col justify-between flex-grow h-full max-w-76">
        <div class="flex flex-col justify-center items-center">
            <Logo class="w-32 h-32" />
            <h5 class="mt-1 text-lg font-bold text-gray-600 dark:font-normal dark:text-gray-400">Let's create your first vault</h5>
        </div>
        <div class="">
            <p class="mb-4">Choose the name for your first vault.</p>
            <InputText
                v-model="createVaultForm.name"
                placeholder="My Vault"
                @keydown.enter.prevent="submit"
                fluid
            />
            <Message
                v-if="!!createVaultForm.errors.name"
                variant="simple"
                severity="error"
                size="small"
                @keydown.enter.prevent="submit"
                :invalid="!!createVaultForm.errors.name"
            >
                {{ createVaultForm.errors.name }}
            </Message>
        </div>
        <div></div>
    </div>
</template>
<script setup lang="ts">
import { Message, InputText } from 'primevue'
import { ref } from 'vue'
import axios from 'axios'
import Logo from '~/Components/Logo.vue'

const emit = defineEmits(['next'])

const createVaultForm = ref({name: '', errors: { name: '' }})

const submit = async () => {
    try {
        await axios.post('/onboard/vault', {
            ...createVaultForm.value,
        })

        emit('next')
    } catch (err) {
        console.error(err)
        return
    }
}

defineExpose({ submit });
</script>
