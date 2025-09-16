import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiMiscConfigurationsGetCollection200,
  apiMiscConfigurationsPost,
  type MiscConfigurationJsonldReadMiscConfiguration,
} from '~/services/client'

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (newMiscConfiguration: Partial<MiscConfigurationJsonldReadMiscConfiguration>): Promise<MiscConfigurationJsonldReadMiscConfiguration> => apiMiscConfigurationsPost(newMiscConfiguration),
    onSuccess: (data) => {
      queryClient.setQueryData(['miscConfigurations'], (old: ApiMiscConfigurationsGetCollection200) => {
        const newMiscConfigurations = {
          ...old,
          member: [...old.member],
        }
        newMiscConfigurations.member.push(data)

        if (newMiscConfigurations.totalItems) {
          newMiscConfigurations.totalItems++
        }

        return newMiscConfigurations
      })
    },
  })
}
