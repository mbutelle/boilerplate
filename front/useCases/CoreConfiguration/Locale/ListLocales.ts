import { useQuery } from '@tanstack/vue-query'
import {
  apiLocalesGetCollection,
  type ApiLocalesGetCollection200,
} from '~/services/client'

export type Query = {
  page?: number | undefined
  itemsPerPage?: number | undefined
}

export default (query?: Query | Ref<Query | undefined> | null) => {
  return useQuery({
    queryKey: ['locales', query],
    queryFn: ({ queryKey }): Promise<ApiLocalesGetCollection200> => {
      const [_key, query] = queryKey as [string, Query | null]
      return apiLocalesGetCollection({
        page: query?.page,
        itemsPerPage: query?.itemsPerPage,
      })
    },
  })
}
