<template>
    <PageLayout>
        <template #title>Dashboard</template>
        <template #content>
            <div class="flex flex-col gap-4">
                <Card>
                    <template #title>Authenticator</template>
                    <template #content>
                        <SpinnerLoader v-if="loading" />
                        <ul v-else class="max-h-80 overflow-y-auto">
                            <li v-for="record in filteredRecords">
                                <AuthenticatorListItem :record />
                            </li>
                            <li v-if="!filteredRecords.length">
                                <span class="text-sm text-gray-400">No records found</span>
                            </li>
                        </ul>
                    </template>
                </Card>
                <Card>
                    <template #title>My Vaults</template>
                    <template #content>
                        <SpinnerLoader v-if="loading" />
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
                    </template>
                </Card>
            </div>
        </template>
    </PageLayout>
</template>
<script setup lang="ts">
import { Card, Button, Message, InputText } from 'primevue'
import { Link, router } from '@inertiajs/vue3'
import PageLayout from '~/Layouts/PageLayout.vue'
import AuthenticatorListItem from '~/Components/VaultRecords/AuthenticatorListItem.vue'
import { computed, nextTick, ref, watch } from 'vue'
import { CategoryResource, VaultRecordResource, VaultResource } from '~/types/resources'
import axios from 'axios'
import SpinnerLoader from '~/Components/SpinnerLoader.vue'
import { useErrorHandler } from '~/Composables/errors'
import { useVaults } from '~/Composables/vaults'

const { handleAPIError } = useErrorHandler()
const { loadVaults } = useVaults()

const categories = ref([])
const tfaRecords = ref([])
const vaults = ref([])

const loading = ref(false)
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
        loadVaults()
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

const filteredRecords = computed(() => tfaRecords.value.filter(record => {
    const tfa = record.values.find(i => i.field.slug === '2fa')
    if (tfa.invalid) {
        return false
    }

    return atob(tfa.value || '')?.trim().length
}))

const loadRecords = async () => {
    loading.value = true;
    const { data } = await axios.get<{
        authenticator: VaultRecordResource[];
        vaults: VaultResource[];
        categories: CategoryResource[];
    }>('/dashboard');

    tfaRecords.value = data.authenticator
    vaults.value = data.vaults
    categories.value = data.categories

    loading.value = false
}

loadRecords();

watch(
    () => newVaultName.value,
    () => error.value = undefined,
)
</script>
