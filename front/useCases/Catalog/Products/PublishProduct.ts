import { useQueryClient } from '@tanstack/vue-query'
import {
  getApiProductsGetCollectionQueryKey,
  useApiProductsIdpublishPost,
} from '@bp/api-client/api/product/product'

export default () => {
  const queryClient = useQueryClient()
  const toast = useToast()

  const mutation = useApiProductsIdpublishPost({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getApiProductsGetCollectionQueryKey() })
      },
      // Le back refuse en 422 la publication d'un produit archivé : la règle est
      // tenue côté serveur, le front se contente de la restituer.
      onError: () => {
        toast.add({
          severity: 'warn',
          summary: 'Ce produit ne peut pas être publié en l\'état.',
          life: 6000,
        })
      },
    },
  })

  return {
    ...mutation,
    publishProduct: (id: string) => mutation.mutate({ id, data: {} }),
  }
}
