import { useQuery } from '@tanstack/vue-query'
import {
  apiLocalestaticContentsGetCollection,
  type ApiLocalestaticContentsGetCollection200,
} from '~/services/client'

export type Query = {
  page?: number | undefined
  itemsPerPage?: number | undefined
}

export default (query?: Query | Ref<Query | undefined> | null) => {
  const { currentLocale } = UseLocale()

  return useQuery({
    queryKey: ['staticContents', currentLocale, query],
    queryFn: ({ queryKey }): Promise<ApiLocalestaticContentsGetCollection200> => {
      const [_key, locale, query] = queryKey as [string, string, Query | null]

      return apiLocalestaticContentsGetCollection(locale, {
        page: query?.page,
        itemsPerPage: query?.itemsPerPage,
      })
    },
  })
}
