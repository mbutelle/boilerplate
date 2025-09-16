import { useQuery } from '@tanstack/vue-query'
import { apiUsersGetCollection } from '~/services/client'

export default () => {
  return useQuery({
    queryKey: ['admins'],
    queryFn: () => apiUsersGetCollection(),
  })
}
