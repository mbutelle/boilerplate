import { useQueryClient } from '@tanstack/vue-query'
import {
  getApiUsersGetCollectionQueryKey,
  useApiUsersIdDelete,
} from '@bp/api-client/api/user/user'

export default () => {
  const queryClient = useQueryClient()

  const mutation = useApiUsersIdDelete({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getApiUsersGetCollectionQueryKey() })
      },
    },
  })

  return {
    ...mutation,
    deleteUser: (id: string) => mutation.mutate({ id }),
  }
}
