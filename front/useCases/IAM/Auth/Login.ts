import { useLoginCheckPost } from '@bp/api-client/api/login-check/login-check'
import type { LoginCheckPostBody } from '@bp/api-client/api/model'

/**
 * Un useCase est un adaptateur fin : il branche le hook généré sur ce dont
 * l'écran a besoin (cookies, redirection, message). Il n'appelle jamais l'API
 * lui-même — c'est le SDK qui le fait, donc la couche HTTP unique.
 */
export default () => {
  const token = useCookie('token')
  const refreshToken = useCookie('refreshToken')
  const toast = useToast()

  const mutation = useLoginCheckPost({
    mutation: {
      onSuccess: (data) => {
        token.value = data.token
        refreshToken.value = data.refresh_token
        navigateTo('/')
      },
      onError: () => {
        toast.add({
          severity: 'error',
          summary: 'Connexion impossible. Vérifiez vos identifiants.',
          life: 6000,
        })
      },
    },
  })

  return {
    ...mutation,
    login: (credentials: LoginCheckPostBody) => mutation.mutate({ data: credentials }),
  }
}
