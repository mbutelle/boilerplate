import { useQueryClient } from '@tanstack/vue-query'
import {
  getListProductsQueryKey,
  useCreateProduct,
} from '@bp/api-client/api/product/product'
import type { ProductCreateProductPayload } from '@bp/api-client/api/model'

export default () => {
  const queryClient = useQueryClient()

  const mutation = useCreateProduct({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getListProductsQueryKey() })
      },
    },
  })

  return {
    ...mutation,
    createProduct: (payload: ProductCreateProductPayload) => mutation.mutate({ data: payload }),
  }
}
