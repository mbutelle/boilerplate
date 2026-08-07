import { useQueryClient } from '@tanstack/vue-query'
import {
  getListUsersQueryKey,
  useDeleteUser,
} from '@bp/api-client/api/user/user'

export default () => {
  const queryClient = useQueryClient()

  const mutation = useDeleteUser({
    mutation: {
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getListUsersQueryKey() })
      },
    },
  })

  return {
    ...mutation,
    deleteUser: (id: string) => mutation.mutate({ id }),
  }
}
