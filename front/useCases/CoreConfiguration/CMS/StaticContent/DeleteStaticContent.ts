import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalestaticContentsGetCollection200,
  apiLocalestaticContentsIdDelete,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()
  const { currentLocale } = UseLocale()

  return useMutation({
    mutationFn: (id: number): Promise<void> => apiLocalestaticContentsIdDelete(currentLocale.value, id.toString()),
    onSuccess: (_, id) => {
      queryClient.setQueryData(['staticContents', currentLocale.value], (old: ApiLocalestaticContentsGetCollection200) => {
        const newStaticContents = {
          ...old,
          member: old.member.filter(staticContent => staticContent.id !== id),
        }

        if (newStaticContents.totalItems) {
          newStaticContents.totalItems--
        }

        return newStaticContents
      })
    },
  })
}
