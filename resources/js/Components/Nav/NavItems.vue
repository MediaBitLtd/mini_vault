<template>
    <div class="flex flex-col justify-between h-full max-h-72 px-8 py-5">
        <section>
            <ul class="flex flex-col">
                <li>
                    <Link href="/">
                        All Items
                    </Link>
                </li>
                <li>
                    <Link href="/">
                        Favourites
                    </Link>
                </li>
            </ul>
        </section>
        <section>
            <h3 class="text-xl text-bolder text-gray-400 dark:text-sot-500">My Vaults</h3>
            <ul class="flex flex-col">
                <li>
                    <Link href="/">
                        Each Vault
                    </Link>
                </li>
            </ul>
        </section>
        <section>
            <Link href="/">
                Settings
            </Link>
        </section>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3'
</script>
