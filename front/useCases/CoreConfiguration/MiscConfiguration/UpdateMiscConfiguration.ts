import { useMutation, useQueryClient } from '@tanstack/vue-query'
import {
  type ApiMiscConfigurationsGetCollection200,
  apiMiscConfigurationsIdPatch,
  type MiscConfigurationWriteMiscConfiguration,
  type MiscConfigurationJsonldReadMiscConfiguration,
} from '~/services/client'

interface UpdateMiscConfigurationParams {
  id: number
  miscConfiguration: MiscConfigurationWriteMiscConfiguration
}

export default () => {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ id, miscConfiguration }: UpdateMiscConfigurationParams): Promise<MiscConfigurationJsonldReadMiscConfiguration> => apiMiscConfigurationsIdPatch(id.toString(), miscConfiguration),
    onSuccess: (data) => {
      queryClient.setQueryData(['miscConfigurations'], (old: ApiMiscConfigurationsGetCollection200) => {
        const newMiscConfigurations = {
          ...old,
          member: [...old.member],
        }
        const index = newMiscConfigurations.member.findIndex(miscConfiguration => miscConfiguration.id === data.id)
        if (index !== -1) {
          newMiscConfigurations.member[index] = data
        }

        return newMiscConfigurations
      })
    },
  })
}
