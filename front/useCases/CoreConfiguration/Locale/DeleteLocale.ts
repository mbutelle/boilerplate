import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalesGetCollection200,
  apiLocalesIdDelete,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (id: number): Promise<void> => apiLocalesIdDelete(id.toString()),
    onSuccess: (_, id) => {
      queryClient.setQueryData(['locales'], (old: ApiLocalesGetCollection200) => {
        const newLocales = {
          ...old,
          member: old.member.filter(locale => locale.id !== id),
        }

        if (newLocales.totalItems) {
          newLocales.totalItems--
        }

        return newLocales
      })
    },
  })
}
