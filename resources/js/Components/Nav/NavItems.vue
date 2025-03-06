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
                        v-if="newVaultName === undefined"
                        role="button"
                        type="button"
                        class="p-1 text-gray-600 cursor-pointer"
                        @click="prepareForm"
                    >+ Add New Vault
                    </button>
                    <template v-else>
                        <div class="flex items-center gap-3">
                            <!--Autofocus doesn't seem to work all the time-->
                            <InputText
                                ref="nameInput"
                                size="small"
                                v-model="newVaultName"
                                :invalid="!!error"
                                placeholder="Name of vault"
                                @keydown.enter.prevent="createVault"
                                @focusout="() => !newVaultName?.trim().length ? (newVaultName = undefined) : null"
                            />
                            <button
                                v-if="!savingNew"
                                role="button"
                                type="button"
                                class="flex"
                                @click="createVault"
                            >
                                <i class="pi pi-check text-green-700 dark:text-gray-400"></i>
                            </button>
                            <SpinnerLoader v-else />
                        </div>
                        <Message v-if="error" severity="error" size="small" variant="simple">{{ error }}</Message>
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
import { Link, router } from '@inertiajs/vue3'
import { useVaults } from '~/Composables/vaults.js'
import Loader from '~/Components/Loader.vue'
import { ref, watch, nextTick } from 'vue'
import SpinnerLoader from '~/Components/SpinnerLoader.vue'
import axios from 'axios'
import { useErrorHandler } from '~/Composables/errors.js'
import { VaultResource } from '~/types/resources'
import { Message, InputText } from 'primevue'

const { handleAPIError } = useErrorHandler()
const { loadingVaults, vaults } = useVaults()

const nameInput = ref()
const savingNew = ref(false)
const newVaultName = ref<string | undefined>(undefined)
const error = ref<string | undefined>(undefined)

const prepareForm = async () => {
    newVaultName.value = ''
    await nextTick()

    nameInput.value?.$el.focus()
}

const createVault = async () => {
    savingNew.value = true

    try {
        const { data } = await axios.post<VaultResource>('/vaults', {
            name: newVaultName.value,
        })
        vaults.value.push(data)
        newVaultName.value = undefined
        router.get(`/vault/${data.id}`)
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
