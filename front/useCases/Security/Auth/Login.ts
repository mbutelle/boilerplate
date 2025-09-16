import { useMutation } from '@tanstack/vue-query'
import {
  type LoginCheckPostBody,
  type LoginCheckPost200,
  loginCheckPost,
} from '~/services/client'

export default () => {
  const token = useCookie('token')
  const toast = useToast()
  const refreshToken = useCookie('refreshToken')

  return useMutation({
    mutationFn: (log: LoginCheckPostBody): Promise<LoginCheckPost200> => loginCheckPost(log),
    onSuccess: (data) => {
      token.value = data.token
      refreshToken.value = data.refresh_token

      navigateTo('/')
    },
    onError: () => {
      console.log('error')
      toast.add({
        severity: 'error',
        summary: `Une erreur est survenue lors de la connexion. Vérifiez vos identifiants.`,
        life: 6000,
      })
    },
  })
}
