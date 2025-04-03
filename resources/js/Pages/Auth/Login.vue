<template>
    <div class="w-full md:min-w-72">
        <h3 class="text-gray-800 text-3xl text-center font-extrabold mb-8 dark:text-zinc-200">
            Mini Vault
        </h3>

        <template v-if="readyToLogin && !loginWithAuthn">
            <div class="flex flex-col gap-4">
                <div>
                    <InputText
                        v-model="form.email"
                        autofocus
                        name="email"
                        type="email"
                        autocomplete="ds"
                        required
                        placeholder="Email"
                        :invalid="!!form.errors.email"
                        @keydown.enter.prevent="login"
                        fluid
                    />
                    <Message v-if="form.errors.email" severity="error" size="small" variant="simple">
                        {{ form.errors.email }}
                    </Message>
                </div>

                <div>
                    <Password
                        v-model="form.password"
                        :feedback="false"
                        toggle-mask
                        placeholder="Password"
                        :invalid="!!form.errors.email"
                        @keydown.enter.prevent="login"
                        fluid
                    />
                    <Message v-if="form.errors.password" severity="error" size="small" variant="simple">
                        {{ form.errors.password }}
                    </Message>
                </div>
            </div>

            <div class="mt-8">
                <Button fluid @click="login">Login</Button>
            </div>
        </template>
        <div v-else-if="readyToLogin" class="text-center">
            <i class="pi pi-lock !text-6xl mb-4"></i>
            <h1 class="">Login with your device</h1>
            <button class="text-xs underline" @click="loginWithAuthn = false">Use your password instead</button>
        </div>
    </div>
</template>
<script setup lang="ts">
import AuthLayout from '~/Layouts/AuthLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { InputText, Password, Message, Button } from 'primevue'
import CookieJS from 'js-cookie'
import axios from 'axios'
import { useWebAuthn } from '~/Composables/auth'
import { useEncryption } from '~/Composables/encryption'
import { nextTick, ref } from 'vue'

const { base64URLStringToBuffer } = useEncryption()
const { attemptAuthWithCredential, getAuthnAuthConfiguration } = useWebAuthn()

const page = usePage()
const form = useForm({
    email: '',
    password: '',
})
const readyToLogin = ref(false)
const loginWithAuthn = ref(false)


CookieJS.remove('_accessToken')

delete axios.defaults.baseURL
delete axios.defaults.headers.Authorization

const login = () => {
    form.post('')
}

getAuthnAuthConfiguration()
    .then(async (config) => {
        readyToLogin.value = true

        if (config) {
            loginWithAuthn.value = true
            await nextTick()

            // We have webauthn
            const credential = await navigator.credentials.get({
                publicKey: {
                    challenge: base64URLStringToBuffer(config.challenge),
                    rpId: config.rpId,
                    allowCredentials: config.allowCredentials.map(key => ({
                        id: base64URLStringToBuffer(key),
                        type: 'public-key',
                    })),
                }
            })

            await attemptAuthWithCredential(credential)
        }
    })

defineOptions({ layout: AuthLayout })
</script>
