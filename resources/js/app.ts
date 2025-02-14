import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from 'axios'

// @ts-ignore
axios.defaults.baseURL = import.meta.env.VITE_BASE_URL;

createInertiaApp({
    resolve: name => {
        // @ts-ignore
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
