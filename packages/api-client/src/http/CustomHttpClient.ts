import Axios, { type AxiosError, type AxiosRequestConfig } from 'axios'
import { jwtDecode } from 'jwt-decode'

/**
 * Couche HTTP unique du SDK : c'est ici, et nulle part ailleurs, que le jeton
 * est posé et que le 401 est traité. Un front qui appelle l'API à la main
 * contourne les deux.
 */
export const CustomHttpClient = async <T>(
  config: AxiosRequestConfig,
  options?: AxiosRequestConfig,
): Promise<T> => {
  const token = useCookie('token')
  const runtimeConfig = useRuntimeConfig()

  const instance = Axios.create({
    baseURL: runtimeConfig.public.API_URL as string,
  })

  if (token.value) {
    // `exp` est en secondes depuis l'époque : le comparer à Date.now(), qui est
    // en millisecondes, demande de convertir. Un jeton déjà expiré est retiré
    // ici plutôt que d'aller chercher un 401 côté serveur.
    const { exp } = jwtDecode<{ exp: number }>(token.value)

    if (Date.now() >= exp * 1000) {
      token.value = null
    }
  }

  const headers: Record<string, string> = {
    ...(config.headers as Record<string, string> | undefined),
    ...(options?.headers as Record<string, string> | undefined),
  }

  if (token.value) {
    headers.Authorization = `Bearer ${token.value}`
  }

  const source = Axios.CancelToken.source()

  return instance({
    ...config,
    ...options,
    headers,
    cancelToken: source.token,
  })
    .then(({ data }) => data as T)
    .catch((error: AxiosError) => {
      if (401 === error.response?.status) {
        token.value = null
        navigateTo('/auth/login')
      }

      throw error
    })
}

export type BodyType<BodyData> = BodyData
