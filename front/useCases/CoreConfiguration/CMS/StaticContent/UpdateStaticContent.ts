import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalestaticContentsGetCollection200,
  apiLocalestaticContentsIdPut,
  type StaticContentJsonld,
  type StaticContent,
} from '~/services/client'

interface UpdateStaticContentParams {
  id: number
  staticContent: StaticContent
}

export default () => {
  const queryClient = useQueryClient()
  const { currentLocale } = UseLocale()

  return useMutation({
    mutationFn: ({ id, staticContent }: UpdateStaticContentParams): Promise<StaticContentJsonld> => apiLocalestaticContentsIdPut(currentLocale.value, id.toString(), staticContent),
    onSuccess: (data) => {
      queryClient.setQueryData(['staticContents', currentLocale.value], (old: ApiLocalestaticContentsGetCollection200) => {
        const newStaticContents = {
          ...old,
          member: [...old.member],
        }
        const index = newStaticContents.member.findIndex(staticContent => staticContent.id === data.id)
        if (index !== -1) {
          newStaticContents.member[index] = data
        }

        return newStaticContents
      })
    },
  })
}
