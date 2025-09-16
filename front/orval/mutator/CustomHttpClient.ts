import Axios, { type AxiosError, type AxiosRequestConfig } from 'axios'
import { jwtDecode } from 'jwt-decode'

export const CustomHttpClient = async <T>(
  config: AxiosRequestConfig,
  options?: AxiosRequestConfig,
): Promise<T> => {
  const token = useCookie('token')
  const c = useRuntimeConfig()

  const AXIOS_INSTANCE = Axios.create({
    baseURL: c.public.API_URL,
  })

  if (token.value) {
    const decodedToken = jwtDecode(token.value) as { exp: number }

    if (Date.now() >= 1000 * decodedToken.exp) {

    }

    if (!config) {
      config = {}
    }

    if (!config.headers) {
      if (options?.headers) {
        config.headers = options.headers
      }
      else {
        config.headers = {}
      }
    }

    config.headers.Authorization = ['Bearer', token.value].join(' ')
  }

  const source = Axios.CancelToken.source()
  const promise = AXIOS_INSTANCE({
    ...config,
    ...options,
    cancelToken: source.token,
  })
    .then(({ data }) => data)
    .catch((error: AxiosError) => {
      if (401 === error.response?.status) {
        token.value = null
        navigateTo('/auth/login')
      }

      throw error
    })

  return promise
}

export type BodyType<BodyData> = BodyData
