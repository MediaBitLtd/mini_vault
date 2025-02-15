<template>
    <h1>Logging in</h1>
</template>
<script setup lang="ts">
import CookieJS from 'js-cookie'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const props = defineProps<{
    code: string;
}>()

const page = usePage()

axios.post('/oauth/token', {
    grant_type: 'authorization_code',
    client_id: import.meta.env.VITE_CLIENT_ID,
    client_secret: import.meta.env.VITE_CLIENT_SECRET,
    redirect_uri: window.location.href.split('?')[0],
    code: props.code,
})
    .then(({ data }) => {
        const accessToken = data.accessToken
        CookieJS.set('_accessToken', accessToken) // Session cookie
        axios.defaults.headers.Authorization = `Bearer ${ accessToken }`
        window.location.href = '/'
    })
    .catch(error => {
        if (page.props.isLocal) {
            alert('Error')
            return
        }

        window.location.href = '/auth/logout'
    })

</script>
