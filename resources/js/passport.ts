import { createApp } from 'vue'
import ConfirmationService from 'primevue/confirmationservice'
import Toast from 'vue-toastification'
import PrimeVue from 'primevue/config'
import Preset from '~/theme'
import { Ripple } from 'primevue'
import PassportAuthentication from '~/Pages/Auth/PassportAuthentication.vue'
import { useDarkMode } from '~/Composables/settings'

import 'primeicons/primeicons.css'

useDarkMode()

const el = document.getElementById('app')

createApp(PassportAuthentication, {
    ...el.dataset
})
    .use(ConfirmationService)
    .use(Toast)
    .use(PrimeVue, {
        ripple: true,
        theme: {
            preset: Preset,
            options: {
                darkModeSelector: '.dark'
            },
        },
    })
    .directive('ripple', Ripple)
    .mount('#app')
