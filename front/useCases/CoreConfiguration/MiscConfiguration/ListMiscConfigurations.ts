import { useQuery } from '@tanstack/vue-query'
import {
  apiMiscConfigurationsGetCollection,
  type ApiMiscConfigurationsGetCollection200,
} from '~/services/client'

export type Query = {
  page?: number | undefined
  itemsPerPage?: number | undefined
}

export default (query?: Query | Ref<Query | undefined> | null) => {
  return useQuery({
    queryKey: ['miscConfigurations', query],
    queryFn: ({ queryKey }): Promise<ApiMiscConfigurationsGetCollection200> => {
      const [_key, query] = queryKey as [string, Query | null]
      return apiMiscConfigurationsGetCollection({
        page: query?.page,
        itemsPerPage: query?.itemsPerPage,
      })
    },
  })
}
