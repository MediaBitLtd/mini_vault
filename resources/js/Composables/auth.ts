import CookieJS from 'js-cookie'
import axios from 'axios'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { UserResource } from '~/types/resources'

const page = usePage()
const user = ref<UserResource | undefined>()

export const useAuth = () => {
    const accessToken = CookieJS.get('_accessToken')
    if (!accessToken) {
        window.location.href = '/auth/logout'
    }

    axios.defaults.headers.Authorization = `Bearer ${ accessToken }`
    axios.defaults.baseURL = '/api'

    user.value = page.props.auth.user

    return {
        user,
    }
}
