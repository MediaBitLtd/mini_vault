import { ref, watch } from 'vue'

const darkMode = ref<boolean>(false)
const systemDarkMode = ref<boolean>(false)
const settingDarkMode = ref<null | string>(null)

const setDarkMode = () => {
    document.documentElement.classList.remove('dark')
    if (darkMode.value) {
        document.documentElement.classList.add('dark')
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#171f2a')
    } else {
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#3E56A2')
    }

    if (!settingDarkMode.value && systemDarkMode.value !== darkMode.value) {
        settingDarkMode.value = darkMode.value ? 'dark' : 'light'
        window.localStorage.setItem('theme', settingDarkMode.value)
    } else if (settingDarkMode.value) {
        settingDarkMode.value = darkMode.value ? 'dark' : 'light'
        window.localStorage.setItem('theme', settingDarkMode.value)
    }
}

export const useDarkMode = () => {
    systemDarkMode.value = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    settingDarkMode.value = window.localStorage.getItem('theme')

    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            systemDarkMode.value = !!event.matches
            if (!settingDarkMode.value) {
                darkMode.value = !!event.matches
            }
        })
    }

    if ((!settingDarkMode.value && systemDarkMode.value) || settingDarkMode.value === 'dark') {
        darkMode.value = true
    }

    return {
        darkMode,
    }
}

watch(
    () => darkMode.value,
    () => setDarkMode(),
)
