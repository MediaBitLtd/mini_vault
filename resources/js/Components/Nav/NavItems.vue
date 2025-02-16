<template>
    <div class="flex flex-col justify-between px-8 py-5">
        <section>
            <ul class="flex flex-col">
                <li>
                    <Link href="/all">
                        All Items
                    </Link>
                </li>
                <li>
                    <Link href="/favourites">
                        Favourites
                    </Link>
                </li>
            </ul>
        </section>
        <section class="my-10">
            <h3 class="text-xl text-bolder mb-2 text-gray-400 dark:text-sot-500">My Vaults</h3>
            <Loader v-if="loadingVaults" />
            <ul v-else class="flex flex-col gap-1">
                <li v-for="vault in vaults">
                    <Link :href="`/vault/${vault.id}`">
                        <span v-if="vault.is_unlockable">🔒</span>
                        <span v-else>❌</span>
                        {{ vault.name }}
                    </Link>
                </li>
                <li class="mt-2">
                    <button
                        v-if="!addingVault"
                        role="button"
                        type="button"
                        class="p-1 text-gray-600 cursor-pointer"
                        @click="addingVault = true"
                    >+ Add New Vault
                    </button>
                    <template v-else>
                        <div class="flex items-center gap-2">
                            <input
                                autofocus
                                v-model="newVaultName"
                                type="text"
                                class="border rounded-lg p-1"
                                placeholder="Name of vault"
                                @keydown.enter.prevent="createVault"
                            />
                            <button
                                v-if="!savingNew"
                                role="button"
                                type="button"
                                class="cursor-pointer"
                                @click="createVault"
                            >✔
                            </button>
                            <SpinnerLoader v-else />
                        </div>
                        <span class="text-xs text-red-600 block mt-1" v-if="error">{{ error }}</span>
                    </template>
                </li>
            </ul>
        </section>
        <section>
            <Link href="/settings">
                Settings
            </Link>
        </section>
    </div>
</template>
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useVaults } from '~/Composables/vaults.js'
import Loader from '~/Components/Loader.vue'
import { ref, watch } from 'vue'
import SpinnerLoader from '~/Composables/SpinnerLoader.vue'
import axios from 'axios'
import { useErrorHandler } from '~/Composables/errors.js'
import { VaultResource } from '~/types/resources'

const { handleAPIError } = useErrorHandler()
const { loadingVaults, vaults } = useVaults()

const addingVault = ref(false)
const savingNew = ref(false)
const newVaultName = ref('')
const error = ref<string | undefined>(undefined)

const createVault = async () => {
    savingNew.value = true

    try {
        const { data } = await axios.post<VaultResource>('/vaults', {
            name: newVaultName.value,
        })
        vaults.value.push(data)
        addingVault.value = false
        newVaultName.value = ''
    } catch (e) {
        const { errorMessage } = handleAPIError(e)
        if (errorMessage) {
            error.value = errorMessage
        }
    } finally {
        savingNew.value = false
    }
}

watch(
    () => newVaultName.value,
    () => error.value = undefined,
)
</script>
