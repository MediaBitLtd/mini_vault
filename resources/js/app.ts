import { createApp, h } from 'vue'
import type { DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3'
import axios from 'axios'
import GeneralLayout from '~/Layouts/GeneralLayout.vue'
import { useDarkMode } from '~/Composables/settings'

useDarkMode();

axios.defaults.baseURL = import.meta.env.VITE_BASE_URL;

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue', { eager: true })
        const page = pages[`./Pages/${name}.vue`]

        page.default.layout = page.default.layout || GeneralLayout

        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
