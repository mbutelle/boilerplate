import { useQuery } from '@tanstack/vue-query'
import {
  apiLocalefaqEntriesGetCollection,
  type ApiLocalefaqEntriesGetCollection200,
} from '~/services/client'

export type Query = {
  page?: number | undefined
  itemsPerPage?: number | undefined
}

export default (query?: Query | Ref<Query | undefined> | null) => {
  const { currentLocale } = UseLocale()

  return useQuery({
    queryKey: ['faqEntries', currentLocale, query],
    queryFn: ({ queryKey }): Promise<ApiLocalefaqEntriesGetCollection200> => {
      const [_key, locale, query] = queryKey as [string, string, Query | null]

      return apiLocalefaqEntriesGetCollection(locale, {
        page: query?.page,
        itemsPerPage: query?.itemsPerPage,
      })
    },
  })
}
