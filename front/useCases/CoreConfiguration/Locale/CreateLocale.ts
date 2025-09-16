import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalesGetCollection200,
  apiLocalesPost,
  type LocaleJsonld,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (newLocale: Partial<LocaleJsonld>): Promise<LocaleJsonld> => apiLocalesPost(newLocale),
    onSuccess: (data) => {
      queryClient.setQueryData(['locales'], (old: ApiLocalesGetCollection200) => {
        const newLocales = {
          ...old,
          member: [...old.member],
        }
        newLocales.member.push(data)

        if (newLocales.totalItems) {
          newLocales.totalItems++
        }

        return newLocales
      })
    },
  })
}
