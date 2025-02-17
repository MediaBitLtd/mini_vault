import { ref } from 'vue'
import { CollectionResource, VaultResource } from '~/types/resources'
import axios from 'axios'
import { useErrorHandler } from '~/Composables/errors'

const { handleAPIError } = useErrorHandler()

const vaults = ref<VaultResource[] | undefined>()
const loadingVaults = ref(false)

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

export const useVaults = () => {
    if (!vaults.value) {
        loadVaults()
    }

    return {
        loadingVaults,
        loadVaults,
        vaults,
    }
}
