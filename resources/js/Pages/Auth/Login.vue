<template>
    <div class="w-full md:min-w-72">
        <h3 class="text-gray-800 text-3xl text-center font-extrabold mb-8 dark:text-zinc-200">
            Mini Vault
        </h3>

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
    </div>
</template>
<script setup lang="ts">
import AuthLayout from '~/Layouts/AuthLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { InputText, Password, Message, Button } from 'primevue'
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
