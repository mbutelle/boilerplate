import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiMiscConfigurationsGetCollection200,
  apiMiscConfigurationsIdDelete,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (id: number): Promise<void> => apiMiscConfigurationsIdDelete(id.toString()),
    onSuccess: (_, id) => {
      queryClient.setQueryData(['miscConfigurations'], (old: ApiMiscConfigurationsGetCollection200) => {
        const newMiscConfigurations = {
          ...old,
          member: old.member.filter(miscConfiguration => miscConfiguration.id !== id),
        }

        if (newMiscConfigurations.totalItems) {
          newMiscConfigurations.totalItems--
        }

        return newMiscConfigurations
      })
    },
  })
}
