import { createApp, h } from 'vue'
import type { DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from 'axios'
import GeneralLayout from '~/Layouts/GeneralLayout.vue'
import { useDarkMode } from '~/Composables/settings'
import PrimeVue from 'primevue/config'
import { Ripple } from 'primevue'
import ConfirmationService from 'primevue/confirmationservice'
import Preset from '~/theme'
import Toast from 'vue-toastification'

import 'primeicons/primeicons.css'
import 'vue-toastification/dist/index.css'

useDarkMode()

axios.defaults.baseURL = import.meta.env.VITE_BASE_URL

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue', { eager: true })
        const page = pages[`./Pages/${ name }.vue`]

        page.default.layout = page.default.layout || GeneralLayout

        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
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
            .mount(el)
    },
})
