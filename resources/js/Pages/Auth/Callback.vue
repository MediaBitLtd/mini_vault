<template>
    <h1>Logging in</h1>
</template>
<script setup lang="ts">
import CookieJS from 'js-cookie'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'
import AuthLayout from '~/Layouts/AuthLayout.vue'

const props = defineProps<{
    code: string;
}>()

const page = usePage()

defineOptions({ layout: AuthLayout })

axios.post('/oauth/token', {
    grant_type: 'authorization_code',
    client_id: import.meta.env.VITE_CLIENT_ID,
    client_secret: import.meta.env.VITE_CLIENT_SECRET,
    redirect_uri: window.location.href.split('?')[0],
    code: props.code,
})
    .then(({ data }) => {
        const accessToken = data.access_token
        CookieJS.set('_accessToken', accessToken) // Session cookie
        axios.defaults.headers.Authorization = `Bearer ${ accessToken }`
        window.location.href = '/'
    })
    .catch(error => {
        CookieJS.remove('_accessToken')

        if (page.props.app.isLocal) {
            alert('Error')
            return
        }

        window.location.href = '/auth/logout'
    })
</script>
