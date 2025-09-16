import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalesGetCollection200,
  apiLocalesIdPatch,
  type Locale,
  type LocaleJsonld,
} from '~/services/client'

interface UpdateLocaleParams {
  id: number
  locale: Locale
}

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ id, locale }: UpdateLocaleParams): Promise<LocaleJsonld> => apiLocalesIdPatch(id.toString(), locale),
    onSuccess: (data) => {
      queryClient.setQueryData(['locales'], (old: ApiLocalesGetCollection200) => {
        const newLocales = {
          ...old,
          member: [...old.member],
        }
        const index = newLocales.member.findIndex(locale => locale.id === data.id)
        if (index !== -1) {
          newLocales.member[index] = data
        }

        return newLocales
      })
    },
  })
}
