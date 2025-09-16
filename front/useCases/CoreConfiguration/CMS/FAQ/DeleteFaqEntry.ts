import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalefaqEntriesGetCollection200,
  apiLocalefaqEntriesIdDelete,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()
  const { currentLocale } = UseLocale()

  return useMutation({
    mutationFn: (id: number): Promise<void> => apiLocalefaqEntriesIdDelete(currentLocale.value, id.toString()),
    onSuccess: (_, id) => {
      queryClient.setQueryData(['faqEntries', currentLocale.value], (old: ApiLocalefaqEntriesGetCollection200) => {
        const newFaqEntries = {
          ...old,
          member: old.member.filter(faqEntry => faqEntry.id !== id),
        }

        if (newFaqEntries.totalItems) {
          newFaqEntries.totalItems--
        }

        return newFaqEntries
      })
    },
  })
}
