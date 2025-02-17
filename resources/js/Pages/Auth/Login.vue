<template>
    <div class="w-full">
        <h3 class="text-gray-800 text-3xl text-center font-extrabold mb-8 dark:text-zinc-200">
            Mini Vault
        </h3>

        <div class="flex flex-col gap-4">
            <InputText v-model="form.email" name="email" type="email" autocomplete="ds" required placeholder="Email" />

            <Password v-model="form.password" :feedback="false" toggleMask placeholder="Password" />
        </div>

        <div class="mt-8">
            <Button fluid @click="login">Login</Button>
        </div>
    </div>
</template>
<script setup lang="ts">
import AuthLayout from '~/Layouts/AuthLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { InputText, Password, Button } from 'primevue'
import CookieJS from 'js-cookie'
import axios from 'axios'

const page = usePage()
const form = useForm({
    email: '',
    password: '',
})

CookieJS.remove('_accessToken')

delete axios.defaults.baseURL
delete axios.defaults.headers.Authorization

const login = () => {
    form.post('')
}

defineOptions({ layout: AuthLayout })
</script>
