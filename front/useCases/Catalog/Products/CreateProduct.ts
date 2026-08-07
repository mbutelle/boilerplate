import { useQueryClient } from '@tanstack/vue-query'
import {
  getApiProductsGetCollectionQueryKey,
  useApiProductsPost,
} from '@bp/api-client/api/product/product'
import type { ProductCreateProductPayload } from '@bp/api-client/api/model'

export default () => {
  const queryClient = useQueryClient()

  const mutation = useApiProductsPost({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getApiProductsGetCollectionQueryKey() })
      },
    },
  })

  return {
    ...mutation,
    createProduct: (payload: ProductCreateProductPayload) => mutation.mutate({ data: payload }),
  }
}
