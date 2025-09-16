import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalefaqEntriesGetCollection200,
  apiLocalefaqEntriesIdPut,
  type FaqEntryJsonld,
} from '~/services/client'

interface UpdateFaqEntryParams {
  id: number
  faqEntry: FaqEntryJsonld
}

export default () => {
  const queryClient = useQueryClient()
  const { currentLocale } = UseLocale()

  return useMutation({
    mutationFn: ({ id, faqEntry }: UpdateFaqEntryParams): Promise<FaqEntryJsonld> => apiLocalefaqEntriesIdPut(currentLocale.value, id.toString(), faqEntry),
    onSuccess: (data) => {
      queryClient.setQueryData(['faqEntries', currentLocale.value], (old: ApiLocalefaqEntriesGetCollection200) => {
        const newFaqEntries = {
          ...old,
          member: [...old.member],
        }
        const index = newFaqEntries.member.findIndex(faqEntry => faqEntry.id === data.id)
        if (index !== -1) {
          newFaqEntries.member[index] = data
        }

        return newFaqEntries
      })
    },
  })
}
