<template>
    <PageLayout>
        <template #title>Settings</template>
        <template #content>
            <div v-if="hasAuthnSetup" class="flex items-center gap-4">
                <i class="pi pi-lock !text-3xl" />
                <p>You have biometrics enabled</p>
            </div>
            <Button v-else @click="setupBiometrics">Setup Biometrics</Button>
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import PageLayout from '~/Layouts/PageLayout.vue'
import { Button } from 'primevue'
import axios from 'axios'
import { useEncryption } from '~/Composables/encryption'
import { useWebAuthn } from '~/Composables/auth'
import { NavigatorCredential } from '~/types'
import { POSITION, useToast } from 'vue-toastification'

const toast = useToast();

const { bufferToBase64URLString, base64URLStringToBuffer } = useEncryption()
const { hasAuthnSetup, getAuthnAuthConfiguration, registerAuthnAuthentication } = useWebAuthn()

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
        toast.error('Something went wrong setting up biometrics', {
            position: POSITION.BOTTOM_CENTER,
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
