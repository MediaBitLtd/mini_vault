import { ref } from 'vue'
import { CollectionResource, VaultResource, VaultRecordResource } from '~/types/resources'
import axios from 'axios'
import { useErrorHandler } from '~/Composables/errors'

const { handleAPIError } = useErrorHandler()

const vaults = ref<VaultResource[] | undefined>(undefined)
const records = ref<VaultRecordResource[]>([])
const loadingVaults = ref(false)
const loadingRecords = ref(false)
const lastLoadedPage = ref<number>(0);
const lastPage = ref<boolean>(false);

const loadVaults = async () => {
    if (loadingVaults.value) {
        return
    }

    loadingVaults.value = true
    try {
        const { data } = await axios.get<CollectionResource<VaultResource>>('/vaults')
        vaults.value = data.items
    } catch (e) {
        handleAPIError(e)
    } finally {
        loadingVaults.value = false
    }
}

const loadRecords = async (vault: VaultResource, { page, q } = { page: 1, q: undefined }) => {
    if (loadingRecords.value) {
        return
    }

    if (page === 1) {
        records.value = [];
        lastLoadedPage.value = 1;
    }

    loadingRecords.value = true
    try {
        const { data } = await axios.get<CollectionResource<VaultRecordResource>>(`/vaults/${vault.id}/records`, {
            params: { page, q, include_values: true },
        })

        records.value.push(...data.items)
        lastLoadedPage.value = data.meta.page
        lastPage.value = data.meta.page >= data.meta.last_page
    } catch (e) {
        handleAPIError(e)
    } finally {
        loadingRecords.value = false;
    }
}

const loadFavourites = async ({ page, q } = { page: 1, q: undefined }) => {
    if (loadingRecords.value) {
        return
    }

    if (page === 1) {
        records.value = [];
        lastLoadedPage.value = 1;
    }

    loadingRecords.value = true
    try {
        const { data } = await axios.get<CollectionResource<VaultRecordResource>>(`/favourites`, {
            params: { page, q, include_values: true },
        })

        records.value.push(...data.items)
        lastLoadedPage.value = data.meta.page
        lastPage.value = data.meta.page >= data.meta.last_page
    } catch (e) {
        handleAPIError(e)
    } finally {
        loadingRecords.value = false;
    }
}

export const useVaults = () => {
    if (!vaults.value) {
        loadVaults()
    }

    return {
        loadFavourites,
        loadingRecords,
        loadingVaults,
        loadRecords,
        loadVaults,
        records,
        vaults,
    }
}
