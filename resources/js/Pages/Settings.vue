<template>
    <PageLayout>
        <template #title>Settings</template>
        <template #content>
            <div class="space-y-4">
                <Card>
                    <template #title>
                        Device Authentication
                    </template>
                    <template #content>
                        <div class="mt-2">
                            <div v-if="hasAuthnSetup" class="flex items-center gap-4">
                                <i class="pi pi-lock !text-3xl" />
                                <p>You have device authentication enabled</p>
                            </div>
                            <div v-else>
                                <Button @click="setupBiometrics">Get started</Button>
                            </div>
                        </div>
                    </template>
                </Card>
                <Card>
                    <template #title>
                        Password
                    </template>
                    <template #content>
                        <div v-if="changingPassword" class="space-y-8">
                            <p>
                                <strong class="text-red-400 text-sm">
                                    <i class="pi pi-exclamation-triangle mr-1"></i>Warning
                                </strong><br/>
                                If you change and forget your password, you cannot retrieve any of your data!
                            </p>
                            <div>
                                <Password
                                    v-model="changePasswordForm.current_password"
                                    :feedback="false"
                                    toggle-mask
                                    placeholder="Current Password"
                                    @keydown.enter.prevent="changePassword"
                                    :invalid="!!changePasswordForm.errors.current_password"
                                    fluid
                                />
                                <Message
                                    v-if="!!changePasswordForm.errors.current_password"
                                    variant="simple"
                                    severity="error"
                                    size="small"
                                >
                                    {{ changePasswordForm.errors.current_password }}
                                </Message>
                            </div>
                            <div>
                                <Password
                                    v-model="changePasswordForm.password"
                                    class="mb-4"
                                    :feedback="false"
                                    toggle-mask
                                    placeholder="New Password"
                                    @keydown.enter.prevent="changePassword"
                                    :invalid="!!changePasswordForm.errors.password"
                                    fluid
                                />
                                <Password
                                    v-model="changePasswordForm.password_confirmation"
                                    :feedback="false"
                                    toggle-mask
                                    placeholder="Confirm New Password"
                                    @keydown.enter.prevent="changePassword"
                                    fluid
                                />
                                <Message
                                    v-if="!!changePasswordForm.errors.password"
                                    variant="simple"
                                    severity="error"
                                    size="small"
                                >
                                    {{ changePasswordForm.errors.password }}
                                </Message>
                            </div>
                            <Button @click="changePassword">Change</Button>
                        </div>
                        <Button v-else @click="changingPassword = true">Change password</Button>
                    </template>
                </Card>
            </div>
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import PageLayout from '~/Layouts/PageLayout.vue'
import { Button, Card, Password, Message, useConfirm } from 'primevue'
import axios from 'axios'
import { useEncryption } from '~/Composables/encryption'
import { useAuth, useWebAuthn } from '~/Composables/auth'
import { NavigatorCredential } from '~/types'
import { POSITION, useToast } from 'vue-toastification'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const confirm = useConfirm()
const toast = useToast();
const { resetAuth } = useAuth();

const { bufferToBase64URLString, base64URLStringToBuffer } = useEncryption()
const { hasAuthnSetup, getAuthnAuthConfiguration, registerAuthnAuthentication } = useWebAuthn()

const changingPassword = ref(false)
const changingPasswordConfirmed = ref(false)

const changePasswordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

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

const changePassword = () => {
    if (changingPasswordConfirmed.value) {
        return confirmedSubmission()
    }

    confirm.require({
        message: 'This will log you out of all devices. Are you sure?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Change Password',
            severity: 'primary',
        },
        accept: () => {
            changingPasswordConfirmed.value = true
            confirmedSubmission()
        },
    })
}

const confirmedSubmission = () => {
    changePasswordForm.post('/auth/change-password', {
        onSuccess() {
            toast.success('Changed password successfully')
            resetAuth();
        },
    })
}
</script>
