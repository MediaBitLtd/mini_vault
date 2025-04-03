<template>
    <div class="flex flex-col justify-between flex-grow h-full max-w-76">
        <div class="flex flex-col justify-center items-center">
            <Logo class="w-32 h-32" />
            <h5 class="mt-1 text-lg font-bold text-gray-600 dark:font-normal dark:text-gray-400">Let's get your new password set</h5>
        </div>
        <div>
            <div class="mt-5 space-y-3">
                <div>
                    <Password
                        v-model="changePasswordForm.password"
                        :feedback="false"
                        toggle-mask
                        placeholder="Password"
                        @keydown.enter.prevent="submit"
                        :invalid="!!changePasswordForm.errors.password"
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
                <div>
                    <Password
                        v-model="changePasswordForm.password_confirmation"
                        :feedback="false"
                        toggle-mask
                        placeholder="Confirm Password"
                        @keydown.enter.prevent="submit"
                        :invalid="!!changePasswordForm.errors.password_confirmation"
                        fluid
                    />
                </div>
            </div>
            <p class="text-sm my-5">
                <strong class="text-red-400 text-xs mr-1">
                    <i class="pi pi-exclamation-triangle mr-1"></i>Warning
                </strong> If you lose and forget your
                password, you cannot retrieve any of your data!
            </p>
        </div>
        <div></div>
    </div>
</template>
<script setup lang="ts">
import { Message, Password } from 'primevue'
import { ref } from 'vue'
import axios from 'axios'
import CookieJS from 'js-cookie'
import Logo from '~/Components/Logo.vue'

const emit = defineEmits(['next']);

const changePasswordForm = ref({
    password: '',
    password_confirmation: '',

    errors: {
        password: '',
    },
})

const submit = async () => {
    try {
        const { data } = await axios.post('/onboard/password', {
            ...changePasswordForm.value,
        })

        CookieJS.set('_accessToken', data.access_token) // Session cookie
        axios.defaults.headers.Authorization = `Bearer ${ data.access_token }`

        emit('next')
    } catch (err) {
        if (err.response?.status === 422) {
            changePasswordForm.value.errors.password = err.response.data.errors.password[0]
            return
        }
        console.error(err)
        return
    }
}

defineExpose({ submit });
</script>
