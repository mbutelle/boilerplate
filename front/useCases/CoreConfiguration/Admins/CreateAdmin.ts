import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { apiUsersadminPost } from '~/services/client'

export type CreateAdminPayload = { email: string }

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (payload: CreateAdminPayload) => apiUsersadminPost(payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['admins'] })
    },
  })
}
