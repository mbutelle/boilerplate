import { computed, type Ref } from 'vue'
import { useApiProductsGetCollection } from '@bp/api-client/api/product/product'
import type { ApiProductsGetCollectionParams } from '@bp/api-client/api/model'

/**
 * Les paramètres sont typés par le SDK, donc par ce que l'API déclare vraiment.
 * Un filtre ou un champ de tri inventé côté front ne compile pas — c'est l'effet
 * recherché en déclarant les paramètres sur l'opération API Platform.
 */
export default (params: Ref<ApiProductsGetCollectionParams>) => {
  const query = useApiProductsGetCollection(params)

  return {
    ...query,
    products: computed(() => query.data.value?.member ?? []),
    totalItems: computed(() => query.data.value?.totalItems ?? 0),
  }
}
