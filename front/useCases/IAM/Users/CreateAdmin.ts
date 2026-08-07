import { useQueryClient } from '@tanstack/vue-query'
import {
  getApiUsersGetCollectionQueryKey,
  useApiUsersadminPost,
} from '@bp/api-client/api/user/user'
import type { UserCreateAdminPayload } from '@bp/api-client/api/model'

export default () => {
  const queryClient = useQueryClient()

  const mutation = useApiUsersadminPost({
    mutation: {
      // La clé vient du SDK plutôt que d'une chaîne écrite à la main : une clé
      // recopiée finit toujours par diverger de celle du hook de lecture, et
      // l'invalidation devient silencieusement inopérante.
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: getApiUsersGetCollectionQueryKey() })
      },
    },
  })

  return {
    ...mutation,
    createAdmin: (payload: UserCreateAdminPayload) => mutation.mutate({ data: payload }),
  }
}
