<template>
    <div class="flex flex-col justify-center items-center h-full max-w-76">
        <i class="pi pi-lock !text-5xl my-6" />
        <h5 class="text-lg font-bold text-gray-600 dark:font-normal dark:text-gray-400">Setup device authentication</h5>
        <p v-if="!hasAuthnSetup" class="text-md mt-4">
            You can setup device authentication and even biometrics if your device supports it.
        </p>

        <Button severity="contrast" v-if="!hasAuthnSetup" class="mt-5" @click="setupBiometrics">Setup</Button>
        <div v-else class="flex items-center mt-5 gap-2">
            <i class="pi pi-check !text-2xl" />
            <span>All done</span>
        </div>
    </div>
</template>
<script setup lang="ts">
import { Button } from 'primevue'
import { useEncryption } from '~/Composables/encryption'
import { useWebAuthn } from '~/Composables/auth'
import { NavigatorCredential } from '~/types'
import { useToast } from 'vue-toastification'
import axios from 'axios'

const { bufferToBase64URLString, base64URLStringToBuffer } = useEncryption()
const { hasAuthnSetup, getAuthnAuthConfiguration, registerAuthnAuthentication } = useWebAuthn()

const toast = useToast();

const setupBiometrics = async () => {
    try {
        const credential = await navigator.credentials.create(
            await getAuthnConfig()
        ) as NavigatorCredential;

        const { id, rawId, response, type } = credential

        const { data } = await axios.post('/webauthn/register-authn', {
            id,
            rawId: bufferToBase64URLString(rawId),
            type,
            clientExtensionResults: credential.getClientExtensionResults(),
            authenticatorAttachment: credential.authenticatorAttachment,
            response: {
                authenticatorData: bufferToBase64URLString(response.getAuthenticatorData()),
                clientDataJSON: bufferToBase64URLString(response.clientDataJSON),
                publicKey: bufferToBase64URLString(response.getPublicKey()),
                transports: response.getTransports(),
            },
        })

        registerAuthnAuthentication(data.key)
    } catch (e) {
        toast.error('Something went wrong setting up device auth', {
            icon: 'pi pi-lock'
        })
        console.error(e)
        return
    }

    // Verify authentication
    if (!await getAuthnAuthConfiguration()) {
        console.error('Still didn\'t work');
    }
}

const getAuthnConfig = async () => {
    const { data } = await axios.get('/webauthn/get-authn-config')
    return {
        publicKey: {
            ...data,
            excludeCredentials: data.excludeCredentials.map(key => ({
                id: base64URLStringToBuffer(key.id),
                type: key.type
            })),
            challenge: base64URLStringToBuffer(data.challenge),
            user: {
                ...data.user,
                id: base64URLStringToBuffer(data.user.id),
            },
            rp: {
                ...data.rp,
            },
        }
    };
}
</script>
