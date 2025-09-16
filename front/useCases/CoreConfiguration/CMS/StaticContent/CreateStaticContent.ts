import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalestaticContentsGetCollection200,
  apiLocalestaticContentsPost,
  type StaticContentJsonld,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()
  const { currentLocale } = UseLocale()

  return useMutation({
    mutationFn: (newStaticContent: Partial<StaticContentJsonld>): Promise<StaticContentJsonld> => apiLocalestaticContentsPost(currentLocale.value, newStaticContent),
    onSuccess: (data) => {
      queryClient.setQueryData(['staticContents', currentLocale.value], (old: ApiLocalestaticContentsGetCollection200) => {
        const newStaticContents = {
          ...old,
          member: [...old.member],
        }
        newStaticContents.member.push(data)

        if (newStaticContents.totalItems) {
          newStaticContents.totalItems++
        }

        return newStaticContents
      })
    },
  })
}
