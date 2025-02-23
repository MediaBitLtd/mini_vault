<template>
    <PageLayout>
        <template #title>Settings</template>
        <template #content>
            <Button @click="setup">Setup Biometrics</Button>
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import PageLayout from '~/Layouts/PageLayout.vue'
import { Button } from 'primevue'
import axios from 'axios'
import { useEncryption } from '~/Composables/encryption'
import { useWebAuthn } from '~/Composables/auth'

const { bufferToBase64URLString, base64URLStringToBuffer } = useEncryption()
const { getAuthnAuthConfiguration, registerAuthnAuthentication } = useWebAuthn()

const setup = async () => {
    try {
        const credential = await navigator.credentials.create(
            await getAuthnConfig()
        )

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
        // TODO error handling
        alert(e.message)
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
