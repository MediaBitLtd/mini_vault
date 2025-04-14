import CookieJS from 'js-cookie'
import axios from 'axios'
import { computed, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { UserResource } from '~/types/resources'
import { useEncryption } from '~/Composables/encryption'
import { useToast } from 'vue-toastification'
const page = usePage()
const { bufferToBase64URLString } = useEncryption()

const toast = useToast();

const user = ref<UserResource | undefined>()
const hasAuthnSetup = ref(!!localStorage.getItem('_webauthn'))

const getGuestAxios = () => {
    const newInstance = axios.create()
    delete newInstance.defaults.headers.Authorization
    newInstance.defaults.baseURL = '/api'
    return newInstance
}

const getAuthnAuthConfiguration = async () => {
    const webauthn = localStorage.getItem('_webauthn')

    if (!webauthn) {
        return null
    }

    const { userId, userKey, cypher } = JSON.parse(atob(webauthn))

    try {
        const { data } = await (getGuestAxios()).post('/webauthn/get-auth-options', {
            userId,
            userKey,
            cypher,
        })

        return data
    } catch (e) {
        localStorage.removeItem('_webauthn')
        return null
    }
}

const attemptAuthWithCredential = async (credential) => {
    try {
        const { data } = await getGuestAxios().post('/webauthn/login', {
            clientExtensionResults: credential.getClientExtensionResults(),
            id: credential.id,
            type: credential.type,
            authenticatorAttachment: credential.authenticatorAttachment,
            response: {
                authenticatorData: bufferToBase64URLString(credential.response.authenticatorData),
                clientDataJSON: bufferToBase64URLString(credential.response.clientDataJSON),
                signature: bufferToBase64URLString(credential.response.signature),
                userHandle: credential.response.userHandle
                    ? bufferToBase64URLString(credential.response.userHandle)
                    : undefined,
            },
        })

        const accessToken = data.access_token
        CookieJS.set('_accessToken', accessToken) // Session cookie
        axios.defaults.headers.Authorization = `Bearer ${ accessToken }`
        router.post('/auth/verify', {}, {
            headers: {
                Authorization: `Bearer ${ accessToken }`,
            },
        })
    } catch (e) {
        toast.error('Something went wrong authenticating with authn');
        console.error(e)
    }
}

const registerAuthnAuthentication = (key: string) => {
    const accessToken = CookieJS.get('_accessToken')
    if (!accessToken) {
        window.location.href = '/auth/logout'
    }

    const jwt = JSON.parse(atob(accessToken.split('.')[1]));

    localStorage.setItem('_webauthn', btoa(JSON.stringify({
        userId: jwt.sub,
        userKey: key,
        cypher: jwt.cypher,
    })))
    hasAuthnSetup.value = true;
}

const resetAuth = () => {
    localStorage.removeItem('_webauthn')
    CookieJS.remove('_accessToken')
    window.location.href = '/auth/logout'
}

export const useWebAuthn = () => {
    return {
        registerAuthnAuthentication,
        getAuthnAuthConfiguration,
        attemptAuthWithCredential,
        hasAuthnSetup,
    }
}

export const useAuth = () => {
    const accessToken = CookieJS.get('_accessToken')
    if (!accessToken) {
        window.location.href = '/auth/logout'
    }

    axios.defaults.headers.Authorization = `Bearer ${ accessToken }`
    axios.defaults.baseURL = '/api'

    user.value = page.props.auth.user

    return {
        resetAuth,
        user,
    }
}
