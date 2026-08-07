import { computed, type Ref } from 'vue'
import { useListProducts } from '@bp/api-client/api/product/product'
import { ListProductsResponse } from '@bp/api-client/api/product/product.zod'
import type { ListProductsParams } from '@bp/api-client/api/model'

/**
 * Les paramètres sont typés par le SDK, donc par ce que l'API déclare vraiment.
 * Un filtre ou un champ de tri inventé côté front ne compile pas — c'est l'effet
 * recherché en déclarant les paramètres sur l'opération API Platform.
 */
export default (params: Ref<ListProductsParams>) => {
  const query = useListProducts(params, {
    query: {
      // La validation passe par `select` : vue-query ne l'exécute que sur une
      // réponse fraîche, pas à chaque lecture du cache.
      select: data => validateResponse(ListProductsResponse, data, 'GET /products'),
    },
  })

  return {
    ...query,
    products: computed(() => query.data.value?.member ?? []),
    totalItems: computed(() => query.data.value?.totalItems ?? 0),
  }
}
