import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiLocalefaqEntriesGetCollection200,
  apiLocalefaqEntriesPost,
  type FaqEntryJsonld,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()
  const { currentLocale } = UseLocale()

  return useMutation({
    mutationFn: (newFaqEntry: Partial<FaqEntryJsonld>): Promise<FaqEntryJsonld> => apiLocalefaqEntriesPost(currentLocale.value, newFaqEntry),
    onSuccess: (data) => {
      queryClient.setQueryData(['faqEntries', currentLocale.value], (old: ApiLocalefaqEntriesGetCollection200) => {
        const newFaqEntries = {
          ...old,
          member: [...old.member],
        }
        newFaqEntries.member.push(data)

        if (newFaqEntries.totalItems) {
          newFaqEntries.totalItems++
        }

        return newFaqEntries
      })
    },
  })
}
