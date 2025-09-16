import { useMutation, useQueryClient } from '@tanstack/vue-query'
import { apiUsersIdDelete } from '~/services/client'

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (id: string) => apiUsersIdDelete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['admins'] })
    },
  })
}
