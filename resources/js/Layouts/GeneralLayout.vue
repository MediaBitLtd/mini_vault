<template>
    <section class="flex flex-col w-full h-[100dvh]">
        <header
            class="sm:hidden absolute z-30 h-full w-full shadow bg-gray-200 overflow-y-hidden dark:text-white dark:bg-stone-950"
            style="transition: all 300ms ease"
            :style="{ 'max-height': menuOpened ? '100%' : '4rem'}"
        >
            <div class="flex items-center w-full mt-3">
                <nav class="flex justify-between mx-4 w-full">
                    <Link href="/" class="flex items-center">
                        <Logo class="w-10 h-10"/>
                        <span class="ml-1 mt-1 text-sm font-bold dark:font-extrabold text-gray-500 dark:text-gray-400">MiniVault</span>
                    </Link>
                    <div class="flex gap-4">
                        <div class="mt-1">
                            <ToggleSwitch v-model="darkMode" dark-mode-switch />
                        </div>
                        <HamburgerMenuButton :menu-opened="menuOpened" @click="menuOpened = !menuOpened" />
                    </div>
                    <div class="absolute left-0 top-[4rem] w-full h-[calc(100%-4rem)] pb-20">
                        <div class="flex flex-col w-full h-full overflow-auto scroll-bg-inverted">
                            <div class="flex-1 my-10">
                                <NavItems />
                            </div>
                        </div>
                        <div class="flex flex-col items-center w-full gap-1">
                            <div
                                v-if="version"
                                class="w-full text-xs text-right px-2"
                            >
                                Ver. {{ version }}
                            </div>
                            <Link href="/auth/logout" class="py-3 w-full bg-gray-400 dark:bg-stone-800 text-center">
                                Logout
                            </Link>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <main class="flex flex-grow max-h-full overflow-hidden bg-white dark:bg-neutral-900 dark:text-white">
            <aside
                class="hidden sm:flex w-56 lg:w-86 bg-gray-200 border-r border-gray-500 dark:text-white dark:bg-stone-950 dark:border-black"
            >
                <nav class="flex flex-col w-full pt-10 pb-5 gap-10 overflow-y-auto">
                    <Link href="/" class="flex items-center px-10">
                        <img src="" alt="Logo" class="rounded-xl min-w-5 max-w-24 max-h-10">
                    </Link>
                    <div class="flex-grow">
                        <NavItems />
                    </div>
                    <div class="flex flex-col w-full gap-2">
                        <div class="flex justify-between w-full px-4 items-end">
                            <ToggleSwitch v-model="darkMode" dark-mode-switch />
                            <div
                                v-if="version"
                                class="text-xs text-right"
                            >
                                Ver. {{ version }}
                            </div>
                        </div>
                        <Link href="/auth/logout" class="py-3 w-full bg-gray-400 dark:bg-stone-800 text-center">
                            Logout
                        </Link>
                    </div>
                </nav>
            </aside>
            <article class="w-full relative h-[100dvh] pt-[4rem] sm:pt-10 mx-auto sm:mt-0">
                <slot />
            </article>
        </main>
    </section>
    <ConfirmDialog />
</template>
<script setup lang="ts">
import { useAuth } from '~/Composables/auth'
import { Link, router, usePage } from '@inertiajs/vue3'
import HamburgerMenuButton from '~/Components/HamburgerMenuButton.vue'
import { computed, ref } from 'vue'
import ToggleSwitch from '~/Components/ToggleSwitch.vue'
import { useDarkMode } from '~/Composables/settings'
import NavItems from '~/Components/Nav/NavItems.vue'
import { ConfirmDialog } from 'primevue'
import Logo from '~/Components/Logo.vue'

const page = usePage()
const { darkMode } = useDarkMode()
const { user } = useAuth()

const menuOpened = ref(false)

const version = computed(() => page.props.app.version)

router.on('start', () => {
    menuOpened.value = false
})
</script>
<style lang="scss">
::-webkit-scrollbar {
    width: .2rem;
    height: .2rem;

    @media screen and (min-width: 1280px) {
        width: .4rem;
        height: .4rem;
    }

    @media screen and (min-width: 1600px) {
        width: .7rem;
        height: .7rem;
    }

    &-track {
        background: transparent;
    }
    &-thumb {
        background: #b6b7be;
        border-radius: 20px;
    }
}

.dark {
    ::-webkit-scrollbar-thumb {
        background: #4b4d56;
    }
}
</style>
